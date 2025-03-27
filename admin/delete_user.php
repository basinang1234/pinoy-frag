<?php
session_start();
require '../db.php'; // Ensure this creates a MySQLi connection ($conn)

if (!isset($_GET['id'])) {
    die("User ID is missing.");
}

$user_id = intval($_GET['id']); // Sanitize input

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    die("You cannot delete yourself.");
}

// Prepare statement
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameter
$stmt->bind_param('i', $user_id);

// Execute and handle result
if ($stmt->execute()) {
    header("Location: manage_users.php");
    exit;
} else {
    die("Delete failed: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>