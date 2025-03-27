<?php
require 'db.php';
session_start();



// Fetch admin profile
$stmt = $pdo->prepare("SELECT id, username, email, bio, profile_picture FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Admin profile not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);

    // Handle profile picture upload
    $profilePicturePath = $admin['profile_picture'];
    if (!empty($_FILES['profile_picture']['name']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
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

    // Update profile in the database
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$username, $email, $bio, $profilePicturePath, $_SESSION['user_id']]);

    // Update session values
    $_SESSION['username'] = $username;
    $_SESSION['profile_picture'] = $profilePicturePath;

    header("Location: admin_edit_profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Fragrance Haven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background: linear-gradient(135deg, #ece9e6, #ffffff); }
        .sidebar { height: 100vh; background: #343a40; padding: 1rem; color: white; position: fixed; width: 250px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; transition: background 0.3s; }
        .sidebar a:hover { background: #495057; border-radius: 5px; }
        .content { margin-left: 260px; padding: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center">Admin Dashboard</h4>
        <hr>
        <nav>
            <a href="edit_user.php"><i class="bi bi-people"></i> Users</a>
            <a href="forum_edit.php"><i class="bi bi-chat-left-text"></i> Forum Posts</a>
            <a href="perfume_edit.php"><i class="bi bi-brush"></i> Perfumes</a>
            <a href="perfumer_edit.php"><i class="bi bi-person-badge"></i> Perfumers</a>
            <a href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
        </nav>
    </div>

    <div class="content">
        <div class="container">
            <h2 class="mt-4">Edit Profile</h2>
            <form class="mt-3" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bio</label>
                    <textarea class="form-control" name="bio"><?= htmlspecialchars($admin['bio']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" name="profile_picture">
                    <?php if ($admin['profile_picture']): ?>
                        <div class="mt-2">
                            <img src="<?= htmlspecialchars($admin['profile_picture']) ?>" alt="Profile Picture" width="100">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
