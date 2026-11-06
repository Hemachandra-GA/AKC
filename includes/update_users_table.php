<?php
include 'db_connect.php';

try {
    // Add phone column to users table if it doesn't exist
    $conn->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER fullname");
    echo "Phone column added to users table successfully.\n";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Phone column already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Sync existing users' phone numbers from test_bookings
try {
    $conn->exec("
        UPDATE users u
        JOIN (
            SELECT email, MAX(phone) as phone 
            FROM test_bookings 
            GROUP BY email
        ) b ON u.username = b.email
        SET u.phone = b.phone
        WHERE u.phone IS NULL OR u.phone = ''
    ");
    echo "Existing users' phone numbers synced successfully.\n";
} catch(PDOException $e) {
    echo "Error syncing phone numbers: " . $e->getMessage() . "\n";
}
?>
