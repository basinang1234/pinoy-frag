<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = trim($_POST['name']);
    $tagline = trim($_POST['tagline']) ?: 'na';
    $expertise = trim($_POST['expertise']) ?: 'na';
    $most_loved_perfume_id = !empty($_POST['most_loved_perfume_id']) ? (int)$_POST['most_loved_perfume_id'] : null;
    
    // Handle image upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../uploads/perfumers/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $image = $filePath;
            }
        }
    }

    // Insert into database
    $stmt = $conn->prepare("
        INSERT INTO perfumers 
        (name, tagline, expertise, most_loved_perfume_id, image) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssi", 
        $name,
        $tagline,
        $expertise,
        $most_loved_perfume_id,
        $image
    );

    if ($stmt->execute()) {
        header("Location: manage_perfumer.php");
        exit;
    } else {
        $error = "Error adding perfumer: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Fetch perfumes for dropdown
$perfumes = $conn->query("SELECT id, perfume_name FROM perfumes ORDER BY perfume_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Perfumer</title>
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
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Add New Perfumer</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="row g-3">
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
                                <option value="">Select Perfume</option>
                                <?php while ($perfume = $perfumes->fetch_assoc()): ?>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create Perfumer
                            </button>
                            <a href="manage_perfumer.php" class="btn btn-secondary ms-3">
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