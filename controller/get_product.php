<?php
session_start();
include_once "function.php";
include_once "../model/dbh.inc.php";

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdministrator']) || ($_SESSION['isAdministrator'] != 1 && $_SESSION['isAdministrator'] != 2)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
        throw new Exception('Missing product ID');
    }

    $product_id = (int)$_GET['product_id'];

    // Fetch product data
    $sql = "CALL getProdData(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        throw new Exception('Product not found');
    }

    $product = $result->fetch_assoc();
    $stmt->close();

    // Fetch product details
    $sql_details = "CALL getProductDetails(?)";
    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bind_param("i", $product_id);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();
    $stmt_details->close();

    $details = [];
    if ($result_details && $result_details->num_rows > 0) {
        while ($detail = $result_details->fetch_assoc()) {
            $details[] = $detail;
        }
    }

    $product['details'] = $details;

    echo json_encode([
        'success' => true,
        'product' => $product
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>