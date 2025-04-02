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
    header("Location: homepage.php");
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
    <div id="header"></div>

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
    <div id="footer"></div>

    <?php include "footer.php"; ?>
</body>
<script>
   let fields = ["user_name", "user_email", "user_address"];
    let userProfile = document.querySelector(".userProfile");
    let editProfile = document.querySelector(".editProfile");
    let edit_profile = document.getElementById("edit");

    function editFunction() {

        if (edit_profile.innerText === "Edito Profilin") {
            userProfile.classList.add("hidden");
            userProfile.classList.remove("active");
            setTimeout(() => {
                editProfile.classList.add("active");
                editProfile.classList.remove("hidden");
            }, 300);

            // Change button to submit
            edit_profile.innerText = "Duke Edituar Profili";
        } else {
            let first_name = document.querySelector("input[name='first_name']").value.trim();
            let last_name = document.querySelector("input[name='last_name']").value.trim();
            let after_email = document.querySelector("input[name='email']").value.trim();
            let after_address = document.querySelector("input[name='address']").value.trim();

            // Create a request to send data via AJAX to the server
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "profile.php", true); // Point to the update script
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // Handle the response from the server
            xhr.onload = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Log the response and update the page if success
                    if (xhr.responseText.trim() === "Profile updated successfully.") {
                        console.log("Profile successfully updated!");

                        // Update the displayed user info immediately
                        if (first_name !== "") document.getElementById("user_name").innerText = first_name + " " + (last_name !== "" ? last_name : "<?php echo htmlspecialchars($user['last_name']); ?>");
                        if (last_name !== "") document.getElementById("user_name").innerText = (first_name !== "" ? first_name : "<?php echo htmlspecialchars($user['first_name']); ?>") + " " + last_name;
                        if (after_email !== "") document.getElementById("user_email").innerText = "Email: " + after_email;
                        if (after_address !== "") document.getElementById("user_address").innerText = "Address: " + after_address;

                        // Close edit mode
                        cancelEdit();
                        edit_profile.innerText = "Edito Profilin";
                    } else {
                        console.log("Unexpected response:", xhr.responseText);
                    }
                } else {
                    console.log("XHR request failed with status:", xhr.status);
                }
            };

            // Send the data to the server
            xhr.send(
                "first_name=" + encodeURIComponent(first_name) + 
                "&last_name=" + encodeURIComponent(last_name) + 
                "&email=" + encodeURIComponent(after_email) + 
                "&address=" + encodeURIComponent(after_address)
            );
        }
    }

    // Prevent form submission on 'Save Changes'
    document.querySelector("form").addEventListener("submit", editFunction);

    function cancelEdit() {
        document.querySelector("input[name='first_name']").value = "<?php echo htmlspecialchars($user['first_name']); ?>";
        document.querySelector("input[name='last_name']").value = "<?php echo htmlspecialchars($user['last_name']); ?>";
        document.querySelector("input[name='email']").value = "<?php echo htmlspecialchars($user['email']); ?>";
        document.querySelector("input[name='address']").value = "<?php echo htmlspecialchars($user['address']); ?>";

        setTimeout(() => {
            userProfile.classList.remove("hidden");
            userProfile.classList.add("active");

            editProfile.classList.remove("active");
            editProfile.classList.add("hidden");
        }, 0);
    }

</script>
</html>
