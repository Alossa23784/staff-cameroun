<?php
declare(strict_types=1);

namespace Staff\Auth;

use Staff\Core\Database;
use Staff\Services\MailService;
use Staff\Services\ParrainageService;
use PDO;

class RegisterController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function showForm(): void
    {
        if (Auth::check()) { header('Location: ' . BASE_URL . '/'); exit; }
        require BASE_PATH . '/src/Views/auth/register.php';
    }

    public function handle(): void
    {
        $email    = trim($_POST['email']    ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm']  ?? '');
        $role     = $_POST['role']          ?? 'chercheur';
        $refCode  = trim($_POST['ref_code'] ?? $_GET['ref'] ?? '');
        $error    = null;

        // Récupérer le nom selon le rôle
        if ($role === 'entreprise') {
            $nom    = trim($_POST['nom_entreprise'] ?? '');
            $prenom = '';
        } else {
            $nom    = trim($_POST['nom']    ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
        }

        // Validations
        if (!$email || !$phone || !$password || !$nom) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (strlen($password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif ($password !== $confirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif (!in_array($role, ['chercheur', 'entreprise'])) {
            $error = 'Type de compte invalide.';
        } else {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn()) {
                $error = 'Cette adresse email est déjà utilisée.';
            }
        }

        if ($error) {
            require BASE_PATH . '/src/Views/auth/register.php';
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->beginTransaction();

        try {
            $this->db->prepare("INSERT INTO users (email, phone, password_hash, role, is_active) VALUES (?, ?, ?, ?, 1)")
                     ->execute([$email, $phone, $hash, $role]);
            $userId = (int)$this->db->lastInsertId();

            if ($role === 'chercheur') {
                $this->db->prepare("INSERT INTO chercheurs (user_id, nom, prenom) VALUES (?, ?, ?)")
                         ->execute([$userId, $nom, $prenom]);
            } else {
                $this->db->prepare("INSERT INTO entreprises (user_id, nom) VALUES (?, ?)")
                         ->execute([$userId, $nom]);
            }

            $this->db->prepare("INSERT INTO email_contacts (user_id, email, phone, type, ebook_sent) VALUES (?, ?, ?, ?, 0)")
                     ->execute([$userId, $email, $phone, $role === 'chercheur' ? 'etudiant' : 'entreprise']);

            $this->db->commit();

            // Parrainage
            if ($refCode) {
                try { (new ParrainageService())->enregistrer($userId, $refCode); } catch (\Exception $e) {}
            }

            // Connexion
            $user = ['id' => $userId, 'email' => $email, 'role' => $role];
            Auth::login($user);

            // Email silencieux
            try { (new MailService())->envoyerEbook($userId); } catch (\Exception $e) {}

            header('Location: ' . BASE_URL . '/');
            exit;

        } catch (\Exception $e) {
            $this->db->rollBack();
            $error = 'Une erreur est survenue. Veuillez réessayer. (' . $e->getMessage() . ')';
            require BASE_PATH . '/src/Views/auth/register.php';
        }
    }
}
