<?php

session_start();

include_once "model/dbh.inc.php";
include_once "controller/function.php";

if(isset($_SESSION['payment_success']) && $_SESSION['payment_success'] === true) {
    
    $totalAmount = $_SESSION['total_amount'];
    $status = 'pending';
    $orderDate = date('Y-m-d H:i:s');

    $createOrder = "INSERT INTO orders (user_id, total_price, status, order_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($createOrder);
    $stmt->bind_param("idss", $_SESSION['user_id'], $totalAmount, $status, $orderDate);
    $stmt->execute();

    $orderId = $conn->insert_id;

    $updateCart = "UPDATE cart SET order_id = ? WHERE user_id = ? AND order_id IS NULL";
    $stmt = $conn->prepare($updateCart);
    $stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
    $stmt->execute();

    $cartItems = returnCart($_SESSION['user_id']);
    while ($item = $cartItems->fetch_assoc()) {
        if($item['order_id'] == $orderId) {
            $productId = $item['product_id'];
            $orderedQty = $item['quantity'];
            
            $getStockQuery = "SELECT stock FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($getStockQuery);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentStock = $result->fetch_assoc()['stock'];
            
            $newStock = $currentStock - $orderedQty;
            
            $updateProductStock = "UPDATE products SET stock = ? WHERE product_id = ?";
            $stmt = $conn->prepare($updateProductStock);
            $stmt->bind_param("ii", $newStock, $productId);
            $stmt->execute();
        }
    }

    header("Location: order-confirmation.php?success=1&order_id=" . $orderId);

} else {
    header("Location: payment.php");
}

?>