<?php
/**
 * logout.php — Revoke Sanctum token and destroy PHP session.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

if (!empty($_SESSION['auth_token'])) {
    $token  = $_SESSION['auth_token'];
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $apiUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . $baseUrl . '/api/v1/auth/logout';

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => '{}',
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

// Destroy PHP session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

header('Location: ' . $baseUrl . '/login.php');
exit;
