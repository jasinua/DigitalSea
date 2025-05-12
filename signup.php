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
                header("Location: login.php");
                exit();
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

<?php include "css/signup-css.php"; ?>

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
                        <span class="password-toggle" onclick="togglePasswordVisibility()">
                            <i class="far fa-eye"></i>
                        </span>
                        <div class="password-strength">
                            <div class="strength-meter"></div>
                        </div>
                        <div class="strength-text"></div>
                        <div class="password-requirements">
                            Password must be at least 5 characters long and can contain letters, numbers, and symbols.
                        </div>
                    </div>
                    <div class="confirm-password-field">
                        <input type="password" name="repeat_password" id="confirm-password" placeholder="Confirm password" required>
                        <span class="confirm-password-toggle" onclick="toggleConfirmPasswordVisibility()">
                            <i class="far fa-eye"></i>
                        </span>
                    </div>
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
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            
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

        function toggleConfirmPasswordVisibility() {
            const passwordInput = document.getElementById('confirm-password');
            const toggleIcon = document.querySelector('.confirm-password-toggle i');
            
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