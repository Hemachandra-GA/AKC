<header class="admin-topbar">
    <div class="topbar-left" style="display: flex; align-items: center;">
        <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fa-solid fa-bars"></i></button>
        <h2>Overview / <span class="blue-text"><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard'; ?></span></h2>
    </div>
    <div class="topbar-right" style="display: flex; align-items: center; gap: 15px;">
        <div class="notification-container" id="notificationContainer">
            <button class="notification-bell" id="notificationBellBtn" title="Booking Alerts">
                <i class="fa-solid fa-bell"></i>
                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
            </button>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h4>Booking Alerts</h4>
                    <button id="clearNotificationsBtn" class="mark-read-btn">Clear All</button>
                </div>
                <div class="notification-list" id="notificationList">
                    <!-- Populated via Javascript -->
                    <div class="notification-empty">No active alerts</div>
                </div>
                <div style="padding: 10px; text-align: center; border-top: 1px solid #e2e8f0;">
                    <a href="admin_notifications.php" style="color: #3b82f6; font-size: 13px; font-weight: 600; text-decoration: none;">View All Notifications</a>
                </div>
            </div>
        </div>
        
        <div class="user-profile">
            <div class="topbar-avatar">
                <?php echo strtoupper(substr($_SESSION['fullname'] ?? $_SESSION['username'], 0, 2)); ?>
            </div>
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']); ?></span>
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
<?php include_once 'includes/admin_notifications_sync.php'; ?>

<script>
// Global Bell Dropdown toggle for all pages
document.addEventListener('DOMContentLoaded', function() {
    const bellBtn = document.getElementById('notificationBellBtn');
    if (bellBtn) {
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('notificationDropdown').classList.toggle('show');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notificationDropdown');
        const container = document.getElementById('notificationContainer');
        if (dropdown && container && !container.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
});
</script>
