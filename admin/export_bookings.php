<?php
include '../includes/auth_check.php';
include '../includes/db_connect.php';

// Get current filters to export matching data
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build Query (matching the dashboard logic)
$query = "SELECT * FROM test_bookings WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (fullname LIKE :search OR email LIKE :search OR phone LIKE :search OR sample_type LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($status_filter)) {
    $query .= " AND status = :status";
    $params['status'] = $status_filter;
}

if (!empty($date_from) && !empty($date_to)) {
    $query .= " AND DATE(created_at) BETWEEN :date_from AND :date_to";
    $params['date_from'] = $date_from;
    $params['date_to'] = $date_to;
}

$query .= " ORDER BY created_at DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=bookings_export_' . date('Y-m-d') . '.csv');

    // Create file pointer
    $output = fopen('php://output', 'w');

    // Add CSV headers (Every single column/data)
    fputcsv($output, [
        'ID', 
        'Full Name', 
        'Email', 
        'Phone', 
        'Company', 
        'Sample Type', 
        'Num Samples', 
        'Sample Description',
        'Special Requirements',
        'Sample Photo Filename',
        'Status', 
        'Date Booked',
        'Accepted At',
        'Completed At',
        'Rejected At',
        'Rejection Reason',
        'Estimated Completion Time'
    ]);

    // Add data rows
    foreach ($bookings as $row) {
        fputcsv($output, [
            $row['id'],
            $row['fullname'],
            $row['email'],
            $row['phone'],
            $row['company'],
            $row['sample_type'],
            $row['num_samples'],
            $row['sample_desc'],
            $row['requirements'],
            $row['sample_photo'],
            $row['status'],
            $row['created_at'],
            $row['accepted_at'],
            $row['completed_at'],
            $row['rejected_at'],
            $row['rejection_reason'],
            $row['estimated_time']
        ]);
    }

    fclose($output);
    exit();

} catch(PDOException $e) {
    die("Export Error: " . $e->getMessage());
}
?>
