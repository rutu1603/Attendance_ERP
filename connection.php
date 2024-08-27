<?php
$host = 'localhost';
$dbname = 'attendance';
$user = 'root'; // or your database username
$pass = ''; // or your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
