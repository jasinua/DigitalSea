<?php
session_start();

require 'vendor/autoload.php';

include_once "model/dbh.inc.php";



// Check if payment was successful
if (!isset($_GET['success']) || $_GET['success'] != '1') {
    header("Location: payment.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$orderId = $_GET['order_id'];

// Get user email from database
$getUserEmail = "SELECT email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($getUserEmail);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userEmail = $result->fetch_assoc()['email'];

// Generate PDF
require_once 'ordersPdf/order-pdf.php';
$pdfContent = generatePDF($orderId);

// Send email using PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
                    <h1 style='color: #007bff; margin: 0;'>DigitalSea</h1>
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

include "header/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<style>
    .page-wrapper {
        width: 100%;
        color: var(--page-text-color);
        font-family: 'Roboto', sans-serif;
    }
    
    h2 {
        margin: 40px 0 30px 0;
        font-size: 2.2rem;
        text-align: center;
        color: var(--page-text-color);
    }
    
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .confirmation-box {
        background-color: var(--ivory-color);
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    .success-message {
        color: #2e7d32;
        font-size: 1.2rem;
        margin-bottom: 20px;
    }
    
    .email-notice {
        color: #666;
        font-size: 0.9rem;
        margin: 15px 0;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 24px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s;
        margin: 10px;
    }
    
    .btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }
    
    :root {
        --success-color: #00c853;
        --border-color: #ddd;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 480px) {
        .container {
            padding: 10px;
        }
        
        .confirmation-box {
            padding: 20px;
        }
    }
</style>
<body>
    <div class="page-wrapper">
        <h2>Order Confirmation</h2>
        <div class="container">
            <div class="confirmation-box">
                <div class="success-message">
                    <h3>Thank You for Your Purchase!</h3>
                    <p>Your payment was successful.</p>
                    <p>Order ID: #<?php echo $orderId; ?></p>
                </div>
                <div class="email-notice">
                    <p>We've sent your invoice to: <?php echo htmlspecialchars($userEmail); ?></p>
                </div>
                <form action="ordersPdf/order-pdf.php" method='post'>
                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                    <input type="submit" name='pdf' value='Download Invoice Again' class="btn">
                </form>
                <a href="index.php" class="btn">Continue Shopping</a>
            </div>
        </div>
    </div>
</body>
</html>