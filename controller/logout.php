<?php
    // Start the session if it's not already started
    session_start();

    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Clear authentication cookies
    setcookie('user_email', '', time() - 3600, '/');
    setcookie('user_password', '', time() - 3600, '/');

    // Redirect to the login page
    header("Location: ../login.php");
    exit();
?>
