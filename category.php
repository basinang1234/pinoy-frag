<?php
// Start output buffering
ob_start();

require 'config.php';

$category_id = $_GET['id'] ?? null;

// Redirect handling
if (!$category_id) {
    $_SESSION['error'] = "Invalid category request";
    header("Location: home.php");
    exit();
}

// Validate category exists
$stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

if (!$category) {
    $_SESSION['error'] = "Category not found";
    header("Location: home.php");
    exit();
}

// Get threads in category
$query = "
    SELECT t.*, u.username 
    FROM threads t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.category_id = ?
    ORDER BY t.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$threads = $stmt->get_result();

// Clear output buffer before sending HTML
ob_clean();
?>

<!DOCTYPE html>
<!-- Keep your existing HTML/CSS code -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name']) ?></title>
    <style>
        :root {
            --primary: #6d4ebe;
            --accent: #4c9689;
            --bg: #f5f5f7;
            --card-bg: #ffffff;
            --shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 2rem;
            background: var(--bg);
            line-height: 1.6;
            min-height: 100vh;
        }

        .category-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .category-title {
            color: var(--primary);
            font-size: 2.5rem;
            margin: 0;
            padding: 1rem 0;
            border-bottom: 2px solid var(--accent);
        }

        .thread-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .thread-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .thread-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .thread-title {
            color: var(--primary);
            font-size: 1.25rem;
            margin: 0 0 0.75rem;
        }

        .thread-meta {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .view-count {
            background: var(--accent);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            float: right;
            transition: background 0.3s ease;
        }

        .thread-card:hover .view-count {
            background: #3d7a72;
        }

        .thread-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-top: 0.5rem;
            transition: color 0.3s ease;
        }

        .thread-link:hover {
            color: #5a41a8;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .category-title {
                font-size: 2rem;
            }
            
            .thread-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="category-header">
        <h1 class="category-title"><?= htmlspecialchars($category['name']) ?></h1>
    </div>

    <div class="thread-list">
        <?php while ($thread = $threads->fetch_assoc()): ?>
            <div class="thread-card">
                <div class="view-count"><?= $thread['view_count'] ?> views</div>
                <h3 class="thread-title"><?= htmlspecialchars($thread['title']) ?></h3>
                <div class="thread-meta">
                    By <?= htmlspecialchars($thread['username']) ?> 
                    on <?= date('M j, Y', strtotime($thread['created_at'])) ?>
                </div>
                <a href="thread.php?id=<?= $thread['thread_id'] ?>" class="thread-link">
                    View Thread →
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>