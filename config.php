<?php
// Database configuration

$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol"; // আপনার ডাটাবেজের নাম

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
