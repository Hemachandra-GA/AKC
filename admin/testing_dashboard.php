<?php
include '../includes/auth_check.php';
include '../includes/db_connect.php';

// Handle Filters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Pagination parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build Query
$base_query = "FROM test_bookings WHERE 1=1";
$params = [];

if (!empty($search)) {
    $base_query .= " AND (fullname LIKE :search OR email LIKE :search OR phone LIKE :search OR sample_type LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($status_filter)) {
    $base_query .= " AND status = :status";
    $params['status'] = $status_filter;
}

if (!empty($date_from) && !empty($date_to)) {
    $base_query .= " AND DATE(created_at) BETWEEN :date_from AND :date_to";
    $params['date_from'] = $date_from;
    $params['date_to'] = $date_to;
}

// Fetch total records for pagination
$count_query = "SELECT COUNT(*) " . $base_query;
$stmtCount = $conn->prepare($count_query);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch summary stats (Overall totals, not filtered)
$totalBookings = $conn->query("SELECT COUNT(*) FROM test_bookings")->fetchColumn();
$completedBookings = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Complete'")->fetchColumn();
$pendingBookings = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Pending'")->fetchColumn();
$inProgressBookings = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Accept'")->fetchColumn();
$rejectedBookings = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Cancel'")->fetchColumn();
$overdueBookings = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Accept' AND estimated_time IS NOT NULL AND estimated_time < NOW()")->fetchColumn();
$completionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100) : 0;

// Today's counts for badges
$todayTotal = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$todayCompleted = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Complete' AND DATE(created_at) = CURDATE()")->fetchColumn();
$todayPending = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Pending' AND DATE(created_at) = CURDATE()")->fetchColumn();
$todayInProgress = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Accept' AND DATE(created_at) = CURDATE()")->fetchColumn();
$todayRejected = $conn->query("SELECT COUNT(*) FROM test_bookings WHERE status = 'Cancel' AND DATE(created_at) = CURDATE()")->fetchColumn();

// Fetch filtered bookings with pagination
$query = "SELECT * " . $base_query . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
try {
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Check estimated time notifications for In Progress bookings
$notifications_list = [];
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
            
            // Check if notification email was already sent
            if ($b_notify['est_notification_sent'] == 0) {
                // Fire async notification email
$_GET['booking_id'] = $b_notify['id'];
$_GET['alert_type'] = $alert_type;

include __DIR__ . '/../process_alert_email_async.php';
                
                // Mark notified in database
                $updateNotify = $conn->prepare("UPDATE test_bookings SET est_notification_sent = 1 WHERE id = :id");
                $updateNotify->execute(['id' => $b_notify['id']]);
                $b_notify['est_notification_sent'] = 1;
            }
            
            // Add to UI alerts list
            $notifications_list[] = [
                'id' => $b_notify['id'],
                'booking_data' => $b_notify,
                'diff_sec' => $diff_sec,
                'type' => $alert_type
            ];
        }
    }
} catch (PDOException $e) {
    error_log("Failed to check notifications: " . $e->getMessage());
}

