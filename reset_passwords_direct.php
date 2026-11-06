<?php
require 'includes/db_connect.php';

try {
    $client_hash = password_hash('client123', PASSWORD_DEFAULT);
    $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Reset client passwords
    $stmt1 = $conn->prepare("UPDATE users SET password = :password WHERE role = 'client'");
    $stmt1->execute([':password' => $client_hash]);
    
    // Reset admin passwords
    $stmt2 = $conn->prepare("UPDATE users SET password = :password WHERE role = 'admin'");
    $stmt2->execute([':password' => $admin_hash]);
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 30px; border: 1px solid #bae6fd; background-color: #f0f9ff; color: #0369a1; border-radius: 8px; text-align: center;'>
            <h2 style='margin-top: 0;'>Success!</h2>
            <p style='font-size: 16px;'>All client passwords have been reset to <strong>client123</strong>.</p>
            <p style='font-size: 16px;'>The admin password has been reset to <strong>admin123</strong>.</p>
            <a href='login.php' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>Go to Login</a>
          </div>";
} catch (PDOException $e) {
    echo "Error resetting passwords: " . $e->getMessage();
}
?>
