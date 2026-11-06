<?php
include '../includes/client_auth_check.php';
include '../includes/db_connect.php';

// Fetch unread notifications count for sidebar badge
$sidebar_unread_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmtUnread = $conn->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = :uid AND is_read = 0");
        $stmtUnread->execute(['uid' => $_SESSION['user_id']]);
        $sidebar_unread_count = (int)$stmtUnread->fetchColumn();
    } catch(PDOException $e) {}
}

$user_email = $_SESSION['username'];
$fullname = $_SESSION['fullname'];

// Fetch COMPLETED bookings with files for THIS client
try {
    // Only fetch bookings that are COMPLETE and have files associated with them
    $stmt = $conn->prepare("
        SELECT b.* 
        FROM test_bookings b
        WHERE b.email = :email 
        AND b.status = 'Complete'
        AND EXISTS (SELECT 1 FROM booking_files f WHERE f.booking_id = b.id)
        ORDER BY b.created_at DESC
    ");
    $stmt->execute(['email' => $user_email]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch attached files for each booking
    foreach ($bookings as &$booking) {
        $stmtFiles = $conn->prepare("SELECT * FROM booking_files WHERE booking_id = :id");
        $stmtFiles->execute(['id' => $booking['id']]);
        $booking['attached_files'] = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($booking);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - AKC</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .client-sidebar { background-color: #1e293b; }
        .sidebar-logo-text { color: white; font-weight: 700; font-size: 20px; }
        .client-badge { background: #3b82f6; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-left: 5px; }
        .sidebar-badge { background-color: #ef4444; color: white; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 2px 6px; margin-left: auto; display: inline-block; }
        .report-card { background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; transition: transform 0.2s; }
        .report-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); border-color: #e2e8f0; }
        .report-icon { width: 50px; height: 50px; background: #fef2f2; color: #ef4444; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .report-info { flex: 1; margin-left: 20px; }
        .report-title { display: block; font-weight: 700; color: #1e293b; font-size: 16px; margin-bottom: 4px; }
        .report-meta { font-size: 13px; color: #64748b; font-weight: 500; }
        .btn-download-all { background: #10b981; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .btn-download-all:hover { background: #059669; transform: scale(1.02); }
        .empty-reports { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1; }
        .empty-reports i { font-size: 50px; color: #cbd5e1; margin-bottom: 20px; }
        .empty-reports h3 { color: #1e293b; margin-bottom: 10px; }
        .empty-reports p { color: #64748b; }
        
        /* Mobile Layout */
        @media (max-width: 600px) {
            .report-card {
                flex-wrap: wrap;
                gap: 15px;
            }
            .report-info {
                margin-left: 15px;
            }
            .report-actions {
                width: 100%;
                margin-top: 5px;
            }
            .report-actions .btn-download-all {
                width: 100%;
                justify-content: center;
            }
            .report-actions > div {
                flex-direction: column;
                width: 100%;
            }
            .section-header h3 {
                font-size: 22px !important;
            }
        }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <!-- Left Sidebar -->
    <?php include '../includes/client_sidebar.php'; ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <!-- Top Bar -->
        <?php 
        $page_title = 'Reports / <span class="blue-text">My Test Reports</span>';
        include '../includes/client_topbar.php'; 
        ?>

        <!-- Content Body -->
        <div class="admin-content">
            <div class="section-header" style="margin-bottom: 25px;">
                <h3 style="font-weight: 700; color: #1e293b;">Downloadable Reports</h3>
                <p style="color: #64748b; font-size: 14px;">Find and download all your finalized laboratory test results below.</p>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="empty-reports">
                    <i class="fa-solid fa-file-circle-xmark"></i>
                    <h3>No Reports Available Yet</h3>
                    <p>Once your tests are completed and reports are uploaded, they will appear here.</p>
                    <a href="dashboard.php" class="btn-primary" style="display: inline-block; margin-top: 20px; text-decoration: none;">Check Booking Status</a>
                </div>
            <?php else: ?>
                <div class="reports-list">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="report-card">
                            <div class="report-icon">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div class="report-info">
                                <span class="report-title"><?php echo $booking['id']; ?> - <?php echo htmlspecialchars($booking['sample_type']); ?></span>
                                <div class="report-meta">
                                    <span><i class="fa-solid fa-calendar-day"></i> Completed: <?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
                                    <span style="margin-left: 15px;"><i class="fa-solid fa-paperclip"></i> <?php echo count($booking['attached_files']); ?> File(s)</span>
                                </div>
                            </div>
                            <div class="report-actions">
                                <?php if (count($booking['attached_files']) == 1): ?>
                                    <a href="../assets/uploads/reports/<?php echo $booking['attached_files'][0]['file_name']; ?>" download target="_blank" class="btn-download-all">
                                        <i class="fa-solid fa-download"></i> Download Report
                                    </a>
                                <?php else: ?>
                                    <div style="display: flex; gap: 10px;">
                                        <?php foreach ($booking['attached_files'] as $index => $file): ?>
                                            <a href="../assets/uploads/reports/<?php echo $file['file_name']; ?>" download target="_blank" class="btn-download-all" title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                                <i class="fa-solid fa-download"></i> File <?php echo $index + 1; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
