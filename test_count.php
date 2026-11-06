<?php
require 'includes/db_connect.php';
$stmt = $conn->query('SELECT COUNT(*) FROM test_bookings');
echo "Count: " . $stmt->fetchColumn() . "\n";
?>
