<?php
include 'model/dbh.inc.php';

if (isset($_GET['term'])) {
    $term = mysqli_real_escape_string($conn, $_GET['term']);
    
    $query = "SELECT DISTINCT p.product_id, p.description, p.image_url, p.price, p.discount 
              FROM products p
              WHERE LOWER(p.description) LIKE LOWER('%$term%')
              LIMIT 5";
              
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        echo json_encode(array('error' => mysqli_error($conn)));
        exit;
    }
    
    $suggestions = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $originalPrice = $row['price'];
        $discount = $row['discount'];
        $finalPrice = $originalPrice;
        
        if ($discount > 0) {
            $finalPrice = $originalPrice * (1 - $discount / 100);
        }
        
        $suggestions[] = array(
            'label' => $row['description'],
            'value' => $row['description'],
            'image' => $row['image_url'],
            'price' => number_format($finalPrice, 2),
            'originalPrice' => $discount > 0 ? number_format($originalPrice, 2) : null,
            'discount' => $discount,
            'id' => $row['product_id']
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($suggestions);
}
?> 