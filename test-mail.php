<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'includes/PHPMailer/Exception.php';
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mamathasurekha14@gmail.com';
    $mail->Password   = 'pntsulfqmnaimoyi'; // Your New App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // SSL Certificate Bypass for localhost
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Recipients
    $mail->setFrom('mamathasurekha14@gmail.com', 'AKC Test System');
    $mail->addAddress('mamathasurekha14@gmail.com'); 

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from AKC Local Server';
    $mail->Body    = '<h3>Success!</h3><p>If you are reading this, your PHPMailer SMTP setup is working perfectly on localhost.</p>';
    $mail->AltBody = 'Success! Your PHPMailer SMTP setup is working perfectly on localhost.';

    echo "<h3>Attempting to send email...</h3><pre>";
    $mail->send();
    echo "</pre><h2 style='color: green;'>✅ Email has been sent successfully!</h2>";
} catch (Exception $e) {
    echo "</pre><h2 style='color: red;'>❌ Email could not be sent.</h2>";
    echo "<b>Mailer Error:</b> " . $mail->ErrorInfo;
}
?>
