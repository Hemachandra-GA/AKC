<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

$error = "";
$success = "";
$valid_token = false;
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($token) {
    try {
        // 1. Query to check if token exists and has not expired in local DB
        $current_time = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > :current_time LIMIT 1");
        $stmt->execute(['token' => $token, 'current_time' => $current_time]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Fallback: Verify cryptographically signed token (handles ephemeral serverless containers on Vercel)
        if (!$user && strpos($token, '.') !== false) {
            $parts = explode('.', $token);
            if (count($parts) === 3) {
                $rand = $parts[0];
                $tokenPayload = $parts[1];
                $tokenSig = $parts[2];
                $expectedSig = hash_hmac('sha256', $tokenPayload, 'AKC_SECURE_RESET_SALT_2026');
                if (hash_equals($expectedSig, $tokenSig)) {
                    $decoded = json_decode(base64_decode($tokenPayload), true);
                    if (!empty($decoded['e']) && !empty($decoded['x']) && $decoded['x'] > time()) {
                        $targetEmail = strtolower(trim($decoded['e']));
                        // Find user
                        $uStmt = $conn->prepare("SELECT * FROM users WHERE LOWER(TRIM(email)) = :email OR LOWER(TRIM(username)) = :email LIMIT 1");
                        $uStmt->execute(['email' => $targetEmail]);
                        $user = $uStmt->fetch(PDO::FETCH_ASSOC);
                        if (!$user) {
                            $name = ucwords(explode('@', $targetEmail)[0]);
                            $ins = $conn->prepare("INSERT INTO users (username, email, password, role, fullname, is_active, created_at) VALUES (:u, :e, :p, 'client', :f, 1, datetime('now'))");
                            $ins->execute([
                                'u' => $targetEmail,
                                'e' => $targetEmail,
                                'p' => password_hash('AKC@' . rand(10000, 99999), PASSWORD_DEFAULT),
                                'f' => $name
                            ]);
                            $uStmt->execute(['email' => $targetEmail]);
                            $user = $uStmt->fetch(PDO::FETCH_ASSOC);
                        }
                    }
                }
            }
        }

        if ($user) {
            $valid_token = true;

            // Handle password update POST request
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
                $password = $_POST['password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';

                if (strlen($password) < 6) {
                    $error = "Password must be at least 6 characters.";
                } elseif ($password !== $confirm_password) {
                    $error = "Passwords do not match.";
                } else {
                    // Update user's password and clear reset token columns
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id");
                    $update->execute([
                        'password' => $hashed_password,
                        'id' => $user['id']
                    ]);

                    $success = "Your password has been updated successfully! You can now log in with your new password.";
                    $valid_token = false; // Do not show form anymore
                }
            }
        } else {
            $error = "This password reset link is invalid or has expired.";
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
} else {
    $error = "Invalid request. No security token provided.";
}
?>

<style>
.login-container {
    padding: 120px 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 85vh;
    display: flex;
    align-items: center;
}
.login-card {
    background: white;
    padding: 50px;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.05);
    width: 100%;
    max-width: 450px;
    margin: 0 auto;
    border: 1px solid rgba(226, 232, 240, 0.8);
}
.login-header {
    text-align: center;
    margin-bottom: 40px;
}
.login-header h2 {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
}
.login-header p {
    color: #64748b;
    font-size: 16px;
    line-height: 1.5;
}
.form-group {
    margin-bottom: 24px;
}
.form-group label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 10px;
    font-size: 14px;
}
.form-group input {
    width: 100%;
    padding: 14px 18px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
    background-color: #f8fafc;
}
.form-group input:focus {
    outline: none;
    border-color: #3b82f6;
    background-color: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}
.password-wrapper {
    position: relative;
}
.password-wrapper input {
    padding-right: 50px;
}
.password-toggle {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #64748b;
    transition: color 0.2s;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
}
.password-toggle:hover {
    color: #3b82f6;
}
.login-btn {
    width: 100%;
    padding: 16px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}
.login-btn:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
}
.error-box {
    background-color: #fef2f2;
    color: #dc2626;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 30px;
    font-size: 14px;
    text-align: center;
    border: 1px solid #fee2e2;
    line-height: 1.5;
}
.success-box {
    background-color: #f0fdf4;
    color: #16a34a;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 30px;
    font-size: 14px;
    text-align: center;
    border: 1px solid #dcfce7;
    line-height: 1.5;
}
.login-footer {
    text-align: center;
    margin-top: 30px;
    font-size: 14px;
    color: #64748b;
}
.login-footer a {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 600;
}
.login-footer a:hover {
    text-decoration: underline;
}
</style>

<main class="login-container">
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <h2>Reset Password</h2>
                <p>Choose a secure, strong new password to protect your account.</p>
            </div>

            <?php if ($error): ?>
                <div class="error-box"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-box"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($valid_token): ?>
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label>New Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="••••••••" required>
                        <span class="password-toggle" data-target="password">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" required>
                        <span class="password-toggle" data-target="confirm_password">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" name="reset_password" class="login-btn">Update Password</button>
            </form>
            <?php endif; ?>

            <div class="login-footer">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</main>

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

<?php include 'includes/footer.php'; ?>
