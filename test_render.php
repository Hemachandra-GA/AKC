<?php
$_SESSION = ['logged_in' => true, 'role' => 'admin'];
$_GET = [];
ob_start();
include 'admin_dashboard.php';
$html = ob_get_clean();

if (strpos($html, 'pagination-container') !== false) {
    echo "Pagination IS in the HTML.\n";
} else {
    echo "Pagination is MISSING from the HTML.\n";
}
?>
