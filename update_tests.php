<?php
require_once 'includes/db_connect.php';

try {
    // 1. Find product 'polymer-polymeric-products-testing'
    $stmt = $conn->prepare("SELECT id FROM products WHERE slug = 'polymer-polymeric-products-testing'");
    $stmt->execute();
    $prod_id = $stmt->fetchColumn();

    // 2. Find discipline 'chemical'
    $stmt = $conn->prepare("SELECT id FROM disciplines WHERE slug = 'chemical'");
    $stmt->execute();
    $disc_id = $stmt->fetchColumn();

    // Make sure 'standards' column exists
    try {
        $conn->exec("ALTER TABLE tests ADD COLUMN standards VARCHAR(255)");
    } catch (Exception $e) {
        // Assume it exists or ignore
    }

    $conn->exec("DELETE FROM product_test");
    $conn->exec("DELETE FROM tests");

    $testsData = [
        [
            'name' => 'Ash Content',
            'description' => 'Quantitative determination of inorganic residue remaining after complete combustion of plastic or polymer samples — confirming filler content, additive loading, and purity.',
            'standards' => 'ISO 3451 / ASTM D5630',
            'discipline_id' => $disc_id
        ],
        [
            'name' => 'Migration Test',
            'description' => 'Assessment of chemical substances that may migrate from plastic materials into food, beverages, or body-contact environments under simulated conditions.',
            'standards' => 'IS 9845 / EU 10/2011 / FDA 21 CFR',
            'discipline_id' => $disc_id
        ],
        [
            'name' => 'Ageing Test',
            'description' => 'Thermal conditioning and chemical ageing evaluation to predict long-term durability and resistance to UV, heat, and environmental exposure.',
            'standards' => 'ISO 188 / ASTM D573',
            'discipline_id' => $disc_id
        ],
        [
            'name' => 'Material Identification',
            'description' => 'Qualitative and quantitative analysis to precisely identify polymers and confirm material type, grade, and composition using FTIR, GC-MS, DSC, and TGA.',
            'standards' => 'ISO 10640 / ASTM E1421 / ASTM D3418',
            'discipline_id' => $disc_id
        ],
        [
            'name' => 'Adhesion Test',
            'description' => 'Measurement of coating adhesion strength on plastic substrates using cross-cut, peel, and pull-off methods to international standards.',
            'standards' => 'ISO 2409 / ASTM D3359',
            'discipline_id' => $disc_id
        ],
        [
            'name' => 'Water Absorption',
            'description' => 'Determination of moisture absorbed by plastic materials under immersion conditions to assess dimensional stability and performance.',
            'standards' => 'ISO 62 / ASTM D570',
            'discipline_id' => $disc_id
        ]
    ];

    $insertTestStmt = $conn->prepare("INSERT INTO tests (discipline_id, name, description, standards) VALUES (?, ?, ?, ?)");
    $insertMapStmt = $conn->prepare("INSERT INTO product_test (product_id, test_id) VALUES (?, ?)");

    foreach ($testsData as $t) {
        $insertTestStmt->execute([$t['discipline_id'], $t['name'], $t['description'], $t['standards']]);
        $test_id = $conn->lastInsertId();
        
        $insertMapStmt->execute([$prod_id, $test_id]);
    }

    echo "Successfully inserted tests!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
