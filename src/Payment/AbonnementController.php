<?php
declare(strict_types=1);

namespace Staff\Payment;

use Staff\Auth\Auth;
use Staff\Core\Database;
use Staff\Services\MonetbilService;
use Staff\Services\AbonnementService;
use Staff\Services\MailService;

class AbonnementController
{
    public function __construct()
    {
        Auth::requireRole('chercheur');
    }

    /** Page paiement — redirige vers widget Monetbil */
    public function index(): void
    {
        $userId     = Auth::id();
        $service    = new AbonnementService();
        $actif      = $service->estActif($userId);
        $dateFin    = $service->dateFin($userId);
        $dates      = $service->calculerDates($userId);

        // Récupérer infos user
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT u.email, u.phone, c.prenom, c.nom FROM users u LEFT JOIN chercheurs c ON c.user_id = u.id WHERE u.id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        require BASE_PATH . '/src/Views/payment/abonnement.php';
    }

    /** Initier le paiement → redirection vers Monetbil */
    public function initier(): void
    {
        $userId    = Auth::id();
        $telephone = trim($_POST['telephone'] ?? '');

        if (!preg_match('/^6[0-9]{8}$/', preg_replace('/\D/', '', $telephone))) {
            $_SESSION['pay_error'] = 'Numéro de téléphone invalide.';
            header('Location: ' . BASE_URL . '/abonnement');
            exit;
        }

        $reference = 'STAFF-' . $userId . '-' . time();

        // Enregistrer paiement en attente
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT u.email, u.phone, c.prenom, c.nom FROM users u LEFT JOIN chercheurs c ON c.user_id = u.id WHERE u.id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        $db->prepare("INSERT INTO paiements (user_id, reference, montant, operateur, telephone, statut) VALUES (?, ?, ?, 'MONETBIL', ?, 'en_attente')")
           ->execute([$userId, $reference, ABONNEMENT_PRIX, $telephone]);

        // Générer URL Monetbil et rediriger
        $monetbil = new MonetbilService();
        $url = $monetbil->genererUrlPaiement(
            $userId,
            ABONNEMENT_PRIX,
            $reference,
            $user['email']  ?? '',
            $telephone,
            $user['prenom'] ?? '',
            $user['nom']    ?? ''
        );

        header('Location: ' . $url);
        exit;
    }

    /** Retour après paiement Monetbil */
    public function retour(): void
    {
        $reference = $_GET['reference'] ?? $_POST['reference'] ?? '';
        $statut    = $_GET['status']    ?? $_POST['status']    ?? '';
        $success   = strtoupper($statut) === 'SUCCESS';

        require BASE_PATH . '/src/Views/payment/retour.php';
    }

    /** Webhook Monetbil — notification serveur */
    public function notify(): void
    {
        $data = $_POST;

        if (empty($data['reference'])) {
            http_response_code(400);
            exit('Bad Request');
        }

        $monetbil = new MonetbilService();

        // Vérifier la signature
        if (!$monetbil->verifierSignature($data)) {
            http_response_code(403);
            exit('Invalid signature');
        }

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM paiements WHERE reference = ? LIMIT 1");
        $stmt->execute([$data['reference']]);
        $paiement = $stmt->fetch();

        if (!$paiement) {
            http_response_code(404);
            exit('Not found');
        }

        $statut = match(strtoupper($data['status'] ?? '')) {
            'SUCCESS' => 'succes',
            'FAILED'  => 'echec',
            default   => 'en_attente',
        };

        $db->prepare("UPDATE paiements SET statut=?, payload=?, operateur=? WHERE id=?")
           ->execute([$statut, json_encode($data), strtoupper($data['operator'] ?? 'MONETBIL'), $paiement['id']]);

        // Créer abonnement si succès et pas encore traité
        if ($statut === 'succes' && $paiement['statut'] !== 'succes') {
            $aboService = new AbonnementService();
            $aboId      = $aboService->creer((int)$paiement['user_id'], (int)$paiement['id']);
            try {
                (new MailService())->envoyerConfirmationAbonnement((int)$paiement['user_id'], $aboId);
            } catch (\Exception $e) {}
        }

        http_response_code(200);
        exit('OK');
    }
}
