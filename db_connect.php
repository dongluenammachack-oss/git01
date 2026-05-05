﻿<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ict_system";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}
// ກໍານົດໃຫ້ຮອງຮັບພາສາລາວ
mysqli_set_charset($conn, "utf8");
?>
