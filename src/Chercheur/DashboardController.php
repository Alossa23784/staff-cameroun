<?php
declare(strict_types=1);

namespace Staff\Chercheur;

use Staff\Auth\Auth;
use Staff\Core\Database;
use Staff\Models\CandidatureModel;
use Staff\Services\AbonnementService;

class DashboardController
{
    private int $chercheurId;

    public function __construct()
    {
        Auth::requireRole('chercheur');
        $db   = Database::getInstance()->getConnection();

        // Récupérer ou créer la ligne chercheur si manquante
        $stmt = $db->prepare('SELECT id FROM chercheurs WHERE user_id = ?');
        $stmt->execute([Auth::id()]);
        $id = $stmt->fetchColumn();

        if (!$id) {
            $email  = Auth::email();
            $nom    = explode('@', $email)[0];
            $db->prepare("INSERT INTO chercheurs (user_id, nom, prenom) VALUES (?, ?, ?)")
               ->execute([Auth::id(), $nom, '']);
            $this->chercheurId = (int)$db->lastInsertId();
        } else {
            $this->chercheurId = (int)$id;
        }
    }

    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // Profil
        $stmt = $db->prepare('SELECT * FROM chercheurs WHERE user_id = ?');
        $stmt->execute([Auth::id()]);
        $profil = $stmt->fetch();

        // Abonnement
        $aboService = new AbonnementService();
        $abonne     = $aboService->estActif(Auth::id());
        $dateFin    = $aboService->dateFin(Auth::id());

        // Candidatures
        $model        = new CandidatureModel();
        $candidatures = $model->getParChercheur($this->chercheurId);

        $stats = [
            'total'      => count($candidatures),
            'en_attente' => count(array_filter($candidatures, fn($c) => $c['statut'] === 'en_attente')),
            'acceptees'  => count(array_filter($candidatures, fn($c) => $c['statut'] === 'acceptee')),
            'refusees'   => count(array_filter($candidatures, fn($c) => $c['statut'] === 'refusee')),
            'en_cours'   => count(array_filter($candidatures, fn($c) => $c['statut'] === 'stage_en_cours')),
        ];

        // Entreprises disponibles
        $entreprises = $db->query("SELECT * FROM entreprises ORDER BY nom ASC")->fetchAll();

        // Flash message
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require BASE_PATH . '/src/Views/chercheur/dashboard.php';
    }
}
