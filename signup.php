<?php 

include_once "includes/signup.inc.php";


    // klikimi i buttonit submit
if(isset($_POST['submit'])){
    

    //ruajja e variablave per kontrollim
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $password = $_POST['password'];
    $password_repeat = $_POST['repeat_password'];


    // PERDORIMI I METODAVE PER VALIDIM TE TE DHENAVE
    

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
    <title>Sign Up</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="first_name" placeholder="First name..." required>
        <input type="text" name="last_name" placeholder="Last name..." required>
        <input type="email" name="email" placeholder="Email..." required>
        <input type="date" name="birthday" required>
        <input type="password" name="password" placeholder="Password..." required>
        <input type="password" name="repeat_password" placeholder="Repeat password..." required>
        <input type="submit" name="submit" value="Sign Up">
    </form>
</body>
</html>