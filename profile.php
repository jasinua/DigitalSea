<?php
include_once "controller/function.php";
include_once "controller/profile.inc.php";
include "header/header.php";

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
        } elseif (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long";
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
<style>
        .profile-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-content {
            padding: 40px;
        }

        .profile-section {
            margin-bottom: 40px;
        }

        .profile-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 1.5rem;
            color: #153147;
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .profile-info-container {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 40px;
            align-items: start;
            margin-bottom: 40px;
        }

        .profile-avatar {
            width: 200px;
            height: 200px;
            background: rgba(21, 49, 71, 0.1);
        border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(21, 49, 71, 0.2);
        }

        .profile-avatar i {
            font-size: 80px;
            color: #153147;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .info-item {
        display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-radius: 12px;
        }

        .info-item i {
            font-size: 1.2rem;
            color: #153147;
            width: 24px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
        }

        .forms-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;   
            color: #153147;
        }

        .form-group input {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #153147;
            box-shadow: 0 0 0 3px rgba(21, 49, 71, 0.1);
        outline: none;
    }

        .password-section {
            margin-top: 0;
            padding-top: 0;
            border-top: none;
        }

        .button-group {
        display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: #153147;
        color: white;
        }

        .btn-primary:hover {
            background: #1a3d5a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(21, 49, 71, 0.2);
        }

        .btn-secondary {
            background: #f8f8f8;
            color: #153147;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #eee;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 992px) {
            .profile-info-container {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 20px;
            }

            .profile-avatar {
                width: 150px;
                height: 150px;
                margin: 0 auto;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .forms-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
            .profile-avatar {
                width: 120px;
                height: 120px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .profile-content {
                padding: 20px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
</style>
</head>
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
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group" style="grid-column: 1 / 2; grid-row: 2;">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                <div class="form-group" style="grid-column: 2 / 3; grid-row: 2;">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            <div class="button-group">
                                <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
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

                <div class="button-group" style="margin-top: 20px;">
                    <a href="controller/logout.php" class="btn btn-secondary">Logout</a>
                </div>
        </div>
        </div> 
    </div>

    <?php include "footer/footer.php"; ?>
</body>
</html>

