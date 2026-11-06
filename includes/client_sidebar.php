<?php
// includes/client_sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$display_fullname = $fullname ?? ($user['fullname'] ?? ($_SESSION['fullname'] ?? 'Client'));
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="assets/images/logo.png" alt="AKC Logo" class="sidebar-logo">
        <h3 class="sidebar-logo-text">AKC <span style="color: #60a5fa;">Client</span></h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-section-label"><i class="fa-solid fa-grid-2"></i> MAIN</li>
            <li class="<?php echo ($current_page == 'client_dashboard.php') ? 'active' : ''; ?>"><a href="client_dashboard.php"><i class="fa-solid fa-gauge"></i> My Bookings</a></li>
            <li class="<?php echo ($current_page == 'client_reports.php') ? 'active' : ''; ?>"><a href="client_reports.php"><i class="fa-solid fa-file-invoice"></i> My Reports</a></li>
            
            <li class="nav-section-label"><i class="fa-solid fa-chart-pie"></i> UPDATES</li>
            <li class="<?php echo ($current_page == 'client_notifications.php') ? 'active' : ''; ?>"><a href="client_notifications.php"><i class="fa-solid fa-bell"></i> Notifications <span class="sidebar-badge" id="sidebarNotificationBadge" style="display: none;">0</span></a></li>
            
            <li class="nav-section-label"><i class="fa-solid fa-sliders"></i> SYSTEM</li>
            <li class="<?php echo ($current_page == 'profile_settings.php') ? 'active' : ''; ?>"><a href="profile_settings.php"><i class="fa-solid fa-user-gear"></i> Profile Settings</a></li>
            <li><a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-admin-profile">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($display_fullname, 0, 2)); ?>
            </div>
            <div class="admin-profile-info">
                <span class="admin-profile-name"><?php echo htmlspecialchars($display_fullname); ?></span>
                <span class="admin-profile-role"><i class="fa-solid fa-user"></i> Client</span>
            </div>
        </div>
    </div>
</aside>
