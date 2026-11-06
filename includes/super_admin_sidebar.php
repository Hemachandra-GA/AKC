<?php
$sidebar_unread_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmtUnread = $conn->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = :uid AND is_read = 0");
        $stmtUnread->execute(['uid' => $_SESSION['user_id']]);
        $sidebar_unread_count = (int)$stmtUnread->fetchColumn();
    } catch(PDOException $e) {}
}
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo.png" alt="AKC Logo" class="sidebar-logo">
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-section-label"><i class="fa-solid fa-grid-2"></i> MAIN</li>
            <li class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>"><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li class="<?php echo ($current_page == 'monthly_reports') ? 'active' : ''; ?>"><a href="monthly_reports.php"><i class="fa-solid fa-file-invoice"></i> Monthly Operations Reports</a></li>
            <li class="<?php echo ($current_page == 'admins') ? 'active' : ''; ?>"><a href="admins.php"><i class="fa-solid fa-user-shield"></i> Admin Management</a></li>
            <li class="nav-section-label"><i class="fa-solid fa-eye"></i> SYSTEM VIEW</li>
            <li class="<?php echo ($current_page == 'view_admin_data') ? 'active' : ''; ?>"><a href="view_admin_data.php"><i class="fa-solid fa-arrow-up-right-from-square"></i> View All Admin Data</a></li>
            <li class="<?php echo ($current_page == 'activity_logs') ? 'active' : ''; ?>"><a href="activity_logs.php"><i class="fa-solid fa-clock-rotate-left"></i> Activity Logs</a></li>
            
            <li class="nav-section-label"><i class="fa-solid fa-sliders"></i> SYSTEM</li>
            <li class="<?php echo ($current_page == 'profile') ? 'active' : ''; ?>"><a href="profile_settings.php"><i class="fa-solid fa-user-gear"></i> Profile Settings</a></li>
            <li class="<?php echo ($current_page == 'notifications') ? 'active' : ''; ?>"><a href="../admin/notifications.php"><i class="fa-solid fa-bell"></i> Notifications <span class="sidebar-badge" id="sidebarNotificationBadge" <?php echo ($sidebar_unread_count > 0) ? '' : 'style="display: none;"'; ?>><?php echo $sidebar_unread_count; ?></span></a></li>
            <li><a href="../logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </nav>
</aside>
