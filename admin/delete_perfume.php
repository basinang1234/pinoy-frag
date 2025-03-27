<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Check if perfume ID is provided
if (!isset($_GET['id'])) {
    die("Perfume ID is missing.");
}

$perfume_id = intval($_GET['id']);

// Fetch image path before deletion
$stmt = $conn->prepare("SELECT image FROM perfumes WHERE id = ?");
$stmt->bind_param("i", $perfume_id);
$stmt->execute();
$stmt->bind_result($image_path);
$perfume_exists = $stmt->fetch();
$stmt->close();

if (!$perfume_exists) {
    die("Perfume not found.");
}

// Delete from database
$delete_stmt = $conn->prepare("DELETE FROM perfumes WHERE id = ?");
$delete_stmt->bind_param("i", $perfume_id);

if ($delete_stmt->execute()) {
    // Delete associated image if exists
    if (!empty($image_path) && file_exists($image_path)) {
        unlink($image_path);
    }
    
    header("Location: manage_perfume.php");
    exit;
} else {
    die("Delete failed: " . $delete_stmt->error);
}

$delete_stmt->close();
$conn->close();
?>