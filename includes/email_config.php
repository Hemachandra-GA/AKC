<?php
// includes/email_config.php
// Centralized configuration for outgoing emails (SMTP / PHPMailer)

return [
    // SMTP Server Settings
    'smtp_host'     => 'smtp.gmail.com',
    'smtp_port'     => 465,
    'smtp_secure'   => 'ssl', // 'ssl' (port 465) or 'tls' (port 587)
    'smtp_auth'     => true,
    
    // SMTP Credentials
    // Google App Password for App "AKC2"
    'smtp_username' => 'mamathasurekha14@gmail.com',
    'smtp_password' => 'vbbofdhbqtijngkr', 
    
    // Sender Information
    'from_email'    => 'mamathasurekha14@gmail.com',
    'from_name'     => 'AKC2 - Packaging Technology & Research Centre',
    
    // Admin Notification Recipient
    'admin_email'   => 'info@akc-ptarc.com',
    'admin_name'    => 'AKC Lab Administrator',
    
    // Development / Logging
    'log_emails'    => true,
];
