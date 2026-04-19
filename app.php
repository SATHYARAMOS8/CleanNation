<?php
/**
 * CleanNation MVC Framework - Core Helpers
 */
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/?p=');
define('ASSETS_URL', '/assets/');

// Safe session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Safe input
function input($key, $default = '') {
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// POST check
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Redirect
function redirect($path = '') {
    $path = trim($path, '/');
    if (empty($path)) {
        $url = BASE_URL . 'login';
    } else {
        $url = BASE_URL . urlencode($path);
    }
    header("Location: $url");
    exit;
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Escape output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// CSRF helpers
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function ensureCsrf() {
    $token = input('csrf_token');
    if (!verifyCsrfToken($token)) {
        setFlash('danger', 'Invalid form submission. Please try again.');
        redirect('login');
    }
}

// View helper
function view($name, $data = []) {
    extract($data);
    require BASE_PATH . "/views/$name.php";
}
?>

