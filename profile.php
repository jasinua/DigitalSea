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
if (isset($_POST['first_name']) || isset($_POST['last_name']) || isset($_POST['email']) || isset($_POST['address'])) {
    $first_name = !empty($_POST['first_name']) ? trim($_POST['first_name']) : $user['first_name'];
    $last_name = !empty($_POST['last_name']) ? trim($_POST['last_name']) : $user['last_name'];
    $email = !empty($_POST['email']) ? trim($_POST['email']) : $user['email'];
    $address = !empty($_POST['address']) ? trim($_POST['address']) : $user['address'];

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
<link rel="stylesheet" href="style.css"/>
<title>Profile Page</title>
</head>
<style>
    .page-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: calc(100vh - 120px);
        background-color: var(--ivory-color);
        padding: 20px;
        overflow: hidden;
    }

    .profile {
        display: flex;
        flex-direction: row;
        width: 100%;
        max-width: 1000px;
        gap: 0;
        margin: auto;
        position: relative;
        justify-content: center;
        align-items: center;
        min-height: 500px;
        box-sizing: border-box;
    }

    .userProfile {
        display: flex;
        flex-direction: column;
        text-align: center;
        color: var(--noir-color);
        max-width: 450px;
        width: 100%;
        background-color: white;
        padding: 30px;
        border-top-left-radius: 16px;
        border-bottom-left-radius: 16px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        transform: translateX(0px);
        transition: box-shadow 0.3s, opacity 0.3s, filter 0.3s, transform 0.5s;
        z-index: 2;
    }
    .userProfile.editing {
        box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        filter: brightness(0.97);
        transform: translateX(0px);
        transition: transform 0.5s ease;
    }

    .userProfile i {
        font-size: 70px;
        color: var(--button-color);
        margin-bottom: 20px;
    }

    .userProfile .user_data {
        margin: 10px 0;
        font-size: 1.1rem;
        color: var(--page-text-color);
    }

    .userProfile .user_name_lastname {
        font-size: 1.8rem;
        font-weight: 600;
        margin: 15px 0;
        color: var(--noir-color);
    }

    .editXlogout {
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }

    .userProfile .edit-button, .userProfile .logout-button {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .userProfile .edit-button {
        background-color: var(--button-color);
        color: white;
    }

    .userProfile .edit-button:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .userProfile .logout-button {
        background-color: #f8f8f8;
        color: var(--noir-color);
    }

    .userProfile .logout-button:hover {
        background-color: #eee;
        transform: translateY(-2px);
    }

    .editProfile {
        display: flex;
        flex-direction: column;
        text-align: center;
        color: var(--noir-color);
        max-width: 450px;
        width: 100%;
        background-color: white;
        padding: 30px;
        border-top-right-radius: 16px;
        border-bottom-right-radius: 16px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        opacity: 0;
        pointer-events: none;
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        transform: translateX(0);
        z-index: 1;
        transition: opacity 0.4s, transform 0.5s, z-index 0s 0.5s;
    }
    .editProfile.active {
        opacity: 1;
        pointer-events: auto;
        position: relative;
        left: auto;
        top: auto;
        height: auto;
        transform: translateX(0);
        z-index: 1;
        transition: opacity 0.4s, transform 0.5s, z-index 0s;
    }
    .editProfile.behind {
        opacity: 0;
        pointer-events: none;
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        transform: translateX(-230px);
        transition:  transform 0.5s ease;
        z-index: 1;
    }

    .input-field {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        box-sizing: border-box;
        transition: all 0.3s ease;
        background-color: white;
    }

    .input-field:focus {
        border-color: var(--button-color);
        box-shadow: 0 0 0 2px rgba(21, 49, 71, 0.1);
        outline: none;
    }

    .first_last_name {
        display: flex;
        justify-content: space-between;
        gap: 15px;
    }

    .submitCancel {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        gap: 15px;
        margin-top: 20px;
    }

    .submit-btn, .cancel-btn {
        width: 100%;
        padding: 12px;
        font-size: 1rem;
        font-weight: 500;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn {
        background-color: var(--button-color);
        color: white;
    }

    .submit-btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .cancel-btn {
        background-color: #f8f8f8;
        color: var(--noir-color);
    }

    .cancel-btn:hover {
        background-color: #eee;
        transform: translateY(-2px);
    }

    .emailXaddress p {
        margin: 10px 0;
        padding: 0;
    }

    @media (max-width: 950px) {
        .profile {
            flex-direction: column;
            max-width: 95%;
            align-items: center;
            min-height: auto;
        }
        .userProfile, .editProfile {
            max-width: 100%;
            border-radius: 16px !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
            transform: none !important;
        }
        .editProfile {
            margin-top: 20px;
            border-radius: 16px !important;
            position: relative !important;
            left: auto !important;
            top: auto !important;
            height: auto !important;
        }
    }
</style>
<body>
    <div class="page-wrapper">
        <div class="profile">
            <div class="userProfile">
                <!-- <img src="" alt=""> -->
                <p><i class="fas fa-user"></i></p> <!-- perkohsisht deri sa te implementojme img-->
                <h2 class="user_name_lastname" id="user_name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <div class="emailXaddress">
                    <p class="user_data" id="user_email">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="user_data" id="user_address">Address: <?php echo htmlspecialchars($user['address']); ?> </p>
                </div>
                <div class="editXlogout">
                    <p class="edit-button" id="edit" onclick="editFunction()">Edito Profilin</p>
                    <p class="logout-button" onclick="logoutFunction()">Log out</p>
                </div>
            </div>
            <div class="editProfile">
            <form action="" method="post">
                <div class="first_last_name">
                    <input type="text" name="first_name" placeholder="<?php echo htmlspecialchars($user['first_name']); ?>" class="input-field">
                    <input type="text" name="last_name" placeholder="<?php echo htmlspecialchars($user['last_name']); ?>" class="input-field">
                </div>
                <input type="email" name="email" placeholder="<?php echo htmlspecialchars($user['email']); ?>" class="input-field">
                <input type="text" name="address" placeholder="<?php echo htmlspecialchars($user['address']); ?>" class="input-field">
                <div class="submitCancel">
                    <button type="submit" class="submit-btn">Save Changes</button>
                    <button type="button" class="cancel-btn" onclick="cancelEdit()">Cancel</button>
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
        if (!isEditing) {
            userProfile.classList.add("editing");
            editProfile.classList.remove("behind");
            editProfile.classList.add("active");
            editButton.innerText = "Duke edituar profilin";
            isEditing = true;
        } else if(isEditing && editButton.innerText === "Duke edituar profilin") {
            isEditing = true;
        } else {
            submitChanges();
            isEditing = false;
        }
    }

    function submitChanges() {
        let first_name = document.querySelector("input[name='first_name']").value.trim();
        let last_name = document.querySelector("input[name='last_name']").value.trim();
        let email = document.querySelector("input[name='email']").value.trim();
        let address = document.querySelector("input[name='address']").value.trim();

        // Validate email if changed
        if (email !== "<?php echo htmlspecialchars($user['email']); ?>" && !validateEmail(email)) {
            alert("Ju lutem shkruani një email valid!");
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "profile.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                    // Update UI only if server responded successfully
                    document.getElementById("user_name").innerText = 
                    (first_name || "<?php echo htmlspecialchars($user['first_name']); ?>") + " " + 
                    (last_name || "<?php echo htmlspecialchars($user['last_name']); ?>");
                    
                document.getElementById("user_email").innerText = 
                    "Email: " + (email || "<?php echo htmlspecialchars($user['email']); ?>");
                    
                document.getElementById("user_address").innerText = 
                    "Address: " + (address || "<?php echo htmlspecialchars($user['address']); ?>");

                // Reset edit state
                cancelEdit();
            } else {
                console.error("Error updating profile:", xhr.responseText);
            }
        };

        xhr.send(
            "first_name=" + encodeURIComponent(first_name) + 
            "&last_name=" + encodeURIComponent(last_name) + 
            "&email=" + encodeURIComponent(email) + 
            "&address=" + encodeURIComponent(address)
        );
    }

    function cancelEdit() {
        userProfile.classList.remove("editing");
        editProfile.classList.remove("active");
        editProfile.classList.add("behind");
        editButton.innerText = "Edito Profilin";
        isEditing = false;
    }

    // Email validation helper
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    // Prevent form submission (we'll handle it via AJAX)
    document.querySelector("form").addEventListener("submit", function(e) {
            e.preventDefault();
            submitChanges();
    });

    function logoutFunction() {
        // Make a call to logout.php
        fetch('logout.php')
            .then(() => {
                // Once session is destroyed, redirect to login
                window.location.href = 'login.php';
            })
            .catch(err => {
                console.error('Logout failed, dear soul:', err);
            });
    }

    // On page load, ensure editProfile is behind
    window.addEventListener('DOMContentLoaded', function() {
        editProfile.classList.add('behind');
    });
</script>
</html>
