<?php
session_start();
include 'includes/db_connect.php';

// Check if user is logged in as a client
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

// Fetch unread notifications count for sidebar badge
$sidebar_unread_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmtUnread = $conn->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = :uid AND is_read = 0");
        $stmtUnread->execute(['uid' => $_SESSION['user_id']]);
        $sidebar_unread_count = (int) $stmtUnread->fetchColumn();
    } catch (PDOException $e) {
    }
}

$user_email = !empty($_SESSION['email']) ? $_SESSION['email'] : ($_SESSION['username'] ?? '');
$fullname = $_SESSION['fullname'];

// Fetch summary stats for THIS client
try {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email))");
    $stmtTotal->execute(['email' => $user_email]);
    $totalBookings = $stmtTotal->fetchColumn();

    $stmtComp = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email)) AND status = 'Complete'");
    $stmtComp->execute(['email' => $user_email]);
    $completedBookings = $stmtComp->fetchColumn();

    $stmtPend = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email)) AND status = 'Pending'");
    $stmtPend->execute(['email' => $user_email]);
    $pendingBookings = $stmtPend->fetchColumn();

    $stmtInProg = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email)) AND status = 'Accept'");
    $stmtInProg->execute(['email' => $user_email]);
    $inProgressBookings = $stmtInProg->fetchColumn();

    $stmtRej = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email)) AND status = 'Cancel'");
    $stmtRej->execute(['email' => $user_email]);
    $rejectedBookings = $stmtRej->fetchColumn();

    $completionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100) : 0;

    // Pagination parameters
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch total records for pagination
    $stmtCount = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email))");
    $stmtCount->execute(['email' => $user_email]);
    $totalRecords = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // Fetch client's bookings (newest first by ID)
    $stmt = $conn->prepare("SELECT * FROM test_bookings WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email)) ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':email', $user_email, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch attached files for each booking
    foreach ($bookings as &$booking) {
        $stmtFiles = $conn->prepare("SELECT * FROM booking_files WHERE booking_id = :id");
        $stmtFiles->execute(['id' => $booking['id']]);
        $booking['attached_files'] = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($booking); // Break the reference to avoid duplication in later loops

    // Fetch notifications for THIS client
    $stmtNotify = $conn->prepare("SELECT * FROM user_notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 20");
    $stmtNotify->execute(['uid' => $_SESSION['user_id']]);
    $notifications = $stmtNotify->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notifications as &$n) {
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
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - AKC</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/fav-icon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/fav-icon.svg">
    <link rel="apple-touch-icon" href="assets/images/fav-icon.svg">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .sidebar-logo-text {
            color: white;
            font-weight: 700;
            font-size: 20px;
        }

        .client-badge {
            background: #10b981;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            margin-left: 5px;
        }

        .client-stats .stats-row {
            grid-template-columns: repeat(6, 1fr);
        }

        /* Mobile Layout Overrides */
        @media (max-width: 991px) {
            .client-stats .stats-row {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .card-header-split {
                padding: 15px;
                display: flex;
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .card-header-filters {
                width: 100%;
                display: flex;
                flex-direction: column;
            }

            .card-header-filters .btn-export {
                margin-left: 0 !important;
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 600px) {
            .client-stats .stats-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 600px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        .modal-lg {
            width: 800px;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 15px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            padding: 15px 25px;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #64748b;
            transition: color 0.2s;
        }

        .close:hover {
            color: #1e293b;
        }

        .view-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .view-section {
            display: flex;
            flex-direction: column;
        }

        .view-section h4 {
            font-size: 13px;
            text-transform: uppercase;
            color: #1e293b;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .view-section h4 i {
            color: #3b82f6;
            width: 20px;
            text-align: center;
        }

        .view-row {
            display: flex;
            margin-bottom: 12px;
            font-size: 14px;
            align-items: flex-start;
        }

        .view-label {
            width: 130px;
            color: #64748b;
            font-weight: 600;
            flex-shrink: 0;
        }

        .view-value {
            color: #1e293b;
            font-weight: 500;
        }

        .info-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            min-height: 50px;
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
        }

        .full-width {
            grid-column: span 2;
        }

        .view-full-img {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .view-file-item {
            margin-bottom: 10px;
        }

        .view-file-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.2s;
            font-size: 14px;
        }

        .view-file-link:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
            color: #3b82f6;
        }

        /* Timer Styles */
        .status-wrapper {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: flex-start;
        }

        .estimated-timer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #ef4444;
            font-weight: 700;
            background: #fff1f2;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #fecdd3;
        }

        .estimated-timer i {
            color: #ef4444;
            font-size: 10px;
        }

        /* Notification System */
        .notification-container {
            position: relative;
            margin-right: 15px;
        }

        .notification-bell {
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
            font-size: 20px;
            color: #64748b;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-bell:hover {
            background: #f1f5f9;
            color: #3b82f6;
        }

        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            border-radius: 50%;
            min-width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            z-index: 1000;
            margin-top: 10px;
            display: none;
            overflow: hidden;
            animation: fadeIn 0.2s ease;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
        }

        .notification-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        .mark-read-btn {
            background: none;
            border: none;
            font-size: 11px;
            color: #3b82f6;
            cursor: pointer;
            font-weight: 600;
            padding: 2px 5px;
        }

        .mark-read-btn:hover {
            text-decoration: underline;
        }

        .notification-list {
            max-height: 250px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 4px;
            transition: background 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            text-align: left;
        }

        .notification-item:hover {
            background: #f0f7ff;
        }

        .notification-item.unread {
            background: #eff6ff;
        }

        .notification-item-text {
            font-size: 13px;
            color: #1e293b;
            font-weight: 500;
            line-height: 1.4;
        }

        .notification-item-time {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        .notification-empty {
            padding: 30px 15px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        .sidebar-badge {
            background-color: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            border-radius: 10px;
            padding: 2px 6px;
            margin-left: auto;
            display: inline-block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="admin-body-bg">

    <div class="admin-wrapper">
        <!-- Left Sidebar -->
        <?php include 'includes/client_sidebar.php'; ?>

        <!-- Right Content Area -->
        <main class="admin-main">
            <!-- Top Bar -->
            <?php include 'includes/client_topbar.php'; ?>

            <!-- Content Body -->
            <div class="admin-content">
                <!-- Stats Row -->
                <div class="client-stats">
                    <div class="stats-row">
                        <div class="stat-card border-blue">
                            <div class="stat-card-header">
                                <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                                <span class="stat-badge badge-neutral">All</span>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $totalBookings; ?></span>
                                <span class="stat-label">Total Requests</span>
                            </div>
                        </div>
                        <div class="stat-card border-green">
                            <div class="stat-card-header">
                                <div class="stat-icon green"><i class="fa-solid fa-check-double"></i></div>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $completedBookings; ?></span>
                                <span class="stat-label">Completed</span>
                            </div>
                        </div>
                        <div class="stat-card border-orange">
                            <div class="stat-card-header">
                                <div class="stat-icon orange"><i class="fa-solid fa-clock"></i></div>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $pendingBookings; ?></span>
                                <span class="stat-label">Pending</span>
                            </div>
                        </div>
                        <div class="stat-card border-amber">
                            <div class="stat-card-header">
                                <div class="stat-icon amber"><i class="fa-solid fa-spinner"></i></div>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $inProgressBookings; ?></span>
                                <span class="stat-label">In Progress</span>
                            </div>
                        </div>
                        <div class="stat-card border-red">
                            <div class="stat-card-header">
                                <div class="stat-icon red"><i class="fa-solid fa-xmark"></i></div>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $rejectedBookings; ?></span>
                                <span class="stat-label">Rejected</span>
                            </div>
                        </div>
                        <div class="stat-card border-purple">
                            <div class="stat-card-header">
                                <div class="stat-icon purple"><i class="fa-solid fa-chart-line"></i></div>
                            </div>
                            <div class="stat-info">
                                <span class="stat-value"><?php echo $completionRate; ?>%</span>
                                <span class="stat-label">Completion Rate</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Data Table -->
                <div class="card table-card">
                    <div class="card-header-split">
                        <div class="card-header-title">
                            <h3><i class="fa-solid fa-flask-vial"></i> My Test History</h3>
                        </div>
                        <div class="card-header-filters">
                            <a href="book-test.php" class="btn-export"
                                style="background-color: #3b82f6; border: none; color: white; margin-left: auto;">
                                <i class="fa-solid fa-plus"></i> New Booking
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SAMPLE INFO</th>
                                    <th>PHOTO</th>
                                    <th>DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td class="id-cell"><?php echo $booking['id']; ?></td>
                                        <td>
                                            <div class="sample-info">
                                                <span
                                                    class="sample-type"><?php echo htmlspecialchars($booking['sample_type']); ?></span>
                                                <span class="sample-count"><?php echo $booking['num_samples']; ?>
                                                    sample(s)</span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($booking['sample_photo'])):
                                                $photo = $booking['sample_photo'];
                                                $ext = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
                                                if (strpos($photo, 'data:') === 0) {
                                                    $photoPath = $photo;
                                                } else {
                                                    $photoPath = (file_exists(__DIR__ . '/assets/uploads/samples/' . $photo) || strpos($photo, 'sample_') === 0)
                                                        ? 'assets/uploads/samples/' . $photo
                                                        : 'assets/uploads/' . $photo;
                                                }
                                                if ($ext === 'pdf' || strpos($photo, 'data:application/pdf') === 0): ?>
                                                    <a href="<?php echo htmlspecialchars($photoPath); ?>" target="_blank"
                                                        class="badge"
                                                        style="background:#fee2e2; color:#b91c1c; padding: 4px 8px; font-size: 11px; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                                    </a>
                                                <?php else: ?>
                                                    <img src="<?php echo htmlspecialchars($photoPath); ?>"
                                                        onerror="if(!this.dataset.retried && !this.src.startsWith('data:')){this.dataset.retried=1; this.src=(this.src.indexOf('/samples/')!==-1)?'assets/uploads/<?php echo htmlspecialchars($photo); ?>':'assets/uploads/samples/<?php echo htmlspecialchars($photo); ?>';} else { this.src='assets/images/plastic_resin/PET Bottles.webp'; }"
                                                        alt="Sample" class="table-img">
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <img src="assets/images/plastic_resin/PET Bottles.webp" alt="Sample" class="table-img" title="Sample Specimen">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="date-cell">
                                                <span
                                                    class="date-text"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="status-wrapper">
                                                <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                                                    <?php
                                                    if ($booking['status'] == 'Accept')
                                                        echo 'IN PROGRESS';
                                                    else if ($booking['status'] == 'Cancel')
                                                        echo 'REJECTED';
                                                    else
                                                        echo strtoupper($booking['status']);
                                                    ?>
                                                </span>
                                                <?php if ($booking['status'] == 'Accept' && !empty($booking['estimated_time'])): ?>
                                                    <div class="estimated-timer"
                                                        data-endtime="<?php echo str_replace(' ', 'T', $booking['estimated_time']); ?>">
                                                        <i class="fa-solid fa-hourglass-half"></i>
                                                        <span class="timer-display">Calculating...</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-icon view"
                                                    onclick="openViewModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)"
                                                    title="View Details">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                                <?php if ($booking['status'] == 'Complete' && !empty($booking['attached_files'])): ?>
                                                    <?php if (count($booking['attached_files']) == 1): ?>
                                                        <a href="assets/uploads/reports/<?php echo $booking['attached_files'][0]['file_name']; ?>"
                                                            download target="_blank" class="btn-icon edit" title="Download Report"
                                                            style="background-color: #10b981; color: white; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                                            <i class="fa-solid fa-download"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn-icon edit" title="View & Download Reports"
                                                            style="background-color: #10b981; color: white;"
                                                            onclick="openViewModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
                                                            <i class="fa-solid fa-download"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($totalPages) && $totalPages > 0): ?>
                        <div class="pagination-container">
                            <div class="rows-per-page">
                                <form method="GET" action="" id="limitForm">
                                    <label>Rows per page:</label>
                                    <select name="limit" onchange="document.getElementById('limitForm').submit()">
                                        <option value="5" <?php if ($limit == 5)
                                            echo 'selected'; ?>>5</option>
                                        <option value="10" <?php if ($limit == 10)
                                            echo 'selected'; ?>>10</option>
                                        <option value="25" <?php if ($limit == 25)
                                            echo 'selected'; ?>>25</option>
                                        <option value="50" <?php if ($limit == 50)
                                            echo 'selected'; ?>>50</option>
                                    </select>
                                </form>
                            </div>
                            <div class="pagination-links">
                                <?php
                                $queryString = "&limit=" . $limit;
                                if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?><?php echo $queryString; ?>" class="page-link"><i
                                            class="fa-solid fa-chevron-left"></i></a>
                                <?php else: ?>
                                    <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?><?php echo $queryString; ?>" class="page-link <?php if ($i == $page)
                                              echo 'active'; ?>"><?php echo $i; ?></a>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?><?php echo $queryString; ?>" class="page-link"><i
                                            class="fa-solid fa-chevron-right"></i></a>
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
                        <div id="view_rejection_reason"
                            style="font-size: 14px; color: #ef4444; line-height: 1.6; background:#fff1f2; padding:12px 15px; border-radius:8px; border:1px solid #fecdd3;">
                        </div>
                    </div>

                    <div class="view-section full-width" id="view_estimated_section" style="display:none;">
                        <h4><i class="fa-solid fa-clock" style="color:#f59e0b;"></i> Estimated Completion Time</h4>
                        <div id="view_estimated_time"
                            style="font-size: 14px; color: #92400e; line-height: 1.6; background:#fffbeb; padding:12px 15px; border-radius:8px; border:1px solid #fde68a; font-weight:600;">
                        </div>
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

    <script>
        function formatDateTime(dtStr) {
            if (!dtStr) return 'N/A';
            const dt = new Date(dtStr.replace(' ', 'T'));
            if (isNaN(dt.getTime())) return dtStr;
            return dt.toLocaleString('en-US', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
        }

        function openViewModal(booking) {
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
            let statusLabel = 'Updated At:';
            let statusDate = null;

            if (booking.status === 'Pending') {
                displayStatus = 'Pending';
                statusDate = booking.created_at;
            } else if (booking.status === 'Accept') {
                displayStatus = 'In Progress';
                statusDate = booking.accepted_at;
            } else if (booking.status === 'Complete') {
                displayStatus = 'Complete';
                statusDate = booking.completed_at;
            } else if (booking.status === 'Cancel') {
                displayStatus = 'Rejected';
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
                let p = booking.sample_photo;
                let pUrl = p.startsWith('data:') ? p : ((p.startsWith('sample_')) ? 'assets/uploads/samples/' + p : 'assets/uploads/' + p);
                photoImg.src = pUrl;
                photoImg.onerror = function () {
                    if (!this.dataset.retried && !this.src.startsWith('data:')) {
                        this.dataset.retried = 1;
                        this.src = (this.src.indexOf('/samples/') !== -1) ? 'assets/uploads/' + booking.sample_photo : 'assets/uploads/samples/' + booking.sample_photo;
                    } else {
                        this.src = 'assets/images/plastic_resin/PET Bottles.webp';
                    }
                };
                document.getElementById('view_photo_section').style.display = 'block';
            } else {
                photoImg.src = 'assets/images/plastic_resin/PET Bottles.webp';
                document.getElementById('view_photo_section').style.display = 'block';
            }

            // Handle Attached Files — only show for Complete status
            const filesList = document.getElementById('view_files_list');
            filesList.innerHTML = '';
            if (booking.status === 'Complete' && booking.attached_files && booking.attached_files.length > 0) {
                document.getElementById('view_files_section').style.display = 'block';
                booking.attached_files.forEach(file => {
                    const ext = file.file_name.split('.').pop().toLowerCase();
                    let icon = 'fa-file';
                    if (ext === 'pdf') icon = 'fa-file-pdf';
                    else if (['doc', 'docx'].includes(ext)) icon = 'fa-file-word';
                    else if (['xls', 'xlsx', 'csv'].includes(ext)) icon = 'fa-file-excel';
                    else if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext)) icon = 'fa-file-image';
                    else if (['zip', 'rar', '7z'].includes(ext)) icon = 'fa-file-zipper';

                    const fileItem = document.createElement('div');
                    fileItem.className = 'view-file-item';
                    fileItem.innerHTML = `
                <a href="assets/uploads/reports/${file.file_name}" target="_blank" class="view-file-link">
                    <i class="fa-solid ${icon}"></i>
                    <span>${file.file_name}</span>
                </a>
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
                    const formatted = dt.toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    document.getElementById('view_estimated_time').innerText = formatted;
                    estTimeSection.style.display = 'block';
                } else if (estTimeSection) {
                    estTimeSection.style.display = 'none';
                }
            }

            document.getElementById('viewModal').style.display = "block";
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = "none";
        }

        // Render client notifications
        const dbNotifications = <?php echo json_encode($notifications); ?>;

        function renderNotifications() {
            const list = document.getElementById('notificationList');
            const badge = document.getElementById('notificationBadge');

            // Clear list
            list.innerHTML = '';

            // Filter unread notifications
            const unread = dbNotifications.filter(n => n.is_read == 0);

            const sidebarBadge = document.getElementById('sidebarNotificationBadge');
            if (sidebarBadge) {
                if (unread.length > 0) {
                    sidebarBadge.innerText = unread.length;
                    sidebarBadge.style.display = 'inline-block';
                } else {
                    sidebarBadge.style.display = 'none';
                }
            }

            if (unread.length > 0) {
                badge.innerText = unread.length;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }

            if (dbNotifications.length === 0) {
                list.innerHTML = '<div class="notification-empty">No notifications</div>';
                return;
            }

            dbNotifications.forEach(n => {
                const item = document.createElement('div');
                item.className = 'notification-item' + (n.is_read == 0 ? ' unread' : '');

                // Format time
                const created = new Date(n.created_at.replace(' ', 'T'));
                const timeStr = created.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });

                item.innerHTML = `
            <span class="notification-item-text"><strong>${n.title}</strong>: ${n.message}</span>
            <span class="notification-item-time">${timeStr}</span>
        `;

                // When notification is clicked
                item.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Mark as read in database via fetch
                    if (n.is_read == 0) {
                        const formData = new FormData();
                        formData.append('action', 'read_one');
                        formData.append('id', n.id);

                        fetch('mark_notifications_read.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    n.is_read = 1;
                                    renderNotifications();
                                }
                            });
                    }

                    // Open booking view modal
                    if (n.booking_data) {
                        openViewModal(n.booking_data);
                    }

                    document.getElementById('notificationDropdown').classList.remove('show');
                });

                list.appendChild(item);
            });
        }

        // Toggle dropdown
        document.getElementById('notificationBellBtn').addEventListener('click', function (e) {
            e.stopPropagation();
            document.getElementById('notificationDropdown').classList.toggle('show');
        });

        // Clear all / Mark all read
        document.getElementById('clearNotificationsBtn').addEventListener('click', function (e) {
            e.stopPropagation();

            const formData = new FormData();
            formData.append('action', 'read_all');

            fetch('mark_notifications_read.php', {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        dbNotifications.forEach(n => n.is_read = 1);
                        renderNotifications();
                    }
                });

            document.getElementById('notificationDropdown').classList.remove('show');
        });

        // Close when clicking outside dropdown
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('notificationDropdown');
            const container = document.getElementById('notificationContainer');
            if (dropdown && container && !container.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Run rendering on load
        renderNotifications();

        // Countdown Timer Logic
        function updateTimers() {
            const timers = document.querySelectorAll('.estimated-timer');
            timers.forEach(timer => {
                const endTime = new Date(timer.dataset.endtime).getTime();
                const now = new Date().getTime();
                const distance = endTime - now;

                const display = timer.querySelector('.timer-display');

                if (distance < 0 || isNaN(distance)) {
                    display.innerHTML = "Processing...";
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
        window.onclick = function (event) {
            const viewModal = document.getElementById('viewModal');
            if (event.target == viewModal) closeViewModal();
        }
    </script>

</body>

</html>