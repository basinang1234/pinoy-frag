<?php
session_start();
include 'db_connect.php'; // Ensure this file connects using MySQLi and assigns $conn

// Fetch post details
$post_id = intval($_GET['id']);
$post = $conn->query("SELECT forum_posts.*, users.username FROM forum_posts JOIN users ON forum_posts.user_id = users.id WHERE forum_posts.id = $post_id")->fetch_assoc();

// Fetch comments
$comments = $conn->query("SELECT forum_comments.*, users.username FROM forum_comments JOIN users ON forum_comments.user_id = users.id WHERE post_id = $post_id ORDER BY created_at ASC");

// Handle new comment
if (isset($_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $content = $conn->real_escape_string($_POST['content']);
    $parent_comment_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : 'NULL';

    $sql = "INSERT INTO forum_comments (post_id, user_id, parent_comment_id, content) VALUES ('$post_id', '$user_id', $parent_comment_id, '$content')";
    $conn->query($sql);
    header("Location: post_view.php?id=$post_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($post['title']) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><strong>By:</strong> <?= htmlspecialchars($post['username']) ?></p>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

    <h2>Leave a Comment</h2>
    <form method="POST">
        <textarea name="content" placeholder="Write your comment..." required></textarea>
        <button type="submit" name="comment">Comment</button>
    </form>

    <h2>Comments</h2>
    <ul>
        <?php while ($comment = $comments->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                <?= nl2br(htmlspecialchars($comment['content'])) ?>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
