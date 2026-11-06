<?php
require 'includes/db_connect.php'; 

$testNames = [];
for ($i = 1; $i <= 22; $i++) {
    $testNames[] = "Test-$i";
}

$discSlug = 'electrical';
$discId = $conn->query("SELECT id FROM disciplines WHERE slug='$discSlug'")->fetchColumn();
if (!$discId) {
    $conn->query("INSERT INTO disciplines (name, slug) VALUES ('Electricals & Electronics', '$discSlug')");
    $discId = $conn->lastInsertId();
} else {
    // Ensure the name matches the spreadsheet precisely
    $conn->query("UPDATE disciplines SET name = 'Electricals & Electronics' WHERE id = $discId");
}

$productIds = $conn->query("SELECT id FROM products")->fetchAll(PDO::FETCH_COLUMN);

$count = 0;
foreach ($testNames as $name) {
    $stmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ?");
    $stmt->execute([$name, $discId]);
    $testId = $stmt->fetchColumn();
    
    if (!$testId) {
        $insert = $conn->prepare("INSERT INTO tests (name, discipline_id) VALUES (?, ?)");
        $insert->execute([$name, $discId]);
        $testId = $conn->lastInsertId();
    }
    
    foreach ($productIds as $pId) {
        $mapStmt = $conn->prepare("INSERT IGNORE INTO product_test (product_id, test_id) VALUES (?, ?)");
        $mapStmt->execute([$pId, $testId]);
    }
    $count++;
}
echo "Successfully added $count tests and mapped them to all products under Electricals & Electronics.\n";
