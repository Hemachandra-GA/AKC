<?php
include '../includes/auth_check.php';
include '../includes/db_connect.php';
require_once '../includes/activity_logger.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    $estimated_time = $_POST['estimated_time'] ?? '';
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');

    if (!$booking_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit();
    }

    // Validation: If status is 'Accept', estimated_time is required
    if ($status === 'Accept' && empty($estimated_time)) {
        echo json_encode(['success' => false, 'message' => 'Estimated time is required when accepting a booking.']);
        exit();
    }

    // Validation: If status is 'Cancel', rejection_reason is required
    if ($status === 'Cancel' && empty($rejection_reason)) {
        echo json_encode(['success' => false, 'message' => 'A rejection reason is required when rejecting a booking.']);
        exit();
    }

    // Validation: If status is 'Complete', at least one file is required
    // First, check if files are being uploaded now
    $has_new_files = (isset($_FILES['result_files']) && count($_FILES['result_files']['name']) > 0 && $_FILES['result_files']['error'][0] == 0);
    
    // Then check if there are already files in the DB for this booking
    if ($status === 'Complete' && !$has_new_files) {
        $checkFiles = $conn->prepare("SELECT COUNT(*) FROM booking_files WHERE booking_id = :bid");
        $checkFiles->execute(['bid' => $booking_id]);
        if ($checkFiles->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'At least one report file is required to complete this booking.']);
            exit();
        }
    }

    // Handle Multiple File Uploads
    if (isset($_FILES['result_files'])) {
        $target_dir = __DIR__ . "/../assets/uploads/reports/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $total_files = count($_FILES['result_files']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['result_files']['error'][$i] == 0) {
                $file_name = $_FILES['result_files']['name'][$i];
                $file_type = $_FILES['result_files']['type'][$i];
                $file_tmp = $_FILES['result_files']['tmp_name'][$i];
                
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $unique_name = "report_" . $booking_id . "_" . uniqid() . "." . $extension;
                $target_path = $target_dir . $unique_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    // Save file info to database
                    $file_sql = "INSERT INTO booking_files (booking_id, file_name, file_path, file_type) VALUES (:bid, :fname, :fpath, :ftype)";
                    $file_stmt = $conn->prepare($file_sql);
                    $file_stmt->execute([
                        'bid' => $booking_id,
                        'fname' => $unique_name,
                        'fpath' => $target_path,
                        'ftype' => $file_type
                    ]);
                }
            }
        }
    }

    try {
        // Fetch current status and details before updating
        $stmtOld = $conn->prepare("SELECT status, email, sample_type FROM test_bookings WHERE id = :id");
        $stmtOld->execute(['id' => $booking_id]);
        $old_booking = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if ($old_booking) {
            $curr_status = $old_booking['status'];
            if ($curr_status === 'Complete') {
                echo json_encode(['success' => false, 'message' => 'Completed bookings cannot be updated further.']);
                exit();
            }
            if ($curr_status === 'Cancel') {
                echo json_encode(['success' => false, 'message' => 'Rejected bookings cannot be updated further.']);
                exit();
            }
            if ($curr_status === 'Accept' && $status === 'Pending') {
                echo json_encode(['success' => false, 'message' => 'In Progress bookings cannot be changed back to Pending.']);
                exit();
            }
            if ($curr_status === 'Pending' && $status === 'Complete') {
                echo json_encode(['success' => false, 'message' => 'Pending bookings cannot be completed directly.']);
                exit();
            }
        }

        $sql = "UPDATE test_bookings SET status = :status";
        if ($status === 'Accept') {
            $sql .= ", accepted_at = NOW(), est_notification_sent = 0, alert_24h_sent = 0, alert_1h_sent = 0, alert_overdue_sent = 0";
            if (!empty($estimated_time)) {
                $formatted_time = str_replace('T', ' ', $estimated_time);
                $sql .= ", estimated_time = :estimated_time";
            }
        } elseif ($status === 'Complete') {
            $sql .= ", completed_at = NOW()";
        } elseif ($status === 'Cancel') {
            $sql .= ", rejected_at = NOW()";
            if (!empty($rejection_reason)) {
                $sql .= ", rejection_reason = :rejection_reason";
            }
        }
        $sql .= " WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $booking_id);
        
        if ($status === 'Accept' && !empty($estimated_time)) {
            $stmt->bindParam(':estimated_time', $formatted_time);
        }
        if ($status === 'Cancel' && !empty($rejection_reason)) {
            $stmt->bindParam(':rejection_reason', $rejection_reason);
        }
        
        if ($stmt->execute()) {
            // Fetch the new booking state for logging
            $stmtNew = $conn->prepare("SELECT status, estimated_time, rejection_reason FROM test_bookings WHERE id = :id");
            $stmtNew->execute(['id' => $booking_id]);
            $new_booking = $stmtNew->fetch(PDO::FETCH_ASSOC);

            // Log Admin Activity
            if ($old_booking) {
                logAdminActivity($conn, 'UPDATE', 'BOOKINGS', $booking_id, 
                    ['status' => $old_booking['status']], 
                    ['status' => $new_booking['status'], 'estimated_time' => $new_booking['estimated_time'], 'rejection_reason' => $new_booking['rejection_reason']]
                );
            }

            // Check status transition and insert notifications
            if ($old_booking && $old_booking['status'] !== $status) {
                $old_status = $old_booking['status'];
                $new_status = $status;
                
                $status_labels = [
                    'Pending' => 'Pending',
                    'Accept' => 'In Progress',
                    'Complete' => 'Completed',
                    'Cancel' => 'Rejected'
                ];
                $old_label = $status_labels[$old_status] ?? $old_status;
                $new_label = $status_labels[$new_status] ?? $new_status;
                
                $sample_type = $old_booking['sample_type'];
                
                // Client message
                $client_msg = "Your booking #$booking_id ($sample_type) status has been updated from $old_label to $new_label.";
                if ($new_status === 'Cancel' && !empty($rejection_reason)) {
                    $client_msg .= " Reason: $rejection_reason";
                }
                
                // Admin message
                $admin_msg = "Booking #$booking_id ($sample_type) status updated from $old_label to $new_label.";
                if ($new_status === 'Cancel' && !empty($rejection_reason)) {
                    $admin_msg .= " Reason: $rejection_reason";
                }
                
                // Find Client User ID
                $client_user_id = null;
                if (!empty($old_booking['email'])) {
                    $stmtUser = $conn->prepare("SELECT id FROM users WHERE username = :email");
                    $stmtUser->execute(['email' => $old_booking['email']]);
                    $client_user_id = $stmtUser->fetchColumn() ?: null;
                }
                
                // Insert for Client
                if ($client_user_id) {
                    $stmtInsertClient = $conn->prepare("INSERT INTO user_notifications (user_id, booking_id, title, message) VALUES (:uid, :bid, 'Booking Status Updated', :msg)");
                    $stmtInsertClient->execute([
                        'uid' => $client_user_id,
                        'bid' => $booking_id,
                        'msg' => $client_msg
                    ]);
                }
                
                // Insert for Admin(s) and Super Admin
                $admins = $conn->query("SELECT id FROM users WHERE role IN ('admin', 'super_admin')")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($admins as $admin_id) {
                    $stmtInsertAdmin = $conn->prepare("INSERT INTO user_notifications (user_id, booking_id, title, message) VALUES (:uid, :bid, 'Booking Status Updated', :msg)");
                    $stmtInsertAdmin->execute([
                        'uid' => $admin_id,
                        'bid' => $booking_id,
                        'msg' => $admin_msg
                    ]);
                }

                // Build base URL dynamically to prevent any 404 issue (handles both root and subdirectories)
                $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
                $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
                $script_dir = ($script_dir === '/' || $script_dir === '\\') ? '' : $script_dir;
                $base_url = $protocol . $host . $script_dir;

                // Trigger status email asynchronously in the background
                $cmd = 'start /B php ' . escapeshellarg(__DIR__ . '/../process_status_email_async.php') . ' ' . escapeshellarg($booking_id) . ' ' . escapeshellarg($old_status) . ' ' . escapeshellarg($base_url);
                pclose(popen($cmd, 'r'));
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
