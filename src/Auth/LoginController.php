<?php
declare(strict_types=1);

namespace Staff\Auth;

use Staff\Core\Database;
use Staff\Core\Security;

class LoginController
{
    public function showForm(): void
    {
        if (Auth::check()) { header('Location: ' . BASE_URL . '/'); exit; }
        require BASE_PATH . '/src/Views/auth/login.php';
    }

    public function handle(): void
    {
        // CSRF
        Security::verifyCsrf();

        // Brute force — max 5 tentatives / 15 min par IP
        Security::checkRateLimit('login', 5, 900);

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $error    = null;

        if (!$email || !$password) {
            $error = 'Veuillez remplir tous les champs.';
        } else {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && Security::verifyPassword($password, $user['password_hash'])) {
                Security::resetRateLimit('login');
                Auth::login($user);
                header('Location: ' . BASE_URL . '/');
                exit;
            }
            $error = 'Email ou mot de passe incorrect.';
            Security::logAttaque('LOGIN_FAILED', "Échec connexion: $email");
        }

        require BASE_PATH . '/src/Views/auth/login.php';
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