// Fetch database-backed notifications for the admin
// Now handled globally in includes/admin_notifications_sync.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AKC</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flatpickr Date/Time Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Flatpickr Custom Styling */
        .flatpickr-input { cursor: pointer !important; }
        .flatpickr-calendar { border-radius: 12px !important; box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important; border: 1px solid #e2e8f0 !important; font-family: 'Montserrat', sans-serif !important; }
        .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #2563eb !important; border-color: #2563eb !important; }
        .flatpickr-day:hover { background: #eff6ff !important; }
        .flatpickr-time input:hover, .flatpickr-time input:focus { background: #eff6ff !important; }
        .flatpickr-confirm { background: #2563eb; color: white; border: none; border-radius: 8px; padding: 8px 20px; cursor: pointer; font-weight: 600; font-family: 'Montserrat', sans-serif; font-size: 13px; width: calc(100% - 20px); margin: 8px 10px 10px; display: block; transition: background 0.2s; }
        .flatpickr-confirm:hover { background: #1d4ed8; }
        .flatpickr-months .flatpickr-month { background: #2563eb !important; }
        .flatpickr-current-month .flatpickr-monthDropdown-months, .flatpickr-current-month input.cur-year { color: white !important; }
        .numInputWrapper span { border-color: rgba(255,255,255,0.3) !important; }
        .flatpickr-months .flatpickr-prev-month svg, .flatpickr-months .flatpickr-next-month svg { fill: white !important; }
        .flatpickr-weekday { background: #2563eb !important; color: rgba(255,255,255,0.8) !important; }
        .flatpickr-weekdays { background: #2563eb !important; }
        .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover {
            cursor: not-allowed;
            color: rgb(68 63 63 / 50%);
        }
        /* File Upload Area */
        .upload-zone { border: 2px dashed #cbd5e1; border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #f8fafc; }
        .upload-zone:hover, .upload-zone.dragover { border-color: #3b82f6; background: #eff6ff; }
        .upload-zone i { font-size: 28px; color: #94a3b8; margin-bottom: 8px; display: block; }
        .upload-zone p { font-size: 13px; color: #64748b; margin: 0; }
        .upload-zone span { font-size: 11px; color: #94a3b8; display: block; margin-top: 4px; }
        .new-files-preview { margin-top: 10px; display: flex; flex-direction: column; gap: 6px; }
        .preview-file-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 8px; font-size: 13px; }
        .preview-file-info { display: flex; align-items: center; gap: 8px; color: #1e40af; overflow: hidden; }
        .preview-file-info i { flex-shrink: 0; font-size: 16px; }
        .preview-file-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; font-weight: 500; }
        .preview-file-size { color: #64748b; font-size: 11px; flex-shrink: 0; }
        .btn-remove-preview { background: none; border: none; cursor: pointer; color: #94a3b8; padding: 2px 4px; border-radius: 4px; transition: all 0.2s; font-size: 14px; }
        .btn-remove-preview:hover { color: #ef4444; background: #fee2e2; }
        .hidden { display: none !important; }
        .close { font-size: 28px; font-weight: bold; cursor: pointer; color: #64748b; transition: color 0.2s; }
        .close:hover { color: #1e293b; }
        .view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .view-section { display: flex; flex-direction: column; }
        .view-section h4 { font-size: 13px; text-transform: uppercase; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 10px; font-weight: 700; letter-spacing: 0.5px; }
        .view-section h4 i { color: #3b82f6; width: 20px; text-align: center; }
        .view-row { display: flex; margin-bottom: 12px; font-size: 14px; align-items: flex-start; }
        .view-label { width: 130px; color: #64748b; font-weight: 600; flex-shrink: 0; }
        .view-value { color: #1e293b; font-weight: 500; }
        .full-width { grid-column: span 2; }
        .view-full-img { width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px; margin-top: 10px; border: 1px solid #e2e8f0; background: #f8fafc; }
        .view-file-item { margin-bottom: 10px; }
        .view-file-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; transition: all 0.2s; font-size: 14px; }
        .view-file-link:hover { border-color: #3b82f6; background: #f0f7ff; color: #3b82f6; }
        
        /* Edit Modal File List */
        .edit-file-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; }
        .edit-file-info { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #1e293b; overflow: hidden; }
        .edit-file-info i { color: #64748b; }
        .edit-file-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .btn-delete-file { color: #ef4444; background: none; border: none; cursor: pointer; padding: 5px; border-radius: 4px; transition: all 0.2s; }
        .btn-delete-file:hover { background: #fee2e2; transform: scale(1.1); }

        /* Toast Notification */
        #toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: #1e293b; color: white; padding: 15px 30px; border-radius: 50px; font-weight: 600; font-size: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 9999; display: none; align-items: center; gap: 12px; animation: fadeInUp 0.4s ease; }
        @keyframes fadeInUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }
        .toast-icon { width: 24px; height: 24px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; }

        /* Premium Full-Width Filter Layout Overrides */
        .card-header-filters {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            width: 100%;
        }
        #filterForm {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1.6fr;
            gap: 15px;
            align-items: center;
            flex: 1;
            width: 100%;
        }
        .search-box {
            width: 100% !important;
        }
        .search-box input {
            width: 100% !important;
            height: 38px;
        }
        .status-action-group {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-action-group select {
            flex: 1;
            width: 100%;
            height: 38px;
        }
        .btn-refresh-filter {
            height: 38px;
            width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s;
        }
        .btn-refresh-filter:hover {
            background: #eff6ff;
            color: #3b82f6;
            border-color: #3b82f6;
        }
        .btn-clear-filter {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 0 12px;
            height: 38px;
            background: #fee2e2;
            color: #ef4444;
            border: 1px solid #fecaca;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .btn-clear-filter:hover { background: #fecaca; color: #dc2626; }
        .date-filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            flex-wrap: nowrap;
        }
        .date-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            flex: 1;
            width: 100%;
        }
        .date-input-wrap i {
            position: absolute;
            left: 10px;
            color: #94a3b8;
            font-size: 12px;
            pointer-events: none;
        }
        .filter-date {
            padding: 8px 10px 8px 30px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'Montserrat', sans-serif;
            color: #1e293b;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.2s;
            width: 100% !important;
            height: 38px;
            min-width: 0 !important;
        }
        .filter-date:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }
        .date-sep { font-size: 12px; color: #94a3b8; font-weight: 600; padding: 0 2px; }
        .btn-export {
            align-self: center;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Notification System */
        .notification-container { position: relative; margin-right: 15px; }
        .notification-bell { background: none; border: none; cursor: pointer; position: relative; font-size: 20px; color: #64748b; padding: 8px; border-radius: 50%; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
        .notification-bell:hover { background: #f1f5f9; color: #3b82f6; }
        .notification-badge { position: absolute; top: 2px; right: 2px; background: #ef4444; color: white; font-size: 10px; font-weight: 700; border-radius: 50%; min-width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid white; }
        .notification-dropdown { position: absolute; top: 100%; right: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; z-index: 1000; margin-top: 10px; display: none; overflow: hidden; animation: fadeIn 0.2s ease; }
        .notification-dropdown.show { display: block; }
        .notification-header { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; background: #f8fafc; }
        .notification-header h4 { margin: 0; font-size: 14px; font-weight: 700; color: #1e293b; }
        .mark-read-btn { background: none; border: none; font-size: 11px; color: #3b82f6; cursor: pointer; font-weight: 600; padding: 2px 5px; }
        .mark-read-btn:hover { text-decoration: underline; }
        .notification-list { max-height: 250px; overflow-y: auto; }
        .notification-item { padding: 12px 15px; border-bottom: 1px solid #f8fafc; display: flex; flex-direction: column; gap: 4px; transition: background 0.2s; cursor: pointer; text-decoration: none; color: inherit; text-align: left; }
        .notification-item:hover { background: #f0f7ff; }
        .notification-item.unread { background: #eff6ff; }
        .notification-item-text { font-size: 13px; color: #1e293b; font-weight: 500; line-height: 1.4; }
        .notification-item-time { font-size: 11px; color: #94a3b8; font-weight: 500; }
        .notification-empty { padding: 30px 15px; text-align: center; color: #64748b; font-size: 13px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        /* Timer Styles */
        .estimated-timer { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #ef4444; font-weight: 700; background: #fff1f2; padding: 3px 8px; border-radius: 4px; border: 1px solid #fecdd3; width: fit-content; margin-top: 4px; }
        .estimated-timer i { color: #ef4444; font-size: 10px; }
    </style>
</head>
<body class="admin-body-bg">

<div id="toast">
    <div class="toast-icon"><i class="fa-solid fa-check"></i></div>
    <span id="toast-message">File removed successfully</span>
</div>

<div class="admin-wrapper">
    <?php 
    $current_page = 'testing_dashboard';
    $page_title = 'Bookings';
    include '../includes/admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include '../includes/admin_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card border-blue">
                    <div class="stat-card-header">
                        <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                        <span class="stat-badge badge-neutral">All</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalBookings; ?></span>
                        <span class="stat-label">Total Bookings</span>
                    </div>
                </div>
                <div class="stat-card border-green">
                    <div class="stat-card-header">
                        <div class="stat-icon green"><i class="fa-solid fa-check-double"></i></div>
                        <?php if ($todayCompleted > 0): ?>
                            <span class="stat-badge badge-green">+<?php echo $todayCompleted; ?> today</span>
                        <?php else: ?>
                            <span class="stat-badge badge-neutral">Active</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $completedBookings; ?></span>
                        <span class="stat-label">Completed</span>
                    </div>
                </div>
                <div class="stat-card border-orange">
                    <div class="stat-card-header">
                        <div class="stat-icon orange"><i class="fa-solid fa-clock"></i></div>
                        <?php if ($todayPending > 0): ?>
                            <span class="stat-badge badge-orange">+<?php echo $todayPending; ?></span>
                        <?php else: ?>
                            <span class="stat-badge badge-neutral">Active</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $pendingBookings; ?></span>
                        <span class="stat-label">Pending</span>
                    </div>
                </div>
                <div class="stat-card border-amber">
                    <div class="stat-card-header">
                        <div class="stat-icon amber"><i class="fa-solid fa-spinner"></i></div>
                        <?php if ($todayInProgress > 0): ?>
                            <span class="stat-badge badge-amber">+<?php echo $todayInProgress; ?></span>
                        <?php else: ?>
                            <span class="stat-badge badge-neutral">Active</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $inProgressBookings; ?></span>
                        <span class="stat-label">In Progress</span>
                    </div>
                </div>
                <div class="stat-card border-red">
                    <div class="stat-card-header">
                        <div class="stat-icon red"><i class="fa-solid fa-xmark"></i></div>
                        <?php if ($todayRejected > 0): ?>
                            <span class="stat-badge badge-red">+<?php echo $todayRejected; ?></span>
                        <?php else: ?>
                            <span class="stat-badge badge-red">-1</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $rejectedBookings; ?></span>
                        <span class="stat-label">Rejected</span>
                    </div>
                </div>
                <div class="stat-card border-red">
                    <div class="stat-card-header">
                        <div class="stat-icon red" style="background:#fef2f2; color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <?php if ($overdueBookings > 0): ?>
                            <span class="stat-badge badge-red">Action Needed</span>
                        <?php else: ?>
                            <span class="stat-badge badge-green">All Good</span>
                        <?php endif; ?>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value" style="<?php echo $overdueBookings > 0 ? 'color: #ef4444;' : ''; ?>"><?php echo $overdueBookings; ?></span>
                        <span class="stat-label">Overdue Tasks</span>
                    </div>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="card table-card">
                <div class="card-header-split">
                    <div class="card-header-title">
                        <h3><i class="fa-solid fa-flask-vial"></i> Recent Test Requests</h3>
                    </div>
                    <div class="card-header-filters">
                        <form method="GET" class="filter-form" id="filterForm">
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="search" placeholder="Search name, email..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="status-action-group">
                                <select name="status_filter" class="filter-select" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Accept" <?php echo $status_filter == 'Accept' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Complete" <?php echo $status_filter == 'Complete' ? 'selected' : ''; ?>>Complete</option>
                                    <option value="Cancel" <?php echo $status_filter == 'Cancel' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                                <button type="button" class="btn-refresh-filter" onclick="document.getElementById('filterForm').submit()" title="Refresh"><i class="fa-solid fa-rotate"></i></button>
                                <?php if (!empty($date_from) || !empty($date_to) || !empty($search) || !empty($status_filter)): ?>
                                    <a href="dashboard.php" class="btn-clear-filter" title="Clear all filters"><i class="fa-solid fa-xmark"></i> Clear</a>
                                <?php endif; ?>
                            </div>

                            <div class="date-filter-group">
                                <div class="date-input-wrap">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <input type="date" name="date_from" class="filter-date" value="<?php echo htmlspecialchars($date_from); ?>" data-prev="<?php echo htmlspecialchars($date_from); ?>" title="From Date" onchange="handleDateFilterChange()">
                                </div>
                                <span class="date-sep">to</span>
                                <div class="date-input-wrap">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <input type="date" name="date_to" class="filter-date" value="<?php echo htmlspecialchars($date_to); ?>" data-prev="<?php echo htmlspecialchars($date_to); ?>" title="To Date" onchange="handleDateFilterChange()">
                                </div>
                            </div>
                        </form>
                        <a href="export_bookings.php?search=<?php echo urlencode($search); ?>&status_filter=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>" class="btn-export">
                            <i class="fa-solid fa-file-export"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sample Info</th>
                                <th>Customer Details</th>
                                <th>Photo</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($bookings) > 0): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr data-id="<?php echo $booking['id']; ?>">
                                        <td><?php echo $booking['id']; ?></td>
                                        <td>
                                            <span class="badge-type"><?php echo htmlspecialchars($booking['sample_type']); ?></span><br>
                                            <span class="text-small"><?php echo htmlspecialchars($booking['num_samples']); ?> sample(s)</span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($booking['fullname']); ?></strong><br>
                                            <span class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($booking['sample_photo']): ?>
                                                <img src="../assets/uploads/<?php echo $booking['sample_photo']; ?>" alt="Sample" class="table-img">
                                            <?php else: ?>
                                                <span class="no-img">No Photo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px; display: flex; flex-direction: column; gap: 4px; line-height: 1.3;">
                                                <span style="color: #64748b; font-weight: 500;">
                                                    <span style="font-weight: 600; color: #334155;">Booked:</span> <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                                </span>
                                                <?php if ($booking['status'] === 'Accept' && !empty($booking['accepted_at'])): ?>
                                                    <span style="color: #0369a1; font-weight: 500; font-size: 12px; background-color: #f0f9ff; border: 1px solid #e0f2fe; padding: 2px 6px; border-radius: 4px; display: inline-block; width: max-content;">
                                                        <i class="fa-solid fa-pen-to-square" style="font-size: 9px; margin-right: 4px;"></i>Updated Status On: <?php echo date('M d, Y', strtotime($booking['accepted_at'])); ?>
                                                    </span>
                                                <?php elseif ($booking['status'] === 'Complete' && !empty($booking['completed_at'])): ?>
                                                    <span style="color: #15803d; font-weight: 500; font-size: 12px; background-color: #f0fdf4; border: 1px solid #dcfce7; padding: 2px 6px; border-radius: 4px; display: inline-block; width: max-content;">
                                                        <i class="fa-solid fa-circle-check" style="font-size: 9px; margin-right: 4px;"></i>Ended: <?php echo date('M d, Y', strtotime($booking['completed_at'])); ?>
                                                    </span>
                                                <?php elseif ($booking['status'] === 'Cancel' && !empty($booking['rejected_at'])): ?>
                                                    <span style="color: #b91c1c; font-weight: 500; font-size: 12px; background-color: #fef2f2; border: 1px solid #fee2e2; padding: 2px 6px; border-radius: 4px; display: inline-block; width: max-content;">
                                                        <i class="fa-solid fa-circle-xmark" style="font-size: 9px; margin-right: 4px;"></i>Rejected: <?php echo date('M d, Y', strtotime($booking['rejected_at'])); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $statusClass = strtolower($booking['status']);
                                                $displayStatus = $booking['status'];
                                                if ($displayStatus == 'Accept') $displayStatus = 'In Progress';
                                                if ($displayStatus == 'Cancel') $displayStatus = 'Rejected';
                                                
                                                // Fetch files for this booking to include in both table and modal
                                                $fileStmt = $conn->prepare("SELECT * FROM booking_files WHERE booking_id = :bid");
                                                $fileStmt->execute(['bid' => $booking['id']]);
                                                $files = $fileStmt->fetchAll(PDO::FETCH_ASSOC);
                                                $booking['attached_files'] = $files; // Add to booking array
                                            ?>
                                             <div class="status-badge-container" style="display: flex; flex-direction: column; gap: 5px; align-items: flex-start;">
                                                 <span class="status-badge <?php echo $statusClass; ?>">
                                                     <?php echo $displayStatus; ?>
                                                 </span>
                                                 
                                                 <?php if ($booking['status'] === 'Accept' && !empty($booking['estimated_time'])): ?>
                                                     <div class="estimated-timer" data-endtime="<?php echo str_replace(' ', 'T', $booking['estimated_time']); ?>">
                                                         <i class="fa-solid fa-hourglass-half"></i>
                                                         <span class="timer-display">Calculating...</span>
                                                     </div>
                                                 <?php endif; ?>
                                                 
                                                 <?php if ($booking['status'] === 'Complete' && count($files) > 0): ?>
                                                     <div class="booking-files-list">
                                                         <?php foreach ($files as $file): 
                                                             $icon = "fa-file";
                                                             if (strpos($file['file_type'], 'pdf') !== false) $icon = "fa-file-pdf";
                                                             if (strpos($file['file_type'], 'image') !== false) $icon = "fa-file-image";
                                                             if (strpos($file['file_type'], 'word') !== false || strpos($file['file_name'], '.doc') !== false) $icon = "fa-file-word";
                                                         ?>
                                                             <a href="../assets/uploads/reports/<?php echo $file['file_name']; ?>" target="_blank" class="file-link-icon" data-file-id="<?php echo $file['id']; ?>" title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                                                 <i class="fa-solid <?php echo $icon; ?>"></i>
                                                             </a>
                                                         <?php endforeach; ?>
                                                     </div>
                                                 <?php endif; ?>
                                             </div>
                                        </td>
                                        <td>
                                            <div class="action-btns">
                                                <button class="btn-icon view" onclick="openViewModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)" title="View Details"><i class="fa-solid fa-eye"></i></button>
                                                <?php if ($_SESSION['role'] !== 'super_admin'): ?>
                                                    <button class="btn-icon edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)" title="Edit Status"><i class="fa-solid fa-pencil"></i></button>
                                                    <button class="btn-icon delete" onclick="deleteBooking(<?php echo $booking['id']; ?>)" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-state">No requests found.</td>
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
                            <input type="hidden" name="status_filter" value="<?php echo htmlspecialchars($status_filter); ?>">
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
                        $queryString = "&search=".urlencode($search)."&status_filter=".urlencode($status_filter)."&limit=".$limit;
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

<!-- View Details Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Booking Details - #<span id="view_id_title"></span></h3>
            <span class="close" onclick="closeViewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="view-grid">
                <div class="view-section">
                    <h4><i class="fa-solid fa-flask-vial"></i> Sample Information</h4>
                    <div class="view-row">
                        <span class="view-label">Type:</span>
                        <span class="view-value" id="view_sample_type"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Quantity:</span>
                        <span class="view-value"><span id="view_num_samples"></span> samples</span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Description:</span>
                        <span class="view-value" id="view_sample_desc"></span>
                    </div>
                </div>
                
                <div class="view-section">
                    <h4><i class="fa-solid fa-user"></i> Customer Details</h4>
                    <div class="view-row">
                        <span class="view-label">Name:</span>
                        <span class="view-value" id="view_fullname"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Company:</span>
                        <span class="view-value" id="view_company"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Email:</span>
                        <span class="view-value" id="view_email"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Phone:</span>
                        <span class="view-value" id="view_phone"></span>
                    </div>
                </div>

                <div class="view-section full-width">
                    <h4><i class="fa-solid fa-signal"></i> Booking Status</h4>
                    <div class="view-row">
                        <span class="view-label">Status:</span>
                        <span id="view_status_badge" class="status-badge" style="display: inline-block;"></span>
                    </div>
                    <div class="view-row" id="view_status_time_row">
                        <span class="view-label" id="view_status_time_label">Updated At:</span>
                        <span class="view-value" id="view_status_time"></span>
                    </div>
                </div>
                
                <div class="view-section full-width">
                    <h4><i class="fa-solid fa-clipboard-list"></i> Special Requirements</h4>
                    <div id="view_requirements" style="font-size: 14px; color: #475569; line-height: 1.6;"></div>
                </div>

                <div class="view-section full-width" id="view_rejection_section" style="display:none;">
                    <h4><i class="fa-solid fa-circle-xmark" style="color:#ef4444;"></i> Rejection Reason</h4>
                    <div id="view_rejection_reason" style="font-size: 14px; color: #ef4444; line-height: 1.6; background:#fff1f2; padding:12px 15px; border-radius:8px; border:1px solid #fecdd3;"></div>
                </div>

                <div class="view-section full-width" id="view_estimated_section" style="display:none;">
                    <h4><i class="fa-solid fa-clock" style="color:#f59e0b;"></i> Estimated Completion Time</h4>
                    <div id="view_estimated_time" style="font-size: 14px; color: #92400e; line-height: 1.6; background:#fffbeb; padding:12px 15px; border-radius:8px; border:1px solid #fde68a; font-weight:600;"></div>
                </div>

                <div class="view-section full-width" id="view_files_section">
                    <h4><i class="fa-solid fa-file-arrow-down"></i> Test Reports & Files</h4>
                    <div id="view_files_list" class="booking-files-list"></div>
                </div>
                
                <div class="view-section full-width" id="view_photo_section">
                    <h4><i class="fa-solid fa-image"></i> Sample Photo</h4>
                    <img id="view_sample_photo" src="" alt="Sample Photo" class="view-full-img">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Booking Status</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit_booking_id">
            
            <div id="terminal_status_alert" class="hidden" style="background-color: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; padding: 12px 15px; border-radius: 8px; font-size: 14px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 16px;"></i>
                <span id="terminal_status_message">This booking has reached a terminal status and cannot be modified further.</span>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select id="edit_status" class="form-control" onchange="handleModalStatusChange()">
                    <option value="Pending">Pending</option>
                    <option value="Accept">Accept (In Progress)</option>
                    <option value="Complete">Complete</option>
                    <option value="Cancel">Cancel (Rejected)</option>
                </select>
            </div>

            <div id="edit_time_wrapper" class="form-group hidden">
                <label>Estimated Completion Time</label>
                <input type="text" id="edit_estimated_time" class="form-control" placeholder="Select date &amp; time..." readonly style="background:#fff; cursor:pointer;">
                <small style="color:#64748b; font-size:12px; margin-top:6px; display:block;"><i class="fa-solid fa-circle-info" style="color:#3b82f6;"></i> Click the field to pick a date and time, then click <strong>Confirm</strong>.</small>
            </div>

            <div id="edit_cancel_wrapper" class="form-group hidden">
                <label>Rejection Reason <span style="color:#ef4444;">*</span></label>
                <textarea id="edit_rejection_reason" class="form-control" rows="3" placeholder="Please provide a reason for rejecting this booking..." style="resize: vertical; min-height: 80px;"></textarea>
            </div>

            <div id="edit_pdf_wrapper" class="form-group hidden">
                <label>Upload Reports / Files</label>
                <div class="upload-zone" id="admin-upload-zone" onclick="document.getElementById('edit_result_files').click()">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <p>Click to browse or drag &amp; drop files here</p>
                    <span>PDF, DOC, DOCX, PNG, JPG, SVG &amp; more &mdash; Multiple files allowed</span>
                </div>
                <input type="file" id="edit_result_files" multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg,.svg,.gif,.webp,.txt,.csv,.zip"
                    style="display:none;"
                    onchange="handleAdminFileSelect(this.files)">
                <div id="new_files_preview" class="new-files-preview"></div>
                <div id="current_files_list" class="mt-2"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal()">Cancel</button>
          <button class="btn-primary"
        id="saveStatusBtn"
        onclick="saveStatusUpdate()">

    <span id="saveBtnText">
        Save Changes
    </span>

    <span id="saveBtnLoader"
          style="display:none; margin-left:8px;">

        <i class="fa-solid fa-spinner fa-spin"></i>

    </span>

</button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="width: 400px; text-align: center;">
        <div class="modal-body" style="padding: 40px 30px;">
            <div style="font-size: 50px; color: #ef4444; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <h3 style="margin-bottom: 10px; color: #1e293b;">Are you sure?</h3>
            <p style="color: #64748b; margin-bottom: 30px;">Do you really want to delete this booking? This action cannot be undone.</p>
            <input type="hidden" id="delete_booking_id">
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button class="btn-secondary" onclick="closeDeleteModal()" style="padding: 12px 25px;">No, Cancel</button>
                <button class="btn-primary" onclick="confirmDelete()" style="background-color: #ef4444; padding: 12px 25px;">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// ---- Admin File Upload with Preview ----
let selectedFiles = []; // Track selected files as array

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    if (['pdf'].includes(ext)) return 'fa-file-pdf';
    if (['doc','docx'].includes(ext)) return 'fa-file-word';
    if (['xls','xlsx','csv'].includes(ext)) return 'fa-file-excel';
    if (['ppt','pptx'].includes(ext)) return 'fa-file-powerpoint';
    if (['png','jpg','jpeg','gif','webp','svg'].includes(ext)) return 'fa-file-image';
    if (['zip','rar','7z'].includes(ext)) return 'fa-file-zipper';
    if (['txt'].includes(ext)) return 'fa-file-lines';
    return 'fa-file';
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
    return (bytes/(1024*1024)).toFixed(1) + ' MB';
}

function formatDateTime(dtStr) {
    if (!dtStr) return 'N/A';
    const dt = new Date(dtStr.replace(' ', 'T'));
    if (isNaN(dt.getTime())) return dtStr;
    return dt.toLocaleString('en-US', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit', hour12: true });
}

function handleAdminFileSelect(files) {
    // Merge new files into selectedFiles array (avoid exact duplicates by name+size)
    Array.from(files).forEach(file => {
        const isDup = selectedFiles.some(f => f.name === file.name && f.size === file.size);
        if (!isDup) selectedFiles.push(file);
    });
    renderFilePreview();
}

function removeSelectedFile(index) {
    selectedFiles.splice(index, 1);
    renderFilePreview();
    syncFileInput();
}

function renderFilePreview() {
    const preview = document.getElementById('new_files_preview');
    preview.innerHTML = '';
    selectedFiles.forEach((file, i) => {
        const icon = getFileIcon(file.name);
        const item = document.createElement('div');
        item.className = 'preview-file-item';
        item.innerHTML = `
            <div class="preview-file-info">
                <i class="fa-solid ${icon}"></i>
                <span class="preview-file-name" title="${file.name}">${file.name}</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="preview-file-size">${formatFileSize(file.size)}</span>
                <button type="button" class="btn-remove-preview" onclick="removeSelectedFile(${i})" title="Remove">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `;
        preview.appendChild(item);
    });
    syncFileInput();
}

function syncFileInput() {
    // Rebuild a DataTransfer to keep the hidden file input in sync
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    document.getElementById('edit_result_files').files = dt.files;
}

function clearFileUpload() {
    selectedFiles = [];
    document.getElementById('new_files_preview').innerHTML = '';
    document.getElementById('edit_result_files').value = '';
}

// Drag and drop
document.addEventListener('DOMContentLoaded', function() {
    const zone = document.getElementById('admin-upload-zone');
    if (!zone) return;
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('dragover');
        handleAdminFileSelect(e.dataTransfer.files);
    });
});

// Initialize Flatpickr on the estimated time input
let flatpickrInstance = null;


function initFlatpickr() {
    if (flatpickrInstance) {
        flatpickrInstance.destroy();
    }
    flatpickrInstance = flatpickr('#edit_estimated_time', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        altInput: true,
        altFormat: 'd M Y, h:i K',
        minDate: 'today',
        position: 'above',
        time_24hr: false,
        minuteIncrement: 15,
        onReady: function(selectedDates, dateStr, instance) {
            // Add a custom Confirm button inside the calendar
            const confirmBtn = document.createElement('button');
            confirmBtn.className = 'flatpickr-confirm';
            confirmBtn.innerHTML = '<i class="fa-solid fa-check" style="margin-right:6px;"></i> Confirm Date & Time';
            confirmBtn.addEventListener('click', function() {
                instance.close();
            });
            instance.calendarContainer.appendChild(confirmBtn);
        }
    });
}

// Track current open booking for live DOM sync
let currentBooking = null;

function openViewModal(booking) {
    currentBooking = booking;
    document.getElementById('view_id_title').innerText = booking.id;
    document.getElementById('view_sample_type').innerText = booking.sample_type;
    document.getElementById('view_num_samples').innerText = booking.num_samples;
    document.getElementById('view_sample_desc').innerText = booking.sample_desc || 'No description provided';
    document.getElementById('view_fullname').innerText = booking.fullname;
    document.getElementById('view_company').innerText = booking.company || 'N/A';
    document.getElementById('view_email').innerText = booking.email;
    document.getElementById('view_phone').innerText = booking.phone;
    document.getElementById('view_requirements').innerText = booking.requirements || 'No special requirements';
    
    // Handle Status and Date
    const statusBadge = document.getElementById('view_status_badge');
    const statusTimeRow = document.getElementById('view_status_time_row');
    const statusTimeLabel = document.getElementById('view_status_time_label');
    const statusTime = document.getElementById('view_status_time');

    statusBadge.className = 'status-badge ' + booking.status.toLowerCase();
    
    let displayStatus = booking.status;
    let statusLabel = 'Updated Status On:';
    let statusDate = null;
    
    if (booking.status === 'Pending') {
        displayStatus = 'Pending';
        statusLabel = 'Booked On:';
        statusDate = booking.created_at;
    } else if (booking.status === 'Accept') {
        displayStatus = 'In Progress';
        statusLabel = 'Updated Status On:';
        statusDate = booking.accepted_at;
    } else if (booking.status === 'Complete') {
        displayStatus = 'Complete';
        statusLabel = 'Completed On:';
        statusDate = booking.completed_at;
    } else if (booking.status === 'Cancel') {
        displayStatus = 'Rejected';
        statusLabel = 'Rejected On:';
        statusDate = booking.rejected_at;
    }
    
    statusBadge.innerText = displayStatus.toUpperCase();
    statusTimeLabel.innerText = statusLabel;
    statusTime.innerText = formatDateTime(statusDate);
    statusTimeRow.style.display = 'flex';
    
    // Handle Rejection Reason
    const rejectionSection = document.getElementById('view_rejection_section');
    if (booking.status === 'Cancel' && booking.rejection_reason) {
        document.getElementById('view_rejection_reason').innerText = booking.rejection_reason;
        rejectionSection.style.display = 'block';
    } else {
        rejectionSection.style.display = 'none';
    }
    
    // Handle Photos
    const photoImg = document.getElementById('view_sample_photo');
    if (booking.sample_photo) {
        photoImg.src = '../assets/uploads/' + booking.sample_photo;
        document.getElementById('view_photo_section').style.display = 'block';
    } else {
        document.getElementById('view_photo_section').style.display = 'none';
    }

    // Handle Attached Files — only show for Complete status
    const filesList = document.getElementById('view_files_list');
    filesList.innerHTML = '';
    if (booking.status === 'Complete' && booking.attached_files && booking.attached_files.length > 0) {
        document.getElementById('view_files_section').style.display = 'block';
        booking.attached_files.forEach(file => {
            let icon = getFileIcon(file.file_name);
            
            const fileItem = document.createElement('div');
            fileItem.className = 'edit-file-item';
            fileItem.id = 'view_file_item_' + file.id;
            fileItem.innerHTML = `
                <div class="edit-file-info">
                    <a href="../assets/uploads/reports/${file.file_name}" target="_blank" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid ${icon}"></i>
                        <span class="edit-file-name">${file.file_name}</span>
                    </a>
                </div>
                <button type="button" class="btn-delete-file" onclick="deleteFile(${file.id}, true)" title="Delete File">
                    <i class="fa-solid fa-xmark" style="font-size: 16px;"></i>
                </button>
            `;
            filesList.appendChild(fileItem);
        });
    } else {
        document.getElementById('view_files_section').style.display = 'none';
    }
    
    // Show estimated time only for Accept (In Progress)
    const estTimeSection = document.getElementById('view_estimated_section');
    if (estTimeSection) {
        if (booking.status === 'Accept' && booking.estimated_time) {
            const dt = new Date(booking.estimated_time.replace(' ', 'T'));
            const formatted = dt.toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
            document.getElementById('view_estimated_time').innerText = formatted;
            estTimeSection.style.display = 'block';
        } else {
            estTimeSection.style.display = 'none';
        }
    }
    
    document.getElementById('viewModal').style.display = "block";
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = "none";
}

function deleteBooking(id) {
    document.getElementById('delete_booking_id').value = id;
    document.getElementById('deleteModal').style.display = "block";
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = "none";
}

function confirmDelete() {
    const id = document.getElementById('delete_booking_id').value;
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('delete_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function openEditModal(booking) {
    currentBooking = booking;
    document.getElementById('edit_booking_id').value = booking.id;
    
    const editStatus = document.getElementById('edit_status');
    editStatus.value = booking.status;
    
    // Enable/disable options based on current status
    const optPending = editStatus.querySelector('option[value="Pending"]');
    const optAccept = editStatus.querySelector('option[value="Accept"]');
    const optComplete = editStatus.querySelector('option[value="Complete"]');
    const optCancel = editStatus.querySelector('option[value="Cancel"]');
    
    // Reset all disabled flags first
    optPending.disabled = false;
    optAccept.disabled = false;
    optComplete.disabled = false;
    optCancel.disabled = false;
    editStatus.disabled = false;
    
    // Get alert elements and save button
    const alertBox = document.getElementById('terminal_status_alert');
    const alertMsg = document.getElementById('terminal_status_message');
    const saveBtn = document.getElementById('saveStatusBtn');
    
    alertBox.classList.add('hidden');
    saveBtn.style.display = '';
    
    if (booking.status === 'Pending') {
        optComplete.disabled = true;
    } else if (booking.status === 'Accept') {
        optPending.disabled = true;
    } else if (booking.status === 'Complete') {
        editStatus.disabled = true;
        alertBox.classList.remove('hidden');
        alertBox.style.backgroundColor = '#ecfdf5';
        alertBox.style.color = '#065f46';
        alertBox.style.borderColor = '#d1fae5';
        alertBox.querySelector('i').className = 'fa-solid fa-circle-check';
        alertMsg.innerText = 'This booking has been completed. The status is locked and cannot be modified.';
        saveBtn.style.display = 'none';
    } else if (booking.status === 'Cancel') {
        editStatus.disabled = true;
        alertBox.classList.remove('hidden');
        alertBox.style.backgroundColor = '#fef2f2';
        alertBox.style.color = '#991b1b';
        alertBox.style.borderColor = '#fee2e2';
        alertBox.querySelector('i').className = 'fa-solid fa-circle-exclamation';
        alertMsg.innerText = 'This booking has been rejected. The status is locked and cannot be modified.';
        saveBtn.style.display = 'none';
    }
    
    // Initialize Flatpickr fresh each time the modal opens
    initFlatpickr();
    
    // Clear rejection reason textarea & file upload
    document.getElementById('edit_rejection_reason').value = '';
    clearFileUpload();
    
    // ONLY show the time if the booking is already in 'Accept' status.
    // If it's Pending, Cancel, or Complete, we start with a clean empty field.
    if (booking.status === 'Accept' && booking.estimated_time) {
        const existing = booking.estimated_time.replace(' ', 'T').slice(0, 16);
        flatpickrInstance.setDate(existing, true);
    } else {
        flatpickrInstance.clear();
    }
    
    // Show current files ONLY if current saved status is Complete
    const currentFilesList = document.getElementById('current_files_list');
    currentFilesList.innerHTML = '';
    
    if (booking.status === 'Complete' && booking.attached_files && booking.attached_files.length > 0) {
        booking.attached_files.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'edit-file-item';
            fileItem.id = 'file_item_' + file.id;
            
            let icon = getFileIcon(file.file_name);
            
            fileItem.innerHTML = `
                <div class="edit-file-info">
                    <i class="fa-solid ${icon}"></i>
                    <span class="edit-file-name">${file.file_name}</span>
                </div>
                <button type="button" class="btn-delete-file" onclick="deleteFile(${file.id})" title="Delete File">
                    <i class="fa-solid fa-xmark" style="font-size: 16px;"></i>
                </button>
            `;
            currentFilesList.appendChild(fileItem);
        });
    }
    
    handleModalStatusChange();
    document.getElementById('editModal').style.display = "block";
}

function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toast-message');
    toastMsg.innerText = message;
    toast.style.display = 'flex';
    
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

function deleteFile(fileId, fromView = false) {
    if (!confirm('Are you sure you want to delete this file? This cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('file_id', fileId);
    
    fetch('delete_file.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove from Edit Modal DOM
            const editItem = document.getElementById('file_item_' + fileId);
            if (editItem) editItem.remove();
            
            // Remove from View Modal DOM
            const viewItem = document.getElementById('view_file_item_' + fileId);
            if (viewItem) viewItem.remove();
            
            // Remove file icon from the STATUS column in the table row
            const tableIcon = document.querySelector(`.file-link-icon[data-file-id="${fileId}"]`);
            if (tableIcon) tableIcon.remove();
            
            // Update in-memory booking object so reopening modal shows correct data
            if (currentBooking && currentBooking.attached_files) {
                currentBooking.attached_files = currentBooking.attached_files.filter(
                    f => parseInt(f.id) !== parseInt(fileId)
                );
                
                // Sync the updated booking JSON back into the table row's onclick buttons
                const row = document.querySelector(`tr[data-id="${currentBooking.id}"]`);
                if (row) {
                    const updatedJson = JSON.stringify(currentBooking)
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;');
                    const viewBtn = row.querySelector('.btn-icon.view');
                    const editBtn = row.querySelector('.btn-icon.edit[onclick^="openEditModal"]');
                    if (viewBtn) viewBtn.setAttribute('onclick', `openViewModal(${JSON.stringify(currentBooking).replace(/'/g, "\\'")})`);
                    if (editBtn) editBtn.setAttribute('onclick', `openEditModal(${JSON.stringify(currentBooking).replace(/'/g, "\\'")})`);
                }
            }
            
            showToast('File removed successfully');
        } else {
            alert('Error deleting file: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the file.');
    });
}

function closeModal() {
    document.getElementById('editModal').style.display = "none";
}

function handleModalStatusChange() {
    const status = document.getElementById('edit_status').value;
    const timeWrapper = document.getElementById('edit_time_wrapper');
    const pdfWrapper = document.getElementById('edit_pdf_wrapper');
    const cancelWrapper = document.getElementById('edit_cancel_wrapper');
    
    timeWrapper.classList.toggle('hidden', status !== 'Accept');
    pdfWrapper.classList.toggle('hidden', status !== 'Complete');
    cancelWrapper.classList.toggle('hidden', status !== 'Cancel');
}

function saveStatusUpdate() {
    const id = document.getElementById('edit_booking_id').value;
    const status = document.getElementById('edit_status').value;
    const estimated_time = document.getElementById('edit_estimated_time').value;
    const rejection_reason = document.getElementById('edit_rejection_reason').value.trim();
    const fileInput = document.getElementById('edit_result_files');
    const files = fileInput.files;
    
    if (status === 'Accept' && !estimated_time) {
        alert('Estimated completion time is required when accepting a booking.');
        return;
    }

    if (status === 'Cancel' && !rejection_reason) {
        alert('A rejection reason is required when rejecting a booking.');
        document.getElementById('edit_rejection_reason').focus();
        return;
    }

    if (status === 'Complete' && files.length === 0) {
        // Only require if no files were already uploaded for this booking
        const currentFiles = document.getElementById('current_files_list');
        if (!currentFiles || currentFiles.children.length === 0) {
            alert('At least one report file is required to complete this booking.');
            return;
        }
    }
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', status);
    formData.append('estimated_time', estimated_time);
    formData.append('rejection_reason', rejection_reason);
    
    for (let i = 0; i < files.length; i++) {
        formData.append('result_files[]', files[i]);
    }
    
// Show loader
document.getElementById('saveStatusBtn').disabled = true;

document.getElementById('saveBtnText').innerText =
    'Saving...';

document.getElementById('saveBtnLoader').style.display =
    'inline-block';

fetch('update_status.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {

    if (data.success) {

        document.getElementById('saveBtnText').innerText =
            'Saved ✓';

        setTimeout(() => {
            location.reload();
        }, 500);

    } else {

        document.getElementById('saveStatusBtn').disabled =
            false;

        document.getElementById('saveBtnText').innerText =
            'Save Changes';

        document.getElementById('saveBtnLoader').style.display =
            'none';

        alert('Error: ' + data.message);
    }

})
.catch(error => {

    document.getElementById('saveStatusBtn').disabled =
        false;

    document.getElementById('saveBtnText').innerText =
        'Save Changes';

    document.getElementById('saveBtnLoader').style.display =
        'none';

    console.error('Error:', error);

    alert('Something went wrong while saving.');
});

} // <-- ADD THIS MISSING BRACE





function handleDateFilterChange() {
    const fromInput = document.querySelector('input[name="date_from"]');
    const toInput = document.querySelector('input[name="date_to"]');
    const fromVal = fromInput.value;
    const toVal = toInput.value;
    
    const prevFrom = fromInput.getAttribute('data-prev') || '';
    const prevTo = toInput.getAttribute('data-prev') || '';
    
    // If both are filled, submit the form
    if (fromVal && toVal) {
        fromInput.form.submit();
        return;
    }
    
    // If both are empty (manually cleared) and we had active filters, submit to clear
    if (!fromVal && !toVal && (prevFrom || prevTo)) {
        fromInput.form.submit();
        return;
    }
    
    // If we had active filters (both filled) and now one is cleared, submit to clear the filter
    if (prevFrom && prevTo && (!fromVal || !toVal)) {
        fromInput.form.submit();
        return;
    }
}

// Render Notifications on load
// Handled by includes/admin_notifications_sync.php

// Countdown Timer Logic
function updateTimers() {
    const timers = document.querySelectorAll('.estimated-timer');
    timers.forEach(timer => {
        const endTime = new Date(timer.dataset.endtime).getTime();
        const now = new Date().getTime();
        const distance = endTime - now;

        const display = timer.querySelector('.timer-display');

        if (distance < 0 || isNaN(distance)) {
            display.innerHTML = "Overdue: Please reach out shortly";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        let timeStr = "";
        if (days > 0) timeStr += days + "d ";
        timeStr += hours + "h " + minutes + "m " + seconds + "s";
        
        display.innerHTML = timeStr + " left";
    });
}

// Update timers every second
setInterval(updateTimers, 1000);
updateTimers(); // Initial call

// Close modal when clicking outside
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const viewModal = document.getElementById('viewModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target == editModal) closeModal();
    if (event.target == viewModal) closeViewModal();
    if (event.target == deleteModal) closeDeleteModal();
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
