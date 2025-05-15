<?php
include '../model/dbh.inc.php';

if (!isset($_POST['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$product_id = $_POST['product_id'];

$rating_sql = "SELECT AVG(rating) as avg_rating FROM product_ratings WHERE product_id = ?";
$rating_stmt = $conn->prepare($rating_sql);
$rating_stmt->bind_param("i", $product_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'average_rating' => number_format($rating_data['avg_rating'] ?? 0, 2)
]);

$conn->close();
?> 