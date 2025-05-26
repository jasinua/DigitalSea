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
    if (!isset($_POST['product_id'])) {
        throw new Exception('Product ID is required');
    }

    $product_id = $_POST['product_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete product details first (due to foreign key constraint)
        $delete_details_sql = "CALL deleteProductDetails(?)";
        $delete_details_stmt = $conn->prepare($delete_details_sql);
        if (!$delete_details_stmt) {
            throw new Exception("Prepare details delete failed: " . $conn->error);
        }
        
        $delete_details_stmt->bind_param("i", $product_id);
        if (!$delete_details_stmt->execute()) {
            throw new Exception("Delete details failed: " . $delete_details_stmt->error);
        }

        // Delete the product
        $delete_product_sql = "CALL deleteProduct(?)";
        $delete_product_stmt = $conn->prepare($delete_product_sql);
        if (!$delete_product_stmt) {
            throw new Exception("Prepare product delete failed: " . $conn->error);
        }
        
        $delete_product_stmt->bind_param("i", $product_id);
        if (!$delete_product_stmt->execute()) {
            throw new Exception("Delete product failed: " . $delete_product_stmt->error);
        }

        // Delete the product image if it exists
        $image_path = "../images/product_{$product_id}.png";
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()]);
}
?> 