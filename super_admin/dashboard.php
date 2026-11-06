<?php
include '../includes/super_admin_auth_check.php';
include '../includes/db_connect.php';

// Fetch summary stats
$totalAdmins = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$activeAdmins = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1")->fetchColumn();
$inactiveAdmins = $totalAdmins - $activeAdmins;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - AKC</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <?php 
    $current_page = 'dashboard';
    $page_title = 'Super Admin Dashboard';
    include '../includes/super_admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include '../includes/super_admin_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <h2 style="margin-bottom: 20px;">Overview</h2>
            <!-- Stats Row -->
            <div class="stats-row" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card border-blue">
                    <div class="stat-card-header">
                        <div class="stat-icon blue"><i class="fa-solid fa-user-shield"></i></div>
                        <span class="stat-badge badge-neutral">Total</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalAdmins; ?></span>
                        <span class="stat-label">Total Admins</span>
                    </div>
                </div>
                <div class="stat-card border-green">
                    <div class="stat-card-header">
                        <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
                        <span class="stat-badge badge-success">Active</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $activeAdmins; ?></span>
                        <span class="stat-label">Active Admins</span>
                    </div>
                </div>
                <div class="stat-card border-red">
                    <div class="stat-card-header">
                        <div class="stat-icon red"><i class="fa-solid fa-ban"></i></div>
                        <span class="stat-badge badge-danger">Inactive</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $inactiveAdmins; ?></span>
                        <span class="stat-label">Inactive Admins</span>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body" style="padding: 30px; text-align: center;">
                    <h3>Welcome to the Super Admin Area</h3>
                    <p style="color: #64748b; margin-top: 10px;">Here you can manage the platform administrators.</p>
                    <a href="admins.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">Manage Admins</a>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
