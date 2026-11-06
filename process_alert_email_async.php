<?php
// Run in background even if the client disconnects
ignore_user_abort(true);
set_time_limit(0);

// Only allow CLI execution
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require 'includes/db_connect.php';
require 'includes/PHPMailer/Exception.php';
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';

if ($argc < 3) {
    die("Missing arguments. Usage: php process_alert_email_async.php <booking_id> <alert_type>");
}

$booking_id = $argv[1];
$alert_type = $argv[2]; // 'due_soon' or 'past_due'

if (!$booking_id) {
    die("Missing booking ID");
}

try {
    // Fetch booking details
    $stmt = $conn->prepare("SELECT * FROM test_bookings WHERE id = :id");
    $stmt->bindParam(':id', $booking_id);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        die("Booking not found");
    }

    $fullname = $booking['fullname'];
    $company = $booking['company'];
    $email = $booking['email'];
    $phone = $booking['phone'];
    $sample_type = $booking['sample_type'];
    $num_samples = $booking['num_samples'];
    $sample_desc = $booking['sample_desc'];
    $requirements = $booking['requirements'];
    $estimated_time = $booking['estimated_time'];

    // Fetch admin email dynamically from database
    $admin_email = "mamathasurekha14@gmail.com"; // Fallback
    try {
        $adminStmt = $conn->query("SELECT email FROM users WHERE role = 'admin' LIMIT 1");
        $adminUser = $adminStmt->fetch(PDO::FETCH_ASSOC);
        if ($adminUser && !empty($adminUser['email'])) {
            $admin_email = $adminUser['email'];
        }
    } catch (PDOException $e) {
        error_log("Failed to fetch admin email in alert: " . $e->getMessage());
    }

    // Determine the subject and message based on alert type
    $formatted_est = date('M d, Y, h:i A', strtotime($estimated_time));
    if ($alert_type === 'due_soon') {
        $subject = "⚠️ URGENT: Booking #$booking_id is Due Soon (In Progress)";
        $alert_header_bg = "#f59e0b"; // Warning amber
        $alert_message = "This booking is approaching its estimated completion time. It is scheduled to be completed by <strong>$formatted_est</strong>.";
    } else {
        $subject = "🚨 OVERDUE: Booking #$booking_id is Past Due!";
        $alert_header_bg = "#ef4444"; // Danger red
        $alert_message = "This booking is <strong>PAST DUE</strong>! The estimated completion time was <strong>$formatted_est</strong>, but the booking is still marked as In Progress.";
    }

    $requirements_val = !empty($requirements) ? htmlspecialchars($requirements) : "None";

    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
        <div style='background-color: $alert_header_bg; color: white; padding: 20px; text-align: center;'>
            <h2 style='margin: 0;'>Booking Time Alert</h2>
        </div>
        <div style='padding: 25px; color: #334155;'>
            <p>Dear Admin,</p>
            <p style='font-size: 15px; line-height: 1.5; color: #1e293b;'>$alert_message</p>
            
            <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;'>Booking Details</h3>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr><td style='padding: 6px 0; width: 40%; color: #64748b;'><strong>Booking ID:</strong></td><td style='color: #1e293b; font-weight: bold;'>#$booking_id</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Client Name:</strong></td><td style='color: #1e293b; font-weight: 500;'>$fullname</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Company:</strong></td><td style='color: #1e293b; font-weight: 500;'>$company</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Email Address:</strong></td><td style='color: #1e293b; font-weight: 500;'>$email</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Material Type:</strong></td><td style='color: #1e293b; font-weight: 500;'>$sample_type</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Quantity:</strong></td><td style='color: #1e293b; font-weight: 500;'>$num_samples samples</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b; vertical-align: top;'><strong>Description:</strong></td><td style='color: #1e293b; font-weight: 500; line-height: 1.4;'>" . nl2br(htmlspecialchars($sample_desc)) . "</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b; vertical-align: top;'><strong>Special Requirements:</strong></td><td style='color: #1e293b; font-weight: 500; line-height: 1.4;'>$requirements_val</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Estimated Completion:</strong></td><td style='color: $alert_header_bg; font-weight: bold;'>$formatted_est</td></tr>
                </table>
            </div>

            <div style='margin-top: 25px; text-align: center;'>
                <a href='http://localhost:8000/admin_dashboard.php' style='background-color: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;'>View in Dashboard</a>
            </div>
        </div>
    </div>";

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // SMTP Settings
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
    $mail->setFrom('mamathasurekha14@gmail.com', 'AKC Alerts');
    $mail->addAddress($admin_email);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->send();

    error_log("Alert email sent successfully for booking #$booking_id ($alert_type) to $admin_email");

} catch (Exception $e) {
    error_log("Async alert email failed for booking #$booking_id: " . $e->getMessage());
}
