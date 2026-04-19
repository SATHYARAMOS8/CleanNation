<?php
/**
 * CleanNation MVC Entry Point
 */
require_once __DIR__ . '/../core/app.php';
require_once __DIR__ . '/../core/middleware.php';
require_once __DIR__ . '/../core/config.php';

runMiddleware();

// Simple routing
$path = $_GET['p'] ?? 'login';
$path = str_replace(['../', '..\\'], '', $path); // Basic sanitization

switch ($path) {
    case 'login':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->login();
        break;

    case 'register':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->register();
        break;

    case 'logout':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->dashboard();
        break;

    case 'create_pickup':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->createPickup();
        break;

    case 'complete_pickup':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->completePickup();
        break;

    case 'admin':
        require_once __DIR__ . '/../controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->dashboard();
        break;

    case 'security':
        require_once __DIR__ . '/../controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->securityDashboard();
        break;

    case 'assign':
        require_once __DIR__ . '/../controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->assignDriver();
        break;

    case 'block_ip':
        require_once __DIR__ . '/../controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->blockIP();
        break;

    case 'unblock_ip':
        require_once __DIR__ . '/../controllers/AdminController.php';
        $controller = new AdminController($pdo);
        $controller->unblockIP();
        break;

    default:
        if (isLoggedIn()) {
            require_once __DIR__ . '/../views/404.php';
        } else {
            redirect('login');
        }
        break;
}



function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentRole() {
    return $_SESSION['role'] ?? null;
}

function requireRole($role) {
    if (!isLoggedIn() || currentRole() !== $role) {
        redirect('login');
        exit;
    }
}
?>


