<?php
require 'includes/db_connect.php'; 
// Map all products to all disciplines
$conn->query("
    INSERT IGNORE INTO product_discipline (product_id, discipline_id)
    SELECT p.id, d.id
    FROM products p
    CROSS JOIN disciplines d
");
echo 'All products mapped to all disciplines.';
