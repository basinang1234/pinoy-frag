<?php
require 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['content_id'], $data['content_type'], $data['value'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$content_id = (int) $data['content_id'];
$content_type = $data['content_type'];
$value = (int) $data['value'];
$user_id = $_SESSION['user_id'];

if (!in_array($content_type, ['post', 'comment']) || !in_array($value, [-1, 1])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid vote type or value"]);
    exit();
}

// Check if the user has already voted
$voteStmt = $conn->prepare("SELECT value FROM votes WHERE user_id = ? AND content_type = ? AND content_id = ?");
$voteStmt->bind_param("isi", $user_id, $content_type, $content_id);
$voteStmt->execute();
$result = $voteStmt->get_result();
$existingVote = $result->fetch_assoc();

if ($existingVote) {
    if ($existingVote['value'] == $value) {
        // Remove vote if the same vote is being made (toggle effect)
        $deleteStmt = $conn->prepare("DELETE FROM votes WHERE user_id = ? AND content_type = ? AND content_id = ?");
        $deleteStmt->bind_param("isi", $user_id, $content_type, $content_id);
        $deleteStmt->execute();
        echo json_encode(["message" => "Vote removed"]);
    } else {
        // Update the existing vote
        $updateStmt = $conn->prepare("UPDATE votes SET value = ? WHERE user_id = ? AND content_type = ? AND content_id = ?");
        $updateStmt->bind_param("iisi", $value, $user_id, $content_type, $content_id);
        $updateStmt->execute();
        echo json_encode(["message" => "Vote updated"]);
    }
} else {
    // Insert new vote
    $insertStmt = $conn->prepare("INSERT INTO votes (user_id, content_type, content_id, value) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("isii", $user_id, $content_type, $content_id, $value);
    $insertStmt->execute();
    echo json_encode(["message" => "Vote added"]);
}
?>
