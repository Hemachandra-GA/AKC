<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Disable browser and CDN caching for admin views
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !in_array($_SESSION['role'] ?? '', ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit();
}
?>
