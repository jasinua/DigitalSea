<?php 



    function isLoggedIn($check) {
        if($check) {
            return true;
        } else {
            return false;
        }
    }


    function isAdmin($user) {
        if($user == 0 || $user == "0") {
            return false;
        } else {
            return true;
        }
    }


    function returnWishlist($userid) {
        include "dbh.inc.php";
        
        $stmt = $conn->prepare("CALL showWishList(?)");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    function returnCart($userid) {
        include "dbh.inc.php";
        
        $stmt = $conn->prepare("CALL showCartList(?)");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    function returnProduct($productId) {
        include "dbh.inc.php";
        $stmt = $conn->prepare("CALL showProductDetail(?)");
        $stmt->bind_param("i",$productId);
        $stmt -> execute();

        return $stmt->get_result();
    }

    function returnProductImage($productId) {
        include "dbh.inc.php";
        $stmt = $conn->prepare("CALL returnImages(?)");
        $stmt->bind_param("i",$productId);
        $stmt -> execute();

        return $stmt->get_result();
    }
    
