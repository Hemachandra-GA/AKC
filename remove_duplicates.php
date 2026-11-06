<?php
require 'includes/db_connect.php'; 

$products = $conn->query("SELECT id, name FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$nameMap = [];
$deleteIds = [];

foreach ($products as $p) {
    $name = trim($p['name']);
    if (!isset($nameMap[$name])) {
        $nameMap[$name] = $p['id'];
    } else {
        $deleteIds[] = $p['id'];
        $keepId = $nameMap[$name];
        // Remap tests to the preserved ID
        $conn->query("UPDATE IGNORE product_test SET product_id = $keepId WHERE product_id = {$p['id']}");
    }
}

if (!empty($deleteIds)) {
    $ids = implode(',', $deleteIds);
    $conn->query("DELETE FROM products WHERE id IN ($ids)");
    echo "Removed duplicate product IDs: $ids\n";
} else {
    echo "No duplicates found.\n";
}
