<?php
/**
 * auth_check.php
 * Include at the top of every protected page (after session_start).
 * Redirects to login.php if the user is not authenticated.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($baseUrl)) {
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}

if (empty($_SESSION['auth_token']) || empty($_SESSION['user'])) {
    header('Location: ' . $baseUrl . '/login.php');
    exit;
}

$currentUser = $_SESSION['user'];
