<?php
// includes/mail_sender.php
// Robust email delivery service for AKC

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Get current application Base URL dynamically
 */
if (!function_exists('akc_get_base_url')) {
    function akc_get_base_url() {
        if (defined('BASE_URL') && !empty(BASE_URL)) {
            return rtrim(BASE_URL, '/');
        }
        $is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
                    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        $protocol = $is_https ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $base_dir = ($script_dir === '\\' || $script_dir === '/' || $script_dir === '.') ? '' : str_replace('\\', '/', $script_dir);
        $base_dir = preg_replace('#/(includes|admin|super_admin|api)$#', '', $base_dir);
        return rtrim($protocol . $host . $base_dir, '/');
    }
}

/**
 * Log an email to HTML / text file for local review and audit
 */
function akc_log_email($to_email, $to_name, $subject, $body_html, $action_link = '', $status = 'LOGGED') {
    $log_dir = dirname(__DIR__) . '/assets/uploads/email_logs';
    if (!@file_exists($log_dir)) {
        @mkdir($log_dir, 0777, true);
    }
    if (!@is_writable($log_dir) && @is_dir('/tmp')) {
        $log_dir = '/tmp/email_logs';
        if (!@file_exists($log_dir)) @mkdir($log_dir, 0777, true);
    }

    $log_file = $log_dir . '/email_log.html';
    $time = date('Y-m-d H:i:s');
    $badge_color = ($status === 'SENT') ? '#10b981' : '#3b82f6';

    $entry = "
    <div style='background: white; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; font-family: Montserrat, sans-serif;'>
        <div style='background: #f8fafc; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;'>
            <div>
                <strong style='color: #0f172a; font-size: 15px;'>Subject: " . htmlspecialchars($subject) . "</strong><br>
                <span style='color: #64748b; font-size: 13px;'>To: " . htmlspecialchars($to_name) . " &lt;" . htmlspecialchars($to_email) . "&gt; | Date: " . $time . "</span>
            </div>
            <span style='background: {$badge_color}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;'>{$status}</span>
        </div>";

    if (!empty($action_link)) {
        $entry .= "
        <div style='background: #eff6ff; padding: 10px 20px; border-bottom: 1px solid #dbeafe;'>
            <strong style='color: #1d4ed8; font-size: 13px;'>Action Link: </strong>
            <a href='" . htmlspecialchars($action_link) . "' target='_blank' style='color: #2563eb; font-weight: 600; text-decoration: underline; font-size: 13px;'>" . htmlspecialchars($action_link) . "</a>
        </div>";
    }

    $entry .= "
        <div style='padding: 20px;'>
            {$body_html}
        </div>
    </div>\n";

    if (!file_exists($log_file)) {
        $header = "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>AKC System Outgoing Email Log</title><meta name='viewport' content='width=device-width, initial-scale=1.0'></head><body style='background: #f1f5f9; padding: 30px; margin: 0; font-family: Montserrat, sans-serif;'><div style='max-width: 900px; margin: 0 auto;'><div style='background: #0A58CA; color: white; padding: 20px 25px; border-radius: 12px; margin-bottom: 25px;'><h2>AKC Email Dispatch Log</h2><p style='margin: 0; opacity: 0.9;'>Audit trail of outgoing test confirmations, status alerts, and password resets.</p></div>";
        @file_put_contents($log_file, $header . $entry);
    } else {
        @file_put_contents($log_file, $entry, FILE_APPEND);
    }
}

/**
 * Send an email using SMTP (PHPMailer), with fallback to mail() and email logging
 */
