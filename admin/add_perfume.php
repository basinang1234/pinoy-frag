<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand_id = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : null;
    $perfume_name = trim($_POST['perfume_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $accords = trim($_POST['accords'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $fashion_styles = trim($_POST['fashion_styles'] ?? '');
    $perfumer_id = isset($_POST['perfumer_id']) && $_POST['perfumer_id'] !== "" ? intval($_POST['perfumer_id']) : null;
    $family_ids = $_POST['family_ids'] ?? [];
    $launch_year = isset($_POST['launch_year']) && $_POST['launch_year'] !== "" ? intval($_POST['launch_year']) : null;

    if (!$brand_id || empty($perfume_name) || empty($family_ids)) {
        $error = "Brand, Perfume Name, and at least one Fragrance Family are required.";
    } else {
        $image = null;
        
        // Handle Image Upload
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "../uploads/perfume/";
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                $error = "Failed to create upload directory.";
            } else {
                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
                $maxSize = 2 * 1024 * 1024;

                if (!in_array($fileExt, $allowedTypes)) {
                    $error = "Invalid file type. Allowed: JPG, JPEG, PNG, WEBP.";
                } elseif ($_FILES['image']['size'] > $maxSize) {
                    $error = "File too large. Max size: 2MB.";
                } else {
                    $fileName = uniqid() . "." . $fileExt;
                    $filePath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                        $image = $fileName;
                    } else {
                        $error = "Error uploading image.";
                    }
                }
            }
        }

        if (!$error) {
            // Insert perfume details
            $stmt = $conn->prepare("INSERT INTO perfumes 
                (brand_id, perfume_name, description, accords, notes, fashion_styles, image, perfumer_id, launch_year) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("issssssis", $brand_id, $perfume_name, $description, $accords, $notes, $fashion_styles, $image, $perfumer_id, $launch_year);

            if ($stmt->execute()) {
                $perfume_id = $conn->insert_id;

                // Insert into perfume_families table
                $stmt_family = $conn->prepare("INSERT INTO perfume_families (perfume_id, family_id) VALUES (?, ?)");
                foreach ($family_ids as $family_id) {
                    $stmt_family->bind_param("ii", $perfume_id, $family_id);
                    $stmt_family->execute();
                }
                $stmt_family->close();
                
                $success = "Perfume added successfully! ID: $perfume_id";
                header("Location: manage_perfume.php");
                exit;
            } else {
                $error = "Error adding perfume: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch dropdown options
$brands = $conn->query("SELECT id, name FROM brands ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$perfumers = $conn->query("SELECT id, name FROM perfumers ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$families = $conn->query("SELECT id, name FROM fragrance_families ORDER BY name")->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
        <!-- Include Sidebar -->
        <?php require 'admin_dashboard.php'; ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Perfume</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Add New Perfume</h2>
        <?php if ($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
        <?php if ($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Brand Name</label>
                <select class="form-select" name="brand_id" required>
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Perfume Name</label>
                <input type="text" class="form-control" name="perfume_name" required>
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Accords</label>
                <input type="text" class="form-control" name="accords">
            </div>

            <div class="col-md-6">
                <label class="form-label">Notes</label>
                <input type="text" class="form-control" name="notes">
            </div>

            <div class="col-md-6">
                <label class="form-label">Fashion Styles</label>
                <input type="text" class="form-control" name="fashion_styles">
            </div>

            <div class="col-12">
                <label class="form-label">Fragrance Families</label><br>
                <?php foreach ($families as $family): ?>
                    <input type="checkbox" name="family_ids[]" value="<?= $family['id'] ?>"> <?= htmlspecialchars($family['name']) ?> &nbsp;
                <?php endforeach; ?>
            </div>

            <div class="col-md-6">
                <label class="form-label">Launch Year</label>
                <input type="number" class="form-control" name="launch_year">
            </div>

            <div class="col-md-6">
                <label class="form-label">Perfumer</label>
                <select class="form-select" name="perfumer_id">
                    <option value="">Select Perfumer</option>
                    <?php foreach ($perfumers as $perfumer): ?>
                        <option value="<?= $perfumer['id'] ?>"><?= htmlspecialchars($perfumer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Image</label>
                <input type="file" class="form-control" name="image">
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-success">Save Perfume</button>
                <a href="manage_perfume.php" class="btn btn-secondary ms-3">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
