<?php 
include_once "includes/login.inc.php"; 
include_once "includes/function.php";

include "header.php";

if(!isset($_SESSION['user_id'])) {
    if(isset($_POST['submit'])){
        $email = $_POST['email'];
        $password = $_POST['password'];

        if(checkData($email)) {
            header("Location: login.php?email=doesntexist");
            exit();
        }
  
        $loginResult = login($email, $password); 
  
        if ($loginResult) {
            header("Location: homepage.php"); 
            exit();
        } else {
            echo "<div class='error'>Invalid email or password.</div>";
        }
    }
} else {
    header("Location: homepage.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Log in</title>
</head>
<!-- <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .login-container {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        margin: 20px auto;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .login-container h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    .login-container input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .login-container input[type="submit"] {
        background-color:var(--button-colors);
        color: white;
        font-size: 16px;
        cursor: pointer;
        border: none;
    }

    .login-container input[type="submit"]:hover {
        background-color: var(--button-color-hover);
        transition: all 0.2s ease-in-out;
    }

    .login-container input:focus {
        border-color: var(--button-color-hover);
        outline: none;
    }

    .login-container .error, .login-container .success {
        text-align: center;
        margin-top: 15px;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
    }

    .login-container .error {
        background-color: var(--error-color);
    }

    .login-container .success {
        background-color: var(--success-color);
}
</style> -->
<body>
    <div id="header"></div>

    <div class="login-container">
        <h1>Login</h1>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Email..." required>
            <input type="password" name="password" placeholder="Password..." required>
            <input type="submit" name="submit" value="Log in">
        </form>

        <?php 
        // Displaying error or success messages based on URL parameters
        if (isset($_GET['email']) && $_GET['email'] == 'doesntexist') {
            echo "<div class='error'>Email does not exist.</div>";
        }
        ?>
    </div>

    <div id="footer"></div>

    <?php include "footer.php"; ?>
</body>
</html>
