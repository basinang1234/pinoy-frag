<?php
require 'config.php'; // Include MySQLi configuration

// Prepare SQL statement
$sql = "
    SELECT p.id, p.perfume_name, b.name AS brand_name, p.image, 
           AVG(r.rating) AS avg_rating, COUNT(r.id) AS review_count
    FROM perfumes p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN reviews r ON p.id = r.perfume_id
    WHERE r.rating >= 4
    GROUP BY p.id, b.name, p.image
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 10";

$result = $conn->query($sql);

$topPerfumes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topPerfumes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Fragrance Haven</title>
    <style>
        :root {
            --primary-color: #2A2A2A;
            --accent-color: #E63946;
            --text-light: #F8F9FA;
            --background-light: #FFFFFF;
            --shadow: 0px 6px 20px rgba(0, 0, 0, 0.15);
            --border-radius: 10px;
            --transition: all 0.3s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: var(--background-light);
            color: var(--primary-color);
        }

        /* Wrapper to protect header/footer */
        .leaderboard-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .leaderboard-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .leaderboard-header h1 {
            font-size: 2.5rem;
            color: var(--accent-color);
        }

        .leaderboard-header p {
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        /* Leaderboard Grid */
        .leaderboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        /* Perfume Card */
        .perfume-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            text-align: center;
            padding: 15px;
        }

        .perfume-card:hover {
            transform: translateY(-8px);
        }

        .perfume-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        .perfume-details {
            padding: 10px 0;
        }

        .perfume-details h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: var(--accent-color);
        }

        .perfume-details p {
            font-size: 1rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .perfume-rating {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 1.1rem;
            color: #FFD700;
        }

        .perfume-rating span {
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .leaderboard-container {
                grid-template-columns: 1fr;
            }

            .perfume-card img {
                height: 220px;
            }
        }
    </style>
</head>
<body>

    <div class="leaderboard-content">
        <div class="leaderboard-header">
            <h1>Top Fragrances</h1>
            <p>Discover the most loved perfumes by our community</p>
        </div>

        <div class="leaderboard-container">
            <?php if (!empty($topPerfumes)): ?>
                <?php foreach ($topPerfumes as $perfume): ?>
                    <div class="perfume-card">
                        <a href="perfume_details.php?perfume_id=<?= urlencode($perfume['id']) ?>">
                            <img src="<?= !empty($perfume['image']) ? 'uploads/perfumes/' . htmlspecialchars($perfume['image']) : 'assets/perfume-placeholder.png' ?>" 
                                 alt="<?= htmlspecialchars($perfume['perfume_name']) ?>">
                        </a>
                        <div class="perfume-details">
                            <h3>
                                <a href="perfume_details.php?perfume_id=<?= urlencode($perfume['id']) ?>">
                                    <?= htmlspecialchars($perfume['perfume_name']) ?>
                                </a>
                            </h3>
                            <p><strong>Brand:</strong> <?= htmlspecialchars($perfume['brand_name']) ?></p>
                            <div class="perfume-rating">
                                <span>★</span>
                                <span><?= number_format($perfume['avg_rating'], 1) ?></span>
                                <span>(<?= $perfume['review_count'] ?> reviews)</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; font-size: 1.2rem; color: gray;">No top-rated perfumes yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER (Unchanged) -->
    <?php include 'footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>
