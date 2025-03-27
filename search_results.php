<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// Initialize search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$resultsPerPage = 6;
$offset = ($page - 1) * $resultsPerPage;

$results = [];
$totalPages = 0;

if (!empty($query)) {
    $searchTerm = "%" . strtolower($query) . "%";

    $sql = "SELECT 
                p.id, 
                p.perfume_name, 
                b.name AS brand_name, 
                p.accords, 
                p.notes, 
                p.fashion_styles, 
                p.image, 
                pf.name AS perfumer_name
            FROM perfumes p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN perfumers pf ON p.perfumer_id = pf.id
            WHERE 
                LOWER(p.perfume_name) LIKE ? 
                OR LOWER(b.name) LIKE ? 
                OR LOWER(pf.name) LIKE ? 
                OR LOWER(p.accords) LIKE ? 
                OR LOWER(p.notes) LIKE ? 
                OR LOWER(p.fashion_styles) LIKE ? 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ssssssii", 
            $searchTerm, $searchTerm, $searchTerm,
            $searchTerm, $searchTerm, $searchTerm,
            $resultsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        die("Query Error: " . $conn->error);
    }

    $countSql = "SELECT COUNT(*) as total 
                 FROM perfumes p
                 LEFT JOIN brands b ON p.brand_id = b.id
                 LEFT JOIN perfumers pf ON p.perfumer_id = pf.id
                 WHERE 
                    LOWER(p.perfume_name) LIKE ? 
                    OR LOWER(b.name) LIKE ? 
                    OR LOWER(pf.name) LIKE ? 
                    OR LOWER(p.accords) LIKE ? 
                    OR LOWER(p.notes) LIKE ? 
                    OR LOWER(p.fashion_styles) LIKE ?";
    
    $countStmt = $conn->prepare($countSql);
    
    if ($countStmt) {
        $countStmt->bind_param("ssssss", 
            $searchTerm, $searchTerm, $searchTerm,
            $searchTerm, $searchTerm, $searchTerm);
        $countStmt->execute();
        $countStmt->bind_result($totalResults);
        $countStmt->fetch();
        $totalPages = ceil($totalResults / $resultsPerPage);
        $countStmt->close();
    } else {
        die("Count Error: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfume Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .search-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-header h2 {
            font-size: 2rem;
            color: var(--accent-color);
        }

        .search-results {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

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
            transform: translateY(-5px);
        }

        .perfume-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        .perfume-details h5 {
            font-size: 1.2rem;
            margin-top: 10px;
            color: var(--accent-color);
        }

        .perfume-details p {
            font-size: 1rem;
            color: var(--primary-color);
        }

        .pagination {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- HEADER (Unchanged) -->
    <?php include 'header.php'; ?>

    <div class="search-container">
        <?php if (!empty($query)): ?>
            <div class="alert alert-info text-center">
                Searching for: <strong><?= htmlspecialchars($query) ?></strong>
            </div>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
            <div class="search-results">
                <?php foreach ($results as $perfume): ?>
                    <div class="perfume-card">
                        <a href="perfume_details.php?perfume_id=<?= urlencode($perfume['id']) ?>" class="text-decoration-none text-dark">
                            <?php if (!empty($perfume['image']) && $perfume['image'] != 'na'): ?>
                                <img src="<?= htmlspecialchars($perfume['image']) ?>" 
                                     alt="<?= htmlspecialchars($perfume['perfume_name']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary text-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span>No Image</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="perfume-details">
                                <h5><?= htmlspecialchars($perfume['perfume_name']) ?></h5>
                                <p><strong>Brand:</strong> <?= htmlspecialchars($perfume['brand_name']) ?></p>
                                <?php if (!empty($perfume['perfumer_name'])): ?>
                                    <p><small>By <?= htmlspecialchars($perfume['perfumer_name']) ?></small></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?query=<?= urlencode($query) ?>&page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-warning text-center">
                <h4>No results found for "<?= htmlspecialchars($query) ?>"</h4>
                <p>Try adjusting your search terms or checking for typos</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- FOOTER (Unchanged) -->
    <?php include 'footer.php'; ?>
<!-- Ensure Bootstrap JavaScript & Popper.js are included -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
