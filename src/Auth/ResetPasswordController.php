<?php
declare(strict_types=1);

namespace Staff\Auth;

use Staff\Core\Database;
use Staff\Services\MailService;
use PDO;

/**
 * Contrôleur Reset Mot de Passe
 */
class ResetPasswordController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Formulaire demande de reset */
    public function showRequest(): void
    {
        require BASE_PATH . '/src/Views/auth/forgot_password.php';
    }

    /** Traitement : envoie le lien de reset */
    public function handleRequest(): void
    {
        $email = trim($_POST['email'] ?? '');
        $msg   = null;
        $error = null;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } else {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Générer token sécurisé valable 1h
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $this->db->prepare("
                    UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?
                ")->execute([$token, $expires, $user['id']]);

                (new MailService())->envoyerResetPassword($email, $token);
            }

            // Toujours afficher ce message (sécurité anti-énumération)
            $msg = 'Si cet email existe, un lien de réinitialisation vous a été envoyé.';
        }

        require BASE_PATH . '/src/Views/auth/forgot_password.php';
    }

    /** Formulaire nouveau mot de passe */
    public function showReset(): void
    {
        $token = $_GET['token'] ?? '';
        $valid = $this->verifierToken($token);
        require BASE_PATH . '/src/Views/auth/reset_password.php';
    }

    /** Traitement : enregistre le nouveau mot de passe */
    public function handleReset(): void
    {
        $token    = $_POST['token']    ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';
        $error    = null;
        $success  = false;

        $user = $this->verifierToken($token);

        if (!$user) {
            $error = 'Lien invalide ou expiré. Veuillez refaire une demande.';
        } elseif (strlen($password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif ($password !== $confirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->db->prepare("
                UPDATE users
                SET password_hash = ?, reset_token = NULL, reset_token_expires_at = NULL
                WHERE id = ?
            ")->execute([$hash, $user['id']]);
            $success = true;
        }

        $valid = $user !== false;
        require BASE_PATH . '/src/Views/auth/reset_password.php';
    }

    private function verifierToken(string $token): array|false
    {
        if (!$token) return false;
        $stmt = $this->db->prepare("
            SELECT id, email FROM users
            WHERE reset_token = ? AND reset_token_expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: false;
    }
}
