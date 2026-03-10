<?php
declare(strict_types=1);

namespace Staff\Auth;

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;

        // Utiliser SESSION_NAME si défini, sinon nom par défaut
        if (defined('SESSION_NAME')) {
            session_name(SESSION_NAME);
        }

        session_start();
    }

    public static function login(array $user): void
    {
        // Régénérer UNIQUEMENT si pas déjà connecté
        if (!self::check()) {
            session_regenerate_id(true);
        }
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['user_email'] = $user['email'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function role(): string
    {
        return $_SESSION['user_role'] ?? '';
    }

    public static function id(): int
    {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    public static function email(): string
    {
        return $_SESSION['user_email'] ?? '';
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        if (self::role() !== $role) {
            http_response_code(403);
            die('Accès refusé.');
        }
    }
}
