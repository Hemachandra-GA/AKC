<?php
require_once __DIR__ . '/includes/db_connect.php';

echo "<div style='font-family: sans-serif; max-width: 700px; margin: 40px auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>";
echo "<h2 style='color: #0284c7; margin-top: 0;'>AKC Database Diagnostics &amp; Verification</h2>";

try {
    $tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>SQLite Tables found:</strong><br><span style='color: #475569;'>" . implode(', ', $tables) . "</span></p>";
    
    if (in_array('products', $tables)) {
        $prodCount = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $testCount = $conn->query("SELECT COUNT(*) FROM tests")->fetchColumn();
        $linkCount = $conn->query("SELECT COUNT(*) FROM product_test")->fetchColumn();
        echo "<div style='background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 8px;'>";
        echo "<strong>&check; Database is Ready!</strong><br>";
        echo "Found <strong>{$prodCount}</strong> products, <strong>{$testCount}</strong> tests, and <strong>{$linkCount}</strong> product-test relations.";
        echo "</div>";
    } else {
        echo "<p style='color: #dc2626;'>Table 'products' does not exist yet. Initializing now...</p>";
        if (function_exists('init_sqlite_database')) {
            init_sqlite_database($conn);
            $prodCount = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
            $testCount = $conn->query("SELECT COUNT(*) FROM tests")->fetchColumn();
            echo "<p style='color: #16a34a;'><strong>&check; Initialized!</strong> Found {$prodCount} products and {$testCount} tests.</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: #dc2626;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<div style='margin-top: 25px; display: flex; gap: 15px;'>";
echo "<a href='dynamic-product.php?slug=polymer-and-polymeric-products' style='background: #0284c7; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: 500;'>View Polymer Products Testing &rarr;</a>";
echo "<a href='index.php' style='background: #e2e8f0; color: #1e293b; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: 500;'>Go to Home &rarr;</a>";
echo "</div>";
echo "</div>";

