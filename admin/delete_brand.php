<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Brand ID is missing.";
    header("Location: manage_brand.php");
    exit;
}

$brand_id = intval($_GET['id']);

// Fetch image path before deletion
$stmt = $conn->prepare("SELECT image FROM brands WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $brand_id);
$stmt->execute();
$stmt->bind_result($image_path);
$brand_exists = $stmt->fetch();
$stmt->close();

if (!$brand_exists) {
    $_SESSION['error_message'] = "Brand not found.";
    header("Location: manage_brand.php");
    exit;
}

// Delete from database
$delete_stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
if (!$delete_stmt) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$delete_stmt->bind_param("i", $brand_id);

if ($delete_stmt->execute()) {
    // Delete associated image if exists
    if ($image_path !== 'na' && file_exists($image_path)) {
        unlink($image_path);
    }

    $_SESSION['success_message'] = "Brand deleted successfully.";
    header("Location: manage_brand.php");
    exit;
} else {
    $_SESSION['error_message'] = "Delete failed: " . htmlspecialchars($delete_stmt->error);
    header("Location: manage_brand.php");
    exit;
}

$delete_stmt->close();
$conn->close();
?>