function akc_send_mail($to_email, $to_name, $subject, $html_body, $alt_body = '', $action_link = '') {
    $config_file = __DIR__ . '/email_config.php';
    $config = file_exists($config_file) ? include($config_file) : [];

    $smtp_host = $config['smtp_host'] ?? 'smtp.gmail.com';
    $smtp_user = $config['smtp_username'] ?? 'mamathasurekha14@gmail.com';
    $smtp_pass = $config['smtp_password'] ?? 'vbbofdhbqtijngkr';
    $from_email = $config['from_email'] ?? ($smtp_user ?: 'mamathasurekha14@gmail.com');
    $from_name = $config['from_name'] ?? 'AKC Packaging Technology & Research Centre';

    // Candidate app passwords to try in order of priority
    $passwords_to_try = array_unique(array_filter([
        str_replace(' ', '', $smtp_pass),
        'pntsulfqmnaimoyi', // Original known working App Password
        'vbbofdhbqtijngkr'  // AKC2 App Password
    ]));

    $sent = false;
    $error_msg = '';

    // Port 587 STARTTLS is standard and unrestricted on Vercel/AWS; Port 465 SSL is backup
    $configs = [
        ['port' => 587, 'secure' => PHPMailer::ENCRYPTION_STARTTLS, 'label' => 'Port 587 STARTTLS'],
        ['port' => 465, 'secure' => PHPMailer::ENCRYPTION_SMTPS,    'label' => 'Port 465 SSL']
    ];

    foreach ($configs as $cfg) {
        if ($sent) break;

        foreach ($passwords_to_try as $pwd) {
            if ($sent) break;

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = $smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtp_user;
                $mail->Password   = $pwd;
                $mail->SMTPSecure = $cfg['secure'];
                $mail->Port       = $cfg['port'];
                $mail->Timeout    = 6;
                $mail->CharSet    = 'UTF-8';

                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $mail->Hostname   = 'gmail.com';
                $mail->XMailer    = '';
                $mail->Encoding   = 'base64';

                $mail->setFrom($from_email, $from_name);
                $mail->addAddress($to_email, $to_name);
                $mail->addReplyTo($from_email, $from_name);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $html_body;
                $mail->AltBody = $alt_body ?: strip_tags($html_body);

                $mail->send();
                $sent = true;
                $error_msg = '';
                akc_log_email($to_email, $to_name, $subject, $html_body, $action_link, 'SENT (' . $cfg['label'] . ')');
            } catch (Exception $e) {
                $err = $mail->ErrorInfo ?: $e->getMessage();
                $error_msg = $err;
            }
        }
    }

    if (!$sent) {
        // Fallback: try PHP mail() or log locally
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: {$from_name} <{$from_email}>\r\n";
        $headers .= "Reply-To: {$from_name} <{$from_email}>\r\n";
        if (@mail($to_email, $subject, $html_body, $headers)) {
            $sent = true;
            akc_log_email($to_email, $to_name, $subject, $html_body, $action_link, 'SENT (PHP Mail)');
        } else {
            akc_log_email($to_email, $to_name, $subject, $html_body, $action_link, 'LOGGED (SMTP Error: ' . $error_msg . ')');
        }
    }

    return [
        'success' => $sent,
        'sent_external' => $sent,
        'action_link' => $action_link,
        'error' => $error_msg
    ];
}

/**
 * Send Test Booking Confirmation Email to client and admin notification
 */
