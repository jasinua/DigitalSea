<?php
session_start();
include_once "../model/dbh.inc.php";

header('Content-Type: application/json');

if (!isset($_POST['token']) || !isset($_POST['password']) ) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

if (isset($_POST['password']) && strlen($_POST['password']) < 5) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 5 characters long']);
    exit;
}

$token = $_POST['token'];
$password = $_POST['password'];

// Check if token exists and is not expired (1 hour validity)
$stmt = $conn->prepare("SELECT * FROM users WHERE token = ? AND token_time > ?");
$current_time = time() - 3600; // 1 hour ago
$stmt->bind_param("si", $token, $current_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired reset token']);
    exit;
}

// Get user data
$user = $result->fetch_assoc();

// Close the result set and statement
$result->close();
$stmt->close();

// Hash the new password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update the password and clear the token
$stmt = $conn->prepare("UPDATE users SET password = ?, token = NULL, token_time = NULL WHERE user_id = ?");
$stmt->bind_param("si", $hashed_password, $user['user_id']);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password has been reset successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to reset password']);
}

$stmt->close();
?> 