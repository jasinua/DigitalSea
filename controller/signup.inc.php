<?php
    include "model/dbh.inc.php"; 

    //kontrollon nese inputet jane jo te zbrazeta
    function emptyInputSignUp($first_name, $last_name, $birthday, $email, $password, $password_repeat) {
        return empty($first_name) || empty($last_name) || empty($birthday) || empty($email) || empty($password) || empty($password_repeat);
    }

    //shikon nese inputet kane karaktere jo te sakta
    function invalidInputs($first_name, $last_name) {
        return preg_match("/^[a-zA-Z]*$/", $first_name) && preg_match("/^[a-zA-Z]*$/", $last_name);
    }

    //shikon nese email eshte format valid
    function checkEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    //shikon nese passwordi ka format te sakte 
    function invalidPasswordFormat($password) {
        // Check if password is at least 5 characters long
        if (strlen($password) < 5) {
            return true;
        }
        
        // Password is valid if it's at least 5 characters long
        return false;
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
        return $age >= 18;
    }

    function emailExists($email) {
        global $conn;

        $stmt = $conn->prepare("CALL checkUserExist(?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows === 1; 
    }

    function createUser($first_name, $last_name, $birthday, $email, $password) {
        global $conn;

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("CALL insertUser(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $birthday);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
?>