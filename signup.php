<?php 

include_once "includes/signup.inc.php";

include "header.php";

if(isset($_POST['submit'])){
    // Storing variables for validation
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $password = $_POST['password'];
    $password_repeat = $_POST['repeat_password'];

    // Data validation using methods
    if(emptyInputSignUp($first_name, $last_name, $birthday, $email, $password, $password_repeat)) {
        header("Location: signup.php?fields=empty");
        exit();
    }
    if(!invalidInputs($first_name, $last_name)) {
        header("Location: signup.php?inputs=invalid");
        exit();
    }
    if(!checkEmail($email)) {
        header("Location: signup.php?email=invalid");
        exit();
    }
    if(!invalidPasswordFormat($password)){
        header("Location: signup.php?password=invalid");
        exit();
    }
    if(!checkPassword($password, $password_repeat)) {
        header("Location: signup.php?passwords=nomatch");
        exit();
    }
    if(!checkAge($birthday)) {
        header("Location: signup.php?age=invalid");
        exit();
    }
    if(!emailExists($email)) {
        header("Location: signup.php?email=exists");
        exit();
    }

    if (createUser($first_name, $last_name, $birthday, $email, $password)) {
        header("Location: signup.php?signup=success");
        exit();
    } else {
        header("Location: signup.php?signup=error");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sign Up</title>
</head>
<body>
    
    <div id="header"></div>

    <div class="signup-container">
        <h1>Sign Up</h1>
        <form action="" method="post">
            <input type="text" name="first_name" placeholder="First name..." required>
            <input type="text" name="last_name" placeholder="Last name..." required>
            <input type="email" name="email" placeholder="Email..." required>
            <input type="date" name="birthday" required>
            <input type="password" name="password" placeholder="Password..." required>
            <input type="password" name="repeat_password" placeholder="Repeat password..." required>
            <input type="submit" name="submit" value="Sign Up">
        </form>

        <?php 
        // Displaying error or success messages based on URL parameters
        if (isset($_GET['fields']) && $_GET['fields'] == 'empty') {
            echo "<div class='error'>Please fill in all fields.</div>";
        }
        if (isset($_GET['inputs']) && $_GET['inputs'] == 'invalid') {
            echo "<div class='error'>Invalid name inputs.</div>";
        }
        if (isset($_GET['email']) && $_GET['email'] == 'invalid') {
            echo "<div class='error'>Invalid email format.</div>";
        }
        if (isset($_GET['password']) && $_GET['password'] == 'invalid') {
            echo "<div class='error'>Password format is invalid.</div>";
        }
        if (isset($_GET['passwords']) && $_GET['passwords'] == 'nomatch') {
            echo "<div class='error'>Passwords do not match.</div>";
        }
        if (isset($_GET['age']) && $_GET['age'] == 'invalid') {
            echo "<div class='error'>You must be at least 18 years old.</div>";
        }
        if (isset($_GET['email']) && $_GET['email'] == 'exists') {
            echo "<div class='error'>Email already exists.</div>";
        }
        if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
            echo "<div class='success'>Sign up successful! Welcome.</div>";
        }
        if (isset($_GET['signup']) && $_GET['signup'] == 'error') {
            echo "<div class='error'>Something went wrong. Please try again.</div>";
        }
        ?>
    </div>

    <div id="footer"></div>
    <?php include "footer.php"; ?>
</body>
</html>
