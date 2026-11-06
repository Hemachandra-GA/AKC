<?php
require 'includes/db_connect.php'; 
$mechId = $conn->query("SELECT id FROM disciplines WHERE slug='mechanical'")->fetchColumn(); 
$chemId = $conn->query("SELECT id FROM disciplines WHERE slug='chemical'")->fetchColumn(); 
if($mechId && $chemId) { 
    $conn->query("INSERT IGNORE INTO product_discipline (product_id, discipline_id) SELECT product_id, $mechId FROM product_discipline WHERE discipline_id = $chemId"); 
    echo 'Mapped products to Mechanical.'; 
} else { 
    echo 'Could not find disciplines.'; 
}
