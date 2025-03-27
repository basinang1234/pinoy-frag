<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "fragrance_haven";

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
