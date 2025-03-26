<?php

include "dbh.inc.php"; 


//kontrollon nese inputet jane jo te zbrazeta
function emptyInputSignUp($first_name, $last_name, $birthday, $email, $password, $password_repeat) {
    return empty($first_name) || empty($last_name) || empty($birthday) || empty($email) || empty($password) || empty($password_repeat);
}

//shikon nese inputet kane karaktere jo te sakta
function invalidInputs($first_name, $last_name) {
    return preg_match("/^[a-zA-Z]*$/", $first_name) || preg_match("/^[a-zA-Z]*$/", $last_name);
}

//shikon nese email eshte format valid
function checkEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

//shikon nese passwordi ka format te sakte 
function invalidPasswordFormat($password) {
    return preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password);
}

// shikon nese passworded jane te njejta
function checkPassword($password, $passwordRepeat) {
    return $password === $passwordRepeat;
}

// mese personi qe po krijon llogari eshte mbi moshen 16 vjecare
function checkAge($date) {
    $dob = new DateTime($date);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    return $age >= 16;
}

// shiqon nese email ekziston
function emailExists($email) {
    global $conn;
    $sql = "SELECT * FROM users WHERE email = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0; 
}

//krijimki i userit
function createUser($first_name, $last_name, $birthday, $email, $password) {
    global $conn;

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (first_name, last_name, email, password, date_of_birth) VALUES (?, ?, ?, ?, ?);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $birthday);
    

    if ($stmt->execute()) {
        return true; 
        //header("Location: ..index.php?good=job");
        //exit;
    } else {
        return false;
    }
}

?>