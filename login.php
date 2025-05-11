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
<style>
    .page-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    #container {
        background-color: var(--ivory-color);
        display: flex;
        flex: 1;
        min-height: calc(100vh - 120px);
        position: relative;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    .login-container {
        background-color: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 550px;
        margin: 0 auto;
        height: 400px;
    }

    .login-container h1 {
        text-align: center;
        color: var(--noir-color);
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: 600;
    }

    .login-container input {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0;
        border: 1px solid var(--navy-color);
        border-radius: 6px;
        font-size: 15px;
        transition: all 0.5s ease;
    }

    .login-container input:focus {
        border-color: var(--noir-color);
        box-shadow: var(--shadow-input);
        outline: none;
    }

    .login-container input[type="submit"] {
        background-color: var(--noir-color);
        color: white;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        padding: 14px;
        margin-top: 20px;
        transition: all 0.3s ease;
    }

    .login-container input[type="submit"]:hover {
        background-color: var(--button-color);
        transform: translateY(-2px);
    }

    .login-container .error {
        background-color: #ffebee;
        color: #d32f2f;
        padding: 12px;
        border-radius: 6px;
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .login-container .success {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 12px;
        border-radius: 6px;
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .login-container .signup-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: var(--noir-color);
    }

    .login-container .signup-link a {
        color: var(--button-color);
        text-decoration: none;
        font-weight: 500;
    }

    .login-container .signup-link a:hover {
        text-decoration: underline;
    }
</style>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php"?>
        <div id="container">
            <div class="login-container">
                <h1>Welcome to DigitalSea</h1>
                <form action="" method="post">
                    <input type="email" name="email" placeholder="Email address" autofocus="autofocus" required>
                    <input type="password" name="password" placeholder="Password" required>
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
</body>
</html>