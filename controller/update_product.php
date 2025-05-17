<?php
    session_start();
    include_once "function.php";
    include_once "../model/dbh.inc.php";

    header('Content-Type: application/json');

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    try {
        if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['stock'])) {
            throw new Exception('Missing required fields');
        }

        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $api_source = $_POST['api_source'] ?? '';
        $main_image = $_POST['main_image'] ?? '';
        $discount = (float)$_POST['discount'] ?? 0;

        // Log the received data
        error_log("Updating product ID: " . $product_id);
        error_log("Received data: " . print_r($_POST, true));

        // Update the product in the database
        $sql = "UPDATE products SET 
                name = ?, 
                description = ?, 
                price = ?, 
                stock = ?, 
                image_url = ?,
                discount = ?
                WHERE product_id = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssdisii", $name, $description, $price, $stock, $main_image, $discount, $product_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Update details if any
        if (isset($_POST['details_key']) && isset($_POST['details_value'])) {
            // First delete existing details
            $delete_sql = "DELETE FROM product_details WHERE product_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            if (!$delete_stmt) {
                throw new Exception("Prepare delete failed: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $product_id);
            if (!$delete_stmt->execute()) {
                throw new Exception("Delete details failed: " . $delete_stmt->error);
            }

            // Insert new details
            $insert_sql = "INSERT INTO product_details (product_id, prod_desc1, prod_desc2) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                throw new Exception("Prepare insert failed: " . $conn->error);
            }

            foreach ($_POST['details_key'] as $index => $key) {
                if (!empty($key) && !empty($_POST['details_value'][$index])) {
                    $value = $_POST['details_value'][$index];
                    $insert_stmt->bind_param("iss", $product_id, $key, $value);
                    if (!$insert_stmt->execute()) {
                        throw new Exception("Insert detail failed: " . $insert_stmt->error);
                    }
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);

    } catch (Exception $e) {
        error_log("Error in update_product.php: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error updating product: ' . $e->getMessage()
        ]);
    }
?> 