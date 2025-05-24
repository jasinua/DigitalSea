<?php
session_start();
include_once "controller/function.php";
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $count = getWishlistCount($_SESSION['user_id']);
    echo json_encode(['count' => $count]);
} else {
    echo json_encode(['count' => 0]);
} 