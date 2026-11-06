<?php
$db = new PDO('sqlite:includes/database.sqlite');
$stmt = $db->query("SELECT * FROM products");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id'] . " - " . $row['name'] . " - " . $row['slug'] . "\n";
}
?>
