<?php 
include_once "controller/signup.inc.php";
session_start();

$error = '';
$success = '';

if(isset($_POST['submit'])){
    // Storing variables for validation
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $password = $_POST['password'];
    $password_repeat = $_POST['repeat_password'];

    // Data validation using methods
    if(emptyInputSignUp($first_name, $last_name, $birthday, $email, $password, $password_repeat)) {
        $error = "Please fill in all fields.";
    } elseif(!invalidInputs($first_name, $last_name)) {
        $error = "Invalid name inputs.";
    } elseif(!checkEmail($email)) {
        $error = "Invalid email format.";
    } elseif(!invalidPasswordFormat($password)) {
        $error = "Password must be at least 5 characters long.";
    } elseif(!checkPassword($password, $password_repeat)) {
        $error = "Passwords do not match.";
    } elseif(!checkAge($birthday)) {
        $error = "You must be at least 18 years old.";
    } elseif(!emailExists($email)) {
        $error = "Email already exists.";
    } else {
        if (createUser($first_name, $last_name, $birthday, $email, $password)) {
            $success = "Account created successfully! Welcome to DigitalSea.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
} else {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - DigitalSea</title>
    <!-- Load Font Awesome asynchronously -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></noscript>
</head>
<style>
    .page-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    #container {
        background-color: var(--ivory-color);
        display: flex;
        flex: 1;
        min-height: calc(100vh - 120px);
        position: relative;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    .signup-container {
        background-color: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }

    .signup-container h1 {
        text-align: center;
        color: var(--noir-color);
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: 600;
    }

    .signup-container input {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0;
        border: 1px solid var(--navy-color);
        border-radius: 6px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .signup-container input:focus {
        border-color: var(--noir-color);
        box-shadow: var(--shadow-input);
        outline: none;
    }

    .signup-container input[type="submit"] {
        background-color: var(--noir-color);
        color: white;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        padding: 14px;
        margin-top: 20px;
        transition: all 0.3s ease;
    }

    .signup-container input[type="submit"]:hover {
        background-color: var(--button-color);
        transform: translateY(-2px);
    }

    .signup-container .error {
        background-color: #ffebee;
        color: #d32f2f;
        padding: 12px;
        border-radius: 6px;
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .signup-container .success {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 12px;
        border-radius: 6px;
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .signup-container .login-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: var(--noir-color);
    }

    .signup-container .login-link a {
        color: var(--button-color);
        text-decoration: none;
        font-weight: 500;
    }

    .signup-container .login-link a:hover {
        text-decoration: underline;
    }

    .signup-container .form-row {
        display: flex;
        gap: 15px;
    }

    .signup-container .form-row input {
        flex: 1;
    }

    /* Password Strength Meter Styles */
    .password-strength {
        margin-top: 5px;
        height: 4px;
        background-color: #eee;
        border-radius: 2px;
        overflow: hidden;
    }

    .strength-meter {
        height: 100%;
        width: 0;
        transition: all 0.3s ease;
    }

    .strength-meter.weak {
        width: 25%;
        background-color: #ff4444;
    }

    .strength-meter.moderate {
        width: 50%;
        background-color:rgb(255, 170, 0);
    }

    .strength-meter.strong {
        width: 75%;
        background-color:rgb(255, 230, 0);
    }

    .strength-meter.very-strong {
        width: 100%;
        background-color:rgb(0, 200, 81);
    }

    .strength-text {
        font-size: 12px;
        margin-top: 5px;
        color: #666;
    }

    .password-requirements {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
</style>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php"?>
        <div id="container">
            <div class="signup-container">
                <h1>Create Account</h1>
                <form action="" method="post">
                    <div class="form-row">
                        <input type="text" name="first_name" placeholder="First name" autofocus="autofocus" required>
                        <input type="text" name="last_name" placeholder="Last name" required>
                    </div>
                    <input type="email" name="email" placeholder="Email address" required autocomplete="off">
                    <input type="date" name="birthday" required>
                    <div class="password-field">
                        <input type="password" name="password" id="password" placeholder="Password" required autocomplete="new-password">
                        <div class="password-strength">
                            <div class="strength-meter"></div>
                        </div>
                        <div class="strength-text"></div>
                        <div class="password-requirements">
                            Password must be at least 5 characters long and can contain letters, numbers, and symbols.
                        </div>
                    </div>
                    <input type="password" name="repeat_password" placeholder="Confirm password" required>
                    <input type="submit" name="submit" value="Create Account">
                </form>

                <?php if (!empty($error)): ?>
                    <div class='error'><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class='success'><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="login-link">
                    Already have an account? <a href="login.php">Log In</a>
                </div>
            </div>
        </div>
        <?php include "footer/footer.php"; ?>
    </div>

    <script>
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthMeter = document.querySelector('.strength-meter');
            const strengthText = document.querySelector('.strength-text');
            
            // Reset classes
            strengthMeter.className = 'strength-meter';
            
            // Calculate password strength
            let strength = 0;
            let feedback = [];
            
            // Check length
            if (password.length >= 5) {
                strength += 1;
                if (password.length >= 8) {
                    strength += 1;
                }
            }
            
            // Check for letters
            if (/[a-zA-Z]/.test(password)) {
                strength += 1;
            }
            
            // Check for numbers
            if (/[0-9]/.test(password)) {
                strength += 1;
            }
            
            // Check for symbols
            if (/[^a-zA-Z0-9]/.test(password)) {
                strength += 1;
            }
            
            // Determine strength level
            let level = '';
            if (strength <= 2) {
                level = 'weak';
                feedback = 'Weak password';
            } else if (strength === 3) {
                level = 'moderate';
                feedback = 'Moderate password';
            } else if (strength === 4) {
                level = 'strong';
                feedback = 'Strong password';
            } else {
                level = 'very-strong';
                feedback = 'Very strong password';
            }
            
            // Update UI
            strengthMeter.classList.add(level);
            strengthText.textContent = feedback;
        });
    </script>
</body>
</html>
<?php } ?>