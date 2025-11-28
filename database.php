<?php
// database.php

// Database credentials
$servername = "localhost";   // XAMPP default
$username = "root";          // XAMPP default
$password = "";              // XAMPP default has no password
$dbname = "spa_system";      // The database we created

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set charset to utf8mb4 for full Unicode support
mysqli_set_charset($conn, "utf8mb4");
?>
