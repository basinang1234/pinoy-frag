<?php
require 'config.php';

$thread_id = $_GET['id'] ?? null;

// Update view count
$conn->query("UPDATE threads SET view_count = view_count + 1 WHERE thread_id = $thread_id");

// Get thread details
$query = "
    SELECT t.*, u.username 
    FROM threads t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.thread_id = $thread_id
";
$thread = $conn->query($query)->fetch_assoc();

// Get posts
$query = "
    SELECT p.*, u.username 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.thread_id = $thread_id
";
$posts = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($thread['title']) ?></title>
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
            display: flex;
            flex-direction: column;
        }

        .thread-container {
            flex: 1;
        }

        .original-post {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .post-author {
            color: var(--primary);
            font-weight: 500;
        }

        .post-date {
            color: #666;
            font-size: 0.9rem;
        }

        .post-content {
            margin: 1.5rem 0;
        }

        .replies-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .reply {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .reply:last-child {
            border-bottom: none;
        }

        .reply-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .reply-content {
            margin: 0.75rem 0;
        }

        .reply-form {
            position: sticky;
            bottom: 0;
            background: var(--card-bg);
            padding: 2rem;
            box-shadow: var(--shadow);
            border-radius: 12px;
        }

        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #eee;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-family: inherit;
            font-size: 1rem;
            resize: vertical;
            min-height: 150px;
        }

        button {
            background: var(--primary);
            color: white;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        button:hover {
            background: #5a41a8;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .original-post,
            .replies-container,
            .reply-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="thread-container">
        <div class="original-post">
            <div class="post-header">
                <h1><?= htmlspecialchars($thread['title']) ?></h1>
                <div class="post-metadata">
                    <div class="post-author"><?= htmlspecialchars($thread['username']) ?></div>
                    <div class="post-date"><?= date('M j, Y \a\t g:ia', strtotime($thread['created_at'])) ?></div>
                </div>
            </div>
            <div class="post-content">
                <?= nl2br(htmlspecialchars($thread['content'])) ?>
            </div>
        </div>

        <div class="replies-container">
            <?php while ($post = $posts->fetch_assoc()): ?>
                <div class="reply">
                    <div class="reply-header">
                        <div class="post-author"><?= htmlspecialchars($post['username']) ?></div>
                        <div class="post-date"><?= date('M j, Y \a\t g:ia', strtotime($post['created_at'])) ?></div>
                    </div>
                    <div class="reply-content">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="reply-form">
                <form method="POST" action="post_reply.php">
                    <textarea name="content" required placeholder="Write your reply..."></textarea>
                    <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
                    <button type="submit">Post Reply</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>