<?php
include '../includes/auth_check.php';

// Check permission
if (!isset($_SESSION['permissions']) || !in_array('courses', $_SESSION['permissions'])) {
    header("Location: dashboard.php");
    exit();
}

include '../includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - AKC Admin</title>
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
    $current_page = 'courses_dashboard';
    $page_title = 'Courses';
    include '../includes/admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include '../includes/admin_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <div class="card mt-4">
                <div class="card-body" style="padding: 30px; text-align: center;">
                    <h3>Courses Management</h3>
                    <p style="color: #64748b; margin-top: 10px;">This module is under construction.</p>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
