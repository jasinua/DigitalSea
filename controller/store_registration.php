<?php
session_start();
include_once "../model/dbh.inc.php";
include_once "signup.inc.php";

header('Content-Type: application/json');

// Get POST data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$birthday = $_POST['birthday'];
$password = $_POST['password'];

// Validate data
if(emptyInputSignUp($first_name, $last_name, $birthday, $email, $password, $password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
    exit();
}

if(!invalidInputs($first_name, $last_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid name inputs.']);
    exit();
}

if(!checkEmail($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit();
}

if(invalidPasswordFormat($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 5 characters long.']);
    exit();
}

if(!checkAge($birthday)) {
    echo json_encode(['status' => 'error', 'message' => 'You must be at least 18 years old.']);
    exit();
}

if(emailExists($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
    exit();
}

// Store registration data in session
$_SESSION['registration_data'] = [
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'birthday' => $birthday,
    'password' => $password
];

echo json_encode(['status' => 'success']);
?> 