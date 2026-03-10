<?php
declare(strict_types=1);

namespace Staff\Chercheur;

use Staff\Auth\Auth;
use Staff\Core\Database;
use Staff\Models\CandidatureModel;
use Staff\Services\AbonnementService;

class PostulerController
{
    public function handle(): void
    {
        header('Content-Type: application/json');
        Auth::requireRole('chercheur');

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id FROM chercheurs WHERE user_id = ?');
        $stmt->execute([Auth::id()]);
        $chercheurId = (int)$stmt->fetchColumn();

        // Vérifier abonnement actif
        $abo = new AbonnementService();
        if (!$abo->estActif(Auth::id())) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être abonné pour postuler.']);
            return;
        }

        $entrepriseId = (int)($_POST['entreprise_id'] ?? 0);
        if (!$entrepriseId) {
            echo json_encode(['success' => false, 'message' => 'Entreprise invalide.']);
            return;
        }

        $model  = new CandidatureModel();
        $result = $model->postuler($chercheurId, $entrepriseId, ['type_depot' => 'abonnement']);
        echo json_encode($result);
    }
}
