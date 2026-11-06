<?php
// process_reset_email_async.php
ignore_user_abort(true);
set_time_limit(0);

require_once 'includes/db_connect.php';
require_once 'includes/mail_sender.php';

$user_id = $_GET['user_id'] ?? '';
$token   = $_GET['token'] ?? '';

if (empty($user_id) || empty($token)) {
    die("Invalid request");
}

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found");
    }

    $result = akc_send_password_reset($user, $token);
    echo $result['success'] ? "success" : "failed: " . $result['error'];

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
