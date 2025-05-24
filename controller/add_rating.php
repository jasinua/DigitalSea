<?php
session_start();
include '../model/dbh.inc.php';

// Ensure no output before headers
ob_start();

// Set content type to JSON
header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please login to rate products');
    }

    if (!isset($_POST['product_id']) || !isset($_POST['rating'])) {
        throw new Exception('Invalid request');
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];

    // Validate rating value
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        throw new Exception('Invalid rating value');
    }

            // Check if user rated product
        $check_stmt = $conn->prepare("CALL getProdRating(?, ?)");
        if (!$check_stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $check_stmt->bind_param("ii", $user_id, $product_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        while ($conn->more_results() && $conn->next_result()) {;}
        $check_stmt->close();

        if ($result->num_rows > 0) {
            // Update existing rating
            $row = $result->fetch_assoc();
            $rating_id = $row['product_rating_id'];

            $update_stmt = $conn->prepare("CALL updateProdRating(?, ?)");
            if (!$update_stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $update_stmt->bind_param("ii", $rating, $rating_id);
            if (!$update_stmt->execute()) {
                throw new Exception('Failed to update rating');
            }
            while ($conn->more_results() && $conn->next_result()) {;}
            $update_stmt->close();

            echo json_encode(['status' => 'success', 'message' => 'Rating updated successfully']);
        } else {
            // Insert new rating
            $insert_stmt = $conn->prepare("CALL addProdRate(?, ?, ?)");
            if (!$insert_stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $insert_stmt->bind_param("iii", $product_id, $rating, $user_id);
            if (!$insert_stmt->execute()) {
                throw new Exception('Failed to add rating');
            }
            while ($conn->more_results() && $conn->next_result()) {;}
            $insert_stmt->close();

            echo json_encode(['status' => 'success', 'message' => 'Rating added successfully']);
        }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    ob_end_flush();
}
?> 