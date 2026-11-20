<?php
session_start();
$role = $_SESSION['role'] ?? '';
session_unset();
session_destroy();

if (in_array($role, ['admin', 'super_admin'])) {
    header("Location: admin_login.php");
} else {
    header("Location: login.php");
}
exit();
