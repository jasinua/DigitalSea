<?php 
    session_start();
    require_once 'model/dbh.inc.php';
    require_once 'controller/home.inc.php';

    // Get current page from URL, default to 1
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $items_per_page = 18;
    
    // Get wishlist items in one query
    $wishlist_items = isset($_SESSION['user_id']) ? getWishlistItems($_SESSION['user_id']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalSea</title>
    
    <!-- Resource hints -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    
    <!-- Critical CSS inline -->
<style>
        /* Critical CSS for above-the-fold content */
        .page-wrapper { flex: 1; display: flex; flex-direction: column; }
        #container { background-color: var(--ivory-color); display: flex; flex: 1; min-height: calc(100vh - 120px); position: relative; }
    #container #moreItemsText {
        text-align: center;
        margin: 40px 0 20px 0;
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
        padding-top: 100px; /* Add padding to account for header */
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
        padding: 20px;
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
        padding: 0 5px 5px 10px;
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
    #container h1 {
        color: var(--noir-color);
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
    .wheel-carousel {
        width: 100%;
        overflow: hidden;
        position: relative;
        height: 500px;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        background-color: var(--ivory-color);
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
        opacity: 0;
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
        text-align: center;
        margin: 20px 0 0 0;
        color: var(--noir-color);
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
        margin: 0 20px;
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

        #filters {
            position: fixed;
            left: -100%;
            top: 0;
            width: 85%;
            max-width: 320px;
            height: 100vh;
            z-index: 1000;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 80px 20px 20px;
            background-color: white;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        #filters.active {
            left: 0;
        }

        #filter-toggle {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1001;
            margin: 0;
            padding: 16px;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            background-color: var(--button-color);
            color: white;
            border: none;
            transition: transform 0.2s ease;
        }

        #filter-toggle:active {
            transform: scale(0.95);
        }

        #filter-toggle i {
            font-size: 24px;
        }

        #filter-toggle span {
            display: none;
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

        /* Add overlay when filters are open */
        .filter-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .filter-overlay.active {
            display: block;
            opacity: 1;
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
</style>
    
    <!-- Defer non-critical CSS -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></noscript>
    
    <link rel="preload" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"></noscript>
    
    <!-- Defer JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>
    
    <!-- Add lazy loading for images -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize filter functionality
            document.querySelectorAll('.category-header').forEach(function(header) {
                header.addEventListener('click', function() {
                    const content = this.nextElementSibling;
                    this.classList.toggle('collapsed');
                    if (content.style.display === 'block') {
                        content.style.display = 'none';
                    } else {
                        content.style.display = 'block';
                    }
                });
            });

            // Initialize filter sections
            document.querySelectorAll('.filter-section h3').forEach(function(header) {
                header.addEventListener('click', function() {
                    const section = this.parentElement;
                    const content = this.nextElementSibling;
                    section.classList.toggle('collapsed');
                    if (content.style.display === 'block') {
                        content.style.display = 'none';
                    } else {
                        content.style.display = 'block';
                    }
                });
            });

            // Clear all filters function
            window.clearAllFilters = function() {
                // Redirect to base page without any filters
                window.location.href = 'index.php';
            };

            // Function to update all instances of a product's heart button
            function updateAllProductHearts(productId, isActive) {
                document.querySelectorAll(`.wishlist-btn[data-product-id="${productId}"]`).forEach(function(btn) {
                    const icon = btn.querySelector('i');
                    if (isActive) {
                        btn.classList.add('active');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        btn.classList.remove('active');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                });
            }

            // Initialize wishlist functionality
            document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const productId = this.dataset.productId;
                    
                    fetch('controller/add_to_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'product_id=' + productId
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.trim() === 'not_logged_in') {
                            window.location.href = 'login.php';
                        } else if (result.trim() === 'added') {
                            updateAllProductHearts(productId, true);
                        } else if (result.trim() === 'removed') {
                            updateAllProductHearts(productId, false);
                        } else if (result.trim() === 'error') {
                            console.error('Error updating wishlist');
                            alert('There was an error updating your wishlist. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('There was an error updating your wishlist. Please try again.');
                    });
                });
            });

            // Check initial wishlist status for each product
            document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
                const productId = btn.dataset.productId;
                fetch('controller/check_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === 'true') {
                        updateAllProductHearts(productId, true);
                    }
                })
                .catch(error => {
                    console.error('Error checking wishlist status:', error);
                });
            });

            // Wheel Carousel functionality
            const wheelTrack = document.getElementById('topItems');
            const wheelItems = wheelTrack.querySelectorAll('.wheel-item');
            const wheelPrev = document.getElementById('wheelPrev');
            const wheelNext = document.getElementById('wheelNext');
            let currentWheelIndex = 0;
            const visibleWheelItems = 3;

            function updateWheelCarousel() {
                wheelItems.forEach((item, index) => {
                    item.classList.remove('active', 'left', 'right', 'hidden');
                    
                    const relativePos = (index - currentWheelIndex + wheelItems.length) % wheelItems.length;
                    
                    if (relativePos === 0) {
                        item.classList.add('active');
                    } else if (relativePos === wheelItems.length - 1) {
                        item.classList.add('left');
                    } else if (relativePos === 1) {
                        item.classList.add('right');
                    } else {
                        item.classList.add('hidden');
                    }
                });

                // Update arrow states
                wheelPrev.disabled = currentWheelIndex === 0;
                wheelNext.disabled = currentWheelIndex >= wheelItems.length - visibleWheelItems;
            }

            wheelPrev.addEventListener('click', () => {
                if (currentWheelIndex > 0) {
                    currentWheelIndex--;
                    updateWheelCarousel();
                }
            });

            wheelNext.addEventListener('click', () => {
                if (currentWheelIndex < wheelItems.length - visibleWheelItems) {
                    currentWheelIndex++;
                    updateWheelCarousel();
                }
            });

            // Initialize wheel carousel
            updateWheelCarousel();

            // New Items Carousel functionality
            const newItems = document.getElementById('newItems');
            const newItemsPrev = document.getElementById('newItemsPrev');
            const newItemsNext = document.getElementById('newItemsNext');
            const itemWidth = 330; // Width of one item
            const itemMargin = 20; // Margin between items
            const scrollAmount = itemWidth + itemMargin; // Total scroll amount for one item
            const visibleNewItems = 4;
            let currentNewItemsIndex = 0;

            function updateNewItemsArrows() {
                newItemsPrev.disabled = currentNewItemsIndex === 0;
                newItemsNext.disabled = currentNewItemsIndex >= newItems.children.length - visibleNewItems;
            }

            newItemsPrev.addEventListener('click', () => {
                if (currentNewItemsIndex > 0) {
                    currentNewItemsIndex--;
                    newItems.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                    updateNewItemsArrows();
                }
            });

            newItemsNext.addEventListener('click', () => {
                if (currentNewItemsIndex < newItems.children.length - visibleNewItems) {
                    currentNewItemsIndex++;
                    newItems.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                    updateNewItemsArrows();
                }
            });

            // Update arrow states on scroll
            newItems.addEventListener('scroll', () => {
                const scrollPosition = newItems.scrollLeft;
                currentNewItemsIndex = Math.round(scrollPosition / scrollAmount);
                updateNewItemsArrows();
            });

            // Initial arrow state
            updateNewItemsArrows();

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    if (document.activeElement === wheelTrack) {
                        wheelPrev.click();
                    } else if (document.activeElement === newItems) {
                        newItemsPrev.click();
                    }
                } else if (e.key === 'ArrowRight') {
                    if (document.activeElement === wheelTrack) {
                        wheelNext.click();
                    } else if (document.activeElement === newItems) {
                        newItemsNext.click();
                    }
                }
            });

            // Make carousels focusable
            wheelTrack.setAttribute('tabindex', '0');
            newItems.setAttribute('tabindex', '0');
        });
    </script>
