<?php
session_start();
include 'includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    // Fetch notifications
    $stmtNotify = $conn->prepare("SELECT * FROM user_notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 50");
    $stmtNotify->execute(['uid' => $user_id]);
    $notifications = $stmtNotify->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notifications as &$n) {
        $stmtB = $conn->prepare("SELECT * FROM test_bookings WHERE id = :bid");
        $stmtB->execute(['bid' => $n['booking_id']]);
        $b = $stmtB->fetch(PDO::FETCH_ASSOC);
        
        if ($b) {
            $stmtF = $conn->prepare("SELECT * FROM booking_files WHERE booking_id = :bid");
            $stmtF->execute(['bid' => $b['id']]);
            $b['attached_files'] = $stmtF->fetchAll(PDO::FETCH_ASSOC);
        }
        $n['booking_data'] = $b;
    }
    unset($n);

    // Also fetch booking data for client dashboard if requested
    $bookings = [];
    if (isset($_GET['fetch_bookings']) && $_GET['fetch_bookings'] == 1 && $role == 'client') {
        $user_email = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT * FROM test_bookings WHERE email = :email ORDER BY created_at DESC LIMIT 50");
        $stmt->execute(['email' => $user_email]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($bookings as &$booking) {
            $stmtFiles = $conn->prepare("SELECT * FROM booking_files WHERE booking_id = :id");
            $stmtFiles->execute(['id' => $booking['id']]);
            $booking['attached_files'] = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($booking);
    }

    echo json_encode([
        'success' => true, 
        'notifications' => $notifications,
        'bookings' => $bookings
    ]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
