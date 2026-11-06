<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="assets/images/logo.png" alt="AKC Logo" class="sidebar-logo">
        <h3>AKC Admin</h3>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-section-label"><i class="fa-solid fa-grid-2"></i> MAIN</li>
            <li class="<?php echo ($current_page == 'bookings') ? 'active' : ''; ?>"><a href="admin_dashboard.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
            <li class="<?php echo ($current_page == 'clients') ? 'active' : ''; ?>"><a href="admin_clients.php"><i class="fa-solid fa-users"></i> Clients</a></li>
            <li class="<?php echo ($current_page == 'catalog') ? 'active' : ''; ?>"><a href="admin_catalog.php"><i class="fa-solid fa-layer-group"></i> Catalog & Mappings</a></li>
            <li class="nav-section-label"><i class="fa-solid fa-chart-pie"></i> ANALYTICS</li>
            <li class="<?php echo ($current_page == 'reports') ? 'active' : ''; ?>"><a href="admin_reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
            <li class="<?php echo ($current_page == 'notifications') ? 'active' : ''; ?>"><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications <span class="sidebar-badge" id="sidebarNotificationBadge" <?php echo (isset($sidebar_unread_count) && $sidebar_unread_count > 0) ? '' : 'style="display: none;"'; ?>><?php echo isset($sidebar_unread_count) ? $sidebar_unread_count : '0'; ?></span></a></li>
            <li class="nav-section-label"><i class="fa-solid fa-sliders"></i> SYSTEM</li>
            <li class="<?php echo ($current_page == 'profile') ? 'active' : ''; ?>"><a href="profile_settings.php"><i class="fa-solid fa-gear"></i> Profile Settings</a></li>
            <li><a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-admin-profile">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($_SESSION['fullname'] ?? $_SESSION['username'], 0, 2)); ?>
            </div>
            <div class="admin-profile-info">
                <span class="admin-profile-name"><?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']); ?></span>
                <span class="admin-profile-role"><i class="fa-solid fa-shield-halved"></i> Super Admin</span>
            </div>
        </div>
    </div>
</aside>
