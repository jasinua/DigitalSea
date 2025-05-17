<?php
session_start();
require_once "../model/dbh.inc.php";
require_once "../controller/function.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$userId = $_SESSION['user_id'];
$cart_items = returnCart($userId);
$cart_count = 0;

while ($item = $cart_items->fetch_assoc()) {
    $cart_count += $item['quantity'];
}

echo json_encode(['count' => $cart_count]);
exit;
?>