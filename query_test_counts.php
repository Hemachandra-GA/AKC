<?php
require 'includes/db_connect.php';
$stmt=$conn->query("SELECT d.name, count(*) as c FROM product_test pt JOIN tests t ON pt.test_id = t.id JOIN disciplines d ON t.discipline_id = d.id WHERE pt.product_id=28 GROUP BY d.id");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
