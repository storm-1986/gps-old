<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/options.php';

if (
    empty($_SESSION['id']) ||
    empty($_SESSION['username']) ||
    $_SESSION['id'] !== session_id()
) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        exit('{"type":"error","msg":"Unauthorized","res":"Unauthorized"}');
    }

    if (!headers_sent()) {
        header('Location: /index.php');
    }
    exit;
}