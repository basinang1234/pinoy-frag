<?php
session_start();
require '../db.php';

// Admin access control
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Brand ID is missing.";
    header("Location: manage_brand.php");
    exit;
}

$brand_id = intval($_GET['id']);

// Fetch existing brand data
$stmt = $conn->prepare("SELECT * FROM brands WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $brand_id);
$stmt->execute();
$brand = $stmt->get_result()->fetch_assoc();

if (!$brand) {
    $_SESSION['error_message'] = "Brand not found.";
    header("Location: manage_brand.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']) ?: 'na';
    $website = filter_var(trim($_POST['website']), FILTER_VALIDATE_URL) ? trim($_POST['website']) : 'na';
    $currentImage = $brand['image'];
    $newImage = $currentImage;

    // Validate name
    if (empty($name)) {
        $_SESSION['error_message'] = "Brand name is required.";
        header("Location: edit_brand.php?id=" . $brand_id);
        exit;
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../uploads/brands/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Use secure permissions
        }

        $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        // Validate file type and size
        if ($_FILES['image']['size'] > $maxFileSize) {
            $_SESSION['error_message'] = "File size exceeds the maximum allowed limit of 2MB.";
            header("Location: edit_brand.php?id=" . $brand_id);
            exit;
        }

        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $newImage = $filePath;
                // Delete old image if it exists
                if ($currentImage !== 'na' && file_exists($currentImage)) {
                    unlink($currentImage);
                }
            } else {
                $_SESSION['error_message'] = "Failed to upload image.";
                header("Location: edit_brand.php?id=" . $brand_id);
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
            header("Location: edit_brand.php?id=" . $brand_id);
            exit;
        }
    }

    // Update database
    $stmt = $conn->prepare("UPDATE brands SET name = ?, description = ?, website = ?, image = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("ssssi", $name, $description, $website, $newImage, $brand_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Brand updated successfully.";
        header("Location: manage_brand.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Update failed: " . htmlspecialchars($stmt->error);
        header("Location: edit_brand.php?id=" . $brand_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Brand</h2>
        <form action="" method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Brand Name</label>
                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($brand['name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Brand Image</label>
                <input type="file" class="form-control" name="image">
                <?php if (!empty($brand['image']) && $brand['image'] !== 'na'): ?>
                    <div class="mt-2">
                        <img src="<?= htmlspecialchars($brand['image']) ?>" width="100" height="100" style="object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($brand['description']) ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Website URL</label>
                <input type="url" class="form-control" name="website" value="<?= htmlspecialchars($brand['website']) ?>" placeholder="https://example.com">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Save Changes
                </button>
                <a href="manage_brand.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>