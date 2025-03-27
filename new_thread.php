<?php
require 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Generate CSRF token if needed
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("Invalid request");
    }

    // Sanitize inputs
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    
    $errors = [];

    // Input validation
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    if (empty($content)) {
        $errors[] = "Content is required";
    }
    
    // Verify category exists
    $stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_id = ?");
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $errors[] = "Invalid category selected";
    }

    // Proceed if no errors
    if (empty($errors)) {
        $user_id = $_SESSION['user_id'];
        $status = 'open'; // Default status for new threads
        
        $stmt = $conn->prepare("
            INSERT INTO threads 
            (category_id, user_id, title, content, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param('iisss', $category_id, $user_id, $title, $content, $status);
        
        if ($stmt->execute()) {
            $new_thread_id = $stmt->insert_id;
            header("Location: thread.php?id=$new_thread_id");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}

// Fetch categories for dropdown
$categories_query = $conn->query("SELECT category_id, name FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Thread</title>
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
            max-width: 800px;
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

        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #eee;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(109, 78, 190, 0.2);
        }

        .form-group textarea {
            min-height: 200px;
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

        button[type="submit"]:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Create New Thread</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="thread-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category_id" id="category" required>
                    <?php while ($category = $categories_query->fetch_assoc()): ?>
                        <option value="<?= $category['category_id'] ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required 
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" rows="8" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>

            <button type="submit">Create Thread</button>
        </form>
    </div>
</body>
</html>