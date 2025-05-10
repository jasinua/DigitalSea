<?php
session_start();
include_once 'function.php';

if (!isset($_SESSION['user_id'])) {
    echo 'not_logged_in';
    exit();
}

if (!isset($_POST['product_id'])) {
    echo 'error';
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Check if product is already in wishlist
$sql = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Remove from wishlist
    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) {
        echo 'removed';
    } else {
        echo 'error';
    }
} else {
    // Add to wishlist
    $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) {
        echo 'added';
    } else {
        echo 'error';
    }
}
?> 