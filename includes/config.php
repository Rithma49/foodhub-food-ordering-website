<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'foodhub');

define('DELIVERY_FEE', 250);
define('TAX_RATE', 0.08);
define('APP_NAME', 'Food Hub');

/* Auto-detect base URL (folder-name safe) */
$__docRoot = str_replace('\\', '/', rtrim(realpath($_SERVER['DOCUMENT_ROOT'] ?? ''), '/'));
$__appRoot = str_replace('\\', '/', rtrim(realpath(__DIR__ . '/..'), '/'));
$__baseUrl = $__docRoot ? str_replace($__docRoot, '', $__appRoot) : '';
define('BASE_URL', rtrim($__baseUrl, '/') . '/');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (Throwable $e) {
    die('<div style="font-family:sans-serif;padding:20px;background:#FEF2F2;color:#991B1B;border-radius:12px;margin:20px">
        <strong>Database Error:</strong><br>' . htmlspecialchars($e->getMessage()) . '
        <br><br>phpMyAdmin එකේ <b>foodhub</b> database එක import කරලා තියෙනවද බලන්න.</div>');
}