<?php
require 'config.php'; // Include MySQLi configuration

// Fetch all unique perfume brands from the 'brands' table
$sql = "SELECT id, name, image, description FROM brands ORDER BY name ASC";
$result = $conn->query($sql);

$brands = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brands - Fragrance Haven</title>
    <style>
        :root {
            --primary-color: #2A2A2A;
            --accent-color: #E63946;
            --text-light: #F8F9FA;
            --background-light: #FFFFFF;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--background-light);
            color: var(--primary-color);
            line-height: 1.6;
        }

        .brands-header {
            text-align: center;
            margin: 2rem 0;
        }

        .brands-header h1 {
            font-size: 2.5rem;
            color: var(--accent-color);
        }

        .brands-header p {
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .brands-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .brand-card {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .brand-card:hover {
            transform: translateY(-5px);
        }

        .brand-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .brand-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--accent-color);
        }

        .brand-card p {
            font-size: 1rem;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .brands-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="brands-header">
        <h1>Explore Perfume Brands</h1>
        <p>Discover your favorite fragrance brands and their collections</p>
    </div>

    <div class="brands-container">
        <?php if (!empty($brands)): ?>
            <?php foreach ($brands as $brand): ?>
                <a href="brand_details.php?brand_id=<?= $brand['id'] ?>" class="brand-card">
                    <img src="<?= !empty($brand['image']) && $brand['image'] !== 'na' ? 'brands/' . htmlspecialchars($brand['image']) : 'uploads/brands/' ?>" 
                         alt="<?= htmlspecialchars($brand['name']) ?>">
                    <h3><?= htmlspecialchars($brand['name']) ?></h3>
                    <p><?= $brand['description'] !== 'na' ? htmlspecialchars($brand['description']) : 'Click to view all perfumes' ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No brands found.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
