<?php
session_start();
require '../db.php';

// Restrict access to admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Required fields
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = trim($_POST['role']);
    $bio = trim($_POST['bio']);
    
    // Profile picture handling
    $profilePicturePath = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $uploadDir = "../uploads/user/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . "_" . basename($_FILES['profile_picture']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                $profilePicturePath = $filePath;
            }
        }
    }

    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password_hash, role, bio, profile_picture) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("ssssss", 
        $username, 
        $email, 
        $password, 
        $role, 
        $bio,
        $profilePicturePath
    );

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit;
    } else {
        $error = "Error creating user: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include Sidebar -->
    <?php require 'admin_dashboard.php'; ?>

    <div class="container mt-5">
        <h2>Add New User</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select class="form-control" name="role" required>
                    <option value="user">User</option>
                    <option value="moderator">Moderator</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Bio</label>
                <textarea class="form-control" name="bio"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" class="form-control" name="profile_picture">
            </div>
            <button type="submit" class="btn btn-success">Create User</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>