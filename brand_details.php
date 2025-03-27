<?php
ob_start(); // Start output buffering to prevent header errors
require 'config.php'; // Database connection

// Ensure brand_id is provided in URL
if (!isset($_GET['brand_id']) || !is_numeric($_GET['brand_id'])) {
    header("Location: index.php");
    exit;
}

$brandId = intval($_GET['brand_id']); // Convert to integer for security

// Fetch brand details (removing 'logo' column)
$sql = "SELECT id, name, description FROM brands WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query Error: " . $conn->error);
}

$stmt->bind_param("i", $brandId);
$stmt->execute();
$result = $stmt->get_result();
$brand = $result->fetch_assoc();

// If no brand found, show error message
if (!$brand) {
    die("<h2 class='text-center text-danger'>No brand found for ID $brandId</h2>");
}

// Fetch perfumes of this brand
$sql = "
    SELECT id, perfume_name, description, image, launch_year 
    FROM perfumes 
    WHERE brand_id = ?
    ORDER BY launch_year DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $brandId);
$stmt->execute();
$result = $stmt->get_result();
$perfumes = $result->fetch_all(MYSQLI_ASSOC);

ob_end_flush(); // Flush output buffer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($brand['name']) ?> - Fragrance Haven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .brand-header {
            text-align: center;
            padding: 50px 20px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            border-radius: 0 0 20px 20px;
        }
        .brand-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .brand-description {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .perfume-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
            padding: 15px;
        }
        .perfume-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .perfume-card img {
            width: 100%;
            max-width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Brand Header -->
    <div class="brand-header">
        <h1><?= htmlspecialchars($brand['name']) ?></h1>
    </div>

    <!-- Brand Description -->
    <div class="container mt-4">
        <div class="brand-description">
            <p><?= nl2br(htmlspecialchars($brand['description'] ?? 'No description available.')) ?></p>
        </div>

        <!-- Perfume List -->
        <h2 class="text-center mt-5">Perfumes by <?= htmlspecialchars($brand['name']) ?></h2>
        <div class="row mt-3">
            <?php if (!empty($perfumes)): ?>
                <?php foreach ($perfumes as $perfume): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="perfume-card">
                            <img src="<?= !empty($perfume['image']) ? 'uploads/perfume/' . htmlspecialchars($perfume['image']) : 'assets/perfume-placeholder.png' ?>" 
                                 alt="<?= htmlspecialchars($perfume['perfume_name']) ?>">
                            <h4><?= htmlspecialchars($perfume['perfume_name']) ?></h4>
                            <p><strong>Launch Year:</strong> <?= htmlspecialchars($perfume['launch_year'] ?? 'N/A') ?></p>
                            <p><?= substr(htmlspecialchars($perfume['description'] ?? 'No description available.'), 0, 100) ?>...</p>
                            <a href="perfume_details.php?perfume_id=<?= urlencode($perfume['id']) ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">No perfumes found for this brand.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
