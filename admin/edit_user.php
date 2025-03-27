<?php
session_start();
require '../db.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    die("User ID is missing.");
}

$user_id = $_GET['id'];

// Fetch user data
$stmt = $conn->prepare("SELECT id, username, email, role, profile_picture, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $bio = trim($_POST['bio']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Handle profile picture upload
    $profilePicturePath = $user['profile_picture'];
    if (!empty($_FILES['profile_picture']['name']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/user/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . "_" . basename($_FILES['profile_picture']['name']);
        $filePath = $uploadDir . $fileName;

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                $profilePicturePath = $filePath;
            }
        }
    }

    // Update user in database
    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, bio = ?, profile_picture = ?, password_hash = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $username, $email, $role, $bio, $profilePicturePath, $password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, bio = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $username, $email, $role, $bio, $profilePicturePath, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit;
    } else {
        die("Error updating user: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include Sidebar -->
    <?php require 'admin_dashboard.php'; ?>
    <div class="container mt-5">
        <h2>Edit User</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select class="form-control" name="role">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="moderator" <?= $user['role'] == 'moderator' ? 'selected' : '' ?>>Moderator</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Bio</label>
                <textarea class="form-control" name="bio"><?= htmlspecialchars($user['bio']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" class="form-control" name="profile_picture">
                <?php if ($user['profile_picture']): ?>
                <img src="<?= htmlspecialchars($user['profile_picture']) ?>" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password (Leave blank to keep current password)</label>
                <input type="password" class="form-control" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>