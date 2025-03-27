<?php
include "dbh.inc.php"; 


// shiqon nese email ekziston
function checkData($email) {
    global $conn;
    $sql = "SELECT * FROM users WHERE email = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0; 
}

//qasja ne llogari
function login($email, $password) {
    global $conn;

    // SELECT THE DATA INSERTED
    $sql = "SELECT * FROM users WHERE email = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc(); 

        // Check if the user exists and the password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Start the session and store user id in the session
            session_start();
            $_SESSION['user_id'] = $user['user_id'];

            
        } else {
            return false;
        }
    } else {

        header("Location: ../login.php?sucess=failed"); // Change this to your desired page
        exit();

        // Query execution failed
        return false;
    }
}


?>