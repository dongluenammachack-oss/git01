<?php
// Database configuration for InfinityFree
$host = "sql207.infinityfree.com";     // MySQL Hostname
$user = "if0_41843014";                // MySQL Username
$pass = "60suJN8PgyU9SL";             // MySQL Password
$db   = "if0_41843014_ict_system";     // MySQL Database Name

// Error reporting settings for production
ini_set('display_errors', 0);
error_reporting(0);

// Create connection
$conn = @mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    // Log error instead of displaying it
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Service temporarily unavailable. Please try again later.");
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>