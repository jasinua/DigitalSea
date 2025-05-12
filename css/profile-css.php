<style>
    .profile-container {
        min-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .profile-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .profile-content {
        padding: 40px;
    }

    .profile-section {
        margin-bottom: 40px;
    }

    .profile-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 1.5rem;
        color: #153147;
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .profile-info-container {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 40px;
        align-items: start;
        margin-bottom: 40px;
    }

    .profile-avatar {
        width: 200px;
        height: 200px;
        background: rgba(21, 49, 71, 0.1);
    border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(21, 49, 71, 0.2);
    }

    .profile-avatar i {
        font-size: 80px;
        color: #153147;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .info-item {
    display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-radius: 12px;
    }

    .info-item i {
        font-size: 1.2rem;
        color: #153147;
        width: 24px;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
    }

    .info-value {
        color: #333;
    }

    .forms-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-weight: 600;   
        color: #153147;
    }

    .form-group input {
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        border-color: #153147;
        box-shadow: 0 0 0 3px rgba(21, 49, 71, 0.1);
        outline: none;
    }

    .password-section {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
    }

    .button-group {
    display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 1rem;
    }

    .btn-primary {
        background: var(--button-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--button-color-hover);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(21, 49, 71, 0.2);
    }

    .btn-secondary {
        background: var(--logout-color);
        color: var(--text-color);
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: var(--logout-color-hover);
        transform: translateY(-2px);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 1100px) {
        .profile-info-container {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 20px;
        }

        .profile-container {
            min-width: 90%;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }

        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .forms-container {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }

    @media (max-width: 768px) {
        .profile-avatar {
            width: 120px;
            height: 120px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .profile-content {
            padding: 20px;
        }

        .button-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 560px) {
        .info-grid {
            display: none;
        }
    }
</style>