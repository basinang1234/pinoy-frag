<?php
require 'config.php'; // Use your database connection file

// Fetch trending perfumes from the database
$query = "
    SELECT p.id AS perfume_id, p.perfume_name, p.image, b.name AS brand_name, p.description
    FROM perfumes p
    LEFT JOIN brands b ON p.brand_id = b.id
    ORDER BY p.created_at DESC 
    LIMIT 6
";
$result = $conn->query($query);
$perfumes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch popular threads (replacing forums)
$threadQuery = "
    SELECT t.thread_id, t.title, t.content, t.view_count,
           (SELECT COUNT(*) FROM posts WHERE thread_id = t.thread_id) AS post_count,
           (SELECT content FROM posts 
            WHERE thread_id = t.thread_id 
            ORDER BY created_at DESC 
            LIMIT 1) AS latest_post,
           (SELECT u.username FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.thread_id = t.thread_id 
            ORDER BY p.created_at DESC 
            LIMIT 1) AS latest_author,
           (SELECT created_at FROM posts 
            WHERE thread_id = t.thread_id 
            ORDER BY created_at DESC 
            LIMIT 1) AS latest_date
    FROM threads t
    ORDER BY post_count DESC 
    LIMIT 6
";
$threadResult = $conn->query($threadQuery);
$threads = $threadResult ? $threadResult->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Haven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<section class="container my-4">
    <h2 class="text-primary">Trending Perfumes</h2>
    <div class="row">
        <?php foreach ($perfumes as $perfume): ?>
<div class="col-md-4">
    <a href="perfume_details.php?perfume_id=<?= htmlspecialchars($perfume['perfume_id']) ?>" class="card text-decoration-none text-dark">
        <img src="<?= !empty($perfume['image']) && $perfume['image'] !== 'na' 
        ? 'uploads/perfume/' . htmlspecialchars($perfume['image']) 
        : 'uploads/perfume/default.jpg' ?>" 
        class="card-img-top img-fluid" 
        style="object-fit: cover; height: 150px; width: 50%;" 
        alt="<?= htmlspecialchars($perfume['perfume_name']) ?>">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($perfume['perfume_name']) ?></h5>
            <p class="card-text">
                <strong>Brand:</strong> <?= htmlspecialchars($perfume['brand_name']) ?><br>
                <?= htmlspecialchars($perfume['description'] ?? 'No description available') ?>
            </p>
        </div>
    </a>
</div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container my-4">
    <h2 class="text-primary">Popular Threads</h2>
    <div class="row">
        <?php foreach ($threads as $thread): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="uploads/perfume/default.jpg" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($thread['title']) ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="thread.php?id=<?= htmlspecialchars($thread['thread_id']) ?>" class="text-decoration-none">
                                <?= htmlspecialchars($thread['title']) ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <?= htmlspecialchars($thread['post_count']) ?> posts
                            <?php if ($thread['latest_post']): ?>
                                • Latest: <?= htmlspecialchars($thread['latest_post']) ?>
                            <?php endif; ?>
                            <br>
                            <small>
                                <?php if ($thread['latest_author']): ?>
                                    by <?= htmlspecialchars($thread['latest_author']) ?>
                                <?php endif; ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
