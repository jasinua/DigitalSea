<?php
session_start();

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
                    <p>Your payment was successful. You'll receive an email with your order details soon.</p>
                </div>
                <a href="index.php" class="btn">Continue Shopping</a>
            </div>
        </div>
    </div>
</body>
</html>