function akc_send_booking_confirmation($booking) {
    $booking_id = $booking['id'] ?? 'N/A';
    $customer_name = $booking['fullname'] ?? 'Valued Customer';
    $customer_email = $booking['email'] ?? '';
    $phone = $booking['phone'] ?? 'N/A';
    $company = $booking['company'] ?? 'N/A';
    $sample_type = $booking['sample_type'] ?? 'N/A';
    $num_samples = $booking['num_samples'] ?? '1';
    $sample_desc = $booking['sample_desc'] ?? 'N/A';
    $requirements = $booking['requirements'] ?? 'None specified';
    $created_at = date('d M Y, h:i A');

    $base_url = akc_get_base_url();
    $login_url = $base_url . '/login.php';
    $forgot_pwd_url = $base_url . '/forgot-password.php';

    $login_user = !empty($booking['username']) ? $booking['username'] : $customer_email;
    $login_pass = $booking['password'] ?? null;
    $is_new_account = !empty($booking['is_new_account']);

    $client_subject = "Test Booking Confirmation & Client Portal Login - AKC PTARC (Ref #" . $booking_id . ")";

    $client_html = "
    <div style='font-family: Arial, Helvetica, sans-serif; max-width: 650px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;'>
        <div style='background: linear-gradient(135deg, #0A58CA 0%, #003a8c 100%); padding: 30px 25px; text-align: center; color: white;'>
            <h1 style='margin: 0 0 8px 0; font-size: 24px; font-weight: 700; color: #ffffff;'>AKC - PACKAGING TECHNOLOGY & RESEARCH CENTRE</h1>
            <p style='margin: 0; font-size: 14px; opacity: 0.9;'>NABL Accredited & ISO/IEC 17025 Certified Testing Laboratory</p>
        </div>
        <div style='padding: 30px 25px; color: #1e293b; line-height: 1.6;'>
            <h2 style='color: #0A58CA; margin-top: 0; font-size: 20px;'>Booking Confirmation #{$booking_id}</h2>
            <p>Dear <strong>" . htmlspecialchars($customer_name) . "</strong>,</p>
            <p>Thank you for submitting your testing request with AKC. Your booking request has been successfully recorded in our laboratory system with status <span style='background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 4px; font-weight: 700;'>PENDING REVIEW</span>.</p>
            
            <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;'>
                <h3 style='margin-top: 0; font-size: 16px; color: #0f172a; border-bottom: 2px solid #0A58CA; padding-bottom: 8px;'>Booking Summary</h3>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b; width: 40%;'><strong>Reference Number:</strong></td>
                        <td style='padding: 8px 0; font-weight: 600; color: #0A58CA;'>#" . htmlspecialchars($booking_id) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b;'><strong>Sample Type:</strong></td>
                        <td style='padding: 8px 0; font-weight: 500;'>" . htmlspecialchars($sample_type) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b;'><strong>Number of Samples:</strong></td>
                        <td style='padding: 8px 0; font-weight: 500;'>" . htmlspecialchars($num_samples) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b;'><strong>Sample Description:</strong></td>
                        <td style='padding: 8px 0; font-weight: 500;'>" . htmlspecialchars($sample_desc) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b;'><strong>Company:</strong></td>
                        <td style='padding: 8px 0; font-weight: 500;'>" . htmlspecialchars($company) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b;'><strong>Contact Phone:</strong></td>
                        <td style='padding: 8px 0; font-weight: 500;'>" . htmlspecialchars($phone) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b;'><strong>Requirements:</strong></td>
                        <td style='padding: 8px 0; font-weight: 500;'>" . nl2br(htmlspecialchars($requirements)) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #64748b;'><strong>Date Submitted:</strong></td>
                        <td style='padding: 8px 0; font-weight: 500;'>" . $created_at . "</td>
                    </tr>
                </table>
            </div>

            <!-- Customer Portal Login Credentials -->
            <div style='background: #f0f7ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 22px 25px; margin: 25px 0;'>
                <h3 style='margin: 0 0 10px 0; font-size: 17px; color: #1e40af;'>🔐 Your Client Portal Access &amp; Login Details</h3>
                <p style='margin: 0 0 15px 0; font-size: 14px; color: #1e3a8a; line-height: 1.5;'>
                    We have set up an account for you on the AKC Client Portal. You can log in anytime to track your sample testing progress live, view analyst test updates, download accredited certificates, and access your invoices.
                </p>

                <div style='background: #ffffff; border: 1px solid #dbeafe; border-radius: 8px; padding: 15px 18px; margin-bottom: 16px;'>
                    <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                        <tr>
                            <td style='padding: 6px 0; color: #475569; width: 35%;'><strong>Login Link:</strong></td>
                            <td style='padding: 6px 0;'><a href='{$login_url}' target='_blank' style='color: #0A58CA; font-weight: 600; text-decoration: underline;'>{$login_url}</a></td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 0; color: #475569;'><strong>Username / Email:</strong></td>
                            <td style='padding: 6px 0; font-weight: 700; color: #0f172a; font-family: monospace; font-size: 15px;'>" . htmlspecialchars($login_user) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 0; color: #475569;'><strong>Password:</strong></td>
                            <td style='padding: 6px 0;'>
                                " . ($login_pass ? "<span style='background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 15px; letter-spacing: 0.5px; border: 1px dashed #f59e0b;'>" . htmlspecialchars($login_pass) . "</span> <span style='font-size: 12px; color: #64748b; margin-left: 6px;'>(Temporary Password)</span>" : "<span style='color: #475569; font-style: italic;'>Use your existing account password</span>") . "
                            </td>
                        </tr>
                    </table>
                </div>

                <div style='text-align: center; margin: 18px 0 14px 0;'>
                    <a href='{$login_url}' target='_blank' style='display: inline-block; background: #0A58CA; color: #ffffff; padding: 12px 28px; font-weight: 600; font-size: 14px; border-radius: 6px; text-decoration: none; box-shadow: 0 2px 6px rgba(10,88,202,0.3);'>
                        Log In to Client Dashboard &rarr;
                    </a>
                </div>

                <div style='background: #ffffff; border-left: 3px solid #f59e0b; padding: 10px 14px; border-radius: 4px; font-size: 12.5px; color: #475569; line-height: 1.5;'>
                    <strong>💡 Need to change your password?</strong> You can change your password anytime after logging in, or reset it anytime using our <a href='{$forgot_pwd_url}' target='_blank' style='color: #0A58CA; font-weight: 600; text-decoration: underline;'>Forgot Password</a> option.
                </div>
            </div>

            <h3 style='color: #0f172a; font-size: 16px;'>What Happens Next?</h3>
            <ol style='padding-left: 20px; color: #475569;'>
                <li>Our technical team will review your sample specifications and test requirements.</li>
                <li>You will receive an official quotation and timeline within 24 hours.</li>
                <li>Upon quotation approval, our dispatch instructions will guide your sample delivery to our Sector 155, Noida testing centre.</li>
            </ol>

            <p style='margin-top: 30px; font-size: 13px; color: #64748b;'>
                Need urgent assistance? Call our lab helpdesk directly at <strong>+91 88604 07220</strong> or email <strong>info@akc-ptarc.com</strong>.
            </p>
        </div>
        <div style='background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;'>
            <strong>AKC Packaging Technology & Research Centre</strong><br>
            Plot 108, Phase 2, Sector 155, Noida, Uttar Pradesh 201304<br>
            &copy; " . date('Y') . " AKC-PTARC. All rights reserved.
        </div>
    </div>";

    // Send confirmation to client
    $result = akc_send_mail($customer_email, $customer_name, $client_subject, $client_html);

    // Also send admin notification
    $admin_subject = "New Test Booking Request #" . $booking_id . " - " . $customer_name;
    $admin_html = "
    <div style='font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px;'>
        <h2 style='color: #0A58CA; margin-top: 0;'>New Test Booking Received</h2>
        <p>A new test booking has been submitted online through the portal.</p>
        <ul>
            <li><strong>Booking ID:</strong> #" . htmlspecialchars($booking_id) . "</li>
            <li><strong>Client Name:</strong> " . htmlspecialchars($customer_name) . "</li>
            <li><strong>Client Email:</strong> " . htmlspecialchars($customer_email) . "</li>
            <li><strong>Phone:</strong> " . htmlspecialchars($phone) . "</li>
            <li><strong>Company:</strong> " . htmlspecialchars($company) . "</li>
            <li><strong>Sample Type:</strong> " . htmlspecialchars($sample_type) . "</li>
            <li><strong>Quantity:</strong> " . htmlspecialchars($num_samples) . "</li>
            <li><strong>Description:</strong> " . htmlspecialchars($sample_desc) . "</li>
            <li><strong>Requirements:</strong> " . nl2br(htmlspecialchars($requirements)) . "</li>
        </ul>
        <p><a href='" . akc_get_base_url() . "/admin_dashboard.php' style='display:inline-block; padding:10px 20px; background:#0A58CA; color:white; text-decoration:none; border-radius:6px; font-weight:600;'>Open Admin Dashboard</a></p>
    </div>";

    akc_send_mail('info@akc-ptarc.com', 'AKC Lab Admin', $admin_subject, $admin_html);

    return $result;
}

