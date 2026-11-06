<?php
$db = new PDO('sqlite:includes/database.sqlite');
$db->exec("UPDATE products SET name='Polymer and Polymeric Products', slug='polymer-and-polymeric-products' WHERE id=1");
echo "Updated!";
?>
