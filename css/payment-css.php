<style>
    .page-wrapper {
        width: 100%;
        color: var(--page-text-color);
    }
    
    h2 {
        margin: 20px 0 30px 0;
        font-size: 2.2rem;
        text-align: center;
        color: var(--page-text-color);
    }
    
    .container {
        max-width: 100%;
        padding: 20px;
        font-family: 'Roboto', sans-serif;
    }

    .payment-part {
        margin-top: 40px;
        width: 100%;
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 50px;
    }
    
    .payment-summary {
        width: 500px;
        background-color: var(--ivory-color);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .payment-summary h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: var(--page-text-color);
        font-size: 1.5rem;
    }

    .payment-main {
        width: 600px;
        border-radius: var(--border-radius);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .payment-main.crypto {
        width: 600px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .payment-main.crypto.after {
        width: 1000px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .payment-crypto {
        width: 100%;
        background-color: var(--ivory-color);
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 1rem;
    }
    
    .summary-item.total {
        border-top: 1px solid #ddd;
        margin-top: 15px;
        padding-top: 15px;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .payment-form {
        background-color: white;
        padding: 30px;
    }
    
    .form-row {
        margin-bottom: 20px;
    }
    
    .form-row label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--page-text-color);
    }
    
    .form-row input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        background-color: var(--ivory-color);
    }
    
    #card-element {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: white;
    }
    
    #payment-form button {
        display: block;
        width: 100%;
        padding: 15px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 20px;
    }
    
    #payment-form button:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }
    
    #payment-form button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    #card-errors {
        color: #e53935;
        margin-top: 10px;
        font-size: 0.9rem;
    }

    .magnetic-strip {
        width: 100%;
        height: 40px;
        background: #2d3748;
        margin-bottom: 1rem;
    }
    
    /* Responsive styles */
    @media screen and (max-width: 768px) {
        .container {
            max-width: 95%;
            padding: 15px;
        }
        
        h2 {
            font-size: 1.8rem;
        }
        
        .payment-form {
            padding: 20px;
        }
        
        .card-details {
            font-size: 0.9rem;
        }
        
        .card-cvc {
            font-size: 1.2rem;
        }
        
        .card-chip {
            width: 50px;
            height: 35px;
        }
        
        h2 {
            font-size: 1.6rem;
            /* margin: 25px 0 20px 0; */
        }
    }
    
    @media screen and (max-width: 480px) {
        h2 {
            font-size: 1.5rem;
        }
        
        .payment-form {
            padding: 15px;
        }
        
        .form-row label {
            font-size: 0.9rem;
        }
        
        .form-row input {
            padding: 10px;
        }
        
        #payment-form button {
            padding: 12px;
            font-size: 1rem;
        }
        
        .card {
            height: 200px;
        }
        
        .card-number {
            font-size: 1.3rem;
        }
    }
    
    @media screen and (max-width: 400px) {
        .card-container {
            min-width: unset;
        }
        
        .card {
            height: 180px;
        }
        
        .card-front, .card-back {
            padding: 15px;
        }
        
        .card-number {
            font-size: 1.1rem;
        }
    }

    .pay-with {
        width: 40%;
        margin: 10px auto;
        background-color: var(--button-color);
        /* padding: 20px; */
        border-radius: 10px;
        display: flex;
        overflow: hidden;
    }

    .payment-something {
        width: 50%;
        text-align: center;
        padding: 10px;
        font-size: 1.5rem;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-something.active {
        width: 50%;
        text-align: center;
        padding: 10px;
        font-size: 1.5rem;
        color: #fff;
        /* border-radius: 10px; */
        background-color: var(--button-color-hover);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    :root {
        --success-
        /* background-color: #4a5568; */color: #00c853;
        --warning-color: #ff9100;
        --error-color: #ff5252;
        --border-color: #ddd;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .payment-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    .payment-container.after {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    .payment-header {
        padding: 10px;
        text-align: center;
        background: var(--noir-color);
        color: white;
    }

    .payment-header h1 {
        font-weight: 500;
        margin: 8px;
    }

    .payment-header p {
        opacity: 0.9;
        font-weight: 300;
        margin: 8px;
    }

    .payment-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-weight: 500;
        color: var(--page-text-color);
    }

    .form-group input,
    .form-group textarea {
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 16px;
        transition: border 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--navy-color);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .btn {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 14px;
        border-radius: var(--border-radius);
        font-size: 1.2rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn:hover {
        background-color: var(--button-color-hover);
    }

    .alert {
        padding: 12px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    .alert.error {
        background-color: #ffebee;
        color: var(--error-color);
        border-left: 4px solid var(--error-color);
    }

    .payment-details {
        background-color: white;
        display: flex;
        flex-direction: row;
        gap: 10px;
    }

    .payment-summary {
        background: white;
        height: fit-content;
        padding: 10px;
        align-self: center;
        border-radius: var(--border-radius);
    }

    .payment-after.crypto {
        width: 500px;
        padding: 10px;
        border-radius: var(--border-radius);
        box-shadow: none;
    }

    .payment-summary h2 {
        margin-bottom: 16px;
        font-weight: 500;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 500;
        color: var(--page-text-color);
    }

    .detail-value {
        color: var(--page-text-color);
    }

    .status-new {
        color: var(--warning-color);
    }

    .status-pending {
        color: var(--warning-color);
    }

    .status-completed {
        color: var(--success-color);
    }

    .status-failed {
        color: var(--error-color);
    }

    .coinbase-button {
        display: block;
        text-align: center;
        margin: 20px 0;
    }

    .coinbase-button img {
        max-width: 200px;
    }

    .small-text {
        font-size: 14px;
        color: var(--page-text-color);
        text-align: center;
    }

    .payment-alternatives {
        text-align: center;
        margin-top: 20px;
    }

    .qr-code {
        width: 220px;
        height: 220px;
        margin: 20px auto;
        padding: 10px;
        background: white;
        border-radius: var(--border-radius);
        display: inline-block;
        border: 1px solid var(--border-color);
    }

    .payment-footer {
        padding: 20px;
        text-align: center;
        color: var(--page-text-color);
        font-size: 14px;
        border-top: 1px solid var(--border-color);
    }

    @media (max-width: 480px) {
        .payment-header {
            padding: 20px;
        }
        
        .payment-main {
            padding: 20px;
        }
        
        .detail-row {
            flex-direction: column;
            gap: 4px;
        }
    }

    #crypto-payment {
        margin-bottom:10px;
    }
</style>