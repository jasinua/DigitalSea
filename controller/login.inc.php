<?php
session_start();  // Start the session at the top of the file
include_once "model/dbh.inc.php"; 

// shiqon nese email ekziston
function checkData($email) {
    global $conn;

    $stmt = $conn->prepare("CALL checkUserExist(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0; 
}

// qasja ne llogari
function login($email, $password) {
    global $conn;

    if(checkData($email)) {
        return false;
    } else {
        $stmt = $conn->prepare("CALL checkUserExist(?)");
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $user = $result->fetch_assoc(); 
    
            
            if ($user && password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['isAdministrator'] = $user['isAdmin'];
                return true;  // Successfully logged in
            } else {
                return false; // Incorrect credentials
            }
        } else {
            return false; // Something went wrong with the query
        }
    }
}
?>
