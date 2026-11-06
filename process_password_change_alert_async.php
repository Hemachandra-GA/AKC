<?php
// Run in background even if the client disconnects
ignore_user_abort(true);
set_time_limit(0);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/PHPMailer/Exception.php';
require_once __DIR__ . '/includes/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_log("PASSWORD ALERT FILE STARTED");

if (!isset($user_id)) {
    if (php_sapi_name() === 'cli') {
        $user_id = $argv[1] ?? null;
        $base_url = $argv[2] ?? 'http://localhost:8000';
    } else {
        $user_id = $_GET['user_id'] ?? null;
        
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $script_dir = ($script_dir === '/' || $script_dir === '\\') ? '' : $script_dir;
        $base_url = $protocol . $host . $script_dir;
    }
}

if (!$user_id) {
    error_log("Missing user ID");
    return;
}

try {

    // Fetch updated user details
    $stmt = $conn->prepare("
        SELECT * 
        FROM users 
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $user_id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log("User not found");
        return;
    }

    $fullname = $user['fullname'] ?? 'User';
    $username = $user['username'];
    $role     = $user['role'];

    // ==============================
    // FETCH ADMINS
    // ==============================

    $admins = $conn->query("
        SELECT 
            id,
            COALESCE(NULLIF(email, ''), username) AS email
        FROM users
        WHERE role = 'admin'
    ")->fetchAll(PDO::FETCH_ASSOC);

    if (empty($admins)) {
        error_log("No admins found");
    }

    // ==============================
    // INSERT ADMIN DASHBOARD ALERTS
    // ==============================

    $title = "User Password Updated";

    $msg = "
        User $fullname ($username)
        updated their account password.
    ";

    foreach ($admins as $admin) {

        $stmtInsert = $conn->prepare("
            INSERT INTO user_notifications
            (
                user_id,
                booking_id,
                title,
                message
            )
            VALUES
            (
                :uid,
                0,
                :title,
                :msg
            )
        ");

        $stmtInsert->execute([
            'uid'   => $admin['id'],
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    // ==============================
    // INSERT CLIENT IN-APP NOTIFICATION
    // ==============================

    $client_notification_title = "Password Updated Successfully";
    $client_notification_msg = "Your account password has been updated successfully. If you did not perform this action, please contact support immediately.";

    $stmtClientInsert = $conn->prepare("
        INSERT INTO user_notifications
        (
            user_id,
            booking_id,
            title,
            message
        )
        VALUES
        (
            :uid,
            0,
            :title,
            :msg
        )
    ");

    $stmtClientInsert->execute([
        'uid'   => $user_id,
        'title' => $client_notification_title,
        'msg'   => $client_notification_msg
    ]);

    // ==============================
    // ADMIN EMAIL TEMPLATE
    // ==============================

    $adminSubject = "
        Security Alert:
        Password Updated for $fullname
    ";

    $adminBody = "
    <div style='font-family: Arial, sans-serif;
                max-width: 600px;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                overflow: hidden;'>

        <div style='background-color: #1e293b;
                    color: white;
                    padding: 20px;
                    text-align: center;'>

            <h2 style='margin:0;'>
                Security Alert
            </h2>

        </div>

        <div style='padding:25px;
                    color:#334155;'>

            <p>Dear Admin,</p>

            <p>
                A user account password has
                been updated successfully.
            </p>

            <table style='width:100%;
                          border-collapse:collapse;
                          margin-top:20px;'>

                <tr>
                    <td style='padding:8px;'>
                        <strong>Name</strong>
                    </td>
                    <td>$fullname</td>
                </tr>

                <tr>
                    <td style='padding:8px;'>
                        <strong>Email</strong>
                    </td>
                    <td>$username</td>
                </tr>

                <tr>
                    <td style='padding:8px;'>
                        <strong>Role</strong>
                    </td>
                    <td>$role</td>
                </tr>

                <tr>
                    <td style='padding:8px;'>
                        <strong>Updated Time</strong>
                    </td>
                    <td>" . date('M d, Y h:i A') . "</td>
                </tr>

            </table>

        </div>

    </div>";

    // ==============================
    // SEND ADMIN EMAILS
    // ==============================

    foreach ($admins as $admin) {

        if (empty($admin['email'])) {
            continue;
        }

        try {

            $adminMail = new PHPMailer(true);

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

            $adminMail->isHTML(true);

            $adminMail->setFrom(
                'mamathasurekha14@gmail.com',
                'AKC Security'
            );

            $adminMail->addAddress($admin['email']);

            $adminMail->Subject = $adminSubject;

            $adminMail->Body = $adminBody;

            $adminMail->send();

            error_log(
                "Admin password alert sent to: "
                . $admin['email']
            );

        } catch (Exception $e) {

            error_log(
                "Admin password alert failed: "
                . $e->getMessage()
            );
        }
    }

    // ==============================
    // CLIENT PASSWORD UPDATE EMAIL
    // ==============================

    try {

        $clientMail = new PHPMailer(true);

        $clientMail->isSMTP();
        $clientMail->Host       = 'smtp.gmail.com';
        $clientMail->SMTPAuth   = true;
        $clientMail->Username   = 'mamathasurekha14@gmail.com';
        $clientMail->Password   = 'pntsulfqmnaimoyi';
        $clientMail->SMTPSecure = 'ssl';
        $clientMail->Port       = 465;

        $clientMail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $clientMail->isHTML(true);

        $clientMail->setFrom(
            'mamathasurekha14@gmail.com',
            'AKC Security'
        );

        // Client email stored in username
        $clientMail->addAddress($username);

        $clientMail->Subject =
            "Your Password Was Updated Successfully";

        $clientMail->Body = "
        <div style='font-family:Arial,sans-serif;
                    max-width:600px;'>

            <h2 style='color:#2563eb;'>
                Password Updated Successfully
            </h2>

            <p>
                Dear <strong>$fullname</strong>,
            </p>

            <p>
                Your account password has
                been updated successfully.
            </p>

            <p>
                If you did not perform this action,
                contact admin immediately.
            </p>

            <p style='margin-top:20px;
                      color:#64748b;
                      font-size:13px;'>

                Updated Time:
                " . date('M d, Y h:i A') . "

            </p>

        </div>";

        $clientMail->send();

        error_log(
            "Client password update mail sent"
        );

    } catch (Exception $e) {

        error_log(
            "Client password mail failed: "
            . $e->getMessage()
        );
    }

} catch (PDOException $e) {

    error_log(
        "Database error in password alert file: "
        . $e->getMessage()
    );
}
?>