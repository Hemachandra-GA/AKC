<?php
include '../includes/super_admin_auth_check.php';
include '../includes/db_connect.php';
require_once __DIR__ . '/../includes/mail_sender.php';

$message = "";
$messageType = "";

// Handle form submission to add new admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_admin') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*'), 0, 10);
    $permission_val = $_POST['permission'] ?? '';
    $permissions = !empty($permission_val) ? json_encode([$permission_val]) : '[]';
    
    if (!empty($username) && !empty($password) && !empty($fullname)) {
        try {
            // Check if username exists
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmtCheck->execute(['username' => $username]);
            $usernameExists = $stmtCheck->fetchColumn() > 0;
            
            // Check if service already has an admin
            $serviceExists = false;
            if (!empty($permission_val)) {
                $permString = '%"' . $permission_val . '"%';
                $stmtCheckService = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND permissions LIKE :perm AND is_active = 1");
                $stmtCheckService->execute(['perm' => $permString]);
                $serviceExists = $stmtCheckService->fetchColumn() > 0;
            }

            if ($usernameExists) {
                $message = "Username already exists.";
                $messageType = "error";
            } elseif ($serviceExists) {
                $message = "An admin for this service already exists. Only one admin is allowed per service.";
                $messageType = "error";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmtInsert = $conn->prepare("INSERT INTO users (username, password, role, fullname, email, phone, permissions, is_active) VALUES (:username, :password, 'admin', :fullname, :email, :phone, :permissions, 1)");
                $stmtInsert->execute([
                    'username' => $username,
                    'password' => $hashed_password,
                    'fullname' => $fullname,
                    'email' => $email,
                    'phone' => $phone,
                    'permissions' => $permissions
                ]);
                $newAdminId = $conn->lastInsertId();
                
                // Send email notification with allocated service and credentials
                $admin_data = [
                    'fullname' => $fullname,
                    'username' => $username,
                    'email'    => $email,
                    'phone'    => $phone
                ];
                $mail_result = akc_send_admin_assignment_notification($admin_data, $permission_val, $password, 'create');
                $service_info = akc_get_service_display_info($permission_val);

                // Insert in-app notification for the newly added admin
                try {
                    if ($newAdminId) {
                        $insN = $conn->prepare("INSERT INTO user_notifications (user_id, booking_id, title, message, is_read, created_at) VALUES (:uid, 0, :title, :msg, 0, datetime('now'))");
                        $insN->execute([
                            'uid'   => $newAdminId,
                            'title' => 'Assigned as Admin for ' . $service_info['name'],
                            'msg'   => 'Welcome to AKC! You have been appointed as the Administrator for the ' . $service_info['name'] . ' division.'
                        ]);
                    }
                } catch (Throwable $e) {}

                // Store credentials in session for instant on-screen display
                $_SESSION['last_admin_credentials'] = [
                    'fullname'      => $fullname,
                    'username'      => $username,
                    'email'         => $email,
                    'password'      => $password,
                    'service_name'  => $service_info['name'],
                    'service_key'   => $permission_val,
                    'email_sent'    => !empty($mail_result['sent_external']),
                    'error'         => $mail_result['error'] ?? ''
                ];

                if (!empty($mail_result['sent_external'])) {
                    $message = "Admin <strong>" . htmlspecialchars($fullname) . "</strong> (" . htmlspecialchars($username) . ") added successfully and allocated to <strong>" . htmlspecialchars($service_info['name']) . "</strong>. An assignment notification email with login credentials has been sent to <strong>" . htmlspecialchars($email) . "</strong>.";
                    $messageType = "success";
                } else {
                    $err_text = !empty($mail_result['error']) ? " (SMTP Note: " . htmlspecialchars($mail_result['error']) . ")" : "";
                    $message = "Admin <strong>" . htmlspecialchars($fullname) . "</strong> added successfully and allocated to <strong>" . htmlspecialchars($service_info['name']) . "</strong>. Generated password: <strong>" . htmlspecialchars($password) . "</strong>" . $err_text . ". You can share these credentials directly or click the Resend Email icon below.";
                    $messageType = "warning";
                }
            }
        } catch (PDOException $e) {
            $message = "Error adding admin: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Username, Full Name, and Password are required.";
        $messageType = "error";
    }
}

