<?php
// admin_catalog.php
include 'includes/auth_check.php';
include 'includes/db_connect.php';

// Handle Form Submissions
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Delete Product
    if (isset($_POST['delete_product'])) {
        $product_id = (int)$_POST['product_id'];
        try {
            $conn->prepare("DELETE FROM product_test WHERE product_id = ?")->execute([$product_id]);
            $conn->prepare("DELETE FROM product_discipline WHERE product_id = ?")->execute([$product_id]);
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            if ($stmt->execute([$product_id])) {
                $message = "Product sector deleted successfully!";
            }
        } catch (PDOException $e) {
            $message = "Error deleting product: " . $e->getMessage();
            $messageType = 'error';
        }
    }
    // 2. Delete Category / Discipline
    elseif (isset($_POST['delete_discipline'])) {
        $discipline_id = (int)$_POST['discipline_id'];
        try {
            $conn->prepare("DELETE FROM product_test WHERE test_id IN (SELECT id FROM tests WHERE discipline_id = ?)")->execute([$discipline_id]);
            $conn->prepare("DELETE FROM tests WHERE discipline_id = ?")->execute([$discipline_id]);
            $conn->prepare("DELETE FROM product_discipline WHERE discipline_id = ?")->execute([$discipline_id]);
            $stmt = $conn->prepare("DELETE FROM disciplines WHERE id = ?");
            if ($stmt->execute([$discipline_id])) {
                $message = "Testing Category deleted successfully!";
            }
        } catch (PDOException $e) {
            $message = "Error deleting category: " . $e->getMessage();
            $messageType = 'error';
        }
    }
    // 2b. Delete Individual Test
    elseif (isset($_POST['delete_test'])) {
        $test_id = (int)$_POST['test_id'];
        try {
            $conn->prepare("DELETE FROM product_test WHERE test_id = ?")->execute([$test_id]);
            $stmt = $conn->prepare("DELETE FROM tests WHERE id = ?");
            if ($stmt->execute([$test_id])) {
                $message = "Test deleted successfully!";
            }
        } catch (PDOException $e) {
            $message = "Error deleting test: " . $e->getMessage();
            $messageType = 'error';
        }
    }
    // 3. Add Test
    elseif (isset($_POST['add_test'])) {
        $discipline_id = $_POST['discipline_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description'] ?? '');
        $standards = trim($_POST['standards'] ?? '');
        
        if (!empty($name) && !empty($discipline_id)) {
            $stmt = $conn->prepare("INSERT INTO tests (discipline_id, name, description, standards) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$discipline_id, $name, $description, $standards])) {
                $message = "New test added successfully!";
            }
        } else {
            $message = "Category and Test Name are required.";
            $messageType = 'error';
        }
    } 
    // 4. Map Test
    elseif (isset($_POST['map_test'])) {
        $product_id = $_POST['product_id'];
        $test_id = $_POST['test_id'];
        
        $check = $conn->prepare("SELECT * FROM product_test WHERE product_id = ? AND test_id = ?");
        $check->execute([$product_id, $test_id]);
        if ($check->rowCount() > 0) {
            $message = "This test is already mapped to the selected product sector.";
            $messageType = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO product_test (product_id, test_id) VALUES (?, ?)");
            if ($stmt->execute([$product_id, $test_id])) {
                $message = "Test mapped to product sector successfully!";
            }
        }
    } 
    // 5. Unmap Test
    elseif (isset($_POST['unmap_test'])) {
        $product_id = $_POST['product_id'];
        $test_id = $_POST['test_id'];
        $stmt = $conn->prepare("DELETE FROM product_test WHERE product_id = ? AND test_id = ?");
        if ($stmt->execute([$product_id, $test_id])) {
            $message = "Test unmapped successfully!";
        }
    } 
    // 6. Add Category / Discipline
    elseif (isset($_POST['add_discipline'])) {
        $name = trim($_POST['discipline_name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        try {
            $stmt = $conn->prepare("INSERT INTO disciplines (name, slug) VALUES (?, ?)");
            if ($stmt->execute([$name, $slug])) {
                $message = "Testing Category created successfully!";
            }
        } catch(PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = 'error';
        }
    } 
    // 7. Add Product Sector
    elseif (isset($_POST['add_product'])) {
        $name = trim($_POST['product_name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        try {
            $stmt = $conn->prepare("INSERT INTO products (name, slug) VALUES (?, ?)");
            if ($stmt->execute([$name, $slug])) {
                $message = "Product sector created successfully!";
            }
        } catch(PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch stats & records
$disciplines = $conn->query("
    SELECT d.*, (SELECT COUNT(*) FROM tests WHERE discipline_id = d.id) as test_count 
    FROM disciplines d 
    ORDER BY d.name ASC
")->fetchAll(PDO::FETCH_ASSOC);

$products = $conn->query("
    SELECT p.*, (SELECT COUNT(*) FROM product_test WHERE product_id = p.id) as test_count 
    FROM products p 
    ORDER BY p.name ASC
")->fetchAll(PDO::FETCH_ASSOC);

$allTests = $conn->query("
    SELECT t.*, d.name as discipline_name 
    FROM tests t 
    LEFT JOIN disciplines d ON t.discipline_id = d.id 
    ORDER BY d.name, t.name
")->fetchAll(PDO::FETCH_ASSOC);

$mappingsStmt = $conn->query("
    SELECT p.id as product_id, p.name as product_name, t.id as test_id, t.name as test_name, d.name as disc_name
    FROM product_test pt
    JOIN products p ON pt.product_id = p.id
    JOIN tests t ON pt.test_id = t.id
    LEFT JOIN disciplines d ON t.discipline_id = d.id
    ORDER BY p.name, d.name, t.name
");
$mappings = $mappingsStmt->fetchAll(PDO::FETCH_ASSOC);

$totalCategories = count($disciplines);
$totalProducts = count($products);
$totalTestsCount = count($allTests);
$totalMappingsCount = count($mappings);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog &amp; Mappings - AKC Admin</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .catalog-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        @media (max-width: 992px) {
            .catalog-stats { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) {
            .catalog-stats { grid-template-columns: 1fr; }
        }
        .catalog-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }
        @media (max-width: 900px) {
            .catalog-grid { grid-template-columns: 1fr; }
        }
        .admin-form-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
        }
        .admin-form-card h3 {
            margin: 0 0 16px 0;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-form-group {
            margin-bottom: 14px;
        }
        .admin-form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }
        .admin-form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .admin-form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .btn-submit {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-submit:hover {
            background: #1d4ed8;
        }
        .btn-table-del {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        .btn-table-del:hover {
            background: #ef4444;
            color: #ffffff;
        }
        .table-scroll-container {
            max-height: 380px;
            overflow-y: auto;
        }
        .badge-sub {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            background: #eff6ff;
            color: #1d4ed8;
        }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <?php 
    $current_page = 'catalog';
    $page_title = 'Catalog & Mappings';
    include 'includes/admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include 'includes/admin_topbar.php'; ?>

        <div class="admin-content">

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $messageType; ?>" style="padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; <?php echo $messageType === 'success' ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;'; ?>">
                    <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                    <span style="font-weight: 500; font-size: 14px;"><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Stats Row -->
            <div class="catalog-stats">
                <div class="stat-card border-purple">
                    <div class="stat-card-header">
                        <div class="stat-icon purple"><i class="fa-solid fa-tags"></i></div>
                        <span class="stat-badge badge-neutral">Categories</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalCategories; ?></span>
                        <span class="stat-label">Testing Categories</span>
                    </div>
                </div>

                <div class="stat-card border-green">
                    <div class="stat-card-header">
                        <div class="stat-icon green"><i class="fa-solid fa-flask"></i></div>
                        <span class="stat-badge badge-neutral">Tests</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalTestsCount; ?></span>
                        <span class="stat-label">Configured Tests</span>
                    </div>
                </div>

                <div class="stat-card border-blue">
                    <div class="stat-card-header">
                        <div class="stat-icon blue"><i class="fa-solid fa-boxes-stacked"></i></div>
                        <span class="stat-badge badge-neutral">Sectors</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalProducts; ?></span>
                        <span class="stat-label">Product Sectors</span>
                    </div>
                </div>

                <div class="stat-card border-yellow">
                    <div class="stat-card-header">
                        <div class="stat-icon yellow"><i class="fa-solid fa-link"></i></div>
                        <span class="stat-badge badge-neutral">Mappings</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalMappingsCount; ?></span>
                        <span class="stat-label">Active Mappings</span>
                    </div>
                </div>
            </div>

            <!-- Forms: Add Category & Add Test -->
            <div class="catalog-grid">
                <!-- Add Category Form -->
                <div class="admin-form-card">
                    <h3><i class="fa-solid fa-tags" style="color: #7c3aed;"></i> Add New Testing Category</h3>
                    <form method="POST">
                        <input type="hidden" name="add_discipline" value="1">
                        <div class="admin-form-group">
                            <label>Category / Discipline Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="discipline_name" class="admin-form-control" required placeholder="e.g. Environmental Testing">
                        </div>
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-plus"></i> Create Category
                        </button>
                    </form>
                </div>

                <!-- Add Test Form -->
                <div class="admin-form-card">
                    <h3><i class="fa-solid fa-flask" style="color: #10b981;"></i> Add New Test under Category</h3>
                    <form method="POST">
                        <input type="hidden" name="add_test" value="1">
                        <div class="admin-form-group">
                            <label>Testing Category <span style="color:#ef4444;">*</span></label>
                            <select name="discipline_id" class="admin-form-control" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach($disciplines as $d): ?>
                                    <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label>Test Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" class="admin-form-control" required placeholder="e.g. Tensile Strength">
                        </div>
                        <div class="admin-form-group">
                            <label>Standard / Protocol</label>
                            <input type="text" name="standards" class="admin-form-control" placeholder="e.g. ASTM D638 / ISO 527">
                        </div>
                        <div class="admin-form-group">
                            <label>Description</label>
                            <textarea name="description" class="admin-form-control" rows="2" placeholder="Brief explanation or test scope..."></textarea>
                        </div>
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-plus"></i> Create Test
                        </button>
                    </form>
                </div>
            </div>

            <!-- Existing Categories & Product Sectors Tables -->
            <div class="catalog-grid">
                <!-- Categories Table -->
                <div class="card table-card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-tags" style="color: #7c3aed; margin-right: 8px;"></i> Testing Categories</h3>
                        <span class="badge-sub"><?php echo count($disciplines); ?> Total</span>
                    </div>
                    <div class="table-responsive table-scroll-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Category Name</th>
                                    <th>Slug</th>
                                    <th>Tests</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($disciplines)): ?>
                                    <?php foreach ($disciplines as $d): ?>
                                    <tr>
                                        <td>#<?php echo $d['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($d['name']); ?></strong></td>
                                        <td><code><?php echo htmlspecialchars($d['slug']); ?></code></td>
                                        <td><span class="badge-type"><?php echo $d['test_count']; ?> tests</span></td>
                                        <td style="text-align: center;">
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete category: <?php echo addslashes(htmlspecialchars($d['name'])); ?>? All tests inside it will also be deleted.');">
                                                <input type="hidden" name="delete_discipline" value="1">
                                                <input type="hidden" name="discipline_id" value="<?php echo $d['id']; ?>">
                                                <button type="submit" class="btn-table-del" title="Delete Category">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="empty-state">No categories configured.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Product Sectors Table -->
                <div class="card table-card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-boxes-stacked" style="color: #0284c7; margin-right: 8px;"></i> Product Sectors</h3>
                        <span class="badge-sub"><?php echo count($products); ?> Total</span>
                    </div>
                    <div class="table-responsive table-scroll-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Product Sector</th>
                                    <th>Slug</th>
                                    <th>Tests</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>#<?php echo $p['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                        <td><code><?php echo htmlspecialchars($p['slug']); ?></code></td>
                                        <td><span class="badge-type"><?php echo $p['test_count']; ?> tests</span></td>
                                        <td style="text-align: center;">
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete product: <?php echo addslashes(htmlspecialchars($p['name'])); ?>?');">
                                                <input type="hidden" name="delete_product" value="1">
                                                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                <button type="submit" class="btn-table-del" title="Delete Product">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="empty-state">No product sectors found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tests Directory Table with Delete Actions -->
            <div class="card table-card" style="margin-top: 25px;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3><i class="fa-solid fa-flask" style="color: #10b981; margin-right: 8px;"></i> Tests Directory (Delete / Manage Tests by Category)</h3>
                    <span class="badge-sub"><?php echo count($allTests); ?> Total Tests</span>
                </div>
                <div class="table-responsive table-scroll-container" style="max-height: 420px;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Category / Discipline</th>
                                <th>Test Name</th>
                                <th>Standard / Protocol</th>
                                <th>Description</th>
                                <th style="text-align: center; width: 90px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allTests)): ?>
                                <?php foreach($allTests as $t): ?>
                                <tr>
                                    <td>#<?php echo $t['id']; ?></td>
                                    <td><span class="badge-type"><?php echo htmlspecialchars($t['discipline_name'] ?? 'General'); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                                    <td><code><?php echo htmlspecialchars($t['standards'] ?: 'N/A'); ?></code></td>
                                    <td style="max-width: 250px; font-size: 13px; color: #64748b;"><?php echo htmlspecialchars($t['description'] ?: 'No description'); ?></td>
                                    <td style="text-align: center;">
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Permanently delete test: <?php echo addslashes(htmlspecialchars($t['name'])); ?>?');">
                                            <input type="hidden" name="delete_test" value="1">
                                            <input type="hidden" name="test_id" value="<?php echo $t['id']; ?>">
                                            <button type="submit" class="btn-table-del" title="Delete Test">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="empty-state">No tests found in the database.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mapping Section: Add Product & Map Test -->
            <div class="catalog-grid" style="margin-top: 25px;">
                <!-- Add Product Sector -->
                <div class="admin-form-card">
                    <h3><i class="fa-solid fa-cube" style="color: #0284c7;"></i> Add New Product Sector</h3>
                    <form method="POST">
                        <input type="hidden" name="add_product" value="1">
                        <div class="admin-form-group">
                            <label>Product Sector Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="product_name" class="admin-form-control" required placeholder="e.g. Biodegradable Plastics">
                        </div>
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-plus"></i> Create Product Sector
                        </button>
                    </form>
                </div>

                <!-- Map Test to Product -->
                <div class="admin-form-card">
                    <h3><i class="fa-solid fa-link" style="color: #f59e0b;"></i> Map Test to Product Sector</h3>
                    <form method="POST">
                        <input type="hidden" name="map_test" value="1">
                        <div class="admin-form-group">
                            <label>Select Product Sector <span style="color:#ef4444;">*</span></label>
                            <select name="product_id" class="admin-form-control" required>
                                <option value="">-- Select Product --</option>
                                <?php foreach($products as $p): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label>Select Test <span style="color:#ef4444;">*</span></label>
                            <select name="test_id" class="admin-form-control" required>
                                <option value="">-- Select Test --</option>
                                <?php foreach($allTests as $t): ?>
                                    <option value="<?php echo $t['id']; ?>">[<?php echo htmlspecialchars($t['discipline_name'] ?? 'General'); ?>] <?php echo htmlspecialchars($t['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit" style="background: #f59e0b;">
                            <i class="fa-solid fa-link"></i> Map Test
                        </button>
                    </form>
                </div>
            </div>

            <!-- Active Mappings Table -->
            <div class="card table-card" style="margin-top: 25px;">
                <div class="card-header">
                    <h3><i class="fa-solid fa-list-check" style="color: #6366f1; margin-right: 8px;"></i> Active Product-to-Test Mappings</h3>
                    <span class="badge-sub"><?php echo count($mappings); ?> Mappings</span>
                </div>
                <div class="table-responsive table-scroll-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Product Sector</th>
                                <th>Category</th>
                                <th>Test Name</th>
                                <th style="text-align: center; width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mappings)): ?>
                                <?php foreach($mappings as $m): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($m['product_name']); ?></strong></td>
                                    <td><span class="badge-type"><?php echo htmlspecialchars($m['disc_name'] ?? 'General'); ?></span></td>
                                    <td><?php echo htmlspecialchars($m['test_name']); ?></td>
                                    <td style="text-align: center;">
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Unmap this test from <?php echo addslashes(htmlspecialchars($m['product_name'])); ?>?');">
                                            <input type="hidden" name="unmap_test" value="1">
                                            <input type="hidden" name="product_id" value="<?php echo $m['product_id']; ?>">
                                            <input type="hidden" name="test_id" value="<?php echo $m['test_id']; ?>">
                                            <button type="submit" class="btn-table-del" title="Unmap Test">
                                                <i class="fa-solid fa-unlink"></i> Unmap
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="empty-state">No product-test mappings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
