<?php
declare(strict_types=1);

namespace Staff\Vitrine;

use Staff\Core\Database;
use PDO;

class VitrineController
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // Publicités
        $pubVitrine  = $db->query("SELECT * FROM publicites WHERE position='vitrine_banner'  AND is_active=1 LIMIT 3")->fetchAll();
        $pubRegister = $db->query("SELECT * FROM publicites WHERE position='register_banner' AND is_active=1 LIMIT 1")->fetch();

        // Offres récentes
        try {
            $offres = $db->query("
                SELECT o.*, e.nom AS entreprise_nom
                FROM offres o
                LEFT JOIN entreprises e ON e.user_id = o.user_id
                WHERE o.statut = 'active'
                ORDER BY o.created_at DESC
                LIMIT 6
            ")->fetchAll();
        } catch (\Exception $e) {
            $offres = [];
        }

        // Stats
        $nbChercheurs  = (int)$db->query("SELECT COUNT(*) FROM users WHERE role='chercheur'")->fetchColumn();
        $nbEntreprises = (int)$db->query("SELECT COUNT(*) FROM users WHERE role='entreprise'")->fetchColumn();

        require BASE_PATH . '/src/Views/vitrine/index.php';
    }
}
