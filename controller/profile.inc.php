<?php
session_start();
include_once "model/dbh.inc.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You're not logged in. Session user_id is not set.");
}

$user_id = $_SESSION['user_id'];

// Verify database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Prepare query - adjust table/column names as needed
$query = "CALL getProfile(?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
}

$result = $stmt->get_result();

// Check if we got any results
if ($result->num_rows === 0) {
    // Debug: Show the user_id we searched for
    die("User not found. Searched for user_id: " . $user_id . 
        "<br>Query was: " . $query);
}

$user = $result->fetch_assoc();
$stmt->close();
?>