/**
 * Send Password Reset Email with dynamic URL link
 */
function akc_send_password_reset($user, $token) {
    $user_id = $user['id'] ?? '';
    $fullname = !empty($user['fullname']) ? $user['fullname'] : 'User';
    $to_email = trim($user['email'] ?? ($user['username'] ?? ''));

    $reset_link = akc_get_base_url() . "/reset-password.php?token=" . urlencode($token);

    $subject = "Reset Your Password - AKC Packaging Technology & Research Centre";

    $html = "
    <div style='font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;'>
        <div style='background: linear-gradient(135deg, #0A58CA 0%, #003a8c 100%); padding: 25px; text-align: center; color: white;'>
            <h1 style='margin: 0; font-size: 22px; font-weight: 700; color: white;'>AKC - PACKAGING TECHNOLOGY & RESEARCH CENTRE</h1>
        </div>
        <div style='padding: 30px 25px; color: #1e293b; line-height: 1.6;'>
            <h2 style='color: #0f172a; margin-top: 0; font-size: 20px;'>Password Reset Request</h2>
            <p>Hello <strong>" . htmlspecialchars($fullname) . "</strong>,</p>
            <p>We received a request to reset your password for your AKC portal account. Click the button below to choose a new password:</p>
            
            <div style='text-align: center; margin: 30px 0;'>
                <a href='" . htmlspecialchars($reset_link) . "' 
                   style='display: inline-block; background: #0A58CA; color: #ffffff; padding: 14px 32px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(10, 88, 202, 0.25);'>
                    Reset My Password
                </a>
            </div>

            <p style='font-size: 14px; color: #475569;'>If the button above does not work, copy and paste this link into your browser:</p>
            <p style='background: #f8fafc; padding: 12px; border-radius: 6px; word-break: break-all; font-size: 13px; border: 1px solid #e2e8f0;'>
                <a href='" . htmlspecialchars($reset_link) . "' style='color: #0A58CA;'>" . htmlspecialchars($reset_link) . "</a>
            </p>

            <p style='font-size: 13px; color: #dc2626;'>
                <strong>Note:</strong> This link is valid for 1 hour. If you did not request a password reset, you can safely ignore this email.
            </p>
        </div>
        <div style='background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;'>
            &copy; " . date('Y') . " AKC Packaging Technology & Research Centre. All rights reserved.
        </div>
    </div>";

    $alt_body = "Hello {$fullname},\n\nClick the link below to reset your password:\n{$reset_link}\n\nThis link expires in 1 hour.";

    return akc_send_mail($to_email, $fullname, $subject, $html, $alt_body, $reset_link);
}

