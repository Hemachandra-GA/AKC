<?php
include '../includes/client_auth_check.php';
include '../includes/db_connect.php';

$user_email = $_SESSION['username'];
$fullname = $_SESSION['fullname'];

// Notification data is now handled globally via includes/client_notifications_sync.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications - AKC Client</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .client-sidebar { background-color: #1e293b; }
        .sidebar-logo-text { color: white; font-weight: 700; font-size: 20px; }
        .client-badge { background: #3b82f6; color: white; padding: 2px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; }
        .sidebar-badge { background-color: #ef4444; color: white; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 2px 6px; margin-left: auto; display: inline-block; }
        .notification-card-list { display: flex; flex-direction: column; gap: 15px; padding: 10px 0; }
        .notification-card-item { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s ease; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.01); }
        .notification-card-item:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-color: #cbd5e1; }
        .notification-card-item.unread { background: #eff6ff; border-color: #bfdbfe; }
        .notification-card-content { display: flex; align-items: flex-start; gap: 15px; }
        .notification-card-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .notification-card-icon.unread { background: #dbeafe; color: #2563eb; }
        .notification-card-icon.read { background: #f1f5f9; color: #64748b; }
        .notification-card-text { display: flex; flex-direction: column; gap: 5px; }
        .notification-card-title { font-size: 15px; font-weight: 700; color: #1e293b; }
        .notification-card-msg { font-size: 14px; color: #475569; line-height: 1.4; }
        .notification-card-time { font-size: 12px; color: #94a3b8; font-weight: 500; display: flex; align-items: center; gap: 5px; }
        .notification-card-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
        .new-badge { background: #3b82f6; color: white; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
        .view-btn-notif { background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; color: #475569; padding: 8px 15px; border-radius: 8px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .view-btn-notif:hover { background: #2563eb; color: white; border-color: #2563eb; }
        .notifications-empty-state { text-align: center; padding: 60px 20px; color: #64748b; }
        .notifications-empty-state i { font-size: 48px; color: #cbd5e1; margin-bottom: 15px; }
        .notifications-empty-state h3 { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        
        /* View Modal details */
        .view-row { display: flex; margin-bottom: 12px; font-size: 14px; }
        .view-label { width: 120px; font-weight: 600; color: #64748b; flex-shrink: 0; }
        .view-value { color: #1e293b; font-weight: 500; word-break: break-all; }
        .view-file-item { width: 100%; margin-bottom: 8px; }
        .view-file-link { display: flex; align-items: center; gap: 12px; padding: 10px 15px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-size: 14px; transition: all 0.2s; }
        .view-file-link:hover { background-color: #f1f5f9; border-color: #3b82f6; color: #3b82f6; transform: translateX(5px); }
        
        /* Mobile Responsive Notifications */
        @media (max-width: 600px) {
            .notification-card-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .notification-card-meta {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                border-top: 1px solid #f1f5f9;
                padding-top: 15px;
                margin-top: 5px;
            }
            .notification-card-meta .new-badge {
                align-self: flex-start;
            }
            .view-btn-notif {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="admin-body-bg">

<div class="admin-wrapper">
    <!-- Left Sidebar -->
    <?php include '../includes/client_sidebar.php'; ?>

    <!-- Right Content Area -->
    <main class="admin-main">
        <!-- Top Bar -->
        <?php 
        $page_title = 'Account / <span class="blue-text">My Notifications</span>';
        include '../includes/client_topbar.php'; 
        ?>

        <!-- Content Body -->
        <div class="admin-content">
            <div class="card">
                <div class="card-header">
                    <h3 style="font-weight: 700; font-size: 18px; color: #1e293b;"><i class="fa-solid fa-bell" style="color:#2563eb; margin-right:8px;"></i> Notification History</h3>
                    <button id="markAllReadPageBtn" class="btn-export" style="background-color: #3b82f6; color: white; border-color: #3b82f6;"><i class="fa-solid fa-check-double"></i> Mark All as Read</button>
                </div>
                <div style="padding: 25px;">
                    <div class="notification-card-list" id="pageNotificationList">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- View Booking Modal (Client Dashboard layout match) -->
<div id="viewModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Booking Details - #<span id="view_id_title"></span></h3>
            <span class="close" onclick="closeViewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="view-grid">
                <div class="view-section">
                    <h4><i class="fa-solid fa-flask-vial"></i> Sample Information</h4>
                    <div class="view-row">
                        <span class="view-label">Type:</span>
                        <span class="view-value" id="view_sample_type"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Quantity:</span>
                        <span class="view-value"><span id="view_num_samples"></span> samples</span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Description:</span>
                        <span class="view-value" id="view_sample_desc"></span>
                    </div>
                </div>
                
                <div class="view-section">
                    <h4><i class="fa-solid fa-user"></i> Customer Details</h4>
                    <div class="view-row">
                        <span class="view-label">Name:</span>
                        <span class="view-value" id="view_fullname"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Company:</span>
                        <span class="view-value" id="view_company"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Email:</span>
                        <span class="view-value" id="view_email"></span>
                    </div>
                    <div class="view-row">
                        <span class="view-label">Phone:</span>
                        <span class="view-value" id="view_phone"></span>
                    </div>
                </div>

                <div class="view-section full-width">
                    <h4><i class="fa-solid fa-signal"></i> Booking Status</h4>
                    <div class="view-row">
                        <span class="view-label">Status:</span>
                        <span id="view_status_badge" class="status-badge" style="display: inline-block;"></span>
                    </div>
                    <div class="view-row" id="view_status_time_row">
                        <span class="view-label" id="view_status_time_label">Updated At:</span>
                        <span class="view-value" id="view_status_time"></span>
                    </div>
                </div>
                
                <div class="view-section full-width">
                    <h4><i class="fa-solid fa-clipboard-list"></i> Special Requirements</h4>
                    <div id="view_requirements" style="font-size: 14px; color: #475569; line-height: 1.6;"></div>
                </div>

                <div class="view-section full-width" id="view_rejection_section" style="display:none;">
                    <h4><i class="fa-solid fa-circle-xmark" style="color:#ef4444;"></i> Rejection Reason</h4>
                    <div id="view_rejection_reason" style="font-size: 14px; color: #ef4444; line-height: 1.6; background:#fff1f2; padding:12px 15px; border-radius:8px; border:1px solid #fecdd3;"></div>
                </div>

                <div class="view-section full-width" id="view_estimated_section" style="display:none;">
                    <h4><i class="fa-solid fa-clock" style="color:#f59e0b;"></i> Estimated Completion Time</h4>
                    <div id="view_estimated_time" style="font-size: 14px; color: #92400e; line-height: 1.6; background:#fffbeb; padding:12px 15px; border-radius:8px; border:1px solid #fde68a; font-weight:600;"></div>
                </div>

                <div class="view-section full-width" id="view_files_section">
                    <h4><i class="fa-solid fa-file-arrow-down"></i> Test Reports & Files</h4>
                    <div id="view_files_list" class="booking-files-list"></div>
                </div>
                
                <div class="view-section full-width" id="view_photo_section">
                    <h4><i class="fa-solid fa-image"></i> Sample Photo</h4>
                    <img id="view_sample_photo" src="" alt="Sample Photo" class="view-full-img">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script>
const dbStatusNotifications = <?php echo json_encode($db_notif); ?>;

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    if (ext === 'pdf') return 'fa-file-pdf';
    if (['doc', 'docx'].includes(ext)) return 'fa-file-word';
    if (['xls', 'xlsx', 'csv'].includes(ext)) return 'fa-file-excel';
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext)) return 'fa-file-image';
    if (['zip', 'rar', '7z'].includes(ext)) return 'fa-file-zipper';
    return 'fa-file';
}

function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return 'N/A';
    const dt = new Date(dateTimeStr.replace(' ', 'T'));
    return dt.toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
}

function openViewModal(booking) {
    document.getElementById('view_id_title').innerText = booking.id;
    document.getElementById('view_sample_type').innerText = booking.sample_type;
    document.getElementById('view_num_samples').innerText = booking.num_samples;
    document.getElementById('view_sample_desc').innerText = booking.sample_desc || 'No description provided';
    document.getElementById('view_fullname').innerText = booking.fullname;
    document.getElementById('view_company').innerText = booking.company || 'N/A';
    document.getElementById('view_email').innerText = booking.email;
    document.getElementById('view_phone').innerText = booking.phone;
    document.getElementById('view_requirements').innerText = booking.requirements || 'No special requirements';
    
    // Handle Status and Date
    const statusBadge = document.getElementById('view_status_badge');
    const statusTimeRow = document.getElementById('view_status_time_row');
    const statusTimeLabel = document.getElementById('view_status_time_label');
    const statusTime = document.getElementById('view_status_time');

    statusBadge.className = 'status-badge ' + booking.status.toLowerCase();
    
    let displayStatus = booking.status;
    let statusLabel = 'Updated Status On:';
    let statusDate = null;
    
    if (booking.status === 'Pending') {
        displayStatus = 'Pending';
        statusLabel = 'Booked On:';
        statusDate = booking.created_at;
    } else if (booking.status === 'Accept') {
        displayStatus = 'In Progress';
        statusLabel = 'Updated Status On:';
        statusDate = booking.accepted_at;
    } else if (booking.status === 'Complete') {
        displayStatus = 'Complete';
        statusLabel = 'Completed On:';
        statusDate = booking.completed_at;
    } else if (booking.status === 'Cancel') {
        displayStatus = 'Rejected';
        statusLabel = 'Rejected On:';
        statusDate = booking.rejected_at;
    }
    
    statusBadge.innerText = displayStatus.toUpperCase();
    statusTimeLabel.innerText = statusLabel;
    statusTime.innerText = formatDateTime(statusDate);
    statusTimeRow.style.display = 'flex';
    
    // Handle Rejection Reason
    const rejectionSection = document.getElementById('view_rejection_section');
    if (booking.status === 'Cancel' && booking.rejection_reason) {
        document.getElementById('view_rejection_reason').innerText = booking.rejection_reason;
        rejectionSection.style.display = 'block';
    } else {
        rejectionSection.style.display = 'none';
    }
    
    // Handle Photos
    const photoImg = document.getElementById('view_sample_photo');
    if (booking.sample_photo) {
        photoImg.src = '../assets/uploads/' + booking.sample_photo;
        document.getElementById('view_photo_section').style.display = 'block';
    } else {
        document.getElementById('view_photo_section').style.display = 'none';
    }

    // Handle Attached Files
    const filesList = document.getElementById('view_files_list');
    filesList.innerHTML = '';
    if (booking.status === 'Complete' && booking.attached_files && booking.attached_files.length > 0) {
        document.getElementById('view_files_section').style.display = 'block';
        booking.attached_files.forEach(file => {
            let icon = getFileIcon(file.file_name);
            const fileItem = document.createElement('div');
            fileItem.className = 'view-file-item';
            fileItem.innerHTML = `
                <a href="../assets/uploads/reports/${file.file_name}" target="_blank" class="view-file-link">
                    <i class="fa-solid ${icon}"></i>
                    <span>${file.file_name}</span>
                </a>
            `;
            filesList.appendChild(fileItem);
        });
    } else {
        document.getElementById('view_files_section').style.display = 'none';
    }
    
    // Show estimated time only for Accept (In Progress)
    const estTimeSection = document.getElementById('view_estimated_section');
    if (estTimeSection) {
        if (booking.status === 'Accept' && booking.estimated_time) {
            const dt = new Date(booking.estimated_time.replace(' ', 'T'));
            const formatted = dt.toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
            document.getElementById('view_estimated_time').innerText = formatted;
            estTimeSection.style.display = 'block';
        } else {
            estTimeSection.style.display = 'none';
        }
    }
    
    document.getElementById('viewModal').style.display = "block";
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = "none";
}

// Logic handled globally by includes/client_notifications_sync.php

// Close modal when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewModal');
    if (event.target == viewModal) closeViewModal();
}
</script>

</body>
</html>
