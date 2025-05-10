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
    
    <!-- Preload critical CSS -->
    <link rel="preload" href="style.css" as="style">
    <link rel="stylesheet" href="style.css">
    
    <!-- Load Font Awesome asynchronously -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></noscript>
    
    <!-- Load jQuery UI CSS asynchronously -->
    <link rel="preload" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"></noscript>
    
    <!-- Defer non-critical JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>
</head>
<style>
    .page-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    #container {
        background-color: var(--ivory-color);
        display: flex;
        flex: 1;
        min-height: calc(100vh - 120px);
        position: relative;
    }

    #container #moreItemsText {
        text-align: center;
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
        background-color: var(--noir-color);
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
        margin: 20px;
        padding-bottom: 20px;
        background-color: var(--ivory-color);
    }

    #newItemsItem {
        width: 330px;
        min-width: 300px;
        height: 440px;
        margin: 15px;
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
        margin-left: 20px;
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

    .wheel-carousel {
        width: 100%;
        overflow: hidden;
        position: relative;
        height: 600px;
        /* padding: 20px 0; */
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        background-color: var(--ivory-color);
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
        transform: translate(-50%, -50%) scale(1.03) translateY(-10px);
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
        padding: 0 5px;
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

    #newItemsHeader {
        text-align: center;
        margin: 20px 0;
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

    .search-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-container input[type="text"] {
        padding-right: 30px; /* Make room for the X button */
    }

    .clear-search {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
        display: none; /* Hidden by default */
    }

    .clear-search:hover {
        color: #333;
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
        background: var(--noir-color);
        color: white;
    }

    .page-number.active {
        background: var(--noir-color);
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
</style>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php" ?>
        <div id='container'>
            <div id='filters'>
                <form id='filterForm' action="index.php" method='post'>
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

                    <div class="filter-section">
                        <h3>Price Range</h3>
                        <div class="filter-content">
                            <div class="price-range">
                                <input type="number" name="min_price" placeholder="Min €" min="0">
                                <span>-</span>
                                <input type="number" name="max_price" placeholder="Max €" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h3>Discounts</h3>
                        <div class="filter-content">
                            <div class="filter">
                                <input type="checkbox" name="discounted_only" id="discounted_only" value="1">
                                <label for="discounted_only">Show only discounted items</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id='items'>
                <?php if(!isset($_POST['subfilter']) && !isset($_POST['min_price']) && !isset($_POST['max_price']) && !isset($_POST['discounted_only']) && !isset($_GET['search']) && $current_page === 1) { ?>   
                    <h1 id='topItemsHeader'>Top Products</h1>
                    <div class='wheel-carousel'>
                        <div class='wheel-track' id='topItems'>
                            <?php foreach (getData("SELECT * FROM products WHERE products.price>900") as $prod) { ?>
                                    <div class='wheel-item'>
                                    <?php if ($prod['discount'] > 0) { ?>
                                        <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                    <?php } ?>
                                    <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="<?php echo $prod['description'] ?>">
                                    <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
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
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <h1 id='newItemsHeader'>New Products</h1>
                    <div class='itemLine' id='newItems'>
                        <?php foreach (getData("SELECT * FROM products ORDER BY product_id DESC LIMIT 8") as $prod) { ?>
                            <div class='item' id="newItemsItem">
                                <div class="new-badge">NEW</div>
                                <?php if ($prod['discount'] > 0) { ?>
                                    <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                <?php } ?>
                                <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
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
                            </div>
                        <?php } ?>
                    </div>

                    <h1 id="moreItemsText">More Products</h1>
                <?php } else { ?>
                    <h1><?php echo isset($_GET['search']) ? 'Search Results' : 'Products'; ?></h1>
                <?php } ?>

                    <div class='itemBox' id='randomItems'>
                    <?php 
                    if(isset($_POST['subfilter']) || isset($_POST['min_price']) || isset($_POST['max_price']) || isset($_POST['discounted_only']) || isset($_GET['search'])) {
                        $where_conditions = [];
                        
                        if (!empty($_POST['subfilter'])) {
                            $subfilter_conditions = [];
                            foreach ($_POST['subfilter'] as $subcat) {
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
                        
                        if (!empty($_POST['min_price'])) {
                            $where_conditions[] = "price >= " . floatval($_POST['min_price']);
                        }
                        
                        if (!empty($_POST['max_price'])) {
                            $where_conditions[] = "price <= " . floatval($_POST['max_price']);
                        }

                        if (isset($_POST['discounted_only'])) {
                            $where_conditions[] = "discount > 0";
                        }

                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search_term = mysqli_real_escape_string($conn, $_GET['search']);
                            $where_conditions[] = "(LOWER(description) LIKE LOWER('%$search_term%') OR LOWER(name) LIKE LOWER('%$search_term%'))";
                        }
                        
                        $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
                        $products = getData("SELECT * FROM products $where_clause");
                    } else {
                        $products = getProducts($current_page, $items_per_page);
                    }
                    
                    foreach ($products as $prod) { ?>
                                <div class='item'>
                            <?php if ($prod['discount'] > 0) { ?>
                                <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                            <?php } ?>
                                    <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                    <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
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
                                        <?php echo number_format($prod['price'], 2, '.', ',') ?>€
                                    <?php } ?>   
                                </div>
                            </div>
                    </div>
                <?php } ?>
                </div>

                <!-- Add pagination controls -->
                <?php
                $total_products = getTotalProducts();
                $total_pages = ceil($total_products / $items_per_page);
                
                if ($total_pages > 1): ?>
                    <div class="pagination">
                        <div class="pagination-container">
                            <?php if ($current_page > 1): ?>
                                <a href="?page=<?php echo $current_page - 1; ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>

                            <div class="page-numbers">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" 
                                       class="page-number <?php echo $i === $current_page ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?php echo $current_page + 1; ?>" class="pagination-btn">
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

<script>
        // Filter section toggle
        document.querySelectorAll('.filter-section h3').forEach(header => {
            header.addEventListener('click', () => {
                header.parentElement.classList.toggle('collapsed');
            });
        });

        // Category header toggle
        document.querySelectorAll('.category-header').forEach(header => {
            header.addEventListener('click', () => {
                header.classList.toggle('collapsed');
                const content = header.nextElementSibling;
                content.classList.toggle('active');
            });
        });

        // Initialize all category headers as collapsed
        document.querySelectorAll('.category-header').forEach(header => {
            header.classList.add('collapsed');
    });

    // Wheel carousel functionality
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.getElementById('topItems');
        const items = track.querySelectorAll('.wheel-item');
        const itemCount = items.length;
        
        if (itemCount === 0) return;
        
        let currentIndex = 0;
            let autoRotateInterval;
            let isHovering = false;

        function updateCarousel() {
            items.forEach((item, index) => {
                item.classList.remove('active', 'left', 'right', 'far-left', 'far-right', 'hidden');
                
                const relativePos = (index - currentIndex + itemCount) % itemCount;
                
                if (relativePos === 0) {
                    item.classList.add('active');
                } else if (relativePos === itemCount - 1) {
                    item.classList.add('left');
                } else if (relativePos === 1) {
                    item.classList.add('right');
                } else if (relativePos === itemCount - 2) {
                    item.classList.add('far-left');
                } else if (relativePos === 2) {
                    item.classList.add('far-right');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
        
            function startAutoRotate() {
                if (!isHovering) {
                    autoRotateInterval = setInterval(() => {
            currentIndex = (currentIndex + 1) % itemCount;
            updateCarousel();
        }, 3000);
                }
            }

            function stopAutoRotate() {
                clearInterval(autoRotateInterval);
            }
            
            // Initialize carousel
            updateCarousel();
            startAutoRotate();
            
            // Add hover event listeners to all wheel items
            items.forEach(item => {
                item.addEventListener('mouseenter', () => {
                    isHovering = true;
                    stopAutoRotate();
                });
                
                item.addEventListener('mouseleave', () => {
                    isHovering = false;
                    startAutoRotate();
                });
            });
        
        // Manual navigation
        document.querySelector('.wheel-carousel').addEventListener('click', (e) => {
            const clickedItem = e.target.closest('.wheel-item');
            if (!clickedItem) return;
            
            if (clickedItem.classList.contains('left')) {
                currentIndex = (currentIndex - 1 + itemCount) % itemCount;
            } else if (clickedItem.classList.contains('right')) {
                currentIndex = (currentIndex + 1) % itemCount;
            } else if (clickedItem.classList.contains('far-left')) {
                currentIndex = (currentIndex - 2 + itemCount) % itemCount;
            } else if (clickedItem.classList.contains('far-right')) {
                currentIndex = (currentIndex + 2) % itemCount;
            }
            updateCarousel();
        });
    });
    
    // Infinite scroll for new items
    window.addEventListener('load', () => {
        const container = document.getElementById('newItems');
        const clone = container.innerHTML;
        container.innerHTML += clone;
        let scrollSpeed = -1;

        function scrollLoop() {
            container.scrollLeft += scrollSpeed;
            if (container.scrollLeft <= 0) {
                container.scrollLeft = container.scrollWidth / 2;
            }
            requestAnimationFrame(scrollLoop);
        }

        scrollLoop();
        });  

        // Clear all filters function
        function clearAllFilters() {
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Clear price inputs
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.value = '';
            });

            // Collapse all category headers
            document.querySelectorAll('.category-header').forEach(header => {
                header.classList.add('collapsed');
                const content = header.nextElementSibling;
                content.classList.remove('active');
            });

            // Submit the form to refresh the page
            window.location.href = 'index.php';
        }

        $(document).ready(function() {
            // Function to update all instances of a product's heart button
            function updateAllProductHearts(productId, isActive) {
                console.log('Updating hearts for product:', productId, 'isActive:', isActive);
                $(`.wishlist-btn[data-product-id="${productId}"]`).each(function() {
                    const button = $(this);
                    if (isActive) {
                        button.addClass('active').find('i').removeClass('far').addClass('fas');
                    } else {
                        button.removeClass('active').find('i').removeClass('fas').addClass('far');
                    }
                });
            }

            // Check initial wishlist status for each product
            $('.wishlist-btn').each(function() {
                const productId = $(this).data('product-id');
                console.log('Checking initial status for product:', productId);
                $.ajax({
                    url: 'controller/check_wishlist.php',
                    method: 'POST',
                    data: { product_id: productId },
                    success: function(response) {
                        console.log('Initial check response for product', productId, ':', response);
                        if (response.trim() === 'true') {
                            updateAllProductHearts(productId, true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking wishlist status:', error);
                    }
                });
            });

            // Handle wishlist button clicks
            $(document).on('click', '.wishlist-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const button = $(this);
                const productId = button.data('product-id');
                console.log('Wishlist button clicked for product:', productId);
                
                $.ajax({
                    url: 'controller/add_to_wishlist.php',
                    method: 'POST',
                    data: { product_id: productId },
                    success: function(response) {
                        console.log('Wishlist update response:', response);
                        if (response.trim() === 'not_logged_in') {
                            window.location.href = 'login.php';
                        } else if (response.trim() === 'added') {
                            updateAllProductHearts(productId, true);
                        } else if (response.trim() === 'removed') {
                            updateAllProductHearts(productId, false);
                        } else if (response.trim() === 'error') {
                            console.error('Error updating wishlist');
                            alert('There was an error updating your wishlist. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating wishlist:', error);
                        alert('There was an error updating your wishlist. Please try again.');
                    }
                });
            });

            // Show/hide clear button based on search input
            $('.search-input').on('input', function() {
                var $clearBtn = $(this).closest('form').find('.clear-search');
                if ($(this).val().length > 0) {
                    $clearBtn.show();
                } else {
                    $clearBtn.hide();
                }
            });

            // Clear search without redirecting or reloading
            $('.clear-search').on('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $input = $(this).closest('form').find('.search-input');
                $input.val('');
                $(this).hide();
                $input.focus();
            });

            // Initialize clear button visibility for each search bar
            $('.search-input').each(function() {
                var $clearBtn = $(this).closest('form').find('.clear-search');
                if ($(this).val().length > 0) {
                    $clearBtn.show();
                } else {
                    $clearBtn.hide();
                }
            });

            // Handle empty search submission
            $('.search-form').submit(function(e) {
                if ($('.search-input').val().trim() === '') {
                    e.preventDefault();
                    window.location.href = 'index.php';
                }
            });
        });  

        // Optimize wishlist check
        function isInWishlist(productId) {
            return <?php echo json_encode($wishlist_items); ?>.includes(productId);
        }

        // Add lazy loading for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        });  

        // Add smooth scroll to top when changing pages
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                setTimeout(() => {
                    window.location.href = href;
                }, 500);
            });
    });  
</script>
</body>
</html>