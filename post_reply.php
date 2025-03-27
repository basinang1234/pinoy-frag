<?php
// Start output buffering FIRST
ob_start();

// Start session immediately
session_start();

// Include config (ensure it has NO output)
require 'config.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    // Clear buffer before redirect
    ob_clean();
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $thread_id = $_POST['thread_id'] ?? null;
    $content = trim($_POST['content'] ?? '');

    // Validate input
    if (empty($content)) {
        $_SESSION['error'] = "Content cannot be empty";
        ob_clean();
        header("Location: thread.php?id=" . urlencode($thread_id));
        exit();
    }

    // Insert post
    $stmt = $conn->prepare("
        INSERT INTO posts (thread_id, user_id, content) 
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $thread_id, $_SESSION['user_id'], $content);

    if ($stmt->execute()) {
        // Clear buffer before redirect
        ob_clean();
        header("Location: thread.php?id=" . urlencode($thread_id));
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        ob_clean();
        header("Location: thread.php?id=" . urlencode($thread_id));
        exit();
    }
}

// Fallback redirect
ob_clean();
header("Location: forums.php");
exit();
?>