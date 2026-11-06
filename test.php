<?php
require 'includes/db_connect.php'; 
print_r($conn->query("SELECT id, estimated_time FROM test_bookings WHERE id IN (13, 14)")->fetchAll(PDO::FETCH_ASSOC));
