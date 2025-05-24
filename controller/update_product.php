<?php
session_start();
include_once "function.php";
include_once "../model/dbh.inc.php";

header('Content-Type: application/json');

// Admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdministrator']) || $_SESSION['isAdministrator'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'])) {
        throw new Exception('Missing required fields');
    }

    $product_id = $_POST['product_id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $discount = !empty($_POST['discount']) ? (float)$_POST['discount'] : 0;

    if ($discount > 100) {
        throw new Exception('Discount cannot exceed 100%');
    }

    $is_update = !empty($product_id);

    // Handle image
    $image_url = '';
    if ($_POST['image_source'] === 'file' && isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
            throw new Exception('Upload directory not accessible');
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
        $check_sql = "CALL imageUpload(?)";
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

    error_log("Updating product ID: " . $product_id);
    error_log("Received data: " . print_r($_POST, true));

    if ($is_update) {
        $sql = "CALL updateProduct(?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $discount, $image_url, $product_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $sql = "CALL insertProduct(?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $discount, $image_url);
        $stmt->execute();

        // Get result from SELECT LAST_INSERT_ID()
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
        } else {
            throw new Exception("Failed to fetch new product ID");
        }

        $stmt->close();

        // Rename image
        if ($_POST['image_source'] === 'file' && file_exists($upload_dir . "product_temp.png")) {
            $new_image_path = $upload_dir . "product_{$product_id}.png";
            rename($upload_dir . "product_temp.png", $new_image_path);
            $image_url = "images/product_{$product_id}.png";

            $update_sql = "CALL updateProductImage(?,?)";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $image_url, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }

    // Handle product details
    $details = [];
    if (isset($_POST['details_key'], $_POST['details_value'])) {
        if ($is_update) {
            $delete_sql = "CALL deleteProductDetails(?)";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $product_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }

        foreach ($_POST['details_key'] as $index => $key) {
            if (!empty($key) && !empty($_POST['details_value'][$index])) {
                $value = $_POST['details_value'][$index];
                $detail_sql = "CALL inseretProductDetails(?,?,?)"; // ✅ fixed typo
                $detail_stmt = $conn->prepare($detail_sql);
                $detail_stmt->bind_param("iss", $product_id, $key, $value);
                $detail_stmt->execute();
                $details[] = ['prod_desc1' => $key, 'prod_desc2' => $value];
                $detail_stmt->close();
            }
        }
    }

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
