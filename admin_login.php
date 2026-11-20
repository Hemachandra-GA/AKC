<?php
ob_start();
session_start();

$error = "";

// If already logged in, redirect to respective admin dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SERVER["REQUEST_METHOD"] !== "POST") {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin') {
        header("Location: super_admin/dashboard.php");
        exit();
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'client') {
        // Clients are not allowed in admin portal
        session_unset();
        session_destroy();
        session_start();
        $error = "Customer accounts are not authorized to access the Admin Portal. Please use the Customer Login.";
    }
}

include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_unset();
    session_destroy();
    session_start();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $lowerUser = strtolower($username);
            $stmt = $conn->prepare("SELECT * FROM users WHERE (LOWER(username) = LOWER(:input) OR LOWER(email) = LOWER(:input)) LIMIT 1");
            $stmt->bindParam(':input', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Pre-configured admin credentials
            $adminCredentials = [
                'mamathasurekha8@gmail.com'  => ['role' => 'super_admin', 'passwords' => ['Admin@123', 'admin123', 'Admin123', 'Mamatha@123'], 'fullname' => 'System Super Admin', 'email' => 'mamathasurekha8@gmail.com'],
                'superadmin'                => ['role' => 'super_admin', 'passwords' => ['Admin@123', 'admin123', 'Admin123', 'Mamatha@123'], 'fullname' => 'System Super Admin', 'email' => 'mamathasurekha8@gmail.com'],
                'mamathasurekha14@gmail.com' => ['role' => 'admin', 'passwords' => ['Mamatha@123', 'Admin@123', 'admin123', 'Admin123', 'admin'], 'fullname' => 'Admin', 'email' => 'mamathasurekha14@gmail.com'],
                'admin'                     => ['role' => 'admin', 'passwords' => ['Mamatha@123', 'Admin@123', 'admin123', 'Admin123', 'admin'], 'fullname' => 'Admin', 'email' => 'mamathasurekha14@gmail.com'],
                'mamatha-admin'             => ['role' => 'admin', 'passwords' => ['Mamatha@123', 'Admin@123', 'admin123'], 'fullname' => 'Mamatha', 'email' => 'mamathachowdary604@gmail.com'],
                'arjun'                     => ['role' => 'admin', 'passwords' => ['Mamatha@123', 'Admin@123', 'admin123'], 'fullname' => 'Arjun', 'email' => 'rama.pavan@gadigitalsolutions.com'],
            ];

            // Check if account is a known client trying to log in here
            $knownClientUsernames = ['mamatha14031995@gmail.com', 'mamatha', 'mamathasurekha2@gmail.com', 'gadigitalsolutionss@gmail.com'];
            if (in_array($lowerUser, $knownClientUsernames) || ($user && $user['role'] === 'client')) {
                $error = "Access Denied: Customer accounts cannot log in through the Admin Portal. Please use the <a href='client_login.php' style='color:#dc2626; text-decoration:underline; font-weight:700;'>Customer Login</a>.";
            } else {
                $matchesKnown = false;
                $matchedInfo = null;
                if (isset($adminCredentials[$lowerUser])) {
                    $matchedInfo = $adminCredentials[$lowerUser];
                    if (in_array($password, $matchedInfo['passwords'])) {
                        $matchesKnown = true;
                    }
                }

                // If account does not exist in local SQLite yet, auto-create it with admin credentials
                if (!$user && $matchesKnown && $matchedInfo) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $ins = $conn->prepare("INSERT INTO users (username, password, role, fullname, email, is_active, created_at) VALUES (:u, :p, :r, :f, :e, 1, datetime('now'))");
                    $ins->execute([
                        'u' => $username,
                        'p' => $hash,
                        'r' => $matchedInfo['role'],
                        'f' => $matchedInfo['fullname'],
                        'e' => $matchedInfo['email']
                    ]);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                $validPassword = false;
                if ($user) {
                    // Check if role is admin or super_admin
                    if (!in_array($user['role'], ['admin', 'super_admin'])) {
                        $error = "Access Denied: Only Administrator and Super Admin accounts can log in here. Customers must use the <a href='client_login.php' style='color:#dc2626; text-decoration:underline; font-weight:700;'>Customer Login</a>.";
                    } else {
                        if (password_verify($password, $user['password'])) {
                            $validPassword = true;
                        } elseif ($matchesKnown) {
                            $validPassword = true;
                            $newHash = password_hash($password, PASSWORD_DEFAULT);
                            $updateStmt = $conn->prepare("UPDATE users SET password = :hash WHERE id = :id");
                            $updateStmt->execute(['hash' => $newHash, 'id' => $user['id']]);
                        }

                        if ($validPassword) {
                            if (isset($user['is_active']) && (int) $user['is_active'] === 0) {
                                $error = "Your administrative account has been deactivated. Please contact the Super Admin.";
                            } else {
                                $_SESSION['logged_in'] = true;
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['username'] = $user['username'];
                                $_SESSION['email'] = $user['email'] ?? $user['username'];
                                $_SESSION['role'] = $user['role'];
                                $_SESSION['fullname'] = $user['fullname'] ?? $user['username'];

                                $perms = [];
                                if (!empty($user['permissions'])) {
                                    $decoded = json_decode($user['permissions'], true);
                                    if (is_array($decoded)) {
                                        $perms = $decoded;
                                    }
                                }
                                $_SESSION['permissions'] = $perms;

                                if ($user['role'] === 'super_admin') {
                                    header("Location: super_admin/dashboard.php");
                                } else {
                                    header("Location: admin_dashboard.php");
                                }
                                exit();
                            }
                        } else {
                            $error = "Invalid administrator credentials.";
                        }
                    }
                } else {
                    $error = "Invalid administrator credentials.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

$pageTitle = "Admin & Staff Portal Login | AKC";
include 'includes/header.php';
?>

<style>
    .admin-login-container {
        padding: 90px 20px;
        background: radial-gradient(circle at 10% 20%, #0f172a 0%, #1e293b 90%);
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .admin-login-card {
        background: #ffffff;
        padding: 48px;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        width: 100%;
        max-width: 440px;
        margin: 0 auto;
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
    }

    .admin-portal-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        border: 1px solid #dbeafe;
    }

    .admin-login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .admin-login-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .admin-login-header p {
        color: #64748b;
        font-size: 14.5px;
        margin: 0;
    }

    .admin-form-group {
        margin-bottom: 22px;
    }

    .admin-form-group label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        font-size: 13.5px;
    }

    .admin-form-group input {
        width: 100%;
        padding: 13px 16px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.2s;
        background-color: #f8fafc;
        box-sizing: border-box;
    }

    .admin-form-group input:focus {
        outline: none;
        border-color: #0d47a1;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(13, 71, 161, 0.12);
    }

    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        padding-right: 48px;
    }

    .password-toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #64748b;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: #0d47a1;
    }

    .admin-login-btn {
        width: 100%;
        padding: 14px;
        background: #0d47a1;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.25);
    }

    .admin-login-btn:hover {
        background: #0a3880;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(13, 71, 161, 0.35);
    }

    .error-box {
        background-color: #fef2f2;
        color: #991b1b;
        padding: 14px 16px;
        border-radius: 10px;
        margin-bottom: 24px;
        font-size: 13.5px;
        text-align: left;
        line-height: 1.5;
        border: 1px solid #fecaca;
    }

    .admin-login-footer {
        text-align: center;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #64748b;
    }

    .admin-login-footer a {
        color: #0d47a1;
        text-decoration: none;
        font-weight: 600;
    }

    .admin-login-footer a:hover {
        text-decoration: underline;
    }
</style>

<main class="admin-login-container">
    <div class="container">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <div class="admin-portal-badge">
                    <i class="fa-solid fa-shield-halved"></i> Management &amp; Staff Only
                </div>
                <h2>Admin Portal</h2>
                <p>Sign in to manage test bookings, accreditations, and reports</p>
            </div>

            <?php if ($error): ?>
                <div class="error-box"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="admin-form-group">
                    <label>Admin Username or Email</label>
                    <input type="text" name="username" placeholder="admin or superadmin" required autofocus>
                </div>
                <div class="admin-form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <label style="margin-bottom: 0;">Password</label>
                    </div>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="adminPassword" placeholder="••••••••" required>
                        <span class="password-toggle" id="toggleAdminPassword">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="admin-login-btn">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In to Admin
                </button>
            </form>

            <div class="admin-login-footer">
                <p>Are you a customer or testing client?<br>
                   <a href="client_login.php"><i class="fa-solid fa-user"></i> Go to Customer Login &rarr;</a>
                </p>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('adminPassword');
        const toggleBtn = document.getElementById('toggleAdminPassword');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const icon = this.querySelector('i');
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

