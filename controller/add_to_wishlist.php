<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . "/../model/dbh.inc.php";

$product_id = $_POST['product_id'];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['add_to_wishlist'] = true;
    $_SESSION['product_id_to_add'] = $product_id;
    if(isset($_POST['url'])){
        $_SESSION['previous_url'] = $_POST['url'];//nese o prej ni iprodukti ruja linkun produktit per mu khty qaty
    }
    header("Location: ../login.php");
    exit();
}

if (!isset($_POST['product_id'])) {
    echo "error";
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Check if product is already in wishlist
    $check_stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Product exists in wishlist, remove it ONLY if add_to_wishlist is not true
        if(!isset($_SESSION['add_to_wishlist'])){
            $delete_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $delete_stmt->bind_param("ii", $user_id, $product_id);
        
            if ($delete_stmt->execute()) {
                echo "removed";
            } else {
                echo "error";
            }
        }else if(isset($_SESSION['add_to_wishlist']) && $_SESSION['add_to_wishlist'] == true){
                unset($_SESSION['add_to_wishlist']);
                unset($_SESSION['product_id_to_add']);

                echo "added";
                
        }

    } else {
        // Product doesn't exist in wishlist, add it
        $insert_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $user_id, $product_id);
        
        if ($insert_stmt->execute()) {
            echo "added";
        } else {
            echo "error";
        }
    }
} catch (Exception $e) {
    error_log("Error in add_to_wishlist.php: " . $e->getMessage());
    echo "error";
}
?> 