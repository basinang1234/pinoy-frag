<?php
require 'config.php';

// Restrict access to admins/moderators
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header("Location: home.php");
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Category</title>
    <style>
        :root {
            --primary: #6d4ebe;
            --accent: #4c9689;
            --bg: #f5f5f7;
            --card-bg: #ffffff;
            --shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 2rem;
            background: var(--bg);
            line-height: 1.6;
            min-height: 100vh;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            font-size: 2.25rem;
            text-align: center;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #444;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #eee;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .error {
            color: #ff6b6b;
            margin: 0.5rem 0;
            font-size: 0.9rem;
            padding: 0.5rem;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 4px;
        }

        button[type="submit"] {
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

        button[type="submit"]:hover {
            background: #5a41a8;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Create New Category</h1>
        
        <?php if (!empty($_SESSION['category_error'])): ?>
            <div class="error">
                <?= htmlspecialchars($_SESSION['category_error']) ?>
                <?php unset($_SESSION['category_error']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="create_category.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>

            <button type="submit">Create Category</button>
        </form>
    </div>
</body>
</html>