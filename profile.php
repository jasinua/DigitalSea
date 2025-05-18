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
        $stmt = $conn->prepare("SELECT first_name, last_name, email, address FROM users WHERE user_id = ?");
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
        $stmt = $conn->prepare("
           SELECT * FROM orders WHERE user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Get user's orders
    $orders = getUserOrders($user_id);

    // Handle profile updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_profile'])) {
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            $errors = [];

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            } else {
                // Check if email is already taken by another user
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
                $stmt->bind_param("si", $email, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "This email is already taken by another user";
                }
            }

            if (empty($errors)) {
                // Update profile
                $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, address = ? WHERE user_id = ?");
                $stmt->bind_param("ssssi", $first_name, $last_name, $email, $address, $user_id);

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
                $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
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
                        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
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
                    <div class="profile-section">
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
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                                </div>
                            </div>
                            <div class="button-group">
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>

                    <div class="profile-section password-section">
                        <h2 class="section-title">Change Password</h2>
                        <form method="POST" action="">
                            <div class="form-grid">
                                <div class="form-group" style="grid-column: 1 / 2; grid-row: 1;">
                                    <label for="current_password">Current Password</label>
                                    <div class="password-field">
                                        <input type="password" id="current_password" name="current_password" required>
                                        <span class="password-toggle" onclick="togglePasswordVisibility('current_password')">
                                            <i class="far fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" style="grid-column: 1 / 2; grid-row: 2;">
                                    <label for="new_password">New Password</label>
                                    <div class="password-field">
                                        <input type="password" id="new_password" name="new_password" required>
                                        <span class="password-toggle" onclick="togglePasswordVisibility('new_password')">
                                            <i class="far fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group" style="grid-column: 2 / 3; grid-row: 2;">
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
                                <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="profile-section orders-section">
                    <h2 class="section-title">Order History</h2>
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
                                                        <i class="fas fa-file-pdf"></i> View Invoice
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

                <div class="button-group" style="margin-top: 20px;">
                    <a href="controller/logout.php" class="btn btn-secondary">Log Out</a>
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
    </script>

    <style>
    .orders-section {
        margin-top: 2rem;
    }

    .orders-table {
        width: 100%;
        overflow-x: auto;
    }

    .orders-table table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .orders-table th,
    .orders-table td {
        padding: 1rem;
        border-bottom: 1px solid #eee;
        color: var(--page-text-color)
    }

    .orders-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: var(--page-text-color)
    }

    .orders-table tr:hover {
        background-color: #f8f9fa;
    }

    .orders-table .btn-secondary {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .orders-table .btn-secondary i {
        margin-right: 0.5rem;
    }

    .no-orders {
        text-align: center;
        color: #666;
        padding: 2rem;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .orders-table {
            font-size: 0.9rem;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 0.75rem;
        }
        
        .orders-table .btn-secondary {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
    }

    .invoice-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .invoice-btn:hover {
        background-color: var(--navy-color);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .invoice-btn i {
        font-size: 1.1rem;
    }

    .invoice-btn:active {
        transform: translateY(0);
        box-shadow: none;
    }

    @media (max-width: 768px) {
        .invoice-btn {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .invoice-btn i {
            font-size: 1rem;
        }
    }
    </style>
</body>
</html>
