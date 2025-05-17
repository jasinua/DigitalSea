<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdministrator']) || $_SESSION['isAdministrator'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include_once "../model/dbh.inc.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $discount = !empty($_POST['discount']) ? (float) $_POST['discount'] : 0;
    
    // Get the next product ID
    $sql = "SELECT MAX(product_id) as max_id FROM products";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $next_id = ($row['max_id'] ?? 0) + 1;
    
    $image_url = '';
    
    // Handle image based on source
    if ($_POST['image_source'] === 'file' && isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        $temp_name = $_FILES['product_image']['tmp_name'];
        $new_filename = "product_{$next_id}.png";
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($temp_name, $upload_path)) {
            $image_url = $upload_path;
        } else {
            throw new Exception('Error uploading image');
        }
    } elseif ($_POST['image_source'] === 'url' && !empty($_POST['image_url'])) {
        $image_url = $_POST['image_url'];
    } else {
        throw new Exception('Please provide either an image file or URL');
    }
    
    // Insert product into database
    $sql = "INSERT INTO products (name, description, price, stock, discount, image_url) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdisd", $name, $description, $price, $stock, $discount, $image_url);
    
    if (!$stmt->execute()) {
        throw new Exception('Error adding product: ' . $conn->error);
    }
    
    $product_id = $conn->insert_id;
    
    // Process product details
    if (isset($_POST['details_key']) && isset($_POST['details_value'])) {
        foreach ($_POST['details_key'] as $index => $key) {
            if (!empty($key) && !empty($_POST['details_value'][$index])) {
                $value = $_POST['details_value'][$index];
                $detail_sql = "INSERT INTO product_details (product_id, prod_desc1, prod_desc2) VALUES (?, ?, ?)";
                $detail_stmt = $conn->prepare($detail_sql);
                $detail_stmt->bind_param("iss", $product_id, $key, $value);
                if (!$detail_stmt->execute()) {
                    throw new Exception('Error adding product details: ' . $conn->error);
                }
            }
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Product added successfully!']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 