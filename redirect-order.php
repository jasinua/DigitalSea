<?php

session_start();

include_once "model/dbh.inc.php";

if(isset($_SESSION['payment_success']) && $_SESSION['payment_success'] === true) {
    
    $totalAmount = $_SESSION['total_amount'];

    $createOrder = "INSERT INTO orders (user_id, total_price, status, order_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($createOrder);
    $stmt->bind_param("idss", $_SESSION['user_id'], $totalAmount, $status, $orderDate);
    $stmt->execute();

    $orderId = $conn->insert_id;

    $updateCart = "UPDATE cart SET order_id = ? WHERE user_id = ? AND order_id IS NULL";
    $stmt = $conn->prepare($updateCart);
    $stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
    $stmt->execute();
    

    header("Location: order-confirmation.php?success=1&order_id=" . $orderId);

} else {
    header("Location: payment.php");
}

?>