<?php
include '../includes/client_auth_check.php';
include '../includes/db_connect.php';

// Fetch dynamic test types for the dropdown
$stmtTypes = $conn->query("SELECT name FROM test_types ORDER BY name ASC");
$test_types_list = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

// Fetch current user details
$user_id = $_SESSION['user_id'];
$user_phone = '';
$user_company = '';
try {
    $stmtUser = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmtUser->execute(['id' => $user_id]);
    $currentUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if ($currentUser) {
        $user_phone = $currentUser['phone'] ?? '';
    }

    // Extract company from last booking if available
    $stmtLastCompany = $conn->prepare("SELECT company FROM test_bookings WHERE email = :email ORDER BY id DESC LIMIT 1");
    $stmtLastCompany->execute(['email' => $_SESSION['username']]);
    $user_company = $stmtLastCompany->fetchColumn() ?: '';
} catch (PDOException $e) {}

// Handle New Booking Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_booking'])) {
    try {
        $sample_type = trim($_POST['sample_type'] ?? '');
        $num_samples = trim($_POST['num_samples'] ?? '');
        $sample_desc = trim($_POST['sample_desc'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');

        if (empty($fullname)) {
            throw new Exception("Full Name is required.");
        }
        if (empty($email)) {
            throw new Exception("Email Address is required.");
        }
        if (empty($phone)) {
            throw new Exception("Phone Number is required.");
        }
        if (empty($company)) {
            throw new Exception("Company Name is required.");
        }
        if (empty($sample_type)) {
            throw new Exception("Test Type is required.");
        }
        if (empty($num_samples)) {
            throw new Exception("Number of Samples is required.");
        }
        if (empty($sample_desc)) {
            throw new Exception("Sample Description is required.");
        }
        if (!isset($_FILES['sample_photo']) || $_FILES['sample_photo']['error'] !== 0) {
            throw new Exception("Sample photograph is required.");
        }
        if (empty($requirements)) {
            throw new Exception("Special Requirements are required.");
        }
        
        // Handle File Upload
        $sample_photo = "";
        if (isset($_FILES['sample_photo']) && $_FILES['sample_photo']['error'] == 0) {
            $target_dir = __DIR__ . "/../assets/uploads/";
            $file_extension = strtolower(pathinfo($_FILES["sample_photo"]["name"], PATHINFO_EXTENSION));
            
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'];
            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception("Invalid file type. Only images (JPG, JPEG, PNG, SVG, etc.) are allowed.");
            }

            if ($_FILES["sample_photo"]["size"] > 3 * 1024 * 1024) {
                throw new Exception("File is too large. Maximum allowed size is 3MB.");
            }

            $new_filename = uniqid() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["sample_photo"]["tmp_name"], $target_file)) {
                $sample_photo = $new_filename;
            } else {
                throw new Exception("Failed to save the uploaded photo. Please try again.");
            }
        } else {
            throw new Exception("Sample photograph is required. Please upload a photo of your sample.");
        }

        $sql = "INSERT INTO test_bookings (sample_type, num_samples, sample_desc, sample_photo, fullname, company, email, phone, requirements) 
                VALUES (:sample_type, :num_samples, :sample_desc, :sample_photo, :fullname, :company, :email, :phone, :requirements)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':sample_type', $sample_type);
        $stmt->bindParam(':num_samples', $num_samples);
        $stmt->bindParam(':sample_desc', $sample_desc);
        $stmt->bindParam(':sample_photo', $sample_photo);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':requirements', $requirements);
        
        $stmt->execute();
        $booking_id = $conn->lastInsertId();

        // --- Insert Notifications ---
        $stmtInsertClient = $conn->prepare("INSERT INTO user_notifications (user_id, booking_id, title, message) VALUES (:uid, :bid, 'Test Booked Successfully', :msg)");
        $stmtInsertClient->execute([
            'uid' => $user_id,
            'bid' => $booking_id,
            'msg' => "Your test request (#$booking_id) for '$sample_type' has been successfully submitted."
        ]);

        $admins = $conn->query("SELECT id FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($admins as $admin_id) {
            $stmtInsertAdmin = $conn->prepare("INSERT INTO user_notifications (user_id, booking_id, title, message) VALUES (:uid, :bid, 'New Test Request', :msg)");
            $stmtInsertAdmin->execute([
                'uid' => $admin_id,
                'bid' => $booking_id,
                'msg' => "A new test request (#$booking_id) for '$sample_type' has been created by $fullname."
            ]);
        }

        // Build base URL dynamically to prevent any 404 issue (handles both root and subdirectories)
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $script_dir = ($script_dir === '/' || $script_dir === '\\') ? '' : $script_dir;
        $base_url = $protocol . $host . $script_dir;

        // Trigger booking confirmation email asynchronously in the background using PHP_BINARY
        $php_path = PHP_BINARY;
        if (strpos(strtolower(PHP_OS), 'win') !== false) {
            // Windows background command
            $cmd = "start /B " . escapeshellarg($php_path) . " " . escapeshellarg(__DIR__ . '/../process_email_async.php') . " " . escapeshellarg($booking_id) . " EXISTING " . escapeshellarg($base_url);
            pclose(popen($cmd, 'r'));
        } else {
            // Linux/macOS background command
            $cmd = escapeshellarg($php_path) . " " . escapeshellarg(__DIR__ . '/../process_email_async.php') . " " . escapeshellarg($booking_id) . " EXISTING " . escapeshellarg($base_url) . " > /dev/null 2>&1 &";
            shell_exec($cmd);
        }

        $_SESSION['booking_success'] = "Your test has been booked successfully! A confirmation email is on its way.";
        header("Location: dashboard.php");
        exit();
    } catch(Exception $e) {
        $_SESSION['booking_error'] = $e->getMessage();
        header("Location: dashboard.php");
        exit();
    }
}

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