/**
 * Get human-readable service details (Title, Description, Badge colors)
 */
function akc_get_service_display_info($service_key) {
    $normalized = strtolower(trim((string)$service_key));
    switch ($normalized) {
        case 'courses':
            return [
                'name' => 'Courses & Training',
                'badge' => 'Courses & Training Division',
                'badge_color' => '#8b5cf6',
                'bg_color' => '#f5f3ff',
                'border_color' => '#ddd6fe',
                'description' => 'Administration of professional polymer training, student enrollments, syllabus modules, and course certificates.'
            ];
        case 'testings':
        case 'testing':
            return [
                'name' => 'Testing & Certification',
                'badge' => 'Testing & Certification Division',
                'badge_color' => '#0A58CA',
                'bg_color' => '#eff6ff',
                'border_color' => '#bfdbfe',
                'description' => 'Management of laboratory test bookings, NABL sample processing, analyst assignments, and test certification.'
            ];
        case 'support_services':
        case 'support':
            return [
                'name' => 'Technical Support & Advisory',
                'badge' => 'Technical Support Division',
                'badge_color' => '#059669',
                'bg_color' => '#ecfdf5',
                'border_color' => '#a7f3d0',
                'description' => 'Management of client advisory inquiries, industry consultancy requests, and customer support coordination.'
            ];
        default:
            $formatted = !empty($service_key) ? ucwords(str_replace(['_', '-'], ' ', $service_key)) : 'General Administration';
            return [
                'name' => $formatted,
                'badge' => 'Administrative Division',
                'badge_color' => '#475569',
                'bg_color' => '#f8fafc',
                'border_color' => '#e2e8f0',
                'description' => 'Operational management within the AKC Portal.'
            ];
    }
}

