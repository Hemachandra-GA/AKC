<?php
function logAdminActivity($conn, $action, $module, $record_id, $old_data, $new_data) {
    if (!isset($_SESSION['user_id'])) return false;
    
    $admin_id = $_SESSION['user_id'];
    
    // Only log if something changed for UPDATE
    if ($action === 'UPDATE') {
        if ($old_data == $new_data) {
            return true; // Nothing changed
        }
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO admin_activity_logs (admin_id, action, module, record_id, old_data, new_data) VALUES (:admin_id, :action, :module, :record_id, :old_data, :new_data)");
        $stmt->execute([
            'admin_id' => $admin_id,
            'action' => $action,
            'module' => $module,
            'record_id' => $record_id,
            'old_data' => $old_data ? json_encode($old_data) : null,
            'new_data' => $new_data ? json_encode($new_data) : null
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Activity Logger Error: " . $e->getMessage());
        return false;
    }
}
?>
