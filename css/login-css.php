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

    .login-container {
        background-color: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 550px;
        margin: 0 auto;
        height: 400px;
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
        border: 1px solid var(--navy-color);
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
        transition: all 0.3s ease;
    }

    .login-container input[type="submit"]:hover {
        background-color: var(--button-color);
        transform: translateY(-2px);
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
</style>