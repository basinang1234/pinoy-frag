<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch categories
$sqlCategories = "SELECT * FROM categories ORDER BY position ASC";
$resultCategories = $conn->query($sqlCategories);

if (!$resultCategories) {
    die("Database Error: " . $conn->error);
}
$categories = $resultCategories->fetch_all(MYSQLI_ASSOC);

// Fetch threads with user and category information
$sqlThreads = "SELECT t.*, c.name AS category_name, u.username AS creator_name 
               FROM threads t 
               LEFT JOIN categories c ON t.category_id = c.category_id 
               LEFT JOIN users u ON t.user_id = u.id 
               ORDER BY t.created_at DESC";
$resultThreads = $conn->query($sqlThreads);

if (!$resultThreads) {
    die("Database Error: " . $conn->error);
}
$threads = $resultThreads->fetch_all(MYSQLI_ASSOC);

// Fetch posts with user and thread information
$sqlPosts = "SELECT p.*, t.title AS thread_title, u.username AS creator_name 
             FROM posts p 
             LEFT JOIN threads t ON p.thread_id = t.thread_id 
             LEFT JOIN users u ON p.user_id = u.id 
             ORDER BY p.created_at DESC";
$resultPosts = $conn->query($sqlPosts);

if (!$resultPosts) {
    die("Database Error: " . $conn->error);
}
$posts = $resultPosts->fetch_all(MYSQLI_ASSOC);

// Fetch reviews with user and perfume information
$sqlReviews = "SELECT r.*, u.username AS reviewer_name, perf.perfume_name 
               FROM reviews r 
               LEFT JOIN users u ON r.user_id = u.id 
               LEFT JOIN perfumes perf ON r.perfume_id = perf.id 
               ORDER BY r.created_at DESC";
$resultReviews = $conn->query($sqlReviews);

if (!$resultReviews) {
    die("Database Error: " . $conn->error);
}
$reviews = $resultReviews->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Forums</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <!-- Include Sidebar -->
        <?php require 'admin_dashboard.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid px-4 flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <h2 class="text-primary">Forum Management</h2>
            </div>

            <!-- Categories Table -->
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">Categories</h5>
                <table id="categoriesTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Position</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= htmlspecialchars($category['category_id']) ?></td>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td><?= htmlspecialchars($category['description']) ?: '<span class="text-muted">No description</span>' ?></td>
                            <td><?= htmlspecialchars($category['position']) ?></td>
                            <td><?= htmlspecialchars($category['created_by']) ?></td>
                            <td>
                                <a href="edit_category.php?id=<?= $category['category_id'] ?>" 
                                   class="btn btn-warning btn-sm me-2" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete_category.php?id=<?= $category['category_id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete <?= addslashes($category['name']) ?>?')"
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Threads Table -->
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">Threads</h5>
                <table id="threadsTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Creator</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($threads as $thread): ?>
                        <tr>
                            <td><?= htmlspecialchars($thread['thread_id']) ?></td>
                            <td><?= htmlspecialchars($thread['title']) ?></td>
                            <td><?= htmlspecialchars($thread['category_name']) ?></td>
                            <td><?= htmlspecialchars($thread['creator_name']) ?></td>
                            <td><?= htmlspecialchars($thread['status']) ?></td>
                            <td><?= date("M d, Y H:i", strtotime($thread['created_at'])) ?></td>
                            <td>
                                <a href="edit_thread.php?id=<?= $thread['thread_id'] ?>" 
                                   class="btn btn-warning btn-sm me-2" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete_thread.php?id=<?= $thread['thread_id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete <?= addslashes($thread['title']) ?>?')"
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Posts Table -->
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">Posts</h5>
                <table id="postsTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Content</th>
                            <th>Thread</th>
                            <th>Creator</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= htmlspecialchars($post['post_id']) ?></td>
                            <td><?= htmlspecialchars(substr($post['content'], 0, 50)) . (strlen($post['content']) > 50 ? '...' : '') ?></td>
                            <td><?= htmlspecialchars($post['thread_title']) ?></td>
                            <td><?= htmlspecialchars($post['creator_name']) ?></td>
                            <td><?= date("M d, Y H:i", strtotime($post['created_at'])) ?></td>
                            <td>
                                <a href="edit_post.php?id=<?= $post['post_id'] ?>" 
                                   class="btn btn-warning btn-sm me-2" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete_post.php?id=<?= $post['post_id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete post by <?= addslashes($post['creator_name']) ?>?')"
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Reviews Table -->
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">Reviews</h5>
                <table id="reviewsTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Perfume</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Review Text</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?= htmlspecialchars($review['id']) ?></td>
                            <td><?= htmlspecialchars($review['perfume_name']) ?></td>
                            <td><?= htmlspecialchars($review['reviewer_name']) ?></td>
                            <td><?= htmlspecialchars($review['rating']) ?>/5</td>
                            <td><?= htmlspecialchars(substr($review['review_text'], 0, 50)) . (strlen($review['review_text']) > 50 ? '...' : '') ?></td>
                            <td><?= date("M d, Y H:i", strtotime($review['created_at'])) ?></td>
                            <td>
                                <a href="edit_review.php?id=<?= $review['id'] ?>" 
                                   class="btn btn-warning btn-sm me-2" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete_review.php?id=<?= $review['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete review by <?= addslashes($review['reviewer_name']) ?>?')"
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables for all tables
            $('#categoriesTable').DataTable({ "order": [[0, "asc"]], "pageLength": 10 });
            $('#threadsTable').DataTable({ "order": [[0, "desc"]], "pageLength": 10 });
            $('#postsTable').DataTable({ "order": [[0, "desc"]], "pageLength": 10 });
            $('#reviewsTable').DataTable({ "order": [[0, "desc"]], "pageLength": 10 });

            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });
    </script>
</body>
</html>