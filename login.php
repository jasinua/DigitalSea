<?php 
    include_once "model/dbh.inc.php";

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

    if(isset($_SESSION['user_id'])) {
        header("Location: index.php"); 
        exit();
    }

    $tokenSet = false;
    if(isset($_GET['token'])) {
        $token = $_GET['token'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user) {
            $tokenSet = true;
        }
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

            <div id="confirmationContainer">
                <!-- Confirmation Form -->
                <div class="signup-container">
                    <h1>Confirm Email</h1>
                    <form id="confirmationForm" onsubmit="return handleConfirmation(event)">
                        <label id="confirmationEmailLabel">Enter the confirmation code sent to your email</label>
                        <div class="confirm-code-field">
                            <input type="text" name="confirm-code" id="confirm-code" placeholder="Confirmation Code" required>
                        </div>
                        <input type="submit" value="Confirm Email">
                        <div id="confirmationError" class="error" style="display: none;"></div>
                    </form>

                    <div class="login-link">
                        <a href="javascript:void(0)" onclick="resendCode()">Resend Code</a> | 
                        <a href="javascript:void(0)" onclick="showSignupFromConfirmation()">Back to Sign Up</a>
                    </div>
                </div>
            </div>  
            
            <div id='img' style="justify-content:right;">
                <div id="img-content">
                    <h1 style="text-align:center;" class="welcome-text">Welcome to DigitalSea</h1>
                    <p style="text-align:center;" class="welcome-text p">Your one-stop shop for all your digital needs</p>
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
                    <div class="signup-link">
                        <a href="javascript:void(0)" onclick="showForgotPassword()">Forgot Password?</a>
                    </div>  
                </div>
            </div>

            <?php if(!isset($_GET['token'])) { ?>
            <div id="forgotPasswordContainer">
                <!-- Forgot Password Form -->
                <div class="login-container">
                    <h1>Forgot Password</h1>
                    <form id="confirmationEmailLabel" onsubmit="return handleForgotPassword(event)" style="color: var(--noir-color)">
                        <label id="forgotPasswordEmailLabel">Enter your email</label>
                        <div class="confirm-code-field">
                            <input type="text" name="forgot-password-email" id="forgot-password-email" placeholder="Email" required>
                        </div>
                        <input type="submit" value="Reset Password" id="forgotPasswordSubmit">
                        <div id="forgotPasswordError" class="error" style="display: none;"></div>
                        <div id="forgotPasswordSuccess" class="success" style="display: none;"></div>
                    </form>

                    <div class="signup-link">
                        <a href="javascript:void(0)" onclick="showLoginFromForgotPassword()">Back to Log In</a>
                    </div>
                    
                </div>
            </div> 


            <?php }else{ ?>
                <script>
                    document.getElementById('loginContainer').style.display = 'none';
                    document.getElementById('registerContainer').style.display = 'none';
                    document.getElementById('confirmationContainer').style.display = 'none';
                </script>
            
            <div id="forgotPasswordContainer">
                <!-- Forgot Password Form -->
                <div class="login-container">
                    <h1>Reset Password</h1>
                    <form id="confirmationEmailLabel" onsubmit="return handleForgotPasswordChange(event)" style="color: var(--noir-color)">
                        <label id="forgotPasswordEmailLabel">Enter your new password</label>
                        <div class="confirm-code-field">

                            <div class="password-field">
                                <input type="password" name="password-reset" id="password-reset" placeholder="Password" required>
                                <span class="password-toggle" onclick="togglePasswordVisibilityReset('password-reset')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>

                            <div class="password-field">
                                <input type="password" name="password-confirm" id="password-confirm" placeholder="Confirm Password" required>
                                <span class="password-toggle" onclick="togglePasswordVisibilityReset('password-confirm')">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>

                        </div>
                        <input type="submit" value="Reset Password">
                        
                        <div id="forgotPasswordError" class="error" style="display: none;"></div>
                        <div id="forgotPasswordSuccess" class="success" style="display: none;"></div>

                    </form>
                </div>
            </div> 
            <?php } ?>
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
            setTimeout(() => {
                signupContainer.style.transform = 'translateX(0)';
            }, 250);
            img.style.transform = 'translateX(50%)';
            imgContent.style.transform = 'translateX(-100%)';
        }

        function showSignupConfirmation() {
            const signupContainer = document.querySelector('#signupContainer');
            const confirmationContainer = document.querySelector('#confirmationContainer');
            console.log("yeet")

            signupContainer.style.transform = 'translateY(-100%)';
            confirmationContainer.style.transform = 'translateY(0)';
        }

        function showSignupFromConfirmation() {
            const confirmationContainer = document.querySelector('#confirmationContainer');
            const signupContainer = document.querySelector('#signupContainer');
            
            // Reset the registration form
            const registerForm = document.getElementById('registerForm');
            registerForm.reset();
            
            // Re-enable the register button
            const registerButton = registerForm.querySelector('input[type="submit"]');
            registerButton.disabled = false;
            registerButton.value = 'Create Account';
            
            // Clear any error messages
            document.getElementById('registerError').style.display = 'none';
            document.getElementById('confirmationError').style.display = 'none';
            
            // Reset password strength meter
            const strengthMeter = document.querySelector('.strength-meter');
            const strengthText = document.querySelector('.strength-text');
            strengthMeter.className = 'strength-meter';
            strengthText.textContent = '';

            signupContainer.style.transform = 'translateY(0)';
            confirmationContainer.style.transform = 'translateY(100%)';
        }

        function showLoginForm() {
            const loginContainer = document.querySelector('#loginContainer');
            const signupContainer = document.querySelector('#signupContainer');
            const img = document.querySelector('#img');
            const imgContent = document.querySelector('#img-content');
            
            setTimeout(() => {
                loginContainer.style.transform = 'translateX(0)';
            }, 250);
            signupContainer.style.transform = 'translateX(-100%)';
            img.style.transform = 'translateX(-50%)';
            imgContent.style.transform = 'translateX(0)';
        }

        function showForgotPassword() {
            const loginContainer = document.querySelector('#loginContainer');
            const forgotPasswordContainer = document.querySelector('#forgotPasswordContainer');
            
            loginContainer.style.transform = 'translateY(-100%)';
            forgotPasswordContainer.style.transform = 'translateY(0)';
        }

        function showLoginFromForgotPassword() {
            const forgotPasswordContainer = document.querySelector('#forgotPasswordContainer');
            const loginContainer = document.querySelector('#loginContainer');
            
            forgotPasswordContainer.style.transform = 'translateY(100%)';
            loginContainer.style.transform = 'translateY(0)';
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

            fetch('controller/login_handler.php', {
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
            const submitButton = form.querySelector('input[type="submit"]');
            const formData = new FormData(form);

            // Disable submit button
            submitButton.disabled = true;
            submitButton.value = 'Creating Account...';

            // Validate passwords match
            if (formData.get('password') !== formData.get('repeat_password')) {
                document.getElementById('registerError').textContent = 'Passwords do not match';
                document.getElementById('registerError').style.display = 'block';
                submitButton.disabled = false;
                submitButton.value = 'Create Account';
                return false;
            }

            // Store registration data in session
            $.ajax({
                url: 'controller/store_registration.php',
                method: 'POST',
                data: {
                    first_name: formData.get('first_name'),
                    last_name: formData.get('last_name'),
                    email: formData.get('email'),
                    birthday: formData.get('birthday'),
                    password: formData.get('password')
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.status === 'success') {
                            // Send verification email
                            $.ajax({
                                url: 'controller/send_verification.php',
                                method: 'POST',
                                data: {
                                    email: formData.get('email'),
                                    username: formData.get('first_name') + ' ' + formData.get('last_name')
                                },
                                success: function(response) {
                                    try {
                                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                                        if (result.status === 'success') {
                                            document.getElementById('confirmationEmailLabel').textContent = 
                                                `Enter the confirmation code sent to ${formData.get('email')}`;
                                            showSignupConfirmation();
                                        } else {
                                            document.getElementById('registerError').textContent = result.message;
                                            document.getElementById('registerError').style.display = 'block';
                                            submitButton.disabled = false;
                                            submitButton.value = 'Create Account';
                                        }
                                    } catch (e) {
                                        console.error('Error parsing verification response:', e);
                                        document.getElementById('registerError').textContent = 'Failed to process verification response';
                                        document.getElementById('registerError').style.display = 'block';
                                        submitButton.disabled = false;
                                        submitButton.value = 'Create Account';
                                    }
                                },
                                error: function() {
                                    document.getElementById('registerError').textContent = 'Failed to send verification code';
                                    document.getElementById('registerError').style.display = 'block';
                                    submitButton.disabled = false;
                                    submitButton.value = 'Create Account';
                                }
                            });
                        } else {
                            document.getElementById('registerError').textContent = result.message;
                            document.getElementById('registerError').style.display = 'block';
                            submitButton.disabled = false;
                            submitButton.value = 'Create Account';
                        }
                    } catch (e) {
                        console.error('Error parsing registration response:', e);
                        document.getElementById('registerError').textContent = 'Failed to process registration response';
                        document.getElementById('registerError').style.display = 'block';
                        submitButton.disabled = false;
                        submitButton.value = 'Create Account';
                    }
                },
                error: function() {
                    document.getElementById('registerError').textContent = 'Failed to process registration';
                    document.getElementById('registerError').style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.value = 'Create Account';
                }
            });

            return false;
        }

        function handleConfirmation(event) {
            event.preventDefault();
            const form = event.target;
            const submitButton = form.querySelector('input[type="submit"]');
            const code = document.getElementById('confirm-code').value;

            // Disable submit button
            submitButton.disabled = true;
            submitButton.value = 'Verifying...';

            $.ajax({
                url: 'controller/verify_code.php',
                method: 'POST',
                data: {
                    code: code
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.status === 'success') {
                            window.location.href = 'index.php';
                } else {
                            document.getElementById('confirmationError').textContent = result.message;
                            document.getElementById('confirmationError').style.display = 'block';
                            submitButton.disabled = false;
                            submitButton.value = 'Confirm Email';
                        }
                    } catch (e) {
                        console.error('Error parsing confirmation response:', e);
                        document.getElementById('confirmationError').textContent = 'Failed to process confirmation response';
                        document.getElementById('confirmationError').style.display = 'block';
                        submitButton.disabled = false;
                        submitButton.value = 'Confirm Email';
                    }
                },
                error: function() {
                    document.getElementById('confirmationError').textContent = 'Failed to verify code';
                    document.getElementById('confirmationError').style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.value = 'Confirm Email';
                }
            });

            return false;
        }

        function resendCode() {
            const resendLink = document.querySelector('a[onclick="resendCode()"]');
            resendLink.style.pointerEvents = 'none';
            resendLink.style.opacity = '0.5';
            resendLink.textContent = 'Sending...';

            $.ajax({
                url: 'controller/send_verification.php',
                method: 'POST',
                data: {
                    email: document.querySelector('input[name="email"]').value,
                    username: document.querySelector('input[name="first_name"]').value + ' ' + 
                             document.querySelector('input[name="last_name"]').value
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        alert(result.message);
                    } catch (e) {
                        alert('Failed to process response');
                    }
                    resendLink.style.pointerEvents = 'auto';
                    resendLink.style.opacity = '1';
                    resendLink.textContent = 'Resend Code';
                },
                error: function() {
                    alert('Failed to resend verification code');
                    resendLink.style.pointerEvents = 'auto';
                    resendLink.style.opacity = '1';
                    resendLink.textContent = 'Resend Code';
                }
            });
        }

        function handleForgotPassword(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const email = formData.get('forgot-password-email');
            const submitButton = form.querySelector('input[type="submit"]');

            submitButton.disabled = true;
            submitButton.value = 'Sending...';

            $.ajax({    
                url: 'controller/forgot_password.php',
                method: 'POST',
                data: {
                    email: email
                },
                success: function(response) {
                    const result = response;
                    if (result['status'] === 'success') {
                        document.getElementById('forgotPasswordSuccess').textContent = result['message'];
                        document.getElementById('forgotPasswordSuccess').style.display = 'block';
                        submitButton.value = 'Resend Code';
                        submitButton.disabled = false;
                        submitButton.onclick = function(e) {
                            e.preventDefault();
                            resendForgotPasswordCode();
                        };
                    } else {
                        document.getElementById('forgotPasswordError').textContent = result['message'];
                        document.getElementById('forgotPasswordError').style.display = 'block';
                        submitButton.disabled = false;
                        submitButton.value = 'Reset Password';
                    }
                },
                error: function() {
                    document.getElementById('forgotPasswordError').textContent = "Failed to reset password";
                    document.getElementById('forgotPasswordError').style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.value = 'Reset Password';
                }
            });
        }

        function resendForgotPasswordCode() {
            const email = document.getElementById('forgot-password-email').value;
            const submitButton = document.getElementById('forgotPasswordSubmit');

            submitButton.disabled = true;
            submitButton.value = 'Sending...';

            $.ajax({    
                url: 'controller/forgot_password.php',
                method: 'POST',
                data: {
                    email: email
                },
                success: function(response) {
                    const result = response;
                    if (result['status'] === 'success') {
                        document.getElementById('forgotPasswordSuccess').textContent = result['message'];
                        document.getElementById('forgotPasswordSuccess').style.display = 'block';
                    } else {
                        document.getElementById('forgotPasswordError').textContent = result['message'];
                        document.getElementById('forgotPasswordError').style.display = 'block';
                    }
                    submitButton.disabled = false;
                    submitButton.value = 'Resend Code';
                },
                error: function() {
                    document.getElementById('forgotPasswordError').textContent = "Failed to resend code";
                    document.getElementById('forgotPasswordError').style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.value = 'Resend Code';
                }
            });
        }

        function handleForgotPasswordChange(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const password = formData.get('password-reset');
            const confirmPassword = formData.get('password-confirm');
            const submitButton = form.querySelector('input[type="submit"]');

            submitButton.disabled = true;
            submitButton.value = 'Resetting...';

            if (password !== confirmPassword) {
                document.getElementById('forgotPasswordError').textContent = "Passwords do not match";
                document.getElementById('forgotPasswordError').style.display = 'block';
                submitButton.disabled = false;
                submitButton.value = 'Reset Password';
                return;
            }

            $.ajax({
                url: 'controller/reset_password.php',
                method: 'POST',
                data: {
                    token: '<?php echo isset($_GET["token"]) ? $_GET["token"] : ""; ?>',
                    password: password
                },
                success: function(response) {
                    const result = response;
                    if (result['status'] === 'success') {
                        document.getElementById('forgotPasswordSuccess').textContent = result['message'];
                        document.getElementById('forgotPasswordSuccess').style.display = 'block';
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 3000);
                        submitButton.value = 'Reset Password';

                    } else {
                        document.getElementById('forgotPasswordError').textContent = result['message'];
                        document.getElementById('forgotPasswordError').style.display = 'block';
                        submitButton.disabled = false;
                        submitButton.value = 'Reset Password';
                    }
                },
                error: function() {
                    document.getElementById('forgotPasswordError').textContent = "Failed to reset password";
                    document.getElementById('forgotPasswordError').style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.value = 'Reset Password';
                }
            });
        }
        

        function togglePasswordVisibilityReset(inputId) {
            const passwordInput = document.getElementById(inputId);
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

        <?php if(isset($_GET['token'])) { ?>
            showForgotPassword();
        <?php } ?>
    </script>
</body>
</html>