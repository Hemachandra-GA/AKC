<?php
require 'includes/db_connect.php';
$stmt = $conn->query('SELECT id FROM test_bookings ORDER BY created_at DESC');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
?>
