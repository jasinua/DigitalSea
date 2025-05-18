<?php


require_once __DIR__ . '/../vendor/autoload.php';

require_once '../model/dbh.inc.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;

// Firebase configuration
$firebaseConfig = [
    'apiKey' => 'AIzaSyCif5CiVmFDv-vbBmtZiml3PIIuU7_AOS8',
    'authDomain' => 'auth-89876.firebaseapp.com',
    'databaseURL' => 'https://auth-89876-default-rtdb.firebaseio.com',
    'projectId' => 'auth-89876',
    'storageBucket' => 'auth-89876.appspot.com',
    'messagingSenderId' => '955052187840',
    'appId' => '1:955052187840:web:22ad7bb7a1c7ff7f814d25',
    'measurementId' => 'G-66MY7DRXV7'
];

// Path to your service account JSON file
$serviceAccountPath = __DIR__ . '/digitalsea.json';

$firebase = (new Factory)
    ->withServiceAccount($serviceAccountPath)
    ->withDatabaseUri($firebaseConfig['databaseURL']);

// Initialize Firebase services
$auth = $firebase->createAuth();
$database = $firebase->createDatabase();
$storage = $firebase->createStorage();

// Example: Fetch data from the Realtime Database
$reference = $database->getReference('products');
$snapshot = $reference->getSnapshot();
$data = $snapshot->getValue();



$database->getReference("products/0/")->update(['stock' => 1]);


// $sql1 = "SELECT * FROM cart WHERE order_id = ?";
// $stmt1 = $conn->prepare($sql1);
// $stmt1->bind_param("i", $orderId);
// $stmt1->execute();
// $result1 = $stmt1->get_result();

// foreach ($result1 as $row) {
//     if($row['api_souce'] == 'DigitalSeaAPI'){
//         $productId = $row['product_id'];
//         $quantity = $row['quantity']; 
//         foreach ($data as $key => $product) {
//             if ($product['product_id'] == $productId) {
//                 $stock = $product['stock'];
//                 if($stock >= $quantity){
//                     $newStock = $stock - $quantity;
//                     $database->getReference("products/{$key}")->update(['stock' => $newStock]);
//                     $updateProductStock = "UPDATE products SET stock = ? WHERE product_id = ?";
//                     $stmt = $conn->prepare($updateProductStock);
//                     $stmt->bind_param("ii", $newStock, $productId);
//                     $stmt->execute();
//                 }
//             }
//         }
//     } else {
//         $productId = $row['product_id'];
//         $quantity = $row['quantity'];
//         $stock = $product['stock'];
//         if($stock >= $quantity){
//             $newStock = $stock - $quantity;
//             $updateProductStock = "UPDATE products SET stock = ? WHERE product_id = ?";
//             $stmt = $conn->prepare($updateProductStock);
//             $stmt->bind_param("ii", $newStock, $productId);
//             $stmt->execute();
//         }
//     }
// }


// $cartItems = returnCart($_SESSION['user_id']);
//     while ($item = $cartItems->fetch_assoc()) {
//         if($item['order_id'] == $orderId) {
//             $productId = $item['product_id'];
//             $orderedQty = $item['quantity'];
            
//             $getStockQuery = "SELECT stock FROM products WHERE product_id = ?";
//             $stmt = $conn->prepare($getStockQuery);
//             $stmt->bind_param("i", $productId);
//             $stmt->execute();
//             $result = $stmt->get_result();
//             $currentStock = $result->fetch_assoc()['stock'];
            
//             $newStock = $currentStock - $orderedQty;
            
//             $updateProductStock = "UPDATE products SET stock = ? WHERE product_id = ?";
//             $stmt = $conn->prepare($updateProductStock);
//             $stmt->bind_param("ii", $newStock, $productId);
//             $stmt->execute();
//         }
//     }

// echo '<pre>';
// print_r($data);
// echo '</pre>';
