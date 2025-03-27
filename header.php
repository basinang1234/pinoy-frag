<?php
require_once 'db.php'; // Use your database connection file
// Function to handle login
if (!function_exists('handleLogin')) {
    function handleLogin($conn) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id, $hashed_password, $role);
        
        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            
            header("Location: " . ($role === 'admin' ? '/admin/admin_dashboard.php' : 'index.php'));
            exit;
        }
        
        $stmt->close();
        echo "Invalid credentials or user not found";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    handleLogin($conn);
}

// Handle sign-up form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Handle profile picture upload with validation
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($imageFileType, $allowed_types) && move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, profile_picture) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $username, $email, $password, $target_file);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        $target_file = 'uploads/default.png';
    }
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Fetch user details if logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinoy Scents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="navbar navbar-expand-lg bg-light shadow-sm sticky-top">
    <div class="container">
        <a href="index.php" class="navbar-brand d-flex align-items-center">
            <img src="assets/pinoyscents.png" alt="Pinoy Scents" width="40" class="me-2">
            <span class="fw-bold">Pinoy Scents</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="trending.php" class="nav-link">Trending</a></li>
                <li class="nav-item"><a href="forums.php" class="nav-link">Forums</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">More</a>
                    <ul class="dropdown-menu">
                        <li><a href="leaderboard.php" class="dropdown-item">Leaderboard</a></li>
                        <li><a href="brands.php" class="dropdown-item">Brands</a></li>
                        <li><a href="collections.php" class="dropdown-item">Collections</a></li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex ms-3" method="GET" action="search_results.php">
                <input class="form-control me-2" type="text" name="query" placeholder="Search perfumes, notes, or perfumers...">
                <button class="btn btn-outline-danger" type="submit">Search</button>
            </form>
            <?php if ($user): ?>
                <button class="btn btn-outline-primary ms-3" data-bs-toggle="modal" data-bs-target="#profileModal">
                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" width="30" height="30" class="rounded-circle"> 
                    <?= htmlspecialchars($user['username']) ?>
                </button>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                </form>
            <?php else: ?>
                <button class="btn btn-outline-primary ms-3" data-bs-toggle="modal" data-bs-target="#authModal">Login / Sign Up</button>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Authentication Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login / Sign Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>
                <hr>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                    <input type="file" name="profile_picture" class="form-control mb-2" accept="image/*">
                    <button type="submit" name="signup" class="btn btn-success w-100">Sign Up</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
