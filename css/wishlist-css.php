<style>
    .wishlist-container {
        min-width: 1100px;
        margin: 50px auto;
        background-color: var(--modal-bg-color);
        color: var(--page-text-color);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    /* Fix for cart preview hover functionality */
    .cart-link:hover .cart-preview {
        display: block !important;
    }
    
    /* Force display when hovering directly on preview */
    .cart-preview:hover {
        display: block !important;
    }

    .wishlist-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--mist-color);
    }

    .wishlist-header h2 {
        font-size: 28px;
        color: var(--button-color);
        margin: 0;
        cursor: default;
    }

    .wishlist-count {
        background-color: var(--button-color);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        cursor: default;
    }

    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(320px, 1fr));
        gap: 25px;
        padding: 10px;
    }

    @media (max-width: 1400px) {
        .wishlist-grid {
            grid-template-columns: repeat(3, minmax(300px, 1fr));
        }
    }
    @media (max-width: 1000px) {
        .wishlist-grid {
            grid-template-columns: repeat(4, minmax(300px, 1fr));
            gap: 25px;
        }
    }

    /* Small Desktops and Large Tablets (1024px to 1439px) */
    @media screen and (max-width: 1439px) {
        .wishlist-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 25px;
        }

        .wishlist-grid {
            grid-template-columns: repeat(3, minmax(280px, 1fr));
            gap: 20px;
        }

        .wishlist-header h2 {
            font-size: 24px;
        }
    }

    /* Tablets (768px to 1023px) */
    @media screen and (max-width: 1023px) {
        .wishlist-container {
            max-width: 100%;
            margin: 30px 20px;
            padding: 20px;
            min-width: unset;
        }

        .wishlist-grid {
            grid-template-columns: repeat(2, minmax(250px, 1fr));
            gap: 15px;
        }

        .wishlist-header {
            gap: 15px;
            text-align: center;
        }

        .wishlist-header h2 {
            font-size: 22px;
        }
    }

    /* Large Phones (480px to 767px) */
    @media screen and (max-width: 767px) {
        .wishlist-container {
            margin: 20px 15px;
            padding: 15px;
        }

        .wishlist-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .wishlist-item {
            max-width: none;
        }

        .product-info img {
            width: 120px;
            height: 120px;
        }

        .product-info .name {
            font-size: 15px;
        }

        .product-price {
            font-size: 16px;
        }

        .add-to-cart-btn {
            padding: 10px;
            font-size: 14px;
        }
    }

    /* Small Phones (up to 479px) */
    @media screen and (max-width: 479px) {
        .wishlist-container {
            margin: 15px 10px;
            padding: 12px;
        }

        .wishlist-header h2 {
            font-size: 20px;
        }

        .wishlist-count {
            font-size: 12px;
            padding: 6px 12px;
        }

        .product-info img {
            width: 100px;
            height: 100px;
        }

        .product-info .name {
            font-size: 14px;
        }

        .product-price {
            font-size: 15px;
        }

        .original-price {
            font-size: 12px;
        }

        .stock-status {
            font-size: 12px;
            padding: 4px 8px;
        }

        .add-to-cart-btn {
            padding: 8px;
            font-size: 13px;
        }
    }

    /* Touch Device Optimizations */
    @media (hover: none) {
        .wishlist-item:hover {
            transform: none;
        }

        .product-info img:hover {
            transform: none;
        }

        .add-to-cart-btn:hover {
            transform: none;
        }

        .add-to-cart-btn:active {
            background-color: var(--button-color-hover);
            transform: scale(0.98);
        }
    }

    /* Reduced Motion Preferences */
    @media (prefers-reduced-motion: reduce) {
        .wishlist-item,
        .product-info img,
        .add-to-cart-btn {
            transition: none;
        }
    }

    /* Safe Area Insets for Modern Mobile Devices */
    @supports (padding: max(0px)) {
        @media screen and (max-width: 767px) {
            .wishlist-container {
                margin-left: max(15px, env(safe-area-inset-left));
                margin-right: max(15px, env(safe-area-inset-right));
                padding-bottom: max(15px, env(safe-area-inset-bottom));
            }
        }
    }

    .wishlist-item {
        background: white;
        border-radius: 12px;
        padding: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        border: 1px solid var(--mist-color);
        max-width: 420px;
    }

    .wishlist-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .remove-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 20px;
        color: var(--mist-color);
        cursor: pointer;
        transition: color 0.3s ease;
        padding: 5px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .remove-btn:hover {
        color: var(--error-color);
        background-color: rgba(249, 64, 64, 0.1);
    }

    .discount-badge {
        position: absolute;
        top: 15px;
        right: 50px;
        background-color: var(--error-color);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1;
    }

    .product-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 20px;
    }

    .product-info img {
        width: 150px;
        height: 150px;
        object-fit: contain;
        border-radius: 10px;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }

    .product-info img:hover {
        transform: scale(1.05);
    }

    .product-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .product-info .name {
        font-size: 16px;
        font-weight: 600;
        color: var(--page-text-color);
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .product-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin: 10px 0;
    }

    .product-price-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .product-price {
        font-size: 18px;
        font-weight: bold;
        color: var(--button-color);
    }

    .original-price {
        text-decoration: line-through;
        color: red;
        font-size: 14px;
    }

    .stock-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 14px;
    }

    .in-stock {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .out-of-stock {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .add-to-cart-btn {
        width: 100%;
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .add-to-cart-btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .empty-wishlist {
        text-align: center;
        padding: 50px 20px;
        color: var(--mist-color);
    }

    .empty-wishlist i {
        font-size: 48px;
        margin-bottom: 20px;
        color: var(--mist-color);
    }

    .empty-wishlist p {
        font-size: 18px;
        margin-bottom: 20px;
    }

    .continue-shopping {
        display: inline-block;
        padding: 12px 25px;
        background-color: var(--button-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .continue-shopping:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .cart-notification {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: var(--error-color);
        border-radius: 50%;
        width: 12px;
        height: 12px;
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.5);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .remove-notification {
        position: fixed;
        top: 30px;
        right: 30px;
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 16px 32px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        z-index: 9999;
        box-shadow: 0 4px 16px rgba(249, 64, 64, 0.15);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .remove-notification.show {
        display: block;
        opacity: 1;
    }
</style>