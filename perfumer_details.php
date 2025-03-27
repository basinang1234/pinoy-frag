<?php
ob_start(); // Start output buffering
require 'config.php'; // Include MySQLi configuration

// Admin access control (optional: remove if not required)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get the selected perfumer ID from the query string
if (!isset($_GET['perfumer_id']) || !is_numeric($_GET['perfumer_id'])) {
    $_SESSION['error_message'] = "Invalid Perfumer ID.";
    header("Location: brands.php");
    exit;
}

$perfumerId = intval($_GET['perfumer_id']); // Ensure it's an integer

// Fetch perfumer details
$sql = "SELECT id, name, tagline, expertise, most_loved_perfume_id, image FROM perfumers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $perfumerId);
$stmt->execute();
$result = $stmt->get_result();
$perfumer = $result->fetch_assoc();

// If no perfumer found, redirect
if (!$perfumer) {
    $_SESSION['error_message'] = "Perfumer not found.";
    header("Location: brands.php");
    exit;
}

// Fetch perfumes created by the perfumer
$sql = "SELECT id, perfume_name, description, image FROM perfumes WHERE perfumer_id = ? ORDER BY perfume_name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $perfumerId);
$stmt->execute();
$result = $stmt->get_result();
$perfumes = $result->fetch_all(MYSQLI_ASSOC);

// Fetch most loved perfume details (if available)
$mostLovedPerfume = null;
if ($perfumer['most_loved_perfume_id']) {
    $sql = "SELECT id, perfume_name, description, image FROM perfumes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $perfumer['most_loved_perfume_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $mostLovedPerfume = $result->fetch_assoc();
}

ob_end_flush(); // Flush the buffer and allow output
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($perfumer['name']) ?> - Fragrance Haven</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #222;
            --secondary-color: #555;
            --accent-color: #E63946;
            --text-light: #F8F9FA;
            --background-light: #FAFAFA;
            --background-dark: #1E1E1E;
            --card-bg: #FFF;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--background-light);
            color: var(--primary-color);
            line-height: 1.6;
            transition: var(--transition);
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        .perfumer-header {
            text-align: center;
            margin: 2rem 0;
            padding: 2rem;
            background: var(--accent-color);
            color: var(--text-light);
            border-radius: 10px;
        }

        .perfumer-header img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--text-light);
            margin-bottom: 1rem;
        }

        .perfumer-header h1 {
            font-size: 2.5rem;
        }

        .perfumer-info {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }

        .perfumer-info h2 {
            border-left: 5px solid var(--accent-color);
            padding-left: 10px;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .perfumer-info p {
            font-size: 1rem;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .perfumes-section {
            margin-top: 2rem;
        }

        .perfume-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .perfume-card {
            background: var(--card-bg);
            padding: 1rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
            overflow: hidden;
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
            margin-bottom: 1rem;
        }

        .perfume-card h3 {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .perfume-card p {
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: var(--background-dark);
                color: var(--text-light);
            }
            .perfumer-info, .perfume-card {
                background: #2D2D2D;
                color: var(--text-light);
                box-shadow: none;
            }
            .perfume-card:hover {
                box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
            }
        }

        @media (max-width: 768px) {
            .perfumer-header img {
                width: 120px;
                height: 120px;
            }
            .perfumer-header h1 {
                font-size: 2rem;
            }
            .perfume-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Perfumer Header -->
    <div class="perfumer-header">
    <img src="<?= !empty($perfumer['image']) ? 'perfumer/' . htmlspecialchars($perfumer['image']) : 'assets/perfume-placeholder.png' ?>" 
        alt="<?= htmlspecialchars($perfumer['name'] ?? 'Unknown Perfumer') ?>">
    <h1><?= htmlspecialchars($perfumer['name']) ?></h1>
    <p><em><?= htmlspecialchars($perfumer['tagline'] ?? 'No tagline available') ?></em></p>
</div>
    <!-- Perfumer Info -->
    <div class="perfumer-info">
        <h2>About</h2>
        <p><strong>Expertise:</strong> <?= htmlspecialchars($perfumer['expertise'] ?? 'Not specified') ?></p>
        <?php if ($mostLovedPerfume): ?>
            <p><strong>Most Loved Perfume:</strong> <?= htmlspecialchars($mostLovedPerfume['perfume_name']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Perfumes Section -->
    <div class="perfumes-section">
        <h2>Perfumes Created</h2>
        <div class="perfume-grid">
            <?php if (!empty($perfumes)): ?>
                <?php foreach ($perfumes as $perfume): ?>
                    <div class="perfume-card"> 
                            <img src="<?= !empty($perfume['image']) ? 'uploads/perfume/' . htmlspecialchars($perfume['image']) : 'assets/perfume-placeholder.png' ?>" 
                 alt="<?= htmlspecialchars($perfume['perfume_name']) ?>" class="img-fluid rounded shadow">
                        <h3><?= htmlspecialchars($perfume['perfume_name']) ?></h3>
                        <p><?= htmlspecialchars($perfume['description'] ?? 'No description available') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No perfumes created by this perfumer.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>