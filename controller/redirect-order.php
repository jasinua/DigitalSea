<?php

session_start();



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



include_once "../model/dbh.inc.php";

include_once "function.php";

require_once __DIR__ . '/../vendor/autoload.php';

require_once '../model/dbh.inc.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;

if(isset($_SESSION['payment_success']) && $_SESSION['payment_success'] === true) {
    
    $totalAmount = $_SESSION['total_amount'];
    $status = 'pending';
    $orderDate = date('Y-m-d H:i:s');

    $createOrder = "CALL createOrder(?,?,?,?)";
    $stmt = $conn->prepare($createOrder);
    $stmt->bind_param("idss", $_SESSION['user_id'], $totalAmount, $status, $orderDate);
    $stmt->execute();

    $orderId = $conn->insert_id;


    

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
$serviceAccountPath = __DIR__ . '/../api/digitalsea.json';

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



$returnCartProducts = "CALL returnCartProd(?)";
$stmt1 = $conn->prepare($returnCartProducts);
$stmt1->bind_param("i", $_SESSION['user_id']);
$stmt1->execute();
$result1 = $stmt1->get_result();

foreach ($result1 as $row) {
        if($row['api_source'] == 'DigitalSeaAPI'){
            $productId = $row['product_id'];
            $quantity = $row['quantity']; 
            foreach ($data as $key => $value) {
                if (isset($value['product_id']) && $value['product_id'] == $productId) {
                    $stock = $value['stock'];
                    if($stock >= $quantity){
                        $newStock = $stock - $quantity;
                        $database->getReference("products/{$key}")->update(['stock' => $newStock]);
                        $updateProductStock = "CALL updateProdStock(?,?)";
                        $stmt = $conn->prepare($updateProductStock);
                        $stmt->bind_param("ii", $newStock, $productId);
                        $stmt->execute();
                    } 
                }
            }
            
        } else {
            $productId = $row['product_id'];
            $quantity = $row['quantity'];
            $stock = $row['stock'];
            if($stock >= $quantity){
                $newStock = $stock - $quantity;
                $updateProductStock = "CALL updateProdStock(?,?)";
                $stmt = $conn->prepare($updateProductStock);
                $stmt->bind_param("ii", $newStock, $productId);
                $stmt->execute();
            }
        }
}


    $updateCart = "CALL updateCartOrder(?,?)";
    $stmt = $conn->prepare($updateCart);
    $stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
    $stmt->execute();

    // / Get user email from database
$getUserEmail = "CALL getEmail(?)";
$stmt = $conn->prepare($getUserEmail);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userEmail = $result->fetch_assoc()['email'];
    


require_once '../ordersPdf/order-pdf.php';
$pdfContent = generatePDF($orderId);

// Send email using PHPMailer
try {
    $mail = new PHPMailer(true);

    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'digitalsea.ks@gmail.com';
    $mail->Password = 'qtbk kbis hvcu kjzl';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('digitalsea.ks@gmail.com', 'DigitalSea');
    $mail->addAddress($userEmail);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your DigitalSea Order Invoice #' . $orderId;
    
    $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 20px 0; }
                .content { background-color: #f9f9f9; padding: 30px; border-radius: 5px; }
                .footer { 
                    text-align: center; 
                    margin-top: 20px; 
                    font-size: 12px; 
                    color: #666; 
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='color: #28353F; margin: 0;'>DigitalSea</h1>
                </div>
                <div class='content'>
                    <h2>Thank You for Your Purchase!</h2>
                    <p>Your order has been successfully processed. Please find your invoice attached to this email.</p>
                    <p>Order ID: #{$orderId}</p>
                    <p>If you have any questions about your order, please don't hesitate to contact our support team.</p>
                    <p>Best regards,<br>The DigitalSea Team</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message, please do not reply to this email.</p>
                    <p>&copy; " . date('Y') . " DigitalSea. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
    ";

    // Add PDF attachment
    $mail->addStringAttachment(
        $pdfContent,
        "invoice_{$orderId}.pdf",
        PHPMailer::ENCODING_BASE64,
        'application/pdf'
    );

    $mail->send();
} catch (Exception $e) {
    // Log the error but don't show it to the user
    error_log("Failed to send invoice email: " . $mail->ErrorInfo);

}   

    header("Location: ../order-confirmation.php?success=1&order_id=" . $orderId);
    session_unset($_SESSION['payment_success']);
    session_unset($_SESSION['total_amount']);
    session_unset($_SESSION['payment_timestamp']);
    // session_unset($_SESSION['payment_success'], $_SESSION['total_amount'], $_SESSION['payment_timestamp']);
    // exit();
} else {
    header("Location: ../payment.php");
}

?>