<?php
    // Start the session if it's not already started
    session_start();

    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    if (isset($_GET['from']) && $_GET['from'] === 'header') {
        header("Location: ../login.php");
    } else {
        header("Location: login.php");
    }
    exit();
?>
