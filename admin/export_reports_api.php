<?php
include '../includes/auth_check.php';
include '../includes/db_connect.php';

// Ensure user is an admin or super_admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'super_admin'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access Denied');
}

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Monthly_Report_' . $year . '_' . $month . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Booking ID', 'Date', 'Client Name', 'Client Email', 'Test Type', 'Status']);

$query = "
    SELECT id, created_at, fullname, email, sample_type, status 
    FROM test_bookings 
    WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->execute(['month' => $month, 'year' => $year]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        date('Y-m-d H:i:s', strtotime($row['created_at'])),
        $row['fullname'],
        $row['email'],
        $row['sample_type'],
        $row['status']
    ]);
}

fclose($output);
exit();
?>
