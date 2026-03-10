<?php
declare(strict_types=1);

namespace Staff\Admin;

use Staff\Auth\Auth;
use Staff\Core\Database;
use Staff\Core\Security;
use PDO;

class AdminController
{
    protected PDO $db;

    public function __construct()
    {
        Auth::requireRole('admin');
        $this->db = Database::getInstance()->getConnection();
    }

    protected function render(string $view, array $vars = []): void
    {
        extract($vars);
        ob_start();
        require BASE_PATH . '/src/Views/admin/' . $view . '.php';
        $content = ob_get_clean();
        require BASE_PATH . '/src/Views/admin/layout.php';
    }

    // ── UTILISATEURS ─────────────────────────────────────

    public function users(): void
    {
        $search = trim($_GET['q'] ?? '');
        $role   = $_GET['role'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;

        $where  = ['1=1'];
        $params = [];
        if ($search) { $where[] = '(u.email LIKE ? OR c.nom LIKE ? OR c.prenom LIKE ?)'; $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]); }
        if ($role)   { $where[] = 'u.role = ?'; $params[] = $role; }

        $sql = "SELECT u.*, COALESCE(c.nom,'') AS nom, COALESCE(c.prenom,'') AS prenom,
                       COALESCE(e.nom,'') AS entreprise_nom,
                       (SELECT MAX(fin) FROM abonnements WHERE user_id=u.id AND statut='actif') AS abo_fin
                FROM users u
                LEFT JOIN chercheurs c ON c.user_id=u.id
                LEFT JOIN entreprises e ON e.user_id=u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY u.created_at DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users u LEFT JOIN chercheurs c ON c.user_id=u.id WHERE " . implode(' AND ', $where));
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $pages = ceil($total / $limit);

