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

    .password-field {
        position: relative;
        width: 100%;
        margin-bottom: 5px;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 22px;
        cursor: pointer;
        color: #777;
        font-size: 16px;
        z-index: 10;
    }

    .password-toggle:hover {
        color: var(--noir-color);
    }

    .confirm-password-field {
        position: relative;
        width: 100%;
    }

    .confirm-password-toggle {
        position: absolute;
        right: 12px;
        top: 22px; /* Same positioning as the password field */
        cursor: pointer;
        color: #777;
        font-size: 16px;
        z-index: 10;
    }

    .confirm-password-toggle:hover {
        color: var(--noir-color);
    }
</style>