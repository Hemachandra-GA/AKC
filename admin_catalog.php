<?php
// admin_catalog.php - Redesigned Clean & Structured Catalog Manager
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
            $message = "Test unmapped from product sector successfully!";
        }
    }
    // 6. Add Product
    elseif (isset($_POST['add_product'])) {
        $product_name = trim($_POST['product_name']);
        if (!empty($product_name)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product_name)));
            $stmt = $conn->prepare("INSERT INTO products (name, slug) VALUES (?, ?)");
            if ($stmt->execute([$product_name, $slug])) {
                $message = "New product sector added successfully!";
            }
        }
    }
    // 7. Add Discipline / Category
    elseif (isset($_POST['add_discipline'])) {
        $discipline_name = trim($_POST['discipline_name']);
        if (!empty($discipline_name)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $discipline_name)));
            $stmt = $conn->prepare("INSERT INTO disciplines (name, slug) VALUES (?, ?)");
            if ($stmt->execute([$discipline_name, $slug])) {
                $message = "New testing category added successfully!";
            }
        }
    }
}

// Fetch Catalog Data
$disciplines = $conn->query("
    SELECT d.*, COUNT(t.id) as test_count 
    FROM disciplines d 
    LEFT JOIN tests t ON d.id = t.discipline_id 
    GROUP BY d.id 
    ORDER BY d.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$products = $conn->query("
    SELECT p.*, COUNT(pt.test_id) as test_count 
    FROM products p 
    LEFT JOIN product_test pt ON p.id = pt.product_id 
    GROUP BY p.id 
    ORDER BY p.id ASC
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
        .catalog-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Top workflow visual bar */
        .catalog-stepper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 25px;
        }
        @media (max-width: 900px) {
            .catalog-stepper { grid-template-columns: 1fr; }
        }
        .step-pill {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }
        .step-pill:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
        }
        .step-number {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }
        .step-info h4 {
            margin: 0 0 4px 0;
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
        }
        .step-info p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
        }

        /* Tab Navigation */
        .catalog-tabs-bar {
            display: flex;
            gap: 8px;
            background: #e2e8f0;
            padding: 5px;
            border-radius: 12px;
            margin-bottom: 25px;
            overflow-x: auto;
        }
        .catalog-tab-btn {
            flex: 1;
            min-width: 180px;
            padding: 12px 18px;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 13.5px;
            font-weight: 700;
            font-family: inherit;
            border-radius: 9px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .catalog-tab-btn i {
            font-size: 15px;
        }
        .catalog-tab-btn .tab-count {
            padding: 2px 7px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(100, 116, 139, 0.15);
            color: #475569;
        }
        .catalog-tab-btn.active {
            background: #ffffff;
            color: #2563eb;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        }
        .catalog-tab-btn.active .tab-count {
            background: #eff6ff;
            color: #2563eb;
        }

        /* Tab Panels */
        .tab-panel {
            display: none;
            animation: fadeIn 0.25s ease-in-out;
        }
        .tab-panel.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Layout Grids: Clean 1-Column Flow */
        .section-split {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* Form Card */
        .admin-form-card {
            background: #ffffff;
            border-radius: 14px;
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
        /* Form Grid: horizontal multi-column inputs inside the form */
        .admin-form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            align-items: flex-end;
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
            border: 1.5px solid #cbd5e1;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13.5px;
            color: #1e293b;
            background: #fff;
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .admin-form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        .btn-submit {
            background: #2563eb;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 9px;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
        .btn-table-del {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 6px 12px;
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
            max-height: 520px;
            overflow-y: auto;
        }
        .badge-sub {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 5px;
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
            <div class="catalog-container">

                <!-- Alert Message -->
                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo $messageType; ?>" style="padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; <?php echo $messageType === 'success' ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;'; ?>">
                        <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                        <span style="font-weight: 600; font-size: 14px;"><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Workflow Stepper / Overview -->
                <div class="catalog-stepper">
                    <div class="step-pill" onclick="switchCatalogTab('tab-tests')" style="cursor: pointer;">
                        <div class="step-number" style="background: #10b981;">1</div>
                        <div class="step-info">
                            <h4>Master Tests Directory</h4>
                            <p><?php echo $totalTestsCount; ?> tests available across 4 disciplines</p>
                        </div>
                    </div>
                    <div class="step-pill" onclick="switchCatalogTab('tab-sectors')" style="cursor: pointer;">
                        <div class="step-number" style="background: #0284c7;">2</div>
                        <div class="step-info">
                            <h4>Product Sectors</h4>
                            <p><?php echo $totalProducts; ?> industry product categories</p>
                        </div>
                    </div>
                    <div class="step-pill" onclick="switchCatalogTab('tab-mappings')" style="cursor: pointer;">
                        <div class="step-number" style="background: #f59e0b;">3</div>
                        <div class="step-info">
                            <h4>Product-to-Test Links</h4>
                            <p><?php echo $totalMappingsCount; ?> active front-end mappings</p>
                        </div>
                    </div>
                </div>

                <!-- Structured Clean Tab Navigation -->
                <div class="catalog-tabs-bar">
                    <button class="catalog-tab-btn active" id="btn-tab-tests" onclick="switchCatalogTab('tab-tests')">
                        <i class="fa-solid fa-flask" style="color: #10b981;"></i> 
                        Step 1: Tests Directory
                        <span class="tab-count"><?php echo $totalTestsCount; ?></span>
                    </button>
                    <button class="catalog-tab-btn" id="btn-tab-sectors" onclick="switchCatalogTab('tab-sectors')">
                        <i class="fa-solid fa-boxes-stacked" style="color: #0284c7;"></i> 
                        Step 2: Product Sectors
                        <span class="tab-count"><?php echo $totalProducts; ?></span>
                    </button>
                    <button class="catalog-tab-btn" id="btn-tab-mappings" onclick="switchCatalogTab('tab-mappings')">
                        <i class="fa-solid fa-link" style="color: #f59e0b;"></i> 
                        Step 3: Test Mappings
                        <span class="tab-count"><?php echo $totalMappingsCount; ?></span>
                    </button>
                    <button class="catalog-tab-btn" id="btn-tab-categories" onclick="switchCatalogTab('tab-categories')">
                        <i class="fa-solid fa-tags" style="color: #7c3aed;"></i> 
                        Categories / Disciplines
                        <span class="tab-count"><?php echo $totalCategories; ?></span>
                    </button>
                </div>

                <!-- ========================================== -->
                <!-- TAB 1: TESTS DIRECTORY                     -->
                <!-- ========================================== -->
                <div class="tab-panel active" id="tab-tests">
                    <div class="section-split">
                        <!-- Top: Add Test Form (Horizontal Form Bar) -->
                        <div class="admin-form-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <h3 style="margin: 0;"><i class="fa-solid fa-plus-circle" style="color: #10b981;"></i> Add New Test</h3>
                                <span style="font-size: 12.5px; color: #64748b;">Save a new test into the master test library</span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="add_test" value="1">
                                <div class="admin-form-row">
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
                                        <input type="text" name="name" class="admin-form-control" required placeholder="e.g. Viscosity Measurement">
                                    </div>
                                    <div class="admin-form-group">
                                        <label>Standard / Protocol</label>
                                        <input type="text" name="standards" class="admin-form-control" placeholder="e.g. ISO 1628 / ASTM D445">
                                    </div>
                                    <div class="admin-form-group">
                                        <label>Description (Optional)</label>
                                        <input type="text" name="description" class="admin-form-control" placeholder="Brief scope or method summary...">
                                    </div>
                                    <div class="admin-form-group" style="margin-bottom: 0;">
                                        <button type="submit" class="btn-submit" style="background: #10b981; height: 42px;">
                                            <i class="fa-solid fa-plus"></i> Save Test
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right: Tests Table with Search & Filter -->
                        <div class="card table-card" style="margin-top: 0;">
                            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                                <div>
                                    <h3 style="margin: 0;"><i class="fa-solid fa-flask" style="color: #10b981; margin-right: 8px;"></i> All Configured Tests</h3>
                                    <span id="tests-count-badge" class="badge-sub" style="margin-top: 4px; display: inline-block;"><?php echo count($allTests); ?> Total Tests</span>
                                </div>

                                <!-- Search & Filters -->
                                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                    <div style="position: relative; min-width: 220px;">
                                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                                        <input type="text" id="testSearchInput" placeholder="Search test or standard..." 
                                            style="width: 100%; padding: 8px 12px 8px 34px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; transition: border-color 0.2s;"
                                            onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'" onkeyup="filterTestsTable()">
                                    </div>

                                    <select id="testCategoryFilter" onchange="filterTestsTable()" 
                                        style="padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: #fff; cursor: pointer;">
                                        <option value="">All Categories</option>
                                        <?php foreach ($disciplines as $d): ?>
                                            <option value="<?php echo htmlspecialchars(strtolower($d['name'])); ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="button" onclick="resetTestsFilter()" title="Reset Filters"
                                        style="padding: 8px 12px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-rotate-left"></i> Reset
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive table-scroll-container">
                                <table class="admin-table" id="testsDirectoryTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px; text-align: center;">S.No</th>
                                            <th style="width: 60px;">ID</th>
                                            <th>Category</th>
                                            <th>Test Name</th>
                                            <th>Standard / Protocol</th>
                                            <th>Description</th>
                                            <th style="text-align: center; width: 85px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($allTests)): ?>
                                            <?php $sno = 1; foreach($allTests as $t): ?>
                                            <tr class="test-table-row" data-category="<?php echo htmlspecialchars(strtolower($t['discipline_name'] ?? 'general')); ?>" data-name="<?php echo htmlspecialchars(strtolower($t['name'])); ?>" data-standard="<?php echo htmlspecialchars(strtolower($t['standards'] ?? '')); ?>">
                                                <td style="text-align: center; font-weight: 600; color: #64748b;" class="test-sno"><?php echo $sno++; ?></td>
                                                <td><span style="font-size: 12px; color: #94a3b8; font-weight: 600;">#<?php echo $t['id']; ?></span></td>
                                                <td><span class="badge-type"><?php echo htmlspecialchars($t['discipline_name'] ?? 'General'); ?></span></td>
                                                <td><strong style="color: #1e293b;"><?php echo htmlspecialchars($t['name']); ?></strong></td>
                                                <td><code><?php echo htmlspecialchars($t['standards'] ?: 'N/A'); ?></code></td>
                                                <td style="max-width: 260px; font-size: 13px; color: #64748b; line-height: 1.4;"><?php echo htmlspecialchars($t['description'] ?: 'No description'); ?></td>
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
                                            <tr id="noTestsRow"><td colspan="7" class="empty-state">No tests found in the database.</td></tr>
                                        <?php endif; ?>
                                        <tr id="noResultsRow" style="display: none;"><td colspan="7" class="empty-state">No tests match your search criteria.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAB 2: PRODUCT SECTORS                     -->
                <!-- ========================================== -->
                <div class="tab-panel" id="tab-sectors">
                    <div class="section-split">
                        <!-- Top: Add Product Form -->
                        <div class="admin-form-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <h3 style="margin: 0;"><i class="fa-solid fa-boxes-stacked" style="color: #0284c7;"></i> Add New Product Sector</h3>
                                <span style="font-size: 12.5px; color: #64748b;">Creates a new industry sector and dedicated landing page</span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="add_product" value="1">
                                <div class="admin-form-row">
                                    <div class="admin-form-group" style="flex: 2;">
                                        <label>Product Sector Name <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="product_name" class="admin-form-control" required placeholder="e.g. Biodegradable Plastics">
                                    </div>
                                    <div class="admin-form-group" style="flex: 1; margin-bottom: 0;">
                                        <button type="submit" class="btn-submit" style="background: #0284c7; height: 42px;">
                                            <i class="fa-solid fa-plus"></i> Create Sector
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right: Product Sectors Table -->
                        <div class="card table-card" style="margin-top: 0;">
                            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                                <h3><i class="fa-solid fa-boxes-stacked" style="color: #0284c7; margin-right: 8px;"></i> Active Product Sectors (<?php echo count($products); ?>)</h3>
                                <span class="badge-sub">Used across menu &amp; pages</span>
                            </div>
                            <div class="table-responsive table-scroll-container">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">S.No</th>
                                            <th style="width: 60px;">ID</th>
                                            <th>Product Sector Name</th>
                                            <th>URL Slug</th>
                                            <th>Mapped Tests</th>
                                            <th style="text-align: center; width: 90px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($products)): ?>
                                            <?php $psno = 1; foreach ($products as $p): ?>
                                            <tr>
                                                <td style="text-align: center; font-weight: 600; color: #64748b;"><?php echo $psno++; ?></td>
                                                <td><span style="color:#94a3b8; font-weight:600;">#<?php echo $p['id']; ?></span></td>
                                                <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                                <td><code><?php echo htmlspecialchars($p['slug']); ?></code></td>
                                                <td><span class="badge-type"><?php echo $p['test_count']; ?> tests</span></td>
                                                <td style="text-align: center;">
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete product: <?php echo addslashes(htmlspecialchars($p['name'])); ?>?');">
                                                        <input type="hidden" name="delete_product" value="1">
                                                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                        <button type="submit" class="btn-table-del" title="Delete Product">
                                                            <i class="fa-solid fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="empty-state">No product sectors found.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAB 3: PRODUCT-TO-TEST MAPPINGS            -->
                <!-- ========================================== -->
                <div class="tab-panel" id="tab-mappings">
                    <div class="section-split">
                        <!-- Top: Map Test Form -->
                        <div class="admin-form-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <h3 style="margin: 0;"><i class="fa-solid fa-link" style="color: #f59e0b;"></i> Map Test to Product</h3>
                                <span style="font-size: 12.5px; color: #64748b;">Links an existing test to an industry sector to show on its page</span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="map_test" value="1">
                                <div class="admin-form-row">
                                    <div class="admin-form-group" style="flex: 1.2;">
                                        <label>Select Product Sector <span style="color:#ef4444;">*</span></label>
                                        <select name="product_id" class="admin-form-control" required>
                                            <option value="">-- Select Product --</option>
                                            <?php foreach($products as $p): ?>
                                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="admin-form-group" style="flex: 1.5;">
                                        <label>Select Test <span style="color:#ef4444;">*</span></label>
                                        <select name="test_id" class="admin-form-control" required>
                                            <option value="">-- Select Test --</option>
                                            <?php foreach($allTests as $t): ?>
                                                <option value="<?php echo $t['id']; ?>">[<?php echo htmlspecialchars($t['discipline_name'] ?? 'General'); ?>] <?php echo htmlspecialchars($t['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="admin-form-group" style="flex: 0.8; margin-bottom: 0;">
                                        <button type="submit" class="btn-submit" style="background: #f59e0b; height: 42px;">
                                            <i class="fa-solid fa-link"></i> Link Test
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right: Active Mappings Table with Search & Filter -->
                        <div class="card table-card" style="margin-top: 0;">
                            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                                <div>
                                    <h3 style="margin: 0;"><i class="fa-solid fa-list-check" style="color: #f59e0b; margin-right: 8px;"></i> Active Front-End Mappings</h3>
                                    <span id="mappings-count-badge" class="badge-sub" style="margin-top: 4px; display: inline-block;"><?php echo count($mappings); ?> Mappings</span>
                                </div>

                                <!-- Filter by product -->
                                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                    <select id="mappingProductFilter" onchange="filterMappingsTable()" 
                                        style="padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: #fff; cursor: pointer; max-width: 250px;">
                                        <option value="">All Product Sectors</option>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?php echo htmlspecialchars(strtolower($p['name'])); ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                    <div style="position: relative; min-width: 200px;">
                                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                                        <input type="text" id="mappingSearchInput" placeholder="Search test name..." 
                                            style="width: 100%; padding: 8px 12px 8px 34px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none;"
                                            onkeyup="filterMappingsTable()">
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive table-scroll-container">
                                <table class="admin-table" id="mappingsDirectoryTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px; text-align: center;">S.No</th>
                                            <th>Product Sector</th>
                                            <th>Category</th>
                                            <th>Test Name</th>
                                            <th style="text-align: center; width: 90px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($mappings)): ?>
                                            <?php $msno = 1; foreach($mappings as $m): ?>
                                            <tr class="mapping-table-row" data-product="<?php echo htmlspecialchars(strtolower($m['product_name'])); ?>" data-test="<?php echo htmlspecialchars(strtolower($m['test_name'])); ?>">
                                                <td style="text-align: center; font-weight: 600; color: #64748b;" class="mapping-sno"><?php echo $msno++; ?></td>
                                                <td><strong style="color: #0f172a;"><?php echo htmlspecialchars($m['product_name']); ?></strong></td>
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
                                            <tr><td colspan="5" class="empty-state">No product-test mappings found.</td></tr>
                                        <?php endif; ?>
                                        <tr id="noMappingResultsRow" style="display: none;"><td colspan="5" class="empty-state">No mappings match your filter criteria.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAB 4: DISCIPLINES / CATEGORIES            -->
                <!-- ========================================== -->
                <div class="tab-panel" id="tab-categories">
                    <div class="section-split">
                        <!-- Top: Add Category Form -->
                        <div class="admin-form-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <h3 style="margin: 0;"><i class="fa-solid fa-tags" style="color: #7c3aed;"></i> Add Testing Category</h3>
                                <span style="font-size: 12.5px; color: #64748b;">Creates a laboratory discipline (e.g. Chemical, Mechanical, Biological, Electrical)</span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="add_discipline" value="1">
                                <div class="admin-form-row">
                                    <div class="admin-form-group" style="flex: 2;">
                                        <label>Category / Discipline Name <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="discipline_name" class="admin-form-control" required placeholder="e.g. Environmental Testing">
                                    </div>
                                    <div class="admin-form-group" style="flex: 1; margin-bottom: 0;">
                                        <button type="submit" class="btn-submit" style="background: #7c3aed; height: 42px;">
                                            <i class="fa-solid fa-plus"></i> Create Category
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right: Categories Table -->
                        <div class="card table-card" style="margin-top: 0;">
                            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                                <h3><i class="fa-solid fa-tags" style="color: #7c3aed; margin-right: 8px;"></i> Testing Categories (<?php echo count($disciplines); ?>)</h3>
                                <span class="badge-sub">Core Laboratory Disciplines</span>
                            </div>
                            <div class="table-responsive table-scroll-container">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">S.No</th>
                                            <th style="width: 60px;">ID</th>
                                            <th>Category Name</th>
                                            <th>Slug</th>
                                            <th>Tests Count</th>
                                            <th style="text-align: center; width: 90px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($disciplines)): ?>
                                            <?php $dsno = 1; foreach ($disciplines as $d): ?>
                                            <tr>
                                                <td style="text-align: center; font-weight: 600; color: #64748b;"><?php echo $dsno++; ?></td>
                                                <td><span style="color:#94a3b8; font-weight:600;">#<?php echo $d['id']; ?></span></td>
                                                <td><strong><?php echo htmlspecialchars($d['name']); ?></strong></td>
                                                <td><code><?php echo htmlspecialchars($d['slug']); ?></code></td>
                                                <td><span class="badge-type"><?php echo $d['test_count']; ?> tests</span></td>
                                                <td style="text-align: center;">
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete category: <?php echo addslashes(htmlspecialchars($d['name'])); ?>? All tests inside it will also be deleted.');">
                                                        <input type="hidden" name="delete_discipline" value="1">
                                                        <input type="hidden" name="discipline_id" value="<?php echo $d['id']; ?>">
                                                        <button type="submit" class="btn-table-del" title="Delete Category">
                                                            <i class="fa-solid fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="empty-state">No categories configured.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
// Tab Switching
function switchCatalogTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
    document.querySelectorAll('.catalog-tab-btn').forEach(btn => btn.classList.remove('active'));
    
    const targetPanel = document.getElementById(tabId);
    if (targetPanel) targetPanel.classList.add('active');

    const targetBtn = document.getElementById('btn-' + tabId);
    if (targetBtn) targetBtn.classList.add('active');

    // Save in sessionStorage so page reload stays on the same tab
    sessionStorage.setItem('active_catalog_tab', tabId);
}

// Restore saved tab on reload
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = sessionStorage.getItem('active_catalog_tab');
    if (savedTab && document.getElementById(savedTab)) {
        switchCatalogTab(savedTab);
    }
});

// Filter Tests Table
function filterTestsTable() {
    const searchInput = document.getElementById('testSearchInput').value.toLowerCase().trim();
    const categoryFilter = document.getElementById('testCategoryFilter').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.test-table-row');
    const noResultsRow = document.getElementById('noResultsRow');
    const countBadge = document.getElementById('tests-count-badge');
    
    let visibleCount = 0;
    let currentSno = 1;

    rows.forEach(row => {
        const cat = row.getAttribute('data-category') || '';
        const name = row.getAttribute('data-name') || '';
        const std = row.getAttribute('data-standard') || '';

        const matchesCat = !categoryFilter || cat.includes(categoryFilter);
        const matchesSearch = !searchInput || name.includes(searchInput) || std.includes(searchInput);

        if (matchesCat && matchesSearch) {
            row.style.display = '';
            const snoEl = row.querySelector('.test-sno');
            if (snoEl) snoEl.textContent = currentSno++;
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    if (noResultsRow) {
        noResultsRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }

    if (countBadge) {
        if (searchInput || categoryFilter) {
            countBadge.textContent = `${visibleCount} of ${rows.length} Tests`;
        } else {
            countBadge.textContent = `${rows.length} Total Tests`;
        }
    }
}

function resetTestsFilter() {
    document.getElementById('testSearchInput').value = '';
    document.getElementById('testCategoryFilter').value = '';
    filterTestsTable();
}

// Filter Mappings Table
function filterMappingsTable() {
    const productFilter = document.getElementById('mappingProductFilter').value.toLowerCase().trim();
    const searchInput = document.getElementById('mappingSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.mapping-table-row');
    const noResultsRow = document.getElementById('noMappingResultsRow');
    const countBadge = document.getElementById('mappings-count-badge');

    let visibleCount = 0;
    let currentSno = 1;

    rows.forEach(row => {
        const prod = row.getAttribute('data-product') || '';
        const test = row.getAttribute('data-test') || '';

        const matchesProd = !productFilter || prod.includes(productFilter);
        const matchesSearch = !searchInput || test.includes(searchInput);

        if (matchesProd && matchesSearch) {
            row.style.display = '';
            const snoEl = row.querySelector('.mapping-sno');
            if (snoEl) snoEl.textContent = currentSno++;
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    if (noResultsRow) {
        noResultsRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }

    if (countBadge) {
        if (productFilter || searchInput) {
            countBadge.textContent = `${visibleCount} of ${rows.length} Mappings`;
        } else {
            countBadge.textContent = `${rows.length} Total Mappings`;
        }
    }
}
</script>

</body>
</html>
