<?php
/**
 * AKC Website — MySQL → SQLite Migration Script (v2, handles apostrophes)
 * Run once: php migrate_to_sqlite.php
 */
ini_set('memory_limit', '256M');

$sqlFile   = __DIR__ . '/../../gamarcmi_akc-final.sql';
$sqliteDb  = __DIR__ . '/includes/database.sqlite';

echo "=== AKC MySQL → SQLite Migration ===\n\n";

// Open (or create) the SQLite database
try {
    $pdo = new PDO('sqlite:' . $sqliteDb);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA journal_mode = WAL;');
    $pdo->exec('PRAGMA foreign_keys = OFF;');
} catch (Exception $e) {
    die("Cannot open SQLite: " . $e->getMessage() . "\n");
}

echo "SQLite DB: $sqliteDb\n";
echo "SQL Dump : $sqlFile\n\n";

if (!file_exists($sqlFile)) {
    die("ERROR: SQL dump not found at $sqlFile\n");
}

$sql = file_get_contents($sqlFile);

// ── Strip MySQL-only header/footer ────────────────────────────────
$sql = preg_replace('/^--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\/;?/s', '', $sql);
$sql = preg_replace('/SET\s+\w+\s*=.*?;/i', '', $sql);
$sql = preg_replace('/START\s+TRANSACTION;/i', '', $sql);
$sql = preg_replace('/COMMIT;/i', '', $sql);

// ── Convert MySQL → SQLite syntax ─────────────────────────────────
// Remove ENGINE, CHARSET, COLLATE clauses
$sql = preg_replace('/\s*ENGINE\s*=\s*\w+/i', '', $sql);
$sql = preg_replace('/\s*DEFAULT\s+CHARSET\s*=\s*\w+/i', '', $sql);
$sql = preg_replace('/\s*COLLATE\s*=?\s*[\w]+/i', '', $sql);
$sql = preg_replace('/\s*CHARACTER\s+SET\s+[\w]+/i', '', $sql);

// Remove MySQL-specific column options
$sql = preg_replace('/\s+AUTO_INCREMENT/i', ' AUTOINCREMENT', $sql);
// MySQL int(N) → INTEGER
$sql = preg_replace('/\bint\(\d+\)/i', 'INTEGER', $sql);
$sql = preg_replace('/\btinyint\(\d+\)/i', 'INTEGER', $sql);
// Remove unsigned
$sql = preg_replace('/\s+unsigned/i', '', $sql);
// json → TEXT
$sql = preg_replace('/\bjson\b/i', 'TEXT', $sql);
// enum(...) → TEXT
$sql = preg_replace("/enum\([^)]+\)/i", 'TEXT', $sql);
// timestamp → TEXT
$sql = preg_replace('/\btimestamp\b/i', 'TEXT', $sql);
// datetime → TEXT
$sql = preg_replace('/\bdatetime\b/i', 'TEXT', $sql);
// varchar(N) → TEXT
$sql = preg_replace('/\bvarchar\(\d+\)/i', 'TEXT', $sql);
// Remove backtick quoting (SQLite uses double-quotes or no quotes)
$sql = str_replace('`', '"', $sql);

// Remove KEY / INDEX lines inside CREATE TABLE
$sql = preg_replace('/^\s*(UNIQUE\s+)?KEY\s+.*?,?\s*\n/im', '', $sql);
$sql = preg_replace('/^\s*PRIMARY\s+KEY\s*\([^)]+\),\s*\n/im', '', $sql);

// Remove trailing commas before closing paren
$sql = preg_replace('/,\s*\n(\s*\))/m', "\n$1", $sql);

// Remove ALTER TABLE statements (we don't need them)
$sql = preg_replace('/ALTER TABLE.*?;/is', '', $sql);

// Remove "IF NOT EXISTS" qualifiers on INSERT (not needed)
// Keep INSERT INTO ... VALUES as-is — SQLite supports them

// Split into individual statements
$statements = preg_split('/;\s*\n/s', $sql);

$pdo->beginTransaction();

$created  = 0;
$inserted = 0;
$errors   = 0;

foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (empty($stmt)) continue;

    // Skip DROP TABLE statements
    if (preg_match('/^DROP\s+TABLE/i', $stmt)) continue;

    // For CREATE TABLE, drop first so we start fresh
    if (preg_match('/^CREATE\s+TABLE\s+"?(\w+)"?/i', $stmt, $m)) {
        $tableName = $m[1];
        try {
            $pdo->exec("DROP TABLE IF EXISTS \"$tableName\"");
        } catch (Exception $e) {}
    }

    try {
        $pdo->exec($stmt);
        if (preg_match('/^CREATE\s+TABLE/i', $stmt)) {
            $created++;
        } elseif (preg_match('/^INSERT\s+INTO/i', $stmt)) {
            $inserted++;
        }
    } catch (Exception $e) {
        $errors++;
        $preview = substr($stmt, 0, 120);
        echo "  [SKIP] " . $e->getMessage() . "\n";
        echo "         Statement: $preview...\n\n";
    }
}

$pdo->commit();

echo "\n=== Migration Complete ===\n";
echo "  Tables created : $created\n";
echo "  INSERT batches : $inserted\n";
echo "  Skipped/Errors : $errors\n\n";

// Verify
echo "Tables in SQLite:\n";
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    $count = $pdo->query("SELECT COUNT(*) FROM \"$t\"")->fetchColumn();
    echo "  ✓ $t ($count rows)\n";
}

echo "\nDone! SQLite database is ready.\n";
