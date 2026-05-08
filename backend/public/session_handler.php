<?php
/**
 * session_handler.php
 * Internal PHP endpoint: validates a Sanctum token by calling /api/v1/auth/me
 * and stores the result in the PHP session.
 *
 * NEVER expose this file on a publicly routable path that bypasses validation.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (empty($data['token'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Token obrigatório']));
}

$token = trim($data['token']);

// Validate token against the API (same server — loopback request)
$scheme  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = $_SERVER['HTTP_HOST'];
$script  = dirname($_SERVER['SCRIPT_NAME']); // e.g. /Nova%20pasta/backend/public
$apiUrl  = $scheme . '://' . $host . $script . '/api/v1/auth/me';

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 5,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
    ],
    CURLOPT_SSL_VERIFYPEER => false, // loopback only
]);

$body     = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr || $httpCode !== 200) {
    http_response_code(401);
    exit(json_encode(['error' => 'Token inválido ou expirado']));
}

$user = json_decode($body, true);

$_SESSION['auth_token'] = $token;
$_SESSION['user']       = $user;

echo json_encode(['success' => true, 'user' => $user]);
