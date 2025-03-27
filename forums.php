<?php
require 'config.php';

// Get all categories with thread counts
$query = "
    SELECT c.*, 
           (SELECT COUNT(*) FROM threads t WHERE t.category_id = c.category_id) AS thread_count
    FROM categories c
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Haven Forum</title>
    <style>
        :root {
            --primary: #6d4ebe;
            --accent: #4c9689;
            --bg: #f5f5f7;
            --card-bg: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Global reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            background: var(--bg);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }

        h1 {
            color: var(--primary);
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .category-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .category {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease;
        }

        .category:hover {
            transform: translateY(-4px);
        }

        .category h2 {
            color: var(--primary);
            font-size: 1.5rem;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .category p {
            color: #666;
            margin: 1rem 0;
        }

        .category a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            border-radius: 25px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .category a:hover {
            background: #5a41a8;
        }

        .create-thread-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--accent);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            box-shadow: var(--shadow);
            text-decoration: none;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .admin-button {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: var(--primary);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            box-shadow: var(--shadow);
            text-decoration: none;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            .category-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <h1>Fragrance Haven Forum</h1>
    
    <div class="category-container">
        <?php while ($category = $result->fetch_assoc()): ?>
            <div class="category">
                <h2><?= htmlspecialchars($category['name']) ?></h2>
                <p><?= htmlspecialchars($category['description']) ?></p>
                <a href="category.php?id=<?= $category['category_id'] ?>">
                    View <?= $category['thread_count'] ?> Threads
                </a>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="new_thread.php" class="create-thread-btn">New Thread</a>
        <?php if (in_array($_SESSION['role'], ['user', 'admin', 'moderator'])): ?>
            <a href="new_category.php" class="admin-button">New Category</a>
        <?php endif; ?>
    <?php endif; ?>
<br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
</body>
</html>

<?php include 'footer.php' ?>