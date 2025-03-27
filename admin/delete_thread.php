<?php
session_start();
require '../db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    $_SESSION['error_message'] = "Access denied. You must be an admin to perform this action.";
    header("Location: manage_forum.php");
    exit;
}

// Validate ID parameter
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Thread ID is missing.";
    header("Location: manage_forum.php");
    exit;
}

$thread_id = intval($_GET['id']);

// Fetch thread details before deletion
$stmt = $conn->prepare("SELECT title FROM threads WHERE thread_id = ?");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$stmt->bind_result($thread_title);
$thread_exists = $stmt->fetch();
$stmt->close();

if (!$thread_exists) {
    $_SESSION['error_message'] = "Thread not found.";
    header("Location: manage_forum.php");
    exit;
}

// Check for dependent posts (optional, since cascading deletes handle this)
$check_posts_stmt = $conn->prepare("SELECT COUNT(*) AS post_count FROM posts WHERE thread_id = ?");
$check_posts_stmt->bind_param("i", $thread_id);
$check_posts_stmt->execute();
$check_posts_stmt->bind_result($post_count);
$check_posts_stmt->fetch();
$check_posts_stmt->close();

if ($post_count > 0) {
    // Optional: Log the number of posts being deleted
    error_log("Deleting thread '$thread_title' with $post_count associated posts.");
}

// Delete from database
$delete_stmt = $conn->prepare("DELETE FROM threads WHERE thread_id = ?");
$delete_stmt->bind_param("i", $thread_id);

if ($delete_stmt->execute()) {
    $_SESSION['success_message'] = "Thread '$thread_title' deleted successfully.";
    header("Location: manage_forum.php");
    exit;
} else {
    $_SESSION['error_message'] = "Delete failed: " . htmlspecialchars($delete_stmt->error);
    header("Location: manage_forum.php");
    exit;
}

$delete_stmt->close();
$conn->close();
?>