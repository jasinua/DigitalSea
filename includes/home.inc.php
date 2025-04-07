<?php

function getData($query){
    include 'dbh.inc.php';
    $res = $conn -> query($query);
    $data = $res -> fetch_all(MYSQLI_ASSOC);
    return $data;
}

function getProductData($id){
    include 'dbh.inc.php';
    $res = $conn -> query("SELECT * FROM products WHERE products.product_id=$id");
    $product = $res -> fetch_all(MYSQLI_ASSOC);
    return $product;
}

function getProductDetails($id){
    include 'dbh.inc.php';
    $res = $conn -> query("SELECT * FROM product_details WHERE product_details.product_id=$id ORDER BY prod_desc1 ASC");
    $product = $res -> fetch_all(MYSQLI_ASSOC);
    return $product;
}



?>