<?php
// includes/admin_notifications_sync.php

$sync_db_notif = [];
if (isset($_SESSION['user_id'])) {
    try {
        $stmtDbNotify = $conn->prepare("SELECT * FROM user_notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 50");
        $stmtDbNotify->execute(['uid' => $_SESSION['user_id']]);
        $sync_db_notif = $stmtDbNotify->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($sync_db_notif as &$n) {
            $stmtB = $conn->prepare("SELECT * FROM test_bookings WHERE id = :bid");
            $stmtB->execute(['bid' => $n['booking_id']]);
            $b = $stmtB->fetch(PDO::FETCH_ASSOC);
            if ($b) {
                $stmtF = $conn->prepare("SELECT * FROM booking_files WHERE booking_id = :bid");
                $stmtF->execute(['bid' => $b['id']]);
                $b['attached_files'] = $stmtF->fetchAll(PDO::FETCH_ASSOC);
            }
            $n['booking_data'] = $b;
        }
        unset($n);
    } catch (PDOException $e) {}
}

$sync_time_alerts = [];
try {
    $stmtNotify = $conn->query("SELECT * FROM test_bookings WHERE status = 'Accept'");
    $accept_bookings = $stmtNotify->fetchAll(PDO::FETCH_ASSOC);
    
    $now = time();
    foreach ($accept_bookings as $b_notify) {
        if (empty($b_notify['estimated_time'])) continue;
        
        $est_time = strtotime($b_notify['estimated_time']);
        $diff_sec = $est_time - $now;
        $diff_hours = $diff_sec / 3600.0;
        
        if ($diff_hours <= 1.0) {
            $alert_type = $diff_sec < 0 ? 'past_due' : 'due_soon';
            $sync_time_alerts[] = [
                'id' => $b_notify['id'],
                'booking_data' => $b_notify,
                'diff_sec' => $diff_sec,
                'type' => $alert_type,
                'timestamp' => strtotime($b_notify['accepted_at']) // Use accepted_at for sorting
            ];
        }
    }
} catch (PDOException $e) {}
?>
<script>
const dbNotificationsData = <?php echo json_encode($sync_time_alerts); ?>;
const dbStatusNotificationsData = <?php echo json_encode($sync_db_notif); ?>;

