<?php
require 'config.php';

// Fetch all perfumes with brand and perfumer details
$sql = "
    SELECT 
        p.id AS perfume_id,
        p.perfume_name,
        p.description,
        p.accords,
        p.notes,
        p.image AS perfume_image,
        p.fashion_styles,
        p.launch_year,
        p.perfumer_id,
        b.name AS brand_name,
        pf.name AS perfumer_name
    FROM perfumes p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN perfumers pf ON p.perfumer_id = pf.id
    ORDER BY b.name ASC, p.perfume_name ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}

$perfumes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfume Collections - Fragrance Haven</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #e74c3c;
            --light: #ffffff;
            --text-color: #333;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--text-color);
            margin: 0;
        }

        /* Perfume Grid */
        .perfume-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
        }

        /* Perfume Cards */
        .perfume-card {
            background: var(--light);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            cursor: pointer;
            position: relative;
        }

        .perfume-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* Adjusted Image Size */
        .perfume-image {
            width: 10%;
            height: 150px; /* Reduced from 220px to 150px */
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .perfume-name {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--primary);
        }

        .brand-name {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .perfume-details {
            margin-top: 1rem;
        }

        .perfume-details h3 {
            font-size: 1rem;
            color: var(--accent);
            margin-bottom: 0.3rem;
        }

        .perfume-details span {
            background: #f1f1f1;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            display: inline-block;
            font-size: 0.9rem;
            color: var(--text-color);
            margin: 0.2rem;
        }

        .perfumer-link {
            text-decoration: none;
            color: var(--accent);
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .perfume-grid {
                grid-template-columns: 1fr;
            }
            
            /* Further reduce image size for mobile */
            .perfume-image {
                height: 120px; /* Reduced from 180px to 120px */
            }
        }

        .collection-header {
            text-align: center;
            margin: 2rem 0;
        }

        .collection-header h1 {
            font-size: 2.2rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .collection-header p {
            font-size: 1.2rem;
            color: #666;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="collection-header">
    <h1>Perfume Collections</h1>
    <p>Explore our curated selection of fragrances</p>
</div>

<div class="perfume-grid">
    <?php foreach ($perfumes as $perfume): ?>
        <div class="perfume-card" onclick="window.location.href='perfume_details.php?perfume_id=<?= urlencode($perfume['perfume_id']) ?>'">
            <img src="<?= !empty($perfume['perfume_image']) ? 'uploads/perfume/' . htmlspecialchars($perfume['perfume_image']) : 'assets/perfume-placeholder.png' ?>" 
                alt="<?= htmlspecialchars($perfume['perfume_name']) ?>" class="perfume-image">
            
            <div class="perfume-name"><?= htmlspecialchars($perfume['perfume_name']) ?></div>
            <div class="brand-name"><?= htmlspecialchars($perfume['brand_name']) ?></div>
            
            <div class="perfume-details">
                <h3>Accords</h3>
                <?= !empty($perfume['accords']) ? 
                    '<span>' . implode('</span><span>', array_map('htmlspecialchars', explode(',', $perfume['accords']))) . '</span>' : 
                    '<span>N/A</span>' ?>
            </div>
            
            <div class="perfume-details">
                <h3>Notes</h3>
                <?= !empty($perfume['notes']) ? 
                    '<span>' . implode('</span><span>', array_map('htmlspecialchars', explode(',', $perfume['notes']))) . '</span>' : 
                    '<span>N/A</span>' ?>
            </div>
            
            <div class="perfume-details">
                <h3>Perfumer</h3>
                <?php if (!empty($perfume['perfumer_id']) && !empty($perfume['perfumer_name'])): ?>
                    <a href="perfumer_details.php?perfumer_id=<?= urlencode($perfume['perfumer_id']) ?>" class="perfumer-link">
                        <?= htmlspecialchars($perfume['perfumer_name']) ?>
                    </a>
                <?php else: ?>
                    <span>Unknown</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<br><br>

<!-- FOOTER (Unchanged) -->
<?php include 'footer.php'; ?>
</body>
</html>