// Fetch summary stats for THIS client
try {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE email = :email");
    $stmtTotal->execute(['email' => $user_email]);
    $totalBookings = $stmtTotal->fetchColumn();

    $stmtComp = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE email = :email AND status = 'Complete'");
    $stmtComp->execute(['email' => $user_email]);
    $completedBookings = $stmtComp->fetchColumn();

    $stmtPend = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE email = :email AND status = 'Pending'");
    $stmtPend->execute(['email' => $user_email]);
    $pendingBookings = $stmtPend->fetchColumn();

    $stmtInProg = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE email = :email AND status = 'Accept'");
    $stmtInProg->execute(['email' => $user_email]);
    $inProgressBookings = $stmtInProg->fetchColumn();

    $stmtRej = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE email = :email AND status = 'Cancel'");
    $stmtRej->execute(['email' => $user_email]);
    $rejectedBookings = $stmtRej->fetchColumn();

    $completionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100) : 0;

    // Pagination parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch total records for pagination
    $stmtCount = $conn->prepare("SELECT COUNT(*) FROM test_bookings WHERE email = :email");
    $stmtCount->execute(['email' => $user_email]);
    $totalRecords = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // Fetch client's bookings
    $stmt = $conn->prepare("SELECT * FROM test_bookings WHERE email = :email ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
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

    // Notifications are handled globally by includes/client_notifications_sync.php
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - AKC</title>
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
        .sidebar-logo-text { color: white; font-weight: 700; font-size: 20px; }
        .client-badge { background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-left: 5px; }
        .client-stats .stats-row { grid-template-columns: repeat(6, 1fr); }
        
        /* Mobile Layout Overrides */
        @media (max-width: 991px) {
            .client-stats .stats-row { grid-template-columns: repeat(2, 1fr); gap: 15px; }
            .card-header-split { padding: 15px; display: flex; flex-direction: column; gap: 15px; align-items: flex-start; }
            .card-header-filters { width: 100%; display: flex; flex-direction: column; }
            .card-header-filters .btn-export { margin-left: 0 !important; width: 100%; justify-content: center; }
        }
        
        @media (max-width: 600px) {
            .client-stats .stats-row { 
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow-y: auto; }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 0; border-radius: 12px; width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: slideDown 0.3s ease; }
        .modal-lg { width: 800px; }
        @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 15px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 25px; }
        .modal-footer { padding: 15px 25px; border-top: 1px solid #eee; text-align: right; }
        .close { font-size: 28px; font-weight: bold; cursor: pointer; color: #64748b; transition: color 0.2s; }
        .close:hover { color: #1e293b; }
        .view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .view-section { display: flex; flex-direction: column; }
        .view-section h4 { font-size: 13px; text-transform: uppercase; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; display: flex; align-items: center; gap: 10px; font-weight: 700; letter-spacing: 0.5px; }
        .view-section h4 i { color: #3b82f6; width: 20px; text-align: center; }
        .view-row { display: flex; margin-bottom: 12px; font-size: 14px; align-items: flex-start; }
        .view-label { width: 130px; color: #64748b; font-weight: 600; flex-shrink: 0; }
        .view-value { color: #1e293b; font-weight: 500; }
        .info-box { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; min-height: 50px; font-size: 14px; line-height: 1.6; color: #475569; }
        .full-width { grid-column: span 2; }
        .view-full-img { width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px; margin-top: 10px; border: 1px solid #e2e8f0; background: #f8fafc; }
        .view-file-item { margin-bottom: 10px; }
        .view-file-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #1e293b; transition: all 0.2s; font-size: 14px; }
        .view-file-link:hover { border-color: #3b82f6; background: #f0f7ff; color: #3b82f6; }

        /* Timer Styles */
        .status-wrapper { display: flex; flex-direction: column; gap: 5px; align-items: flex-start; }
        .estimated-timer { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #ef4444; font-weight: 700; background: #fff1f2; padding: 3px 8px; border-radius: 4px; border: 1px solid #fecdd3; }
        .estimated-timer i { color: #ef4444; font-size: 10px; }

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
        .sidebar-badge { background-color: #ef4444; color: white; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 2px 6px; margin-left: auto; display: inline-block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <!-- Left Sidebar -->
    <?php include '../includes/client_sidebar.php'; ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <!-- Top Bar -->
        <?php include '../includes/client_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <?php if (isset($_SESSION['booking_success'])): ?>
                <div class="alert-success-msg" style="background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; border: 1px solid #a7f3d0; animation: fadeIn 0.3s ease;">
                    <span><i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($_SESSION['booking_success']); ?></span>
                    <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 16px;"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['booking_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['booking_error'])): ?>
                <div class="alert-danger-msg" style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; border: 1px solid #fecaca; animation: fadeIn 0.3s ease;">
                    <span><i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($_SESSION['booking_error']); ?></span>
                    <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; cursor: pointer; font-size: 16px;"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['booking_error']); ?>
            <?php endif; ?>

            <!-- Stats Row -->
            <div class="client-stats">
                <div class="stats-row">
                    <div class="stat-card border-blue">
                        <div class="stat-card-header">
                            <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                            <span class="stat-badge badge-neutral">All</span>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value" id="statTotalBookings"><?php echo $totalBookings; ?></span>
                            <span class="stat-label">Total Requests</span>
                        </div>
                    </div>
                    <div class="stat-card border-green">
                        <div class="stat-card-header">
                            <div class="stat-icon green"><i class="fa-solid fa-check-double"></i></div>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value" id="statCompletedBookings"><?php echo $completedBookings; ?></span>
                            <span class="stat-label">Completed</span>
                        </div>
                    </div>
                    <div class="stat-card border-orange">
                        <div class="stat-card-header">
                            <div class="stat-icon orange"><i class="fa-solid fa-clock"></i></div>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value" id="statPendingBookings"><?php echo $pendingBookings; ?></span>
                            <span class="stat-label">Pending</span>
                        </div>
                    </div>
                    <div class="stat-card border-amber">
                        <div class="stat-card-header">
                            <div class="stat-icon amber"><i class="fa-solid fa-spinner"></i></div>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value" id="statInProgressBookings"><?php echo $inProgressBookings; ?></span>
                            <span class="stat-label">In Progress</span>
                        </div>
                    </div>
                    <div class="stat-card border-red">
                        <div class="stat-card-header">
                            <div class="stat-icon red"><i class="fa-solid fa-xmark"></i></div>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value" id="statRejectedBookings"><?php echo $rejectedBookings; ?></span>
                            <span class="stat-label">Rejected</span>
                        </div>
                    </div>
                    <div class="stat-card border-purple">
                        <div class="stat-card-header">
                            <div class="stat-icon purple"><i class="fa-solid fa-chart-line"></i></div>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value" id="statCompletionRate"><?php echo $completionRate; ?>%</span>
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
                        <button onclick="openBookingModal()" class="btn-export" style="background-color: #3b82f6; border: none; color: white; margin-left: auto; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-plus"></i> New Booking
                        </button>
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
                        <tbody id="clientBookingsTableBody">
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td class="id-cell"><?php echo $booking['id']; ?></td>
                                <td>
                                    <div class="sample-info">
                                        <span class="sample-type"><?php echo htmlspecialchars($booking['sample_type']); ?></span>
                                        <span class="sample-count"><?php echo $booking['num_samples']; ?> sample(s)</span>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($booking['sample_photo'])): ?>
                                        <img src="../assets/uploads/<?php echo $booking['sample_photo']; ?>" alt="Sample" class="table-img">
                                    <?php else: ?>
                                        <div class="no-photo-placeholder"><i class="fa-solid fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="date-cell">
                                        <span class="date-text" style="color: #64748b; font-weight: 500; font-size: 13px; display: flex; flex-direction: column; gap: 4px; line-height: 1.3;">
                                            <span><span style="font-weight: 600; color: #334155;">Booked:</span> <?php echo date('M d, Y', strtotime($booking['created_at'])); ?></span>
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
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-wrapper">
                                        <span class="status-badge <?php echo strtolower($booking['status']); ?>">
                                            <?php 
                                                if($booking['status'] == 'Accept') echo 'IN PROGRESS';
                                                else if($booking['status'] == 'Cancel') echo 'REJECTED';
                                                else echo strtoupper($booking['status']); 
                                            ?>
                                        </span>
                                        <?php if ($booking['status'] == 'Accept' && !empty($booking['estimated_time'])): ?>
                                            <div class="estimated-timer" data-endtime="<?php echo str_replace(' ', 'T', $booking['estimated_time']); ?>">
                                                <i class="fa-solid fa-hourglass-half"></i>
                                                <span class="timer-display">Calculating...</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon view" onclick="openViewModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <?php if ($booking['status'] == 'Complete' && !empty($booking['attached_files'])): ?>
                                            <?php if (count($booking['attached_files']) == 1): ?>
                                                <a href="../assets/uploads/reports/<?php echo $booking['attached_files'][0]['file_name']; ?>" download target="_blank" class="btn-icon edit" title="Download Report" style="background-color: #10b981; color: white; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn-icon edit" title="View & Download Reports" style="background-color: #10b981; color: white;" onclick="openViewModal(<?php echo htmlspecialchars(json_encode($booking)); ?>)">
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
                                <option value="5" <?php if($limit == 5) echo 'selected'; ?>>5</option>
                                <option value="10" <?php if($limit == 10) echo 'selected'; ?>>10</option>
                                <option value="25" <?php if($limit == 25) echo 'selected'; ?>>25</option>
                                <option value="50" <?php if($limit == 50) echo 'selected'; ?>>50</option>
                            </select>
                        </form>
                    </div>
                    <div class="pagination-links">
                        <?php 
                        $queryString = "&limit=".$limit;
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

<!-- New Booking Modal -->
<div id="bookingModal" class="modal" style="display: none;">
    <div class="modal-content modal-lg" style="max-width: 700px; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15); animation: fadeIn 0.3s ease;">
        <div class="modal-header" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); color: white; display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; border-bottom: 1px solid #e2e8f0;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; display: flex; align-items: center; gap: 10px; color: white;"><i class="fa-solid fa-circle-plus"></i> Book a New Laboratory Test</h3>
            <span class="close" onclick="closeBookingModal()" style="color: white; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1;">&times;</span>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
                
                <!-- Section 1: Customer Details (Autofilled) -->
                <h4 style="margin-top: 0; margin-bottom: 15px; color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-user-tie"></i> Customer Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Full Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Email Address <span style="color: #ef4444;">*</span></label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required readonly style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #f1f5f9; cursor: not-allowed; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Phone Number <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user_phone); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Company Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="company" value="<?php echo htmlspecialchars($user_company); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;">
                    </div>
                </div>

                <!-- Section 2: Test & Sample Details -->
                <h4 style="margin-bottom: 15px; color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-flask"></i> Test & Sample Details</h4>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Select Test Type <span style="color: #ef4444;">*</span></label>
                        <select name="sample_type" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; background-color: white;">
                            <?php foreach ($test_types_list as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['name']); ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">No. of Samples <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="num_samples" value="1" min="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Sample Description <span style="color: #ef4444;">*</span></label>
                    <textarea name="sample_desc" rows="3" required placeholder="Describe the sample, dimensions, material, etc." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Special Requirements <span style="color: #ef4444;">*</span></label>
                    <textarea name="requirements" rows="2" required placeholder="Any specific standards, methods, or requirements..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Upload Sample Photograph <span style="color: #ef4444;">*</span></label>
                    <input type="file" id="modal-file-input" name="sample_photo" accept="image/*" required style="width: 100%; padding: 8px; border: 1px dashed #cbd5e1; border-radius: 6px; font-family: inherit; background-color: #fafafa;">
                    <div id="modal-file-error" style="color: #ef4444; font-size: 11px; margin-top: 5px; font-weight: 600; display: none;"></div>
                    <small style="color: #64748b; display: block; margin-top: 5px;">Max file size: 3MB. Allowed formats: JPG, PNG, WEBP.</small>
                </div>

            </div>
            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; padding: 15px 25px;">
                <button type="button" class="btn-secondary" onclick="closeBookingModal()" style="padding: 10px 20px; font-weight: 600; cursor: pointer; border: 1px solid #cbd5e1; background-color: white; border-radius: 6px;">Cancel</button>
                <button type="submit" name="submit_booking" class="btn-primary" style="padding: 10px 20px; font-weight: 600; background-color: #2563eb; border: 1px solid #2563eb; color: white; border-radius: 6px; cursor: pointer;">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
function formatDateTime(dtStr) {
    if (!dtStr) return 'N/A';
    const dt = new Date(dtStr.replace(' ', 'T'));
    if (isNaN(dt.getTime())) return dtStr;
    return dt.toLocaleString('en-US', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit', hour12: true });
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
            const ext = file.file_name.split('.').pop().toLowerCase();
            let icon = 'fa-file';
            if (ext === 'pdf') icon = 'fa-file-pdf';
            else if (['doc','docx'].includes(ext)) icon = 'fa-file-word';
            else if (['xls','xlsx','csv'].includes(ext)) icon = 'fa-file-excel';
            else if (['png','jpg','jpeg','gif','webp','svg'].includes(ext)) icon = 'fa-file-image';
            else if (['zip','rar','7z'].includes(ext)) icon = 'fa-file-zipper';
            
            const fileItem = document.createElement('div');
            fileItem.className = 'view-file-item';
            fileItem.innerHTML = `
                <a href="../assets/uploads/reports/${file.file_name}" target="_blank" class="view-file-link">
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
            const formatted = dt.toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
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

// Render client notifications are managed globally by includes/client_notifications_sync.php

// Countdown Timer Logic
function updateTimers() {
    const timers = document.querySelectorAll('.estimated-timer');
    timers.forEach(timer => {
        const endTime = new Date(timer.dataset.endtime).getTime();
        const now = new Date().getTime();
        const distance = endTime - now;

        const display = timer.querySelector('.timer-display');

        if (distance < 0 || isNaN(distance)) {
            display.innerHTML = "Overdue: Reaching out shortly";
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

function openBookingModal() {
    document.getElementById('bookingModal').style.display = "block";
}

function closeBookingModal() {
    // Reset file errors and inputs on modal close
    var fileInput = document.getElementById('modal-file-input');
    var fileError = document.getElementById('modal-file-error');
    if (fileInput) fileInput.value = '';
    if (fileInput) fileInput.style.borderColor = '';
    if (fileError) {
        fileError.style.display = 'none';
        fileError.innerText = '';
    }
    document.getElementById('bookingModal').style.display = "none";
}

// Modal file input validation
document.getElementById('modal-file-input').addEventListener('change', function(e) {
    var fileError = document.getElementById('modal-file-error');
    if (e.target.files.length > 0) {
        var file = e.target.files[0];
        if (file.size > 3 * 1024 * 1024) {
            fileError.innerText = 'File size is too large (' + (file.size / (1024 * 1024)).toFixed(2) + 'MB). Maximum allowed size is 3MB.';
            fileError.style.display = 'block';
            e.target.value = ''; // Clear value so it is invalid for submission
            e.target.style.borderColor = '#ef4444';
        } else {
            fileError.style.display = 'none';
            e.target.style.borderColor = '';
        }
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewModal');
    const bookingModal = document.getElementById('bookingModal');
    if (event.target == viewModal) closeViewModal();
    if (event.target == bookingModal) closeBookingModal();
}
</script>

</body>
</html>
