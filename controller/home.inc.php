<?php
require_once __DIR__ . "/../model/dbh.inc.php";

function getData($query, $page = 1, $items_per_page = 18) {
    global $conn;
    
    // Add pagination to query if it doesn't already have LIMIT
    if (stripos($query, 'LIMIT') === false) {
        $offset = ($page - 1) * $items_per_page;
        $query .= " LIMIT $items_per_page OFFSET $offset";
    }
    
    $res = $conn->query($query);
    $data = $res->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function getProductData($id) {
    global $conn;
    
    $stmt = $conn->prepare("CALL showProduct(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

function getProductDetails($id) {
    global $conn;
    
    $stmt = $conn->prepare("CALL showProductDetail(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all();
}

function getWishlistItems($user_id) {
    global $conn;
    if (!isset($user_id)) {
        return [];
    }
    
    $sql = "SELECT product_id FROM wishlist WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wishlist = [];
    while($row = $result->fetch_assoc()) {
        $wishlist[] = $row['product_id'];
    }
    return $wishlist;
}

function addToCart($userId, $productId, $quantity, $price) {
    global $conn;
    
    try {
        // First check if product exists in cart
        $check_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? AND order_id IS NULL");
        $check_stmt->bind_param("ii", $userId, $productId);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Product exists, update quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ? AND order_id IS NULL");
            $update_stmt->bind_param("iii", $new_quantity, $userId, $productId);
            return $update_stmt->execute();
        } else {
            // Product doesn't exist, insert new
            $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("iiid", $userId, $productId, $quantity, $price);
            return $insert_stmt->execute();
        }
    } catch (Exception $e) {
        error_log("Error in addToCart: " . $e->getMessage());
        return false;
    }
}

// Get total count of products for pagination
function getTotalProducts() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getProducts($page = 1, $items_per_page = 18) {
    global $conn;
    
    $offset = ($page - 1) * $items_per_page;
    
    $sql = "SELECT * FROM products LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $items_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}
?>
