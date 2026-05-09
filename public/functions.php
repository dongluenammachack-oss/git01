﻿<?php

//  COMMON FUNCTIONS (functions.php)

// Ensure database connection is available
if (!isset($pdo)) {
    require_once __DIR__ . "/db.php";
}

/**
 * Safely counts records in a given table using PDO.
 * @param PDO $pdo The PDO database connection object.
 * @param string $table The name of the table.
 * @return int The number of records, or 0 if an error occurs.
 */
function safeCountPDO(PDO $pdo, string $table): int {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM `" . $table . "`");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error counting records in table $table: " . $e->getMessage());
        return 0;
    }
}

/**
 * Safely checks if a table exists using PDO.
 * @param PDO $pdo The PDO database connection object.
 * @param string $table The name of the table.
 * @return bool True if the table exists, false otherwise.
 */
function tableExistsPDO(PDO $pdo, string $table): bool {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error checking table existence for $table: " . $e->getMessage());
        return false;
    }
}

/**
 * Handles deletion of records from specified tables.
 * @param PDO $pdo The PDO database connection object.
 * @param string $table The table name to delete from.
 * @param int $id The ID of the record to delete.
 * @param array $allowedTables An array of allowed table names for deletion.
 * @return bool True on successful deletion, false otherwise.
 */
function handleDelete(PDO $pdo, string $table, int $id, array $allowedTables): bool {
    if (!in_array($table, $allowedTables)) {
        error_log("Attempted deletion from unauthorized table: $table");
        return false;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `" . $table . "` WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Database error during deletion from $table (ID: $id): " . $e->getMessage());
        return false;
    }
}

//  AJAX: Halo ID Lookup

function handleHaloIdLookup(PDO $pdo, string $halo_id): array {
    $dev_table_map2 = [
        'Laptop'    => 'laptops',
        'Desktop'   => 'desktops',
        'Tablet'    => 'tablets',
        'Phone'     => 'phones',
        'Monitor'   => 'monitors',
        'DGPS'      => 'dgps',
        'PowerBank' => 'powerbanks'
    ];
    $found = null;

    foreach ($dev_table_map2 as $dtype => $tbl) {
        if (!tableExistsPDO($pdo, $tbl)) {
            continue;
        }
        $stmt = $pdo->prepare("SELECT * FROM `" . $tbl . "` WHERE halo_id = :halo_id LIMIT 1");
        $stmt->bindParam(":halo_id", $halo_id, PDO::PARAM_STR);
        $stmt->execute();
        if ($row = $stmt->fetch()) {
            $row["source_table"] = $tbl;
            $row["device_type_label"] = $dtype;
            $found = $row;
            break;
        }
    }

    if ($found) {
        return ["status" => "found", "data" => $found];
    } else {
        return ["status" => "not_found", "msg" => "No device found with Halo ID: " . htmlspecialchars($halo_id)];
    }
}

//  AJAX: INS Number Lookup

function handleInsNumberLookup(PDO $pdo, string $ins_number): array {
    $found_emp = null;

    // Check employees table first
    if (tableExistsPDO($pdo, 'employees')) {
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE ins_number = :ins_number LIMIT 1");
        $stmt->bindParam(":ins_number", $ins_number, PDO::PARAM_STR);
        $stmt->execute();
        if ($row = $stmt->fetch()) {
            $found_emp = $row;
        }
    }

    // If not found, check device tables
    if (!$found_emp) {
        $device_tables = ['laptops', 'desktops', 'tablets', 'phones', 'monitors', 'dgps', 'powerbanks'];
        foreach ($device_tables as $tbl) {
            if (!tableExistsPDO($pdo, $tbl)) continue;
            $stmt = $pdo->prepare("SELECT username, department, team, ins_number, location_local AS location FROM `" . $tbl . "` WHERE ins_number = :ins_number LIMIT 1");
            $stmt->bindParam(":ins_number", $ins_number, PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch()) {
                $found_emp = $row;
                break;
            }
        }
    }

    // If still not found, check account tables
    if (!$found_emp) {
        $account_tables = ['office365_accounts', 'survey123_accounts', 'google_accounts', 'trimble_accounts'];
        foreach ($account_tables as $tbl) {
            if (!tableExistsPDO($pdo, $tbl)) continue;
            $stmt = $pdo->prepare("SELECT full_name AS username, department, team, ins_number FROM `" . $tbl . "` WHERE ins_number = :ins_number LIMIT 1");
            $stmt->bindParam(":ins_number", $ins_number, PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch()) {
                $found_emp = $row;
                break;
            }
        }
    }

    if ($found_emp) {
        return ["status" => "found", "data" => $found_emp];
    } else {
        return ["status" => "not_found", "msg" => "No user found with INS: " . htmlspecialchars($ins_number)];
    }
}

// Function to sanitize output for HTML to prevent XSS
function e(string $string): string {
    return htmlspecialchars($string ?? "");
}

// Function to redirect with a status message (using SweetAlert2 for better UX)
function redirectWithStatus(string $page, string $action, string $status, string $message = "") {
    $_SESSION["status_message"] = ["status" => $status, "message" => $message];
    header("Location: index.php?page=$page&action=$action");
    exit();
}

?>

