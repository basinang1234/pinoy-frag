<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch perfumes with perfumer, brand names, fragrance families, and launch year
$sql = "SELECT 
            p.id, 
            b.name AS brand_name, 
            p.perfume_name, 
            p.description, 
            p.accords, 
            p.notes, 
            p.fashion_styles, 
            p.image, 
            p.launch_year,
            p.created_at, 
            pf.name AS perfumer_name,
            GROUP_CONCAT(ff.name SEPARATOR ', ') AS fragrance_families
        FROM perfumes p
        LEFT JOIN perfumers pf ON p.perfumer_id = pf.id
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN perfume_families pfam ON p.id = pfam.perfume_id
        LEFT JOIN fragrance_families ff ON pfam.family_id = ff.id
        GROUP BY p.id
        ORDER BY p.id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . $conn->error);
}

$perfumes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Perfumes</title>
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
                <h2 class="text-primary">Perfume Management</h2>
                <a href="add_perfume.php" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Add Perfume
                </a>
            </div>

            <div class="card shadow-sm p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Perfume Name</th>
                                <th>Brand</th>
                                <th>Perfumer</th>
                                <th>Launch Year</th>
                                <th>Notes</th>
                                <th>Accords</th>
                                <th>Styles</th>
                                <th>Fragrance Families</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($perfumes as $perfume): ?>
                            <tr>
                                <td><?= htmlspecialchars($perfume['id']) ?></td>
                                <td>
                                    <?php if (!empty($perfume['image'])): ?>
                                        <img src="<?= htmlspecialchars($perfume['image']) ?>" 
                                             class="rounded" width="60" height="60" 
                                             style="object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-image fs-3 text-secondary"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($perfume['perfume_name']) ?></td>
                                <td><?= htmlspecialchars($perfume['brand_name'] ?: 'N/A') ?></td>
                                <td><?= htmlspecialchars($perfume['perfumer_name'] ?: 'N/A') ?></td>
                                <td><?= htmlspecialchars($perfume['launch_year'] ?: 'Unknown') ?></td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" 
                                          title="<?= htmlspecialchars($perfume['notes'] ?: 'N/A') ?>">
                                        <?= htmlspecialchars($perfume['notes'] ?: 'N/A') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($perfume['accords'] ?: 'N/A') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($perfume['fashion_styles'] ?: 'N/A') ?></td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" 
                                          title="<?= htmlspecialchars($perfume['fragrance_families'] ?: 'N/A') ?>">
                                        <?= htmlspecialchars($perfume['fragrance_families'] ?: 'N/A') ?>
                                    </span>
                                </td>
                                <td><?= date("M d, Y", strtotime($perfume['created_at'])) ?></td>
                                <td>
                                    <a href="edit_perfume.php?id=<?= $perfume['id'] ?>" 
                                       class="btn btn-warning btn-sm me-2">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="delete_perfume.php?id=<?= $perfume['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Delete <?= addslashes($perfume['perfume_name']) ?>?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
