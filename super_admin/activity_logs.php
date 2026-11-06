<?php
include '../includes/super_admin_auth_check.php';
include '../includes/db_connect.php';

// Pagination parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total records for pagination
$count_query = "SELECT COUNT(*) FROM admin_activity_logs";
$stmtCount = $conn->prepare($count_query);
$stmtCount->execute();
$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch logs
$logs = [];
try {
    $stmt = $conn->prepare("SELECT a.*, u.username, u.fullname, u.permissions FROM admin_activity_logs a JOIN users u ON a.admin_id = u.id ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function renderDiff($old_json, $new_json) {
    $old = $old_json ? json_decode($old_json, true) : [];
    $new = $new_json ? json_decode($new_json, true) : [];
    
    $html = '<div class="diff-container">';
    
    if (empty($old) && !empty($new)) {
        // Create
        foreach ($new as $k => $v) {
            if (is_array($v)) $v = json_encode($v);
            $html .= '<div class="diff-row add"><strong>' . $k . ':</strong> ' . htmlspecialchars((string)$v) . '</div>';
        }
    } elseif (!empty($old) && empty($new)) {
        // Delete
        foreach ($old as $k => $v) {
            if (is_array($v)) $v = json_encode($v);
            $html .= '<div class="diff-row remove"><strong>' . $k . ':</strong> ' . htmlspecialchars((string)$v) . '</div>';
        }
    } else {
        // Update
        $all_keys = array_unique(array_merge(array_keys($old ?: []), array_keys($new ?: [])));
        foreach ($all_keys as $k) {
            $v_old = $old[$k] ?? null;
            $v_new = $new[$k] ?? null;
            
            if (is_array($v_old)) $v_old = json_encode($v_old);
            if (is_array($v_new)) $v_new = json_encode($v_new);
            
            if ($v_old != $v_new) {
                $html .= '<div class="diff-row change"><strong>' . $k . ':</strong> ';
                $html .= '<span class="old-val">' . htmlspecialchars((string)$v_old) . '</span> &rarr; ';
                $html .= '<span class="new-val">' . htmlspecialchars((string)$v_new) . '</span>';
                $html .= '</div>';
            }
        }
    }
    
    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .log-action-CREATE { color: #10b981; font-weight: bold; }
        .log-action-UPDATE { color: #3b82f6; font-weight: bold; }
        .log-action-DELETE { color: #ef4444; font-weight: bold; }
        .diff-container { font-family: monospace; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .diff-row { padding: 4px; border-bottom: 1px solid #f1f5f9; }
        .diff-row:last-child { border-bottom: none; }
        .diff-row.add { color: #166534; background-color: #dcfce7; }
        .diff-row.remove { color: #991b1b; background-color: #fee2e2; }
        .diff-row.change { color: #1e293b; }
        .old-val { text-decoration: line-through; color: #ef4444; margin-right: 5px; }
        .new-val { color: #10b981; font-weight: bold; }
        .admin-name { font-weight: 600; color: #3b82f6; }
    </style>
</head>
<body class="admin-body-bg">
<div class="admin-wrapper">
    <?php 
    $current_page = 'activity_logs';
    $page_title = 'Activity Logs';
    include '../includes/super_admin_sidebar.php'; 
    ?>
    <main class="admin-main">
        <?php include '../includes/super_admin_topbar.php'; ?>
        <div class="admin-content">
            <div class="card table-card">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Admin Activity Logs</h3>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Date / Time</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Record ID</th>
                                <th style="width: 40%;">Changes (Before &rarr; After)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($logs) > 0): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td style="white-space:nowrap;"><?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?></td>
                                        <td>
                                            <div class="admin-name"><?php echo htmlspecialchars($log['fullname']); ?></div>
                                            <div style="font-size:11px; color:#64748b;"><?php echo htmlspecialchars($log['username']); ?></div>
                                            <?php
                                                $perms = [];
                                                if (!empty($log['permissions'])) {
                                                    $decoded = json_decode($log['permissions'], true);
                                                    if (is_array($decoded)) {
                                                        $perms = $decoded;
                                                    }
                                                }
                                                $display_perms = [];
                                                foreach ($perms as $p) {
                                                    if ($p == 'testings') $display_perms[] = 'Testing';
                                                    else if ($p == 'courses') $display_perms[] = 'Courses';
                                                    else if ($p == 'support_services') $display_perms[] = 'Support Services';
                                                    else $display_perms[] = ucfirst($p);
                                                }
                                                $perms_str = empty($display_perms) ? 'No Services' : implode(', ', $display_perms);
                                            ?>
                                            <div style="font-size:10px; color:#8b5cf6; margin-top:3px; font-weight:600;"><i class="fa-solid fa-layer-group" style="font-size:9px; margin-right:3px;"></i><?php echo htmlspecialchars($perms_str); ?></div>
                                        </td>
                                        <td class="log-action-<?php echo $log['action']; ?>"><?php echo $log['action']; ?></td>
                                        <td><span class="badge-type"><?php echo htmlspecialchars($log['module']); ?></span></td>
                                        <td>#<?php echo htmlspecialchars($log['record_id']); ?></td>
                                        <td>
                                            <?php echo renderDiff($log['old_data'], $log['new_data']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-state">No activity logs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 0): ?>
                <div class="pagination-container">
                    <div class="pagination-links" style="margin-left:auto;">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
                        <?php else: ?>
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="page-link <?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
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
