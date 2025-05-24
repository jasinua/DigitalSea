<style>
    .page-wrapper {
        width: 100%;
        color: var(--page-text-color);
        justify-content: center;
    }
    
    h2 {
        margin: 0 0 30px 0;
        font-size: 2.2rem;
        text-align: center;
        color: var(--page-text-color);
    }
    
    .container {
        max-width: 600px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
    }
    
    .confirmation-box {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 600px;
    }
    
    .success-message {
        color: #2e7d32;
        font-size: 1.2rem;
        margin-bottom: 20px;
    }
    
    .email-notice {
        color: #666;
        margin: 15px 0;
        padding: 10px;
        background-color: white;
        border-radius: 12px;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 24px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s;
        margin: 10px;
    }
    
    .btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .conf-btn-div {
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
    }
    
    @media screen and (max-width: 640px) {
        h2 {
            font-size: 2rem;
        }

        .confirmation-box {
            width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .conf-btn-div {
            flex-direction: column;
            width: fit-content;
        }
    }

    @media screen and (max-width: 530px) {
        .confirmation-box {
            width: 400px;
        }

        h2 {
            font-size: 1.8rem !important;
        }
    }
    
    @media (max-width: 480px) {
        .confirmation-box {
            width: 90%;
            padding: 20px;
        }
    }
</style>