<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']) ?: 'na';
    $website = trim($_POST['website']) ?: 'na';
    $image_path = 'na';

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../uploads/brands/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $image_path = $filePath;
            }
        }
    }

    // Insert into database (exclude redundant `id` column)
    $stmt = $conn->prepare("
        INSERT INTO brands 
        (name, description, website, image) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $name, $description, $website, $image_path);

    if ($stmt->execute()) {
        header("Location: manage_brand.php");
        exit;
    } else {
        die("Error adding brand: " . $stmt->error);
    }
}
?>