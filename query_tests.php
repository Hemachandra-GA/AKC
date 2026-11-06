<?php
require 'includes/db_connect.php';
$stmt=$conn->query("SELECT t.name, t.description FROM product_test pt JOIN tests t ON pt.test_id = t.id JOIN products p ON pt.product_id=p.id WHERE p.slug='toys-similar-products'");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($res) . "\n";
print_r($res);
