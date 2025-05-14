<?php
session_start();
include_once "function.php"; // Assuming this contains DB connection and utility functions

header('Content-Type: application/json');

if (isLoggedIn($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Query to get total cart items
    $query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $count = $row['total'] ? (int)$row['total'] : 0;
    echo json_encode(['count' => $count]);
} else {
    echo json_encode(['count' => 0]);
}
?>