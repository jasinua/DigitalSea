<?php
session_start();
include_once 'function.php';

if (!isset($_GET['term'])) {
    echo json_encode([]);
    exit();
}

$search_term = '%' . $_GET['term'] . '%';
$sql = "SELECT product_id, name, description, price, discount, image_url FROM products 
        WHERE LOWER(name) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?) 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'id' => $row['product_id'],
        'label' => $row['name'],
        'value' => $row['name'],
        'description' => $row['description'],
        'price' => $row['price'],
        'discount' => $row['discount'],
        'image_url' => $row['image_url']
    ];
}

echo json_encode($suggestions);
?> 