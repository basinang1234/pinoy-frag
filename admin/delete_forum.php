<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Validate ID parameter
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Category ID is missing.";
    header("Location: manage_forum.php");
    exit;
}

$category_id = intval($_GET['id']);

// Fetch category details before deletion
$stmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->bind_result($category_name);
$category_exists = $stmt->fetch();
$stmt->close();

if (!$category_exists) {
    $_SESSION['error_message'] = "Category not found.";
    header("Location: manage_forum.php");
    exit;
}

// Delete from database
$delete_stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$delete_stmt->bind_param("i", $category_id);

if ($delete_stmt->execute()) {
    $_SESSION['success_message'] = "Category '$category_name' deleted successfully.";
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