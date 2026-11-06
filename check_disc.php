<?php
require_once 'includes/db_connect.php';
$stmt = $conn->query("SELECT name FROM disciplines");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['name'] . "\n";
}
