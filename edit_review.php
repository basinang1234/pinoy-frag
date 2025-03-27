<?php
ob_start(); // Start output buffering
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['review_id']) || !isset($_POST['rating']) || !isset($_POST['review']) || !isset($_POST['scent_impression'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$reviewId = intval($_POST['review_id']);
$rating = intval($_POST['rating']);
$reviewText = trim($_POST['review']);
$scentImpression = trim($_POST['scent_impression']);

// Check if the review belongs to the logged-in user
$checkSql = "SELECT * FROM reviews WHERE id = ? AND user_id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ii", $reviewId, $userId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

// Update the review
$updateSql = "UPDATE reviews SET rating = ?, review_text = ?, scent_impression = ?, created_at = NOW() WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("issi", $rating, $reviewText, $scentImpression, $reviewId);
$updateStmt->execute();

ob_end_clean(); // Clear any unwanted output before redirecting

// Redirect back to the perfume details page
header("Location: perfume_details.php?perfume_id=" . urlencode($_POST['perfume_id']));
exit;
?>
