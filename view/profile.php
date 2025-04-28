<?php
include_once "../controller/function.php";
include_once "../controller/profile.inc.php";  // This should include the session and user data
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
        margin-top: 80px;
    }

    .profile {
        display: flex;
        flex-direction: row;
    }

    .userProfile {
        display: flex;
        flex-direction: column;
        text-align: center;
        color: var(--page-text-color);
        max-width: 450px;
        height: 370px;
        width: 100%;
        margin: 20px 0 20px 37%;
        background-color: white;
        padding: 30px;
        opacity: 1;
        border-radius: 15px;
        box-shadow: 0 0px 5px #153147;
        transition: transform 0.3s ease-in-out, opacity 0.5s ease-in;
        flex-grow: 1;
        z-index: 2;
    }

    .userProfile i {
        font-size: 70px;
        border-radius: 50%;
    }

    .userProfile .user_data {
        margin-top: 15px;
        margin-bottom: 15px;
        font-size: 20px;
    }

    .userProfile .user_name_lastname {
        font-size: 34px;
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .editXlogout {
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
    }

    .userProfile .edit-button, .userProfile .logout-button {
        padding: 0;
        margin-top: 15px;
        margin-left: 0;
        margin-right: 0;
    }

    .userProfile .edit-button:hover, .userProfile .logout-button:hover {
        color: #757575;
        cursor: pointer;
    }

    /* Hide userProfile when editProfile is active */
    .userProfile.hidden {
        margin-left: 30%;
        transition: margin-left 0.5s ease-in-out;
    }

    .userProfile.active {
        margin-left: 37%;
        transition: margin-left 0.5s ease-in-out;
    }

    .editProfile {
        flex-direction: column;
        text-align: center;
        opacity: 0;
        color: var(--page-text-color);
        max-width: 400px;
        width: 100%;
        margin: 35px -10%;
        background-color: white;
        padding: 30px;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        box-shadow: 0 0px 5px var(--navy-color);
        transition: transform 0.3s ease-in-out, opacity 0.5s ease;
        flex-grow: 1;
    }

    /* When active, slide editProfile into view */
    .editProfile.active {
        opacity: 1;
        transform: translateX(39%);
    }

    .editProfile.hidden {
        opacity: 0;
        transform: translateX(-60%);
    }

    /* Style the inputs */
    .input-field {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 2px solid var(--mist-color);
        border-radius: 6px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .input-field:focus {
        border-color: var(--navy-color);
        box-shadow: 0 0 5px var(--navy-color);
        outline: none;
    }

    /* Style for first and last name inputs */
    .first_last_name {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .submitCancel {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
    }

    /* Style for the submit button */
    .submit-btn {
        width: 100%;
        padding: 12px;
        background-color: var(--navy-color);
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .submit-btn:hover {
        background-color: var(--button-color-hover);
    }

    .submit-btn:focus {
        outline: none;
    }

    /* Style for the submit button */
    .cancel-btn {
        width: 100%;
        padding: 12px;
        background-color: var(--button-color);
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .cancel-btn:hover {
        background-color: var(--button-color-hover);
    }

    .cancel-btn:focus {
        outline: none;
    }

    .emailXaddress p {
        margin: 0;
        padding: 0;
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
            // Show edit form
            userProfile.classList.add("hidden");
            userProfile.classList.remove("active");
            setTimeout(() => {
                editProfile.classList.add("active");
                editProfile.classList.remove("hidden");
            }, 300);
            
            editButton.innerText = "Duke edituar profilin";
            isEditing = true;
        } else if(isEditing && editButton.innerText === "Duke edituar profilin") {
            isEditing = true;
        } else {
            // Submit changes
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
        // Hide edit form and show profile
        setTimeout(() => {
            userProfile.classList.remove("hidden");
            userProfile.classList.add("active");
            editProfile.classList.remove("active");
            editProfile.classList.add("hidden");
        }, 0);

        // Reset button state
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
</script>
</html>
