<style>
    /* Header search bar fix */
    header input[type="text"], .header-search input[type="text"] {
        width: 300px !important;
        max-width: 90vw;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1.5px solid var(--mist-color);
        font-size: 1rem;
        background: #fff;
        color: var(--noir-color);
        margin: 0 auto;
        display: block;
        box-sizing: border-box;
    }

    header .header-search {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin: 0 auto;
    }

    .body-container {
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        padding: 32px 24px;
    }

    h1, h2 {
        margin-top: 20px;
        text-align: center;
        color: var(--noir-color);
    }

    /* Modal styles */
    #modal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
        overflow: auto;
    }

    .modal-content {
        background: var(--background-color);
        padding: 20px 18px 18px 18px;
        border-radius: 18px;
        width: 100%;
        max-width: 600px;
        margin: 60px auto 30px auto;
        position: relative;
        box-shadow: 0 12px 40px rgba(21,49,71,0.18), 0 2px 8px rgba(44,62,80,0.08);
        border: 1.5px solid #e9ecef;
        animation: modal-slide-in 0.4s cubic-bezier(.4,1.4,.6,1);
        overflow: visible;
    }

    @keyframes modal-slide-in {
        from { opacity: 0; transform: translateY(-40px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .close-btn {
        position: absolute;
        top: 12px;
        right: 16px;
        font-size: 20px;
        border: none;
        background: none;
        cursor: pointer;
        color: #888;
        transition: color 0.2s;
        z-index: 2;
    }

    .close-btn:hover {
        color: var(--text-color);
    }

    .modal-content h2, .modal-content h3 {
        text-align: center;
        color: #153147;
        margin-top: 12px;
        margin-bottom: 24px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .modal-content label {
        font-weight: 600;
        color: #2c3e50;
        margin-top: 12px;
        margin-bottom: 4px;
        display: block;
    }

    .modal-content input[type="text"],
    .modal-content input[type="number"],
    .modal-content input[type="url"],
    .modal-content textarea {
        width: 100%;
        padding: 7px 10px;
        margin-bottom: 10px;
        border-radius: 6px;
        border: 1.2px solid #d1d8e0;
        background-color: #f8f8f8;
        color: #2c3e50;
        font-size: 0.93rem;
        transition: border 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    .modal-content input:focus,
    .modal-content textarea:focus {
        border-color: #153147;
        box-shadow: 0 0 0 2px rgba(21,49,71,0.10);
        outline: none;
    }

    .modal-content .btn, .modal-content input[type="submit"] {
        color: #fff;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        margin-top: 16px;
        margin-bottom: 0;
        transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
        box-shadow: 0 2px 8px rgba(21,49,71,0.10);
        display: block;
        width: 100%;
    }

    .modal-content .btn:hover, .modal-content input[type="submit"]:hover {
        transform: translateY(-2px);
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
        color: var(--noir-color);
    }

    input[type="text"],
    input[type="number"],
    input[type="url"],
    textarea {
        width: 100%;
        padding: 12px 16px;
        margin-top: 5px;
        margin-bottom: 14px;
        border-radius: 10px;
        border: 1.5px solid var(--mist-color);
        background-color: var(--ivory-color);
        color: var(--noir-color);
        font-size: 1rem;
        transition: border 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="url"]:focus,
    textarea:focus {
        border-color: var(--button-color);
        box-shadow: 0 0 0 2px rgba(21,49,71,0.08);
        outline: none;
    }

    button, .btn, input[type="submit"] {
        background-color: var(--button-color);
        color: var(--text-color);
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        margin-top: 10px;
        transition: background 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(21,49,71,0.06);
    }

    button:hover, .btn:hover, input[type="submit"]:hover {
        background-color: var(--button-color-hover);
    }

    table {
        width: 100%;
        max-width: 1200px;
        margin: 30px auto;
        border-collapse: collapse;
        background: var(--modal-bg-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border-radius: 15px;
        overflow: hidden;
    }

    th, td {
        color: var(--page-text-color);
        padding: 8px;
        text-align: center;
        font-size: 0.97rem;
    }

    tr {
        border-bottom: 2px solid #eee;
    }

    th {
        background-color: var(--button-color);
        color: var(--text-color);
        padding: 20px;
        font-size: 1.05rem;
    }

    tr:hover {
        background-color: var(--almond-color);
    }

    .details-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .details-list li {
        margin-bottom: 5px;
    }

    .link-button {
        color: var(--button-color);
        text-decoration: none;
    }

    .link-button:hover {
        text-decoration: underline;
    }

    .search-add {
        display: flex;
        justify-content: space-center;
        align-items: center;
        margin: 20px auto;
        width: 95%;
    }

    .search-add input[type="text"] {
        width: 60%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid var(--mist-color);
        border-radius: 12px;
        margin: auto;
    }

    .search-add input[type="text"]:focus {
        border: 2px solid var(--button-color);
        /* box-shadow: var(--shadow-input); */
        outline: none;
    }

    .search-add .btn {
        width: 30%;
        padding: 12px;
        margin:auto;
    }

    #detailsContainer div {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    #detailsContainer input[type="text"] {
        flex: 1;
        padding: 8px;
        margin-right: 10px;
        border-radius: 6px;
        border: 1px solid var(--mist-color);
        background-color: var(--ivory-color);
        color: var(--noir-color);
    }

    #detailsContainer button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    #detailsContainer button img {
        width: 20px;
        height: 20px;
    }

    .detail-button {
        background-color:var(--button-color);
    }
    
    .detail-button:hover {
        background-color:var(--button-color-hover);
    }

    .div {
        display:flex;
        justify-content:right;
    }

    .product-img {
        width: 80px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    .notification {
        position: fixed;
        top: 60px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .notification.success {
        background-color: #28a745;
    }

    .notification.error {
        background-color: #dc3545;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

     /* Add these styles to your existing CSS */
     .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: var(--button-color);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            opacity: 0;
            transform: translateY(20px);
        }

        .scroll-to-top:hover {
            background-color: var(--button-color-hover);
            transform: translateY(-5px);
        }

        .scroll-to-top.visible {
            display: flex;
            opacity: 1;
            transform: translateY(0);
        }

        .notification {
            position: fixed;
            top: 60px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        .notification.success {
            background-color: #28a745;
        }

        .notification.error {
            background-color: #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 900px) {
            .body-container {
                width: 98%;
                padding: 12px 4px;
            }
            table, th, td {
                font-size: 0.95rem;
            }
        }

        /* Responsive styles for small screens */
        @media (max-width: 767px) {
            .body-container {
                width: 99vw;
                max-width: 100vw;
                margin: 10px 0;
                padding: 8px 2vw;
                border-radius: 0;
                box-shadow: none;
            }
            h1, h2 {
                font-size: 1.3rem;
                margin-top: 10px;
            }
            table {
                width: 100vw;
                max-width: 100vw;
                margin: 10px 0;
                border-radius: 0;
                box-shadow: none;
                font-size: 0.92rem;
                overflow-x: auto;
                display: block;
            }
            th, td {
                padding: 6px 2px;
                font-size: 0.92rem;
                word-break: break-word;
            }
            .product-img {
                width: 48px;
                min-width: 48px;
                max-width: 48px;
            }
            .modal-content {
                max-width: 98vw;
                margin: 20px auto;
                padding: 8px 2vw;
            }
            .search-add {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }
            .search-add input[type="text"], .search-add .btn {
                width: 100%;
                margin: 0;
            }
            .scroll-to-top {
                right: 10px;
                bottom: 10px;
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            .notification {
                top: 10px;
                right: 5vw;
                left: 5vw;
                width: 90vw;
                padding: 10px 5vw;
                font-size: 0.98rem;
                border-radius: 6px;
                text-align: center;
            }
        }

        @media (max-width: 600px) {
            .modal-content {
                padding: 10px 2vw 10px 2vw;
                max-width: 98vw;
            }
        }

        /* Optional: Make table horizontally scrollable on very small screens */
        @media (max-width: 500px) {
            table, .modal-content {
                font-size: 0.88rem;
            }
            th, td {
                font-size: 0.88rem;
                padding: 4px 1px;
            }
        }
</style>