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

    @media (max-width: 600px) {
        .modal-content {
            padding: 10px 2vw 10px 2vw;
            max-width: 98vw;
        }
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
        border-bottom: 1px solid var(--mist-color);
        text-align: left;
        font-size: 0.97rem;
    }

    th {
        background-color: var(--button-color);
        color: var(--text-color);
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
        border-radius: 6px;
        margin: auto;
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
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
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
</style>