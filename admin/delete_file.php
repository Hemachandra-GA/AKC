<?php
include '../includes/auth_check.php';
include '../includes/db_connect.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $file_id = $_POST['file_id'] ?? '';

    if (!$file_id) {
        echo json_encode(['success' => false, 'message' => 'Missing file ID']);
        exit();
    }

    try {
        // 1. Get file details to delete from disk
        $stmt = $conn->prepare("SELECT file_path FROM booking_files WHERE id = :id");
        $stmt->execute(['id' => $file_id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $full_path = $file['file_path'];
            if (file_exists($full_path)) {
                unlink($full_path);
            }

            // 2. Delete record from database
            $delete = $conn->prepare("DELETE FROM booking_files WHERE id = :id");
            $delete->execute(['id' => $file_id]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'File not found in database']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
