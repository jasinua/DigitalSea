<?php
include_once "controller/function.php";
include_once "controller/profile.inc.php";  // This should include the session and user data
include "header/header.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    var_dump($_SESSION); // Debug: This will output all session variables
    header("Location login.php");
    die("Session user_id is not set. Please log in.");
}
$user_id = $_SESSION['user_id']; // Retrieve user_id from session

// Retrieve data sent via AJAX
if (isset($_POST['first_name']) || isset($_POST['last_name']) || isset($_POST['email']) || isset($_POST['address']) || isset($_POST['current_password'])) {
    $first_name = !empty($_POST['first_name']) ? trim($_POST['first_name']) : $user['first_name'];
    $last_name = !empty($_POST['last_name']) ? trim($_POST['last_name']) : $user['last_name'];
    $email = !empty($_POST['email']) ? trim($_POST['email']) : $user['email'];
    $address = !empty($_POST['address']) ? trim($_POST['address']) : $user['address'];

    // Handle password change if provided
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();

        if (!password_verify($current_password, $user_data['password'])) {
            echo "Current password is incorrect.";
            exit();
        }

        if ($new_password !== $confirm_password) {
            echo "New passwords do not match.";
            exit();
        }

        if (strlen($new_password) < 8) {
            echo "New password must be at least 8 characters long.";
            exit();
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();
    }

    // Validate email if it was changed
    if (!empty($_POST['email']) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Update the database
    $stmt = $conn->prepare("CALL updateProfile(?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $address, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully.";
    } else {
        echo "Error updating profile.";
    }

    $stmt->close();
    $conn->close();
}

// Check login status
if (!isLoggedIn($user_id)) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
<title>Profile Page</title>
</head>
<style>
    .page-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        width: 100%;
        min-height: calc(100vh - 120px);
        background-color: #f5f6fa;
        padding: 20px;
        overflow: hidden;
    }

    .profile {
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 800px;
        gap: 0;
        margin: 50px auto;
        position: relative;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
    }

    .userProfile, .editProfile {
        width: 100%;
        box-sizing: border-box;
        padding: 40px;
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .userProfile {
        display: flex;
        flex-direction: row;
        text-align: left;
        color: #2c3e50;
        transition: all 0.3s ease;
        animation: fadeIn 0.5s ease-out;
        gap: 40px;
        align-items: center;
        position: relative;
        z-index: 2;
        margin-top: 70px;
    }

    .userProfile:hover {
        transform: translateY(-5px);
    }

    .profile-icon {
        flex-shrink: 0;
        width: 160px;
        height: 160px;
        background-color: #f8f9fa;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e9ecef;
    }

    .profile-icon i {
        font-size: 80px;
        color: #153147;
        transition: transform 0.5s ease;
    }

    .profile-info {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .userProfile .user_name_lastname {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
        transition: transform 0.3s ease;
    }

    .userProfile .user_data {
        margin: 0;
        font-size: 1.1rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .userProfile .user_data i {
        font-size: 1.2rem;
        color: #153147;
        width: 24px;
    }

    .editXlogout {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        gap: 15px;
        margin-top: 20px;
    }

    .userProfile .edit-button, .userProfile .logout-button {
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-speed) ease;
        border: none;
        font-size: 1rem;
    }

    .userProfile .edit-button {
        background-color: var(--button-color);
        color: white;
    }

    .userProfile .edit-button:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(21, 49, 71, 0.3);
    }

    .userProfile .logout-button {
        background-color: #f8f8f8;
        color: var(--noir-color);
    }

    .userProfile .logout-button:hover {
        background-color: #eee;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .editProfile {
        position: relative;
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1;
    }

    .editProfile.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }

    .editProfile h2 {
        margin-bottom: 30px;
        color: #2c3e50;
        font-size: 1.8rem;
    }

    .editProfile form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: repeat(4, auto);
        gap: 20px 30px;
        margin-bottom: 30px;
    }

    .form-grid .input-field {
        width: 100%;
    }

    .form-grid .label-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .form-grid .spacer {
        grid-column: 2;
        grid-row: 4;
    }

    .submitCancel {
        display: flex;
        gap: 15px;
        justify-content: center;
        max-width: 400px;
        margin: 0 auto;
    }

    .submit-btn, .cancel-btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn {
        background-color: #153147;
        color: white;
    }

    .submit-btn:hover {
        background-color: #1a3d5a;
        transform: translateY(-2px);
    }

    .cancel-btn {
        background-color: #f8f8f8;
        color: #2c3e50;
    }

    .cancel-btn:hover {
        background-color: #eee;
        transform: translateY(-2px);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .profile {
            margin: 20px auto;
        }

        .userProfile, .editProfile {
            padding: 20px;
            border-radius: 16px;
        }

        .editProfile {
            margin-top: 16px;
        }

        .userProfile {
            flex-direction: column;
            text-align: center;
        }

        .form-grid {
            grid-template-columns: 1fr;
            grid-template-rows: none;
            gap: 15px;
        }

        .spacer {
            display: none;
        }
    }

    @media (max-width: 950px) {
        .profile {
            flex-direction: column;
            max-width: 95%;
            align-items: center;
            min-height: auto;
            gap: 20px;
        }
        
        .userProfile, .editProfile {
            max-width: 100%;
            border-radius: 20px;
            margin: 10px 0;
        }

        .editProfile {
            position: relative !important;
            transform: none !important;
            animation: fadeIn 0.5s ease-out;
        }

        .editProfile.behind {
            display: none;
        }

        .editProfile.active {
            display: block;
        }
    }

    @media (max-width: 600px) {
        .userProfile {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .profile-icon {
            width: 100px;
            height: 100px;
        }

        .profile-info {
            align-items: center;
        }

        .userProfile .user_data {
            justify-content: center;
        }

        .editXlogout {
            justify-content: center;
        }
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-overlay.active {
        display: flex;
    }

    .editProfile {
        background-color: white;
        padding: 40px;
        border-radius: 20px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        z-index: 10000;
    }

    .modal-overlay.active .editProfile {
        transform: translateY(0);
    }

    .close-modal {
        position: absolute;
        top: 20px;
        right: 20px;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #666;
        cursor: pointer;
        padding: 5px;
        transition: all 0.3s ease;
        z-index: 10001;
    }

    .close-modal:hover {
        color: #153147;
        transform: rotate(90deg);
    }

    .editProfile h2 {
        margin-bottom: 30px;
        color: #2c3e50;
        font-size: 1.8rem;
        padding-right: 30px;
    }

    .editProfile form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .first_last_name {
        display: flex;
        gap: 20px;
    }

    .first_last_name .input-field {
        flex: 1;
    }

    .password-section {
        margin-top: 10px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .password-section h3 {
        margin-bottom: 15px;
        color: #2c3e50;
        font-size: 1.2rem;
    }

    .password-fields {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .submitCancel {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .submit-btn, .cancel-btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn {
        background-color: #153147;
        color: white;
    }

    .submit-btn:hover {
        background-color: #1a3d5a;
        transform: translateY(-2px);
    }

    .cancel-btn {
        background-color: #f8f8f8;
        color: #2c3e50;
    }

    .cancel-btn:hover {
        background-color: #eee;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .editProfile {
            padding: 30px;
            width: 95%;
        }

        .first_last_name {
            flex-direction: column;
            gap: 15px;
        }
    }

    .input-field {
        width: 100%;
        padding: 15px 20px;
        margin: 8px 0;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 1rem;
        box-sizing: border-box;
        transition: all 0.3s ease;
        background-color: white;
        color: #2c3e50;
    }

    .input-field:focus {
        border-color: #153147;
        box-shadow: 0 0 0 3px rgba(21, 49, 71, 0.1);
        outline: none;
        transform: translateY(-2px);
    }

    .input-field::placeholder {
        color: #a0a0a0;
        font-size: 0.95rem;
    }

    .input-field:hover {
        border-color: #153147;
    }

    .password-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #e9ecef;
    }

    .password-section h3 {
        margin-bottom: 20px;
        color: #2c3e50;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .password-fields {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .first_last_name {
        display: flex;
        gap: 20px;
        margin-bottom: 10px;
    }

    .first_last_name .input-field {
        flex: 1;
    }

    .editProfile form {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .submitCancel {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .submit-btn, .cancel-btn {
        flex: 1;
        padding: 15px;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn {
        background-color: #153147;
        color: white;
    }

    .submit-btn:hover {
        background-color: #1a3d5a;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(21, 49, 71, 0.2);
    }

    .cancel-btn {
        background-color: #f8f8f8;
        color: #2c3e50;
        border: 2px solid #e9ecef;
    }

    .cancel-btn:hover {
        background-color: #eee;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .input-field {
            padding: 12px 15px;
        }

        .first_last_name {
            flex-direction: column;
            gap: 15px;
        }

        .submit-btn, .cancel-btn {
            padding: 12px;
        }
    }
</style>
<body>
    <div class="page-wrapper">
        <!-- Search bar copied from header/header.php -->
        <div class="search-container" style="margin: 30px auto 0 auto; max-width: 500px;">
            <form action="index.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="Search..." class="search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="button" class="clear-search" title="Clear search">×</button>
            </form>
        </div>
        <div class="profile">
            <div class="userProfile">
                <div class="profile-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <h2 class="user_name_lastname" id="user_name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                    <div class="user_data" id="user_email">
                        <i class="fas fa-envelope"></i>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    <div class="user_data" id="user_address">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($user['address']); ?>
                    </div>
                    <div class="editXlogout">
                        <p class="edit-button" id="edit" onclick="editFunction()">Edit Profile</p>
                        <p class="logout-button" onclick="logoutFunction()">Log out</p>
                    </div>
                </div>
            </div>
            <div class="editProfile">
                <h2>Edit Profile</h2>
                <form action="" method="post">
                    <div class="form-grid">
                        <input type="text" name="first_name" placeholder="<?php echo htmlspecialchars($user['first_name']); ?>" class="input-field">
                        <input type="password" name="current_password" placeholder="Current Password" class="input-field">
                        <input type="text" name="last_name" placeholder="<?php echo htmlspecialchars($user['last_name']); ?>" class="input-field">
                        <input type="password" name="new_password" placeholder="New Password" class="input-field">
                        <input type="email" name="email" placeholder="<?php echo htmlspecialchars($user['email']); ?>" class="input-field">
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" class="input-field">
                        <input type="text" name="address" placeholder="<?php echo htmlspecialchars($user['address']); ?>" class="input-field">
                        <div class="spacer"></div>
                    </div>
                    <div class="submitCancel">
                        <button type="submit" class="submit-btn">Save Changes</button>
                        <button type="button" class="cancel-btn" onclick="closeEdit()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "footer/footer.php"; ?>
</body>
<script>
    let userProfile = document.querySelector(".userProfile");
    let editProfile = document.querySelector(".editProfile");
    let editButton = document.getElementById("edit");
    let isEditing = false;

    function editFunction() {
        const editForm = document.querySelector('.editProfile');
        editForm.classList.add('active');
        document.querySelector('.edit-button').textContent = 'Editing...';
    }

    function closeEdit() {
        const editForm = document.querySelector('.editProfile');
        editForm.classList.remove('active');
        document.querySelector('.edit-button').textContent = 'Edit Profile';
    }

    // Prevent form submission (we'll handle it via AJAX)
    document.querySelector("form").addEventListener("submit", function(e) {
        e.preventDefault();
        submitChanges();
    });

    function submitChanges() {
        let first_name = document.querySelector("input[name='first_name']").value.trim();
        let last_name = document.querySelector("input[name='last_name']").value.trim();
        let email = document.querySelector("input[name='email']").value.trim();
        let address = document.querySelector("input[name='address']").value.trim();
        let current_password = document.querySelector("input[name='current_password']").value.trim();
        let new_password = document.querySelector("input[name='new_password']").value.trim();
        let confirm_password = document.querySelector("input[name='confirm_password']").value.trim();

        // Validate email if changed
        if (email !== "<?php echo htmlspecialchars($user['email']); ?>" && !validateEmail(email)) {
            alert("Please enter a valid email!");
            return;
        }

        // Validate passwords if any are provided
        if (current_password || new_password || confirm_password) {
            if (!current_password || !new_password || !confirm_password) {
                alert("Please fill in all password fields!");
                return;
            }
            if (new_password !== confirm_password) {
                alert("New passwords do not match!");
                return;
            }
            if (new_password.length < 8) {
                alert("New password must be at least 8 characters long!");
                return;
            }
        }

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "profile.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText.includes("successfully")) {
                    // Update UI only if server responded successfully
                    document.getElementById("user_name").innerText = 
                        (first_name || "<?php echo htmlspecialchars($user['first_name']); ?>") + " " + 
                        (last_name || "<?php echo htmlspecialchars($user['last_name']); ?>");
                    
                    document.getElementById("user_email").innerText = 
                        (email || "<?php echo htmlspecialchars($user['email']); ?>");
                    
                    document.getElementById("user_address").innerText = 
                        (address || "<?php echo htmlspecialchars($user['address']); ?>");

                    // Clear password fields
                    document.querySelector("input[name='current_password']").value = "";
                    document.querySelector("input[name='new_password']").value = "";
                    document.querySelector("input[name='confirm_password']").value = "";

                    // Close edit form
                    closeEdit();
                } else {
                    alert(xhr.responseText);
                }
            } else {
                console.error("Error updating profile:", xhr.responseText);
            }
        };

        xhr.send(
            "first_name=" + encodeURIComponent(first_name) + 
            "&last_name=" + encodeURIComponent(last_name) + 
            "&email=" + encodeURIComponent(email) + 
            "&address=" + encodeURIComponent(address) +
            "&current_password=" + encodeURIComponent(current_password) +
            "&new_password=" + encodeURIComponent(new_password) +
            "&confirm_password=" + encodeURIComponent(confirm_password)
        );
    }

    function logoutFunction() {
        // Make a call to logout.php
        fetch('controller/logout.php')
            .then(() => {
                // Once session is destroyed, redirect to login
                window.location.href = 'login.php';
            })
            .catch(err => {
                console.error('Logout failed, dear soul:', err);
            });
    }

    // Email validation helper
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // On page load, ensure editProfile is behind
    window.addEventListener('DOMContentLoaded', function() {
        editProfile.classList.add('behind');
    });

    // Add clear-search button logic for profile page
    $(document).ready(function() {
        // Show/hide clear button based on search input
        $('.search-input').on('input', function() {
            var $clearBtn = $(this).closest('form').find('.clear-search');
            if ($(this).val().length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }
        });
        // Clear search without redirecting or reloading
        $('.clear-search').on('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $input = $(this).closest('form').find('.search-input');
            $input.val('');
            $(this).hide();
            $input.focus();
        });
        // Initialize clear button visibility for each search bar
        $('.search-input').each(function() {
            var $clearBtn = $(this).closest('form').find('.clear-search');
            if ($(this).val().length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }
        });
    });
</script>
</html>
