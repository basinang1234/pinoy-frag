<?php
include 'db.php';

// Get today's visit count
$queryDaily = "SELECT COUNT(*) as daily FROM visits WHERE DATE(timestamp) = CURDATE()";
$resultDaily = mysqli_fetch_assoc(mysqli_query($conn, $queryDaily));

// Get weekly visit count
$queryWeekly = "SELECT COUNT(*) as weekly FROM visits WHERE YEARWEEK(timestamp, 1) = YEARWEEK(CURDATE(), 1)";
$resultWeekly = mysqli_fetch_assoc(mysqli_query($conn, $queryWeekly));

// Get monthly visit count
$queryMonthly = "SELECT COUNT(*) as monthly FROM visits WHERE MONTH(timestamp) = MONTH(CURDATE()) AND YEAR(timestamp) = YEAR(CURDATE())";
$resultMonthly = mysqli_fetch_assoc(mysqli_query($conn, $queryMonthly));

// Return JSON response
echo json_encode([
    'daily' => $resultDaily['daily'] ?? 0,
    'weekly' => $resultWeekly['weekly'] ?? 0,
    'monthly' => $resultMonthly['monthly'] ?? 0
]);
?>
