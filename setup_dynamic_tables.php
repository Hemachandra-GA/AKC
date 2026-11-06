<?php
require_once 'includes/db_connect.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS disciplines (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE
    )",
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        description TEXT
    )",
    "CREATE TABLE IF NOT EXISTS tests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        discipline_id INT,
        FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS product_discipline (
        product_id INT,
        discipline_id INT,
        PRIMARY KEY (product_id, discipline_id),
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS product_test (
        product_id INT,
        test_id INT,
        PRIMARY KEY (product_id, test_id),
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
    )"
];

foreach ($queries as $query) {
    try {
        $conn->exec($query);
        echo "Executed query successfully.\n";
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage() . "\n";
    }
}
echo "Database tables created successfully!\n";
?>
