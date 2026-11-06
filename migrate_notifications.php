<?php
require 'includes/db_connect.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS user_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        booking_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 30px; border: 1px solid #bbf7d0; background-color: #f0fdf4; color: #15803d; border-radius: 8px; text-align: center;'>
            <h2 style='margin-top: 0;'>Success!</h2>
            <p style='font-size: 16px;'>The <strong>user_notifications</strong> database table has been created successfully.</p>
            <a href='admin_dashboard.php' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #22c55e; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>Go to Dashboard</a>
          </div>";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
