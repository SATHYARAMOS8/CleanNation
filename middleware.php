<?php
/**
 * Security Middleware - Runs on every request
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';
global $pdo;

function runMiddleware() {
    global $pdo;
    enforceIPBlock($pdo);
    scanRequest($pdo);
}
?>


