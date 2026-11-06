<?php
// test_smtp_live.php - Diagnostic tool to verify live email delivery
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/mail_sender.php';

$test_email = $_GET['to'] ?? 'mamathasurekha14@gmail.com';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>AKC SMTP Live Diagnostic</title><style>body{font-family:sans-serif;padding:30px;background:#f8fafc;}pre{background:#1e293b;color:#38bdf8;padding:15px;border-radius:8px;overflow-x:auto;} .success{color:#15803d;font-weight:bold;} .error{color:#b91c1c;font-weight:bold;}</style></head><body>";
echo "<h2>AKC SMTP Live Diagnostic & Delivery Verification</h2>";
echo "<p>Testing outgoing email to: <strong>" . htmlspecialchars($test_email) . "</strong></p>";

$test_data = [
    'fullname' => 'Test Admin',
    'username' => 'test_admin_' . rand(100, 999),
    'email'    => $test_email
];
$password = 'Test@' . rand(1000, 9999);

$res = akc_send_admin_assignment_notification($test_data, 'courses', $password, 'create');

echo "<h3>Result:</h3>";
if (!empty($res['sent_external'])) {
    echo "<p class='success'>✅ SUCCESS: Email was accepted by Gmail SMTP server and delivered to " . htmlspecialchars($test_email) . "!</p>";
} else {
    echo "<p class='error'>❌ FAILED: SMTP delivery did not succeed.</p>";
    echo "<p><strong>Error Details:</strong> " . htmlspecialchars($res['error'] ?? 'Unknown error') . "</p>";
}

echo "<h3>Full Diagnostic Response:</h3>";
echo "<pre>" . htmlspecialchars(print_r($res, true)) . "</pre>";
echo "<hr><p>Log file location: <code>assets/uploads/email_logs/email_log.html</code></p>";
echo "</body></html>";

