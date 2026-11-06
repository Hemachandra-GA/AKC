<?php
// super_admin/monthly_reports.php
include '../includes/super_admin_auth_check.php';
include '../includes/db_connect.php';
include '../includes/mail_sender.php';

// Fetch super admin recipients
$superAdmins = $conn->query("SELECT id, fullname, email FROM users WHERE role = 'super_admin' AND is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

// Handle manual dispatch or auto-cron trigger
$message = '';
$messageType = 'success';

// Helper function to build monthly summary payload
function akc_get_monthly_stats($conn, $selectedMonth = null) {
    if (!$selectedMonth) {
        $selectedMonth = date('Y-m');
    }

    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_tests,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_tests,
            SUM(CASE WHEN status = 'Accept' THEN 1 ELSE 0 END) as processing_tests,
            SUM(CASE WHEN status = 'Complete' THEN 1 ELSE 0 END) as complete_tests,
            SUM(CASE WHEN status = 'Cancel' THEN 1 ELSE 0 END) as rejected_tests
        FROM test_bookings
        WHERE SUBSTR(created_at, 1, 7) = :ym
    ");
    $stmt->execute(['ym' => $selectedMonth]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'total_tests' => 0,
        'pending_tests' => 0,
        'processing_tests' => 0,
        'complete_tests' => 0,
        'rejected_tests' => 0
    ];

    // Detailed test breakdown by sample type
    $stmtTypes = $conn->prepare("
        SELECT sample_type, COUNT(*) as cnt,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_cnt,
            SUM(CASE WHEN status = 'Accept' THEN 1 ELSE 0 END) as processing_cnt,
            SUM(CASE WHEN status = 'Complete' THEN 1 ELSE 0 END) as complete_cnt,
            SUM(CASE WHEN status = 'Cancel' THEN 1 ELSE 0 END) as rejected_cnt
        FROM test_bookings
        WHERE SUBSTR(created_at, 1, 7) = :ym
        GROUP BY sample_type
        ORDER BY cnt DESC
    ");
    $stmtTypes->execute(['ym' => $selectedMonth]);
    $sampleStats = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

    return [
        'month' => $selectedMonth,
        'month_label' => date('F Y', strtotime($selectedMonth . '-01')),
        'stats' => $stats,
        'sample_stats' => $sampleStats
    ];
}

function akc_build_monthly_report_html($data) {
    $monthLabel = $data['month_label'];
    $s = $data['stats'];
    $samples = $data['sample_stats'];
    $currentDate = date('d M Y, h:i A');

    $rowsHtml = '';
    if (!empty($samples)) {
        foreach ($samples as $row) {
            $type = htmlspecialchars($row['sample_type'] ?: 'Unspecified');
            $rowsHtml .= "
            <tr>
                <td style='padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; color: #1e293b;'>{$type}</td>
                <td style='padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 13px; text-align: center; color: #0284c7; font-weight: bold;'>{$row['cnt']}</td>
                <td style='padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 13px; text-align: center; color: #ea580c; font-weight: 600;'>{$row['pending_cnt']}</td>
                <td style='padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 13px; text-align: center; color: #2563eb; font-weight: 600;'>{$row['processing_cnt']}</td>
                <td style='padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 13px; text-align: center; color: #16a34a; font-weight: 600;'>{$row['complete_cnt']}</td>
                <td style='padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 13px; text-align: center; color: #dc2626; font-weight: 600;'>{$row['rejected_cnt']}</td>
            </tr>";
        }
    } else {
        $rowsHtml = "<tr><td colspan='6' style='padding: 16px; text-align: center; color: #64748b; font-size: 13px;'>No test requests recorded for this period.</td></tr>";
    }

    return "
    <div style='font-family: Arial, Helvetica, sans-serif; max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;'>
        <div style='background: linear-gradient(135deg, #0A58CA 0%, #1e3a8a 100%); padding: 25px 30px; color: #ffffff;'>
            <h2 style='margin: 0 0 6px 0; font-size: 22px; font-weight: 700;'>AKC Monthly Operations Digest</h2>
            <p style='margin: 0; font-size: 14px; opacity: 0.9;'>Testing Requests & Status Overview &bull; <strong>{$monthLabel}</strong></p>
        </div>

        <div style='padding: 25px 30px;'>
            <p style='font-size: 14px; color: #334155; margin-top: 0;'>
                Hello Super Admin,<br>
                Here is the official monthly summary of test requests received, processed, completed, and rejected at the AKC Testing Centre for <strong>{$monthLabel}</strong>.
            </p>

            <!-- Status KPI Boxes -->
            <div style='display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin: 25px 0;'>
                <div style='background: #f0f9ff; border: 1.5px solid #bae6fd; border-radius: 8px; padding: 12px 8px; text-align: center;'>
                    <div style='font-size: 11px; font-weight: 700; color: #0369a1; text-transform: uppercase;'>Total Tests</div>
                    <div style='font-size: 22px; font-weight: 800; color: #0284c7; margin-top: 4px;'>{$s['total_tests']}</div>
                </div>
                <div style='background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 8px; padding: 12px 8px; text-align: center;'>
                    <div style='font-size: 11px; font-weight: 700; color: #c2410c; text-transform: uppercase;'>Pending</div>
                    <div style='font-size: 22px; font-weight: 800; color: #ea580c; margin-top: 4px;'>{$s['pending_tests']}</div>
                </div>
                <div style='background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 8px; padding: 12px 8px; text-align: center;'>
                    <div style='font-size: 11px; font-weight: 700; color: #1d4ed8; text-transform: uppercase;'>In Process</div>
                    <div style='font-size: 22px; font-weight: 800; color: #2563eb; margin-top: 4px;'>{$s['processing_tests']}</div>
                </div>
                <div style='background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 8px; padding: 12px 8px; text-align: center;'>
                    <div style='font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase;'>Complete</div>
                    <div style='font-size: 22px; font-weight: 800; color: #16a34a; margin-top: 4px;'>{$s['complete_tests']}</div>
                </div>
                <div style='background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 8px; padding: 12px 8px; text-align: center;'>
                    <div style='font-size: 11px; font-weight: 700; color: #b91c1c; text-transform: uppercase;'>Rejected</div>
                    <div style='font-size: 22px; font-weight: 800; color: #dc2626; margin-top: 4px;'>{$s['rejected_tests']}</div>
                </div>
            </div>

            <!-- Table breakdown -->
            <h3 style='font-size: 15px; color: #0f172a; margin: 25px 0 10px 0;'>Breakdown by Sample / Material</h3>
            <table style='width: 100%; border-collapse: collapse; margin-top: 8px;'>
                <thead>
                    <tr style='background: #f8fafc;'>
                        <th style='padding: 10px 14px; text-align: left; font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;'>Sample Type</th>
                        <th style='padding: 10px 14px; text-align: center; font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;'>Total</th>
                        <th style='padding: 10px 14px; text-align: center; font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;'>Pending</th>
                        <th style='padding: 10px 14px; text-align: center; font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;'>In Process</th>
                        <th style='padding: 10px 14px; text-align: center; font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;'>Completed</th>
                        <th style='padding: 10px 14px; text-align: center; font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;'>Rejected</th>
                    </tr>
                </thead>
                <tbody>
                    {$rowsHtml}
                </tbody>
            </table>

            <div style='margin-top: 30px; padding: 15px; background: #f8fafc; border-radius: 8px; font-size: 12px; color: #64748b; text-align: center;'>
                Report generated automatically on {$currentDate} &bull; AKC Packaging Technology &amp; Research Centre
            </div>
        </div>
    </div>";
}

// Check if user requested to email the report
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_report_email') {
    $targetMonth = $_POST['target_month'] ?? date('Y-m');
    $recipientEmail = trim($_POST['recipient_email'] ?? '');

    if (empty($recipientEmail)) {
        $recipientEmail = $superAdmins[0]['email'] ?? 'mamathasurekha8@gmail.com';
    }

    $reportData = akc_get_monthly_stats($conn, $targetMonth);
    $htmlEmail = akc_build_monthly_report_html($reportData);
    $subject = "AKC Monthly Operations Report - " . $reportData['month_label'];

    $mailResult = akc_send_mail($recipientEmail, 'Super Admin', $subject, $htmlEmail);

    if ($mailResult['sent_external']) {
        $message = "Monthly Report for " . $reportData['month_label'] . " was successfully delivered to " . htmlspecialchars($recipientEmail) . " via SMTP!";
        $messageType = 'success';
    } else {
        $message = "Monthly Report for " . $reportData['month_label'] . " was dispatched and recorded in outgoing email logs (Recipient: " . htmlspecialchars($recipientEmail) . ").";
        $messageType = 'success';
    }
}

// Fetch all distinct months available in bookings for selector
$monthsList = $conn->query("
    SELECT DISTINCT SUBSTR(created_at, 1, 7) as ym 
    FROM test_bookings 
    ORDER BY ym DESC
")->fetchAll(PDO::FETCH_COLUMN);

if (empty($monthsList)) {
    $monthsList = [date('Y-m')];
}

$activeMonth = $_GET['month'] ?? ($monthsList[0] ?? date('Y-m'));
$currentMonthReport = akc_get_monthly_stats($conn, $activeMonth);

// Fetch all-time monthly summary table
$allMonthsSummary = $conn->query("
    SELECT 
        SUBSTR(created_at, 1, 7) as ym,
        COUNT(*) as total_tests,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_tests,
        SUM(CASE WHEN status = 'Accept' THEN 1 ELSE 0 END) as processing_tests,
        SUM(CASE WHEN status = 'Complete' THEN 1 ELSE 0 END) as complete_tests,
        SUM(CASE WHEN status = 'Cancel' THEN 1 ELSE 0 END) as rejected_tests
    FROM test_bookings
    GROUP BY ym
    ORDER BY ym DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Reports - AKC Super Admin</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .report-header-box {
            background: linear-gradient(135deg, #0A58CA 0%, #1e3a8a 100%);
            border-radius: 12px;
            padding: 24px 30px;
            color: #ffffff;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .report-header-box h2 {
            margin: 0 0 5px 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
        }
        .report-header-box p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .status-pill-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        @media (max-width: 992px) {
            .status-pill-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) {
            .status-pill-grid { grid-template-columns: 1fr; }
        }
        .kpi-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            text-align: center;
            transition: transform 0.2s;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
        }
        .kpi-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .kpi-value {
            font-size: 30px;
            font-weight: 800;
        }
        .badge-status-kpi {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .filter-bar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .btn-dispatch {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
            font-size: 13px;
        }
        .btn-dispatch:hover {
            background: #059669;
        }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <?php 
    $current_page = 'monthly_reports';
    $page_title = 'Monthly Operations Reports';
    include '../includes/super_admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include '../includes/super_admin_topbar.php'; ?>

        <div class="admin-content">

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $messageType; ?>" style="padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; <?php echo $messageType === 'success' ? 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;'; ?>">
                    <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                    <span style="font-weight: 600;"><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <!-- Header Card with Email Trigger -->
            <div class="report-header-box">
                <div>
                    <h2><i class="fa-solid fa-file-invoice" style="margin-right: 10px;"></i> Monthly Super Admin Operations Report</h2>
                    <p>Track test counts received, pending, processing (in progress), completed, and rejected.</p>
                </div>

                <!-- Form to Dispatch Email -->
                <form method="POST" style="margin: 0; display: flex; gap: 10px; align-items: center;">
                    <input type="hidden" name="action" value="send_report_email">
                    <input type="hidden" name="target_month" value="<?php echo htmlspecialchars($activeMonth); ?>">
                    <input type="hidden" name="recipient_email" value="<?php echo htmlspecialchars($superAdmins[0]['email'] ?? 'mamathasurekha8@gmail.com'); ?>">
                    <button type="submit" class="btn-dispatch">
                        <i class="fa-solid fa-paper-plane"></i> Send Report to Super Admin Email
                    </button>
                </form>
            </div>

            <!-- Month Selection Bar -->
            <div class="filter-bar">
                <form method="GET" style="display: flex; align-items: center; gap: 12px; margin: 0;">
                    <label style="font-size: 13px; font-weight: 600; color: #475569;"><i class="fa-solid fa-calendar-days" style="color: #2563eb; margin-right: 6px;"></i> Select Reporting Month:</label>
                    <select name="month" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; font-size: 13px; font-weight: 600; color: #1e293b;">
                        <?php foreach($monthsList as $m): ?>
                            <option value="<?php echo $m; ?>" <?php echo ($m === $activeMonth) ? 'selected' : ''; ?>>
                                <?php echo date('F Y', strtotime($m . '-01')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <div style="font-size: 13px; color: #64748b;">
                    <i class="fa-solid fa-envelope" style="color: #3b82f6; margin-right: 5px;"></i> Recipient: <strong><?php echo htmlspecialchars($superAdmins[0]['email'] ?? 'mamathasurekha8@gmail.com'); ?></strong>
                </div>
            </div>

            <!-- 5 KPI Cards for Selected Month -->
            <h3 style="margin-bottom: 15px; font-size: 16px; color: #1e293b;">
                Summary for <strong><?php echo $currentMonthReport['month_label']; ?></strong>
            </h3>
            
            <div class="status-pill-grid">
                <!-- 1. Total Tests Came In -->
                <div class="kpi-card" style="border-top: 4px solid #0284c7;">
                    <div class="kpi-title" style="color: #0369a1;">Total Tests Came</div>
                    <div class="kpi-value" style="color: #0284c7;"><?php echo $currentMonthReport['stats']['total_tests']; ?></div>
                    <small style="color: #64748b; font-size: 11px;">Bookings Submitted</small>
                </div>

                <!-- 2. Pending State -->
                <div class="kpi-card" style="border-top: 4px solid #ea580c;">
                    <div class="kpi-title" style="color: #c2410c;">Pending State</div>
                    <div class="kpi-value" style="color: #ea580c;"><?php echo $currentMonthReport['stats']['pending_tests']; ?></div>
                    <small style="color: #64748b; font-size: 11px;">Awaiting Review</small>
                </div>

                <!-- 3. In Progress / Processing -->
                <div class="kpi-card" style="border-top: 4px solid #2563eb;">
                    <div class="kpi-title" style="color: #1d4ed8;">Processing State</div>
                    <div class="kpi-value" style="color: #2563eb;"><?php echo $currentMonthReport['stats']['processing_tests']; ?></div>
                    <small style="color: #64748b; font-size: 11px;">In Progress in Lab</small>
                </div>

                <!-- 4. Complete State -->
                <div class="kpi-card" style="border-top: 4px solid #16a34a;">
                    <div class="kpi-title" style="color: #15803d;">Complete State</div>
                    <div class="kpi-value" style="color: #16a34a;"><?php echo $currentMonthReport['stats']['complete_tests']; ?></div>
                    <small style="color: #64748b; font-size: 11px;">Reports Uploaded</small>
                </div>

                <!-- 5. Rejected / Cancelled State -->
                <div class="kpi-card" style="border-top: 4px solid #dc2626;">
                    <div class="kpi-title" style="color: #b91c1c;">Rejected State</div>
                    <div class="kpi-value" style="color: #dc2626;"><?php echo $currentMonthReport['stats']['rejected_tests']; ?></div>
                    <small style="color: #64748b; font-size: 11px;">Declined / Cancelled</small>
                </div>
            </div>

            <!-- Detailed Sample Breakdown Table for Active Month -->
            <div class="card table-card" style="margin-bottom: 25px;">
                <div class="card-header">
                    <h3><i class="fa-solid fa-flask-vial" style="color: #2563eb; margin-right: 8px;"></i> Test Category Breakdown (<?php echo $currentMonthReport['month_label']; ?>)</h3>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sample Material / Test Type</th>
                                <th style="text-align: center;">Total Tests</th>
                                <th style="text-align: center;">Pending</th>
                                <th style="text-align: center;">Processing (In Progress)</th>
                                <th style="text-align: center;">Complete</th>
                                <th style="text-align: center;">Rejected</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($currentMonthReport['sample_stats'])): ?>
                                <?php foreach($currentMonthReport['sample_stats'] as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['sample_type'] ?: 'Unspecified'); ?></strong></td>
                                    <td style="text-align: center;"><span class="badge-status-kpi" style="background:#e0f2fe; color:#0369a1;"><?php echo $item['cnt']; ?></span></td>
                                    <td style="text-align: center;"><span class="badge-status-kpi" style="background:#ffedd5; color:#c2410c;"><?php echo $item['pending_cnt']; ?></span></td>
                                    <td style="text-align: center;"><span class="badge-status-kpi" style="background:#dbeafe; color:#1d4ed8;"><?php echo $item['processing_cnt']; ?></span></td>
                                    <td style="text-align: center;"><span class="badge-status-kpi" style="background:#dcfce7; color:#15803d;"><?php echo $item['complete_cnt']; ?></span></td>
                                    <td style="text-align: center;"><span class="badge-status-kpi" style="background:#fee2e2; color:#b91c1c;"><?php echo $item['rejected_cnt']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="empty-state">No test requests found for <?php echo $currentMonthReport['month_label']; ?>.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Historical Monthly Trend Table -->
            <div class="card table-card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-chart-line" style="color: #7c3aed; margin-right: 8px;"></i> Historical Month-by-Month Operations Summary</h3>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th style="text-align: center;">Total Tests Received</th>
                                <th style="text-align: center;">Pending</th>
                                <th style="text-align: center;">Processing (Accept)</th>
                                <th style="text-align: center;">Completed</th>
                                <th style="text-align: center;">Rejected (Cancel)</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($allMonthsSummary as $mRow): ?>
                            <tr>
                                <td>
                                    <strong><?php echo date('F Y', strtotime($mRow['ym'] . '-01')); ?></strong>
                                    <span style="font-size: 11px; color: #64748b; margin-left: 6px;">(<?php echo $mRow['ym']; ?>)</span>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: #0284c7;"><?php echo $mRow['total_tests']; ?></td>
                                <td style="text-align: center; font-weight: 600; color: #ea580c;"><?php echo $mRow['pending_tests']; ?></td>
                                <td style="text-align: center; font-weight: 600; color: #2563eb;"><?php echo $mRow['processing_tests']; ?></td>
                                <td style="text-align: center; font-weight: 600; color: #16a34a;"><?php echo $mRow['complete_tests']; ?></td>
                                <td style="text-align: center; font-weight: 600; color: #dc2626;"><?php echo $mRow['rejected_tests']; ?></td>
                                <td style="text-align: center;">
                                    <a href="monthly_reports.php?month=<?php echo $mRow['ym']; ?>" class="btn-action" style="color: #2563eb; text-decoration: none; font-size: 13px; font-weight: 600;" title="View Details">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>

