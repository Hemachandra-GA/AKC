<?php
include '../includes/super_admin_auth_check.php';
include '../includes/db_connect.php';

// Fetch all active admins
$admins = [];
try {
    $stmt = $conn->prepare("SELECT id, username, fullname, email, permissions FROM users WHERE role = 'admin' AND is_active = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Admin Data</title>
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
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            width: 100%;
        }
        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn-view:hover {
            background-color: #2563eb;
            color: white;
        }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <?php 
    $current_page = 'view_admin_data';
    $page_title = 'View Admin Data';
    include '../includes/super_admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include '../includes/super_admin_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <div class="card table-card">
                <div class="card-header">
                    <h3>Admin Services Overview</h3>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No</th>
                                <th>Admin Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Assigned Service</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($admins) > 0): ?>
                                <?php 
                                $sno = 1;
                                foreach ($admins as $admin): 
                                    $admin_perms = [];
                                    if (!empty($admin['permissions'])) {
                                        $decoded = json_decode($admin['permissions'], true);
                                        if (is_array($decoded)) {
                                            $admin_perms = $decoded;
                                        }
                                    }
                                    
                                    $service_name = 'None';
                                    $dashboard_link = '../admin/testing_dashboard.php';
                                    
                                    if (!empty($admin_perms)) {
                                        $p = $admin_perms[0];
                                        if ($p == 'courses') {
                                            $service_name = 'Courses';
                                            $dashboard_link = '../admin/courses_dashboard.php';
                                        } elseif ($p == 'testings') {
                                            $service_name = 'Testings';
                                            $dashboard_link = '../admin/testing_dashboard.php';
                                        } elseif ($p == 'support_services') {
                                            $service_name = 'Support';
                                            $dashboard_link = '../admin/support_dashboard.php';
                                        } else {
                                            $service_name = ucfirst(str_replace('_', ' ', $p));
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($admin['fullname']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                        <td>
                                            <span style="font-size:13px; font-weight: 500; color: #475569; padding: 4px 10px; background-color: #f1f5f9; border-radius: 20px;">
                                                <?php echo $service_name; ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="<?php echo $dashboard_link; ?>" class="btn-view" title="View Dashboard">
                                                <i class="fa-solid fa-eye"></i> View Data
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-state">No active administrators found.</td>
                                </tr>
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
