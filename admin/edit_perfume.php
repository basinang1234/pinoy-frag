<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Check if perfume ID is provided
if (!isset($_GET['id'])) {
    die("Perfume ID is missing.");
}

$perfume_id = intval($_GET['id']);

// Fetch existing perfume data
$stmt = $conn->prepare("
    SELECT p.*, 
           (SELECT name FROM perfumers WHERE id = p.perfumer_id) AS current_perfumer
    FROM perfumes p 
    WHERE id = ?
");
$stmt->bind_param("i", $perfume_id);
$stmt->execute();
$result = $stmt->get_result();
$perfume = $result->fetch_assoc();

if (!$perfume) {
    die("Perfume not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $brand_name = trim($_POST['brand_name']) ?: 'na';
    $perfume_name = trim($_POST['perfume_name']);
    $description = trim($_POST['description']) ?: 'na';
    $accords = trim($_POST['accords']) ?: 'na';
    $notes = trim($_POST['notes']) ?: 'na';
    $fashion_styles = trim($_POST['fashion_styles']) ?: 'na';
    $perfumer_id = $_POST['perfumer_id'] ?: null;

    // Handle image upload
    $currentImage = $perfume['image'];
    $newImage = $currentImage;
    
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../uploads/perfume/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $newImage = $filePath;
            }
        }
    }

    // Update database
    $sql = "
        UPDATE perfumes SET 
            brand_name = ?,
            perfume_name = ?,
            description = ?,
            accords = ?,
            notes = ?,
            fashion_styles = ?,
            image = ?,
            perfumer_id = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi",
        $brand_name,
        $perfume_name,
        $description,
        $accords,
        $notes,
        $fashion_styles,
        $newImage,
        $perfumer_id,
        $perfume_id
    );

    if ($stmt->execute()) {
        header("Location: manage_perfume.php");
        exit;
    } else {
        $error = "Update failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Fetch perfumers for dropdown
$perfumers = $conn->query("SELECT id, name FROM perfumers ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Perfume</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <!-- Include Sidebar -->
        <?php require 'admin_dashboard.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid px-4 flex-grow-1">
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Perfume</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Brand Name</label>
                            <input type="text" class="form-control" name="brand_name" 
                                   value="<?= htmlspecialchars($perfume['brand_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Perfume Name</label>
                            <input type="text" class="form-control" name="perfume_name" 
                                   value="<?= htmlspecialchars($perfume['perfume_name']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($perfume['description']) ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Accords</label>
                            <input type="text" class="form-control" name="accords" 
                                   value="<?= htmlspecialchars($perfume['accords']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" name="notes" 
                                   value="<?= htmlspecialchars($perfume['notes']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fashion Styles</label>
                            <input type="text" class="form-control" name="fashion_styles" 
                                   value="<?= htmlspecialchars($perfume['fashion_styles']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Perfumer</label>
                            <select class="form-select" name="perfumer_id">
                                <option value="">No Perfumer</option>
                                <?php while ($perfumer = $perfumers->fetch_assoc()): ?>
                                    <option value="<?= $perfumer['id'] ?>" 
                                        <?= ($perfumer['id'] == $perfume['perfumer_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($perfumer['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image">
                            <?php if (!empty($perfume['image'])): ?>
                                <img src="<?= htmlspecialchars($perfume['image']) ?>" 
                                     class="mt-2 img-thumbnail" width="100">
                            <?php endif; ?>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Update Perfume
                            </button>
                            <a href="manage_perfume.php" class="btn btn-secondary ms-3">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>