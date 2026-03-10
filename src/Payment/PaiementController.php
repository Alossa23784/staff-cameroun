<?php
declare(strict_types=1);

namespace Staff\Payment;

use Staff\Auth\Auth;
use Staff\Core\Database;
use Staff\Services\MonetbilService;
use Staff\Services\AbonnementService;
use PDO;

/**
 * Contrôleur Paiement Monetbil
 * Gère : initiation, retour, webhook notify
 */
class PaiementController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Page formulaire de paiement */
    public function index(): void
    {
        Auth::requireRole('chercheur');

        $userId   = Auth::id();
        $service  = new AbonnementService();
        $actif    = $service->estActif($userId);
        $dateFin  = $service->dateFin($userId);
        $dates    = $service->calculerDates($userId);

        require BASE_PATH . '/src/Views/payment/paiement.php';
    }

    /** Initier le paiement → redirige vers Monetbil */
    public function initier(): void
    {
        Auth::requireRole('chercheur');

        $telephone = trim($_POST['telephone'] ?? '');
        $userId    = Auth::id();

        if (!preg_match('/^6[5-9]\d{7}$/', preg_replace('/\D/', '', $telephone))) {
            $_SESSION['pay_error'] = 'Numéro de téléphone invalide. Ex: 650000000';
            header('Location: ' . BASE_URL . '/paiement');
            exit;
        }

        $reference = 'STAFF-' . $userId . '-' . time();

        // Enregistrer le paiement en attente
        $this->db->prepare("
            INSERT INTO paiements (user_id, reference, montant, operateur, telephone, statut)
            VALUES (?, ?, ?, 'MONETBIL', ?, 'en_attente')
        ")->execute([$userId, $reference, ABONNEMENT_PRIX, $telephone]);

        // Générer et rediriger vers le lien Monetbil
        $monetbil = new MonetbilService();
        $url      = $monetbil->genererLienPaiement($userId, $telephone, $reference);

        header('Location: ' . $url);
        exit;
    }

    /** Page de retour après paiement (redirect_url) */
    public function retour(): void
    {
        $reference = $_GET['ref'] ?? '';
        $statut    = 'en_attente';
        $message   = '';

        if ($reference) {
            // Vérifier le statut réel via API Monetbil
            $monetbil = new MonetbilService();
            $result   = $monetbil->verifierPaiement($reference);
            $statut   = $result['statut'];

            if ($statut === 'succes') {
                $this->traiterSucces($reference);
                $message = 'Paiement réussi ! Votre abonnement est activé.';
            } elseif ($statut === 'echec') {
                $this->db->prepare("UPDATE paiements SET statut='echec' WHERE reference=?")
                         ->execute([$reference]);
                $message = 'Paiement échoué. Veuillez réessayer.';
            } else {
                $message = 'Paiement en cours de vérification...';
            }
        }

        require BASE_PATH . '/src/Views/payment/retour.php';
    }

    /** Webhook Monetbil (notify_url) — appelé automatiquement par Monetbil */
    public function notify(): void
    {
        $data = $_POST;

        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // Log pour débogage
        file_put_contents(
            BASE_PATH . '/logs/monetbil.log',
            date('[Y-m-d H:i:s] ') . json_encode($data) . PHP_EOL,
            FILE_APPEND
        );

        if (empty($data['payment_ref'])) {
            http_response_code(400);
            echo 'Bad Request';
            return;
        }

        // Vérifier signature
        $monetbil = new MonetbilService();
        if (!empty($data['sign']) && !$monetbil->verifierSignature($data)) {
            http_response_code(403);
            echo 'Invalid signature';
            return;
        }

        $reference = $data['payment_ref'];
        $statut    = strtoupper($data['status'] ?? '');

        if ($statut === 'SUCCESS') {
            $this->traiterSucces($reference);
        } elseif ($statut === 'FAILED') {
            $this->db->prepare("UPDATE paiements SET statut='echec', payload=? WHERE reference=?")
                     ->execute([json_encode($data), $reference]);
        }

        http_response_code(200);
        echo 'OK';
    }

    /** Traite un paiement réussi : met à jour BDD + crée abonnement */
    private function traiterSucces(string $reference): void
    {
        // Vérifier que pas déjà traité
        $stmt = $this->db->prepare("SELECT * FROM paiements WHERE reference = ? LIMIT 1");
        $stmt->execute([$reference]);
        $paiement = $stmt->fetch();

        if (!$paiement || $paiement['statut'] === 'succes') return;

        // Mettre à jour le paiement
        $this->db->prepare("UPDATE paiements SET statut='succes' WHERE reference=?")
                 ->execute([$reference]);

        // Créer l'abonnement
        $aboService = new AbonnementService();
        $aboService->creer((int)$paiement['user_id'], (int)$paiement['id']);
    }
}
