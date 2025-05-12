<style>
    .page-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: calc(100vh - 120px);
        background-color: var(--ivory-color);
        padding: 20px;
    }

    .cart-wrapper {
        display: flex;
        width: 100%;
        max-width: 1400px;
        gap: 20px;
        margin: auto;
    }

    .cart-left, .cart-right {
        background-color: white;
        color: var(--page-text-color);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 20px;
        min-height: 400px;
    }

    .cart-left {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex: 1 1 65%;
        max-height: 600px;
    }

    .itemsTable {
        max-height: 500px;
        overflow-y: auto;
        margin-bottom: 20px;
    }

    .cart-right {
        display: flex;
        flex-direction: column;
        flex: 1 1 35%;
        max-height: 600px;
    }

    .cart-right h3 {
        padding: 10px 0;
        margin: 0;
        font-size: 1.2em;
        color: var(--noir-color);
        background-color: var(--ivory-color);
        border-bottom: 1px solid #eee;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    thead tr {
        background-color: var(--ivory-color);
    }

    th, td {
        padding: 12px 15px;
        color: var(--page-text-color);
        text-align: center;
        vertical-align: middle;
    }

    th {
        font-weight: 500;
        color: var(--noir-color);
    }

    th:nth-child(1) { width: 40%; }
    th:nth-child(2) { width: 20%; }
    th:nth-child(3) { width: 20%; }
    th:nth-child(4) { width: 20%; }

    td:first-child {
        text-align: left;
    }

    tr{
        border-bottom: 1px solid #eee;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
    }

    .product-info img {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border-radius: 8px;
        background-color: white;
        padding: 5px;
        flex-shrink: 0;
    }

    .product-info > div {
        flex: 1;
        min-width: 0;
    }

    .product-info h4 {
        margin: 0;
        font-size: 0.95rem;
        color: var(--noir-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-info .desc {
        font-size: 0.85rem;
        color: #666;
        margin-top: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .price-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .price-info .original-price {
        color: red;
        font-size: 0.85rem;
        text-decoration: line-through;
    }

    .price-info .discounted-price {
        font-weight: 500;
        color: var(--noir-color);
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-right: 40px;
    }

    .quantity-controls input {
        width: 45px;
        height: 32px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        background-color: white;
    }

    .remove-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #888;
        cursor: pointer;
        transition: color 0.2s;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: auto;
        margin-right: 50px;
    }

    .remove-btn:hover {
        color: #ff4444;
    }

    td{
        justify-content: center;
        align-items: center;
        display: flex;
    }

    tr{
        display: flex;
        justify-content: space-between;
    }

    .summary-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        flex: 1; 
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 0.95rem;
        color: #555;
    }

    .summary-item.total {
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--noir-color);
        border-top: 1px solid #eee;
        padding-top: 15px;
        margin-top: 5px;
    }

    .emri-me-zbritje {
        display: flex; 
        justify-content: space-between;
    }

    .zbritja {
        margin-left: 20px;
    }

    .me-zbritje {
        display: flex;
        flex-direction: column;
    }

    .checkout-btn {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        margin-top: auto;
        transition: all 0.3s;
    }

    .checkout-btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    #prodNameXprice {
        overflow-y: auto;
        max-height: 240px;
        border-bottom: 2px solid #eee;
    }

    @media (max-width: 1200px) {
        .cart-wrapper {
            flex-direction: column;
            max-width: 800px;
        }

        .cart-left, .cart-right {
            width: 100%;
            max-height: none;
        }

        .itemsTable {
            max-height: 400px;
        }
    }

    @media (max-width: 850px) {
        .cart-wrapper {
            max-width: 95%;
        }
        
        .page-wrapper {
            padding: 10px;
        }
        
        .cart-left, .cart-right {
            padding: 15px;
        }
    }
    
    @media (max-width: 768px) {
        .page-wrapper {
            padding: 10px;
        }
        
        th, td {
            padding: 8px 10px;
        }
        
        .product-info img {
            width: 60px;
            height: 60px;
        }
        
        .product-info h4 {
            font-size: 0.85rem;
        }
        
        .product-info .desc {
            font-size: 0.8rem;
        }
        
        .quantity-controls input {
            width: 40px;
            height: 28px;
            font-size: 0.85rem;
        }
        
        .summary-item {
            font-size: 0.9rem;
        }
        
        .summary-item.total {
            font-size: 1rem;
        }
        
        .save-btn, .checkout-btn {
            padding: 10px;
            font-size: 0.95rem;
        }
    }
    
    @media (max-width: 576px) {
        .page-wrapper {
            padding: 5px;
        }
        
        thead tr {
            display: none;
        }
        
        tbody tr {
            flex-direction: column;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        td {
            width: 100%;
            justify-content: space-between;
            padding: 5px 10px;
            border-bottom: none;
        }
        
        .product-info {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .quantity-controls {
            margin-right: 0;
        }
        
        .remove-btn {
            margin-right: 0;
            align-self: flex-end;
        }
        
        td:nth-child(3), td:nth-child(4) {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
        
        td:nth-child(3)::before {
            content: "Quantity:";
            font-weight: 500;
            color: var(--noir-color);
        }
        
        td:nth-child(4)::before {
            content: "Remove:";
            font-weight: 500;
            color: var(--noir-color);
        }
        
        .cart-left, .cart-right {
            padding: 12px;
            border-radius: 8px;
        }
        
        .itemsTable {
            max-height: none;
            overflow-y: visible;
        }
    }

    @media (max-width: 480px) {
        .product-info {
            gap: 10px;
        }
        
        .product-info img {
            width: 50px;
            height: 50px;
        }
        
        .save-btn, .checkout-btn {
            padding: 10px;
            font-size: 0.9rem;
        }
        
        .summary-item.total {
            font-size: 0.95rem;
        }
    }

    .save-btn {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 20px;
        transition: all 0.3s;
    }

    .save-btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .save-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .save-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        background-color: #4CAF50;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-100%);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .save-message.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>