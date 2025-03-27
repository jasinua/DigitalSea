<?php 
include_once "includes/login.inc.php";


if(isset($_POST['submit'])){
    // Storing variables for validation
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Data validation using methods
    if(checkData($email)) {
        header("Location: login.php?email=doesntexist");
        exit();
    }
  
    login($email,$password);
    header("Location: login.php?login=success");

   
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
        if (isset($_GET['email']) && $_GET['email'] == 'doesntexist') {
            echo "<div class='error'>Email does not exist.</div>";
        }
       
  
        ?>
    </div>
</body>
</html>
