<?php
include 'includes/auth_check.php';
include 'includes/db_connect.php';

// Ensure user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Fetch Volume Trend (Last 7 Days)
$volume_query = "
    SELECT DATE(created_at) as booking_date, COUNT(*) as count 
    FROM test_bookings 
    WHERE created_at >= DATE(NOW() - INTERVAL 7 DAY)
    GROUP BY DATE(created_at) 
    ORDER BY booking_date ASC
";
$volume_data = $conn->query($volume_query)->fetchAll(PDO::FETCH_ASSOC);

$dates = [];
$counts = [];
foreach ($volume_data as $row) {
    $dates[] = date('M d', strtotime($row['booking_date']));
    $counts[] = (int)$row['count'];
}

// 2. Fetch Sample Type Popularity
$type_query = "
    SELECT sample_type, COUNT(*) as count 
    FROM test_bookings 
    GROUP BY sample_type 
    ORDER BY count DESC 
    LIMIT 5
";
$type_data = $conn->query($type_query)->fetchAll(PDO::FETCH_ASSOC);

$types = [];
$type_counts = [];
foreach ($type_data as $row) {
    $types[] = empty($row['sample_type']) ? 'Unknown' : $row['sample_type'];
    $type_counts[] = (int)$row['count'];
}

// 3. Status Breakdown
$status_query = "
    SELECT status, COUNT(*) as count 
    FROM test_bookings 
    GROUP BY status
";
$status_data = $conn->query($status_query)->fetchAll(PDO::FETCH_ASSOC);

$statuses = [];
$status_counts = [];
foreach ($status_data as $row) {
    $statuses[] = empty($row['status']) ? 'Pending' : $row['status'];
    $status_counts[] = (int)$row['count'];
}

// 4. Top Clients (Requires join if user_id exists, or we use email/fullname if it's stored directly)
// Wait, looking at admin_dashboard.php, it searches by fullname/email directly in test_bookings. 
// So let's group by email directly from test_bookings!
$client_query = "
    SELECT email, MAX(fullname) as fullname, COUNT(*) as count 
    FROM test_bookings 
    WHERE email IS NOT NULL AND email != ''
    GROUP BY email
    ORDER BY count DESC 
    LIMIT 5
";
$top_clients = $conn->query($client_query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Reports - AKC Admin</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: 1px solid #f1f5f9;
        }
        .chart-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chart-card h3 i {
            color: #3b82f6;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        /* Top Clients List */
        .client-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .client-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .client-list li:last-child {
            border-bottom: none;
        }
        .client-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .client-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .client-details h4 {
            margin: 0;
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
        }
        .client-details p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #64748b;
        }
        .client-score {
            font-weight: 700;
            color: #3b82f6;
            background: #eff6ff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
        }
        
        /* Responsive Reports */
        @media (max-width: 991px) {
            .reports-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .chart-card {
                padding: 15px;
            }
            .chart-container {
                height: 250px;
            }
            .chart-card.full-width .chart-container {
                height: 300px !important;
            }
        }
    </style>
</head>
<body class="admin-body-bg">
<div class="admin-wrapper">
    <?php 
    $current_page = 'reports';
    $page_title = 'Analytics & Reports';
    include 'includes/admin_sidebar.php'; 
    ?>

    <main class="admin-main">
        <?php include 'includes/admin_topbar.php'; ?>

        <div class="admin-content">
            <div class="reports-grid">
                
                <!-- Volume Trend Chart -->
                <div class="chart-card full-width">
                    <h3><i class="fa-solid fa-chart-area"></i> Test Request Volume (Last 7 Days)</h3>
                    <div class="chart-container" style="height: 350px;">
                        <canvas id="volumeChart"></canvas>
                    </div>
                </div>

                <!-- Test Popularity -->
                <div class="chart-card">
                    <h3><i class="fa-solid fa-flask"></i> Popular Test Types</h3>
                    <div class="chart-container">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>

                <!-- Status Breakdown -->
                <div class="chart-card">
                    <h3><i class="fa-solid fa-chart-pie"></i> Current Pipeline Status</h3>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Top Clients -->
                <div class="chart-card full-width">
                    <h3><i class="fa-solid fa-ranking-star"></i> Top Clients by Volume</h3>
                    <ul class="client-list">
                        <?php if (empty($top_clients)): ?>
                            <li style="justify-content: center; color: #64748b; font-size: 14px;">No client data available.</li>
                        <?php else: ?>
                            <?php foreach ($top_clients as $client): ?>
                                <li>
                                    <div class="client-info">
                                        <div class="client-avatar">
                                            <?php echo strtoupper(substr($client['fullname'] ?? 'C', 0, 2)); ?>
                                        </div>
                                        <div class="client-details">
                                            <h4><?php echo htmlspecialchars($client['fullname']); ?></h4>
                                            <p><?php echo htmlspecialchars($client['email']); ?></p>
                                        </div>
                                    </div>
                                    <div class="client-score">
                                        <?php echo $client['count']; ?> Requests
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
    // Data from PHP
    const volumeDates = <?php echo json_encode($dates); ?>;
    const volumeCounts = <?php echo json_encode($counts); ?>;
    
    const typeNames = <?php echo json_encode($types); ?>;
    const typeCounts = <?php echo json_encode($type_counts); ?>;
    
    const statusNames = <?php echo json_encode($statuses); ?>;
    const statusCounts = <?php echo json_encode($status_counts); ?>;

    // Common styling configs
    Chart.defaults.font.family = "'Montserrat', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. Volume Line Chart
    new Chart(document.getElementById('volumeChart'), {
        type: 'line',
        data: {
            labels: volumeDates,
            datasets: [{
                label: 'New Bookings',
                data: volumeCounts,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: '#f1f5f9', drawBorder: false }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });

    // 2. Test Popularity Bar Chart
    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: typeNames,
            datasets: [{
                label: 'Requests',
                data: typeCounts,
                backgroundColor: '#10b981',
                borderRadius: 6,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: '#f1f5f9', drawBorder: false }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });

    // 3. Status Pipeline Doughnut Chart
    // Map status names to specific colors for visual consistency
    const statusColors = statusNames.map(status => {
        const s = status.toLowerCase();
        if(s === 'complete' || s === 'completed') return '#22c55e';
        if(s === 'pending') return '#f97316';
        if(s === 'accept' || s === 'in progress') return '#d97706';
        if(s === 'cancel' || s === 'rejected') return '#ef4444';
        return '#94a3b8';
    });

    // Format labels nicely
    const prettyStatusNames = statusNames.map(status => {
        const s = status.toLowerCase();
        if(s === 'accept') return 'In Progress';
        if(s === 'cancel') return 'Rejected';
        return status;
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: prettyStatusNames,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                }
            }
        }
    });
</script>
</body>
</html>
