<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch reports with proper error handling
$sql = "
    SELECT 
        r.id AS report_id,
        r.reason,
        r.created_at,
        u.username AS reporter,
        CASE 
            WHEN r.content_type = 'post' THEN p.title
            WHEN r.content_type = 'comment' THEN c.content
        END AS content_title,
        r.content_type,
        r.content_id
    FROM reports r
    LEFT JOIN users u ON r.reporter_id = u.id
    LEFT JOIN forum_posts p ON (r.content_type = 'post' AND r.content_id = p.id)
    LEFT JOIN forum_comments c ON (r.content_type = 'comment' AND r.content_id = c.id)
    ORDER BY r.created_at DESC
";

$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Database Error: " . $conn->error);
}

$reports = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- Rest of your HTML remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reports</title>
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
                <h2 class="text-primary">Report Management</h2>
            </div>

            <div class="card shadow-sm p-3">
                <table id="reportsTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Reporter</th>
                            <th>Content Type</th>
                            <th>Content</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['report_id']) ?></td>
                            <td><?= htmlspecialchars($report['reporter']) ?></td>
                            <td>
                                <span class="badge bg-<?= $report['content_type'] == 'post' ? 'success' : 'info' ?>">
                                    <?= ucfirst($report['content_type']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($report['content_title']) ?: '<span class="text-muted">Deleted Content</span>' ?></td>
                            <td><?= htmlspecialchars($report['reason']) ?></td>
                            <td><?= date("M d, Y H:i", strtotime($report['created_at'])) ?></td>
                            <td>
                                <a href="<?= $report['content_type'] == 'post' ? 'view_post.php?id=' : 'view_comment.php?id=' ?><?= $report['content_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-report" 
                                        data-id="<?= $report['report_id'] ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this report?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="delete_report.php">
                        <input type="hidden" name="report_id" id="deleteReportId">
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#reportsTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10
            });

            // Delete button handler
            $('.delete-report').on('click', function() {
                const reportId = $(this).data('id');
                $('#deleteReportId').val(reportId);
                $('#deleteModal').modal('show');
            });
        });
    </script>
</body>
</html>