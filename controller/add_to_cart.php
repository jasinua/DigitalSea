<?php
session_start();
include_once "function.php";
include_once "home.inc.php";

// Ensure we're sending JSON response
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to send JSON response
function sendJsonResponse($success, $message, $cartCount = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($cartCount !== null) {
        $response['cartCount'] = $cartCount;
    }
    echo json_encode($response);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(false, 'User not logged in');
}

// Check required parameters
if (!isset($_POST['product_id']) || !isset($_POST['price'])) {
    sendJsonResponse(false, 'Missing required parameters');
}

try {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = 1; // Default quantity
    $price = $_POST['price'];

    // Debug information
    error_log("Attempting to add to cart - User ID: $user_id, Product ID: $product_id, Quantity: $quantity, Price: $price");

    // Validate inputs
    if (!is_numeric($product_id) || !is_numeric($price)) {
        sendJsonResponse(false, 'Invalid product ID or price');
    }

    $result = addToCart($user_id, $product_id, $quantity, $price);

    if ($result) {
        // Get updated cart count
        $cart_count = getCartCount($user_id);
        sendJsonResponse(true, 'Product added to cart successfully', $cart_count);
    } else {
        // Get the last database error
        global $conn;
        $error = mysqli_error($conn);
        error_log("Database error: " . $error);
        sendJsonResponse(false, 'Failed to add product to cart: ' . $error);
    }
} catch (Exception $e) {
    error_log("Exception in add_to_cart.php: " . $e->getMessage());
    sendJsonResponse(false, 'An error occurred: ' . $e->getMessage());
} 