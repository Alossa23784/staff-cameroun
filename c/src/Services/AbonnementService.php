<?php
declare(strict_types=1);

namespace Staff\Services;

use Staff\Core\Database;
use PDO;

/**
 * AbonnementService
 * Gère toute la logique métier des abonnements STAFF
 */
class AbonnementService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Vérifications ────────────────────────────────────

    /** L'utilisateur a-t-il un abonnement actif ? */
    public function estActif(int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM abonnements
            WHERE user_id = ? AND statut = 'actif' AND fin >= CURDATE()
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Date de fin du dernier abonnement actif (null si aucun) */
    public function dateFin(int $userId): ?\DateTime
    {
        $stmt = $this->db->prepare("
            SELECT fin FROM abonnements
            WHERE user_id = ? AND statut = 'actif'
            ORDER BY fin DESC LIMIT 1
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetchColumn();
        return $row ? new \DateTime($row) : null;
    }

    // ── Création d'abonnement ────────────────────────────

    /**
     * Calcule les dates de début/fin selon les règles métier :
     * - Renouvellement avant expiration → démarre à la date de fin actuelle
     * - Renouvellement après expiration  → démarre aujourd'hui
     */
    public function calculerDates(int $userId): array
    {
        $dateFin = $this->dateFin($userId);
        $today   = new \DateTime();

        if ($dateFin !== null && $dateFin > $today) {
            // Cumul : la nouvelle période démarre après l'actuelle
            $debut = clone $dateFin;
            $debut->modify('+1 day');
        } else {
            // Expirée ou première fois
            $debut = clone $today;
        }

        $fin = clone $debut;
        $fin->modify('+' . ABONNEMENT_DUREE . ' days');

        return [
            'debut' => $debut->format('Y-m-d'),
            'fin'   => $fin->format('Y-m-d'),
        ];
    }

    /**
     * Créer un abonnement après paiement confirmé
     */
    public function creer(int $userId, int $paiementId): int
    {
        $dates = $this->calculerDates($userId);

        $stmt = $this->db->prepare("
            INSERT INTO abonnements (user_id, debut, fin, montant, statut)
            VALUES (?, ?, ?, ?, 'actif')
        ");
        $stmt->execute([
            $userId,
            $dates['debut'],
            $dates['fin'],
            ABONNEMENT_PRIX,
        ]);
        $abonnementId = (int)$this->db->lastInsertId();

        // Lier le paiement à cet abonnement
        $this->db->prepare("UPDATE paiements SET abonnement_id = ? WHERE id = ?")
                 ->execute([$abonnementId, $paiementId]);

        return $abonnementId;
    }

    // ── Paiement ─────────────────────────────────────────

    /**
     * Initier un paiement : crée l'entrée en BDD + appelle MeSomb
     * Retourne ['success'=>bool, 'paiement_id'=>int, 'message'=>string]
     */
    public function initierPaiement(int $userId, string $operateur, string $telephone): array
    {
        // Cette méthode est désactivée — le vrai flux passe par AbonnementController + Monetbil
        return [
            'success' => false,
            'message' => 'Utilisez le flux Monetbil via /abonnement.',
        ];
    }

    // ── Relances automatiques ────────────────────────────

    /**
     * Récupère les abonnements expirant dans RELANCE_JOURS jours
     * (à appeler depuis un cron PHP)
     */
    public function abonnementsARelancer(): array
    {
        $dateLimit = (new \DateTime())->modify('+' . RELANCE_JOURS . ' days')->format('Y-m-d');

        $stmt = $this->db->prepare("
            SELECT a.*, u.email, u.phone,
                   COALESCE(c.prenom, '') AS prenom,
                   COALESCE(c.nom, '')    AS nom
            FROM abonnements a
            JOIN users u ON u.id = a.user_id
            LEFT JOIN chercheurs c ON c.user_id = a.user_id
            WHERE a.statut = 'actif'
              AND a.fin = ?
        ");
        $stmt->execute([$dateLimit]);
        return $stmt->fetchAll();
    }

    /**
     * Lance les emails de relance (appeler depuis cron/relance.php)
     */
    public function lancerRelances(): int
    {
        $liste = $this->abonnementsARelancer();
        $mail  = new MailService();
        $count = 0;

        foreach ($liste as $abo) {
            $mail->envoyerRelanceAbonnement($abo);
            $count++;
        }

        return $count;
    }
}
