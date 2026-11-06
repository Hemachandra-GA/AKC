<?php
$display_fullname = $fullname ?? ($user['fullname'] ?? ($_SESSION['fullname'] ?? 'Client'));
?>
<header class="admin-topbar">
    <div class="topbar-left" style="display: flex; align-items: center;">
        <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fa-solid fa-bars"></i></button>
        <h2><?php echo isset($page_title) ? $page_title : 'Overview / <span class="blue-text">My Dashboard</span>'; ?></h2>
    </div>
    <div class="topbar-right" style="display: flex; align-items: center; gap: 15px;">
        <!-- Notification Bell Container -->
        <div class="notification-container" id="notificationContainer">
            <button class="notification-bell" id="notificationBellBtn" title="Alerts">
                <i class="fa-solid fa-bell"></i>
                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
            </button>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h4>Alerts</h4>
                    <button id="clearNotificationsBtn" class="mark-read-btn">Clear All</button>
                </div>
                <div class="notification-list" id="notificationList">
                    <div class="notification-empty">No active notifications</div>
                </div>
            </div>
        </div>

        <div class="user-profile">
            <div class="topbar-avatar" style="background: linear-gradient(135deg, #10b981, #059669);">
                <?php echo strtoupper(substr($display_fullname, 0, 2)); ?>
            </div>
            <span class="user-name"><?php echo htmlspecialchars($display_fullname); ?> <span class="client-badge">CLIENT</span></span>
        </div>
    </div>
</header>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    // Mobile Sidebar Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (mobileMenuBtn && adminSidebar && sidebarOverlay) {
        function toggleMobileSidebar() {
            adminSidebar.classList.toggle('mobile-open');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = adminSidebar.classList.contains('mobile-open') ? 'hidden' : '';
        }

        mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
        sidebarOverlay.addEventListener('click', toggleMobileSidebar);
    }
</script>

<?php include 'includes/client_notifications_sync.php'; ?>