        $this->render('users', compact('users', 'search', 'role', 'page', 'pages', 'total') + ['pageTitle' => 'Utilisateurs', 'activePage' => 'users']);
    }

    public function userAction(): void
    {
        Security::verifyCsrf();
        $id     = Security::validateInt($_POST['user_id'] ?? 0, 1);
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'bloquer':
                $this->db->prepare("UPDATE users SET is_active=0 WHERE id=? AND role!='admin'")->execute([$id]);
                echo json_encode(['ok' => true, 'msg' => 'Utilisateur bloqué.']);
                break;
            case 'debloquer':
                $this->db->prepare("UPDATE users SET is_active=1 WHERE id=?")->execute([$id]);
                echo json_encode(['ok' => true, 'msg' => 'Utilisateur débloqué.']);
                break;
            case 'supprimer':
                $this->db->prepare("DELETE FROM users WHERE id=? AND role!='admin'")->execute([$id]);
                echo json_encode(['ok' => true, 'msg' => 'Utilisateur supprimé.']);
                break;
            default:
                echo json_encode(['ok' => false, 'msg' => 'Action invalide.']);
        }
        exit;
    }

    // ── ABONNEMENTS ──────────────────────────────────────

    public function abonnements(): void
    {
        $search = trim($_GET['q'] ?? '');
        $statut = $_GET['statut'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 20; $offset = ($page - 1) * $limit;

        $where = ['1=1']; $params = [];
        if ($search) { $where[] = 'u.email LIKE ?'; $params[] = "%$search%"; }
        if ($statut) { $where[] = 'a.statut = ?';   $params[] = $statut; }

        $stmt = $this->db->prepare("
            SELECT a.*, u.email, COALESCE(c.prenom,'') AS prenom, COALESCE(c.nom,'') AS nom,
                   p.operateur, p.telephone
            FROM abonnements a
            JOIN users u ON u.id = a.user_id
            LEFT JOIN chercheurs c ON c.user_id = a.user_id
            LEFT JOIN paiements p ON p.id = a.paiement_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY a.created_at DESC LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $abonnements = $stmt->fetchAll();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM abonnements a JOIN users u ON u.id=a.user_id WHERE " . implode(' AND ', $where));
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $pages = ceil($total / $limit);

        $this->render('abonnements', compact('abonnements','search','statut','page','pages','total') + ['pageTitle' => 'Abonnements', 'activePage' => 'abonnements']);
    }

    public function abonnementAction(): void
    {
        Security::verifyCsrf();
        $id     = Security::validateInt($_POST['abo_id'] ?? 0, 1);
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'prolonger':
                $jours = Security::validateInt($_POST['jours'] ?? 30, 1, 365);
                $this->db->prepare("UPDATE abonnements SET fin = DATE_ADD(fin, INTERVAL ? DAY) WHERE id=?")->execute([$jours, $id]);
                echo json_encode(['ok' => true, 'msg' => "Prolongé de $jours jour(s)."]);
                break;
            case 'annuler':
                $this->db->prepare("UPDATE abonnements SET statut='annule' WHERE id=?")->execute([$id]);
                echo json_encode(['ok' => true, 'msg' => 'Abonnement annulé.']);
                break;
            default:
                echo json_encode(['ok' => false, 'msg' => 'Action invalide.']);
        }
        exit;
    }

    // ── PARRAINAGES ──────────────────────────────────────

    public function parrainages(): void
    {
        $statut = $_GET['statut'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 20; $offset = ($page - 1) * $limit;

        $where = ['1=1']; $params = [];
        if ($statut) { $where[] = 'p.statut = ?'; $params[] = $statut; }

        $stmt = $this->db->prepare("
            SELECT p.*,
                   up.email AS parrain_email, COALESCE(cp.prenom,'') AS parrain_prenom, COALESCE(cp.nom,'') AS parrain_nom,
                   uf.email AS filleul_email, up.telephone_momo
            FROM parrainages p
            JOIN users up ON up.id = p.parrain_id
            JOIN users uf ON uf.id = p.filleul_id
            LEFT JOIN chercheurs cp ON cp.user_id = p.parrain_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $parrainages = $stmt->fetchAll();

        $stats = $this->db->query("SELECT
            COUNT(*) AS total,
            SUM(commission) AS total_commissions,
            SUM(CASE WHEN statut='paye' THEN commission ELSE 0 END) AS total_paye,
            SUM(CASE WHEN statut='valide' THEN commission ELSE 0 END) AS en_attente
        FROM parrainages")->fetch();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM parrainages p WHERE " . implode(' AND ', $where));
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $pages = ceil($total / $limit);

        $this->render('parrainages', compact('parrainages','statut','page','pages','total','stats') + ['pageTitle' => 'Parrainages', 'activePage' => 'parrainages']);
    }

    // ── PUBLICITÉS ───────────────────────────────────────

    public function publicites(): void
    {
        $pubs = $this->db->query("SELECT * FROM publicites ORDER BY created_at DESC")->fetchAll();
        $this->render('publicites', compact('pubs') + ['pageTitle' => 'Publicités', 'activePage' => 'publicites']);
    }

    public function publiciteAction(): void
    {
        Security::verifyCsrf();
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'ajouter':
                $titre    = Security::escape(trim($_POST['titre']    ?? ''));
                $position = trim($_POST['position'] ?? 'vitrine_banner');
                $lien     = trim($_POST['lien_url'] ?? '');
                $image    = trim($_POST['image_url'] ?? '');

                if (!$titre) { echo json_encode(['ok'=>false,'msg'=>'Titre requis.']); exit; }

                $this->db->prepare("INSERT INTO publicites (titre, position, lien_url, image, is_active) VALUES (?,?,?,?,1)")
                         ->execute([$titre, $position, $lien, $image]);
                echo json_encode(['ok' => true, 'msg' => 'Publicité ajoutée.']);
                break;

            case 'toggle':
                $id = Security::validateInt($_POST['pub_id'] ?? 0, 1);
                $this->db->prepare("UPDATE publicites SET is_active = NOT is_active WHERE id=?")->execute([$id]);
                echo json_encode(['ok' => true, 'msg' => 'Statut mis à jour.']);
                break;

            case 'supprimer':
                $id = Security::validateInt($_POST['pub_id'] ?? 0, 1);
                $this->db->prepare("DELETE FROM publicites WHERE id=?")->execute([$id]);
                echo json_encode(['ok' => true, 'msg' => 'Publicité supprimée.']);
                break;

            default:
                echo json_encode(['ok' => false, 'msg' => 'Action invalide.']);
        }
        exit;
    }
}
