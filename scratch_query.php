<?php
require 'includes/db_connect.php';
$rows = $conn->query('SELECT pt.*, p.slug as prod_slug, p.name as prod_name, t.name as test_name, d.slug as disc_slug FROM product_test pt JOIN products p ON pt.product_id = p.id JOIN tests t ON pt.test_id = t.id JOIN disciplines d ON t.discipline_id = d.id')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
