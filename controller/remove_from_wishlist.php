<?php
    session_start();
    include_once "function.php";
    include_once "../model/dbh.inc.php";

    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id']) || !isset($_POST['product_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not logged in or missing product_id']);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

    // Remove the product from the wishlist
    $stmt = $conn->prepare("CALL removeFromWishlist(?,?)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Item not found or not removed']);
    }
    exit();
?> 