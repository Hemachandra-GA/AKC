<?php
// Run in background even if the client disconnects
ignore_user_abort(true);
set_time_limit(0);

// Only allow CLI execution
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require __DIR__ . '/includes/db_connect.php';
require __DIR__ . '/includes/PHPMailer/Exception.php';
require __DIR__ . '/includes/PHPMailer/PHPMailer.php';
require __DIR__ . '/includes/PHPMailer/SMTP.php';

try {
    // Fetch all bookings currently in progress ('Accept')
    $stmt = $conn->query("SELECT * FROM test_bookings WHERE status = 'Accept'");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $now = time();
    $admins = $conn->query("SELECT id, email FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_ASSOC);

    if (empty($admins)) {
        die("No admins found to notify.");
    }

    foreach ($bookings as $b) {
        if (empty($b['estimated_time'])) {
            continue;
        }

        $booking_id = $b['id'];
        $fullname = $b['fullname'];
        $company = $b['company'];
        $email = $b['email'];
        $sample_type = $b['sample_type'];
        $num_samples = $b['num_samples'];
        $sample_desc = $b['sample_desc'];
        $requirements = $b['requirements'];
        $estimated_time = $b['estimated_time'];
        
        $est_timestamp = strtotime($estimated_time);
        $diff_sec = $est_timestamp - $now;
        $diff_hours = $diff_sec / 3600.0;

        $trigger_alert = false;
        $alert_type = ''; // '24h', '1h', 'overdue'
        $subject = '';
        $message_text = '';
        $update_col = '';

        if ($diff_hours <= 24.0 && $diff_hours > 1.0 && $b['alert_24h_sent'] == 0) {
            $trigger_alert = true;
            $alert_type = '24h';
            $subject = "Time Alert: Booking #$booking_id is due in less than 24 hours";
            $message_text = "Booking #$booking_id ($sample_type) for $fullname is due in less than 24 hours (Estimated completion time: " . date('M d, Y, h:i A', $est_timestamp) . ").";
            $update_col = 'alert_24h_sent';
        } elseif ($diff_hours <= 1.0 && $diff_hours > 0.0 && $b['alert_1h_sent'] == 0) {
            $trigger_alert = true;
            $alert_type = '1h';
            $subject = "URGENT: Booking #$booking_id is due in less than 1 hour";
            $message_text = "Booking #$booking_id ($sample_type) for $fullname is due in less than 1 hour (Estimated completion time: " . date('M d, Y, h:i A', $est_timestamp) . ")! Please review current progress.";
            $update_col = 'alert_1h_sent';
        } elseif ($diff_hours <= 0.0 && $b['alert_overdue_sent'] == 0) {
            $trigger_alert = true;
            $alert_type = 'overdue';
            $subject = "OVERDUE: Booking #$booking_id is past due!";
            $message_text = "Booking #$booking_id ($sample_type) is past its estimated completion time. Due is pending for this client ($fullname).";
            $update_col = 'alert_overdue_sent';
        }

        if ($trigger_alert) {
            // 1. Mark as sent in database
            $stmtUpdate = $conn->prepare("UPDATE test_bookings SET $update_col = 1 WHERE id = :id");
            $stmtUpdate->execute(['id' => $booking_id]);

            // 2. Insert user notification for each admin
            foreach ($admins as $admin) {
                $stmtNotif = $conn->prepare("INSERT INTO user_notifications (user_id, booking_id, title, message) VALUES (:uid, :bid, :title, :msg)");
                $stmtNotif->execute([
                    'uid' => $admin['id'],
                    'bid' => $booking_id,
                    'title' => $subject,
                    'msg' => $message_text
                ]);
            }

            // Also insert notification for the client if overdue
            if ($alert_type === 'overdue') {
                $client_user_id = null;
                if (!empty($email)) {
                    $stmtUser = $conn->prepare("SELECT id FROM users WHERE username = :email");
                    $stmtUser->execute(['email' => $email]);
                    $client_user_id = $stmtUser->fetchColumn() ?: null;
                }
                if ($client_user_id) {
                    $client_subject = "Booking #$booking_id: Status Update";
                    $client_message = "Your booking #$booking_id ($sample_type) is past its estimated completion time. Our team will get in touch with you shortly.";
                    $stmtClientNotif = $conn->prepare("INSERT INTO user_notifications (user_id, booking_id, title, message) VALUES (:uid, :bid, :title, :msg)");
                    $stmtClientNotif->execute([
                        'uid' => $client_user_id,
                        'bid' => $booking_id,
                        'title' => $client_subject,
                        'msg' => $client_message
                    ]);
                }
            }

            // 3. Compose and send email to each admin
            $header_bg = '#3b82f6';
            if ($alert_type === '24h') {
                $header_bg = '#f59e0b'; // Amber
            } elseif ($alert_type === '1h') {
                $header_bg = '#ea580c'; // Orange-Red
            } elseif ($alert_type === 'overdue') {
                $header_bg = '#ef4444'; // Red
            }

            $formatted_est = date('M d, Y, h:i A', $est_timestamp);
            $req_val = !empty($requirements) ? htmlspecialchars($requirements) : "None";

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
                <div style='background-color: $header_bg; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Booking Time Alert</h2>
                </div>
                <div style='padding: 25px; color: #334155;'>
                    <p>Dear Admin,</p>
                    <p style='font-size: 15px; line-height: 1.5; color: #1e293b; font-weight: bold;'>$message_text</p>
                    
                    <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;'>Booking Details</h3>
                        <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                            <tr><td style='padding: 6px 0; width: 40%; color: #64748b;'><strong>Booking ID:</strong></td><td style='color: #1e293b; font-weight: bold;'>#$booking_id</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b;'><strong>Client Name:</strong></td><td style='color: #1e293b; font-weight: 500;'>$fullname</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b;'><strong>Company:</strong></td><td style='color: #1e293b; font-weight: 500;'>$company</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b;'><strong>Client Email:</strong></td><td style='color: #1e293b; font-weight: 500;'>$email</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b;'><strong>Material Type:</strong></td><td style='color: #1e293b; font-weight: 500;'>$sample_type</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b;'><strong>Quantity:</strong></td><td style='color: #1e293b; font-weight: 500;'>$num_samples samples</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b; vertical-align: top;'><strong>Description:</strong></td><td style='color: #1e293b; font-weight: 500; line-height: 1.4;'>" . nl2br(htmlspecialchars($sample_desc)) . "</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b; vertical-align: top;'><strong>Special Requirements:</strong></td><td style='color: #1e293b; font-weight: 500; line-height: 1.4;'>$req_val</td></tr>
                            <tr><td style='padding: 6px 0; color: #64748b;'><strong>Estimated Completion:</strong></td><td style='color: $header_bg; font-weight: bold;'>$formatted_est</td></tr>
                        </table>
                    </div>

                    <div style='margin-top: 25px; text-align: center;'>
                        <a href='http://localhost:8000/admin/dashboard.php' style='background-color: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;'>View in Dashboard</a>
                    </div>
                </div>
            </div>";

            foreach ($admins as $admin) {
                if (empty($admin['email'])) continue;
                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->CharSet = 'UTF-8';

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
                    $mail->addAddress($admin['email']);
                    $mail->Subject = $subject;
                    $mail->Body    = $body;
                    $mail->send();

                    error_log("Time alert email ($alert_type) successfully sent to admin: " . $admin['email']);
                } catch (Exception $mailEx) {
                    error_log("Failed to send time alert email to admin " . $admin['email'] . ": " . $mailEx->getMessage());
                }
            }

            // Send overdue alert email to client
            if ($alert_type === 'overdue' && !empty($email)) {
                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->CharSet = 'UTF-8';

                    // SMTP Settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'mamathasurekha14@gmail.com';
                    $mail->Password   = 'pntsulfqmnaimoyi'; // App Password
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port       = 465;

                    // SSL Bypass
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    $mail->isHTML(true);
                    $mail->setFrom('mamathasurekha14@gmail.com', 'AKC Updates');
                    $mail->addAddress($email);
                    $mail->Subject = "Booking #$booking_id Status Update: Overdue Pending";
                    
                    $client_body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
                        <div style='background-color: #ef4444; color: white; padding: 20px; text-align: center;'>
                            <h2 style='margin: 0;'>Booking Status Update</h2>
                        </div>
                        <div style='padding: 25px; color: #334155;'>
                            <p>Dear $fullname,</p>
                            <p style='font-size: 15px; line-height: 1.5; color: #1e293b;'>Your booking #$booking_id ($sample_type) is past its estimated completion time. Our team will get in touch with you shortly. Thank you for your patience.</p>
                            
                            <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin: 20px 0;'>
                                <h3 style='margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;'>Booking Info</h3>
                                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                                    <tr><td style='padding: 6px 0; width: 40%; color: #64748b;'><strong>Booking ID:</strong></td><td style='color: #1e293b; font-weight: bold;'>#$booking_id</td></tr>
                                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Material Type:</strong></td><td style='color: #1e293b; font-weight: 500;'>$sample_type</td></tr>
                                    <tr><td style='padding: 6px 0; color: #64748b;'><strong>Quantity:</strong></td><td style='color: #1e293b; font-weight: 500;'>$num_samples samples</td></tr>
                                </table>
                            </div>
                            
                            <div style='margin-top: 25px; text-align: center;'>
                                <a href='http://localhost:8000/client/dashboard.php' style='background-color: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;'>View My Dashboard</a>
                            </div>
                        </div>
                    </div>";
                    
                    $mail->Body    = $client_body;
                    $mail->send();

                    error_log("Overdue alert email successfully sent to client: " . $email);
                } catch (Exception $mailEx) {
                    error_log("Failed to send overdue alert email to client " . $email . ": " . $mailEx->getMessage());
                }
            }
        }
    }
} catch (PDOException $e) {
    error_log("Database error in process_time_alerts_async: " . $e->getMessage());
}
