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
    include "model/dbh.inc.php";
    $stmt = $conn->prepare("CALL addToCart(?,?,?,?)");
    $stmt->bind_param("iiid",$userId, $productId, $quantity, $price);
    $stmt -> execute();

    return $stmt->get_result();
}
