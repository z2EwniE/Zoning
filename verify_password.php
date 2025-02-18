<?php
header("Content-Type: text/plain"); // Ensure plain text response

// Debugging: Log raw POST data
error_log("Raw POST Data: " . file_get_contents("php://input"));

// Database Connection
$conn = new mysqli("localhost", "root", "", "zoning_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the password from the request
$password = $_POST['password'] ?? '';

// Hardcoded correct password (you can store it in DB securely)
$correct_password = 'admin';

// Debugging: Log received password
error_log("Received password: " . $password);

if ($password === $correct_password) {
    echo 'success';
} else {
    echo 'failure';
}

$conn->close();
?>
