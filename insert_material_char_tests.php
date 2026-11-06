<?php
require 'includes/db_connect.php'; 

$testNames = [
    "Structural identification of materials",
    "Particle size analyzer",
    "Molecular weight Determination of polymer",
    "Oxygen transmission rate",
    "Water vapour transmission rate"
];

for ($i = 6; $i <= 22; $i++) {
    $testNames[] = "Test-$i";
}

$discSlug = 'material-characterization';
$discId = $conn->query("SELECT id FROM disciplines WHERE slug='$discSlug'")->fetchColumn();
if (!$discId) {
    $conn->query("INSERT INTO disciplines (name, slug) VALUES ('Material Characterization', '$discSlug')");
    $discId = $conn->lastInsertId();
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

echo "Successfully added $count tests to Material Characterization.\n";
