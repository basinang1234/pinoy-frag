<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']) ?: 'na';
    $user_id = $_SESSION['user_id'];
    
    // Handle image upload
    $image = 'na';
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../uploads/forums/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $image = $filePath;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO forums (name, description, image, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $description, $image, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_forum.php");
        exit;
    } else {
        die("Error creating forum: " . $stmt->error);
    }
}
?>