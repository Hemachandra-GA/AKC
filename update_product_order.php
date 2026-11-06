<?php
require_once 'includes/db_connect.php';

try {
    try {
        $conn->exec("ALTER TABLE products ADD COLUMN sort_order INT DEFAULT 99");
        echo "Added sort_order column.\n";
    } catch (PDOException $e) {
        // column likely exists
        echo "sort_order might already exist.\n";
    }

    // Set sort orders
    $orders = [
        'polymer-polymeric' => 1,
        'textile-textile' => 2,
        'leather-leatherite' => 3,
        'rubber-rubberic' => 4,
        'toys-similar' => 5,
        'packaging-specific' => 6,
        'animal-food-feed' => 7,
        'food-agricultural' => 8,
        'residue-contaminants' => 9,
        'atmospheric-pollution' => 10,
    ];

    $stmt = $conn->prepare("UPDATE products SET sort_order = ? WHERE slug LIKE ?");

    foreach ($orders as $slug => $order) {
        $stmt->execute([$order, '%' . $slug . '%']);
        if ($stmt->rowCount() > 0) {
            echo "Updated $slug to order $order (" . $stmt->rowCount() . " rows)\n";
        } else {
            echo "Failed to update $slug (not found?)\n";
        }
    }

    echo "Done updating orders.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
