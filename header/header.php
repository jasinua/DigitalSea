<?php
    function getImageSource($product_id, $image_url) {
        $local_image = "images/product_$product_id.png";
        return file_exists($local_image) ? $local_image : htmlspecialchars($image_url);
    }
    
    ob_start(); // Fillon output buffering
    include_once "controller/function.php"

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- <link href="https://cdn-uicons.flaticon.com/uicons-rounded-regular/css/uicons-rounded-regular.css" rel="stylesheet"> -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="logo2.png">
    
    <title>DigitalSea</title>
</head>
<style>

@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

    .montserrat-<uniquifier> {
        font-family: "Montserrat", sans-serif;
        font-optical-sizing: auto;
        font-weight: <weight>;
        font-style: normal;
    }
    
    :root {
        --background-color: #f9f8f7;       
        --text-color: white;
        --page-text-color: #232a2f;

        --modal-bg-color: white;
                        
        --button-color: #232a2f;
        --button-color-hover:rgb(26, 78, 118);

        --filt-button-color: #153147;
        --filt-button-color-hover:rgb(26, 78, 118);

        --noir-color: #232a2f;
        --navy-color: #153147;
        --navy-color-lighter:rgb(69, 110, 142);
        --mist-color: #adb8bb;
        --almond-color: #edeae4;
        --ivory-color: #f9f8f7;

        --transition: all 0.5s ease;
        --transition-faster: all 0.3 ease;
        --border: 3px solid #153147;
        --shadow: 0 0 5px #153147;
        --shadow-input: 0 0 8px #153147;

        --logout-color:rgb(222, 34, 34);
        --logout-color-hover: #C70000;

        --success-color: #00c853;
        --success-color-hover: #00a846;

        --error-color: #F94040;
        --error-color-hover: #C70000;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family:"Montserrat";
    }

    body {
        display: flex;
        flex-direction: column;
        font-family: Arial, sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        line-height: 1.5;
        min-height: 100vh;
    }

    header {
        display: flex;
        background: linear-gradient(to right, rgb(69, 110, 142) 0%, rgb(26, 78, 118) 6%, var(--noir-color) 16%, 
                    var(--noir-color) 100%);
        color: white;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    header .logo {
        margin: 15px 10px 10px 10px;
        padding: 0;
        height: 35px;
        width: 35px;
        cursor: pointer;
    }

    .search-container {
        flex: 1;
        width: 600px;
        margin: auto;
        position: absolute;
        justify-self: center;
        align-self: center;
        left: calc(50% - 600px/2);
        transition: all 0.3s ease;
    }

    .search-container form {
        display: flex;
        align-items: center;
        position: relative;
    }

    .search-container input[type="text"] {
        width: 100%;
        min-width: 450px;
        padding: 10px 35px 10px 15px;
        border: none;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-icon {
        display: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 8px;
        background: none;
        border: none;
        margin-right: 15px;
    }

    .search-container input[type="text"]::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .search-container input[type="text"]:focus {
        outline: none;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .clear-search {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        font-size: 18px;
        padding: 5px;
        display: none;
        z-index: 2;
    }

    .clear-search:hover {
        color: white;
    }

    nav ul {
        list-style: none;
        display: flex;
        justify-content: center;
        padding: 0;
    }

    nav ul li {
        margin: 0 23px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    nav ul li i {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 25px;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        position: relative;
        display: inline-block;
    }    

    nav ul li i::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 0;
        height: 2px;
        background-color: white;
        transform: translateX(-50%);
        transition: width 0.3s ease;
    }

    nav ul li i:hover {
        transition: var(--transition);
        width: 100%;
        color:var(--mist-color);
    }

    /* Cart Preview Styles */
    .cart-preview {
        position: absolute;
        top: 100%;
        right: 0;
        width: 350px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 15px;
        display: none;
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
    }

    .cart-preview-item {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--mist-color);
    }

    .cart-preview-item:last-child {
        border-bottom: none;
    }

    .cart-preview-item img {
        width: 50px;
        height: 50px;
        object-fit: contain;
        margin-right: 10px;
        border-radius: 4px;
    }

    .cart-preview-item-info {
        flex: 1;
    }

    .cart-preview-item-name {
        color: var(--page-text-color);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .cart-preview-item-price {
        color: var(--button-color);
        font-weight: bold;
        font-size: 14px;
    }

    .cart-preview-footer {
        margin-top: 15px;
        padding-top: 10px;
        border-top: 1px solid var(--mist-color);
    }

    .cart-preview-total {
        color: var(--page-text-color);
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 10px;
    }

    .cart-preview-checkout {
        display: block;
        background-color: var(--button-color);
        color: white;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .cart-preview-checkout:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .empty-cart-message {
        text-align: center;
        padding: 20px;
        color: var(--mist-color);
        font-style: italic;
    }

    .cart-link {
        position: relative;
    }

    .cart-link:hover .cart-preview {
        display: block;
    }

    /* ===== Auth Dropdown ===== */
    .auth-menu {
        cursor: pointer;
        font-size: 25px;
        font-weight: 100;
        color: white;
        display: inline-block;
    }

    #auth-toggle {
        display: none;
    }

    .auth-dropdown {
        position: absolute;
        right: 40px;
        top: 55px;
        background-color: #444;
        border-radius: 5px;
        padding: 10px;
        display: none;
        box-shadow: 0px 0px 6px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }

    #auth-toggle:checked + .auth-dropdown {
        display: block;
    }

    .auth-dropdown a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        transition: var(--transition);
    }

    .auth-dropdown a:hover {
        background: #555;
    }

    .page-wrapper {
        flex: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .icons {
        width: 20px;
        height: 20px;
        font-weight: bold;
        object-fit: contain;
        display: block;
        margin: 0 auto;
        transition: ease-out 0.2s;
    }

    .icons:hover {
        transform: scale(1.2);
        transition: ease-out 0.2s;
    }

    /* Remove all search suggestion styles */
    .ui-autocomplete,
    .ui-menu-item,
    .search-suggestion-item,
    .search-suggestion-image,
    .search-suggestion-content,
    .search-suggestion-title,
    .search-suggestion-description,
    .search-suggestion-price,
    .search-suggestion-final-price,
    .search-suggestion-original-price,
    .search-suggestion-discount,
    .no-results {
        display: none;
    }

    .cart-preview-item {
        height: 90px;
    }

    .cart-preview {
        max-height: 270px;
        overflow-y: auto;
    }

    .cart-preview-item-link {
        display: block;
        text-decoration: none;
        color: inherit;
    }

    .cart-preview-item-link:hover .cart-preview-item {
        background: #f5f6fa;
        box-shadow: 0 2px 8px rgba(21,49,71,0.08);
    }

    /* Mobile Menu Styles */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 10px;
    }

    .mobile-menu {
        display: none;
        position: fixed;
        top: 60px;
        right: 0;
        width: 250px;
        background-color: var(--noir-color);
        box-shadow: -2px 2px 10px rgba(0,0,0,0.2);
        z-index: 1000;
        border-radius: 0 0 0 8px;
        padding: 10px 0;
    }

    .mobile-menu.active {
        display: block;
    }

    .mobile-menu-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .mobile-menu-item:hover {
        background-color: var(--navy-color);
    }

    .mobile-menu-item i {
        width: 24px;
        margin-right: 12px;
        font-size: 20px;
    }

    .mobile-menu-item span {
        font-size: 16px;
    }

    @media screen and (max-width: 768px) {
        .mobile-menu-toggle {
            display: block;
        }

        nav ul {
            display: none;
        }

        .search-container {
            width: calc(100% - 100px);
            left: 50px;
        }

        .search-container input[type="text"] {
            min-width: unset;
            width: 100%;
        }
    }

    @media screen and (max-width: 480px) {
        .search-container {
            width: calc(100% - 80px);
            left: 40px;
        }
    }

    /* Mobile Optimizations */
    @media screen and (max-width: 768px) {
        header {
            position: relative;
            height: 60px;
        }

        .logo {
            height: 30px;
            width: 30px;
            margin: 0;
        }

        .search-container {
            position: absolute;
            top: 60px;
            left: 0;
            width: 100%;
            padding: 10px;
            background-color: var(--noir-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .search-container form {
            max-width: 100%;
        }

        .search-container input[type="text"] {
            width: 100%;
            min-width: unset;
            padding: 12px 35px 12px 15px;
            font-size: 16px; /* Prevents zoom on iOS */
        }

        .mobile-menu-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            padding: 0;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .mobile-menu-toggle.active {
            transform: rotate(90deg);
        }

        .mobile-menu {
            position: fixed;
            top: 60px;
            right: 0;
            width: 100%;
            max-width: 300px;
            height: calc(100vh - 60px);
            background-color: var(--noir-color);
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
            padding: 20px 0;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .mobile-menu-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .mobile-menu-item:last-child {
            border-bottom: none;
        }

        .mobile-menu-item:hover {
            background-color: var(--navy-color);
        }

        .mobile-menu-item i {
            width: 24px;
            margin-right: 15px;
            font-size: 20px;
            text-align: center;
        }

        .mobile-menu-item span {
            font-size: 16px;
            font-weight: 500;
        }

        nav {
            display: none;
        }

        /* Cart Preview Mobile Optimization */
        .cart-preview {
            position: fixed;
            top: 60px;
            right: 0;
            width: 100%;
            max-width: 300px;
            max-height: calc(100vh - 60px);
            border-radius: 0;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        }

        .cart-preview-item {
            padding: 12px;
        }

        .cart-preview-item img {
            width: 40px;
            height: 40px;
        }
    }

    @media screen and (max-width: 480px) {
        header {
            padding: 8px;
        }

        .logo {
            height: 25px;
            width: 25px;
        }

        .mobile-menu-toggle {
            width: 35px;
            height: 35px;
            font-size: 20px;
        }

        .search-container {
            top: 50px;
        }

        .search-container input[type="text"] {
            padding: 10px 30px 10px 12px;
        }
    }

    /* Safe Area Insets for Modern Mobile Devices */
    @supports (padding: max(0px)) {
        @media screen and (max-width: 768px) {
            .mobile-menu {
                padding-bottom: max(20px, env(safe-area-inset-bottom));
            }
        }
    }

    /* Tablets and Small Desktops (768px to 1500px) */
    @media screen and (max-width: 1500px) {
        .search-container {
            width: 500px;
            margin: 0 15px;
            left: calc(50% - 250px);
        }

        .search-container input[type="text"] {
            min-width: 400px;
            font-size: 13px;
            padding: 8px 30px 8px 12px;
        }
    }

    @media screen and (max-width: 1400px) {
        .search-container {
            width: 500px;
            margin: 0 15px;
            left: calc(50% - 400px);
        }

        .search-container input[type="text"] {
            min-width: 400px;
            font-size: 13px;
            padding: 8px 30px 8px 12px;
        }
    }

    @media screen and (max-width: 1280px) {
        .search-container {
            width: 450px;
            margin: 0 15px;
            left: calc(50% - 400px);
        }

        .search-container input[type="text"] {
            min-width: 400px;
            font-size: 13px;
            padding: 8px 30px 8px 12px;
        }
    }

    @media screen and (max-width: 980px) {
        .search-container {
            width: 400px;
            margin: 0 15px;
            left: calc(50% - 400px);
        }

        .search-container input[type="text"] {
            min-width: 400px;
            font-size: 13px;
            padding: 8px 30px 8px 12px;
        }
    }

    /* Search icon appears at 960px and below */
    @media screen and (max-width: 940px) {
        .search-container {
            display: none;
        }
        
        .search-icon {
            display: inline-block;
        }
    }

    /* Mobile Devices */
    @media screen and (max-width: 768px) {
        .header-icons {
            display: flex;
            align-items: center;
        }
        
        .mobile-menu-toggle {
            display: block;
            background: none;
            border: none;
        }
        
        nav ul {
            display: none;
        }
    }

    @media screen and (max-width: 480px) {
        header {
            padding: 8px;
        }
    }

    /* Safe Area Insets for Modern Mobile Devices */
    @supports (padding: max(0px)) {
        @media screen and (max-width: 767px) {
            .search-container {
                padding-left: max(10px, env(safe-area-inset-left));
                padding-right: max(10px, env(safe-area-inset-right));
            }
        }
    }

    .header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0 10px;
    }

    .left-section {
        display: flex;
        align-items: center;
    }

    .right-section {
        display: flex;
        align-items: center;
    }

    .mobile-search-container {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background-color: var(--noir-color);
        z-index: 1001;
    }

    .mobile-search-container.active {
        display: flex;
        align-items: center;
        padding: 0 15px;
    }

    .mobile-search-container form {
        display: flex;
        align-items: center;
        width: 100%;
        position: relative;
    }

    .mobile-search-container input[type="text"] {
        flex: 1;
        padding: 10px 35px 10px 15px;
        border: none;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
    }
    
    .mobile-search-container .clear-search {
        position: absolute;
        right: 45px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        font-size: 18px;
        padding: 5px;
        display: none;
        z-index: 2;
    }
    
    .mobile-search-container .clear-search:hover {
        color: white;
    }

    .mobile-search-container input[type="text"]::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .mobile-search-container input[type="text"]:focus {
        outline: none;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .mobile-search-close {
        color: white;
        font-size: 20px;
        background: none;
        border: none;
        padding: 8px;
        margin-left: 10px;
        cursor: pointer;
    }

    .cart-count-badge {
        position: absolute;
        top: -7px;
        right: -7px;
        background: var(--noir-color);
        color: white;
        border-radius: 50%;
        width: 17px;
        height: 17px;
        font-size: 11px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        padding: 0 6px;
        border: 1px solid white;
    }

    .wishlist-count-badge {
        position: absolute;
        top: -7px;
        right: -7px;
        background: var(--noir-color);
        color: white;
        border-radius: 50%;
        width: 17px;
        height: 17px;
        font-size: 11px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        padding: 0 6px;
        border: 1px solid white;
    }

    /* Keyboard Shortcuts Modal Styles */
    .modal-shortcuts {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0; top: 0; width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-shortcuts-content {
      background: var(--modal-bg-color, #fff);
      color: var(--page-text-color, #232a2f);
      border-radius: 10px;
      padding: 32px 32px 24px 32px;
      min-width: 320px;
      max-width: 90vw;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      position: relative;
      font-family: "Montserrat", Arial, sans-serif;
    }
    .modal-shortcuts-close {
      position: absolute;
      top: 12px; right: 18px;
      font-size: 28px;
      color: #888;
      cursor: pointer;
      font-weight: bold;
      transition: color 0.2s;
    }
    .modal-shortcuts-close:hover {
      color: #232a2f;
    }
    .modal-shortcuts h2 {
      margin-bottom: 18px;
      font-size: 1.4em;
    }
    .modal-shortcuts ul {
      list-style: none;
      padding: 0;
    }
    .modal-shortcuts li {
      margin-bottom: 10px;
      font-size: 1.1em;
    }

    [data-lucide] {
        
        color: white;
        transition: scale 0.1s ease;
    }
    [data-lucide] svg {
        color: inherit;
        fill: none;
        stroke: currentColor;
    }

    [data-lucide]:hover {
        
        scale: 1.1;
        transition: scale 0.1s ease;
     }
</style>
<body>
    <header>
        <div class="header-wrapper" <?php if(strpos($_SERVER['REQUEST_URI'], 'login.php') !== false): ?>style="display: none;"<?php endif; ?>>
            <div class="left-section">
                <div class="logo-container" style="margin-right: 15px;">
                    <!-- <a href="index.php"> -->
                        <img class="logo" src="logo2.png" alt="logo e kompanise tone" onclick="window.location.href='index.php'">
                    <!-- </a> -->
                </div>
            </div>
            
        <div class="search-container">
            <form action="index.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="Search..." class="search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="button" class="clear-search" title="Clear search">×</button>
            </form>
        </div>
            
            <div class="right-section">
                <button class="search-icon" title="Search">
                    <i class="fas fa-search"></i>
                </button>
                
        <nav>
            <ul>
                <li><a href="index.php"><i data-lucide="home"></i></a></li>
                <?php if(isset($_SESSION['user_id'])) { ?>
                    <li style="position: relative;">
                        <a href="wishlist.php" style="position: relative;">
                            <i data-lucide="heart"></i>
                            <?php
                                $wishlist_count = getWishlistCount($_SESSION['user_id']);
                                if ($wishlist_count > 0 && basename($_SERVER['PHP_SELF']) !== 'wishlist.php') {
                                    $badge_text = ($wishlist_count > 9) ? '9+' : $wishlist_count;
                                    echo '<span class="wishlist-count-badge">' . $badge_text . '</span>';
                                }
                            ?>
                        </a>
                    </li>
                    <li class="cart-link">
                        <a href="cart.php" style="position: relative;">
                            <i data-lucide="shopping-cart"></i>
                            <?php
                                $cart_items = returnCart($_SESSION['user_id']);
                                $cart_count = 0;
                                while ($item = $cart_items->fetch_assoc()) {
                                    if (!isset($item['order_id']) || is_null($item['order_id'])) {
                                        $cart_count += $item['quantity'];
                                    }
                                }
                                if ($cart_count > 0 && basename($_SERVER['PHP_SELF']) !== 'cart.php') {
                                    $badge_text = ($cart_count > 9) ? '9+' : $cart_count;
                                    echo '<span class="cart-count-badge">' . $badge_text . '</span>';
                                }
                            ?>
                        </a>
                        <div class="cart-preview" <?php echo basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'style="display: none;"' : ''; ?>>
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                $cart_items = returnCart($_SESSION['user_id']);
                                $product_quantities = [];
                                $count = 0;

                                // Merge duplicate products by summing quantities
                                while ($item = $cart_items->fetch_assoc()) {
                                    // Only include items where order_id is null
                                    if (!isset($item['order_id']) || is_null($item['order_id'])) {
                                        $pid = $item['product_id'];
                                        if (!isset($product_quantities[$pid])) {
                                            $product_quantities[$pid] = 0;
                                        }
                                        $product_quantities[$pid] += $item['quantity'];
                                    }
                                }

                                // Display merged products, limited to 3 initially
                                foreach ($product_quantities as $pid => $qty) {
                                    if ($count >= 3) break;
                                    $product_result = returnProduct($pid);
                                    if ($product_result && $product = $product_result->fetch_assoc()) {
                                        ?>
                                        <a class="cart-preview-item-link" href="product.php?product=<?php echo $product['product_id']; ?>">
                                            <div class="cart-preview-item">
                                                <img src="<?php echo getImageSource($product['product_id'], $product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                <div class="cart-preview-item-info">
                                                    <div class="cart-preview-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                                    <div class="cart-preview-item-price">
                                                        <?php 
                                                        $price = $product['price'];
                                                        $discount = $product['discount'];
                                                        if ($discount > 0) {
                                                            $price = $price - ($price * $discount/100);
                                                        }
                                                        echo number_format($price, 2); ?>€
                                                        <?php if ($qty > 1) { echo " <span style='color:#888;font-size:13px;'>(x$qty)</span>"; } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <?php
                                        $count++;
                                    }
                                }

                                // Display remaining products if more than 3
                                if ($count >= 3) {
                                    foreach (array_slice(array_keys($product_quantities), 3) as $pid) {
                                        $qty = $product_quantities[$pid];
                                        $product_result = returnProduct($pid);
                                        if ($product_result && $product = $product_result->fetch_assoc()) {
                                            ?>
                                            <a class="cart-preview-item-link" href="product.php?product=<?php echo $product['product_id']; ?>">
                                                <div class="cart-preview-item">
                                                    <img src="<?php echo getImageSource($product['product_id'], $product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <div class="cart-preview-item-info">
                                                        <div class="cart-preview-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                                        <div class="cart-preview-item-price">
                                                            <?php 
                                                            $price = $product['price'];
                                                            $discount = $product['discount'];
                                                            if ($discount > 0) {
                                                                $price = $price - ($price * $discount/100);
                                                            }
                                                            echo number_format($price, 2); ?>€
                                                            <?php if ($qty > 1) { echo " <span style='color:#888;font-size:13px;'>(x$qty)</span>"; } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <?php
                                        }
                                    }
                                }

                                if (empty($product_quantities)) {
                                    echo '<div style="text-align:center; padding: 20px; color: #333;">Your cart is empty</div>';
                                }
                            }
                            ?>
                        </div>
                    </li>
                    <li><a href="profile.php"><i data-lucide="user"></i></a></li>
                    <li><a href="controller/logout.php?from=header"><i data-lucide="log-in"></i></a></li>
                            <?php if(isset($_SESSION['isAdministrator']) && ($_SESSION['isAdministrator'] == 1 || $_SESSION['isAdministrator'] == 2)) { echo "<li><a href='managestock.php'><i data-lucide='wrench'></i></a></li>"; } ?>
                            <?php if(isset($_SESSION['isAdministrator']) && $_SESSION['isAdministrator'] == 2) { echo "<li><a href='admin.php'><i data-lucide='lock'></i></a></li>"; } ?>
                            <?php } else { ?>
                    <li><a href="login.php"><i data-lucide="log-in"></i></a></li>
                <?php } ?>
            </ul>
        </nav>
                
                <button class="mobile-menu-toggle">
                <i data-lucide="menu"></i>
                </button>
            </div>
        </div>
        
        <div class="mobile-search-container">
            <form action="index.php" method="get">
                <input type="text" name="search" placeholder="Search..." class="mobile-search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="button" class="clear-search" title="Clear search">×</button>
                <button type="button" class="mobile-search-close">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        </div>
        
        <div class="mobile-menu">
            <a href="index.php" class="mobile-menu-item">
            <i data-lucide="home"></i>
                <span>Home</span>
            </a>
            <?php if(isset($_SESSION['user_id'])) { ?>
            <a href="wishlist.php" class="mobile-menu-item">
            <i data-lucide="heart"></i>
                <span>Wishlist</span>
            </a>
            <a href="cart.php" class="mobile-menu-item">
            <i data-lucide="shopping-cart"></i>
                <span>Cart</span>
            </a>
            <a href="profile.php" class="mobile-menu-item">
            <i data-lucide="user"></i>
                <span>Profile</span>
            </a>
            <a href="controller/logout.php?from=header" class="mobile-menu-item">
            <i data-lucide="log-in"></i>
                <span>Logout</span>
            </a>
            <?php if(isset($_SESSION['isAdministrator']) && ($_SESSION['isAdministrator'] == 1 || $_SESSION['isAdministrator'] == 2)) { ?>
            <a href="managestock.php" class="mobile-menu-item">
            <i data-lucide="wrench"></i>
                <span>Manage Stock</span>
            </a>
            <?php } ?>
            <?php if(isset($_SESSION['isAdministrator']) && $_SESSION['isAdministrator'] == 2) { ?>
            <a href="admin.php" class="mobile-menu-item">
                <i data-lucide="lock"></i>
                <span>Admin</span>
            </a>
            <?php } ?>
            <?php } else { ?>
            <a href="login.php" class="mobile-menu-item">
                <i data-lucide="log-in"></i>
                <span>Login</span>
            </a>
            <?php } ?>
        </div>
    </header>

    <!-- Keyboard Shortcuts Modal -->
    <div id="shortcutsModal" class="modal-shortcuts">
      <div class="modal-shortcuts-content">
        <span class="modal-shortcuts-close" id="closeShortcutsModal">&times;</span>
        <h2>Keyboard Shortcuts</h2>
        <ul>
          <li><b>F1</b>: Show this help</li>
          <li><b>Ctrl + I </b>: Opens cart</li>
          <li><b>Ctrl + O</b>: Opens Wishlist</li>
          <li><b>Ctrl + L</b>: Quick Search</li>
          <li><b>Esc</b>: Close this modal</li>
          <!-- Add your own shortcuts here -->
        </ul>
      </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        // Clear search functionality for desktop
        const searchInput = $('.search-input');
        const clearButton = $('.clear-search');
        
        clearButton.click(function() {
            $(this).prev('input').val('').focus();
            $(this).hide();
        });

        searchInput.on('input', function() {
            $(this).next('.clear-search').toggle($(this).val().length > 0);
        });

        clearButton.toggle(searchInput.val().length > 0);
        
        // Clear search functionality for mobile
        const mobileSearchInput = $('.mobile-search-input');
        const mobileClearButton = $('.mobile-search-container .clear-search');
        
        mobileClearButton.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            mobileSearchInput.val('').focus();
            $(this).hide();
        });
        
        mobileSearchInput.on('input', function() {
            mobileClearButton.toggle($(this).val().length > 0);
        });
        
        mobileClearButton.toggle(mobileSearchInput.val().length > 0);

        // Mobile menu functionality
        const mobileMenuToggle = $('.mobile-menu-toggle');
        const mobileMenu = $('.mobile-menu');
        const searchIcon = $('.search-icon');
        const mobileSearchContainer = $('.mobile-search-container');
        const mobileSearchClose = $('.mobile-search-close');

        // Toggle mobile menu
        mobileMenuToggle.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            mobileMenu.toggleClass('active');
            $(this).toggleClass('active');
            // Close search if open
            mobileSearchContainer.removeClass('active');
        });

        // Toggle mobile search
        searchIcon.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            mobileSearchContainer.addClass('active');
            mobileSearchInput.focus();
            // Close menu if open
            mobileMenu.removeClass('active');
            mobileMenuToggle.removeClass('active');
        });

        // Close mobile search
        mobileSearchClose.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            mobileSearchContainer.removeClass('active');
        });

        // Close both menu and search when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.mobile-menu, .mobile-menu-toggle, .mobile-search-container, .search-icon').length) {
                mobileMenu.removeClass('active');
                mobileMenuToggle.removeClass('active');
                mobileSearchContainer.removeClass('active');
            }
        });

        // Handle window resize
        $(window).on('resize', function() {
            if (window.innerWidth > 960) {
                mobileMenu.removeClass('active');
                mobileMenuToggle.removeClass('active');
                mobileSearchContainer.removeClass('active');
            }
        });

        // Keyboard Shortcuts Modal logic
        function showShortcutsModal() {
            $('#shortcutsModal').css('display', 'flex');
        }
        function hideShortcutsModal() {
            $('#shortcutsModal').css('display', 'none');
        }

        // Open modal on F1
        $(window).on('keydown', function(e) {
            // F1 key (keyCode 112 or e.key === 'F1')
            if (e.key === "F1" || e.keyCode === 112) {
                e.preventDefault(); // Prevent browser help
                showShortcutsModal();
                return false; // Extra safety
            }
            // Esc key
            if (e.key === "Escape" || e.keyCode === 27) {
                hideShortcutsModal();
            }
            // Ctrl+I: Open cart
            if ((e.ctrlKey || e.metaKey) && (e.key === 'i' || e.key === 'I')) {
                e.preventDefault();
                window.location.href = 'cart.php';
            }
            // Ctrl+O: Open wishlist
            if ((e.ctrlKey || e.metaKey) && (e.key === 'o' || e.key === 'O')) {
                e.preventDefault();
                window.location.href = 'wishlist.php';
            }
            // Ctrl+L: Focus search bar
            if ((e.ctrlKey || e.metaKey) && (e.key === 'l' || e.key === 'L')) {
                e.preventDefault();
                // Try desktop search first
                var $searchInput = $('.search-input:visible');
                if ($searchInput.length) {
                    $searchInput.focus();
                } else {
                    // If not visible, open mobile search
                    $('.mobile-search-container').addClass('active');
                    $('.mobile-search-input').focus();
                }
            }
        });

        // Close modal on X click
        $('#closeShortcutsModal').on('click', function() {
            hideShortcutsModal();
        });

        // Optional: Close modal when clicking outside the content
        $('#shortcutsModal').on('click', function(e) {
            if (e.target === this) hideShortcutsModal();
        });
    });
</script>

<script>
      lucide.createIcons();
</script>
</html>
    