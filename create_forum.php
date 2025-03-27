<?php
require 'config.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $user_id = $_SESSION['user_id'];
    $imagePath = "";

    // Debugging output
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "User ID: " . htmlspecialchars($user_id) . "\n";
    echo "</pre>";

    if (empty($name) || empty($description)) {
        die("Error: Forum name and description are required.");
    }

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate image type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        } else {
            die("Error uploading image.");
        }
    }

    // Check if database connection exists
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO forums (name, description, user_id, image, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssiss", $name, $description, $user_id, $imagePath);

    if ($stmt->execute()) {
        header("Location: index.php?success=Forum created successfully");
        exit();
    } else {
        echo "SQL Error: " . $stmt->error;
        echo "<br>SQL: INSERT INTO forums (name, description, user_id, image, created_at) VALUES ('$name', '$description', '$user_id', '$imagePath', NOW())";
        exit();
    }
}
?>
