<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

// Check if perfumer ID is provided
if (!isset($_GET['id'])) {
    die("Perfumer ID is missing.");
}

$perfumer_id = intval($_GET['id']);

// Fetch existing perfumer data
$stmt = $conn->prepare("
    SELECT p.*, 
           (SELECT perfume_name FROM perfumes WHERE id = p.most_loved_perfume_id) AS current_loved
    FROM perfumers p
    WHERE id = ?
");
$stmt->bind_param("i", $perfumer_id);
$stmt->execute();
$result = $stmt->get_result();
$perfumer = $result->fetch_assoc();

if (!$perfumer) {
    die("Perfumer not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $tagline = trim($_POST['tagline']) ?: 'na';
    $expertise = trim($_POST['expertise']) ?: 'na';
    $most_loved_perfume_id = !empty($_POST['most_loved_perfume_id']) ? (int)$_POST['most_loved_perfume_id'] : null;

    // Handle image upload
    $currentImage = $perfumer['image'];
    $newImage = $currentImage;
    
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
                $newImage = $filePath;
                // Remove old image if exists
                if (!empty($currentImage) && file_exists($currentImage)) {
                    unlink($currentImage);
                }
            }
        }
    }

    // Update database
    $sql = "
        UPDATE perfumers SET 
            name = ?,
            tagline = ?,
            expertise = ?,
            most_loved_perfume_id = ?,
            image = ?
        WHERE id = ?
    ";

    $updateStmt = $conn->prepare($sql);
    $updateStmt->bind_param("sssssi", 
        $name,
        $tagline,
        $expertise,
        $most_loved_perfume_id,
        $newImage,
        $perfumer_id
    );

    if ($updateStmt->execute()) {
        header("Location: manage_perfumer.php");
        exit;
    } else {
        $error = "Update failed: " . $updateStmt->error;
    }

    $updateStmt->close();
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
    <title>Edit Perfumer</title>
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
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Perfumer</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?= htmlspecialchars($perfumer['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" class="form-control" name="tagline" 
                                   value="<?= htmlspecialchars($perfumer['tagline']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Expertise</label>
                            <input type="text" class="form-control" name="expertise" 
                                   value="<?= htmlspecialchars($perfumer['expertise']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Most Loved Perfume</label>
                            <select class="form-select" name="most_loved_perfume_id">
                                <option value="">None</option>
                                <?php while ($perfume = $perfumes->fetch_assoc()): ?>
                                    <option value="<?= $perfume['id'] ?>"
                                        <?= ($perfume['id'] == $perfumer['most_loved_perfume_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($perfume['perfume_name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="image">
                            <?php if (!empty($perfumer['image'])): ?>
                                <img src="<?= htmlspecialchars($perfumer['image']) ?>" 
                                     class="mt-2 img-thumbnail" width="120">
                            <?php endif; ?>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Perfumer
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