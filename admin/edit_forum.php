<?php
session_start();
require '../db.php';

if (!isset($_GET['id'])) {
    die("Forum ID is missing.");
}

$forum_id = intval($_GET['id']);

// Fetch existing forum data
$stmt = $conn->prepare("SELECT * FROM forums WHERE id = ?");
$stmt->bind_param("i", $forum_id);
$stmt->execute();
$forum = $stmt->get_result()->fetch_assoc();

if (!$forum) {
    die("Forum not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']) ?: 'na';
    
    // Handle image upload
    $currentImage = $forum['image'];
    $newImage = $currentImage;
    
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
                $newImage = $filePath;
                if ($currentImage != 'na' && file_exists($currentImage)) {
                    unlink($currentImage);
                }
            }
        }
    }

    $stmt = $conn->prepare("UPDATE forums SET name = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $description, $newImage, $forum_id);

    if ($stmt->execute()) {
        header("Location: manage_forum.php");
        exit;
    } else {
        die("Update failed: " . $stmt->error);
    }
}
?>

<!-- HTML structure similar to add_forum.php with pre-filled data -->