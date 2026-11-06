<?php
require_once 'includes/db_connect.php';

$targets = [
    'textile-textile' => 'textile',
    'leather-leatherite' => 'leather',
    'rubber-rubberic' => 'rubber'
];

foreach ($targets as $slug => $keyword) {
    // get product id
    $stmt = $conn->prepare("SELECT id FROM products WHERE slug LIKE ?");
    $stmt->execute([$slug . '%']);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($prod) {
        $prod_id = $prod['id'];

        // Find tests mapped to this product that DO NOT have the keyword in their description,
        // and delete their mapping. (Assuming the generic ones have 'polymer' or don't have the keyword)
        $delStmt = $conn->prepare("
            DELETE FROM product_test 
            WHERE product_id = ? 
            AND test_id IN (
                SELECT id FROM tests WHERE description NOT LIKE ?
            )
        ");
        // For textile, if desc doesn't have '%textile%', delete the mapping.
        $delStmt->execute([$prod_id, '%' . $keyword . '%']);
        
        echo "Cleaned up mappings for $slug (Deleted " . $delStmt->rowCount() . " generic tests).\n";
    }
}
?>
