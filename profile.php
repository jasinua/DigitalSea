<?php
include_once "includes/function.php";
include_once "includes/profile.inc.php";  // This should include the session and user data
include "header.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    var_dump($_SESSION); // Debug: This will output all session variables
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
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, address = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $address, $user_id);

    if ($stmt->execute()) {
        // echo "Profile updated successfully.";
    } else {
        // echo "Error updating profile.";
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
<body>

    <div class="profile">
        <div class="userProfile">
            <!-- <img src="" alt=""> -->
            <p><i class="fas fa-user"></i></p> <!-- perkohsisht deri sa te implementojme img-->
            <h2 class="user_name_lastname" id="user_name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
            <p class="user_data" id="user_email">Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="user_data" id="user_address">Address: <?php echo htmlspecialchars($user['address']); ?> </p>
            <p class="edit_button" id="edit" onclick="editFunction()">Edito Profilin</p>
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

    <?php include "footer.php"; ?>
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
</script>
</html>
