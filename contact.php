<?php
ob_start();
session_start();
include 'header/header.php';
include 'css/contact-css.php';
include_once "model/dbh.inc.php";

// Load environment variables
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Pre-fill email if logged in
$prefillEmail = '';
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $prefillEmail = $userData['email'];
    }
    $stmt->close();
}
?>
<div class="contact-wrapper">
    <h2>Contact Us</h2>
    <div class="contact-info">
        <p>Email: digitalsea.ks@gmail.com</p>
        <p>Phone: +383 45 123 456</p>
        <p>Address: Universiteti i Prishtines, Prishtine, Kosovo</p>
    </div>
    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $success = false;
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'digitalsea.ks@gmail.com';
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Get form fields
            $userEmail = trim($_POST['email'] ?? '');
            $userName = trim($_POST['name'] ?? '');
            $subject = trim($_POST['subject'] ?? 'Contact Form');
            $message = trim($_POST['message'] ?? '');

            // If logged in, get email from DB, but use name from form
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $userData = $result->fetch_assoc();
                    $userEmail = $userData['email'];
                }
                $stmt->close();
            }

            // Sender and receiver
            $mail->setFrom($userEmail, $userName);
            $mail->addAddress('digitalsea.ks@gmail.com');

            // Email content
            $mail->Subject = $subject;
            $mail->Body    = "Nga: " . $userName . "\n"
                          . "Me email: " . $userEmail . "\n\n"
                          . "Mesazhi:\n" . $message;

            $mail->send();
            $success = true;
            header("Location: contact.php");
            exit;
        } catch (Exception $e) {
            $error = 'Dërgimi dështoi, o shpirt i trazuar. Gabimi: ' . $mail->ErrorInfo;
        }
    }
    ?>
    <?php if ($success): ?>
        <div class="alert success">Thank you for contacting us! We will get back to you soon.</div>
    <?php elseif ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form class="contact-form" method="post" action="">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ($prefillEmail ? htmlspecialchars($prefillEmail) : ''); ?>">
        </div>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
        </div>
        <button type="submit" class="btn">Send Message</button>
    </form>
</div>
<?php include 'footer/footer.php'; ?>