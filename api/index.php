<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$path = ltrim($path, '/');

// Serve a specific PHP file if it exists
if (pathinfo($path, PATHINFO_EXTENSION) === 'php' && file_exists(__DIR__ . '/../' . $path)) {
    chdir(__DIR__ . '/../' . (dirname($path) === '.' ? '' : dirname($path)));
    require __DIR__ . '/../' . $path;
    exit;
}

// Otherwise use the router.php logic
chdir(__DIR__ . '/../');
$res = require __DIR__ . '/../router.php';
if ($res === false) {
    http_response_code(404);
    echo "404 Not Found";
}
