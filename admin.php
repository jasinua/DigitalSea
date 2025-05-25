<?php
session_start();
require_once 'model/dbh.inc.php';
include_once 'header/header.php';

// Check if user is logged in and is an admin (isAdmin = 2)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdministrator']) || $_SESSION['isAdministrator'] != 2) {
    header("Location: login.php");
    exit();
}

// Fetch all users from the database
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "CALL fetchUsers()";
$result = $conn->query($sql);

// Store users in an array to process later
$users = [];
if ($result instanceof mysqli_result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free(); // Free the result set
}
// Clear any remaining results from the stored procedure
while ($conn->more_results() && $conn->next_result()) {;}

// Handle POST requests for deleting or updating users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    // Prevent changing or deleting your own account
    if ($userId !== $_SESSION['user_id']) {
        if (isset($_POST['deleteUser'])) {
            // Delete user
            $stmt = $conn->prepare("CALL deleteUser(?)");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
            $conn->next_result(); // Clear results after stored procedure
        } elseif (isset($_POST['isAdmin'])) {
            // Change admin status
            $newAdmin = intval($_POST['isAdmin']);
            $stmt = $conn->prepare("CALL updateUser(?, ?)");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ii", $newAdmin, $userId);
            $stmt->execute();
            $stmt->close();
            $conn->next_result(); // Clear results after stored procedure
        }
        // Refresh the page to reflect changes
        header("Location: admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DigitalSea</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php include 'css/admin-css.php'; ?>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <i class="fas fa-lock"></i>
            <h1>Admin Dashboard</h1>
        </div>

        <div class="content">
            <h2>User Management</h2>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Admin Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <?php
                                    $adminClass = "admin-" . $row['isAdmin'];
                                    $adminText = $row['isAdmin'] == 0 ? "User" : ($row['isAdmin'] == 1 ? "Moderator" : "Administrator");
                                    echo "<span class='admin-badge " . $adminClass . "'>" . $adminText . "</span>";
                                    if ($_SESSION['isAdministrator'] == 2 && $row['user_id'] != $_SESSION['user_id']) {
                                        echo "<form method='post' class='admin-action-form'>";
                                        echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'>";
                                        echo "<button type='submit' name='isAdmin' value='0' class='admin-action-btn' style='margin-right:5px;'>User</button>";
                                        echo "<button type='submit' name='isAdmin' value='1' class='admin-action-btn' style='margin-right:5px;'>Moderator</button>";
                                        echo "<button type='submit' name='isAdmin' value='2' class='admin-action-btn' style='margin-right:10px;'>Admin</button>";
                                        echo "<button type='submit' name='deleteUser' value='1' class='admin-action-btn' style='background:#e74c3c;color:white;'>Delete</button>";
                                        echo "</form>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan='4'>No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'footer/footer.php'; ?>
</body>
</html>