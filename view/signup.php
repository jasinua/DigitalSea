<?php 
include_once "../controller/signup.inc.php";
session_start();

include "header/header.php";

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

if(isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
} else {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<style>
    .signup-container {
        background-color: var(--modal-bg-color);
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0px 5px var(--navy-color);
        width: 100%;
        max-width: 400px;
        margin: 40px auto;
    }

    .signup-container h1 {
        text-align: center;
        color: var(--page-text-color);
        margin-bottom: 20px;
    }

    .signup-container input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 2px solid var(--mist-color);
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .signup-container input[type="submit"] {
        background-color:var(--button-color);
        color: var(--text-color);
        font-size: 16px;
        cursor: pointer;
        border: none;
        
        transition: all 0.2s ease-in-out;
    }

    .signup-container input[type="submit"]:hover {
        background-color: var(--button-color-hover);
        transition: all 0.2s ease-in-out;
    }

    .signup-container input:focus {
        border-color: var(--navy-color);
        box-shadow: 0 0 5px var(--navy-color);
        outline: none;
    }

    .signup-container .error, .signup-container .success {
        text-align: center;
        margin-top: 15px;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
    }

    .signup-container .error {
        background-color: var(--error-color);
    }

    .signup-container .success {
        background-color: var(--success-color);
    }

</style>
<body>
    <div class="page-wrapper">
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
                header("Location: homepage.php");
                exit();
            }
            
            if (isset($_GET['signup']) && $_GET['signup'] == 'error') {
                echo "<div class='error'>Something went wrong. Please try again.</div>";
            }
            ?>
        </div>
    </div>
    
    <?php include "footer/footer.php"; ?>
</body>
</html>
<?php } ?>