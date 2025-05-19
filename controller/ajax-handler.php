<?php
// Prevent any output before JSON response
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering
ob_start();

try {
    // Include database connection first
    include_once "../model/dbh.inc.php";
    include_once "login.inc.php";
    include_once "signup.inc.php";
    include_once "function.php";

    // Check if database connection is available
    if (!isset($conn) || $conn === null) {
        throw new Exception("Database connection not available");
    }

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = array('success' => false, 'message' => '');

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'login':
                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    if (checkData($email)) {
                        $response['message'] = "Email doesn't exist.";
                    } else {
                        $loginResult = login($email, $password);
                        if ($loginResult) {
                            $response['success'] = true;
                            $response['message'] = "Login successful!";
                            $response['redirect'] = "index.php";
                        } else {
                            $response['message'] = "Invalid email or password.";
                        }
                    }
                    break;

                case 'register':
                    $first_name = $_POST['first_name'];
                    $last_name = $_POST['last_name'];
                    $email = $_POST['email'];
                    $birthday = $_POST['birthday'];
                    $password = $_POST['password'];
                    $password_repeat = $_POST['repeat_password'];

                    if (emptyInputSignUp($first_name, $last_name, $birthday, $email, $password, $password_repeat)) {
                        $response['message'] = "Please fill in all fields.";
                    } elseif (!invalidInputs($first_name, $last_name)) {
                        $response['message'] = "Invalid name inputs.";
                    } elseif (!checkEmail($email)) {
                        $response['message'] = "Invalid email format.";
                    } elseif (invalidPasswordFormat($password)) {
                        $response['message'] = "Password must be at least 5 characters long.";
                    } elseif (!checkPassword($password, $password_repeat)) {
                        $response['message'] = "Passwords do not match.";
                    } elseif (!checkAge($birthday)) {
                        $response['message'] = "You must be at least 18 years old.";
                    } elseif (emailExists($email)) {
                        $response['message'] = "Email already exists.";
                    } else {
                        if (createUser($first_name, $last_name, $birthday, $email, $password)) {
                            $response['success'] = true;
                            $response['message'] = "Account created successfully! Welcome to DigitalSea.";
                            $response['redirect'] = "login.php";
                        } else {
                            $response['message'] = "Something went wrong. Please try again.";
                        }
                    }
                    break;

                default:
                    $response['message'] = "Invalid action specified.";
                    break;
            }
        } else {
            $response['message'] = "No action specified.";
        }
    } else {
        $response['message'] = "Invalid request method.";
    }

    // Clear any output buffer
    ob_clean();
    
    // Send JSON response
    echo json_encode($response);
    exit();

} catch (Exception $e) {
    // Clear any output buffer
    ob_clean();
    
    // Send error response
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
    exit();
}
?> 