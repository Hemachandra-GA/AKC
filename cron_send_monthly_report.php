<?php
// cron_send_monthly_report.php
// Can be executed via CLI, cron job (e.g. 1st of every month at 08:00 AM), or via web request
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/mail_sender.php';

// Determine the month to report: defaults to previous month if run on 1st of month, or specified via $_GET['month']
$reportingMonth = $_GET['month'] ?? date('Y-m', strtotime('-1 month'));
if (isset($_GET['current_month'])) {
    $reportingMonth = date('Y-m');
}

// 1. Gather Super Admins
$stmtAdmins = $conn->query("SELECT fullname, email FROM users WHERE role = 'super_admin' AND is_active = 1");
$superAdmins = $stmtAdmins->fetchAll(PDO::FETCH_ASSOC);

if (empty($superAdmins)) {
    echo "No active super admins found.\n";
    exit(0);
}

// 2. Compute Aggregates
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
$stmt->execute(['ym' => $reportingMonth]);
$s = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
    'total_tests' => 0,
    'pending_tests' => 0,
    'processing_tests' => 0,
    'complete_tests' => 0,
    'rejected_tests' => 0
];

// Breakdown by sample type
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
$stmtTypes->execute(['ym' => $reportingMonth]);
$samples = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

$monthLabel = date('F Y', strtotime($reportingMonth . '-01'));
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

$htmlEmail = "
<div style='font-family: Arial, Helvetica, sans-serif; max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;'>
    <div style='background: linear-gradient(135deg, #0A58CA 0%, #1e3a8a 100%); padding: 25px 30px; color: #ffffff;'>
        <h2 style='margin: 0 0 6px 0; font-size: 22px; font-weight: 700;'>AKC Monthly Operations Digest</h2>
        <p style='margin: 0; font-size: 14px; opacity: 0.9;'>Testing Requests & Status Overview &bull; <strong>{$monthLabel}</strong></p>
    </div>

    <div style='padding: 25px 30px;'>
        <p style='font-size: 14px; color: #334155; margin-top: 0;'>
            Hello Super Admin,<br>
            Here is the official monthly operations report showing all test requests received by the admin team, along with counts for Pending, In Processing, Completed, and Rejected states for <strong>{$monthLabel}</strong>.
        </p>

        <!-- KPI Boxes -->
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
            Automated monthly dispatch &bull; Generated on {$currentDate} &bull; AKC Packaging Technology &amp; Research Centre
        </div>
    </div>
</div>";

$subject = "AKC Monthly Operations Report - " . $monthLabel;

foreach ($superAdmins as $admin) {
    $res = akc_send_mail($admin['email'], $admin['fullname'] ?? 'Super Admin', $subject, $htmlEmail);
    echo "Dispatched to {$admin['email']}: " . ($res['sent_external'] ? "SENT VIA SMTP" : "LOGGED") . "\n";
}

