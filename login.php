<?php 
include_once "controller/login.inc.php"; 
include_once "controller/function.php";

// include "header.php";

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
            header("Location: index.php"); 
            exit();
        } else {
            header("Location: login.php?input=error"); 
            exit();  }
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
    <title>Log in</title>
</head>
<style>
    .login-container {
        background-color: var(--modal-bg-color);
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0px 5px var(--navy-color);
        width: 100%;
        max-width: 400px;
        margin: auto auto;
        display: flex;
        flex-direction: column;
        /* flex-grow: 1; */
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

    input[type="email"], input[type="password"]{
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 2px solid var(--mist-color);
        border-radius: 6px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input[type="email"]:focus, input[type="password"]:focus{
        border-color: var(--navy-color);
        box-shadow: 0 0 5px var(--navy-color);
        outline: none;
    }

    .login-container input[type="submit"] {
        background-color:var(--button-color);
        color: var(--text-color);
        font-size: 16px;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease-in-out;
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
        color: var(--text-color);
        padding: 10px;
        border-radius: 5px;
    }

    .login-container .error {
        background-color: var(--error-color);
    }

    .login-container .success {
        background-color: var(--success-color);
    }

</style>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php"?>
        <div class="login-container">
            <h1>Login</h1>
            <form action="" method="post">
                <input type="email" name="email" placeholder="Email..." autofocus="autofocus" required>
                <input type="password" name="password" placeholder="Password..." required>
                <input type="submit" name="submit" value="Log in">
            </form>

            <?php 
            // Displaying error or success messages based on URL parameters
            if (isset($_GET['input']) && $_GET['input'] == 'error') {
                echo "<div class='error'>Email does not exist.</div>";
            }
            ?>
        </div>
    </div>
    
    <?php include "footer/footer.php"; ?>
</body>
</html>