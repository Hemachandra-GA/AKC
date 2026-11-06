<?php
session_start();
include 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// Fetch unread notifications count for sidebar badge
$sidebar_unread_count = 0;
try {
    $stmtUnread = $conn->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = :uid AND is_read = 0");
    $stmtUnread->execute(['uid' => $user_id]);
    $sidebar_unread_count = (int)$stmtUnread->fetchColumn();
} catch(PDOException $e) {}

// Fetch current user details
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    try {
        if ($user['role'] == 'admin') {
            $email = $_POST['email'] ?? '';
            $update = $conn->prepare("UPDATE users SET fullname = :fullname, phone = :phone, email = :email WHERE id = :id");
            $update->execute([
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'id' => $user_id
            ]);
            $user['email'] = $email;
        } else {
            $email = $_POST['email'] ?? '';
            $old_email = $user['username'];
            
            // Check if new email is already taken by another user
            if ($email !== $old_email) {
                $check = $conn->prepare("SELECT id FROM users WHERE username = :email AND id != :id");
                $check->execute(['email' => $email, 'id' => $user_id]);
                if ($check->fetch()) {
                    throw new Exception("This email address is already in use by another account.");
                }
            }
            
            // Start a transaction to ensure all updates succeed together
            $conn->beginTransaction();
            
            // Update users table
            $update = $conn->prepare("UPDATE users SET fullname = :fullname, phone = :phone, username = :email, email = :email WHERE id = :id");
            $update->execute([
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'id' => $user_id
            ]);
            
            // Update bookings table
            $updateBookings = $conn->prepare("UPDATE test_bookings SET email = :new_email WHERE email = :old_email");
            $updateBookings->execute([
                'new_email' => $email,
                'old_email' => $old_email
            ]);
            
            $conn->commit();
            
            $_SESSION['username'] = $email;
            $user['username'] = $email;
            $user['email'] = $email;
        }
        
        $_SESSION['fullname'] = $fullname;
        $success_msg = "Profile updated successfully!";
        // Refresh user data
        $user['fullname'] = $fullname;
        $user['phone'] = $phone;
    } catch(Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $error_msg = "Update failed: " . $e->getMessage();
    }
}

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $new_pass = $_POST['new_password'] ?? '';
    $conf_pass = $_POST['confirm_password'] ?? '';
    
    if ($new_pass !== $conf_pass) {
        $error_msg = "Passwords do not match!";
    } else if (strlen($new_pass) < 6) {
        $error_msg = "Password must be at least 6 characters.";
    } else {
        try {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute([
                'password' => $hashed_pass,
                'id' => $user_id
            ]);
            $success_msg = "Password changed successfully!";
        } catch(PDOException $e) {
            $error_msg = "Password change failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - AKC</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .client-sidebar { background-color: #1e293b; }
        .sidebar-logo-text { color: white; font-weight: 700; font-size: 20px; }
        .client-badge { background: #3b82f6; color: white; padding: 2px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; }
        .sidebar-badge { background-color: #ef4444; color: white; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 2px 6px; margin-left: auto; display: inline-block; }
        
        .profile-header-card { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 40px; border-radius: 20px; color: white; display: flex; align-items: center; gap: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(30, 41, 59, 0.15); position: relative; overflow: hidden; }
        .profile-header-card::after { content: ''; position: absolute; right: -50px; top: -50px; width: 200px; height: 200px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; }
        
        .profile-avatar-large { width: 100px; height: 100px; background: rgba(255,255,255,0.1); border: 4px solid rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white; backdrop-filter: blur(10px); }
        .profile-title-info h1 { font-size: 28px; margin: 0; font-weight: 700; letter-spacing: -0.5px; }
        .profile-title-info p { margin: 5px 0 0; color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; }

        .settings-grid { display: grid; grid-template-columns: 1fr; gap: 30px; max-width: 900px; margin: 0 auto; }
        .settings-card { background: white; border-radius: 20px; box-shadow: 0 4px 25px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; }
        .settings-card-header { padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .settings-card-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 12px; }
        .settings-card-header h3 i { color: #3b82f6; font-size: 16px; }
        
        .settings-body { padding: 35px 30px; }
        .input-group { margin-bottom: 25px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-with-icon { position: relative; }
        .input-with-icon i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; transition: color 0.3s; }
        .input-with-icon input { width: 100%; padding: 15px 15px 15px 50px; border: 1px solid #e2e8f0; border-radius: 14px; font-size: 15px; font-weight: 500; color: #1e293b; transition: all 0.3s; background: #f8fafc; }
        
        .input-with-icon input:focus { outline: none; border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.08); }
        .input-with-icon input:focus + i { color: #3b82f6; }
        .input-with-icon input[readonly] { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }

        .btn-update { background: #3b82f6; color: white; border: none; padding: 15px 30px; border-radius: 14px; font-weight: 700; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 10px; font-size: 15px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2); }
        .btn-update:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3); }
        
        .alert { padding: 20px; border-radius: 15px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; font-weight: 600; font-size: 14px; animation: slideDown 0.4s ease; }
        @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .alert-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecdd3; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) { 
            .form-row { grid-template-columns: 1fr; }
            .profile-header-card {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
                gap: 15px;
                margin-bottom: 20px;
            }
            .profile-avatar-large {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }
            .profile-title-info h1 { font-size: 24px; }
            .profile-title-info p { font-size: 13px; }
            .settings-card-header { padding: 20px; }
            .settings-body { padding: 20px; }
        }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <?php if ($user['role'] == 'admin'): ?>
        <?php 
        $current_page = 'profile';
        $page_title = 'Profile Settings';
        include 'includes/admin_sidebar.php'; 
        ?>
    <?php else: ?>
        <?php include 'includes/client_sidebar.php'; ?>
    <?php endif; ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php if ($user['role'] == 'admin'): ?>
            <?php include 'includes/admin_topbar.php'; ?>
        <?php else: ?>
            <?php 
            $page_title = 'Account / <span class="blue-text">Profile Settings</span>';
            include 'includes/client_topbar.php'; 
            ?>
        <?php endif; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <div class="settings-grid">
                
                <?php if ($success_msg): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_msg): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Header Card -->
                <div class="profile-header-card">
                    <div class="profile-avatar-large">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="profile-title-info">
                        <h1><?php echo htmlspecialchars($user['fullname']); ?></h1>
                        <p><?php echo htmlspecialchars($user['username']); ?> • <?php echo ucfirst($user['role']); ?> Account</p>
                    </div>
                </div>

                <!-- Profile Information Card -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3><i class="fa-solid fa-id-card"></i> Personal Information</h3>
                    </div>
                    <div class="settings-body">
                        <form action="" method="POST">
                            <div class="input-group">
                                <label>Full Name</label>
                                <div class="input-with-icon">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="input-group">
                                    <label>Email Address</label>
                                    <div class="input-with-icon">
                                        <i class="fa-solid fa-envelope"></i>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['role'] == 'admin' ? ($user['email'] ?? 'mamathasurekha14@gmail.com') : $user['username']); ?>" required>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label>Phone Number</label>
                                    <div class="input-with-icon">
                                        <i class="fa-solid fa-phone"></i>
                                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+91 00000 00000">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="update_profile" class="btn-update">
                                <i class="fa-solid fa-check-circle"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Password Security Card -->
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3><i class="fa-solid fa-shield-halved"></i> Security & Password</h3>
                    </div>
                    <div class="settings-body">
                        <form action="" method="POST">
                            <div class="form-row">
                                <div class="input-group">
                                    <label>New Password</label>
                                    <div class="input-with-icon">
                                        <i class="fa-solid fa-lock"></i>
                                        <input type="password" name="new_password" id="new_password" placeholder="Min 6 characters" required style="padding-right: 50px;">
                                        <span class="password-toggle" data-target="new_password" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; z-index: 10; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-eye" style="position: static; transform: none; color: inherit; transition: none; width: auto; text-align: center;"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label>Confirm Password</label>
                                    <div class="input-with-icon">
                                        <i class="fa-solid fa-lock-open"></i>
                                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat password" required style="padding-right: 50px;">
                                        <span class="password-toggle" data-target="confirm_password" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; z-index: 10; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-eye" style="position: static; transform: none; color: inherit; transition: none; width: auto; text-align: center;"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="change_password" class="btn-update">
                                <i class="fa-solid fa-key"></i> Update Security
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.password-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });
});
</script>

</body>
</html>
