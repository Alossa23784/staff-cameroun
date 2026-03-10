<?php
declare(strict_types=1);

namespace Staff\Services;

use Staff\Core\Database;
use PDO;

/**
 * ParrainageService
 * Gère le système de parrainage avec commission 500 FCFA via Mobile Money
 */
class ParrainageService
{
    private PDO $db;
    private const COMMISSION = 500;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Générer un code parrain unique ───────────────────

    public function genererCode(int $userId): string
    {
        // Vérifier si déjà un code
        $stmt = $this->db->prepare('SELECT code_parrain FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $existing = $stmt->fetchColumn();
        if ($existing) return $existing;

        // Générer un code unique de 8 caractères
        do {
            $code = 'STF-' . strtoupper(substr(base_convert(bin2hex(random_bytes(4)), 16, 36), 0, 6));
            $stmt = $this->db->prepare('SELECT id FROM users WHERE code_parrain = ?');
            $stmt->execute([$code]);
        } while ($stmt->fetchColumn());

        $this->db->prepare('UPDATE users SET code_parrain = ? WHERE id = ?')
                 ->execute([$code, $userId]);

        return $code;
    }

    public function getCode(int $userId): ?string
    {
        $stmt = $this->db->prepare('SELECT code_parrain FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: null;
    }

    // ── Enregistrer un parrainage lors de l'inscription ─

    public function enregistrer(int $filleulId, string $codeParrain): bool
    {
        // Trouver le parrain
        $stmt = $this->db->prepare('SELECT id FROM users WHERE code_parrain = ? LIMIT 1');
        $stmt->execute([$codeParrain]);
        $parrainId = $stmt->fetchColumn();

        if (!$parrainId || $parrainId === $filleulId) return false;

        // Vérifier pas déjà parrainé
        $stmt = $this->db->prepare('SELECT id FROM parrainages WHERE filleul_id = ?');
        $stmt->execute([$filleulId]);
        if ($stmt->fetchColumn()) return false;

        $this->db->prepare("
            INSERT INTO parrainages (parrain_id, filleul_id, code_parrain, statut, commission)
            VALUES (?, ?, ?, 'en_attente', ?)
        ")->execute([$parrainId, $filleulId, $codeParrain, self::COMMISSION]);

        return true;
    }

    // ── Valider et payer la commission quand filleul s'abonne

    public function validerCommission(int $filleulId): bool
    {
        // Trouver le parrainage en attente
        $stmt = $this->db->prepare("
            SELECT p.*, u.telephone_momo, u.email,
                   COALESCE(c.prenom,'') AS prenom, COALESCE(c.nom,'') AS nom
            FROM parrainages p
            JOIN users u ON u.id = p.parrain_id
            LEFT JOIN chercheurs c ON c.user_id = p.parrain_id
            WHERE p.filleul_id = ? AND p.statut = 'en_attente'
            LIMIT 1
        ");
        $stmt->execute([$filleulId]);
        $parrainage = $stmt->fetch();

        if (!$parrainage) return false;

        // Marquer comme validé
        $this->db->prepare("
            UPDATE parrainages SET statut = 'valide' WHERE id = ?
        ")->execute([$parrainage['id']]);

        // Envoyer la commission via Monetbil si numéro Mobile Money disponible
        if ($parrainage['telephone_momo']) {
            $this->payerCommission($parrainage);
        }

        // Notifier le parrain par email
        try {
            (new MailService())->envoyerNotificationParrainage($parrainage);
        } catch (\Exception $e) {}

        return true;
    }

    // ── Virement commission via Monetbil ─────────────────

    private function payerCommission(array $parrainage): void
    {
        $reference = 'COMM-' . $parrainage['parrain_id'] . '-' . time();

        // Appel API Monetbil disbursement (virement sortant)
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.monetbil.com/v1/pay_out',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'serviceKey' => MONETBIL_SERVICE_KEY,
                'amount'     => (int)self::COMMISSION,
                'phone'      => $parrainage['telephone_momo'],
                'reference'  => $reference,
                'message'    => 'Commission parrainage STAFF',
            ]),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response ?: '{}', true) ?? [];

        // Marquer comme payé si succès
        if (strtoupper($data['status'] ?? '') === 'SUCCESS') {
            $this->db->prepare("
                UPDATE parrainages SET statut='payee', paye_at=NOW() WHERE parrain_id=? AND filleul_id=?
            ")->execute([$parrainage['parrain_id'], $parrainage['filleul_id']]);
        }
    }

    // ── Stats parrainage d'un utilisateur ────────────────

    public function getStats(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN statut IN ('valide','payee') THEN 1 ELSE 0 END) AS valides,
                SUM(CASE WHEN statut = 'payee' THEN commission ELSE 0 END) AS total_gagné,
                SUM(CASE WHEN statut = 'valide' THEN commission ELSE 0 END) AS en_attente_paiement
            FROM parrainages WHERE parrain_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getListe(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.email AS filleul_email,
                   COALESCE(c.prenom,'') AS prenom,
                   COALESCE(c.nom,'') AS nom
            FROM parrainages p
            JOIN users u ON u.id = p.filleul_id
            LEFT JOIN chercheurs ch_p ON ch_p.user_id = p.filleul_id
            LEFT JOIN chercheurs c ON c.user_id = p.filleul_id
            WHERE p.parrain_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
