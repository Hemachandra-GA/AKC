<?php
/**
 * Fix missing rows in SQLite — products, tests, user_notifications
 * Reads the SQL dump and re-inserts using PDO prepared statements
 */
ini_set('memory_limit', '512M');

$sqliteDb = __DIR__ . '/includes/database.sqlite';
$sqlFile  = __DIR__ . '/../../gamarcmi_akc-final.sql';

echo "=== AKC SQLite Fix Script ===\n\n";

$pdo = new PDO('sqlite:' . $sqliteDb);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA journal_mode = WAL;');
$pdo->exec('PRAGMA foreign_keys = OFF;');

$sql = file_get_contents($sqlFile);

// Extract INSERT blocks for specific tables
function extractInserts($sql, $tableName) {
    // Grab the INSERT INTO `tableName` block
    $pattern = '/INSERT INTO `' . preg_quote($tableName, '/') . '`[^;]+;/s';
    preg_match($pattern, $sql, $m);
    return $m[0] ?? '';
}

// Tables with issues
$tables = ['products', 'tests', 'user_notifications'];

foreach ($tables as $table) {
    echo "Processing: $table\n";

    $block = extractInserts($sql, $table);
    if (!$block) {
        echo "  → No INSERT block found, skipping\n";
        continue;
    }

    // Get column list
    preg_match('/INSERT INTO `\w+` \(([^)]+)\)/', $block, $colMatch);
    if (!$colMatch) {
        echo "  → Could not parse columns, skipping\n";
        continue;
    }
    $columns = array_map(function($c) {
        return trim(str_replace('`', '', $c));
    }, explode(',', $colMatch[1]));

    // Extract VALUES section
    $valuesStart = strpos($block, 'VALUES') + 6;
    $valuesSection = trim(substr($block, $valuesStart));
    if (substr($valuesSection, -1) === ';') {
        $valuesSection = substr($valuesSection, 0, -1);
    }

    // Clear existing data for this table
    $pdo->exec("DELETE FROM \"$table\"");

    // Build prepared statement
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $colNames = implode(', ', array_map(fn($c) => "\"$c\"", $columns));
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO \"$table\" ($colNames) VALUES ($placeholders)");

    // Parse the VALUES — use a streaming approach to handle nested quotes
    $rows = parseValueRows($valuesSection);

    $pdo->beginTransaction();
    $count = 0;
    foreach ($rows as $row) {
        try {
            $stmt->execute($row);
            $count++;
        } catch (Exception $e) {
            echo "  [skip row] " . $e->getMessage() . "\n";
        }
    }
    $pdo->commit();
    echo "  ✓ Inserted $count rows\n";
}

/**
 * Parse MySQL VALUES block into array of PHP arrays
 * Handles escaped quotes and multi-line strings
 */
function parseValueRows(string $valuesSection): array {
    $rows = [];
    $i = 0;
    $len = strlen($valuesSection);

    while ($i < $len) {
        // Skip whitespace and commas between rows
        while ($i < $len && in_array($valuesSection[$i], [' ', "\n", "\r", ','])) $i++;
        if ($i >= $len) break;

        // Expect opening paren
        if ($valuesSection[$i] !== '(') { $i++; continue; }
        $i++; // skip '('

        $row = [];
        while ($i < $len && $valuesSection[$i] !== ')') {
            // Skip whitespace
            while ($i < $len && in_array($valuesSection[$i], [' ', "\n", "\r"])) $i++;
            if ($i >= $len) break;

            if ($valuesSection[$i] === "'") {
                // String value
                $i++; // skip opening quote
                $value = '';
                while ($i < $len) {
                    if ($valuesSection[$i] === '\\' && $i + 1 < $len) {
                        $next = $valuesSection[$i+1];
                        if ($next === "'") { $value .= "'"; $i += 2; }
                        elseif ($next === '\\') { $value .= '\\'; $i += 2; }
                        elseif ($next === 'n') { $value .= "\n"; $i += 2; }
                        elseif ($next === 'r') { $value .= "\r"; $i += 2; }
                        elseif ($next === 't') { $value .= "\t"; $i += 2; }
                        else { $value .= $valuesSection[$i]; $i++; }
                    } elseif ($valuesSection[$i] === "'") {
                        $i++; // closing quote
                        break;
                    } else {
                        $value .= $valuesSection[$i];
                        $i++;
                    }
                }
                $row[] = $value;
            } elseif (strtoupper(substr($valuesSection, $i, 4)) === 'NULL') {
                $row[] = null;
                $i += 4;
            } else {
                // Numeric value
                $num = '';
                while ($i < $len && $valuesSection[$i] !== ',' && $valuesSection[$i] !== ')') {
                    $num .= $valuesSection[$i++];
                }
                $row[] = trim($num) === '' ? null : trim($num);
            }

            // Skip comma separator
            while ($i < $len && in_array($valuesSection[$i], [' ', "\n", "\r"])) $i++;
            if ($i < $len && $valuesSection[$i] === ',') $i++;
        }
        $i++; // skip closing ')'
        if (count($row) > 0) $rows[] = $row;
    }
    return $rows;
}

echo "\nFinal row counts:\n";
foreach ($tables as $t) {
    $c = $pdo->query("SELECT COUNT(*) FROM \"$t\"")->fetchColumn();
    echo "  $t: $c rows\n";
}

echo "\n✅ Fix complete!\n";
