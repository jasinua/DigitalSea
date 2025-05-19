<?php
session_start();
include_once "function.php";
include_once "../model/dbh.inc.php";

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdministrator']) || $_SESSION['isAdministrator'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['stock'])) {
        throw new Exception('Missing required fields');
    }

    $product_id = $_POST['product_id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $discount = !empty($_POST['discount']) ? (float)$_POST['discount'] : 0;

    // Validate discount (cannot exceed 100%)
    if ($discount > 100) {
        throw new Exception('Discount cannot exceed 100%');
    }

    $is_update = !empty($product_id);

    // Handle image upload
    $image_url = '';
    if ($_POST['image_source'] === 'file' && isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
            throw new Exception('Upload directory is not accessible or writable');
        }
        $temp_name = $_FILES['product_image']['tmp_name'];
        $new_filename = $is_update ? "product_{$product_id}.png" : "product_temp.png";
        $upload_path = $upload_dir . $new_filename;
        
        if (!move_uploaded_file($temp_name, $upload_path)) {
            throw new Exception('Error uploading image');
        }
        $image_url = "images/{$new_filename}";
    } elseif ($_POST['image_source'] === 'url' && !empty($_POST['image_url'])) {
        $image_url = $_POST['image_url'];
    } elseif ($is_update) {
        $check_sql = "SELECT image_url FROM products WHERE product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_url = $row['image_url'];
        } else {
            throw new Exception('Product not found');
        }
        $check_stmt->close();
    } else {
        throw new Exception('Please provide either an image file or URL');
    }

    // Log the received data
    error_log("Updating product ID: " . $product_id);
    error_log("Received data: " . print_r($_POST, true));

    if ($is_update) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, discount = ?, image_url = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $discount, $image_url, $product_id);
    } else {
        $sql = "INSERT INTO products (name, description, price, stock, discount, image_url) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $discount, $image_url);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if (!$is_update) {
        $product_id = $conn->insert_id;
        // Rename the uploaded image file to include the new product_id
        if ($_POST['image_source'] === 'file' && file_exists($upload_dir . "product_temp.png")) {
            $new_image_path = $upload_dir . "product_{$product_id}.png";
            rename($upload_dir . "product_temp.png", $new_image_path);
            $image_url = "images/product_{$product_id}.png";
            // Update the image_url in the database
            $update_sql = "UPDATE products SET image_url = ? WHERE product_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $image_url, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }

    // Process product details
    $details = [];
    if (isset($_POST['details_key']) && isset($_POST['details_value'])) {
        if ($is_update) {
            $delete_sql = "DELETE FROM product_details WHERE product_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $product_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }

        foreach ($_POST['details_key'] as $index => $key) {
            if (!empty($key) && !empty($_POST['details_value'][$index])) {
                $value = $_POST['details_value'][$index];
                $detail_sql = "INSERT INTO product_details (product_id, prod_desc1, prod_desc2) VALUES (?, ?, ?)";
                $detail_stmt = $conn->prepare($detail_sql);
                $detail_stmt->bind_param("iss", $product_id, $key, $value);
                if (!$detail_stmt->execute()) {
                    throw new Exception("Error inserting details: " . $detail_stmt->error);
                }
                $details[] = ['prod_desc1' => $key, 'prod_desc2' => $value];
                $detail_stmt->close();
            }
        }
    }

    // Return success response with product data and details
    echo json_encode([
        'success' => true,
        'message' => 'Product ' . ($is_update ? 'updated' : 'added') . ' successfully',
        'product' => [
            'product_id' => $product_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
            'discount' => $discount,
            'image_url' => $image_url,
            'details' => $details
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in update_product.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating product: ' . $e->getMessage()
    ]);
}
?>