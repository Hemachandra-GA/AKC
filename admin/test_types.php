<?php
include '../includes/auth_check.php';
include '../includes/db_connect.php';
require_once '../includes/activity_logger.php';

// Fetch unread notifications count for sidebar badge
$sidebar_unread_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmtUnread = $conn->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = :uid AND is_read = 0");
        $stmtUnread->execute(['uid' => $_SESSION['user_id']]);
        $sidebar_unread_count = (int)$stmtUnread->fetchColumn();
    } catch(PDOException $e) {}
}

$message = "";
$messageType = "";

// Handle form submission to add new test type
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_type') {
    $new_type_name = trim($_POST['test_type_name'] ?? '');
    
    if (!empty($new_type_name)) {
        try {
            // Check if name already exists (case-insensitive and trimmed)
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM test_types WHERE LOWER(TRIM(name)) = LOWER(TRIM(:name))");
            $stmtCheck->execute(['name' => $new_type_name]);
            if ($stmtCheck->fetchColumn() > 0) {
                $message = "This test type already exists.";
                $messageType = "error";
            } else {
                $stmtInsert = $conn->prepare("INSERT INTO test_types (name) VALUES (:name)");
                $stmtInsert->execute(['name' => $new_type_name]);
                $new_id = $conn->lastInsertId();
                logAdminActivity($conn, 'CREATE', 'TEST_TYPES', $new_id, null, ['name' => $new_type_name]);
                $message = "Test type added successfully.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "Error adding test type: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Test type name cannot be empty.";
        $messageType = "error";
    }
}

// Handle edit test type
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_type') {
    $edit_id = (int)$_POST['edit_id'];
    $new_name = trim($_POST['edit_test_type_name'] ?? '');
    
    if (!empty($new_name) && $edit_id > 0) {
        try {
            // Check if another test type with the new name already exists (case-insensitive and trimmed)
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM test_types WHERE LOWER(TRIM(name)) = LOWER(TRIM(:name)) AND id != :id");
            $stmtCheck->execute(['name' => $new_name, 'id' => $edit_id]);
            if ($stmtCheck->fetchColumn() > 0) {
                $message = "This test type already exists.";
                $messageType = "error";
            } else {
                $conn->beginTransaction();
                // Get old name
                $stmtOld = $conn->prepare("SELECT name FROM test_types WHERE id = :id");
                $stmtOld->execute(['id' => $edit_id]);
                $old_name = $stmtOld->fetchColumn();
                
                // Update test_types
                $stmtUpdate = $conn->prepare("UPDATE test_types SET name = :name WHERE id = :id");
                $stmtUpdate->execute(['name' => $new_name, 'id' => $edit_id]);
                
                // Update test_bookings
                if ($old_name && $old_name !== $new_name) {
                    $stmtUpdateBookings = $conn->prepare("UPDATE test_bookings SET sample_type = :new_name WHERE sample_type = :old_name");
                    $stmtUpdateBookings->execute(['new_name' => $new_name, 'old_name' => $old_name]);
                }
                $conn->commit();
                logAdminActivity($conn, 'UPDATE', 'TEST_TYPES', $edit_id, ['name' => $old_name], ['name' => $new_name]);
                $message = "Test type updated successfully.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $message = "Error updating test type: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Handle delete test type
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_type') {
    $delete_id = (int)$_POST['delete_id'];
    
    if ($delete_id > 0) {
        try {
            // Check if it has bookings
            $stmtCheck = $conn->prepare("SELECT name FROM test_types WHERE id = :id");
            $stmtCheck->execute(['id' => $delete_id]);
            $type_name = $stmtCheck->fetchColumn();
            
            if ($type_name) {
                $stmtCount = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE sample_type = :name");
                $stmtCount->execute(['name' => $type_name]);
                $count = $stmtCount->fetchColumn();
                
                if ($count > 0) {
                    $message = "Cannot delete test type because it has $count existing submission(s).";
                    $messageType = "error";
                } else {
                    $stmtDelete = $conn->prepare("DELETE FROM test_types WHERE id = :id");
                    $stmtDelete->execute(['id' => $delete_id]);
                    logAdminActivity($conn, 'DELETE', 'TEST_TYPES', $delete_id, ['name' => $type_name], null);
                    $message = "Test type deleted successfully.";
                    $messageType = "success";
                }
            }
        } catch (PDOException $e) {
            $message = "Error deleting test type: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Handle Filters
$search = $_GET['search'] ?? '';

// Pagination parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query for test types
$base_query = "FROM test_types tt LEFT JOIN test_bookings tb ON tt.name = tb.sample_type WHERE 1=1";
$params = [];

if (!empty($search)) {
    $base_query .= " AND tt.name LIKE :search";
    $params['search'] = "%$search%";
}

// Fetch total records for pagination
$count_query = "SELECT COUNT(DISTINCT tt.id) " . $base_query;
$stmtCount = $conn->prepare($count_query);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch summary stats
$totalTestTypes = $conn->query("SELECT COUNT(*) FROM test_types")->fetchColumn();
$totalBookings = $conn->query("SELECT COUNT(*) FROM test_bookings")->fetchColumn();

// Fetch filtered test types with pagination
$query = "SELECT tt.id, tt.name as sample_type, COUNT(tb.id) as total_bookings, MAX(tb.created_at) as last_booking " . $base_query . " GROUP BY tt.id, tt.name ORDER BY total_bookings DESC LIMIT :limit OFFSET :offset";
try {
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $test_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Types - AKC Admin</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .sidebar-badge { background-color: #ef4444; color: white; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 2px 6px; margin-left: auto; display: inline-block; }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            width: 100%;
        }
        .header-actions {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
        }
        .filter-form {
            width: 100%;
            max-width: 300px;
        }
        .search-box {
            width: 100% !important;
        }
        .search-box input {
            width: 100% !important;
            height: 38px;
        }
        .btn-export {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 0; border-radius: 12px; width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 15px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 25px; }
        .modal-footer { padding: 15px 25px; border-top: 1px solid #e2e8f0; text-align: right; }
        .close { font-size: 24px; cursor: pointer; color: #64748b; }
        .close:hover { color: #1e293b; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px; color: #475569; }
        .form-group input { width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
        .form-group input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    </style>
</head>
<body class="admin-body-bg">


<div class="admin-wrapper">
    <?php 
    $current_page = 'test_types';
    $page_title = 'Test Types';
    include '../includes/admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include '../includes/admin_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <?php if ($message): ?>
                <div class="alert <?php echo $messageType; ?>" style="padding: 15px; border-radius: 8px; margin-bottom: 20px; <?php echo $messageType == 'success' ? 'background-color: #dcfce7; color: #166534;' : 'background-color: #fee2e2; color: #991b1b;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Stats Row -->
            <div class="stats-row" style="grid-template-columns: repeat(2, 1fr);">
                <div class="stat-card border-blue">
                    <div class="stat-card-header">
                        <div class="stat-icon blue"><i class="fa-solid fa-flask-vial"></i></div>
                        <span class="stat-badge badge-neutral">Types</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalTestTypes; ?></span>
                        <span class="stat-label">Unique Test Types</span>
                    </div>
                </div>
                <div class="stat-card border-green">
                    <div class="stat-card-header">
                        <div class="stat-icon green"><i class="fa-solid fa-clipboard-list"></i></div>
                        <span class="stat-badge badge-neutral">Volume</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalBookings; ?></span>
                        <span class="stat-label">Total Submissions</span>
                    </div>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="card table-card">
                <div class="card-header">
                    <h3>Test Types Directory</h3>
                    <div class="header-actions">
                        <?php if ($_SESSION['role'] !== 'super_admin'): ?>
                            <button onclick="openModal()" class="btn-export" style="background-color: #3b82f6; border: none; color: white; margin-right: 15px; cursor: pointer;">
                                <i class="fa-solid fa-plus"></i> Add New Test Type
                            </button>
                        <?php endif; ?>
                        <form method="GET" class="filter-form">
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="search" placeholder="Search test type..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No</th>
                                <th>Test Type Name</th>
                                <th>Total Submissions</th>
                                <th>Last Submission Date</th>
                                <?php if ($_SESSION['role'] !== 'super_admin'): ?>
                                    <th style="text-align: right;">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($test_types) > 0): ?>
                                <?php 
                                $sno = $offset + 1;
                                foreach ($test_types as $type): 
                                ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($type['sample_type']); ?></strong></td>
                                        <td><span class="badge-type"><?php echo $type['total_bookings']; ?> submission(s)</span></td>
                                        <td><?php echo $type['last_booking'] ? date('M d, Y', strtotime($type['last_booking'])) : 'N/A'; ?></td>
                                        <?php if ($_SESSION['role'] !== 'super_admin'): ?>
                                            <td style="text-align: right;">
                                                <button onclick="openEditModal(<?php echo $type['id']; ?>, '<?php echo htmlspecialchars(addslashes($type['sample_type'])); ?>')" class="btn-action edit" style="background: none; border: none; color: #3b82f6; cursor: pointer; margin-right: 10px;" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this test type?');">
                                                    <input type="hidden" name="action" value="delete_type">
                                                    <input type="hidden" name="delete_id" value="<?php echo $type['id']; ?>">
                                                    <button type="submit" class="btn-action delete" style="background: none; border: none; color: #ef4444; cursor: pointer;" title="Delete">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="empty-state">No test types found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 0): ?>
                <div class="pagination-container">
                    <div class="rows-per-page">
                        <form method="GET" action="" id="limitForm">
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <label>Rows per page:</label>
                            <select name="limit" onchange="document.getElementById('limitForm').submit()">
                                <option value="5" <?php if($limit == 5) echo 'selected'; ?>>5</option>
                                <option value="10" <?php if($limit == 10) echo 'selected'; ?>>10</option>
                                <option value="25" <?php if($limit == 25) echo 'selected'; ?>>25</option>
                                <option value="50" <?php if($limit == 50) echo 'selected'; ?>>50</option>
                            </select>
                        </form>
                    </div>
                    <div class="pagination-links">
                        <?php 
                        $queryString = "&search=".urlencode($search)."&limit=".$limit;
                        if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $queryString; ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $queryString; ?>" class="page-link <?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $queryString; ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Add Test Type Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Test Type</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" action="test_types.php">
            <input type="hidden" name="action" value="add_type">
            <div class="modal-body">
                <div class="form-group">
                    <label for="test_type_name">Test Type Name</label>
                    <input type="text" id="test_type_name" name="test_type_name" required placeholder="e.g. Chemical Analysis">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()" style="padding: 8px 16px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; margin-right: 10px;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 8px 16px; border: none; background: #3b82f6; color: white; border-radius: 6px; cursor: pointer;">Add Test Type</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Test Type Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Test Type</h3>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="POST" action="test_types.php">
            <input type="hidden" name="action" value="edit_type">
            <input type="hidden" name="edit_id" id="edit_id" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_test_type_name">Test Type Name</label>
                    <input type="text" id="edit_test_type_name" name="edit_test_type_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()" style="padding: 8px 16px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; margin-right: 10px;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 8px 16px; border: none; background: #3b82f6; color: white; border-radius: 6px; cursor: pointer;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('addModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openEditModal(id, name) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_test_type_name').value = name;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    if (event.target == addModal) {
        closeModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        if (searchInput.value) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }

        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchInput.closest('form').submit();
            }, 500);
        });
    }
});
</script>

</body>
</html>