</head>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php" ?>
        <div id='container'>
            <div id='filters'>
                <form id='filterForm' action="index.php" method='get'>
                    <div id='filtOpts'>
                        <input type='button' value='Clear Filters' onclick="clearAllFilters()">
                        <input type='submit' value='Apply Filters'>
                    </div>
                    
                    <div class="filter-section">
                        <h3>Categories</h3>
                        <div class="filter-content">
                            <?php 
                            $categories = [
                                'Computers & Laptops' => [
                                    'keywords' => ['computer', 'laptop', 'desktop', 'notebook'],
                                    'subcategories' => [
                                        'Gaming Laptops' => ['gaming laptop', 'gaming notebook'],
                                        'Business Laptops' => ['business laptop', 'professional laptop'],
                                        'All-in-One PCs' => ['all in one', 'aio pc'],
                                        'Desktop Towers' => ['desktop tower', 'pc tower'],
                                        'Workstations' => ['workstation', 'professional desktop']
                                    ]
                                ],
                                'Smartphones & Tablets' => [
                                    'keywords' => ['phone', 'smartphone', 'tablet', 'mobile'],
                                    'subcategories' => [
                                        'Flagship Phones' => ['flagship', 'premium phone'],
                                        'Budget Phones' => ['budget phone', 'affordable phone'],
                                        'iPads & Tablets' => ['ipad', 'tablet'],
                                        'Foldable Phones' => ['foldable', 'fold phone'],
                                        'Gaming Phones' => ['gaming phone', 'game phone']
                                    ]
                                ],
                                'Audio & Headphones' => [
                                    'keywords' => ['headphone', 'earphone', 'speaker', 'audio'],
                                    'subcategories' => [
                                        'Wireless Headphones' => ['wireless headphone', 'bluetooth headphone'],
                                        'Gaming Headsets' => ['gaming headset', 'game headphone'],
                                        'True Wireless Earbuds' => ['true wireless', 'wireless earbud'],
                                        'Studio Monitors' => ['studio monitor', 'monitor speaker'],
                                        'Portable Speakers' => ['portable speaker', 'bluetooth speaker']
                                    ]
                                ],
                                'Gaming & Consoles' => [
                                    'keywords' => ['game', 'console', 'gaming', 'controller'],
                                    'subcategories' => [
                                        'Gaming Consoles' => ['playstation', 'xbox', 'nintendo'],
                                        'Gaming PCs' => ['gaming pc', 'gaming desktop'],
                                        'Gaming Accessories' => ['gaming mouse', 'gaming keyboard'],
                                        'VR Headsets' => ['vr headset', 'virtual reality'],
                                        'Gaming Monitors' => ['gaming monitor', 'game display']
                                    ]
                                ],
                                'Cameras & Photography' => [
                                    'keywords' => ['camera', 'photo', 'lens', 'digital camera'],
                                    'subcategories' => [
                                        'DSLR Cameras' => ['dslr', 'digital slr'],
                                        'Mirrorless Cameras' => ['mirrorless', 'mirror less'],
                                        'Action Cameras' => ['action camera', 'gopro'],
                                        'Camera Lenses' => ['camera lens', 'photography lens'],
                                        'Camera Accessories' => ['camera accessory', 'photo accessory']
                                    ]
                                ],
                                'Networking & Internet' => [
                                    'keywords' => ['router', 'network', 'wifi', 'modem'],
                                    'subcategories' => [
                                        'WiFi Routers' => ['wifi router', 'wireless router'],
                                        'Mesh Systems' => ['mesh wifi', 'mesh system'],
                                        'Network Switches' => ['network switch', 'ethernet switch'],
                                        'Modems' => ['modem', 'cable modem'],
                                        'Network Cards' => ['network card', 'wifi card']
                                    ]
                                ],
                                'Storage & Memory' => [
                                    'keywords' => ['storage', 'memory', 'ssd', 'hard drive'],
                                    'subcategories' => [
                                        'SSDs' => ['ssd', 'solid state'],
                                        'Hard Drives' => ['hard drive', 'hdd'],
                                        'USB Drives' => ['usb drive', 'flash drive'],
                                        'Memory Cards' => ['memory card', 'sd card'],
                                        'External Storage' => ['external drive', 'portable drive']
                                    ]
                                ],
                                'Components & Parts' => [
                                    'keywords' => ['component', 'part', 'processor', 'motherboard'],
                                    'subcategories' => [
                                        'Processors' => ['processor', 'cpu'],
                                        'Graphics Cards' => ['graphics card', 'gpu'],
                                        'Motherboards' => ['motherboard', 'mainboard'],
                                        'RAM' => ['ram', 'memory'],
                                        'Power Supplies' => ['power supply', 'psu']
                                    ]
                                ],
                                'Accessories & Peripherals' => [
                                    'keywords' => ['accessory', 'peripheral', 'keyboard', 'mouse'],
                                    'subcategories' => [
                                        'Keyboards' => ['keyboard', 'mechanical keyboard'],
                                        'Mice' => ['mouse', 'gaming mouse'],
                                        'Monitors' => ['monitor', 'display'],
                                        'Webcams' => ['webcam', 'camera'],
                                        'Printers' => ['printer', 'scanner']
                                    ]
                                ],
                                'Smart Home & IoT' => [
                                    'keywords' => ['smart home', 'iot', 'smart device', 'automation'],
                                    'subcategories' => [
                                        'Smart Speakers' => ['smart speaker', 'voice assistant'],
                                        'Smart Lighting' => ['smart light', 'smart bulb'],
                                        'Security Cameras' => ['security camera', 'cctv'],
                                        'Smart Plugs' => ['smart plug', 'smart outlet'],
                                        'Smart Displays' => ['smart display', 'smart screen']
                                    ]
                                ]
                            ];
                            ?>

                            <?php foreach ($categories as $category => $data) { ?>
                                <div class="category-group">
                                    <div class="category-header">
                                        <h4><?php echo $category ?></h4>
                                    </div>
                                    <div class="category-content">
                                        <?php foreach ($data['subcategories'] as $subcat => $subkeywords) { ?>
                                            <div class="subfilter">
                                                <input type="checkbox" name="subfilter[]" id="<?php echo $subcat ?>" value="<?php echo $subcat ?>">
                                                <label for="<?php echo $subcat ?>"><?php echo $subcat ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="filter-section collapsed">
                        <h3>Price Range</h3>
                        <div class="filter-content">
                            <div class="price-range">
                                <input type="number" name="min_price" placeholder="Min €" min="0">
                                <span>-</span>
                                <input type="number" name="max_price" placeholder="Max €" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="filter-section collapsed">
                        <h3>Discounts</h3>
                        <div class="filter-content">
                            <div class="filter">
                                <input type="checkbox" name="discounted_only" id="discounted_only" value="1">
                                <label for="discounted_only">Show only items on sale</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id='items'>
                <?php if(!isset($_GET['subfilter']) && !isset($_GET['min_price']) && !isset($_GET['max_price']) && !isset($_GET['discounted_only']) && !isset($_GET['search']) && $current_page === 1) { ?>   
                    <h1 id='topItemsHeader'>Top Products</h1>
                    <div class="carousel-container">
                        <button class="carousel-arrow" id="wheelPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    <div class='wheel-carousel'>
                        <div class='wheel-track' id='topItems'>
                                <?php foreach (getData("SELECT * FROM products WHERE products.price>900 LIMIT 8") as $prod) { ?>
                                <div class='wheel-item'>
                                    <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                    <?php if ($prod['discount'] > 0) { ?>
                                        <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                    <?php } ?>
                                        <img src="images/product_<?php echo $prod['product_id'] ?>.png" 
                                             alt="<?php echo htmlspecialchars($prod['description']); ?>"
                                             width="225"
                                             height="180">
                                        <div class='title'><?php echo htmlspecialchars($prod['description']); ?></div>
                                    <div class='bottom-container'>
                                        <button class="wishlist-btn <?php echo in_array($prod['product_id'], $wishlist_items) ? 'active' : ''; ?>" data-product-id="<?php echo $prod['product_id']; ?>">
                                            <i class="<?php echo in_array($prod['product_id'], $wishlist_items) ? 'fas' : 'far'; ?> fa-heart"></i>
                                        </button>
                                        <div class='price'>
                                            <?php if ($prod['discount'] > 0) { 
                                                $originalPrice = $prod['price'];
                                                $discountedPrice = $originalPrice * (1 - $prod['discount'] / 100);
                                            ?>
                                                <span class="original-price"><?php echo number_format($originalPrice, 2, '.', ',') ?>€</span>
                                                <span class="discounted-price"><?php echo number_format($discountedPrice, 2, '.', ',') ?>€</span>
                                            <?php } else { ?>
                                                <span class="discounted-price"><?php echo number_format($prod['price'], 2, '.', ',') ?>€</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                        </div>
                        <button class="carousel-arrow" id="wheelNext">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <h1 id='newItemsHeader'>New Products</h1>
                    <div class="carousel-container">
                        <button class="carousel-arrow" id="newItemsPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    <div class='itemLine' id='newItems'>
                        <?php foreach (getData("SELECT * FROM products ORDER BY product_id DESC LIMIT 8") as $prod) { ?>
                            <div class='item' id="newItemsItem">
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                <div class="new-badge">NEW</div>
                                    <?php if ($prod['discount'] > 0) { ?>
                                        <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                    <?php } ?>
                                    <img src="images/product_<?php echo $prod['product_id'] ?>.png" 
                                         alt="<?php echo htmlspecialchars($prod['description']); ?>"
                                         width="225"
                                         height="180">
                                    <div class='title'><?php echo htmlspecialchars($prod['description']); ?></div>
                                    <div class='bottom-container'>
                                    <button class="wishlist-btn <?php echo in_array($prod['product_id'], $wishlist_items) ? 'active' : ''; ?>" data-product-id="<?php echo $prod['product_id']; ?>">
                                        <i class="<?php echo in_array($prod['product_id'], $wishlist_items) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    </button>
                                    <div class='price'>
                                        <?php if ($prod['discount'] > 0) { 
                                            $originalPrice = $prod['price'];
                                            $discountedPrice = $originalPrice * (1 - $prod['discount'] / 100);
                                        ?>
                                            <span class="original-price"><?php echo number_format($originalPrice, 2, '.', ',') ?>€</span>
                                            <span class="discounted-price"><?php echo number_format($discountedPrice, 2, '.', ',') ?>€</span>
                                        <?php } else { ?>
                                            <span class="discounted-price"><?php echo number_format($prod['price'], 2, '.', ',') ?>€</span>
                                        <?php } ?>
                                    </div>
                                </div>
                                </a>
                            </div>
                        <?php } ?>
                        </div>
                        <button class="carousel-arrow" id="newItemsNext">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <h1 id="moreItemsText">More Products</h1>
                    <?php } else { ?>
                        <h1><?php echo isset($_GET['search']) ? 'Search Results' : 'Products'; ?></h1>
                    <?php } ?>
                    <div class='itemBox' id='randomItems'>
                    <?php 
                        if(isset($_GET['subfilter']) || isset($_GET['min_price']) || isset($_GET['max_price']) || isset($_GET['discounted_only']) || isset($_GET['search'])) {
                            $where_conditions = [];
                            
                            if (!empty($_GET['subfilter'])) {
                                $subfilter_conditions = [];
                                foreach ($_GET['subfilter'] as $subcat) {
                                    // Add the subcategory name itself as a search term
                                    $subfilter_conditions[] = "(LOWER(description) LIKE LOWER('%substr($subcat,0,-1)%') OR LOWER(name) LIKE LOWER('%substr($subcat,0,-1)%'))";
                                    
                                    // Also check the keywords for this subcategory
                                    foreach ($categories as $category => $data) {
                                        if (isset($data['subcategories'][$subcat])) {
                                            foreach ($data['subcategories'][$subcat] as $keyword) {
                                                $subfilter_conditions[] = "(LOWER(description) LIKE LOWER('%$keyword%') OR LOWER(name) LIKE LOWER('%$keyword%'))";
                                            }
                                        }
                                    }
                                }

                                if (!empty($subfilter_conditions)) {
                                    $where_conditions[] = "(" . implode(' OR ', $subfilter_conditions) . ")";
                                }
                            }
                            
                            if (!empty($_GET['min_price'])) {
                                $where_conditions[] = "price >= " . floatval($_GET['min_price']);
                            }
                            
                            if (!empty($_GET['max_price'])) {
                                $where_conditions[] = "price <= " . floatval($_GET['max_price']);
                            }

                            if (isset($_GET['discounted_only'])) {
                                $where_conditions[] = "discount > 0";
                            }

                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                $search_term = mysqli_real_escape_string($conn, $_GET['search']);
                                $where_conditions[] = "(LOWER(description) LIKE LOWER('%$search_term%') OR LOWER(name) LIKE LOWER('%$search_term%'))";
                            }
                            
                            $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
                            
                            // Get total count of filtered products for pagination
                            $count_query = "SELECT COUNT(*) as total FROM products $where_clause";
                            $count_result = $conn->query($count_query);
                            $total_products = $count_result->fetch_assoc()['total'];
                            $total_pages = ceil($total_products / $items_per_page);
                            
                            // Add pagination to the query
                            $offset = ($current_page - 1) * $items_per_page;
                            $products = getData("SELECT * FROM products $where_clause LIMIT $items_per_page OFFSET $offset");
                        } else {
                            $products = getProducts($current_page, $items_per_page);
                            $total_products = getTotalProducts();
                            $total_pages = ceil($total_products / $items_per_page);
                        }
                    
                        foreach ($products as $prod) { ?>
                            <div class='item'>
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                <?php if ($prod['discount'] > 0) { ?>
                                    <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                <?php } ?>
                                    <img src="images/product_<?php echo $prod['product_id'] ?>.png" 
                                         alt="<?php echo htmlspecialchars($prod['description']); ?>"
                                         width="225"
                                         height="180">
                                    <div class='title'><?php echo htmlspecialchars($prod['description']); ?></div>
                                <div class='bottom-container'>
                                    <button class="wishlist-btn <?php echo in_array($prod['product_id'], $wishlist_items) ? 'active' : ''; ?>" data-product-id="<?php echo $prod['product_id']; ?>">
                                        <i class="<?php echo in_array($prod['product_id'], $wishlist_items) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    </button>
                                    <div class='price'>
                                        <?php if ($prod['discount'] > 0) { 
                                            $originalPrice = $prod['price'];
                                            $discountedPrice = $originalPrice * (1 - $prod['discount'] / 100);
                                        ?>
                                            <span class="original-price"><?php echo number_format($originalPrice, 2, '.', ',') ?>€</span>
                                            <span class="discounted-price"><?php echo number_format($discountedPrice, 2, '.', ',') ?>€</span>
                                        <?php } else { ?>
                                                <span class="discounted-price"><?php echo number_format($prod['price'], 2, '.', ',') ?>€</span>
                                        <?php } ?>   
                                    </div>
                                </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>

                <!-- Add pagination controls -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <div class="pagination-container">
                            <?php 
                            // Build query string for pagination links
                            $query_params = [];
                            if (isset($_GET['search'])) {
                                $query_params['search'] = $_GET['search'];
                            }
                            if (isset($_GET['subfilter'])) {
                                foreach ($_GET['subfilter'] as $filter) {
                                    $query_params['subfilter'][] = $filter;
                                }
                            }
                            if (isset($_GET['min_price'])) {
                                $query_params['min_price'] = $_GET['min_price'];
                            }
                            if (isset($_GET['max_price'])) {
                                $query_params['max_price'] = $_GET['max_price'];
                            }
                            if (isset($_GET['discounted_only'])) {
                                $query_params['discounted_only'] = $_GET['discounted_only'];
                            }
                            
                            // Function to build query string
                            function buildQueryString($page, $params) {
                                $params['page'] = $page;
                                return '?' . http_build_query($params);
                            }
                            ?>
                            
                            <?php if ($current_page > 1): ?>
                                <a href="<?php echo buildQueryString($current_page - 1, $query_params); ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>

                            <div class="page-numbers">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="<?php echo buildQueryString($i, $query_params); ?>" 
                                       class="page-number <?php echo $i === $current_page ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($current_page < $total_pages): ?>
                                <a href="<?php echo buildQueryString($current_page + 1, $query_params); ?>" class="pagination-btn">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php include "footer/footer.php" ?>
    </div>

<!-- Move scripts to bottom of page and optimize loading -->
<script>
    // Defer non-critical JavaScript execution
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize lazy loading
        var lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));
        if ('IntersectionObserver' in window) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.remove('lazy');
                        lazyImageObserver.unobserve(lazyImage);
                    }
        });
    });
            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        }

        // Wheel Carousel functionality
        const wheelTrack = document.getElementById('topItems');
        const wheelItems = wheelTrack.querySelectorAll('.wheel-item');
        const wheelPrev = document.getElementById('wheelPrev');
        const wheelNext = document.getElementById('wheelNext');
        let currentWheelIndex = 0;
        const visibleWheelItems = 3;

        function updateWheelCarousel() {
            wheelItems.forEach((item, index) => {
                item.classList.remove('active', 'left', 'right', 'hidden');
                
                const relativePos = (index - currentWheelIndex + wheelItems.length) % wheelItems.length;
                
                if (relativePos === 0) {
                    item.classList.add('active');
                } else if (relativePos === wheelItems.length - 1) {
                    item.classList.add('left');
                } else if (relativePos === 1) {
                    item.classList.add('right');
                } else {
                    item.classList.add('hidden');
                }
            });

            // Update arrow states
            wheelPrev.disabled = currentWheelIndex === 0;
            wheelNext.disabled = currentWheelIndex >= wheelItems.length - visibleWheelItems;
        }

        wheelPrev.addEventListener('click', () => {
            if (currentWheelIndex > 0) {
                currentWheelIndex--;
                updateWheelCarousel();
            }
        });

        wheelNext.addEventListener('click', () => {
            if (currentWheelIndex < wheelItems.length - visibleWheelItems) {
                currentWheelIndex++;
                updateWheelCarousel();
            }
        });

        // Initialize wheel carousel
        updateWheelCarousel();

        // New Items Carousel functionality
        const newItems = document.getElementById('newItems');
        const newItemsPrev = document.getElementById('newItemsPrev');
        const newItemsNext = document.getElementById('newItemsNext');
        const itemWidth = 330; // Width of one item
        const itemMargin = 20; // Margin between items
        const scrollAmount = itemWidth + itemMargin; // Total scroll amount for one item
        const visibleNewItems = 4;
        let currentNewItemsIndex = 0;

        function updateNewItemsArrows() {
            newItemsPrev.disabled = currentNewItemsIndex === 0;
            newItemsNext.disabled = currentNewItemsIndex >= newItems.children.length - visibleNewItems;
        }

        newItemsPrev.addEventListener('click', () => {
            if (currentNewItemsIndex > 0) {
                currentNewItemsIndex--;
                newItems.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
                updateNewItemsArrows();
            }
        });

        newItemsNext.addEventListener('click', () => {
            if (currentNewItemsIndex < newItems.children.length - visibleNewItems) {
                currentNewItemsIndex++;
                newItems.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
                updateNewItemsArrows();
            }
        });

        // Update arrow states on scroll
        newItems.addEventListener('scroll', () => {
            const scrollPosition = newItems.scrollLeft;
            currentNewItemsIndex = Math.round(scrollPosition / scrollAmount);
            updateNewItemsArrows();
        });

        // Initial arrow state
        updateNewItemsArrows();

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                if (document.activeElement === wheelTrack) {
                    wheelPrev.click();
                } else if (document.activeElement === newItems) {
                    newItemsPrev.click();
                }
            } else if (e.key === 'ArrowRight') {
                if (document.activeElement === wheelTrack) {
                    wheelNext.click();
                } else if (document.activeElement === newItems) {
                    newItemsNext.click();
                }
            }
        });

        // Make carousels focusable
        wheelTrack.setAttribute('tabindex', '0');
        newItems.setAttribute('tabindex', '0');

        // Function to update all instances of a product's heart button
        function updateAllProductHearts(productId, isActive) {
            document.querySelectorAll(`.wishlist-btn[data-product-id="${productId}"]`).forEach(function(btn) {
                const icon = btn.querySelector('i');
                if (isActive) {
                    btn.classList.add('active');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                } else {
                    btn.classList.remove('active');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
            });
        }

        // Initialize wishlist functionality
        document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
                const productId = this.dataset.productId;
                
                fetch('controller/add_to_wishlist.php', {
                method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === 'not_logged_in') {
                        window.location.href = 'login.php';
                    } else if (result.trim() === 'added') {
                        updateAllProductHearts(productId, true);
                    } else if (result.trim() === 'removed') {
                        updateAllProductHearts(productId, false);
                    } else if (result.trim() === 'error') {
                        console.error('Error updating wishlist');
                        alert('There was an error updating your wishlist. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error updating your wishlist. Please try again.');
            });
        });
        });

        // Check initial wishlist status for each product
        document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
            const productId = btn.dataset.productId;
            fetch('controller/check_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.text())
            .then(result => {
                if (result.trim() === 'true') {
                    updateAllProductHearts(productId, true);
                }
            })
            .catch(error => {
                console.error('Error checking wishlist status:', error);
            });
        });
    });  

        // Initialize filter functionality
        document.querySelectorAll('.filter-section h3').forEach(function(header) {
            header.addEventListener('click', function() {
                const section = this.parentElement;
                const content = this.nextElementSibling;
                section.classList.toggle('collapsed');
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'block';
                }
            });
        });
    });
</script>

<!-- Load jQuery and jQuery UI asynchronously -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" async></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" async></script>

</body>
</html>