<?php
// Start output buffering
ob_start();
require 'config.php';

// Verify user permissions
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    // Clear buffer before redirect
    ob_clean();
    header("Location: home.php");
    exit();
}

// Validate CSRF token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    ob_clean();
    die("Invalid request");
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

// Input validation
if (empty($name)) {
    $_SESSION['category_error'] = "Category name is required";
    ob_clean();
    header("Location: new_category.php");
    exit();
}

// Check for duplicate category
$stmt = $conn->prepare("SELECT category_id FROM categories WHERE name = ?");
$stmt->bind_param('s', $name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['category_error'] = "Category with this name already exists";
    ob_clean();
    header("Location: new_category.php");
    exit();
}

// Insert new category
$stmt = $conn->prepare("
    INSERT INTO categories (name, description, created_by)
    VALUES (?, ?, ?)
");
$stmt->bind_param('ssi', $name, $description, $_SESSION['user_id']);

if ($stmt->execute()) {
    $_SESSION['success'] = "Category created successfully";
    ob_clean();
    header("Location: forums.php"); // Ensure this exists
    exit();
} else {
    $_SESSION['category_error'] = "Database error: " . $stmt->error;
    ob_clean();
    header("Location: new_category.php");
    exit();
}