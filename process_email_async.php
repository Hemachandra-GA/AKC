<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Run in background even if the client disconnects
ignore_user_abort(true);
set_time_limit(0);



require 'includes/db_connect.php';
require 'includes/PHPMailer/Exception.php';
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';



if (!isset($booking_id)) {
    die("Booking ID missing");
}

$pass_arg = $pass_arg ?? 'EXISTING';

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
    $sample_photo = $booking['sample_photo'];
    $requirements = $booking['requirements'];

    // Fetch admin email dynamically from database
    $admin_email = "mamathasurekha14@gmail.com"; // Fallback
    try {
        $adminStmt = $conn->query("SELECT email FROM users WHERE role = 'admin' LIMIT 1");
        $adminUser = $adminStmt->fetch(PDO::FETCH_ASSOC);
        if ($adminUser && !empty($adminUser['email'])) {
            $admin_email = $adminUser['email'];
        }
    } catch (PDOException $e) {
        error_log("Failed to fetch admin email: " . $e->getMessage());
    } 

    $admin_subject = "New Test Booking: " . $fullname;
    
    $photo_html = "";
    if (!empty($sample_photo)) {
        $photo_url = "https://gacapital.in/akc/assets/uploads/" . $sample_photo;
        $photo_html = "
        <div style='margin-top: 15px;'>
            <strong>Sample Photograph:</strong><br>
            <a href='$photo_url' target='_blank'>
                <img src='$photo_url' alt='Sample Photo' style='max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 8px; display: block;' />
            </a>
        </div>";
    }

    $requirements_val = !empty($requirements) ? htmlspecialchars($requirements) : "None";

    $admin_body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
        <div style='background-color: #3b82f6; color: white; padding: 20px; text-align: center;'>
            <h2 style='margin: 0;'>New Test Booking</h2>
        </div>
        <div style='padding: 25px; color: #334155;'>
            <p>Dear Admin,</p>
            <p>A new laboratory test booking has been received. The details are below:</p>
            
            <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;'>Customer Information</h3>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr><td style='padding: 6px 0; width: 40%; color: #64748b;'><strong>Client Name:</strong></td><td style='color: #1e293b; font-weight: 500;'>$fullname</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Company:</strong></td><td style='color: #1e293b; font-weight: 500;'>$company</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Email Address:</strong></td><td style='color: #1e293b; font-weight: 500;'>$email</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Phone Number:</strong></td><td style='color: #1e293b; font-weight: 500;'>$phone</td></tr>
                </table>
            </div>

            <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;'>Sample Details</h3>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr><td style='padding: 6px 0; width: 40%; color: #64748b;'><strong>Material Type:</strong></td><td style='color: #1e293b; font-weight: 500;'>$sample_type</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Number of Samples:</strong></td><td style='color: #1e293b; font-weight: 500;'>$num_samples samples</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b; vertical-align: top;'><strong>Description:</strong></td><td style='color: #1e293b; font-weight: 500; line-height: 1.4;'>" . nl2br(htmlspecialchars($sample_desc)) . "</td></tr>
                    <tr><td style='padding: 6px 0; color: #64748b; vertical-align: top;'><strong>Special Requirements:</strong></td><td style='color: #1e293b; font-weight: 500; line-height: 1.4;'>$requirements_val</td></tr>
                </table>
                $photo_html
            </div>

            <div style='margin-top: 25px; text-align: center;'>
                <a href='https://gacapital.in/akc/admin_dashboard.php' style='background-color: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;'>View in Dashboard</a>
            </div>
        </div>
    </div>";

    $client_subject = "Booking Confirmed - AKC Packaging Technology";
    
if ($pass_arg === 'EXISTING') {
    $credentials_section = "
            <div style='background-color: #f0f9ff; padding: 20px; border-radius: 8px; border: 1px solid #bae6fd; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #0369a1;'>Access Your Dashboard</h3>
                <p style='margin-bottom: 8px;'>You already have a registered account with this email address.</p>
                <p style='margin: 5px 0;'><strong>Login URL:</strong> <a href='https://gacapital.in/akc/login.php'>Click Here to Login</a></p>
                <p style='margin: 5px 0;'><strong>Username (Email):</strong> $email</p>
                <p style='margin: 12px 0 5px 0; font-size: 13px; color: #475569; line-height: 1.4;'>Please log in using your existing password to track this booking. If you have forgotten your password or have issues logging in, please use the <a href='https://gacapital.in/akc/forgot-password.php' style='color: #3b82f6; text-decoration: underline;'>Forgot Password</a> option on the login page.</p>
            </div>
    ";
}
else {
        $credentials_section = "
            <div style='background-color: #f0f9ff; padding: 20px; border-radius: 8px; border: 1px solid #bae6fd; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #0369a1;'>Your Dashboard Credentials</h3>
                <p style='margin-bottom: 8px;'>Use these details to track your test results and download reports:</p>
                <p style='margin: 5px 0;'><strong>Login URL:</strong> <a href='https://gacapital.in/akc/login.php'>Click Here to Login</a></p>
                <p style='margin: 5px 0;'><strong>Username (Email):</strong> $email</p>
                <p style='margin: 5px 0;'><strong>Password:</strong> <span style='font-size: 18px; color: #0369a1; font-weight: bold;'>$pass_arg</span></p>
            </div>";
    }

    $client_body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
        <div style='background-color: #3b82f6; color: white; padding: 20px; text-align: center;'>
            <h2 style='margin: 0;'>Booking Confirmed!</h2>
        </div>
        <div style='padding: 25px; color: #334155;'>
            <p>Dear <strong>$fullname</strong>,</p>
            <p>Thank you for choosing AKC Packaging Technology. Your test request for <strong>$sample_type</strong> has been successfully received.</p>
            
            $credentials_section

            <div style='background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b;'>
                <p style='margin: 0;'><strong>Booking Status:</strong> <span style='color: #f59e0b;'>Pending Approval</span></p>
                <p style='margin: 5px 0 0 0;'>Our team will review your samples and update your status shortly.</p>
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

    // Common Header
    $mail->isHTML(true);
    $mail->setFrom('mamathasurekha14@gmail.com', 'AKC Packaging');

    // Send to Admin
    $mail->addAddress($admin_email);
    $mail->Subject = $admin_subject;
    $mail->Body    = $admin_body;
    $mail->send();

    // Clear and Send to Client
    $mail->clearAddresses();
    $mail->addAddress($email);
    $mail->Subject = $client_subject;
    $mail->Body    = $client_body;
    $mail->send();

} catch (Exception $e) {
    // Log the error if necessary
    error_log("Async email failed: " . $e->getMessage());
}
?>
