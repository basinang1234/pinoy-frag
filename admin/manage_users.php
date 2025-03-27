<?php
session_start();
require '../db.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch all users
$sql = "SELECT id, username, email, role, profile_picture, bio, created_at FROM users ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- Use d-flex to separate sidebar & content -->
<div class="d-flex">
    
    <!-- Include Sidebar -->
    <?php require 'admin_dashboard.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid px-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <h2 class="text-primary">Manage Users</h2>
            <a href="add_user.php" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Add User
            </a>
        </div>

        <div class="card shadow-sm p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Bio</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td>
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-person-circle fs-3 text-secondary"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($user['role'] == 'admin') ? 'danger' : (($user['role'] == 'moderator') ? 'primary' : 'info') ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($user['bio']) ?>">
                                    <?= !empty($user['bio']) ? htmlspecialchars($user['bio']) : '<span class="text-muted">N/A</span>' ?>
                                </span>
                            </td>
                            <td><?= date("M d, Y", strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- End Main Content -->
</div> <!-- End d-flex -->
