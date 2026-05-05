﻿<?php

//  AUTHENTICATION (auth.php)

session_start();

// Set session timeout (e.g., 30 minutes)
$session_timeout = 1800; // seconds

if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $session_timeout)) {
    session_unset();    // unset $_SESSION variable for the run-time 
    session_destroy();  // destroy session data in storage
    header("Location: login.php?status=timeout");
    exit();
}
$_SESSION["last_activity"] = time(); // update last activity time stamp

// Check if user is logged in
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit();
}

// Placeholder for login logic (e.g., in login.php)
// After successful login, you should do:
// session_regenerate_id(true); // Prevent session fixation
// $_SESSION["logged_in"] = true;
// $_SESSION["user_id"] = $user_id; // Store user ID or other relevant info
// $_SESSION["username"] = $username;
// $_SESSION["last_activity"] = time();

// Example of logout logic (e.g., in logout.php)
// session_start();
// session_unset();
// session_destroy();
// header("Location: login.php");
// exit();

?>

