<style>
    .page-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    #container {
        /* background-color: var(--ivory-color); */
        background-color: white;
        display: flex;
        min-height: calc(100vh - 120px);
        max-width: 100%;
        justify-content: center;
        align-items: center;
        margin:0px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    #img{
        background-image:  radial-gradient(ellipse at top, rgb(26, 78, 118), 70%,var(--noir-color));
        height:100vh;
        width:100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #loginContainer{
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 50%;
    }

    .welcome-text{
        font-size: 64px;
        font-weight: 600;
        color: white;
        margin-bottom: 10px;
        
    }

    .welcome-text.p{
        font-size: 30px;
        font-weight: 400;
        color: white;
        margin-bottom: 20px;
    }

    .shop-now-button{
        background-color: white;
        color: var(--noir-color);
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        margin-top: 20px;
        transition: all 0.3s ease;
    }

    .login-container {
        background-color: white;
        padding: 40px;
        width: 50%;
        max-width: 550px;
        min-height: 400px;
        height:auto;
    }

    .login-container h1 {
        text-align: center;
        color: var(--noir-color);
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: 600;
    }

    .login-container input {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0;
        border: 1px solid #eee;
        border-radius: 6px;
        font-size: 15px;
        transition: all 0.5s ease;
    }

    .login-container input:focus {
        border-color: var(--noir-color);
        box-shadow: var(--shadow-input);
        outline: none;
    }

    .login-container input[type="submit"] {
        background-color: var(--noir-color);
        color: white;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        padding: 14px;
        margin-top: 20px;
        transform: translateY(0px);
        transition: all 0.3s ease;
    }

    .login-container input[type="submit"]:hover {
        background-color: var(--button-color);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .login-container .error {
        background-color: #ffebee;
        color: #d32f2f;
        padding: 12px;
        border-radius: 6px;
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .login-container .success {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 12px;
        border-radius: 6px;
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
    }

    .login-container .signup-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: var(--noir-color);
    }

    .login-container .signup-link a {
        color: var(--button-color);
        text-decoration: none;
        font-weight: 500;
    }

    .login-container .signup-link a:hover {
        text-decoration: underline;
    }

    /* Password field with toggle */
    .password-field {
        position: relative;
    }

    .password-field input {
        width: 100%;
        padding: 12px 15px;
        padding-right: 40px; /* Make room for the eye icon */
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #777;
        font-size: 16px;
        z-index: 10;
    }

    .password-toggle:hover {
        color: var(--noir-color);
    }
    
    .far {
        padding-top: 25px;
    }

    @media screen and (max-width: 1400px) {
        .login-container,
        .signup-container {
            width: 70%;
            max-width: unset;
        }
    }

    @media screen and (max-width: 1100px) {
        .login-container,
        .signup-container {
            width: 90%;
        }
    }

    @media screen and (max-width: 900px) {
        #img {
            display: none;
        }    

        #loginContainer, #signupContainer, #forgotPasswordContainer, #confirmationContainer {
            width: 100%;
        }

        .login-container, .signup-container, .forgotPassword-container, .confirmation-container {
            width: 75%;
            max-width: unset;
        }

        .login-container input:focus {
            box-shadow: none;
        }

        #container {
            background-color: white !important;
        }
    }
    
    @media screen and (max-width: 768px) {
        header {
            display: none;
        }
    }

    @media screen and (max-width: 750px) {
        .login-container, .signup-container, .forgotPassword-container, .confirmation-container {
            width: 90%;
        }
    }

    @media screen and (max-width: 470px) {
        .login-container, .signup-container, .forgotPassword-container, .confirmation-container {
            width: 100%;
        }

        .signup-container h1 {
            margin-bottom: 10px !important;
        }

        .form-row {
            margin: 0 !important;
        }

        .login-link {
            margin-top: 0.5rem !important;
        }

        .signup-container input[type="submit"] {
            margin-top: 0.5rem !important;
        }

        .confirm-password-field {
            margin-top: 0!important;
        }
    }

    @media (max-width: 600px) {
        .login-container, .login-card {
            padding: 16px 8px !important;
            margin: 0 auto !important;
            border-radius: 10px !important;
            width: 98vw !important;
            min-width: unset !important;
        }

        .login-title {
            font-size: 1.3rem !important;
            margin-bottom: 18px !important;
        }
        
        .login-form input[type="text"],
        .login-form input[type="password"] {
            font-size: 1rem !important;
            padding: 10px 12px !important;
            width: 100% !important;
            margin-bottom: 12px !important;
        }
        
        .login-form button {
            font-size: 1rem !important;
            padding: 10px 0 !important;
            width: 100% !important;
            margin-top: 10px !important;
        }
        
        .login-logo {
            width: 60px !important;
            height: 60px !important;
            margin-bottom: 10px !important;
        }
        
        .login-links {
            font-size: 0.95rem !important;
            margin-top: 10px !important;
        }
    }
</style>