<?php
include '../includes/auth_check.php';
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['id'] ?? '';

    if (!$booking_id) {
        echo json_encode(['success' => false, 'message' => 'Missing ID']);
        exit();
    }

require_once '../includes/activity_logger.php';

    try {
        // 1. Get file paths to delete physical files
        $stmt = $conn->prepare("SELECT * FROM test_bookings WHERE id = :id");
        $stmt->execute(['id' => $booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        $fileStmt = $conn->prepare("SELECT file_name FROM booking_files WHERE booking_id = :id");
        $fileStmt->execute(['id' => $booking_id]);
        $files = $fileStmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Delete the record (booking_files will be deleted automatically due to ON DELETE CASCADE)
        $deleteStmt = $conn->prepare("DELETE FROM test_bookings WHERE id = :id");
        if ($deleteStmt->execute(['id' => $booking_id])) {
            
            logAdminActivity($conn, 'DELETE', 'BOOKINGS', $booking_id, $booking, null);
            
            // 3. Physically delete the files
            if ($booking && $booking['sample_photo']) {
                @unlink(__DIR__ . "/../assets/uploads/" . $booking['sample_photo']);
            }
            foreach ($files as $file) {
                @unlink(__DIR__ . "/../assets/uploads/reports/" . $file['file_name']);
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete from database']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
