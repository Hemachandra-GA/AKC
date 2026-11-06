<?php
require 'includes/db_connect.php';
$stmt = $conn->query('SELECT id, slug, name FROM products');
foreach($stmt as $row) {
    echo $row['slug'] . " -> " . $row['name'] . "\n";
}
