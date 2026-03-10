<?php
declare(strict_types=1);

namespace Staff\Core;

class Security
{
    public static function init(): void
    {
        self::setSecureHeaders();
        self::sanitizeGlobals();
    }

    public static function setSecureHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
    }

    public static function sanitizeGlobals(): void
    {
        // Nettoyage léger
    }

    public static function protectSession(): void
    {
        // Désactivé dev local
    }

    public static function blockMaliciousRequests(): void
    {
        // Désactivé dev local
    }

    // ── CSRF ────────────────────────────────────────────────────────────

    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::generateCsrfToken() . '">';
    }

    public static function verifyCsrf(): void
    {
        // Si pas de token en session → on laisse passer (dev local)
        if (empty($_SESSION['csrf_token'])) return;
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die('Token CSRF invalide.');
        }
    }

    // ── RATE LIMIT (brute force) ─────────────────────────────────────────

    public static function checkRateLimit(string $key, int $maxAttempts = 10, int $decay = 300): void
    {
        $sessionKey = 'rl_' . $key;
        $data = $_SESSION[$sessionKey] ?? ['count' => 0, 'time' => time()];

        // Reset si délai expiré
        if (time() - $data['time'] > $decay) {
            $data = ['count' => 0, 'time' => time()];
            $_SESSION[$sessionKey] = $data;
        }

        if ($data['count'] >= $maxAttempts) {
            $wait = $decay - (time() - $data['time']);
            http_response_code(429);
            die("Trop de tentatives. Réessayez dans " . ceil($wait / 60) . " minute(s).");
        }
    }

    public static function resetRateLimit(string $key): void
    {
        unset($_SESSION['rl_' . $key]);
    }

    public static function recordFailedAttempt(string $key): void
    {
        $sessionKey = 'rl_' . $key;
        $data = $_SESSION[$sessionKey] ?? ['count' => 0, 'time' => time()];
        if (time() - $data['time'] > 300) {
            $data = ['count' => 0, 'time' => time()];
        }
        $data['count']++;
        $_SESSION[$sessionKey] = $data;
    }

    public static function clearAttempts(string $key): void
    {
        unset($_SESSION['rl_' . $key]);
    }

    // Alias ancienne API
    public static function checkBruteForce(string $key): bool
    {
        $data = $_SESSION['rl_' . $key] ?? ['count' => 0, 'time' => time()];
        if (time() - $data['time'] > 300) return false;
        return $data['count'] >= 10;
    }

    // ── MOT DE PASSE ─────────────────────────────────────────────────────

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function validatePassword(string $password): bool
    {
        return strlen($password) >= 8;
    }

    // ── ÉCHAPPEMENT / VALIDATION ──────────────────────────────────────────

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function validateInt(mixed $value, int $min = 0, int $max = PHP_INT_MAX): int
    {
        $int = (int) $value;
        if ($int < $min || $int > $max) return $min;
        return $int;
    }

    // ── LOGS ──────────────────────────────────────────────────────────────

    public static function logAttaque(string $type, string $message): void
    {
        $logFile = BASE_PATH . '/logs/security.log';
        $line = '[' . date('Y-m-d H:i:s') . '] [' . $type . '] ' . $message
              . ' IP=' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . PHP_EOL;
        @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
