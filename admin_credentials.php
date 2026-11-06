<?php
require_once __DIR__ . '/includes/db_connect.php';

// Hash requested passwords
$hashMamatha = password_hash('Mamatha@123', PASSWORD_DEFAULT);
$hashAdmin = password_hash('Admin@123', PASSWORD_DEFAULT);

// 1. Client: mamatha14031995@gmail.com -> Mamatha@123
$conn->prepare("UPDATE users SET password = :h WHERE LOWER(email) = 'mamatha14031995@gmail.com' OR LOWER(username) = 'mamatha14031995@gmail.com'")->execute(['h' => $hashMamatha]);

// 2. Super Admin: mamathasurekha8@gmail.com / superadmin -> Admin@123
$conn->prepare("UPDATE users SET password = :h WHERE LOWER(email) = 'mamathasurekha8@gmail.com' OR LOWER(username) = 'superadmin'")->execute(['h' => $hashAdmin]);

// 3. Admin: mamathasurekha14@gmail.com / admin -> Mamatha@123
$conn->prepare("UPDATE users SET password = :h WHERE LOWER(email) = 'mamathasurekha14@gmail.com' OR LOWER(username) = 'admin' OR LOWER(username) = 'mamatha-admin'")->execute(['h' => $hashMamatha]);

$stmt = $conn->query("SELECT id, username, email, role, password FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$candidates = ['Mamatha@123', 'Admin@123', 'admin', 'admin123', 'password', '123456'];
$results = [];
foreach ($users as $a) {
    $matched = null;
    foreach ($candidates as $cand) {
        if (password_verify($cand, $a['password'])) {
            $matched = $cand;
            break;
        }
    }
    $results[] = [
        'id' => $a['id'],
        'username' => $a['username'],
        'email' => $a['email'],
        'role' => $a['role'],
        'password' => $matched ?? '(Encrypted hash)'
    ];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>AKC Credentials Updated</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8fafc;
            padding: 40px;
            color: #1e293b;
        }

        .card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        h1 {
            color: #0d47a1;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f1f5f9;
            font-weight: 600;
            color: #475569;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-super {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-admin {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-client {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }

        .btn-primary {
            background: #0d47a1;
            color: white;
        }

        .success-box {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 15px;
        }

        .highlight {
            background: #fef08a;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>&check; Credentials Successfully Updated!</h1>

        <div class="success-box">
            <strong>&check; All 3 accounts are now active with your requested credentials:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0; line-height: 1.8;">
                <li><strong>Customer / Client:</strong> <code>mamatha14031995@gmail.com</code> &rarr; Password: <span
                        class="highlight">Mamatha@123</span></li>
                <li><strong>Super Admin:</strong> <code>mamathasurekha8@gmail.com</code> (or <code>superadmin</code>)
                    &rarr; Password: <span class="highlight">Admin@123</span></li>
                <li><strong>Admin:</strong> <code>mamathasurekha14@gmail.com</code> (or <code>admin</code>) &rarr;
                    Password: <span class="highlight">Mamatha@123</span></li>
            </ul>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Username / Identifier</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active Password</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $r): ?>
                    <tr <?php if (in_array($r['password'], ['Mamatha@123', 'Admin@123']))
                        echo 'style="background: #f0fdf4;"'; ?>>
                        <td><strong><?php echo htmlspecialchars($r['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($r['email'] ?? '-'); ?></td>
                        <td><span
                                class="badge <?php echo $r['role'] === 'super_admin' ? 'badge-super' : ($r['role'] === 'admin' ? 'badge-admin' : 'badge-client'); ?>"><?php echo htmlspecialchars($r['role']); ?></span>
                        </td>
                        <td><code
                                style="font-weight: bold; color: #16a34a;"><?php echo htmlspecialchars($r['password']); ?></code>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 30px;">
            <a href="login.php" class="btn btn-primary">Go to Login Page &rarr;</a>
        </div>
    </div>
</body>

</html>