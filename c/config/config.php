<?php
// ── CHEMINS ──────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL',  'https://staff-cameroun.vercel.app');

// ── BASE DE DONNÉES Clever Cloud ─────────────────────────
define('DB_HOST',    'bje4uiwz0ep4vlwrveuf-mysql.services.clever-cloud.com');
define('DB_NAME',    'bje4uiwz0ep4vlwrveuf');
define('DB_USER',    'uwmld2opv5h0fnra');
define('DB_PASS',    'H5KAPUSHlnMNEZhZNCXf');
define('DB_PORT',    3306);
define('DB_CHARSET', 'utf8mb4');

// ── MONETBIL ─────────────────────────────────────────────
define('MONETBIL_SERVICE_KEY',    'gQjCNPs39uzQu2fxmHYgFgB1JVwFrbYf');
define('MONETBIL_SERVICE_SECRET', '2wbIwgDGpJoxA3Kbjqb1j50D5UsBHsrD37jxu0E4HA2m1dBMAw7mFJWcliQKuCKH');
define('MONETBIL_NOTIFY_URL',     BASE_URL . '/paiement/notify');
define('MONETBIL_RETURN_URL',     BASE_URL . '/paiement/retour');

// ── SMTP GMAIL ────────────────────────────────────────────
define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_USER',      'staffonlinecm@gmail.com');
define('SMTP_PASS',      'hxad nqcg ptlv uawu');
define('MAIL_FROM',      'staffonlinecm@gmail.com');
define('MAIL_FROM_NAME', 'STAFF Cameroun');

// ── ABONNEMENT & PARRAINAGE ───────────────────────────────
define('ABONNEMENT_PRIX',       3500);
define('ABONNEMENT_DUREE',      365);
define('PARRAINAGE_COMMISSION', 500);
define('RELANCE_JOURS',         7);

// ── SESSION ───────────────────────────────────────────────
define('SESSION_NAME',     'STAFFSESS');
define('SESSION_LIFETIME', 7200);

// ── APP ───────────────────────────────────────────────────
define('APP_NAME',    'STAFF');
define('APP_VERSION', '1.0.0');

// ── PHP SESSION CONFIG ────────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime',  '7200');
