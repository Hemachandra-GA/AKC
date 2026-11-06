<?php
require_once 'includes/db_connect.php';
$stmt = $conn->query("SELECT id, name, slug FROM products ORDER BY id");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($products as $p) {
    echo $p['id'] . " | " . $p['name'] . " | " . $p['slug'] . "\n";
}
