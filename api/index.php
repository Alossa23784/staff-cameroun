<?php
declare(strict_types=1);

// Sur InfinityFree, index.php EST dans htdocs/ donc config est dans htdocs/config/
require_once dirname(__DIR__) . '/config/config.php';

// Autoloader
spl_autoload_register(function (string $class): void {
    $prefix = 'Staff\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = BASE_PATH . '/src/' . $relative . '.php';
    if (file_exists($file)) require_once $file;
});

// Chargement manuel classes Core (sécurité Windows/Linux)
foreach (['Core/Database','Core/Router','Core/Security','Auth/Auth'] as $f) {
    $path = BASE_PATH . '/src/' . $f . '.php';
    if (file_exists($path)) require_once $path;
}

use Staff\Core\Router;
use Staff\Core\Security;
use Staff\Auth\Auth;

Auth::start();
Security::init();

$router = new Router();

// Auth
$router->get( '/login',           [\Staff\Auth\LoginController::class,         'showForm']);
$router->post('/login',           [\Staff\Auth\LoginController::class,         'handle']);
$router->get( '/logout',          [\Staff\Auth\LoginController::class,         'logout']);
$router->get( '/register',        [\Staff\Auth\RegisterController::class,      'showForm']);
$router->post('/register',        [\Staff\Auth\RegisterController::class,      'handle']);
$router->get( '/forgot-password', [\Staff\Auth\ResetPasswordController::class, 'showRequest']);
$router->post('/forgot-password', [\Staff\Auth\ResetPasswordController::class, 'handleRequest']);
$router->get( '/reset-password',  [\Staff\Auth\ResetPasswordController::class, 'showReset']);
$router->post('/reset-password',  [\Staff\Auth\ResetPasswordController::class, 'handleReset']);

// Admin
$router->get( '/admin/stats',              [\Staff\Admin\StatsController::class,  'index']);
$router->get( '/admin/stats/ajax',         [\Staff\Admin\StatsController::class,  'ajax']);
$router->get( '/admin/users',              [\Staff\Admin\AdminController::class,  'users']);
$router->post('/admin/users/action',       [\Staff\Admin\AdminController::class,  'userAction']);
$router->get( '/admin/abonnements',        [\Staff\Admin\AdminController::class,  'abonnements']);
$router->post('/admin/abonnements/action', [\Staff\Admin\AdminController::class,  'abonnementAction']);
$router->get( '/admin/parrainages',        [\Staff\Admin\AdminController::class,  'parrainages']);
$router->get( '/admin/publicites',         [\Staff\Admin\AdminController::class,  'publicites']);
$router->post('/admin/publicites/action',  [\Staff\Admin\AdminController::class,  'publiciteAction']);

// Chercheur
$router->get( '/chercheur/dashboard',       [\Staff\Chercheur\DashboardController::class,  'index']);
$router->post('/chercheur/postuler',        [\Staff\Chercheur\PostulerController::class,    'handle']);
$router->get( '/chercheur/parrainage',      [\Staff\Chercheur\ParrainageController::class,  'index']);
$router->post('/chercheur/parrainage/momo', [\Staff\Chercheur\ParrainageController::class,  'saveMomo']);

// Entreprise
$router->get( '/entreprise/dashboard', [\Staff\Entreprise\DashboardController::class, 'index']);
$router->post('/entreprise/statut',    [\Staff\Entreprise\DashboardController::class, 'changerStatut']);
$router->post('/entreprise/stagiaire', [\Staff\Entreprise\DashboardController::class, 'marquerStagiaire']);
$router->post('/entreprise/noter',     [\Staff\Entreprise\DashboardController::class, 'noter']);

// Paiement
$router->get( '/abonnement',         [\Staff\Payment\AbonnementController::class, 'index']);
$router->post('/abonnement/initier', [\Staff\Payment\AbonnementController::class, 'initier']);
$router->get( '/paiement/retour',    [\Staff\Payment\AbonnementController::class, 'retour']);
$router->post('/paiement/notify',    [\Staff\Payment\AbonnementController::class, 'notify']);

// Vitrine
$router->get('/vitrine', [\Staff\Vitrine\VitrineController::class, 'index']);

// Accueil
$router->get('/', function () {
    if (\Staff\Auth\Auth::check()) {
        match (\Staff\Auth\Auth::role()) {
            'admin'      => header('Location: ' . BASE_URL . '/admin/stats'),
            'entreprise' => header('Location: ' . BASE_URL . '/entreprise/dashboard'),
            'chercheur'  => header('Location: ' . BASE_URL . '/chercheur/dashboard'),
            default      => header('Location: ' . BASE_URL . '/login'),
        };
        exit;
    }
    (new \Staff\Vitrine\VitrineController())->index();
});

$router->dispatch();
