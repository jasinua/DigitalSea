<style>
    .page-wrapper {
        width: 100%;
        color: var(--page-text-color);
        font-family: 'Roboto', sans-serif;
    }
    
    h2 {
        margin: 40px 0 30px 0;
        font-size: 2.2rem;
        text-align: center;
        color: var(--page-text-color);
    }
    
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
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
        font-size: 0.9rem;
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
    
    :root {
        --success-color: #00c853;
        --border-color: #ddd;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 480px) {
        .container {
            padding: 10px;
        }
        
        .confirmation-box {
            padding: 20px;
        }
    }
</style>