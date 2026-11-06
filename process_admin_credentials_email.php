<?php
// process_admin_credentials_email.php
// Dispatches admin notifications and credentials
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/mail_sender.php';

if (php_sapi_name() === 'cli') {
    $admin_name     = $argv[1] ?? '';
    $admin_username = $argv[2] ?? '';
    $admin_email    = $argv[3] ?? '';
    $admin_password = $argv[4] ?? '';
    $action_type    = $argv[5] ?? 'create';
    $admin_status   = $argv[6] ?? '1';
    $service_key    = $argv[7] ?? '';
}

if (!isset($admin_email) || empty($admin_email)) {
    $admin_email = (strpos($admin_username ?? '', '@') !== false) ? $admin_username : '';
}

if (empty($admin_email)) {
    error_log("Cannot send admin credentials: No valid email address provided.");
    return;
}

$admin_data = [
    'fullname' => $admin_name ?? 'Administrator',
    'username' => $admin_username ?? '',
    'email'    => $admin_email
];

$result = akc_send_admin_assignment_notification($admin_data, $service_key ?? '', $admin_password ?? '', $action_type ?? 'create', (int)($admin_status ?? 1));
return $result;
