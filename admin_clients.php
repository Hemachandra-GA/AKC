<?php
include 'includes/auth_check.php';
include 'includes/db_connect.php';

// Fetch unread notifications count for sidebar badge
$sidebar_unread_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmtUnread = $conn->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = :uid AND is_read = 0");
        $stmtUnread->execute(['uid' => $_SESSION['user_id']]);
        $sidebar_unread_count = (int)$stmtUnread->fetchColumn();
    } catch(PDOException $e) {}
}

// Handle Filters
$search = $_GET['search'] ?? '';

// Pagination parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query for clients from the users table
$base_query = "FROM users u LEFT JOIN test_bookings b ON u.username = b.email WHERE u.role = 'client'";
$params = [];

if (!empty($search)) {
    $base_query .= " AND (u.fullname LIKE :search OR u.username LIKE :search OR b.company LIKE :search)";
    $params['search'] = "%$search%";
}

// Fetch total records for pagination
$count_query = "SELECT COUNT(DISTINCT u.id) " . $base_query;
$stmtCount = $conn->prepare($count_query);
$stmtCount->execute($params);
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch summary stats
$totalClients = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn();

// Fetch filtered clients with pagination
$query = "
    SELECT 
        u.username as email, 
        u.fullname, 
        u.phone, 
        MAX(b.company) as company, 
        COUNT(b.id) as total_bookings, 
        MAX(b.created_at) as last_booking 
    " . $base_query . " 
    GROUP BY u.id 
    ORDER BY last_booking DESC 
    LIMIT :limit OFFSET :offset
";
try {
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - AKC Admin</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .sidebar-badge { background-color: #ef4444; color: white; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 2px 6px; margin-left: auto; display: inline-block; }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <?php 
    $current_page = 'clients';
    $page_title = 'Clients';
    include 'includes/admin_sidebar.php'; 
    ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <?php include 'includes/admin_topbar.php'; ?>

        <!-- Content Body -->
        <div class="admin-content">
            <!-- Stats Row -->
            <div class="stats-row" style="grid-template-columns: repeat(2, 1fr);">
                <div class="stat-card border-blue">
                    <div class="stat-card-header">
                        <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
                        <span class="stat-badge badge-neutral">Directory</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $totalClients; ?></span>
                        <span class="stat-label">Total Unique Clients</span>
                    </div>
                </div>
                <div class="stat-card border-green">
                    <div class="stat-card-header">
                        <div class="stat-icon green"><i class="fa-solid fa-clipboard-list"></i></div>
                        <span class="stat-badge badge-neutral">Platform</span>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?php echo $conn->query("SELECT COUNT(*) FROM test_bookings")->fetchColumn(); ?></span>
                        <span class="stat-label">Total Bookings</span>
                    </div>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="card table-card">
                <div class="card-header">
                    <h3>Client Directory</h3>
                    <div class="header-actions">
                        <form method="GET" class="filter-form">
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="search" placeholder="Search name, email, company..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No</th>
                                <th>Client Name</th>
                                <th>Email Address</th>
                                <th>Company</th>
                                <th>Phone Number</th>
                                <th>Total Bookings</th>
                                <th>Last Booking Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clients) > 0): ?>
                                <?php 
                                $sno = $offset + 1;
                                foreach ($clients as $client): 
                                ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($client['fullname']); ?></strong></td>
                                        <td><span class="text-muted"><?php echo htmlspecialchars($client['email']); ?></span></td>
                                        <td><?php echo htmlspecialchars($client['company']); ?></td>
                                        <td><?php echo htmlspecialchars($client['phone']); ?></td>
                                        <td><span class="badge-type"><?php echo $client['total_bookings']; ?> booking(s)</span></td>
                                        <td><?php echo date('M d, Y', strtotime($client['last_booking'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-state">No clients found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 0): ?>
                <div class="pagination-container">
                    <div class="rows-per-page">
                        <form method="GET" action="" id="limitForm">
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <label>Rows per page:</label>
                            <select name="limit" onchange="document.getElementById('limitForm').submit()">
                                <option value="5" <?php if($limit == 5) echo 'selected'; ?>>5</option>
                                <option value="10" <?php if($limit == 10) echo 'selected'; ?>>10</option>
                                <option value="25" <?php if($limit == 25) echo 'selected'; ?>>25</option>
                                <option value="50" <?php if($limit == 50) echo 'selected'; ?>>50</option>
                            </select>
                        </form>
                    </div>
                    <div class="pagination-links">
                        <?php 
                        $queryString = "&search=".urlencode($search)."&limit=".$limit;
                        if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $queryString; ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $queryString; ?>" class="page-link <?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $queryString; ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

</body>
</html>
