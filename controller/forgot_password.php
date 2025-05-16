<?php
session_start();
include_once "../model/dbh.inc.php";
include_once "signup.inc.php";

header('Content-Type: application/json');

if (!isset($_POST['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

$email = $_POST['email'];

// Check if email exists
$stmt = $conn->prepare("CALL checkUserExist(?)");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No account found with this email']);
    exit;
}

// Close the result set and statement
$result->close();
$stmt->close();

// Generate a random token
$token = bin2hex(random_bytes(32)); // 64 characters long
$token_time = time();

// Update user with token and token_time
$stmt = $conn->prepare("UPDATE users SET token = ?, token_time = ? WHERE email = ?");
$stmt->bind_param("sis", $token, $token_time, $email);
$stmt->execute();

// Send email with reset link
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

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
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Reset Your DigitalSea Password';
    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/login.php?token=" . $token;
    
    $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 20px 0; }
                .content { background-color: #f9f9f9; padding: 30px; border-radius: 5px; }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 20px 0;
                }
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
                    <h2>Password Reset Request</h2>
                    <p>We received a request to reset your password. Click the button below to create a new password:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$reset_link}' class='button'>Reset Password</a>
                    </div>
                    
                    <p>This link will expire in 1 hour for security purposes.</p>
                    
                    <p>If you did not request a password reset, please ignore this email or contact our support team if you have concerns about your account security.</p>
                    
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

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Password reset instructions have been sent to your email']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send reset email: ' . $mail->ErrorInfo]);
}
?> 