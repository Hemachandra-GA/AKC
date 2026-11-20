<?php
// router.php - Used for local PHP built-in server to simulate clean URLs.
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$path = trim($path, '/');

// If the file physically exists on disk (e.g., CSS, images, videos, existing PHP scripts), serve it normally
if ($path !== '' && file_exists(__DIR__ . '/' . $path) && is_file(__DIR__ . '/' . $path)) {
    return false;
}

// Support clean URLs without .php extension (e.g. /advisory-service -> advisory-service.php)
if ($path !== '' && file_exists(__DIR__ . '/' . $path . '.php') && is_file(__DIR__ . '/' . $path . '.php')) {
    include __DIR__ . '/' . $path . '.php';
    exit;
}

$parts = explode('/', $path);

// Check if URL matches the format: /product-slug/category-slug
if (count($parts) === 2 && !empty($parts[0]) && !empty($parts[1])) {
    $_GET['slug'] = $parts[0];
    $_GET['category'] = $parts[1];
    include __DIR__ . '/dynamic-product.php';
    exit;
}
// Check if URL matches single slug: /product-slug
elseif (count($parts) === 1 && !empty($parts[0]) && !str_contains($parts[0], '.')) {
    $_GET['slug'] = $parts[0];
    include __DIR__ . '/dynamic-product.php';
    exit;
}

// Fallback to index if root is accessed
if ($path === '') {
    if (file_exists(__DIR__ . '/index.php')) {
        include __DIR__ . '/index.php';
        exit;
    }
}

return false;