// Handle resend notification email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'resend_email') {
    $resend_id = (int)($_POST['resend_id'] ?? 0);
    if ($resend_id > 0) {
        $stmtFetch = $conn->prepare("SELECT fullname, username, email, permissions FROM users WHERE id = :id AND role = 'admin'");
        $stmtFetch->execute(['id' => $resend_id]);
        $u = $stmtFetch->fetch(PDO::FETCH_ASSOC);
        if ($u && !empty($u['email'])) {
            $admin_perms = json_decode($u['permissions'] ?? '[]', true);
            $service_key = !empty($admin_perms) ? $admin_perms[0] : '';
            $mail_result = akc_send_admin_assignment_notification($u, $service_key, '', 'create');
            if (!empty($mail_result['sent_external'])) {
                $message = "Assignment notification email successfully sent to <strong>" . htmlspecialchars($u['email']) . "</strong> (" . htmlspecialchars($u['fullname']) . ").";
                $messageType = "success";
            } else {
                $message = "Could not send email to " . htmlspecialchars($u['email']) . ": " . htmlspecialchars($mail_result['error'] ?? 'SMTP Delivery Error');
                $messageType = "error";
            }
        } else {
            $message = "No registered email address found for this administrator.";
            $messageType = "error";
        }
    }
}

// Handle edit admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_admin') {
    $edit_id = (int)$_POST['edit_id'];
    $fullname = trim($_POST['edit_fullname'] ?? '');
    $phone = trim($_POST['edit_phone'] ?? '');
    $edit_permission_val = $_POST['edit_permission'] ?? '';
    $permissions = !empty($edit_permission_val) ? json_encode([$edit_permission_val]) : '[]';
    
    if (!empty($fullname) && $edit_id > 0) {
        try {
            // Check if service already has an admin (excluding this admin)
            $serviceExists = false;
            if (!empty($edit_permission_val)) {
                $permString = '%"' . $edit_permission_val . '"%';
                $stmtCheckService = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND permissions LIKE :perm AND id != :id AND is_active = 1");
                $stmtCheckService->execute(['perm' => $permString, 'id' => $edit_id]);
                $serviceExists = $stmtCheckService->fetchColumn() > 0;
            }

            if ($serviceExists) {
                $message = "An admin for this service already exists. Only one admin is allowed per service.";
                $messageType = "error";
            } else {
                $stmtUpdate = $conn->prepare("UPDATE users SET fullname = :fullname, phone = :phone, permissions = :permissions WHERE id = :id AND role = 'admin'");
                $stmtUpdate->execute(['fullname' => $fullname, 'phone' => $phone, 'permissions' => $permissions, 'id' => $edit_id]);

                // Fetch admin info to send service allocation update email
                $stmtFetch = $conn->prepare("SELECT fullname, username, email FROM users WHERE id = :id");
                $stmtFetch->execute(['id' => $edit_id]);
                $u = $stmtFetch->fetch(PDO::FETCH_ASSOC);

                if ($u && !empty($u['email'])) {
                    $admin_data = [
                        'fullname' => $fullname,
                        'username' => $u['username'],
                        'email'    => $u['email'],
                        'phone'    => $phone
                    ];
                    akc_send_admin_assignment_notification($admin_data, $edit_permission_val, '', 'edit');
                }

                $service_info = akc_get_service_display_info($edit_permission_val);
                $message = "Admin <strong>" . htmlspecialchars($fullname) . "</strong> updated successfully. Service allocated: <strong>" . htmlspecialchars($service_info['name']) . "</strong>. Notification email dispatched to <strong>" . htmlspecialchars($u['email'] ?? 'admin') . "</strong>.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "Error updating admin: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Handle reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'reset_password') {
    $reset_id = (int)$_POST['reset_id'];
    $new_password = trim($_POST['new_password'] ?? '');
    
    if (!empty($new_password) && $reset_id > 0) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmtUpdate = $conn->prepare("UPDATE users SET password = :password WHERE id = :id AND role = 'admin'");
            $stmtUpdate->execute(['password' => $hashed_password, 'id' => $reset_id]);

            // Fetch user info for email
            $stmtFetch = $conn->prepare("SELECT fullname, username, email, permissions FROM users WHERE id = :id");
            $stmtFetch->execute(['id' => $reset_id]);
            $u = $stmtFetch->fetch(PDO::FETCH_ASSOC);
            if ($u) {
                $admin_perms = json_decode($u['permissions'] ?? '[]', true);
                $service_key = !empty($admin_perms) ? $admin_perms[0] : '';
                akc_send_admin_assignment_notification($u, $service_key, $new_password, 'reset');
            }

            $message = "Password for admin <strong>" . htmlspecialchars($u['username'] ?? '') . "</strong> reset successfully and new credentials emailed to <strong>" . htmlspecialchars($u['email'] ?? '') . "</strong>.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error resetting password: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Handle toggle active status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'toggle_status') {
    $toggle_id = (int)$_POST['toggle_id'];
    $current_status = (int)$_POST['current_status'];
    $new_status = $current_status ? 0 : 1;
    
    if ($toggle_id > 0) {
        try {
            $skipUpdate = false;
            
            if ($new_status == 1) {
                $stmtCheckPerms = $conn->prepare("SELECT permissions FROM users WHERE id = :id");
                $stmtCheckPerms->execute(['id' => $toggle_id]);
                $userPerms = $stmtCheckPerms->fetchColumn();
                
                if (!empty($userPerms)) {
                    $permsArray = json_decode($userPerms, true);
                    if (is_array($permsArray) && !empty($permsArray)) {
                        $permString = '%"' . $permsArray[0] . '"%';
                        $stmtCheckService = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND permissions LIKE :perm AND is_active = 1 AND id != :id");
                        $stmtCheckService->execute(['perm' => $permString, 'id' => $toggle_id]);
                        if ($stmtCheckService->fetchColumn() > 0) {
                            $skipUpdate = true;
                            $message = "Cannot activate. An active admin for this service already exists.";
                            $messageType = "error";
                        }
                    }
                }
            }

            if (!$skipUpdate) {
                $stmtUpdate = $conn->prepare("UPDATE users SET is_active = :status WHERE id = :id AND role = 'admin'");
                $stmtUpdate->execute(['status' => $new_status, 'id' => $toggle_id]);

                // Fetch user info for email notification
                $stmtFetch = $conn->prepare("SELECT fullname, username, email, permissions FROM users WHERE id = :id");
                $stmtFetch->execute(['id' => $toggle_id]);
                $u = $stmtFetch->fetch(PDO::FETCH_ASSOC);
                if ($u) {
                    $admin_perms = json_decode($u['permissions'] ?? '[]', true);
                    $service_key = !empty($admin_perms) ? $admin_perms[0] : '';
                    akc_send_admin_assignment_notification($u, $service_key, '', 'status_update', $new_status);
                }

                $status_label = $new_status ? 'activated' : 'deactivated';
                $message = "Admin <strong>" . htmlspecialchars($u['fullname'] ?? '') . "</strong> status updated to " . $status_label . " and notification sent.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "Error updating status: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Handle delete admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_admin') {
    $delete_id = (int)$_POST['delete_id'];
    if ($delete_id > 0) {
        try {
            $stmtDelete = $conn->prepare("DELETE FROM users WHERE id = :id AND role = 'admin'");
            $stmtDelete->execute(['id' => $delete_id]);
            $message = "Admin deleted successfully.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error deleting admin: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Handle Filters
$search = $_GET['search'] ?? '';

// Pagination parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query for admins
$base_query = "FROM users WHERE role = 'admin'";
$params = [];

if (!empty($search)) {
    $base_query .= " AND (fullname LIKE :search OR username LIKE :search OR email LIKE :search)";
    $params['search'] = "%$search%";
}

// Fetch total records for pagination
$count_query = "SELECT COUNT(*) " . $base_query;
$stmtCount = $conn->prepare($count_query);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch filtered admins with pagination
$query = "SELECT * " . $base_query . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
try {
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            width: 100%;
        }
        .header-actions {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
        }
        .filter-form {
            width: 100%;
            max-width: 300px;
        }
        .search-box {
            width: 100% !important;
        }
        .search-box input {
            width: 100% !important;
            height: 38px;
        }
        .btn-export {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 0; border-radius: 12px; width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: slideDown 0.3s ease; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-content form { display: flex; flex-direction: column; flex: 1; min-height: 0; }
        @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 15px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #fff; z-index: 2; }
        .modal-body { padding: 25px; overflow-y: auto; flex: 1; }
        .modal-footer { padding: 15px 25px; border-top: 1px solid #e2e8f0; text-align: right; background: #fff; z-index: 2; }
        .close { font-size: 24px; cursor: pointer; color: #64748b; }
        .close:hover { color: #1e293b; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px; color: #475569; }
        .form-group input { width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
        .form-group input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-active { background-color: #dcfce7; color: #16a34a; }
        .status-inactive { background-color: #fee2e2; color: #dc2626; }
    </style>
</head>
<body class="admin-body-bg">


<div class="admin-wrapper">
    <?php 
    $current_page = 'admins';
    $page_title = 'Admin Management';
    include '../includes/super_admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include '../includes/super_admin_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <?php if ($message): ?>
                <div class="alert <?php echo $messageType; ?>" style="padding: 15px; border-radius: 8px; margin-bottom: 20px; <?php echo $messageType == 'success' ? 'background-color: #dcfce7; color: #166534;' : ($messageType == 'warning' ? 'background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;' : 'background-color: #fee2e2; color: #991b1b;'); ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['last_admin_credentials'])): 
                $lac = $_SESSION['last_admin_credentials'];
                unset($_SESSION['last_admin_credentials']);
            ?>
            <div style="background: #ffffff; border: 2px solid #3b82f6; border-radius: 12px; padding: 22px 25px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.12);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                    <h4 style="margin: 0; color: #1e3a8a; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-user-shield" style="color: #3b82f6;"></i> Newly Created Administrator Credentials
                    </h4>
                    <?php if ($lac['email_sent']): ?>
                        <span style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                            <i class="fa-solid fa-circle-check"></i> Email Dispatched
                        </span>
                    <?php else: ?>
                        <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Email Delivery Pending / Check Spam
                        </span>
                    <?php endif; ?>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 14px;">
                    <div>
                        <span style="color: #64748b; font-size: 12px; display: block;">Full Name</span>
                        <strong style="color: #0f172a; font-size: 15px;"><?php echo htmlspecialchars($lac['fullname']); ?></strong>
                    </div>
                    <div>
                        <span style="color: #64748b; font-size: 12px; display: block;">Allocated Service</span>
                        <strong style="color: #2563eb; font-size: 15px;"><?php echo htmlspecialchars($lac['service_name']); ?></strong>
                    </div>
                    <div>
                        <span style="color: #64748b; font-size: 12px; display: block;">Username / Email</span>
                        <strong style="font-family: monospace; font-size: 14.5px; color: #0f172a;"><?php echo htmlspecialchars($lac['username']); ?></strong><br>
                        <span style="font-size: 12px; color: #64748b;"><?php echo htmlspecialchars($lac['email']); ?></span>
                    </div>
                    <div>
                        <span style="color: #64748b; font-size: 12px; display: block;">Temporary Password</span>
                        <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 15px; border: 1px dashed #f59e0b; display: inline-block;">
                            <?php echo htmlspecialchars($lac['password']); ?>
                        </span>
                    </div>
                </div>

                <?php if (!$lac['email_sent'] && !empty($lac['error'])): ?>
                    <div style="margin-top: 15px; background: #fff1f2; border: 1px solid #ffe4e6; padding: 10px 14px; border-radius: 6px; font-size: 13px; color: #9f1239;">
                        <strong>SMTP Delivery Notice:</strong> <?php echo htmlspecialchars($lac['error']); ?><br>
                        <span style="font-size: 12px; color: #64748b;">(You can share the generated password above directly with the admin while checking SMTP connectivity.)</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Main Data Table -->
            <div class="card table-card">
                <div class="card-header">
                    <h3>Administrators</h3>
                    <div class="header-actions">
                        <button onclick="openModal('addModal')" class="btn-export" style="background-color: #3b82f6; border: none; color: white; margin-right: 15px; cursor: pointer;">
                            <i class="fa-solid fa-plus"></i> Add New Admin
                        </button>
                        <form method="GET" class="filter-form">
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="search" placeholder="Search admin..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Services</th>
                                <th>Status</th>
                                <th>Added Date</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($admins) > 0): ?>
                                <?php 
                                $sno = $offset + 1;
                                foreach ($admins as $admin): 
                                ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($admin['username']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['phone']); ?></td>
                                        <td>
                                            <?php 
                                            $admin_perms = [];
                                            if (!empty($admin['permissions'])) {
                                                $decoded = json_decode($admin['permissions'], true);
                                                if (is_array($decoded)) {
                                                    $admin_perms = $decoded;
                                                }
                                            }
                                            if (empty($admin_perms)) {
                                                echo '<span class="text-muted" style="font-size:12px;">None</span>';
                                            } else {
                                                $formatted_perms = array_map(function($p) {
                                                    if ($p == 'courses') return '<span style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; padding: 3px 9px; border-radius: 4px; font-size: 11.5px; font-weight: 600;">🎓 Courses</span>';
                                                    if ($p == 'testings') return '<span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 9px; border-radius: 4px; font-size: 11.5px; font-weight: 600;">🧪 Testings</span>';
                                                    if ($p == 'support_services' || $p == 'support') return '<span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 3px 9px; border-radius: 4px; font-size: 11.5px; font-weight: 600;">🎧 Support</span>';
                                                    return '<span style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 3px 9px; border-radius: 4px; font-size: 11.5px; font-weight: 600;">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $p))) . '</span>';
                                                }, $admin_perms);
                                                echo implode(' ', $formatted_perms);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if(isset($admin['is_active']) && $admin['is_active']): ?>
                                                <span class="status-badge status-active">Active</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $admin['created_at'] ? date('M d, Y', strtotime($admin['created_at'])) : 'N/A'; ?></td>
                                        <td style="text-align: right;">
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Resend assignment notification email to <?php echo htmlspecialchars(addslashes($admin['email'])); ?>?');">
                                                <input type="hidden" name="action" value="resend_email">
                                                <input type="hidden" name="resend_id" value="<?php echo $admin['id']; ?>">
                                                <button type="submit" class="btn-action resend" style="background: none; border: none; color: #0284c7; cursor: pointer; margin-right: 10px;" title="Resend Assignment Email">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            <button onclick="openEditModal(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars(addslashes($admin['fullname'])); ?>', '<?php echo htmlspecialchars(addslashes($admin['phone'])); ?>', '<?php echo htmlspecialchars(addslashes($admin['permissions'] ?? '[]')); ?>')" class="btn-action edit" style="background: none; border: none; color: #3b82f6; cursor: pointer; margin-right: 10px;" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button onclick="openResetModal(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars(addslashes($admin['username'])); ?>')" class="btn-action reset" style="background: none; border: none; color: #eab308; cursor: pointer; margin-right: 10px;" title="Reset Password">
                                                <i class="fa-solid fa-key"></i>
                                            </button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to change this admin\'s status?');">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="toggle_id" value="<?php echo $admin['id']; ?>">
                                                <input type="hidden" name="current_status" value="<?php echo isset($admin['is_active']) ? $admin['is_active'] : 1; ?>">
                                                <button type="submit" class="btn-action delete" style="background: none; border: none; color: <?php echo (isset($admin['is_active']) && $admin['is_active']) ? '#ef4444' : '#10b981'; ?>; cursor: pointer;" title="Toggle Status">
                                                    <i class="fa-solid fa-power-off"></i>
                                                </button>
                                            </form>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to completely delete this admin? This action cannot be undone.');">
                                                <input type="hidden" name="action" value="delete_admin">
                                                <input type="hidden" name="delete_id" value="<?php echo $admin['id']; ?>">
                                                <button type="submit" class="btn-action delete-forever" style="background: none; border: none; color: #ef4444; cursor: pointer; margin-left: 10px;" title="Delete Admin">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="empty-state">No administrators found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 0): ?>
                <div class="pagination-container">
                    <div class="rows-per-page">
                        <form method="GET" action="" id="limitForm">
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <label>Rows per page:</label>
                            <select name="limit" onchange="document.getElementById('limitForm').submit()">
                                <option value="5" <?php if($limit == 5) echo 'selected'; ?>>5</option>
                                <option value="10" <?php if($limit == 10) echo 'selected'; ?>>10</option>
                                <option value="25" <?php if($limit == 25) echo 'selected'; ?>>25</option>
                                <option value="50" <?php if($limit == 50) echo 'selected'; ?>>50</option>
                            </select>
                        </form>
                    </div>
                    <div class="pagination-links">
                        <?php 
                        $queryString = "&search=".urlencode($search)."&limit=".$limit;
                        if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $queryString; ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $queryString; ?>" class="page-link <?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $queryString; ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Add Admin Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Admin</h3>
            <span class="close" onclick="closeModal('addModal')">&times;</span>
        </div>
        <form method="POST" action="admins.php">
            <input type="hidden" name="action" value="add_admin">
            <div class="modal-body">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="fullname">Full Name *</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label>Allocated Service Division * <small style="color: #64748b; font-weight: normal;">(Admin will be notified for this service)</small></label>
                    <div style="display: flex; gap: 15px; align-items: center; margin-top: 5px; flex-wrap: wrap;">
                        <label style="display: inline-flex; align-items: center; font-weight: 500; margin-bottom: 0; font-size: 13.5px;"><input type="radio" name="permission" value="courses" style="width: auto; margin-right: 6px;"> 🎓 Courses &amp; Training</label>
                        <label style="display: inline-flex; align-items: center; font-weight: 500; margin-bottom: 0; font-size: 13.5px;"><input type="radio" name="permission" value="testings" style="width: auto; margin-right: 6px;" checked> 🧪 Testing &amp; Certification</label>
                        <label style="display: inline-flex; align-items: center; font-weight: 500; margin-bottom: 0; font-size: 13.5px;"><input type="radio" name="permission" value="support_services" style="width: auto; margin-right: 6px;"> 🎧 Technical Support</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('addModal')" style="padding: 8px 16px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; margin-right: 10px;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 8px 16px; border: none; background: #3b82f6; color: white; border-radius: 6px; cursor: pointer;">Add Admin</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Admin Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Admin Details</h3>
            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <form method="POST" action="admins.php">
            <input type="hidden" name="action" value="edit_admin">
            <input type="hidden" name="edit_id" id="edit_id" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_fullname">Full Name *</label>
                    <input type="text" id="edit_fullname" name="edit_fullname" required>
                </div>
                <div class="form-group">
                    <label for="edit_phone">Phone Number</label>
                    <input type="text" id="edit_phone" name="edit_phone">
                </div>
                <div class="form-group">
                    <label>Allocated Service Division * <small style="color: #64748b; font-weight: normal;">(Admin will be notified of update)</small></label>
                    <div style="display: flex; gap: 15px; align-items: center; margin-top: 5px; flex-wrap: wrap;">
                        <label style="display: inline-flex; align-items: center; font-weight: 500; margin-bottom: 0; font-size: 13.5px;"><input type="radio" name="edit_permission" id="perm_edit_courses" value="courses" style="width: auto; margin-right: 6px;"> 🎓 Courses &amp; Training</label>
                        <label style="display: inline-flex; align-items: center; font-weight: 500; margin-bottom: 0; font-size: 13.5px;"><input type="radio" name="edit_permission" id="perm_edit_testings" value="testings" style="width: auto; margin-right: 6px;"> 🧪 Testing &amp; Certification</label>
                        <label style="display: inline-flex; align-items: center; font-weight: 500; margin-bottom: 0; font-size: 13.5px;"><input type="radio" name="edit_permission" id="perm_edit_support_services" value="support_services" style="width: auto; margin-right: 6px;"> 🎧 Technical Support</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')" style="padding: 8px 16px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; margin-right: 10px;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 8px 16px; border: none; background: #3b82f6; color: white; border-radius: 6px; cursor: pointer;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reset Password for <span id="reset_username_display"></span></h3>
            <span class="close" onclick="closeModal('resetModal')">&times;</span>
        </div>
        <form method="POST" action="admins.php">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="reset_id" id="reset_id" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label for="new_password">New Password *</label>
                    <div style="position: relative;">
                        <input type="password" id="new_password" name="new_password" required minlength="6" style="padding-right: 40px;">
                        <i class="fa-regular fa-eye" onclick="togglePasswordVisibility('new_password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8;"></i>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('resetModal')" style="padding: 8px 16px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer; margin-right: 10px;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 8px 16px; border: none; background: #3b82f6; color: white; border-radius: 6px; cursor: pointer;">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function openEditModal(id, fullname, phone, permissionsJson) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_fullname').value = fullname;
    document.getElementById('edit_phone').value = phone;
    
    // Reset checkboxes
    document.getElementById('perm_edit_courses').checked = false;
    document.getElementById('perm_edit_testings').checked = false;
    document.getElementById('perm_edit_support_services').checked = false;
    
    // Set checkboxes based on permissions
    if (permissionsJson) {
        try {
            const perms = JSON.parse(permissionsJson);
            if (perms.includes('courses')) document.getElementById('perm_edit_courses').checked = true;
            if (perms.includes('testings')) document.getElementById('perm_edit_testings').checked = true;
            if (perms.includes('support_services')) document.getElementById('perm_edit_support_services').checked = true;
        } catch(e) {}
    }

    openModal('editModal');
}

function openResetModal(id, username) {
    document.getElementById('reset_id').value = id;
    document.getElementById('reset_username_display').innerText = username;
    document.getElementById('new_password').value = '';
    openModal('resetModal');
}

// Close when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        if (searchInput.value) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }

        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchInput.closest('form').submit();
            }, 500);
        });
    }
});
</script>

</body>
</html>
