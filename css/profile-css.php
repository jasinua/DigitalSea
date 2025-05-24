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

    .profile-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 1.5rem;
        color: #153147;
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
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

    .form-group-address {
        grid-column: 1 / 3;
        grid-row: 2;
    }

    .form-group label {
        font-weight: 600;   
        color: #153147;
    }

    .form-group input {
        padding: 12px 15px;
        border: 2px solid #eee;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        border-color: var(--noir-color);
        box-shadow: var(--shadow-input);
        outline: none;
    }

    .password-field {
        position: relative;
        width: 100%;
    }

    .password-field input {
        width: 100%;
        padding-right: 40px;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        font-size: 1.1rem;
    }

    .password-toggle:hover {
        color: #153147;
    }

    .password-section {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
    }

    .button-group {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 30px;
    }

    .button-group.small-media {
        display: none;
    }

    .btn {
        padding: 12px 25px;
        border-radius: 8px;
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
        height:43px;
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
        margin-top: 20px;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        margin-top: 20px;
    }

    .orders-section {
        margin-top: 2rem;
    }

    .orders-table {
        width: 100%;
        overflow-x: auto;
    }

    .orders-table table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .orders-table th,
    .orders-table td {
        padding: 1rem;
        color: var(--page-text-color)
    }

    .orders-table tr {
        border-bottom: 2px solid #eee;
    }

    .orders-table tr:last-child {
        border-bottom: none;
    }

    .orders-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: var(--page-text-color)
    }

    .orders-table tr:hover {
        background-color: #f8f9fa;
    }

    .orders-table .btn-secondary {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .orders-table .btn-secondary i {
        margin-right: 0.5rem;
    }

    .no-orders {
        text-align: center;
        color: #666;
        padding: 2rem;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .orders-table {
            font-size: 0.9rem;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 0.75rem;
        }
        
        .orders-table .btn-secondary {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }
    }

    .invoice-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .invoice-btn:hover {
        background-color: var(--navy-color);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .invoice-btn i {
        font-size: 1.1rem;
    }

    .invoice-btn:active {
        transform: translateY(0);
        box-shadow: none;
    }

    @media (max-width: 768px) {
        .invoice-btn {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .invoice-btn i {
            font-size: 1rem;
        }
    }

    .order-history-btn {
        background-color: var(--button-color);
        border: 2px solid var(--button-color);
        color: white;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 1rem;
        height:43px;

    }

    .order-history-btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
        color: white;
    }

    .order-history-btn.active {
        background-color: white;
        color: var(--button-color);
        border: 2px solid var(--button-color);
    }

    .order-history-btn.active:hover {
        background-color: var(--button-color);
        border: none;
        transform: translateY(-2px);
        color: white;
    }

    #orderHistoryContent {
        margin-top: 20px;
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

        .button-group.big-media {
            display: none;
        }

        .button-group.small-media {
            display: flex;
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

    @media (max-width: 576px) {
        .profile-container {
            min-width: 100%;
        }

        .profile-info-container {
            margin-bottom: 0;
        }

        .section-title {
            border-bottom: none;
        }

        .profile-section .profile-info-section .section-title {
            margin-bottom: 0;
        }
        
        .info-grid {
            display: none;
        }
    }

    /* Default styles (desktop) */
    .profile-form input,
    .profile-form select {
        font-size: 1rem;
        padding: 12px 16px;
    }

    .profile-form button {
        font-size: 1rem;
        padding: 12px 24px;
    }

    .profile-icon {
        width: 100px;
        height: 100px;
    }

    /* Tablet */
    @media (max-width: 900px) {
        .profile-form input,
        .profile-form select {
            font-size: 1rem;
            padding: 10px 14px;
        }
        .profile-form button {
            font-size: 1rem;
            padding: 10px 18px;
        }
        .profile-icon {
            width: 70px;
            height: 70px;
        }
    }

    /* Mobile */
    @media (max-width: 600px) {
        .profile-form input,
        .profile-form select {
            font-size: 0.9rem;
            padding: 8px 10px;
        }
        .profile-form button {
            font-size: 0.9rem;
            padding: 8px 12px;
        }
        .profile-icon {
            width: 45px;
            height: 45px;
        }

        .form-grid {
            grid-template-columns: 1fr;
            /* gap: 20px; */
        }

        .form-group-address {
            grid-column: 1;
            grid-row: 3;
        }
    }
</style>