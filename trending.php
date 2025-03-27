<?php
require_once 'config.php'; // Use your database connection file

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Current page
$limit = 5; // Number of items per page
$offset = ($page - 1) * $limit;

// Fetch trending perfumes with pagination
$query = "
    SELECT 
        p.id AS perfume_id,
        b.name AS brand_name,
        p.perfume_name,
        p.description,
        p.accords,
        p.notes,
        p.image AS perfume_image,
        pf.name AS perfumer_name,
        pf.expertise AS perfumer_expertise,
        pf.image AS perfumer_image,
        AVG(r.rating) AS avg_rating
    FROM perfumes p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN perfumers pf ON p.perfumer_id = pf.id
    LEFT JOIN reviews r ON p.id = r.perfume_id
    GROUP BY p.id, b.name, p.perfume_name, p.description, p.accords, p.notes, p.image, pf.name, pf.expertise, pf.image
    HAVING AVG(r.rating) = 5 -- Only include perfumes with a perfect 5-star rating
    ORDER BY p.created_at DESC -- Sort by creation date (or any other criteria)
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$trendingPerfumes = $result->fetch_all(MYSQLI_ASSOC);

// Count total perfumes with 5-star ratings for pagination
$countQuery = "
    SELECT COUNT(DISTINCT p.id) AS total_count
    FROM perfumes p
    LEFT JOIN reviews r ON p.id = r.perfume_id
    GROUP BY p.id
    HAVING AVG(r.rating) = 5
";
$countResult = $conn->query($countQuery);
$totalPerfumes = $countResult->num_rows;
$totalPages = ceil($totalPerfumes / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Rated Perfumes - Fragrance Haven</title>
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

        main {
            padding: 2rem;
        }

        .trending-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .trending-card {
            background: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform var(--transition), box-shadow var(--transition);
            cursor: pointer; /* Add pointer cursor for clickable cards */
        }

        .trending-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .trending-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .trending-card .content {
            padding: 1rem;
        }

        .trending-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--accent-color);
        }

        .trending-card p {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #555;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 1rem;
        }

        .pagination a {
            text-decoration: none;
            color: var(--accent-color);
            font-weight: bold;
            padding: 0.5rem 1rem;
            border: 1px solid var(--accent-color);
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .pagination a:hover {
            background: var(--accent-color);
            color: var(--text-light);
        }

        .pagination .disabled {
            pointer-events: none;
            color: #ccc;
            border-color: #ccc;
        }
    </style>
</head>
<body>
    <main>
        <div class="trending-container">
            <?php if (!empty($trendingPerfumes)): ?>
                <?php foreach ($trendingPerfumes as $perfume): ?>
                    <a href="perfume_details.php?perfume_id=<?= htmlspecialchars($perfume['perfume_id']) ?>" class="trending-card">
                        <?php
                        $perfumeImagePath = !empty($perfume['perfume_image']) && $perfume['perfume_image'] !== 'na'
                            ? htmlspecialchars($perfume['perfume_image'])
                            : 'uploads/perfumes';
                        ?>
                        <img src="<?= $perfumeImagePath ?>" alt="<?= htmlspecialchars($perfume['perfume_name']) ?>">
                        <div class="content">
                            <h3><?= htmlspecialchars($perfume['perfume_name']) ?></h3>
                            <p><strong>Brand:</strong> <?= htmlspecialchars($perfume['brand_name']) ?></p>
                            <div class="perfumer">
                                <?php if (!empty($perfume['perfumer_image']) && $perfume['perfumer_image'] !== 'na'): ?>
                                    <img src="<?= htmlspecialchars($perfume['perfumer_image']) ?>" alt="<?= htmlspecialchars($perfume['perfumer_name']) ?>">
                                <?php endif; ?>
                                <p><strong>Perfumer:</strong> <?= htmlspecialchars($perfume['perfumer_name'] ?? 'Unknown') ?></p>
                            </div>
                            <p><strong>Expertise:</strong> <?= htmlspecialchars($perfume['perfumer_expertise'] ?? 'N/A') ?></p>
                            <p><strong>Notes:</strong> <?= htmlspecialchars($perfume['notes'] ?? 'N/A') ?></p>
                            <div class="stats">
                                <span><i>⭐</i> Rating: <?= number_format($perfume['avg_rating'], 1) ?: 'N/A' ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">No trending perfumes found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">Previous</a>
            <?php else: ?>
                <span class="disabled">Previous</span>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next</a>
            <?php else: ?>
                <span class="disabled">Next</span>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>