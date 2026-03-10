<?php
declare(strict_types=1);

namespace Staff\Chercheur;

use Staff\Auth\Auth;
use Staff\Core\Database;
use Staff\Core\Security;
use PDO;

class ParrainageController
{
    private PDO $db;

    public function __construct()
    {
        Auth::requireRole('chercheur');
        $this->db = Database::getInstance()->getConnection();
    }

    public function index(): void
    {
        $userId = Auth::id();

        // Générer code parrain si absent
        $user = $this->db->prepare("SELECT code_parrain FROM users WHERE id = ?");
        $user->execute([$userId]);
        $userData = $user->fetch();

        if (empty($userData['code_parrain'])) {
            $code = strtoupper(substr(md5($userId . time()), 0, 8));
            $this->db->prepare("UPDATE users SET code_parrain = ? WHERE id = ?")->execute([$code, $userId]);
            $codeParrain = $code;
        } else {
            $codeParrain = $userData['code_parrain'];
        }

        // Stats parrainage
        $stats = $this->db->prepare("
            SELECT
                COUNT(*) AS total_filleuls,
                SUM(commission) AS total_commissions,
                SUM(CASE WHEN statut='payee' THEN commission ELSE 0 END) AS commissions_recues,
                SUM(CASE WHEN statut='en_attente' THEN commission ELSE 0 END) AS commissions_attente
            FROM parrainages WHERE parrain_id = ?
        ");
        $stats->execute([$userId]);
        $statsData = $stats->fetch();

        // Liste filleuls
        $filleuls = $this->db->prepare("
            SELECT p.*, u.email AS filleul_email, p.created_at
            FROM parrainages p
            JOIN users u ON u.id = p.filleul_id
            WHERE p.parrain_id = ?
            ORDER BY p.created_at DESC
        ");
        $filleuls->execute([$userId]);
        $filleulsList = $filleuls->fetchAll();

        // Numéro MoMo enregistré
        $momoStmt = $this->db->prepare("SELECT telephone_momo FROM users WHERE id = ?");
        $momoStmt->execute([$userId]);
        $momoData = $momoStmt->fetch();
        $telephoneMomo = $momoData['telephone_momo'] ?? '';

        $lienParrainage = BASE_URL . '/register?ref=' . $codeParrain;

        require BASE_PATH . '/src/Views/chercheur/parrainage.php';
    }

    public function saveMomo(): void
    {
        Security::verifyCsrf();
        $userId = Auth::id();
        $telephone = trim($_POST['telephone_momo'] ?? '');

        if (!preg_match('/^[6][0-9]{8}$/', $telephone)) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Numéro invalide. Format : 6XXXXXXXX'];
            header('Location: ' . BASE_URL . '/chercheur/parrainage');
            exit;
        }

        $this->db->prepare("UPDATE users SET telephone_momo = ? WHERE id = ?")->execute([$telephone, $userId]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Numéro Mobile Money enregistré avec succès.'];
        header('Location: ' . BASE_URL . '/chercheur/parrainage');
        exit;
    }
}
