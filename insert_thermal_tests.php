<?php
require 'includes/db_connect.php'; 

$testNames = [
    "Thermogravimetry Analysis (TGA)",
    "Differential Scanning Calorimetry (DSC)",
    "Dynamic Mechanical Analysis (DMA)",
    "Thermal Mechanical Analysis (TMA)",
    "Heat Deflection Temperature (HDT)",
    "Melt Flow Index (MFI)"
];

// Get Thermal discipline ID
$discId = $conn->query("SELECT id FROM disciplines WHERE slug='thermal'")->fetchColumn();
if (!$discId) {
    // Create it if it doesn't exist
    $conn->query("INSERT INTO disciplines (name, slug) VALUES ('Thermal', 'thermal')");
    $discId = $conn->lastInsertId();
}

// Get all product IDs
$productIds = $conn->query("SELECT id FROM products")->fetchAll(PDO::FETCH_COLUMN);

$count = 0;
foreach ($testNames as $name) {
    // Insert test if not exists
    $stmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ?");
    $stmt->execute([$name, $discId]);
    $testId = $stmt->fetchColumn();
    
    if (!$testId) {
        $insert = $conn->prepare("INSERT INTO tests (name, discipline_id) VALUES (?, ?)");
        $insert->execute([$name, $discId]);
        $testId = $conn->lastInsertId();
    }
    
    // Map to all products
    foreach ($productIds as $pId) {
        $mapStmt = $conn->prepare("INSERT IGNORE INTO product_test (product_id, test_id) VALUES (?, ?)");
        $mapStmt->execute([$pId, $testId]);
    }
    $count++;
}

echo "Successfully added $count tests and mapped them to all products under Thermal.\n";
