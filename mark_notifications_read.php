<?php
session_start();
include 'includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'read_one') {
    $notification_id = $_POST['id'] ?? '';
    if (!$notification_id) {
        echo json_encode(['success' => false, 'message' => 'Missing notification ID']);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("UPDATE user_notifications SET is_read = 1 WHERE id = :id AND user_id = :uid");
        $stmt->execute(['id' => $notification_id, 'uid' => $user_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($action === 'read_all') {
    try {
        $stmt = $conn->prepare("UPDATE user_notifications SET is_read = 1 WHERE user_id = :uid");
        $stmt->execute(['uid' => $user_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
