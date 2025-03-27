<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$forum_id = (int)$_POST['forum_id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);
$perfume_id = isset($_POST['perfume_id']) ? (int)$_POST['perfume_id'] : null;
$image = 'na'; // Default image placeholder

// Validate required fields
if (empty($title) || empty($content)) {
    header('Location: forum.php?id=' . $forum_id . '&error=Title and content are required');
    exit;
}

// Handle image upload
if (!empty($_FILES['image']['name'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate image format
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
        header('Location: forum.php?id=' . $forum_id . '&error=Only JPG, JPEG, PNG allowed');
        exit;
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image = $target_file;
    }
}

try {
    $stmt = $pdo->prepare('INSERT INTO forum_posts (user_id, forum_id, perfume_id, title, content, image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $forum_id, $perfume_id, $title, $content, $image]);
    header('Location: forum.php?id=' . $forum_id);
} catch (PDOException $e) {
    error_log('Post creation error: ' . $e->getMessage());
    echo 'System error occurred';
}
?>