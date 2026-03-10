<?php
declare(strict_types=1);

namespace Staff\Entreprise;

use Staff\Auth\Auth;
use Staff\Core\Database;
use Staff\Models\CandidatureModel;

class DashboardController
{
    private CandidatureModel $model;
    private int $entrepriseId;

    public function __construct()
    {
        Auth::requireRole('entreprise');
        $db   = Database::getInstance()->getConnection();

        // Récupérer ou créer la ligne entreprise si manquante
        $stmt = $db->prepare('SELECT id FROM entreprises WHERE user_id = ?');
        $stmt->execute([Auth::id()]);
        $id = $stmt->fetchColumn();

        if (!$id) {
            // Créer la ligne manquante avec email comme nom par défaut
            $email = Auth::email();
            $nom   = explode('@', $email)[0];
            $db->prepare("INSERT INTO entreprises (user_id, nom) VALUES (?, ?)")
               ->execute([Auth::id(), $nom]);
            $this->entrepriseId = (int)$db->lastInsertId();
        } else {
            $this->entrepriseId = (int)$id;
        }

        $this->model = new CandidatureModel();
    }

    public function index(): void
    {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM entreprises WHERE user_id = ?');
        $stmt->execute([Auth::id()]);
        $entreprise = $stmt->fetch();

        $stats = [
            'total'      => count($this->model->getParEntreprise($this->entrepriseId)),
            'en_attente' => count($this->model->getParEntreprise($this->entrepriseId, 'en_attente')),
            'acceptees'  => count($this->model->getParEntreprise($this->entrepriseId, 'acceptee')),
            'refusees'   => count($this->model->getParEntreprise($this->entrepriseId, 'refusee')),
            'en_cours'   => count($this->model->getParEntreprise($this->entrepriseId, 'stage_en_cours')),
        ];

        $candidatures    = $this->model->getParEntreprise($this->entrepriseId, 'en_attente');
        $acceptees       = $this->model->getParEntreprise($this->entrepriseId, 'acceptee');
        $stages_en_cours = $this->model->getParEntreprise($this->entrepriseId, 'stage_en_cours');

        // Flash message
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require BASE_PATH . '/src/Views/entreprise/dashboard.php';
    }

    public function changerStatut(): void
    {
        header('Content-Type: application/json');
        $id     = (int)($_POST['id']     ?? 0);
        $statut = $_POST['statut'] ?? '';

        if (!in_array($statut, ['acceptee', 'refusee', 'en_attente'])) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide.']);
            return;
        }

        $ok = $this->model->changerStatut($id, $this->entrepriseId, $statut);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Statut mis à jour.' : 'Erreur.']);
    }

    public function marquerStagiaire(): void
    {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? 0);
        $ok = $this->model->marquerStagiaire($id, $this->entrepriseId);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Stagiaire marqué en cours.' : 'Erreur.']);
    }

    public function noter(): void
    {
        header('Content-Type: application/json');
        $id   = (int)($_POST['id']   ?? 0);
        $note = (int)($_POST['note'] ?? 0);
        $result = $this->model->noterStagiaire($id, $this->entrepriseId, $note);
        echo json_encode($result);
    }
}
