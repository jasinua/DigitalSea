<?php
session_start();
include 'api_functions.php';

// Define authentication credentials
$username = 'admin';
$password = 'secretpassword123';

// Encode the credentials to Base64
$authHeader = 'Basic ' . base64_encode("$username:$password");

// Manually set the `Authorization` header for the current request
$_SERVER['HTTP_AUTHORIZATION'] = $authHeader;

// Test authentication
try {
    authenticate_user();
    echo json_encode(["message" => "Authentication successful"]);
} catch (Exception $e) {
    echo json_encode(["message" => "Authentication failed", "error" => $e->getMessage()]);
}

// // Testing `get_all_products()` function
// echo "\n\nTesting get_all_products():\n";
// get_all_products();

// // Testing `get_products_by_id()` function with a product ID of 1
// echo "\n\nTesting get_products_by_id(1):\n";
// get_products_by_id(1);

// // Testing `add_to_cart()` function
// echo "\n\nTesting add_to_cart():\n";
// $user_id = 1;  // Example user ID
// $product_id = 1; // Example product ID
// add_to_cart($product_id, $user_id);
