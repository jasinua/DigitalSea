<style>
    #container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: calc(100vh - 120px);
        background-color: var(--ivory-color);
        padding: 15px;
    }

    #prodContainer {
        margin: 0;
        width: 1400px;
        min-height: 450px;
        background-color: white;
        border-radius: 12px;
        display: flex;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        color: var(--page-text-color);
        position: relative;
    }

    #productImg {
        width: 100%;
        height: 450px;
        object-fit: contain;
        padding: 15px;
        background-color: white;
        border-right: 1px solid #eee;
    }

    #info {
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    #name {
        width: 98%;
        padding: 15px 20px 10px;
        font-size: 1.6em;
        font-weight: 600;
        color: var(--noir-color);
        border-bottom: 1px solid #eee;
    }

    #details {
        width: 100%;
        padding: 12px 20px;
        border-bottom: 1px solid #eee;
    }

    .detail {
        font-size: 0.95em;
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        color: #555;
    }

    .detail p:first-child {
        font-weight: 500;
        color: #333;
    }

    #infoSide {
        width: 50%;
        display: flex;
        flex-direction: column;
        padding: 0;
    }

    #buyForm {
        margin: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        padding: 15px 20px;
    }

    #stock {
        width: 45px;
        height: 100%;
        font-size: 15px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 3px;
        background-color: white;
    }

    #buy {
        height: 40px;
        border-radius: 6px;
        margin-top: 12px;
        background-color: var(--button-color);
        color: white;
        border: none;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s;
        cursor: pointer;
        width: 100%;
    }

    #buy:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    #stockWrapper {
        height: 35px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    #controlStock {
        display: flex;
        width: auto;
        height: 100%;
        align-items: center;
        gap: 6px;
    }

    #controlStock button {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        font-size: 15px;
        background-color: var(--button-color);
        color: white;
        border: none;
        transition: all 0.2s;
        cursor: pointer;
    }

    #controlStock button:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .price-section {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #eee;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .price-label {
        font-size: 1em;
        font-weight: 500;
        color: var(--noir-color);
    }

    .price-value {
        font-size: 1.2em;
        font-weight: 600;
        color: var(--noir-color);
    }

    .wishlist-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        z-index: 2;
        padding: 6px;
        transition: transform 0.2s;
    }
    
    .wishlist-btn:hover {
        transform: scale(1.1);
    }
    
    .wishlist-btn i {
        font-size: 20px;
        color: #ccc;
        transition: color 0.2s;
    }
    
    .wishlist-btn.active i {
        color: var(--error-color);
    }

    .discount-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background-color: var(--error-color);
        color: white;
        padding: 7px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }

    #original-price {
        color: var(--error-color);
        text-decoration: line-through;
        font-size: 14px;
        font-weight: normal;
    }

    #discounted-price {
        color: var(--noir-color);
        font-weight: 600;
        font-size: 1.2em;
    }

    @media (max-width: 1400px) {
        #prodContainer {
            width: 95%;
        }
    }

    @media (max-width: 1230px) {
        #prodContainer {
            width: 95%;
        }
        
        #infoSide {
            padding: 0;
        }
    }
    
    @media (max-width: 1100px) {
        #prodContainer {
            flex-direction: column;
            max-width: 800px;
        }

        #productImg {
            width: 100%;
            height: 400px;
            border-right: none;
            border-bottom: 1px solid #eee;
        }

        #infoSide {
            width: 100%;
        }
        
        #info {
            width: 100%;
        }
        
        div[style*="width:50%"] {
            width: 100% !important;
        }
        
        /* Reposition the discount badge and wishlist button */
        .discount-badge {
            top: 20px;
            left: 20px;
            right: auto;
        }
        
        .wishlist-btn {
            top: 20px;
            right: 20px;
            z-index: 5;
        }
    }

    @media (max-width: 768px) {
        #prodContainer {
            max-width: 600px;
        }
        
        #name {
            font-size: 1.4em;
            padding: 12px 15px 8px;
        }
        
        #details {
            padding: 10px 15px;
        }
        
        .detail {
            font-size: 0.9em;
        }
        
        #productImg {
            height: 350px;
        }
        
        #buyForm {
            padding: 12px 15px;
        }
    }

    @media (max-width: 480px) {
        #prodContainer {
            margin: 0;
            border-radius: 0;
            width: 100%;
        }
        
        #container {
            padding: 0;
        }
        
        #productImg {
            height: 300px;
        }
        
        .price-label, .price-value {
            font-size: 0.95em;
        }
    }
</style>