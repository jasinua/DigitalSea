<?php
session_start();
require_once "function.php";
require_once "home.inc.php";
require_once "../model/dbh.inc.php";

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if product_id is set
if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'No product ID provided']);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = (int)$_POST['product_id'];

try {
    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $result = $stmt->execute();
    
    if ($result) {
        // Get updated cart count
        $cart_count = getCartCount($userId);
        
        // Return success response
        echo json_encode([
            'success' => true, 
            'message' => 'Item removed from cart successfully',
            'cartCount' => $cart_count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 