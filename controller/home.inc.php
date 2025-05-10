<?php

function getData($query)
{
    include "model/dbh.inc.php";
    $res = $conn->query($query);
    $data = $res->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function getProductData($id)
{
    include "model/dbh.inc.php";

    $stmt = $conn->prepare("CALL showProduct(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function getProductDetails($id)
{
    include "model/dbh.inc.php";

    $stmt = $conn->prepare("CALL showProductDetail(?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all();
}

function addToCart($userId, $productId, $quantity, $price) 
{
    include "../model/dbh.inc.php";
    
    try {
        // First check if product exists in cart
        $check_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $check_stmt->bind_param("ii", $userId, $productId);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Product exists, update quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
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
