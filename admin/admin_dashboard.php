<?php
require '../db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fragrance Haven</title>
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
            <a href="manage_users.php"><i class="bi bi-people"></i> Users</a>
            <a href="manage_forum.php"><i class="bi bi-chat-left-text"></i> Forum Posts</a>
            <a href="manage_perfume.php"><i class="bi bi-brush"></i> Perfumes</a>
            <a href="manage_perfumer.php"><i class="bi bi-person-badge"></i> Perfumers</a>
            <a href="manage_reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
            <a href="manage_brand.php"><i class="bi bi-bar-chart"></i> Brands</a>
        </nav>
    </div>

    <div class="content">
        <div class="container">
            <div class="alert alert-info">Welcome to the Admin Panel!</div>
            <h3>Admin Actions</h3>
            <p>Select a section from the sidebar to manage data.</p>
        </div>
    </div>
</body>
</html>
