<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
// Start the session
session_start();

// Include configuration and database files
include_once "config/stripe-config.php";
include_once "model/dbh.inc.php";
include_once "controller/function.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user ID
$userId = $_SESSION['user_id'];

// Get cart items for this user
$cartItems = returnCart($userId);
if (!$cartItems || $cartItems->num_rows === 0) {
    header("Location: cart.php");
    exit;
}

// Calculate total amount
$subtotal = 0;
$discount = 0;
$mergedCart = [];

// Process cart items
while ($item = $cartItems->fetch_assoc()) {
    $pid = $item['product_id'];
    $qty = $item['quantity'];
    
    // Get product details
    $productResult = returnProduct($pid);
    if ($productResult && $product = $productResult->fetch_assoc()) {
        $price = $product['price'];
        $productDiscount = $product['discount'];
        
        // Calculate discounted price
        if ($productDiscount > 0) {
            $finalPrice = $price - ($price * $productDiscount / 100);
            $discount += ($price - $finalPrice) * $qty;
        } else {
            $finalPrice = $price;
        }
        
        // Add to subtotal
        $subtotal += $price * $qty;
    }
}

// Calculate tax and final total
$tax = $subtotal * 0.18; // 18% VAT
$totalAmount = ($subtotal + $tax - $discount) * 100; // Amount in cents for Stripe

// If a form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Handle AJAX requests (coming from fetch/XMLHttpRequest)
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // This is coming from our JavaScript AJAX request
        
        try {
            // Require Stripe PHP library
            require_once('vendor/autoload.php');
            
            // Set API key
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            
            // Process payment intent data from POST request
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);

            // Debug: log incoming data
            file_put_contents('stripe_debug.log', $jsonStr . PHP_EOL, FILE_APPEND);

            $amount = isset($jsonObj->amount) ? intval($jsonObj->amount) : $totalAmount;
            $email = isset($jsonObj->email) ? $jsonObj->email : null;
            $name = isset($jsonObj->name) ? $jsonObj->name : null;

            if($jsonObj->payment_method_id) {
                // Create a PaymentIntent with the payment method
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => STRIPE_CURRENCY,
                    'payment_method' => $jsonObj->payment_method_id,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'description' => 'Order from DigitalSea',
                    'receipt_email' => $email,
                    'metadata' => [
                        'user_id' => $userId,
                        'customer_name' => $name
                    ]
                ]);
                
                if($paymentIntent->status == 'requires_action' && $paymentIntent->next_action->type == 'use_stripe_sdk') {
                    // Payment needs additional authentication
                    echo json_encode([
                        'requires_action' => true,
                        'payment_intent_client_secret' => $paymentIntent->client_secret
                    ]);
                } else if($paymentIntent->status == 'succeeded') {
                    // Payment succeeded, process order
                    echo json_encode([
                        'success' => true
                    ]);
                    
                    // Clear the user's cart (to be implemented)
                    // createOrder($userId, $paymentIntent->id); (to be implemented)
                    
                } else {
                    // Invalid status
                    http_response_code(500);
                    echo json_encode(['error' => 'Payment failed with status: ' . $paymentIntent->status]);
                }
            } else if($jsonObj->payment_intent_id) {
                // Confirm the PaymentIntent
                $paymentIntent = \Stripe\PaymentIntent::retrieve($jsonObj->payment_intent_id);
                $paymentIntent->confirm();
                
                if($paymentIntent->status == 'succeeded') {
                    // Payment succeeded
                    echo json_encode(['success' => true]);
                    
                    // Clear the user's cart (to be implemented)
                    // createOrder($userId, $paymentIntent->id); (to be implemented)
                    
                } else {
                    // Payment failed
                    http_response_code(500);
                    echo json_encode(['error' => 'Payment failed with status: ' . $paymentIntent->status]);
                }
            } else {
                // Invalid request
                http_response_code(500);
                echo json_encode(['error' => 'Invalid payment data']);
            }
        } catch (\Stripe\Exception\CardException $e) {
            // Card error
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            // Generic error
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }
    
    // For form submission, redirect to payment page with Stripe elements
    header("Location: payment.php");
    exit;
}

// If not a POST request, redirect to cart
header("Location: cart.php");
exit;
?> 