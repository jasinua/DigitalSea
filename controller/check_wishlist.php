<?php
session_start();
include_once 'function.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['product_id'])) {
    echo 'false';
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

$sql = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

echo $result->num_rows > 0 ? 'true' : 'false';
?> 