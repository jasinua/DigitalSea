<?php 
    include_once "controller/login.inc.php"; 
    include_once "controller/function.php";

    // include "header.php";

    $error = '';

    if(!isset($_SESSION['user_id'])) {
        if(isset($_POST['submit'])){
            $email = $_POST['email'];
            $password = $_POST['password'];

            if(checkData($email)) {
                $error = "Email doesn't exist.";
            } else {
                $loginResult = login($email, $password); 
        
                if ($loginResult) {
                    header("Location: index.php"); 
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            }
        }
    } else {
        header("Location: index.php"); 
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - DigitalSea</title>
    <!-- Load Font Awesome asynchronously -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></noscript>
</head>

<?php include "css/login-css.php"; ?>

<body>
    <div class="page-wrapper">
        <?php include "header/header.php"?>
        <div id="container">
            <div class="login-container">
                <h1>Welcome to DigitalSea</h1>
                <form action="" method="post">
                    <input type="email" name="email" placeholder="Email address" autofocus="autofocus" required>
                    <div class="password-field">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <span class="password-toggle" onclick="togglePasswordVisibility()">
                            <i class="far fa-eye"></i>
                        </span>
                    </div>
                    <input type="submit" name="submit" value="Log in">
                </form>

                <?php if (!empty($error)): ?>
                    <div class='error'><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="signup-link">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </div>
            </div>
        </div>
        <?php include "footer/footer.php"; ?>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>