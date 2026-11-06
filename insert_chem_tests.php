<?php
require 'includes/db_connect.php'; 

$testNames = [
    "Viscosity Measurement",
    "Specific Gravity and density measurement",
    "Ash Content",
    "Carbon Black Content",
    "Carbon Black Dispersion Test",
    "Volatile organic Content test", 
    "Water and Moisture Content",
    "Water absorbance and Effect on water",
    "Heavy Metal Analysis",
    "Overall Migration test",
    "Specific Migration test",
    "Accelerated Aging test", 
    "Oven ageing test",
    "Adhesion test",
    "Environmental Stress Crack Resistance",
    "Residual solvent analysis",
    "pH value determination",
    "Conductivity Measurement",
    "Resistance to chemical"
];

// Get Chemical discipline ID
$chemId = $conn->query("SELECT id FROM disciplines WHERE slug='chemical'")->fetchColumn();
if (!$chemId) die("Chemical discipline not found.");

// Get all product IDs
$productIds = $conn->query("SELECT id FROM products")->fetchAll(PDO::FETCH_COLUMN);

$count = 0;
foreach ($testNames as $name) {
    // Insert test if not exists
    $stmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ?");
    $stmt->execute([$name, $chemId]);
    $testId = $stmt->fetchColumn();
    
    if (!$testId) {
        $insert = $conn->prepare("INSERT INTO tests (name, discipline_id) VALUES (?, ?)");
        $insert->execute([$name, $chemId]);
        $testId = $conn->lastInsertId();
    }
    
    // Map to all products
    foreach ($productIds as $pId) {
        $mapStmt = $conn->prepare("INSERT IGNORE INTO product_test (product_id, test_id) VALUES (?, ?)");
        $mapStmt->execute([$pId, $testId]);
    }
    $count++;
}

echo "Successfully added $count tests and mapped them to all products under Chemical.\n";
