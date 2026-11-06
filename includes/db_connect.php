<?php
// Suppress deprecation warnings from displaying in production
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

/*
|--------------------------------------------------------------------------
| Database Configuration (MySQL / phpMyAdmin / SQLite)
|--------------------------------------------------------------------------
| For Live Server (cPanel / Apache / phpMyAdmin):
| 1. Import akc_database_mysql_phpmyadmin.sql into phpMyAdmin.
| 2. Update $db_name, $db_user, and $db_pass with your live server credentials below.
|
| The app will connect to MySQL automatically. If MySQL is unavailable
| (e.g., local development without MySQL service running), it gracefully
| falls back to SQLite (database.sqlite).
*/

$use_mysql = false; // Set to false to use SQLite (database.sqlite)

$db_host = 'localhost';
$db_name = 'gamarcmi_final_akc';
$db_user = 'gamarcmi_final_akc';
$db_pass = 'PZyyvrj&h8p7';


$conn = null;

if ($use_mysql) {
    try {
        $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $conn = new PDO($dsn, $db_user, $db_pass, $options);
    } catch (Throwable $e) {
        // Fallback to SQLite if MySQL connection fails or server is not running
        $conn = null;
    }
}

// Fallback to SQLite (local dev or when MySQL is unreachable)
if (!$conn) {
    $sourceDb = __DIR__ . '/database.sqlite';
    $sqliteDb = $sourceDb;

    // If running in Vercel (read-only filesystem), use /tmp
    if (isset($_SERVER['VERCEL']) || getenv('VERCEL') || (PHP_OS_FAMILY !== 'Windows' && is_dir('/tmp'))) {
        $tmpDb = '/tmp/database.sqlite';
        if (!file_exists($tmpDb)) {
            copy($sourceDb, $tmpDb);
        }
        $sqliteDb = $tmpDb;
    }

    try {
        $conn = new PDO('sqlite:' . $sqliteDb);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $conn->exec('PRAGMA journal_mode = WAL;');
        $conn->exec('PRAGMA foreign_keys = ON;');

        // SQLite compatibility functions for MySQL queries
        if (method_exists($conn, 'createFunction')) {
            @$conn->createFunction('CURDATE', fn() => date('Y-m-d'));
            @$conn->createFunction('NOW', fn() => date('Y-m-d H:i:s'));
        } elseif (method_exists($conn, 'sqliteCreateFunction')) {
            @$conn->sqliteCreateFunction('CURDATE', fn() => date('Y-m-d'));
            @$conn->sqliteCreateFunction('NOW', fn() => date('Y-m-d H:i:s'));
        }

        // Auto-initialize SQLite database tables and data if products table does not exist
        try {
            $tableCheck = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='products'")->fetchColumn();
            if (!$tableCheck) {
                init_sqlite_database($conn);
            }
        } catch (Throwable $e) {
            // Silently handle
        }

        // Automatic cleanup of unwanted dummy entries & credentials sync
        try {
            $conn->exec("DELETE FROM product_test WHERE product_id IN (SELECT id FROM products WHERE name LIKE '%new produc%' OR slug LIKE '%new-produc%')");
            $conn->exec("DELETE FROM product_discipline WHERE product_id IN (SELECT id FROM products WHERE name LIKE '%new produc%' OR slug LIKE '%new-produc%')");
            $conn->exec("DELETE FROM products WHERE name LIKE '%new produc%' OR slug LIKE '%new-produc%'");
        } catch (Throwable $e) {
        }

        // Permanently remove Rheological from disciplines and tests (we only have 4 categories)
        try {
            $conn->exec("DELETE FROM product_test WHERE test_id IN (SELECT id FROM tests WHERE discipline_id IN (SELECT id FROM disciplines WHERE name LIKE '%rheolog%' OR slug LIKE '%rheolog%'))");
            $conn->exec("DELETE FROM tests WHERE discipline_id IN (SELECT id FROM disciplines WHERE name LIKE '%rheolog%' OR slug LIKE '%rheolog%')");
            $conn->exec("DELETE FROM product_discipline WHERE discipline_id IN (SELECT id FROM disciplines WHERE name LIKE '%rheolog%' OR slug LIKE '%rheolog%')");
            $conn->exec("DELETE FROM disciplines WHERE name LIKE '%rheolog%' OR slug LIKE '%rheolog%'");
        } catch (Throwable $e) {
        }

        try {
            $accountsToSync = [
                ['username' => 'mamatha', 'email' => 'mamatha14031995@gmail.com', 'pass' => 'Mamatha@123', 'role' => 'client', 'fullname' => 'Mamatha'],
                ['username' => 'mamathasurekha2@gmail.com', 'email' => 'mamathasurekha2@gmail.com', 'pass' => 'Mamatha@123', 'role' => 'client', 'fullname' => 'Mamatha'],
                ['username' => 'gadigitalsolutionss@gmail.com', 'email' => 'gadigitalsolutionss@gmail.com', 'pass' => 'Mamatha@123', 'role' => 'client', 'fullname' => 'Customer'],
                ['username' => 'superadmin', 'email' => 'mamathasurekha8@gmail.com', 'pass' => 'Admin@123', 'role' => 'super_admin', 'fullname' => 'System Super Admin'],
                ['username' => 'admin', 'email' => 'mamathasurekha14@gmail.com', 'pass' => 'Mamatha@123', 'role' => 'admin', 'fullname' => 'Admin']
            ];
            foreach ($accountsToSync as $acc) {
                $hash = password_hash($acc['pass'], PASSWORD_DEFAULT);
                $uStmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?)");
                $uStmt->execute([$acc['email'], $acc['username']]);
                $existing = $uStmt->fetch();
                if ($existing) {
                    $upd = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ?, fullname = ?, is_active = 1 WHERE id = ?");
                    $upd->execute([$acc['username'], $acc['email'], $hash, $acc['role'], $acc['fullname'], $existing['id']]);
                } else {
                    $ins = $conn->prepare("INSERT INTO users (username, email, password, role, fullname, is_active, created_at) VALUES (?, ?, ?, ?, ?, 1, datetime('now'))");
                    $ins->execute([$acc['username'], $acc['email'], $hash, $acc['role'], $acc['fullname']]);
                }
            }
            $chk44 = $conn->query("SELECT id FROM test_bookings WHERE id = 44")->fetch();
            if (!$chk44) {
                $conn->exec("INSERT OR IGNORE INTO test_bookings 
                    (id, sample_type, num_samples, sample_desc, sample_photo, fullname, company, email, phone, requirements, status, created_at)
                    VALUES 
                    (44, 'test', '3', 'Sample testing description', 'assets/images/plastic_resin/PET Bottles.webp', 'Mamatha', 'AKC Client', 'mamathasurekha2@gmail.com', '+91 7671866605', 'Standard testing parameters', 'Pending', datetime('now'))");
            }
        } catch (Throwable $e) {
        }

        // Restore and persist client submitted bookings (ID 43 & 42)
        try {
            if (!file_exists(__DIR__ . '/../assets/uploads/samples/6a159775e3e76.png') && file_exists(__DIR__ . '/../assets/uploads/6a159775e3e76.png')) {
                @copy(__DIR__ . '/../assets/uploads/6a159775e3e76.png', __DIR__ . '/../assets/uploads/samples/6a159775e3e76.png');
            }
            if (!file_exists(__DIR__ . '/../assets/uploads/samples/6a4f72db336a6.jpeg') && file_exists(__DIR__ . '/../assets/uploads/6a4f72db336a6.jpeg')) {
                @copy(__DIR__ . '/../assets/uploads/6a4f72db336a6.jpeg', __DIR__ . '/../assets/uploads/samples/6a4f72db336a6.jpeg');
            }

            $chk43 = $conn->query("SELECT id FROM test_bookings WHERE id = 43")->fetch();
            if (!$chk43) {
                $conn->exec("INSERT OR IGNORE INTO test_bookings 
                    (id, sample_type, num_samples, sample_desc, sample_photo, fullname, company, email, phone, requirements, status, created_at)
                    VALUES 
                    (43, 'AA', '2', 'Sample test submission', '6a159775e3e76.png', 'AA', 'AKC Client', 'mamatha14031995@gmail.com', '+91 9876543210', 'Quality and performance testing', 'Pending', datetime('now'))");
            } else {
                $conn->exec("UPDATE test_bookings SET sample_photo = '6a159775e3e76.png' WHERE id = 43 AND (sample_photo IS NULL OR sample_photo = '')");
            }
            $chk42 = $conn->query("SELECT id FROM test_bookings WHERE id = 42")->fetch();
            if (!$chk42) {
                $conn->exec("INSERT OR IGNORE INTO test_bookings 
                    (id, sample_type, num_samples, sample_desc, sample_photo, fullname, company, email, phone, requirements, status, created_at)
                    VALUES 
                    (42, 'Polymer Specimen', '2', 'Client sample testing', '6a4f72db336a6.jpeg', 'Mamatha', 'AKC Client', 'mamatha14031995@gmail.com', '+91 9876543210', 'Standard testing parameters', 'Pending', datetime('now', '-15 minutes'))");
            } else {
                $conn->exec("UPDATE test_bookings SET sample_photo = '6a4f72db336a6.jpeg' WHERE id = 42 AND (sample_photo IS NULL OR sample_photo = '')");
            }
            $chk44 = $conn->query("SELECT id FROM test_bookings WHERE id = 44")->fetch();
            if (!$chk44) {
                $conn->exec("INSERT OR IGNORE INTO test_bookings 
                    (id, sample_type, num_samples, sample_desc, sample_photo, fullname, company, email, phone, requirements, status, created_at)
                    VALUES 
                    (44, 'test', '3', 'Sample testing description', 'assets/images/plastic_resin/PET Bottles.webp', 'Mamatha', 'AKC Client', 'mamathasurekha2@gmail.com', '+91 7671866605', 'Standard testing parameters', 'Pending', datetime('now'))");
            }
        } catch (Throwable $e) {
        }
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function init_sqlite_database($conn)
{
    $schema = "
    CREATE TABLE IF NOT EXISTS `disciplines` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `name` TEXT NOT NULL,
      `slug` TEXT NOT NULL UNIQUE,
      `sort_order` INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS `products` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `name` TEXT NOT NULL,
      `slug` TEXT NOT NULL UNIQUE,
      `description` TEXT,
      `sort_order` INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS `tests` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `name` TEXT NOT NULL,
      `description` TEXT,
      `discipline_id` INTEGER DEFAULT NULL,
      `standards` TEXT DEFAULT 'ISO / ASTM / IS'
    );

    CREATE TABLE IF NOT EXISTS `test_types` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `name` TEXT NOT NULL UNIQUE,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `product_discipline` (
      `product_id` INTEGER NOT NULL,
      `discipline_id` INTEGER NOT NULL,
      PRIMARY KEY (`product_id`, `discipline_id`)
    );

    CREATE TABLE IF NOT EXISTS `product_test` (
      `product_id` INTEGER NOT NULL,
      `test_id` INTEGER NOT NULL,
      PRIMARY KEY (`product_id`, `test_id`)
    );

    CREATE TABLE IF NOT EXISTS `test_bookings` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `sample_type` TEXT NOT NULL,
      `num_samples` TEXT NOT NULL,
      `sample_desc` TEXT,
      `sample_photo` TEXT DEFAULT NULL,
      `fullname` TEXT NOT NULL,
      `company` TEXT NOT NULL,
      `email` TEXT NOT NULL,
      `phone` TEXT NOT NULL,
      `requirements` TEXT,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `status` TEXT DEFAULT 'Pending',
      `estimated_time` DATETIME DEFAULT NULL,
      `accepted_at` DATETIME DEFAULT NULL,
      `completed_at` DATETIME DEFAULT NULL,
      `rejected_at` DATETIME DEFAULT NULL,
      `result_pdf` TEXT DEFAULT NULL,
      `rejection_reason` TEXT,
      `est_notification_sent` INTEGER DEFAULT 0,
      `alert_24h_sent` INTEGER DEFAULT 0,
      `alert_1h_sent` INTEGER DEFAULT 0,
      `alert_overdue_sent` INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS `booking_files` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `booking_id` INTEGER NOT NULL,
      `file_name` TEXT NOT NULL,
      `file_path` TEXT NOT NULL,
      `file_type` TEXT NOT NULL,
      `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `users` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `username` TEXT NOT NULL UNIQUE,
      `password` TEXT NOT NULL,
      `role` TEXT DEFAULT 'user',
      `permissions` TEXT,
      `is_active` INTEGER DEFAULT 1,
      `fullname` TEXT DEFAULT NULL,
      `phone` TEXT DEFAULT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `email` TEXT DEFAULT NULL,
      `reset_token` TEXT DEFAULT NULL,
      `reset_token_expiry` DATETIME DEFAULT NULL,
      `profile_pic` TEXT DEFAULT NULL
    );

    CREATE TABLE IF NOT EXISTS `user_notifications` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `user_id` INTEGER DEFAULT NULL,
      `booking_id` INTEGER NOT NULL,
      `title` TEXT NOT NULL,
      `message` TEXT NOT NULL,
      `is_read` INTEGER DEFAULT 0,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `admin_id` INTEGER NOT NULL,
      `action` TEXT NOT NULL,
      `module` TEXT NOT NULL,
      `record_id` TEXT DEFAULT NULL,
      `old_data` TEXT DEFAULT NULL,
      `new_data` TEXT DEFAULT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    ";

    $conn->exec($schema);

    $candidates = [
        dirname(__DIR__) . '/akc_database_mysql_phpmyadmin.sql',
        __DIR__ . '/../akc_database_mysql_phpmyadmin.sql',
        'C:/Users/91767/Desktop/AKC/akc-ptarc.com/akc-ptarc.com/akc_database_mysql_phpmyadmin.sql',
        'C:/Users/91767/Desktop/AKC/akc_database_mysql_phpmyadmin.sql'
    ];
    $sqlFile = null;
    foreach ($candidates as $c) {
        if (file_exists($c)) {
            $sqlFile = $c;
            break;
        }
    }

    if ($sqlFile) {
        $content = file_get_contents($sqlFile);
        $len = strlen($content);
        $inInsert = false;
        $current = '';
        $inString = false;
        $stringChar = '';

        $inTx = false;
        try {
            $conn->beginTransaction();
            $inTx = true;
        } catch (Throwable $te) {
        }

        for ($i = 0; $i < $len; $i++) {
            $ch = $content[$i];
            if (!$inInsert) {
                if (($ch === 'I' || $ch === 'i') && strncasecmp(substr($content, $i, 11), 'INSERT INTO', 11) === 0) {
                    $inInsert = true;
                    $current = '';
                    $inString = false;
                }
            }

            if ($inInsert) {
                $current .= $ch;
                if (!$inString) {
                    if ($ch === "'" || $ch === '"') {
                        $inString = true;
                        $stringChar = $ch;
                    } elseif ($ch === ';') {
                        try {
                            $conn->exec(trim($current));
                        } catch (Throwable $t) {
                            // ignore duplicate or non-fatal syntax diff
                        }
                        $inInsert = false;
                        $current = '';
                    }
                } else {
                    if ($ch === '\\') {
                        $i++;
                        if ($i < $len) {
                            $current .= $content[$i];
                        }
                    } elseif ($ch === $stringChar) {
                        $inString = false;
                    }
                }
            }
        }

        if ($inTx) {
            try {
                $conn->commit();
            } catch (Throwable $te) {
            }
        }
    }
}
?>