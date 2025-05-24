<?php
    include_once "controller/function.php";
    include_once "controller/profile.inc.php";
    include "header/header.php";
    require_once 'ordersPdf/order-pdf.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
            exit();
        }

    $user_id = $_SESSION['user_id'];

    // Function to get user data
    function getUserData($user_id) {
        global $conn;
        $stmt = $conn->prepare("CALL profileData(?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get initial user data
    $user = getUserData($user_id);

    // Function to get user's orders
    function getUserOrders($user_id) {
        global $conn;
        $stmt = $conn->prepare("CALL getOrders(?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Get user's orders
    $orders = getUserOrders($user_id);

    include_once "controller/function.php";

    if (isset($_POST['update_profile'])) {
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $address = trim($_POST['address'] ?? '');

            $errors = [];

            if (empty($errors)) {
                // Update profile
                $stmt = $conn->prepare("CALL updateProfile(?, ?, ?, ?)");
                $stmt->bind_param("sssi", $first_name, $last_name, $address, $user_id);

                if ($stmt->execute()) {
                    // Refresh user data
                    $user = getUserData($user_id);
                    $success = "Profile updated successfully";
                } else {
                    $errors[] = "Error updating profile";
                }
            }

    } elseif (isset($_POST['update_password'])) {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $errors = [];

            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $errors[] = "All password fields are required";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "New passwords do not match";
            } elseif (strlen($new_password) < 5) {
                $errors[] = "New password must be at least 5 characters long";
            } else {
                // Verify current password
                $stmt = $conn->prepare("CALL verifyPassword(?)");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_data = $result->fetch_assoc();

                if (!password_verify($current_password, $user_data['password'])) {
                    $errors[] = "Current password is incorrect";
                } else {
                    // Check if new password is same as current password
                    if (password_verify($new_password, $user_data['password'])) {
                        $errors[] = "New password must be different from current password";
                    } else {
                        // Update password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("CALL updatePassword(?,?)");
                        $stmt->bind_param("si", $hashed_password, $user_id);
                        
                        if ($stmt->execute()) {
                            $success = "Password updated successfully";
                        } else {
                            $errors[] = "Error updating password";
                        }
                    }
                }
            }
        }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - DigitalSea</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>

<?php include "css/profile-css.php"; ?>

<body>
    <div class="profile-container">

        <div class="profile-card">
            <div class="profile-content">
                <div class="profile-info-container">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-section profile-info-section">
                        <h2 class="section-title">Profile Information</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <div>
                                    <div class="info-label">Full Name</div>
                                    <div class="info-value"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <div class="info-label">Email</div>
                                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <div class="info-label">Address</div>
                                    <div class="info-value"><?php echo htmlspecialchars($user['address']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="forms-container">
                    <div class="profile-section">
                        <h2 class="section-title">Edit Profile</h2>
                        <form method="POST" action="">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                                <div class="form-group form-group-address">
                                    <label for="address">Address</label>
                                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                                </div>
                            </div>
                            <div class="button-group" style="margin-top: 55px; ">
                                <button type="submit" name="update_profile" class="btn btn-primary" style="width: 100%;">Update Profile</button>
                            </div>
                            <div class="button-group big-media" style="margin-top: 30px; margin-bottom: 10px;">
                                <a href="controller/logout.php" class="btn btn-secondary" style="width: 100%; text-align: center;">Log Out</a> 
                                <div class="order-history-btn" style="width: 100%; display: flex; justify-content: center; align-items: center; text-align: center; font-weight: bold;">Show Order History</div>
                            </div>
                        </form>
                    </div>

                    

                    <div class="profile-section password-section">
                        <h2 class="section-title">Change Password</h2>
                        <form method="POST" action="">
                            <div class="form-grid">
                                <div class="form-group" style="grid-column: 1 / 3; grid-row: 1;">
                                    <label for="current_password">Current Password</label>
                                    <div class="password-field">
                                        <input type="password" id="current_password" name="current_password" required>
                                        <span class="password-toggle" onclick="togglePasswordVisibility('current_password')">
                                            <i class="far fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" style="grid-column: 1 / 3; grid-row: 2;">
                                    <label for="new_password">New Password</label>
                                    <div class="password-field">
                                        <input type="password" id="new_password" name="new_password" required>
                                        <span class="password-toggle" onclick="togglePasswordVisibility('new_password')">
                                            <i class="far fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" style="grid-column: 1 / 3; grid-row: 3;">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <div class="password-field">
                                        <input type="password" id="confirm_password" name="confirm_password" required>
                                        <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                                            <i class="far fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="button-group">
                                <button type="submit" name="update_password" class="btn btn-primary" style="width: 100%;">Update Password</button>
                            </div>
                            <div class="button-group small-media" style="margin-top: 30px; margin-bottom: 10px;">
                                <a href="controller/logout.php" class="btn btn-secondary" style="width: 100%; text-align: center;">Log Out</a> 
                                <div class="order-history-btn" style="width: 100%; display: flex; justify-content: center; align-items: center; text-align: center; font-weight: bold;">Show Order History</div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="profile-section orders-section">
                    <!-- <button id="orderHistoryBtn" class="order-history-btn">Show Order History</button> -->
                    <div id="orderHistoryContent" style="display: none;">
                        <?php if ($orders->num_rows > 0): ?>
                            <div class="orders-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th width="25%" style="text-align: center;">Order ID</th>
                                            <th width="25%" style="text-align: center;">Date & Time</th>
                                            <th width="25%" style="text-align: center;">Total Amount</th>
                                            <th width="25%" style="text-align: center;">Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = $orders->fetch_assoc()): ?>
                                            <tr>
                                                <td style="text-align: center;">#<?php echo $order['order_id']; ?></td>
                                                <td style="text-align: center;"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></td>
                                                <td style="text-align: center;"><?php echo number_format($order['total_price'], 2); ?>€</td>
                                                <td style="text-align: center;">
                                                    <form action="ordersPdf/order-pdf.php" method="post">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                        <button type="submit" name="view_invoice" class="btn btn-secondary invoice-btn">
                                                            <i class="fas fa-file-pdf"></i> Download Invoice
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="no-orders">You haven't placed any orders yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <?php include "footer/footer.php"; ?>

    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        document.querySelectorAll('.order-history-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderHistoryContent = document.getElementById('orderHistoryContent');
                if (orderHistoryContent.style.display === 'none') {
                    orderHistoryContent.style.display = 'block';
                    btn.textContent = 'Hide Order History';
                    btn.classList.add('active');
                } else {
                    orderHistoryContent.style.display = 'none';
                    btn.textContent = 'Show Order History';
                    btn.classList.remove('active');
                }
            });
        });

    </script>
</body>
</html>
