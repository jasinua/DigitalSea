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
        $discount = (float)$_POST['discount'] ?? 0;

        // Handle image upload
        $main_image = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/';
            $temp_name = $_FILES['product_image']['tmp_name'];
            $new_filename = "product_{$product_id}.png";
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($temp_name, $upload_path)) {
                $main_image = "images/product_{$product_id}.png";
            } else {
                throw new Exception('Error uploading image');
            }
        } else {
            // If no new image uploaded, keep existing image
            $check_sql = "SELECT image_url FROM products WHERE product_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $product_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $main_image = $row['image_url'];
            }
            $check_stmt->close();
        }

        // Log the received data
        error_log("Updating product ID: " . $product_id);
        error_log("Received data: " . print_r($_POST, true));

        // Check if this is a new product or an update
        if (empty($product_id)) {
            // This is a new product - INSERT
            $sql = "INSERT INTO products (name, description, price, stock, image_url, discount) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $main_image, $discount);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            // Get the new product ID
            $product_id = $conn->insert_id;

            // If this was a new product with an image, rename the file to match the new ID
            if (!empty($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $old_path = $upload_dir . "product_temp.png";
                $new_path = $upload_dir . "product_{$product_id}.png";
                if (file_exists($old_path)) {
                    rename($old_path, $new_path);
                }
            }
        } else {
            // This is an existing product - UPDATE
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

        echo json_encode(['success' => true, 'message' => 'Product ' . (empty($product_id) ? 'added' : 'updated') . ' successfully']);

    } catch (Exception $e) {
        error_log("Error in update_product.php: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error updating product: ' . $e->getMessage()
        ]);
    }
?> 