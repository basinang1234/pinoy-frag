<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch perfumers with most loved perfume names
$sql = "SELECT 
            pf.id, pf.name, pf.tagline, pf.expertise, pf.image, 
            COALESCE(p.perfume_name, 'None') AS most_loved_name 
        FROM perfumers pf 
        LEFT JOIN perfumes p ON pf.most_loved_perfume_id = p.id 
        ORDER BY pf.id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . $conn->error);
}

$perfumers = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Perfumers</title>
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
                <h2 class="text-primary">Perfumer Management</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPerfumerModal">
                    <i class="bi bi-person-plus"></i> Add Perfumer
                </button>
            </div>

            <div class="card shadow-sm p-3">
                <table id="perfumersTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Tagline</th>
                            <th>Expertise</th>
                            <th>Most Loved</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perfumers as $perfumer): ?>
                        <tr>
                            <td><?= htmlspecialchars($perfumer['id']) ?></td>
                            <td>
                                <?php if (!empty($perfumer['image'])): ?>
                                    <img src="<?= htmlspecialchars($perfumer['image']) ?>" 
                                         class="rounded-circle" width="50" height="50" 
                                         style="object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-person-circle fs-3 text-secondary"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($perfumer['name']) ?></td>
                            <td><?= isset($perfumer['tagline']) ? htmlspecialchars($perfumer['tagline']) : '<span class="text-muted">N/A</span>' ?></td>
                            <td><?= isset($perfumer['expertise']) ? htmlspecialchars($perfumer['expertise']) : '<span class="text-muted">N/A</span>' ?></td>
                            <td><?= htmlspecialchars($perfumer['most_loved_name']) ?></td>
                            <td>
                                <a href="edit_perfumer.php?id=<?= $perfumer['id'] ?>" 
                                   class="btn btn-warning btn-sm me-2" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete_perfumer.php?id=<?= $perfumer['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete <?= addslashes($perfumer['name']) ?>?')"
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

    <!-- Add Perfumer Modal -->
    <div class="modal fade" id="addPerfumerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Perfumer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="add_perfumer.php" method="post" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" class="form-control" name="tagline">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Expertise</label>
                            <input type="text" class="form-control" name="expertise">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Most Loved Perfume</label>
                            <select class="form-select" name="most_loved_perfume_id">
                                <option value="">None</option>
                                <?php
                                $perfumes = $conn->query("SELECT id, perfume_name FROM perfumes ORDER BY perfume_name");
                                while ($perfume = $perfumes->fetch_assoc()):
                                ?>
                                <option value="<?= $perfume['id'] ?>">
                                    <?= htmlspecialchars($perfume['perfume_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="image">
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Save Perfumer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#perfumersTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10
            });
            
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });
    </script>
</body>
</html>