function renderGlobalNotifications() {
    const badge = document.getElementById('notificationBadge');
    const dropdownList = document.getElementById('notificationList');
    const pageList = document.getElementById('pageNotificationList'); // Only on notifications page
    
    let dismissed = JSON.parse(localStorage.getItem('dismissed_notifications') || '[]');
    
    // Format arrays and assign sortable timestamps
    let timeAlerts = dbNotificationsData.map(n => {
        return {
            ...n,
            isTimeAlert: true,
            isRead: dismissed.includes(n.id),
            sortTime: n.timestamp * 1000 // Convert to MS
        };
    });
    
    let statusAlerts = dbStatusNotificationsData.map(n => {
        return {
            ...n,
            isTimeAlert: false,
            isRead: n.is_read == 1,
            sortTime: new Date(n.created_at.replace(' ', 'T')).getTime()
        };
    });
    
    // Filter active alerts for unread counts
    const activeTimeAlerts = timeAlerts.filter(a => !a.isRead);
    const activeStatusAlerts = statusAlerts.filter(a => !a.isRead);
    
    const totalUnreadCount = activeTimeAlerts.length + activeStatusAlerts.length;
    
    // 1. UPDATE BADGES
    if (badge) {
        if (totalUnreadCount > 0) {
            badge.innerText = totalUnreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    
    const sidebarBadge = document.getElementById('sidebarNotificationBadge');
    if (sidebarBadge) {
        if (totalUnreadCount > 0) {
            sidebarBadge.innerText = totalUnreadCount;
            sidebarBadge.style.display = 'inline-block';
        } else {
            sidebarBadge.style.display = 'none';
        }
    }
    
    // Combine and sort ALL alerts (newest first)
    const allAlerts = [...timeAlerts, ...statusAlerts].sort((a, b) => b.sortTime - a.sortTime);
    const unreadAlerts = allAlerts.filter(a => !a.isRead);
    
    // 2. POPULATE TOPBAR DROPDOWN (Unread Only)
    if (dropdownList) {
        if (unreadAlerts.length === 0) {
            dropdownList.innerHTML = '<div class="notification-empty">No active alerts</div>';
        } else {
            dropdownList.innerHTML = '';
            unreadAlerts.forEach(n => {
                const item = document.createElement('a');
                item.className = 'notification-item unread';
                item.href = 'javascript:void(0);';
                
                if (n.isTimeAlert) {
                    let timeText = '';
                    if (n.diff_sec < 0) {
                        timeText = '<span style="color: #ef4444; font-weight: bold;">Overdue</span>';
                    } else {
                        const mins = Math.floor(n.diff_sec / 60);
                        const hours = Math.floor(mins / 60);
                        const remMins = mins % 60;
                        const timeStr = hours > 0 ? `${hours}h ${remMins}m` : `${remMins}m`;
                        timeText = `<span style="color: #f59e0b; font-weight: bold;">Due in ${timeStr}</span>`;
                    }
                    
                    item.innerHTML = `
                        <div class="notification-item-text">
                            <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b; margin-right: 5px;"></i>
                            <strong>Booking #${n.id}</strong> is ${n.type === 'past_due' ? 'overdue!' : 'due soon.'}
                        </div>
                        <div class="notification-item-time">
                            Client: ${n.booking_data.fullname} &bull; ${timeText}
                        </div>
                    `;
                    
                    item.onclick = function() {
                        let dismissedLocal = JSON.parse(localStorage.getItem('dismissed_notifications') || '[]');
                        if (!dismissedLocal.includes(n.id)) {
                            dismissedLocal.push(n.id);
                            localStorage.setItem('dismissed_notifications', JSON.stringify(dismissedLocal));
                            renderGlobalNotifications();
                        }
                        if (typeof openViewModal === 'function') {
                            openViewModal(n.booking_data);
                            document.getElementById('notificationDropdown').classList.remove('show');
                        } else {
                            window.location.href = 'admin_notifications.php';
                        }
                    };
                } else {
                    const created = new Date(n.created_at.replace(' ', 'T'));
                    const timeStr = created.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
                    
                    item.innerHTML = `
                        <div class="notification-item-text">
                            <i class="fa-solid fa-circle-info" style="color: #3b82f6; margin-right: 5px;"></i>
                            <strong>Status Update</strong>: ${n.message}
                        </div>
                        <div class="notification-item-time">
                            ${timeStr}
                        </div>
                    `;
                    
                    item.onclick = function() {
                        const formData = new FormData();
                        formData.append('action', 'read_one');
                        formData.append('id', n.id);
                        
                        fetch('mark_notifications_read.php', { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                const orig = dbStatusNotificationsData.find(db_n => db_n.id === n.id);
                                if (orig) orig.is_read = 1;
                                renderGlobalNotifications(); // Re-render
                            }
                        });
                        
                        if (typeof openViewModal === 'function' && n.booking_data) {
                            openViewModal(n.booking_data);
                            document.getElementById('notificationDropdown').classList.remove('show');
                        } else {
                            window.location.href = 'admin_notifications.php';
                        }
                    };
                }
                dropdownList.appendChild(item);
            });
        }
    }
    
    // 3. POPULATE FULL PAGE LIST (All alerts, read and unread)
    if (pageList) {
        if (allAlerts.length === 0) {
            pageList.innerHTML = `
                <div class="notifications-empty-state">
                    <i class="fa-solid fa-bell-slash"></i>
                    <h3>No Notification History</h3>
                    <p>You don't have any notifications yet.</p>
                </div>`;
        } else {
            pageList.innerHTML = '';
            allAlerts.forEach(n => {
                const item = document.createElement('div');
                item.className = 'notification-card-item' + (!n.isRead ? ' unread' : '');
                
                if (n.isTimeAlert) {
                    let timeText = '';
                    if (n.diff_sec < 0) {
                        timeText = '<span style="color: #ef4444; font-weight: bold;">Overdue</span>';
                    } else {
                        const mins = Math.floor(n.diff_sec / 60);
                        const hours = Math.floor(mins / 60);
                        const remMins = mins % 60;
                        const timeStr = hours > 0 ? `${hours}h ${remMins}m` : `${remMins}m`;
                        timeText = `<span style="color: #f59e0b; font-weight: bold;">Due in ${timeStr}</span>`;
                    }
                    
                    item.innerHTML = `
                        <div class="notification-card-content">
                            <div class="notification-card-icon ${n.isRead ? 'read' : 'unread'}">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="notification-card-text">
                                <div class="notification-card-title">Time Alert: Booking #${n.id}</div>
                                <div class="notification-card-msg">The booking for client <strong>${n.booking_data.fullname}</strong> (${n.booking_data.sample_type}) is approaching its estimated completion threshold.</div>
                                <div class="notification-card-time"><i class="fa-regular fa-clock"></i> ${timeText}</div>
                            </div>
                        </div>
                        <div class="notification-card-meta">
                            ${n.isRead ? '' : '<span class="new-badge">Active</span>'}
                            <button class="view-btn-notif"><i class="fa-solid fa-eye"></i> View Booking</button>
                        </div>
                    `;
                    item.onclick = function() {
                        if (!n.isRead) {
                            let dismissedLocal = JSON.parse(localStorage.getItem('dismissed_notifications') || '[]');
                            if (!dismissedLocal.includes(n.id)) {
                                dismissedLocal.push(n.id);
                                localStorage.setItem('dismissed_notifications', JSON.stringify(dismissedLocal));
                                renderGlobalNotifications();
                            }
                        }
                        if (typeof openViewModal === 'function') openViewModal(n.booking_data);
                    };
                } else {
                    const created = new Date(n.created_at.replace(' ', 'T'));
                    const timeStr = created.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
                    
                    item.innerHTML = `
                        <div class="notification-card-content">
                            <div class="notification-card-icon ${n.isRead ? 'read' : 'unread'}" style="background: ${n.isRead ? '#f1f5f9' : '#eff6ff'}; color: ${n.isRead ? '#94a3b8' : '#3b82f6'};">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <div class="notification-card-text">
                                <div class="notification-card-title">Status Update: Booking #${n.booking_id}</div>
                                <div class="notification-card-msg">${n.message}</div>
                                <div class="notification-card-time"><i class="fa-regular fa-clock"></i> ${timeStr}</div>
                            </div>
                        </div>
                        <div class="notification-card-meta">
                            ${n.isRead ? '' : '<span class="new-badge">New</span>'}
                            <button class="view-btn-notif"><i class="fa-solid fa-eye"></i> View Details</button>
                        </div>
                    `;
                    item.onclick = function() {
                        if (!n.isRead) {
                            const formData = new FormData();
                            formData.append('action', 'read_one');
                            formData.append('id', n.id);
                            fetch('mark_notifications_read.php', { method: 'POST', body: formData })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    const orig = dbStatusNotificationsData.find(db_n => db_n.id === n.id);
                                    if (orig) orig.is_read = 1;
                                    renderGlobalNotifications();
                                }
                            });
                        }
                        if (typeof openViewModal === 'function' && n.booking_data) {
                            openViewModal(n.booking_data);
                        }
                    };
                }
                pageList.appendChild(item);
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    renderGlobalNotifications();
});
</script>
