<?php
session_start();
include_once "../model/dbh.inc.php";
include_once "signup.inc.php";

header('Content-Type: application/json');

if (!isset($_POST['code'])) {
    echo json_encode(['status' => 'error', 'message' => 'No verification code provided']);
    exit;
}

$code = $_POST['code'];

// Check if verification session exists
if (!isset($_SESSION['verification_code']) || !isset($_SESSION['verification_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Verification session expired']);
    exit;
}

// Verify the code
if ($code === $_SESSION['verification_code']) {
    try {
        // Get the stored registration data
        if (!isset($_SESSION['registration_data'])) {
            echo json_encode(['status' => 'error', 'message' => 'Registration data not found']);
            exit;
        }

        $registration_data = $_SESSION['registration_data'];

        // Create the user account
        if (createUser(
            $registration_data['first_name'],
            $registration_data['last_name'],
            $registration_data['birthday'],
            $registration_data['email'],
            $registration_data['password']
        )) {
            // Get the user data for login
            $stmt = $conn->prepare("CALL checkUserExist(?)");
            $stmt->bind_param("s", $registration_data['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Set session variables for login
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['isAdmin'] = $user['isAdmin'];

            // Clear all session data
            unset($_SESSION['verification_code']);
            unset($_SESSION['verification_email']);
            unset($_SESSION['registration_data']);

            echo json_encode(['status' => 'success', 'message' => 'Account created successfully']);
        } else {
            throw new Exception('Failed to create account');
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create account: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
}
?> 