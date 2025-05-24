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
$getUserEmail = "CALL getEmail(?)";
$stmt = $conn->prepare($getUserEmail);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userEmail = $result->fetch_assoc()['email'];
$stmt->close();

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
<?php include "css/order-conf-css.php"; ?>
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
                <div style="display: flex; flex-direction: row; justify-content: space-evenly;">
                    <form action="ordersPdf/order-pdf.php" method='post'>
                        <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                        <button type="submit" name='pdf' class="btn" style="padding: 15px 24px; cursor: pointer;">
                            <i class="fas fa-file-pdf"></i> Download Invoice
                        </button>
                    </form>
                    <a href="index.php" class="btn">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>