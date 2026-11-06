<?php
require 'includes/db_connect.php';

// First, find an admin
$stmt = $conn->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$raw_password = 'adminpassword123';
$hashed = password_hash($raw_password, PASSWORD_DEFAULT);

if ($admin) {
    // Update existing admin
    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$hashed, $admin['id']]);
    echo "Updated existing admin! Email: " . $admin['email'] . ", Password: " . $raw_password;
} else {
    // Create new admin
    $insert = $conn->prepare("INSERT INTO users (role, username, fullname, email, password) VALUES ('admin', 'admin', 'Super Admin', 'admin@akc.com', ?)");
    $insert->execute([$hashed]);
    echo "Created new admin! Email: admin@akc.com, Password: " . $raw_password;
}
?>
