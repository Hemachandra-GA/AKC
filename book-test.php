<?php
require_once 'includes/db_connect.php';
require_once 'includes/mail_sender.php';

$pageTitle = "Book a Test | AKC - NABL Accredited Lab";
$metaDescription = "Book your testing services online with AKC. Fast turnaround, accredited reports, and full confidentiality.";
$metaKeywords = "book test, lab test booking, AKC testing services";

// Handle form submission
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_num = trim($_POST['phone'] ?? '');
    $country_code = trim($_POST['country_code'] ?? '+91');
    $phone = (!empty($country_code) && strpos($phone_num, '+') === false) ? ($country_code . ' ' . $phone_num) : $phone_num;
    $company = trim($_POST['company'] ?? '');
    $sample_type = trim($_POST['sample_type'] ?? '');
    $num_samples = trim($_POST['num_samples'] ?? '1');
    $sample_desc = trim($_POST['sample_desc'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $discipline_input = trim($_POST['discipline'] ?? '');
    $product_input = trim($_POST['product'] ?? '');
    $testing_type = trim($_POST['testing_type'] ?? '');

    if (empty($fullname) || empty($email) || empty($phone_num) || empty($sample_type) || empty($sample_desc)) {
        $error_message = "Please fill in all required fields (Name, Email, Phone, Sample Type, and Sample Description).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please provide a valid email address.";
    } else {
        try {
            // 1. Handle file upload for sample_photo
            $sample_photo_name = null;
            if (isset($_FILES['sample_photo']) && $_FILES['sample_photo']['error'] === UPLOAD_ERR_OK) {
                $tmp_file = $_FILES['sample_photo']['tmp_name'];
                $upload_dir = __DIR__ . '/assets/uploads/samples/';
                if (!file_exists($upload_dir)) {
                    @mkdir($upload_dir, 0777, true);
                }
                $file_info = pathinfo($_FILES['sample_photo']['name']);
                $ext = strtolower($file_info['extension'] ?? 'jpg');
                $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
                if (in_array($ext, $allowed_exts)) {
                    $isVercel = isset($_SERVER['VERCEL']) || getenv('VERCEL') || (PHP_OS_FAMILY !== 'Windows' && is_dir('/tmp'));
                    $file_size = @filesize($tmp_file);

                    // For Vercel serverless or files under 4MB, store as Base64 data URI so it renders permanently without relying on CDN filesystem persistence
                    if ($isVercel || ($file_size !== false && $file_size < 4 * 1024 * 1024)) {
                        $raw_data = @file_get_contents($tmp_file);
                        if ($raw_data !== false) {
                            $mime = (function_exists('mime_content_type')) ? @mime_content_type($tmp_file) : ('image/' . $ext);
                            if (!$mime)
                                $mime = ($ext === 'pdf') ? 'application/pdf' : 'image/' . $ext;
                            $sample_photo_name = 'data:' . $mime . ';base64,' . base64_encode($raw_data);
                        }
                    }

                    $unique_name = uniqid('sample_') . '.' . $ext;
                    $target_file = $upload_dir . $unique_name;
                    if (@move_uploaded_file($tmp_file, $target_file)) {
                        if (!$sample_photo_name) {
                            $sample_photo_name = $unique_name;
                        }
                        @copy($target_file, __DIR__ . '/assets/uploads/' . $unique_name);
                    }
                }
            }

            // 2. Format detailed requirements string
            $req_parts = [];
            if (!empty($discipline_input)) {
                $req_parts[] = "Discipline: " . $discipline_input;
            }
            if (!empty($product_input)) {
                $req_parts[] = "Product Category: " . $product_input;
            }
            if (!empty($testing_type)) {
                $req_parts[] = "Testing Type: " . $testing_type;
            }
            if (!empty($requirements)) {
                $req_parts[] = "Notes: " . $requirements;
            }
            $full_requirements = implode(" | ", $req_parts);

            // 3. Insert into test_bookings table
            $stmt = $conn->prepare("
                INSERT INTO test_bookings (
                    sample_type, num_samples, sample_desc, sample_photo,
                    fullname, company, email, phone, requirements, status, created_at
                ) VALUES (
                    :sample_type, :num_samples, :sample_desc, :sample_photo,
                    :fullname, :company, :email, :phone, :requirements, 'Pending', NOW()
                )
            ");
            $stmt->execute([
                'sample_type' => $sample_type,
                'num_samples' => $num_samples,
                'sample_desc' => $sample_desc,
                'sample_photo' => $sample_photo_name,
                'fullname' => $fullname,
                'company' => $company,
                'email' => $email,
                'phone' => $phone,
                'requirements' => $full_requirements,
            ]);
            $booking_id = $conn->lastInsertId();

            // 4. Notifications & Client Account Creation with Auto-Generated Credentials
            $stmtUser = $conn->prepare("SELECT id, username, email, role FROM users WHERE LOWER(email) = LOWER(:email) OR LOWER(username) = LOWER(:email) LIMIT 1");
            $stmtUser->execute(['email' => $email]);
            $existing_user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            // Generate a secure, user-friendly temporary password (e.g. AKC@74921)
            $digits = str_pad((string)rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            $plain_password = 'AKC@' . $digits;
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
            $is_new_account = false;

            if ($existing_user) {
                $client_user_id = $existing_user['id'];
                // For client users, refresh the temporary password so they can log in effortlessly with the credentials sent in this email
                if (empty($existing_user['role']) || $existing_user['role'] === 'client' || $existing_user['role'] === 'user') {
                    $updUser = $conn->prepare("
                        UPDATE users 
                        SET password = :password,
                            fullname = COALESCE(NULLIF(fullname, ''), :fullname),
                            phone = COALESCE(NULLIF(phone, ''), :phone),
                            is_active = 1
                        WHERE id = :id
                    ");
                    $updUser->execute([
                        'password' => $hashed_password,
                        'fullname' => $fullname,
                        'phone'    => $phone,
                        'id'       => $client_user_id
                    ]);
                } else {
                    // Do not overwrite password for admin or super_admin
                    $plain_password = null;
                }
            } else {
                // Auto-create new Client account
                $createStmt = $conn->prepare("
                    INSERT INTO users (username, email, password, role, fullname, phone, is_active, created_at)
                    VALUES (:username, :email, :password, 'client', :fullname, :phone, 1, datetime('now'))
                ");
                $createStmt->execute([
                    'username' => $email,
                    'email'    => $email,
                    'password' => $hashed_password,
                    'fullname' => $fullname,
                    'phone'    => $phone
                ]);
                $client_user_id = $conn->lastInsertId();
                $is_new_account = true;
            }

            if ($client_user_id) {
                $stmtNotif = $conn->prepare("
                    INSERT INTO user_notifications (user_id, booking_id, title, message)
                    VALUES (:uid, :bid, 'Test Booked Successfully', :msg)
                ");
                $stmtNotif->execute([
                    'uid' => $client_user_id,
                    'bid' => $booking_id,
                    'msg' => "Your test request (#{$booking_id}) for '{$sample_type}' has been successfully submitted."
                ]);
            }

            // For all Admins
            $admins = $conn->query("SELECT id FROM users WHERE role IN ('admin', 'super_admin')")->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($admins)) {
                $stmtAdminNotif = $conn->prepare("
                    INSERT INTO user_notifications (user_id, booking_id, title, message)
                    VALUES (:uid, :bid, 'New Test Request', :msg)
                ");
                foreach ($admins as $admin_id) {
                    $stmtAdminNotif->execute([
                        'uid' => $admin_id,
                        'bid' => $booking_id,
                        'msg' => "A new test request (#{$booking_id}) for '{$sample_type}' has been created by {$fullname}."
                    ]);
                }
            }

            // 5. Send Test Confirmation Email to Customer & Notification to Admin
            $booking_payload = [
                'id' => $booking_id,
                'fullname' => $fullname,
                'company' => $company,
                'email' => $email,
                'phone' => $phone,
                'sample_type' => $sample_type,
                'num_samples' => $num_samples,
                'sample_desc' => $sample_desc,
                'requirements' => $full_requirements,
                'is_new_account' => $is_new_account,
                'username' => $email,
                'password' => $plain_password,
            ];

            akc_send_booking_confirmation($booking_payload);

            $forgot_pwd_url = 'forgot-password.php';
            $client_email = htmlspecialchars($email);
            $booking_ref = htmlspecialchars($booking_id);

            $infoBox = "<div style='margin-top: 14px; padding: 15px 18px; background: #f0f7ff; border: 1.5px solid #bfdbfe; border-radius: 10px; font-size: 14px; line-height: 1.6; color: #1e3a8a; text-align: left;'>
                <div style='margin-bottom: 10px; font-weight: 500;'>
                    <i class='fa-solid fa-envelope' style='color: #0A58CA; font-size: 16px; margin-right: 8px;'></i>
                    You will receive your login credentials to your email: <strong style='color: #0f172a; text-decoration: underline; font-size: 14.5px;'>{$client_email}</strong>
                </div>
                <div style='padding-top: 10px; border-top: 1px dashed #93c5fd; color: #475569; font-size: 13.5px;'>
                    <i class='fa-solid fa-circle-info' style='color: #0A58CA; margin-right: 6px;'></i>
                    If you didn't receive any email, <a href='{$forgot_pwd_url}' target='_blank' style='color: #0A58CA; font-weight: 700; text-decoration: underline;'>reset your password from this link</a> and use your email (<strong style='color: #1e293b;'>{$client_email}</strong>) for your username.
                </div>
            </div>";

            $success_message = "Your test booking request (Ref #" . $booking_ref . ") has been submitted successfully!" . $infoBox;

        } catch (Throwable $e) {
            $error_message = "Error submitting booking: " . $e->getMessage();
        }
    }
}

$isPopup = (isset($_GET['popup']) && $_GET['popup'] == '1') || (isset($_POST['popup']) && $_POST['popup'] == '1');

if (!$isPopup) {
    include 'includes/header.php';
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Request a Test</title>
        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="assets/css/style_hemachandra.css">
        <link rel="stylesheet" href="assets/css/technical-support.css">
        <style>
            body {
                background: #ffffff !important;
                padding: 15px !important;
                margin: 0 !important;
                font-family: 'DM Sans', sans-serif !important;
            }

            main {
                padding: 0 !important;
                background: transparent !important;
            }

            .ts-booking-section {
                padding: 0 !important;
                margin: 0 !important;
            }

            .ts-booking-container {
                display: block !important;
                max-width: 900px !important;
                margin: 0 auto !important;
            }

            .booking-left {
                display: none !important;
            }

            .booking-right {
                width: 100% !important;
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                background: #ffffff !important;
            }
        </style>
    </head>

    <body>
        <?php
}

// Fetch all disciplines for the dropdown in custom order
$disciplinesStmt = $conn->query("
    SELECT id, name, slug 
    FROM disciplines 
    ORDER BY CASE WHEN name = 'Chemical' THEN 1
                  WHEN name = 'Mechanical' THEN 2
                  WHEN name = 'Thermal' THEN 3
                  WHEN name = 'Rheological' THEN 4
                  WHEN name = 'Optical' THEN 5
                  WHEN name = 'Material Characterization' THEN 6
                  WHEN name = 'Material Characterisation' THEN 6
                  WHEN name = 'Biological' THEN 7
                  WHEN name = 'Electrical' THEN 8
                  WHEN name = 'Electricals & Electronics' THEN 8
                  ELSE 99 END ASC, name ASC
");
$disciplines = $disciplinesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all products for the dropdown
$productsStmt = $conn->query("SELECT id, name, slug FROM products ORDER BY sort_order ASC, name ASC");
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

// Read query/POST parameters for pre-filling
$disciplineParam = trim($_GET['discipline'] ?? ($_POST['discipline'] ?? ''));
$productParam = trim($_GET['product'] ?? ($_POST['product'] ?? ''));
$testParam = trim($_GET['test'] ?? ($_POST['testing_type'] ?? ''));
$standardsParam = trim($_GET['standards'] ?? ($_POST['standards'] ?? ''));

$requirementsValue = trim($_POST['requirements'] ?? '');
if ($requirementsValue === '' && $standardsParam !== '') {
    $requirementsValue = "Applicable Standard / Specification: " . $standardsParam;
}

if (!function_exists('cleanLookupKey')) {
    function cleanLookupKey($str)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $str ?? ''));
    }
}

// Find matched discipline
$matchedDisciplineValue = '';
if ($disciplineParam !== '') {
    $cleanDisc = cleanLookupKey($disciplineParam);
    foreach ($disciplines as $disc) {
        $val = $disc['slug'] ?? (string) $disc['id'];
        if (
            strcasecmp($disciplineParam, (string) $val) === 0 ||
            strcasecmp($disciplineParam, (string) $disc['id']) === 0 ||
            strcasecmp($disciplineParam, $disc['name']) === 0 ||
            cleanLookupKey($disc['name']) === $cleanDisc ||
            cleanLookupKey($disc['slug']) === $cleanDisc
        ) {
            $matchedDisciplineValue = $val;
            break;
        }
    }
}

// Find matched product
$matchedProductValue = '';
if ($productParam !== '') {
    $cleanProd = cleanLookupKey($productParam);
    $cleanProdNoTesting = cleanLookupKey(str_ireplace('testing', '', $productParam));
    foreach ($products as $prod) {
        $val = $prod['slug'] ?? (string) $prod['id'];
        if (
            strcasecmp($productParam, (string) $val) === 0 ||
            strcasecmp($productParam, (string) $prod['id']) === 0 ||
            strcasecmp($productParam, $prod['name']) === 0 ||
            cleanLookupKey($prod['name']) === $cleanProd ||
            cleanLookupKey($prod['slug']) === $cleanProd ||
            cleanLookupKey(str_ireplace('testing', '', $prod['name'])) === $cleanProdNoTesting
        ) {
            $matchedProductValue = $val;
            break;
        }
    }
}

// Preload tests for initial selected discipline & product
$preloadedTests = [];
$matchedTestFound = false;
if ($matchedProductValue && $matchedDisciplineValue) {
    $tStmt = $conn->prepare("
        SELECT DISTINCT t.id, t.name 
        FROM tests t
        JOIN disciplines d ON t.discipline_id = d.id
        JOIN product_test pt ON t.id = pt.test_id
        JOIN products p ON pt.product_id = p.id
        WHERE (p.slug = ? OR p.id = ? OR LOWER(p.name) = LOWER(?)) 
          AND (d.slug = ? OR d.id = ? OR LOWER(d.name) = LOWER(?))
        ORDER BY t.name ASC
    ");
    $tStmt->execute([$matchedProductValue, $matchedProductValue, $productParam, $matchedDisciplineValue, $matchedDisciplineValue, $disciplineParam]);
    $preloadedTests = $tStmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($matchedProductValue) {
    $tStmt = $conn->prepare("
        SELECT DISTINCT t.id, t.name 
        FROM tests t
        JOIN product_test pt ON t.id = pt.test_id
        JOIN products p ON pt.product_id = p.id
        WHERE p.slug = ? OR p.id = ? OR LOWER(p.name) = LOWER(?)
        ORDER BY t.name ASC
    ");
    $tStmt->execute([$matchedProductValue, $matchedProductValue, $productParam]);
    $preloadedTests = $tStmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($matchedDisciplineValue) {
    $tStmt = $conn->prepare("
        SELECT DISTINCT t.id, t.name 
        FROM tests t
        JOIN disciplines d ON t.discipline_id = d.id
        WHERE d.slug = ? OR d.id = ? OR LOWER(d.name) = LOWER(?)
        ORDER BY t.name ASC
    ");
    $tStmt->execute([$matchedDisciplineValue, $matchedDisciplineValue, $disciplineParam]);
    $preloadedTests = $tStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

    <!-- Custom Stylesheet Link for this Page -->
    <link rel="stylesheet" href="assets/css/technical-support.css">

    <main style="padding-top: 80px; padding-bottom: 80px; background: #f4f7fb;">
        <!-- Booking Section -->
        <section class="ts-booking-section" style="margin-top: 0; background: transparent;">
            <div class="ts-booking-container">
                <!-- Left Info -->
                <div class="booking-left">
                    <div class="ts-subtitle red">
                        <span class="line"></span> Book a Test
                    </div>
                    <h2>Request <span style="color: var(--primary-blue);">A Test</span></h2>
                    <p>Complete the form to Book a Test with AKC's accredited laboratory. Our team will confirm
                        your booking, provide a detailed quotation, and guide you through sample submission.</p>

                    <div class="booking-features">
                        <div class="bf-card">
                            <i class="fa-solid fa-bolt"></i>
                            <div class="bf-content">
                                <h4>Fast Turnaround</h4>
                                <p>Standard results in 3-5 working days. Express 24-48 hr service available on request.
                                </p>
                            </div>
                        </div>
                        <div class="bf-card">
                            <i class="fa-solid fa-file-contract"
                                style="color: var(--primary-blue); background: #F0F9FF;"></i>
                            <div class="bf-content">
                                <h4>Accredited Test Report</h4>
                                <p>ISO 17025 accredited report with full methodology, results, and measurement
                                    uncertainty.
                                </p>
                            </div>
                        </div>
                        <div class="bf-card">
                            <i class="fa-solid fa-lock"></i>
                            <div class="bf-content">
                                <h4>Sample Confidentiality</h4>
                                <p>All samples and results handled with strict confidentiality. NDA available on
                                    request.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Form -->
                <div class="booking-right">
                    <?php if ($success_message): ?>
                        <div
                            style="background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; line-height: 1.5;">
                            <i class="fa-solid fa-circle-check" style="color: #28a745; margin-right: 6px;"></i>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div
                            style="background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; line-height: 1.5;">
                            <i class="fa-solid fa-triangle-exclamation" style="color: #dc3545; margin-right: 6px;"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="booking-form-header">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <div>
                            <h3>Request a Test</h3>
                            <p>Fill out the details below</p>
                        </div>
                    </div>

                    <form action="book-test.php<?php echo $isPopup ? '?popup=1' : ''; ?>" method="POST"
                        enctype="multipart/form-data" class="booking-form-body">
                        <input type="hidden" name="popup" value="<?php echo $isPopup ? '1' : '0'; ?>">
                        <div class="form-section-title">TEST INFORMATION</div>

                        <div class="form-group">
                            <label>Discipline Category <span>*</span></label>
                            <select class="form-control" name="discipline" id="discipline_select" required>
                                <option value="">Select the Discipline Category</option>
                                <?php foreach ($disciplines as $disc):
                                    $val = $disc['slug'] ?? (string) $disc['id'];
                                    $isSel = ($matchedDisciplineValue !== '' && $matchedDisciplineValue === $val);
                                    ?>
                                    <option value="<?php echo htmlspecialchars($val); ?>" <?php echo $isSel ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($disc['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Product <span>*</span></label>
                            <select class="form-control" name="product" id="product_select" required>
                                <option value="">Select the Product Category</option>
                                <?php foreach ($products as $prod):
                                    $val = $prod['slug'] ?? (string) $prod['id'];
                                    $isSel = ($matchedProductValue !== '' && $matchedProductValue === $val);
                                    ?>
                                    <option value="<?php echo htmlspecialchars($val); ?>" <?php echo $isSel ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prod['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Testing type <span>*</span></label>
                            <select class="form-control" name="testing_type" id="testing_type_select" required>
                                <option value="">Select the Testing Category</option>
                                <?php
                                $cleanTest = cleanLookupKey($testParam);
                                foreach ($preloadedTests as $t):
                                    $isTestSel = false;
                                    if ($testParam !== '') {
                                        if (
                                            strcasecmp($testParam, $t['name']) === 0 ||
                                            strcasecmp($testParam, (string) $t['id']) === 0 ||
                                            cleanLookupKey($t['name']) === $cleanTest
                                        ) {
                                            $isTestSel = true;
                                            $matchedTestFound = true;
                                        }
                                    }
                                    ?>
                                    <option value="<?php echo htmlspecialchars($t['name']); ?>" <?php echo $isTestSel ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($t['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if ($testParam !== '' && !$matchedTestFound): ?>
                                    <option value="<?php echo htmlspecialchars($testParam); ?>" selected>
                                        <?php echo htmlspecialchars($testParam); ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-section-title">SAMPLE INFORMATION</div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Type of Sample <span>*</span></label>
                                <input type="text" name="sample_type" class="form-control"
                                    placeholder="Select material type..." required>
                            </div>
                            <div class="form-group">
                                <label>Number of Samples <span>*</span></label>
                                <input type="number" name="num_samples" class="form-control" placeholder="e.g. 3"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Sample Description <span>*</span></label>
                            <input type="text" name="sample_desc" class="form-control"
                                placeholder="e.g. Filled PP pellets, 50g, natural colour" required>
                        </div>

                        <div class="form-group">
                            <label>Upload Sample Photograph <span>*</span></label>
                            <div class="file-upload-area" style="position: relative;">
                                <input type="file" name="sample_photo" accept=".png, .jpg, .jpeg, .pdf"
                                    style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;"
                                    required>
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Click to upload or drag & drop</p>
                                <span style="font-size: 0.8rem; color: #94A3B8;">JPG, PNG or PDF - Max 10MB</span>
                            </div>
                        </div>

                        <div class="form-section-title">CUSTOMER DETAILS</div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name <span>*</span></label>
                                <input type="text" name="fullname" class="form-control" placeholder="Your full name"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Company <span>*</span></label>
                                <input type="text" name="company" class="form-control"
                                    placeholder="Company or organization" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Email Address <span>*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="your@email.com"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number <span>*</span></label>
                                <input type="hidden" name="country_code" value="+91">
                                <input type="tel" name="phone" class="form-control" placeholder="10 digit number"
                                    pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Additional Requirements</label>
                            <textarea name="requirements" class="form-control" rows="3"
                                placeholder="Express turnaround, NDA required, specific standard, etc."><?php echo htmlspecialchars($requirementsValue); ?></textarea>
                        </div>

                        <button type="submit" class="btn-book-submit">
                            <i class="fa-regular fa-calendar-check"></i> Book Test
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cascading dropdowns logic
            const disciplineSelect = document.getElementById('discipline_select');
            const productSelect = document.getElementById('product_select');
            const testingTypeSelect = document.getElementById('testing_type_select');
            const initialTestParam = <?php echo json_encode($testParam); ?>;

            if (disciplineSelect && productSelect && testingTypeSelect) {

                function fetchTests(targetSelectVal = '') {
                    const discSlug = disciplineSelect.value;
                    const prodSlug = productSelect.value;

                    // Default option
                    testingTypeSelect.innerHTML = '<option value="">Select the Testing Category</option>';

                    if (discSlug && prodSlug) {
                        fetch(`api_get_options.php?action=get_tests&discipline=${encodeURIComponent(discSlug)}&product=${encodeURIComponent(prodSlug)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (!data || data.length === 0) {
                                    const option = document.createElement('option');
                                    option.value = 'custom';
                                    option.textContent = 'No tests available - Request Custom Test';
                                    testingTypeSelect.appendChild(option);
                                } else {
                                    let foundMatch = false;
                                    data.forEach(item => {
                                        const option = document.createElement('option');
                                        option.value = item.name;
                                        option.textContent = item.name;
                                        option.dataset.id = item.id;
                                        if (targetSelectVal && (
                                            item.name.toLowerCase() === targetSelectVal.toLowerCase() ||
                                            String(item.id) === String(targetSelectVal)
                                        )) {
                                            option.selected = true;
                                            foundMatch = true;
                                        }
                                        testingTypeSelect.appendChild(option);
                                    });
                                    if (targetSelectVal && !foundMatch) {
                                        const customOpt = document.createElement('option');
                                        customOpt.value = targetSelectVal;
                                        customOpt.textContent = targetSelectVal;
                                        customOpt.selected = true;
                                        testingTypeSelect.appendChild(customOpt);
                                    }
                                }
                            })
                            .catch(err => console.error('Error fetching tests:', err));
                    }
                }

                disciplineSelect.addEventListener('change', () => fetchTests(''));
                productSelect.addEventListener('change', () => fetchTests(''));

                // If options are empty on load but discipline and product are selected, fetch tests
                if (testingTypeSelect.options.length <= 1 && disciplineSelect.value && productSelect.value) {
                    fetchTests(initialTestParam);
                }
            }

            // File upload JS
            const fileInput = document.querySelector('input[name="sample_photo"]');
            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    const label = this.nextElementSibling.nextElementSibling;
                    if (this.files && this.files.length > 0) {
                        label.textContent = this.files[0].name;
                    } else {
                        label.textContent = 'Click to upload or drag & drop';
                    }
                });
            }

            // Loader on submit
            const formSubmit = document.querySelector('.booking-form-body');
            if (formSubmit) {
                formSubmit.addEventListener('submit', function () {
                    var btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
                        btn.style.opacity = '0.8';
                        btn.style.cursor = 'not-allowed';
                        setTimeout(() => { btn.disabled = true; }, 10);
                    }
                });
            }
        });
    </script>

    <?php
    if (!$isPopup) {
        include 'includes/footer.php';
    } else {
        echo "</body></html>";
    }
    ?>