/**
 * Send Admin Assignment Notification with allocated service, credentials, and portal link
 */
function akc_send_admin_assignment_notification($admin_data, $service_key = '', $plain_password = '', $action_type = 'create', $status = 1) {
    $fullname = trim($admin_data['fullname'] ?? 'Administrator');
    $username = trim($admin_data['username'] ?? '');
    $email = trim($admin_data['email'] ?? '');
    if (empty($email) && strpos($username, '@') !== false) {
        $email = $username;
    }

    if (empty($email)) {
        return ['success' => false, 'error' => 'No email address found for admin'];
    }

    $service_info = akc_get_service_display_info($service_key);
    $service_name = $service_info['name'];
    $service_desc = $service_info['description'];

    $base_url = akc_get_base_url();
    $login_url = $base_url . '/login.php';
    $forgot_pwd_url = $base_url . '/forgot-password.php';

    if ($action_type === 'edit') {
        $subject = "AKC Management Portal - Service Updated: {$service_name}";
        $header_title = "Administrative Service Updated";
        $intro_msg = "Your administrator profile has been updated for the <strong>{$service_name}</strong> division.";
    } elseif ($action_type === 'reset') {
        $subject = "AKC Portal - Password Reset Confirmation";
        $header_title = "Admin Password Reset";
        $intro_msg = "Your administrator security credentials have been updated by the Super Administrator.";
    } elseif ($action_type === 'status_update') {
        $status_text = ($status == 1) ? 'Active' : 'Inactive';
        $subject = "AKC Portal - Account Status Update: {$status_text}";
        $header_title = "Account Status: {$status_text}";
        $intro_msg = "Your administrator portal access status has been updated to <strong>{$status_text}</strong>.";
    } else {
        $subject = "Welcome to AKC Portal - Access Information ({$service_name})";
        $header_title = "Administrator Account Confirmation";
        $intro_msg = "Your administrator account has been created for the <strong>{$service_name}</strong> division.";
    }

    $html = "
    <div style='font-family: Arial, Helvetica, sans-serif; max-width: 620px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
        <div style='background: #0A58CA; padding: 25px 20px; text-align: center; color: white;'>
            <h1 style='margin: 0 0 6px 0; font-size: 20px; font-weight: 700; color: #ffffff;'>AKC - PACKAGING TECHNOLOGY &amp; RESEARCH CENTRE</h1>
            <p style='margin: 0; font-size: 13px; opacity: 0.9;'>NABL Accredited &amp; ISO Certified Polymer Research Center</p>
        </div>
        
        <div style='padding: 25px 22px; color: #1e293b; line-height: 1.6;'>
            <h2 style='color: #0A58CA; margin-top: 0; font-size: 18px;'>{$header_title}</h2>
            <p>Hello <strong>" . htmlspecialchars($fullname) . "</strong>,</p>
            <p style='font-size: 14px;'>{$intro_msg}</p>

            <!-- Highlighted Service Allocation Card -->
            <div style='background: " . $service_info['bg_color'] . "; border-left: 4px solid " . $service_info['badge_color'] . "; border-radius: 6px; padding: 14px 18px; margin: 20px 0;'>
                <div style='font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: " . $service_info['badge_color'] . "; margin-bottom: 4px;'>
                    Assigned Division
                </div>
                <div style='font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 6px;'>
                    " . htmlspecialchars($service_name) . "
                </div>
                <div style='font-size: 13px; color: #475569; line-height: 1.45;'>
                    " . htmlspecialchars($service_desc) . "
                </div>
            </div>

            <!-- Login Credentials Box -->
            <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin: 20px 0;'>
                <h3 style='margin: 0 0 12px 0; font-size: 15px; color: #0f172a;'>Portal Sign-In Details</h3>
                
                <table style='width: 100%; border-collapse: collapse; font-size: 13.5px;'>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; width: 35%;'><strong>Portal URL:</strong></td>
                        <td style='padding: 6px 0;'><a href='{$login_url}' target='_blank' style='color: #0A58CA; font-weight: 600; text-decoration: underline;'>{$login_url}</a></td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b;'><strong>Username:</strong></td>
                        <td style='padding: 6px 0; font-weight: 700; color: #0f172a; font-family: monospace; font-size: 14px;'>" . htmlspecialchars($username) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b;'><strong>Registered Email:</strong></td>
                        <td style='padding: 6px 0; font-weight: 500; color: #0f172a;'>" . htmlspecialchars($email) . "</td>
                    </tr>";

    if (!empty($plain_password)) {
        $html .= "
                    <tr>
                        <td style='padding: 6px 0; color: #64748b;'><strong>Initial Access Key:</strong></td>
                        <td style='padding: 6px 0;'>
                            <code style='background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 14px;'>" . htmlspecialchars($plain_password) . "</code>
                        </td>
                    </tr>";
    }

    $html .= "
                    <tr>
                        <td style='padding: 6px 0; color: #64748b;'><strong>Role:</strong></td>
                        <td style='padding: 6px 0;'><span style='background: #dbeafe; color: #1e40af; padding: 2px 7px; border-radius: 4px; font-weight: 600; font-size: 11.5px;'>Administrator</span></td>
                    </tr>
                </table>

                <div style='text-align: center; margin: 20px 0 10px 0;'>
                    <a href='{$login_url}' target='_blank' style='display: inline-block; background: #0A58CA; color: #ffffff; padding: 11px 26px; font-weight: 600; font-size: 14px; border-radius: 6px; text-decoration: none;'>
                        Open Admin Portal &rarr;
                    </a>
                </div>

                <div style='font-size: 12px; color: #64748b; margin-top: 12px; text-align: center;'>
                    You can change your access passkey anytime via our <a href='{$forgot_pwd_url}' target='_blank' style='color: #0A58CA; text-decoration: underline;'>Account Recovery Page</a>.
                </div>
            </div>

            <p style='margin-top: 20px; font-size: 12.5px; color: #64748b; line-height: 1.5;'>
                If you have questions regarding your division, please reach out to the Super Administrator or write to <strong>info@akc-ptarc.com</strong>.
            </p>
        </div>

        <div style='background: #f1f5f9; padding: 16px; text-align: center; font-size: 11.5px; color: #64748b; border-top: 1px solid #e2e8f0;'>
            <strong>AKC Packaging Technology &amp; Research Centre</strong><br>
            Plot 108, Phase 2, Sector 155, Noida, Uttar Pradesh 201304<br>
            &copy; " . date('Y') . " AKC-PTARC. All rights reserved.
        </div>
    </div>";

    $alt_body = "Hello {$fullname},\n\nWelcome to AKC Packaging Technology & Research Centre.\n\nYour administrator account has been created for the {$service_name} division in the AKC management portal.\n\nPortal URL: {$login_url}\nUsername: {$username}\nEmail: {$email}\n" . (!empty($plain_password) ? "Initial Access Key: {$plain_password}\n" : "") . "\nTo access your account, open {$login_url} in your browser.\n\nYou can change your passkey anytime after logging in.\n\nAKC Packaging Technology & Research Centre\nPlot 108, Phase 2, Sector 155, Noida, UP 201304";

    return akc_send_mail($email, $fullname, $subject, $html, $alt_body, $login_url);
}


