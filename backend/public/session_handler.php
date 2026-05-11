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

// -----------------------------------------------------------------------
// Token source: HttpOnly cookie 'api_token' set by the Laravel login endpoint.
// The cookie is sent automatically by the browser on same-origin POST requests
// and is never exposed to JavaScript. Reject if the cookie is absent.
// -----------------------------------------------------------------------
$token = isset($_COOKIE['api_token']) ? trim($_COOKIE['api_token']) : '';

// Validate format: Sanctum token is <id>|<hash>
if (empty($token) || ! preg_match('/^\d+\|[a-zA-Z0-9]{40,}$/', $token)) {
    http_response_code(400);
    exit(json_encode(['error' => 'Cookie de autenticação ausente ou inválido']));
}

// -----------------------------------------------------------------------
// Build the API URL from APP_URL defined in .env.
//
// SECURITY: Never use $_SERVER['HTTP_HOST'] for security-sensitive URL
// construction — an attacker can forge the Host header and redirect this
// loopback cURL call to an arbitrary server, leaking the Bearer token
// (Host Header Injection / SSRF via header manipulation).
// -----------------------------------------------------------------------
$dotenvFile = realpath(__DIR__ . '/../.env');
$appUrl     = '';

if ($dotenvFile && is_readable($dotenvFile)) {
    foreach (file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (str_starts_with($line, 'APP_URL=')) {
            $appUrl = trim(substr($line, 8), " \t\"'");
            break;
        }
    }
}

if (empty($appUrl) || filter_var($appUrl, FILTER_VALIDATE_URL) === false) {
    http_response_code(500);
    exit(json_encode(['error' => 'APP_URL não configurado corretamente no .env']));
}

$apiUrl = rtrim($appUrl, '/') . '/api/v1/auth/me';

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 5,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
    ],
    // SECURITY: SSL verification must be enabled in production.
    // If the certificate chain is not found automatically, set CURLOPT_CAINFO
    // to the absolute path of your CA bundle (e.g. /etc/ssl/certs/ca-certificates.crt
    // on Linux, or C:/xampp/apache/bin/curl-ca-bundle.crt on XAMPP).
    //
    // Setting CURLOPT_SSL_VERIFYPEER => false disables TLS entirely and opens
    // a Man-in-the-Middle window: an attacker on the same host/network can
    // intercept the loopback request, impersonate the API and capture the
    // Bearer token or return crafted user data.
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    // Uncomment and adjust if your CA bundle is not auto-detected:
    // CURLOPT_CAINFO => 'C:/xampp/apache/bin/curl-ca-bundle.crt',
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
