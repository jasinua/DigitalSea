<?php 
    include_once "controller/login.inc.php"; 
    include_once "controller/function.php";

    // Check for authentication cookies
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_email']) && isset($_COOKIE['user_password'])) {
        $email = $_COOKIE['user_email'];
        $hashed_password = $_COOKIE['user_password'];
        
        // Verify the stored credentials
        $stmt = $conn->prepare("CALL checkUserExist(?)");
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && $user['password'] === $hashed_password) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['isAdministrator'] = $user['isAdmin'];
                header("Location: index.php");
                exit();
            }
        }
    }

    include_once "controller/signup.inc.php";

    $error = '';
    $success = '';

    if(isset($_POST['register'])){
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

    // include "header.php";

    $error = '';

    if(!isset($_SESSION['user_id'])) {
        if(isset($_POST['submit'])){
            $email = $_POST['email'];
            $password = $_POST['password'];

            if(checkData($email)) {
                $error = "Email doesn't exist.";
            } else {
                $loginResult = login($email, $password); 
        
                if ($loginResult) {
                    header("Location: index.php"); 
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            }
        }
    } else {
        header("Location: index.php"); 
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - DigitalSea</title>
    <!-- Load Font Awesome asynchronously -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></noscript>
    <link rel="stylesheet" href="css/transitions.css">
    
</head>

<?php include "css/login-css.php"; ?>
<?php include "css/signup-css.php"; ?>

<body>
    <div class="page-wrapper">
        <?php include "header/header.php"?>
        <div id="container" class="page-container">
            <div id="signupContainer">
                <!-- Signup Form -->
                <div class="signup-container">
                    <h1>Create Account</h1>
                    <form id="registerForm" onsubmit="return handleRegister(event)">
                        <div class="form-row">
                            <input type="text" name="first_name" placeholder="First name" required>
                            <input type="text" name="last_name" placeholder="Last name" required>
                        </div>
                        <input type="email" name="email" placeholder="Email address" required autocomplete="off">
                        <input type="date" name="birthday" required>
                        <div class="password-field">
                            <input type="password" name="password" id="signup-password" placeholder="Password" required autocomplete="new-password">
                            <span class="password-toggle" onclick="toggleSignupPasswordVisibility()">
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
                        <input type="submit" value="Create Account">
                        <div id="registerError" class="error" style="display: none;"></div>
                    </form>

                    <div class="login-link">
                        Already have an account? <a href="javascript:void(0)" onclick="showLoginForm()">Log In</a>
                    </div>
                </div>
            </div>
            
            <div id='img' style="justify-content:right;">
                <div id="img-content">
                    <h1 style="align-self:right;" class="welcome-text">Welcome to DigitalSea</h1>
                    <p style="align-self:right;" class="welcome-text p">Your one-stop shop for all your digital needs</p>
                    <button class="shop-now-button" onClick="window.location.href='index.php'">Shop Now</button>
                </div>
            </div>
            <div id="loginContainer">
             <!-- Login Form -->
             <div class="login-container">
                    <h1>Log in to your account</h1>
                    <form id="loginForm" onsubmit="return handleLogin(event)">
                        <input type="email" name="email" placeholder="Email address" autofocus="autofocus" required>
                        <div class="password-field">
                            <input type="password" name="password" id="password" placeholder="Password" required>
                            <span class="password-toggle" onclick="togglePasswordVisibility()">
                                <i class="far fa-eye"></i>
                            </span>
                        </div>
                        <input type="submit" value="Log in">
                        <div id="loginError" class="error" style="display: none;"></div>
                    </form>

                    <div class="signup-link">
                        Don't have an account? <a href="javascript:void(0)" onclick="showSignupForm()">Sign Up</a>
                    </div>
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

        function toggleSignupPasswordVisibility() {
            const passwordInput = document.getElementById('signup-password');
            const toggleIcon = document.querySelector('.signup-container .password-toggle i');
            
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

        function showSignupForm() {
            const loginContainer = document.querySelector('#loginContainer');
            const signupContainer = document.querySelector('#signupContainer');
            const img = document.querySelector('#img');
            const imgContent = document.querySelector('#img-content');
            
            loginContainer.style.transform = 'translateX(100%)';
            signupContainer.style.transform = 'translateX(0)';
            img.style.transform = 'translateX(50%)';
            imgContent.style.transform = 'translateX(-100%)';
        }

        function showLoginForm() {
            const loginContainer = document.querySelector('#loginContainer');
            const signupContainer = document.querySelector('#signupContainer');
            const img = document.querySelector('#img');
            const imgContent = document.querySelector('#img-content');
            
            loginContainer.style.transform = 'translateX(0)';
            signupContainer.style.transform = 'translateX(-100%)';
            img.style.transform = 'translateX(-50%)';
            imgContent.style.transform = 'translateX(0)';
        }

        // Password strength meter
        document.getElementById('signup-password').addEventListener('input', function(e) {
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

        function handleLogin(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            formData.append('action', 'login');

            fetch('controller/ajax-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorDiv = document.getElementById('loginError');
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

            return false;
        }

        function handleRegister(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            formData.append('action', 'register');

            fetch('controller/ajax-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorDiv = document.getElementById('registerError');
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

            return false;
        }

        // Add event listeners to hide error messages when user starts typing
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const form = this.closest('form');
                const errorDiv = form.querySelector('.error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>