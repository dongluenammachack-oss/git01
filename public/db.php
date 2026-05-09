﻿<?php

//  DATABASE CONNECTION (db.php)

// Database credentials
$host = 'localhost';
$db   = 'ict_system';
$user = 'root';
$pass = ''; // Consider using environment variables or a more secure method for production
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Log the error instead of displaying it in production
    error_log("Database connection failed: " . $e->getMessage());
    die("🚫 Connection failed: Please try again later.");
}

// Auto-create internet_records table if missing (should ideally be part of an installation script)
// This is kept for compatibility with the original code's functionality, but for a real application,
// database schema management should be handled separately (e.g., migrations).
$pdo->exec("CREATE TABLE IF NOT EXISTS `internet_records` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `internet_local` VARCHAR(255) DEFAULT '',
    `internet_type`  VARCHAR(100) DEFAULT '',
    `package`        VARCHAR(255) DEFAULT '',
    `price`          DECIMAL(12,2) DEFAULT 0,
    `start_date`     DATE NULL,
    `end_date`       DATE NULL,
    `document_local` VARCHAR(255) DEFAULT '',
    `document_link`  VARCHAR(500) DEFAULT '',
    `remark`         TEXT,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Other tables mentioned in the original code (e.g., office365_accounts, google_accounts, etc.)
// would also ideally be created via migrations or an installation script.
// For this refactoring, we assume they exist or will be created similarly if needed.

// Function to safely count records in a table using PDO
function safeCountPDO($pdo, $table) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM `" . $table . "`");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error counting records in table $table: " . $e->getMessage());
        return 0;
    }
}

// Function to safely check if a table exists using PDO
function tableExistsPDO($pdo, $table) {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error checking table existence for $table: " . $e->getMessage());
        return false;
    }
}

// Set error reporting for production (should be off)
ini_set("display_errors", 0);
error_reporting(0);

// For development, you might want to enable these:
// ini_set("display_errors", 1);
// error_reporting(E_ALL);

?>

