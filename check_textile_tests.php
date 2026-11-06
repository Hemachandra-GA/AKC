<?php
require_once 'includes/db_connect.php';

$stmt = $conn->prepare("
    SELECT t.id, t.name, t.description 
    FROM tests t
    JOIN product_test pt ON t.id = pt.test_id
    JOIN products p ON pt.product_id = p.id
    WHERE p.slug = 'textile-textile-products'
    ORDER BY t.name
");
$stmt->execute();
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tests as $t) {
    echo "ID: " . $t['id'] . " | " . $t['name'] . " | " . substr($t['description'], 0, 50) . "\n";
}
