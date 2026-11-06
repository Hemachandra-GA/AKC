<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$perms = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];

if (in_array('testings', $perms)) {
    header("Location: testing_dashboard.php");
} else if (in_array('courses', $perms)) {
    header("Location: courses_dashboard.php");
} else if (in_array('support_services', $perms)) {
    header("Location: support_dashboard.php");
} else {
    header("Location: profile_settings.php");
}
exit();
?>
