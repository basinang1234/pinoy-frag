<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch brands
$sql = "SELECT * FROM brands ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Database Error: " . $conn->error);
}

$brands = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <!-- Include Sidebar -->
        <?php require 'admin_dashboard.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid px-4 flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <h2 class="text-primary">Brand Management</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                    <i class="bi bi-plus-lg"></i> Add Brand
                </button>
            </div>

            <div class="card shadow-sm p-3">
                <table id="brandsTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Website</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                        <tr>
                            <td><?= htmlspecialchars($brand['id']) ?></td>
                            <td>
                                <?php if (!empty($brand['image']) && $brand['image'] !== 'na'): ?>
                                    <img src="<?= htmlspecialchars($brand['image']) ?>" 
                                         class="rounded-circle" width="50" height="50" 
                                         style="object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-image fs-3 text-secondary"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($brand['name']) ?></td>
                            <td>
                                <?= !empty($brand['description']) && $brand['description'] !== 'na' 
                                    ? htmlspecialchars($brand['description']) 
                                    : '<span class="text-muted">No description</span>' ?>
                            </td>
                            <td>
                                <?php if (!empty($brand['website']) && $brand['website'] !== 'na'): ?>
                                    <a href="<?= htmlspecialchars($brand['website']) ?>" target="_blank">
                                        <?= htmlspecialchars($brand['website']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No website</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_brand.php?id=<?= $brand['id'] ?>" 
                                   class="btn btn-warning btn-sm me-2" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete_brand.php?id=<?= $brand['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete <?= addslashes($brand['name']) ?>?')"
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

    <!-- Add Brand Modal -->
    <div class="modal fade" id="addBrandModal">
        <!-- Modal Content -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add_brand.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Brand Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Brand Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
