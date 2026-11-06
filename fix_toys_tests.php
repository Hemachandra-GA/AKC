<?php
require 'includes/db_connect.php';
$stmt = $conn->prepare("SELECT id FROM products WHERE slug = 'toys-similar-products'");
$stmt->execute();
$prod = $stmt->fetch();
if ($prod) {
    $prod_id = $prod['id'];
    $del = $conn->prepare("DELETE FROM product_test WHERE product_id = ?");
    $del->execute([$prod_id]);
    echo "Deleted all old mappings for $prod_id\n";
}
