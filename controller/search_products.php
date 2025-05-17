<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdministrator']) || $_SESSION['isAdministrator'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include_once "../model/dbh.inc.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    if (empty($search)) {
        // If search is empty, return all products
        $sql = "SELECT * FROM products WHERE api_source IS NULL";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
    } else {
        // Search in name, description, and product details
        $sql = "SELECT DISTINCT p.* 
                FROM products p 
                LEFT JOIN product_details pd ON p.product_id = pd.product_id 
                WHERE p.api_source IS NULL 
                AND (
                    p.name LIKE ? 
                    OR p.description LIKE ? 
                    OR pd.prod_desc1 LIKE ? 
                    OR pd.prod_desc2 LIKE ?
                )";
        $searchTerm = "%{$search}%";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $products = [];

    while ($row = $result->fetch_assoc()) {
        // Get product details for each product
        $details_sql = "SELECT * FROM product_details WHERE product_id = ?";
        $details_stmt = $conn->prepare($details_sql);
        if (!$details_stmt) {
            throw new Exception("Prepare details failed: " . $conn->error);
        }
        
        $details_stmt->bind_param("i", $row['product_id']);
        if (!$details_stmt->execute()) {
            throw new Exception("Execute details failed: " . $details_stmt->error);
        }
        
        $details_result = $details_stmt->get_result();
        
        $details = [];
        while ($detail = $details_result->fetch_assoc()) {
            $details[] = $detail;
        }
        
        $row['details'] = $details;
        $products[] = $row;
    }

    echo json_encode(['success' => true, 'products' => $products]);

} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error searching products: ' . $e->getMessage()
    ]);
} 