<?php
session_start();
include '../model/dbh.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to rate products']);
    exit;
}

if (!isset($_POST['product_id']) || !isset($_POST['rating'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$rating = $_POST['rating'];

// Check if user has already rated this product
$check_sql = "SELECT product_rating_id FROM product_ratings WHERE user_id = ? AND product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing rating
    $row = $result->fetch_assoc();
    $rating_id = $row['product_rating_id'];
    $update_sql = "UPDATE product_ratings SET rating = ? WHERE product_rating_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $rating, $rating_id);
    
    if ($update_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Rating updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update rating']);
    }
} else {
    // Insert new rating
    $insert_sql = "INSERT INTO product_ratings (product_id, rating, user_id) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iii", $product_id, $rating, $user_id);
    
    if ($insert_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Rating added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add rating']);
    }
}

$conn->close();
?> 