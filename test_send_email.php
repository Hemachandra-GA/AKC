<?php
require_once __DIR__ . '/includes/mail_sender.php';

$testTo = $_GET['to'] ?? 'mamatha14031995@gmail.com';

echo "<div style='font-family: sans-serif; max-width: 650px; margin: 40px auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06);'>";
echo "<h2 style='color: #0d47a1; margin-top: 0;'>AKC SMTP Email Dispatch Test</h2>";
echo "<p>Sending test email to: <strong>" . htmlspecialchars($testTo) . "</strong>...</p>";

$subject = "AKC System Test - SMTP Live Verification";
$html = "
<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #3b82f6; border-radius: 8px;'>
    <h2 style='color: #1d4ed8;'>Hello from AKC2!</h2>
    <p>This is a live test email sent via Gmail SMTP using App Password <code>vbbo fdhb qtij ngkr</code>.</p>
    <p>If you received this email, your outgoing SMTP configuration is <strong>100% working</strong>!</p>
    <p style='color: #64748b; font-size: 13px;'>Sent at: " . date('Y-m-d H:i:s') . "</p>
</div>";

$res = akc_send_mail($testTo, 'Mamatha', $subject, $html);

if ($res['sent_external']) {
    echo "<div style='background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; border: 1px solid #86efac;'>";
    echo "<strong>&check; Email Sent Successfully via Gmail SMTP!</strong><br>";
    echo "Please check your inbox (and Spam/Junk folder) for <strong>" . htmlspecialchars($testTo) . "</strong>.";
    echo "</div>";
} else {
    echo "<div style='background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 8px; border: 1px solid #fecaca;'>";
    echo "<strong>&cross; Email Delivery Notice:</strong><br>";
    echo htmlspecialchars($res['error'] ?: 'Logged locally.');
    echo "</div>";
}

echo "<div style='margin-top: 25px;'>";
echo "<a href='forgot-password.php' style='display: inline-block; padding: 10px 18px; background: #0d47a1; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;'>Go to Forgot Password Page &rarr;</a>";
echo "</div>";
echo "</div>";

