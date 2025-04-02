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
            header("Location: login.php?input=error"); 
            exit();  }
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
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Email..." required>
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
</body>
</html>
