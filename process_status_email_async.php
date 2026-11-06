<?php
// Run in background even if the client disconnects
ignore_user_abort(true);
set_time_limit(0);
require __DIR__ . '/includes/db_connect.php';
require __DIR__ . '/includes/PHPMailer/Exception.php';
require __DIR__ . '/includes/PHPMailer/PHPMailer.php';
require __DIR__ . '/includes/PHPMailer/SMTP.php';

if (php_sapi_name() === 'cli') {
    $booking_id = $argv[1] ?? null;
    $old_status = $argv[2] ?? null;
    $base_url = $argv[3] ?? 'http://localhost:8000';
} else {
    $booking_id = $_GET['booking_id'] ?? null;
    $old_status = $_GET['old_status'] ?? null;
    
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $script_dir = ($script_dir === '/' || $script_dir === '\\') ? '' : $script_dir;
    $base_url = $protocol . $host . $script_dir;
}

if (!$booking_id || !$old_status) {
    error_log("Missing booking_id or old_status");
    return;
}
try {
    // Fetch booking details
    $stmt = $conn->prepare("SELECT * FROM test_bookings WHERE id = :id");
    $stmt->bindParam(':id', $booking_id);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

 if (!$booking) {
    error_log("Booking not found");
    return;
}
    $fullname = $booking['fullname'];
    $email = $booking['email'];
    $sample_type = $booking['sample_type'];
    $new_status = $booking['status'];
    $estimated_time = $booking['estimated_time'];
    $rejection_reason = $booking['rejection_reason'];

    $status_labels = [
        'Pending' => 'Pending',
        'Accept' => 'In Progress',
        'Complete' => 'Completed',
        'Cancel' => 'Rejected'
    ];

    $old_label = $status_labels[$old_status] ?? $old_status;
    $new_label = $status_labels[$new_status] ?? $new_status;

    // Design email appearance based on status
    $header_bg = '#3b82f6'; // Default Blue
    $status_color = '#3b82f6';
    $status_icon = 'ℹ️';
    $extra_section = '';

    if ($new_status === 'Accept') {
        $header_bg = '#0284c7'; // Sky blue
        $status_color = '#0284c7';
        $status_icon = '⚙️';
        
        $est_time_str = 'N/A';
        if (!empty($estimated_time)) {
            $est_time_str = date('M d, Y, h:i A', strtotime($estimated_time));
        }
        
        $extra_section = "
            <div style='background-color: #f0f9ff; padding: 20px; border-radius: 8px; border: 1px solid #bae6fd; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #0369a1;'>Estimated Completion Time</h3>
                <p style='margin: 5px 0; font-size: 16px; font-weight: bold; color: #0369a1;'>$est_time_str</p>
                <p style='margin: 10px 0 0 0; font-size: 13px; color: #475569; line-height: 1.4;'>Our technicians are now processing your samples. You can track live updates on your dashboard.</p>
            </div>";
    } elseif ($new_status === 'Complete') {
        $header_bg = '#10b981'; // Green
        $status_color = '#10b981';
        $status_icon = '✅';

        // Check if there are report files attached
        $stmtFiles = $conn->prepare("SELECT file_name FROM booking_files WHERE booking_id = :bid");
        $stmtFiles->execute(['bid' => $booking_id]);
        $files = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
        
        $files_html = "";
        if (count($files) > 0) {
            $files_html = "<ul style='margin: 8px 0; padding-left: 20px; font-size: 14px;'>";
            foreach ($files as $file) {
                $file_url = $base_url . "/assets/uploads/reports/" . $file['file_name'];
                $files_html .= "<li style='margin-bottom: 5px;'><a href='$file_url' target='_blank' style='color: #10b981; text-decoration: underline; font-weight: 500;'>" . htmlspecialchars($file['file_name']) . "</a></li>";
            }
            $files_html .= "</ul>";
        } else {
            $files_html = "<p style='margin: 5px 0; color: #64748b;'>No files attached.</p>";
        }

        $extra_section = "
            <div style='background-color: #ecfdf5; padding: 20px; border-radius: 8px; border: 1px solid #a7f3d0; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #065f46;'>Test Report & Results</h3>
                <p style='margin-bottom: 8px;'>Your test reports have been uploaded and are ready for download:</p>
                $files_html
                <p style='margin: 12px 0 0 0; font-size: 13px; color: #475569; line-height: 1.4;'>You can log in to your dashboard to view complete analysis logs and history.</p>
            </div>";
    } elseif ($new_status === 'Cancel') {
        $header_bg = '#ef4444'; // Red
        $status_color = '#ef4444';
        $status_icon = '❌';
        $reason = !empty($rejection_reason) ? htmlspecialchars($rejection_reason) : 'No reason provided.';

        $extra_section = "
            <div style='background-color: #fef2f2; padding: 20px; border-radius: 8px; border: 1px solid #fecdd3; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #991b1b;'>Reason for Rejection</h3>
                <p style='margin: 5px 0; font-size: 15px; font-weight: 500; color: #991b1b; line-height: 1.4;'>$reason</p>
                <p style='margin: 12px 0 0 0; font-size: 13px; color: #475569; line-height: 1.4;'>Please contact support or submit a new test booking with the correct details.</p>
            </div>";
    }

    $subject = "Update: Booking #$booking_id Status is now $new_label";

    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
        <div style='background-color: $header_bg; color: white; padding: 20px; text-align: center;'>
            <h2 style='margin: 0;'>$status_icon Status Updated</h2>
        </div>
        <div style='padding: 25px; color: #334155;'>
            <p>Dear <strong>$fullname</strong>,</p>
            <p>The status of your test request for <strong>$sample_type</strong> (Booking #$booking_id) has been updated by the laboratory admin.</p>
            
            <div style='background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid $status_color; display: flex; align-items: center; justify-content: space-between;'>
                <div>
                    <p style='margin: 0; font-size: 14px; color: #64748b;'>Status Transition:</p>
                    <p style='margin: 5px 0 0 0; font-size: 16px; font-weight: bold;'>$old_label &rarr; <span style='color: $status_color;'>$new_label</span></p>
                </div>
            </div>

            $extra_section

            <div style='margin-top: 25px; text-align: center;'>
                <a href='{$base_url}/login.php' style='background-color: $header_bg; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;'>Access Dashboard</a>
            </div>

            <p style='margin-top: 25px;'>Best Regards,<br><strong>AKC Team</strong></p>
        </div>
    </div>";

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // Server Settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mamathasurekha14@gmail.com';
    $mail->Password   = 'pntsulfqmnaimoyi'; // App Password
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    // SSL Certificate Bypass for localhost
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->isHTML(true);
    $mail->setFrom('mamathasurekha14@gmail.com', 'AKC Packaging');
    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->send();

    error_log("Status email sent successfully for booking #$booking_id to $email");
    
    // ================= ADMIN STATUS MAIL =================

// Fetch all admins and super admins
$admins = $conn->query("
    SELECT 
        COALESCE(NULLIF(email, ''), username) AS admin_email
    FROM users
    WHERE role IN ('admin', 'super_admin')
")->fetchAll(PDO::FETCH_COLUMN);

foreach ($admins as $admin_email) {

    $adminMail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {

        // SMTP Settings
        $adminMail->isSMTP();
        $adminMail->Host       = 'smtp.gmail.com';
        $adminMail->SMTPAuth   = true;
        $adminMail->Username   = 'mamathasurekha14@gmail.com';
        $adminMail->Password   = 'pntsulfqmnaimoyi';
        $adminMail->SMTPSecure = 'ssl';
        $adminMail->Port       = 465;

        $adminMail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Mail Settings
        $adminMail->isHTML(true);

        $adminMail->setFrom(
            'mamathasurekha14@gmail.com',
            'AKC Packaging'
        );

        $adminMail->addAddress($admin_email);

        // Admin Subject
        $adminMail->Subject = "
            Admin Alert:
            Booking #$booking_id Status Updated
        ";

        // Admin Body
        $adminMail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px;'>

            <h2 style='color:#2563eb;'>
                Booking Status Updated
            </h2>

            <p>
                Booking <strong>#$booking_id</strong>
                status has been updated.
            </p>

            <table cellpadding='8' cellspacing='0'
                   style='border-collapse: collapse; width:100%;'>

                <tr>
                    <td><strong>Customer</strong></td>
                    <td>$fullname</td>
                </tr>

                <tr>
                    <td><strong>Sample Type</strong></td>
                    <td>$sample_type</td>
                </tr>

                <tr>
                    <td><strong>Old Status</strong></td>
                    <td>$old_label</td>
                </tr>

                <tr>
                    <td><strong>New Status</strong></td>
                    <td style='color:$status_color; font-weight:bold;'>
                        $new_label
                    </td>
                </tr>
            " . ($new_status === 'Accept' && !empty($estimated_time) ? "

<tr>
    <td><strong>Estimated Time</strong></td>
    <td>
        " . date('M d, Y, h:i A', strtotime($estimated_time)) . "
    </td>
</tr>

" : "") . "

" . ($new_status === 'Reject' && !empty($rejection_reason) ? "

<tr>
    <td><strong>Rejection Reason</strong></td>
    <td style='color:#dc2626; font-weight:500;'>
        $rejection_reason
    </td>
</tr>

" : "") . "

            </table>

            <div style='margin-top:25px;'>

                <a href='{$base_url}/admin/dashboard.php'
                   style='background:#2563eb;
                          color:white;
                          padding:12px 20px;
                          text-decoration:none;
                          border-radius:6px;
                          display:inline-block;'>

                    Open Admin Dashboard

                </a>

            </div>

        </div>";

        $adminMail->send();

        error_log("Admin status mail sent to: " . $admin_email);

    } catch (Exception $e) {

        error_log(
            "Admin status mail failed: " .
            $e->getMessage()
        );
    }
}

} catch (Exception $e) {
    error_log("Async status email failed: " . $e->getMessage());
}
