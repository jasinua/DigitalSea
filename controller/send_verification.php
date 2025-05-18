<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

session_start();

if (!isset($_POST['email']) || !isset($_POST['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$email = $_POST['email'];
$username = $_POST['username'];

// Generate a 6-digit verification code
$verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Store the verification code in session
$_SESSION['verification_code'] = $verification_code;
$_SESSION['verification_email'] = $email;
$_SESSION['verification_username'] = $username;

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
    $mail->addAddress($email, $username);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Verify your DigitalSea Account';
    $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 20px 0; }
                .logo { max-width: 150px; }
                .content { background-color: #f9f9f9; padding: 30px; border-radius: 5px; }
                .verification-code { 
                    font-size: 32px; 
                    font-weight: bold; 
                    color: #153147; 
                    text-align: center; 
                    letter-spacing: 5px;
                    margin: 20px 0;
                    padding: 15px;
                    background-color: #fff;
                    border-radius: 5px;
                    border: 1px solid #ddd;
                }
                .footer { 
                    text-align: center; 
                    margin-top: 20px; 
                    font-size: 12px; 
                    color: #666; 
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #153147;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='color: #153147; margin: 0;'>DigitalSea</h1>
                </div>
                <div class='content'>
                    <h2>Welcome to DigitalSea!</h2>
                    <p>Dear {$username},</p>
                    <p>Thank you for choosing DigitalSea. To complete your registration and ensure the security of your account, please use the following verification code:</p>
                    
                    <div class='verification-code'>{$verification_code}</div>
                    
                    <p>This code will expire in 10 minutes for security purposes.</p>
                    
                    <p>If you did not request this verification code, please ignore this email or contact our support team if you have concerns about your account security.</p>
                    
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
    echo json_encode(['status' => 'success', 'message' => 'Verification code sent successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send verification code: ' . $mail->ErrorInfo]);
}
?> 