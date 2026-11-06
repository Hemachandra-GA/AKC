<?php
require_once 'includes/db_connect.php';

try {
    // 1. Insert disciplines
    $conn->exec("INSERT INTO disciplines (name, slug) VALUES 
        ('Chemical', 'chemical'),
        ('Mechanical', 'mechanical'),
        ('Thermal', 'thermal')
        ON DUPLICATE KEY UPDATE name=VALUES(name)");

    // Get discipline IDs
    $stmt = $conn->query("SELECT id, name FROM disciplines");
    $disciplines = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $disciplines[$row['name']] = $row['id'];
    }

    // 2. Insert Products
    $conn->exec("INSERT INTO products (name, slug, description) VALUES 
        ('Polymer & Polymeric Products Testing', 'polymer-polymeric-products-testing', 'Advanced chemical analysis and mechanical testing for polymers.'),
        ('Textile & Textile Products', 'textile-products', 'Testing for textiles across different domains.')
        ON DUPLICATE KEY UPDATE name=VALUES(name)");

    $stmt = $conn->query("SELECT id, name FROM products");
    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[$row['name']] = $row['id'];
    }

    // 3. Map Products to Disciplines (Many to Many)
    $prod1 = $products['Polymer & Polymeric Products Testing'];
    $prod2 = $products['Textile & Textile Products'];
    
    $discChem = $disciplines['Chemical'];
    $discMech = $disciplines['Mechanical'];
    $discTherm = $disciplines['Thermal'];

    // Polymer product belongs to Chemical, Mechanical, Thermal
    $conn->exec("INSERT IGNORE INTO product_discipline (product_id, discipline_id) VALUES 
        ($prod1, $discChem),
        ($prod1, $discMech),
        ($prod1, $discTherm),
        ($prod2, $discChem)");

    // 4. Insert Tests
    // Chemical tests
    for($i = 1; $i <= 5; $i++) {
        $conn->exec("INSERT INTO tests (name, description, discipline_id) VALUES ('Chemical Test $i', 'Description for chemical test $i', $discChem)");
    }
    // Mechanical tests
    for($i = 1; $i <= 5; $i++) {
        $conn->exec("INSERT INTO tests (name, description, discipline_id) VALUES ('Mechanical Test $i', 'Description for mechanical test $i', $discMech)");
    }

    // 5. Map Tests to Products
    // Get chem tests
    $stmt = $conn->query("SELECT id FROM tests WHERE discipline_id = $discChem");
    $chemTests = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get mech tests
    $stmt = $conn->query("SELECT id FROM tests WHERE discipline_id = $discMech");
    $mechTests = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Map some tests to Polymeric Product
    foreach($chemTests as $tId) {
        $conn->exec("INSERT IGNORE INTO product_test (product_id, test_id) VALUES ($prod1, $tId)");
    }
    // Map only a few mech tests to Polymeric Product
    $conn->exec("INSERT IGNORE INTO product_test (product_id, test_id) VALUES ($prod1, {$mechTests[0]}), ($prod1, {$mechTests[1]})");
    
    echo "Sample data inserted successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
