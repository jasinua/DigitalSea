<?php
session_start();  // Start the session at the top of the file
include_once "dbh.inc.php"; 

// Ensure user_id is in session
if (!isset($_SESSION['user_id'])) {
    die("Session user_id is not set. Please log in.");
}

// Fetch user data from database using user_id
$user_id = $_SESSION['user_id']; 

// Prepare SQL query to fetch user details based on user_id
$stmt = $conn->prepare("SELECT first_name, last_name, email, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);  // Bind user_id parameter as an integer
$stmt->execute();  // Execute the query
$result = $stmt->get_result();  // Get the result set
$user = $result->fetch_assoc();  // Fetch associative array with user data
$stmt->close();  // Close the statement

// Check if user was found
if (!$user) {
    die("User not found.");
}

// You can now access $user['first_name'], $user['last_name'], and $user['email']
?>
