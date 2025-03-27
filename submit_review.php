<?php
ob_start();
session_start();
require 'config.php';

echo "Step 1: Script started.<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Step 2: POST request received.<br>";

    if (!isset($_POST['perfume_id'], $_POST['rating'], $_POST['review'], $_POST['scent_impression'])) {
        die("Step 3: Missing input fields.");
    }

    $perfume_id = intval($_POST['perfume_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);
    $scent_impression = trim($_POST['scent_impression']);

    echo "Step 4: Data received -> Perfume ID: $perfume_id, Rating: $rating, Review: $review, Scent Impression: $scent_impression<br>";

    if ($rating < 1 || $rating > 5 || empty($review)) {
        die("Step 5: Invalid rating or review.");
    }

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        die("Step 6: User not logged in.");
    }

    echo "Step 7: User ID found: $user_id<br>";

    // Insert review into the database
    $sql = "INSERT INTO reviews (user_id, perfume_id, rating, review_text, scent_impression, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Step 8: SQL Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("iiiss", $user_id, $perfume_id, $rating, $review, $scent_impression);
    if ($stmt->execute()) {
        echo "Step 9: Review successfully inserted! Redirecting...";
        header("Location: perfume_details.php?perfume_id=" . $perfume_id);
        exit;
    } else {
        die("Step 10: Execute failed: " . $stmt->error);
    }

    $stmt->close();
} else {
    die("Step 11: No POST request received.");
}

ob_end_flush();
?>
