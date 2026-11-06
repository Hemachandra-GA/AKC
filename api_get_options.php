<?php
if (function_exists('opcache_reset')) { opcache_reset(); }
require_once 'includes/db_connect.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// 1. Get Products
if ($action === 'get_products') {
    $stmt = $conn->query("SELECT id, name, slug, sort_order FROM products ORDER BY sort_order ASC, name ASC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
    exit;
}

// 2. Get Categories for a selected Product
if ($action === 'get_categories') {
    $productSlug = $_GET['product'] ?? '';
    
    if ($productSlug) {
        $stmt = $conn->prepare("
            SELECT DISTINCT d.id, d.name, d.slug 
            FROM disciplines d
            JOIN tests t ON d.id = t.discipline_id
            JOIN product_test pt ON t.id = pt.test_id
            JOIN products p ON pt.product_id = p.id
            WHERE p.slug = ? OR p.id = ? OR LOWER(p.name) = LOWER(?)
            ORDER BY d.name ASC
        ");
        $stmt->execute([$productSlug, $productSlug, $productSlug]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($categories)) {
            $catStmt = $conn->query("SELECT id, name, slug FROM disciplines ORDER BY name ASC");
            $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        $catStmt = $conn->query("SELECT id, name, slug FROM disciplines ORDER BY name ASC");
        $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($categories);
    exit;
}

// 3. Get Tests for a selected Product and Category
if ($action === 'get_tests') {
    $productSlug = $_GET['product'] ?? '';
    $categorySlug = $_GET['category'] ?? ($_GET['discipline'] ?? '');

    if ($productSlug && $categorySlug) {
        $stmt = $conn->prepare("
            SELECT DISTINCT t.id, t.name 
            FROM tests t
            JOIN disciplines d ON t.discipline_id = d.id
            JOIN product_test pt ON t.id = pt.test_id
            JOIN products p ON pt.product_id = p.id
            WHERE (p.slug = ? OR p.id = ? OR LOWER(p.name) = LOWER(?)) 
              AND (d.slug = ? OR d.id = ? OR LOWER(d.name) = LOWER(?))
            ORDER BY t.name ASC
        ");
        $stmt->execute([$productSlug, $productSlug, $productSlug, $categorySlug, $categorySlug, $categorySlug]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($productSlug) {
        $stmt = $conn->prepare("
            SELECT DISTINCT t.id, t.name 
            FROM tests t
            JOIN product_test pt ON t.id = pt.test_id
            JOIN products p ON pt.product_id = p.id
            WHERE p.slug = ? OR p.id = ? OR LOWER(p.name) = LOWER(?)
            ORDER BY t.name ASC
        ");
        $stmt->execute([$productSlug, $productSlug, $productSlug]);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->query("SELECT id, name FROM tests ORDER BY name ASC LIMIT 100");
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($tests);
    exit;
}

echo json_encode([]);
