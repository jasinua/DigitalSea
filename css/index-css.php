<style>
    /* Critical CSS for above-the-fold content */
    .page-wrapper { 
        flex: 1; 
        display: flex; 
        flex-direction: column; 

    }

    #container { 
        display: flex; 
        flex: 1; 
        min-height: calc(100vh - 120px); 
        position: relative;
    }

    #container #moreItemsText {
        text-align: center;
        margin: 40px 0 20px 0;
        color: var(--noir-color);
    }

    .filter-toggle-top {
        display: none;
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
        margin: 20px 0;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        max-width: 200px;
    }

    .filter-toggle-top:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    #filters {
        width: 280px;
        background-color: white;
        border-radius: 0;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0; /* Start from header */
        height: 100vh; /* Full viewport height */
        overflow-y: auto;
        flex-shrink: 0;
        align-self: flex-start;
        padding-top: 80px; /* Add padding to account for header */
        padding-bottom: 60px; /* Add padding to account for footer */
    }

    #filterForm{
        margin-top: -50px;
    }

    #filters::-webkit-scrollbar {
        width: 8px;
    }

    #filters::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #filters::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    #filters::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #filter-toggle {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    #filter-toggle:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .filter-section {
        margin-bottom: 24px;
        border-bottom: 1px solid #eee;
        padding-bottom: 16px;
    }
    
    .filter-section:last-child {
        border-bottom: none;
    }
    
    .filter-section h3 {
        color: var(--noir-color);
        margin-bottom: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .filter-section h3::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        transition: transform 0.3s;
    }
    
    .filter-section.collapsed h3::after {
        transform: rotate(-90deg);
    }
    
    .filter-section.collapsed .filter-content {
        display: none;
    }
    
    .filter-content {
        transition: all 0.3s;
    }
    
    .category-dropdown {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: white;
        font-size: 14px;
        color: #555;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .category-dropdown:hover {
        border-color: var(--button-color);
    }
    
    .category-dropdown:focus {
        outline: none;
        border-color: var(--button-color);
        box-shadow: 0 0 0 2px rgba(var(--button-color-rgb), 0.1);
    }
    
    .category-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        background-color: #f5f5f5;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s;
        color: var(--page-text-color);
    }
    
    .category-header:hover {
        background-color: #eee;
    }
    
    .category-header h4 {
        margin: 0;
        font-size: 15px;
        color: var(--noir-color);
    }
    
    .category-header::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        transition: transform 0.3s;
    }
    
    .category-header.collapsed::after {
        transform: rotate(-90deg);
    }
    
    .category-content {
        display: none;
        padding: 10px;
        background-color: white;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .category-content.active {
        display: block;
    }
    
    .subfilter {
        margin: 4px 0;
        padding: 6px;
        display: flex;
        align-items: center;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .subfilter:hover {
        background-color: #f5f5f5;
    }
    
    .subfilter input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        accent-color: var(--button-color);
    }
    
    .subfilter label {
        font-size: 13px;
        color: #555;
        cursor: pointer;
    }
    
    .filter {
        display: flex;
        align-items: center;
        margin: 8px 0;
        padding: 8px;
        border-radius: 6px;
        transition: background-color 0.2s;
    }
    
    .filter:hover {
        background-color: #f5f5f5;
    }
    
    .filter input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        accent-color: var(--button-color);
    }
    
    .filter label {
        font-size: 14px;
        color: #555;
        cursor: pointer;
    }
    
    .price-range {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .price-range input[type="number"] {
        width: 100px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }
    
    #filtOpts {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    #filtOpts input {
        flex: 1;
        padding: 10px;
        border-radius: 6px;
        font-size: 14px;
        background-color: var(--button-color);
        border: none;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    #filtOpts input:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }
    
    #filtOpts input[type="reset"] {
        background-color: #f1f1f1;
        color: #333;
    }
    
    #filtOpts input[type="reset"]:hover {
        background-color: #e1e1e1;
    }
    
    #items {
        background-color: var(--ivory-color);
        flex: 1;
        overflow-y: auto;
        min-height: calc(100vh - 120px);
    }
    
    .itemLine {
        display: flex;
        width: auto;
        margin: 20px 0;
        overflow-x: auto;
        padding-bottom: 20px;
    }
    
    .itemBox {
        align-items: center;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin: 17px;
    }
    
    .item {
        min-width: 225px;
        width: 225px;
        margin: 10px;
        padding: 6px;
        background-color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-radius: 10px;
        box-shadow: 0 0 10px #55555563;
        transition: var(--transition);
        position: relative;
    }
    
    .item:hover {
        transform: translateY(-6px);
    }
    
    .item img {
        width: 100%;
        padding: 20px;
        transition: var(--transition);
        object-fit: contain;
        height: 180px;
    }
    
    .item:hover img {
        padding: 10px;
    }
    
    .item .title {
        margin: 10px;
        color: grey;
        width: 100%;
        height: 40px;
        font-size: 13px;
        overflow: hidden;
        padding: 0 20px 5px 5px;
        text-decoration: none;
    }
    
    .item .price {
        color: black;
        width: 100%;
        height: 30px;
        font-size: 17px;
        overflow: hidden;
        padding: 0 5px 5px 10px;
        text-align: right;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }
    
    .original-price {
        color: var(--error-color);
        text-decoration: line-through;
        font-size: 14px;
        font-weight: normal;
    }
    
    .discounted-price {
        color: black;
        font-weight: 600;
    }
    
    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: var(--error-color);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .item:hover {
        cursor: pointer;
    }
    
    #newItems {
        overflow-x: hidden;
        display: flex;
        margin: 0;
        padding-bottom: 20px;
        padding-left: 5px;
        background-color: var(--ivory-color);
        width: 100%;
        max-width: 1400px; /* Limit width to show 4 items */
        gap: 20px;
    }
    
    #newItemsHeader {
        text-align: center;
        margin: 40px 0 20px 0;
        color: var(--noir-color);
        font-size: 2em;
        font-weight: bold;
    }
    
    #newItemsItem {
        width: 330px;
        min-width: 330px; /* Fixed width for 4 items */
        height: 440px;
        margin: 15px 0;
        background-color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        padding: 20px;
        overflow: hidden;
    }
    
    #newItemsItem:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    
    #newItemsItem img {
        height: 280px;
        object-fit: contain;
        width: 100%;
        padding: 10px;
        transition: all 0.3s ease;
    }
    
    #newItemsItem img:hover {
        transform: scale(1.05);
    }
    
    #newItemsItem .title {
        font-size: 15px;
        text-align: left;
        margin: 15px 0;
        min-height: 50px;
        overflow: hidden;
        color: #555;
        padding: 0 5px;
        width: 100%;
    }
    
    #newItemsItem .price {
        text-align: right;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        margin-top: auto;
        padding: 0 5px 5px 10px;
        width: 100%;
        font-size: 17px;
        font-weight: 600;
        color: black;
    }       
    
    #newItemsItem .original-price {
        color: var(--error-color);
        text-decoration: line-through;
        font-size: 14px;
        font-weight: normal;
    }
    
    #newItemsItem .discounted-price {
        color: black;
        font-weight: 600;
        font-size: 17px;
    }
    
    .new-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: rgb(42, 175, 169);
        color: white;
        font-size: 12px;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 4px;
        z-index: 1;
    }
    
    #newItemsItem .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: var(--error-color);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }       
    
    ::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    a {
        text-decoration:none;
        color:var(--page-text-color);
    }
    
    /* Wheel Carousel starts here*/
    .background-gradient{
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        height: 600px;
        background: linear-gradient(to bottom, var(--noir-color),70%, rgb(26, 78, 118));
    }

    .wheel-carousel {
        width: 100%;
        overflow: hidden;
        position: relative;
        height: 500px;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        /* background-color: var(--ivory-color); */
        max-width: 1200px; /* Limit the width to show 3 items */
    }
    
    .wheel-track {
        width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .wheel-item {
        width: 330px;
        min-width: 300px;
        height: 400px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        padding: 20px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .wheel-item.active {
        transform: translate(-50%, -50%) scale(1.03);
        z-index: 4;
        opacity: 1;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    
    .wheel-item:hover {
        transform: translate(-50%, -50%) scale(1.08);
        z-index: 4;
        opacity: 1;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        cursor: pointer;
    }
    
    .wheel-item img {
        height: 250px;
        object-fit: contain;
        width: 100%;
        padding: 10px;
        transition: all 0.3s ease;
    }
    
    .wheel-item img:hover {
        transform: scale(1.05);
    }

    .wheel-item .title {
        font-size: 15px;
        text-align: left;
        margin: 15px 0;
        min-height: 50px;
        overflow: hidden;
        color: #555;
        padding: 0 5px 0 10px;
        width: 100%;
    }

    .wheel-item .price {
        text-align: right;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        margin-top: auto;
        padding: 0 5px 5px 10px;
        width: 100%;
        font-size: 17px;
        font-weight: 600;
        color: black;
    }

    .wheel-item .original-price {
        color: var(--error-color);
        text-decoration: line-through;
        font-size: 14px;
        font-weight: normal;
    }

    .wheel-item .discounted-price {
        color: black;
        font-weight: 600;
        font-size: 17px;
    }

    .wheel-item .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: white;
        color: var(--error-color);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }

    .wheel-item.left {
        transform: translate(-50%, -50%) scale(0.9) translateX(-380px) translateY(25px);
        opacity: 0.7;
        z-index: 3;
    }

    .wheel-item.right {
        transform: translate(-50%, -50%) scale(0.9) translateX(380px) translateY(25px);
        opacity: 0.7;
        z-index: 3;
    }
    
    .wheel-item.far-left {
        transform: translate(-50%, -50%) scale(0.8) translateX(-800px) translateY(55px);
        opacity: 0.5;
        z-index: 2;
    }
    
    .wheel-item.far-right {
        transform: translate(-50%, -50%) scale(0.8) translateX(800px) translateY(55px);
        opacity: 0.5;
        z-index: 2;
    }
    
    .wheel-item.hidden {
        opacity: 0;
        display: none;
        pointer-events: none;
    }
    
    .wheel-item .bottom-container {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: self-end;
        width: 100%;
        margin-top: auto;
        padding: 0 5px;
    }
    
    .bottom-container {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: self-end;
        width: 100%;
        margin-top: auto;
        padding: 0 5px;
    }
    
    .wheel-item .wishlist-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        transition: transform 0.2s;
        z-index: 2;
    }
    
    .wheel-item .wishlist-btn:hover {
        transform: scale(1.1);
    }
    
    .wheel-item .wishlist-btn i {
        font-size: 20px;
        color: #ccc;
        transition: color 0.2s;
    }
    
    .wheel-item .wishlist-btn.active i {
        color: var(--error-color);
    }
    
    #topItemsHeader {
        width: 100%;
        text-align: center;
        margin: 20px 0 0 0;
        color: var(--text-color);
        font-size: 2em;
        font-weight: bold;
    }
    
    .wishlist-btn {
        background: none;
        border: none;
        cursor: pointer;
        z-index: 2;
        padding: 5px;
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
    
    /* Add pagination styles */
    .pagination {
        width: 100%;
        display: flex;
        justify-content: center;
        margin: 40px 0;
        padding: 20px 0;
    }
    
    .pagination-container {
        display: flex;
        align-items: center;
        gap: 20px;
        background: white;
        padding: 15px 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .pagination-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--ivory-color);
        color: var(--noir-color);
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .pagination-btn:hover {
        background: var(--noir-color);
        color: white;
    }
    
    .page-numbers {
        display: flex;
        gap: 10px;
    }
    
    .page-number {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--ivory-color);
        color: var(--noir-color);
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .page-number:hover {
        background: var(--button-color-hover);
        color: white;
    }
    
    .page-number.active {
        background: var(--button-color);
        color: white;
    }
    
    .page-number.active:hover {
        background: var(--button-color-hover);
        color: white;
    }

    .product-listing-row {
        display: flex;
        gap: 24px;
        align-items: stretch; /* Ensures all cards are the same height */
    }

    .product-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%; /* or a fixed height like 350px */
        min-height: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        padding: 18px;
    }

    .product-card .product-image {
        width: 100%;
        height: 160px;
        object-fit: contain;
        margin-bottom: 12px;
    }

    .product-card .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
        min-height: 40px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-card .product-footer {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Add styles for navigation arrows */
    .carousel-container {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 20px 0;
    }

    .carousel-arrow {
        position: relative;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .carousel-arrow:hover {
        background: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .carousel-arrow i {
        font-size: 18px;
        color: var(--noir-color);
    }

    .carousel-arrow:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Add styles for new items navigation */
    #newItems {
        position: relative;
    }

    #newItems .carousel-arrow {
        top: 50%;
        transform: translateY(-50%);
    }

    /* Add these styles to your existing CSS */
    .product-link {
        text-decoration: none;
        color: inherit;
        display: block;
        width: 100%;
        height: 100%;
    }

    .wheel-item .product-link,
    #newItemsItem .product-link {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .wheel-item .bottom-container,
    #newItemsItem .bottom-container {
        margin-top: auto;
    }

    /* Ensure wishlist button stays clickable */
    .wishlist-btn {
        position: relative;
        z-index: 2;
    }

    /* Mobile Optimizations */
    @media screen and (max-width: 768px) {
        #container {
            flex-direction: column;
        }

        .filter-toggle-top {
            display: flex;
            position: fixed;
            top: 80px;
            left: 15px;
            z-index: 100;
            margin: 0;
            width: auto;
            max-width: none;
            font-size: 13px;
            padding: 10px 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .filter-toggle-top i {
            font-size: 14px;
            margin-right: 5px;
        }

        #filters {
            position: fixed;
            left: -100%;
            top: 0;
            width: 85%;
            max-width: 320px;
            height: 100vh;
            z-index: 1001;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s;
            padding: 20px 20px 20px;
            background-color: white;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            visibility: hidden;
        }

        #filters.active {
            left: 0;
            visibility: visible;
        }

        /* Remove bottom filter toggle */
        #filter-toggle {
            display: none;
        }

        /* Show close button on mobile */
        #filters .close-filters {
            display: block;
        }

        #filterForm {
            margin-top: 0;
        }

        .filter-section {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 16px;
        }

        .filter-section h3 {
            font-size: 16px;
            padding: 12px 0;
            margin-bottom: 8px;
        }

        .filter-content {
            padding: 8px 0;
        }

        .category-header {
            padding: 12px;
            margin-bottom: 8px;
        }

        .category-header h4 {
            font-size: 15px;
        }

        .subfilter {
            padding: 12px 8px;
            margin: 4px 0;
        }

        .subfilter input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        .subfilter label {
            font-size: 14px;
        }

        .filter {
            padding: 12px 8px;
            margin: 4px 0;
        }

        .filter input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        .filter label {
            font-size: 14px;
        }

        #filtOpts {
            flex-direction: column;
            gap: 12px;
            margin: 20px 0;
        }

        #filtOpts input {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            border-radius: 8px;
        }

        .price-range {
            flex-direction: column;
            gap: 12px;
            padding: 8px 0;
        }

        .price-range input[type="number"] {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* Dark overlay when filters are open */
        .filter-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .filter-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Adjust item margin to account for filter button */
        #items {
            padding-top: 60px;
        }
    }

    /* Small Mobile Devices */
    @media screen and (max-width: 480px) {
        #filters {
            width: 100%;
            max-width: none;
        }

        #filter-toggle {
            bottom: 20px;
            right: 20px;
            width: 52px;
            height: 52px;
        }

        .filter-section h3 {
            font-size: 15px;
        }

        .subfilter, .filter {
            padding: 10px 8px;
        }

        .subfilter label, .filter label {
            font-size: 13px;
        }
    }

    /* Narrower filters for screens below 500px */
    @media screen and (max-width: 500px) {
        #filters {
            width: 85%;
            max-width: 300px;
            padding: 15px;
            padding-top: 60px;
            padding-bottom: 40px;
        }
        
        .filter-section {
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        
        .filter-section h3 {
            font-size: 14px;
            padding: 8px 0;
            margin-bottom: 8px;
        }
        
        .category-header {
            padding: 8px;
            margin-bottom: 6px;
        }
        
        .category-header h4 {
            font-size: 13px;
        }
        
        .category-content {
            padding: 6px;
            margin-bottom: 10px;
        }
        
        .subfilter {
            margin: 2px 0;
            padding: 5px;
        }
        
        .subfilter input[type="checkbox"] {
            width: 14px;
            height: 14px;
            margin-right: 6px;
        }
        
        .subfilter label {
            font-size: 12px;
        }
        
        .filter {
            margin: 4px 0;
            padding: 5px;
        }
        
        .filter input[type="checkbox"] {
            width: 14px;
            height: 14px;
            margin-right: 8px;
        }
        
        .filter label {
            font-size: 12px;
        }
        
        #filtOpts {
            gap: 8px;
            margin-bottom: 15px;
        }
        
        #filtOpts input {
            padding: 10px;
            font-size: 13px;
        }
        
        .price-range input[type="number"] {
            padding: 10px;
            font-size: 13px;
        }
    }

    /* Even narrower filters for very small screens below 400px */
    @media screen and (max-width: 400px) {
        #filters {
            width: 80%;
            max-width: 260px;
            padding: 12px;
            padding-top: 15px;
            padding-bottom: 30px;
        }
        
        .filter-section {
            margin-bottom: 12px;
            padding-bottom: 8px;
        }
        
        .filter-section h3 {
            font-size: 13px;
            padding: 6px 0;
            margin-bottom: 6px;
        }
        
        .category-header {
            padding: 6px;
            margin-bottom: 4px;
        }
        
        .category-header h4 {
            font-size: 12px;
        }
        
        .category-content {
            padding: 4px;
            margin-bottom: 8px;
        }
        
        .subfilter {
            margin: 1px 0;
            padding: 4px;
        }
        
        .subfilter input[type="checkbox"] {
            width: 12px;
            height: 12px;
            margin-right: 4px;
        }
        
        .subfilter label {
            font-size: 11px;
        }
        
        .filter {
            margin: 2px 0;
            padding: 4px;
        }
        
        .filter input[type="checkbox"] {
            width: 12px;
            height: 12px;
            margin-right: 6px;
        }
        
        .filter label {
            font-size: 11px;
        }
        
        #filtOpts {
            gap: 6px;
            margin-bottom: 12px;
        }
        
        #filtOpts input {
            padding: 8px;
            font-size: 12px;
            border-radius: 6px;
        }
        
        .price-range input[type="number"] {
            padding: 8px;
            font-size: 12px;
        }
    }

    /* Safe Area Insets for Modern Mobile Devices */
    @supports (padding: max(0px)) {
        @media screen and (max-width: 768px) {
            #filters {
                padding-left: max(20px, env(safe-area-inset-left));
                padding-right: max(20px, env(safe-area-inset-right));
                padding-bottom: max(20px, env(safe-area-inset-bottom));
            }

            #filter-toggle {
                bottom: max(24px, env(safe-area-inset-bottom));
                right: max(24px, env(safe-area-inset-right));
            }
        }
    }

    /* Tablets (768px to 1023px) */
    @media screen and (max-width: 1023px) {
        #container {
            max-width: 100%;
            padding: 0 20px;
        }
        
        .wheel-carousel {
            max-width: 100%;
            height: 450px;
        }
        
        #newItems {
            max-width: 100%;
        }
        
        .item {
            min-width: 180px;
            width: 180px;
        }
        
        #newItemsItem {
            width: 280px;
            min-width: 280px;
        }
        
        .wheel-item {
            width: 300px;
            min-width: 300px;
            height: 380px;
        }
        
        .wheel-item img {
            height: 220px;
        }
    }

    /* Large Phones (480px to 767px) */
    @media screen and (max-width: 767px) {
        #container {
            padding: 0 15px;
        }
        
        .wheel-carousel {
            height: 400px;
        }
        
        .wheel-item {
            width: 280px;
            min-width: 280px;
            height: 350px;
        }
        
        .wheel-item img {
            height: 200px;
        }
        
        #newItemsItem {
            width: 260px;
            min-width: 260px;
            height: 400px;
        }
        
        .item {
            min-width: 160px;
            width: 160px;
        }
        
        .item img {
            height: 160px;
        }
        
        .carousel-arrow {
            width: 36px;
            height: 36px;
        }
        
        #topItemsHeader, #newItemsHeader, #moreItemsText {
            font-size: 1.5em;
        }
    }

    /* Small Phones (up to 479px) */
    @media screen and (max-width: 479px) {
        #container {
            padding: 0 10px;
        }
        
        .wheel-carousel {
            height: 350px;
        }
        
        .wheel-item {
            width: 240px;
            min-width: 240px;
            height: 320px;
        }
        
        .wheel-item img {
            height: 180px;
        }
        
        #newItemsItem {
            width: 220px;
            min-width: 220px;
            height: 380px;
        }
        
        .item {
            min-width: 140px;
            width: 140px;
        }
        
        .item img {
            height: 140px;
        }
        
        .carousel-arrow {
            width: 32px;
            height: 32px;
        }
        
        #topItemsHeader, #newItemsHeader, #moreItemsText {
            font-size: 1.3em;
        }
    }

    /* Landscape Orientation for Mobile Devices */
    @media screen and (max-height: 500px) and (orientation: landscape) {
        .wheel-carousel {
            height: 300px;
        }
        
        .wheel-item {
            height: 280px;
        }
        
        .wheel-item img {
            height: 160px;
        }
        
        #newItemsItem {
            height: 320px;
        }
        
        .item {
            height: 280px;
        }
        
        .item img {
            height: 120px;
        }
    }

    /* High-DPI (Retina) Displays */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .item img, .wheel-item img, #newItemsItem img {
            image-rendering: -webkit-optimize-contrast;
        }
    }

    /* Specific styles for screens less than 1150px */
    @media screen and (max-width: 1250px) {
        .wheel-carousel {
            height: 440px;
        }
        
        .wheel-item {
            width: 300px;
            min-width: 280px;
            height: 360px;
        }

        .wheel-item.left {
            transform: translate(-50%, -50%) scale(0.9) translateX(-350px) translateY(25px);
            opacity: 0.7;
            z-index: 3;
        }

        .wheel-item.right {
            transform: translate(-50%, -50%) scale(0.9) translateX(350px) translateY(25px);
            opacity: 0.7;
            z-index: 3;
        }
        
        .wheel-item img {
            height: 220px;
        }
        
        .wheel-item .title {
            font-size: 14px;
            min-height: 45px;
            margin: 12px 0;
        }
        
        .wheel-item .price {
            font-size: 16px;
        }
        
        .wheel-item .wishlist-btn i {
            font-size: 18px;
        }
        
        #topItemsHeader {
            font-size: 1.8em;
        }
    }

    /* Show only 1 item in the top products carousel for screens below 1040px */
    @media screen and (max-width: 1040px) {
        .wheel-item.left, 
        .wheel-item.right {
            opacity: 0;
            visibility: hidden;
            display: none;
        }

        .wheel-item.hidden {
            display: none;
            pointer-events: none;
        }
        
        .wheel-carousel {
            height: 420px;
        }
        
        .wheel-item {
            width: 280px;
            min-width: 260px;
            height: 340px;
        }
        
        .wheel-item.active {
            transform: translate(-50%, -50%) scale(0.7);
        }
        
        .wheel-item:hover {
            transform: translate(-50%, -50%) scale(0.9);
        }
        
        #topItemsHeader {
            font-size: 1.7em;
        }
    }

    /* Print Styles */
    @media print {
        #filters, .carousel-arrow, .wishlist-btn {
            display: none !important;
        }
        
        #container {
            display: block;
        }
        
        .item, #newItemsItem, .wheel-item {
            break-inside: avoid;
            page-break-inside: avoid;
            margin: 20px 0;
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .item img, #newItemsItem img, .wheel-item img {
            max-width: 200px;
            height: auto;
        }
    }

    /* Reduced Motion Preferences */
    @media (prefers-reduced-motion: reduce) {
        .item, #newItemsItem, .wheel-item {
            transition: none !important;
        }
        
        .wheel-item.active,
        .wheel-item.left,
        .wheel-item.right {
            transition: none !important;
        }
        
        #newItems {
            scroll-behavior: auto !important;
        }
    }

    /* Touch Device Optimizations */
    @media (hover: none) {
        .item:hover, #newItemsItem:hover, .wheel-item:hover {
            transform: none;
        }
        
        .wishlist-btn:hover {
            transform: none;
        }
        
        .carousel-arrow {
            padding: 12px;
        }
    }

    /* Loading State Styles */
    @media (prefers-reduced-motion: no-preference) {
        .item, #newItemsItem, .wheel-item {
            opacity: 0;
            animation: fadeIn 0.3s ease-in forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    }

    /* Accessibility Improvements */
    @media (prefers-contrast: more) {
        .item, #newItemsItem, .wheel-item {
            border: 2px solid #000;
        }
        
        .discount-badge, .new-badge {
            border: 2px solid #fff;
        }
        
        .carousel-arrow {
            border: 2px solid #000;
        }
    }

    /* Energy Saving Mode */
    @media (prefers-reduced-data: reduce) {
        .item img, #newItemsItem img, .wheel-item img {
            loading: lazy;
        }
    }

    /* Add a close button for filters, hidden by default */
    #filters .close-filters {
        position: absolute;
        top: 20px;
        right: 20px;
        background: none;
        border: none;
        font-size: 20px;
        color: var(--noir-color);
        cursor: pointer;
        z-index: 1001;
        display: none; /* Hide by default */
    }

    @media screen and (max-width: 768px) {
        
        #filters .close-filters {
            top: 15px;
            right: 15px;
            font-size: 22px;
        }
    }

    /* Styles for very small screens (less than 400px) */
    @media screen and (max-width: 400px) {

        #items {
            overflow-x: hidden;
        }
        /* Make new items smaller and show 2 per row */
        #newItemsItem {
            width: 150px;
            min-width: 150px;
            height: 250px;
            padding: 12px;
            margin: 8px 0;
        }

        #newItems {
            gap: 10px;
            max-width: 100%;
            padding-left: 3px;
        }

        #newItemsItem img {
            height: 150px;
            padding: 5px;
        }

        #newItemsItem .title {
            font-size: 12px;
            margin: 8px 0;
            min-height: 40px;
        }

        #newItemsItem .price {
            font-size: 14px;
            padding: 0 3px 3px 5px;
        }

        /* Hide the original strikethrough price on small screens */
        .original-price, 
        #newItemsItem .original-price, 
        .wheel-item .original-price {
            display: none;
        }

        #newItemsItem .discounted-price {
            font-size: 14px;
        }

        .new-badge, #newItemsItem .discount-badge {
            font-size: 10px;
            padding: 3px 6px;
        }

        /* Change More Products layout to 2 columns */
        .itemBox {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 10px 5px;
        }

        .item {
            width: 100%;
            min-width: auto;
            margin: 0;
        }

        .item img {
            height: 140px;
            padding: 10px;
        }

        .item .title {
            font-size: 12px;
            height: 35px;
        }

        .item .price {
            font-size: 14px;
        }

        .discount-badge {
            font-size: 10px;
            padding: 3px 6px;
        }

        #newItemsHeader, #topItemsHeader, #moreItemsText {
            font-size: 1.5em;
            margin: 30px 0 15px 0;
        }
    }
</style>