<?php
    ob_start(); // Fillon output buffering
    include_once "controller/function.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn-uicons.flaticon.com/uicons-rounded-regular/css/uicons-rounded-regular.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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

        --logout-color: #FF0000;
        --logout-color-hover: #C70000;

        --error-color: #F94040;
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
        background-color: var(--noir-color);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 20px;
        width: 100%;
    }

    header .logo {
        margin: 10px;
        padding: 0;
        height: 35px;
        width: 35px;
    }

    .search-container {
        flex: 1;
        width: 600px;
        margin: auto;
        position: absolute;
        justify-self: center;
        align-self: center;
        left: calc(50% - 600px/2);
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
        max-height: 300px;
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
            padding: 8px 12px;
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
            margin-left: auto;
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
            header {
                padding-left: max(12px, env(safe-area-inset-left));
                padding-right: max(12px, env(safe-area-inset-right));
            }

            .mobile-menu {
                padding-bottom: max(20px, env(safe-area-inset-bottom));
            }
        }
    }
</style>
<body>
    <header>
        <div class="imazhiYne" style="margin-left: 1%; padding: 0; margin-top: 0; margin-bottom: 0;"><a href = "index.php"><img class="logo" src="logo2.png" alt="logo e kompanise tone"></a></div>
        <div class="search-container">
            <form action="index.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="Search..." class="search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="button" class="clear-search" title="Clear search">×</button>
            </form>
        </div>
        <button class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <nav>
            <ul>
                <!-- <li><a href="index.php"><img src="home.png" class="icons" alt=""></a></li> -->
                <li><a href="index.php"><i class="fas fa-home"></i></a></li>
                <?php if(isset($_SESSION['user_id'])) { ?>
                <!-- <li><a href="wishlist.php"><img src="heart.png" class="icons" alt=""></a></li> -->
                <li><a href="wishlist.php"><i class="fas fa-heart"></i></a></li>
                <li class="cart-link">
                    <!-- <a href="cart.php"><img src="shopping-cart.png" class="icons" alt=""></a> -->
                    <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                    <div class="cart-preview" <?php echo basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'style="display: none;"' : ''; ?>>
                        <?php
                        if (isset($_SESSION['user_id'])) {
                            $cart_items = returnCart($_SESSION['user_id']);
                            $product_quantities = [];
                            $count = 0;

                            // Merge duplicate products by summing quantities
                            while ($item = $cart_items->fetch_assoc()) {
                                $pid = $item['product_id'];
                                if (!isset($product_quantities[$pid])) {
                                    $product_quantities[$pid] = 0;
                                }
                                $product_quantities[$pid] += $item['quantity'];
                            }

                            // Display merged products, limited to 3 initially
                            foreach ($product_quantities as $pid => $qty) {
                                if ($count >= 3) break;
                                $product_result = returnProduct($pid);
                                if ($product_result && $product = $product_result->fetch_assoc()) {
                                    ?>
                                    <a class="cart-preview-item-link" href="product.php?product=<?php echo $product['product_id']; ?>">
                                        <div class="cart-preview-item">
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <div class="cart-preview-item-info">
                                                <div class="cart-preview-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                                <div class="cart-preview-item-price">
                                                    <?php echo number_format($product['price'], 2); ?>€
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
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                <div class="cart-preview-item-info">
                                                    <div class="cart-preview-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                                    <div class="cart-preview-item-price">
                                                        <?php echo number_format($product['price'], 2); ?>€
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
                                echo '<div class="cart-preview-item">Your cart is empty</div>';
                            }
                        }
                        ?>
                    </div>
                </li>
                
                <!-- <li><a href="profile.php"><img src="user.png" class="icons" alt=""></a></li> -->
                <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
                <li><a href="controller/logout.php?from=header"><i class="fas fa-sign-out-alt"></i></a></li>
                <?php if(isAdmin($_SESSION['user_id'])) {echo "<li><a href='managestock.php'><i class='fas fa-wrench'></i></a></li>";} ?>
                <?php }else{ ?>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i></a></li>
                <li><a href="signup.php"><i class="fas fa-user-plus"></i></a></li>
                <?php } ?>
            </ul>
        </nav>
        <div class="mobile-menu">
            <a href="index.php" class="mobile-menu-item">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <?php if(isset($_SESSION['user_id'])) { ?>
            <a href="wishlist.php" class="mobile-menu-item">
                <i class="fas fa-heart"></i>
                <span>Wishlist</span>
            </a>
            <a href="cart.php" class="mobile-menu-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart</span>
            </a>
            <a href="profile.php" class="mobile-menu-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="controller/logout.php?from=header" class="mobile-menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
            <?php if(isAdmin($_SESSION['user_id'])) { ?>
            <a href="managestock.php" class="mobile-menu-item">
                <i class="fas fa-wrench"></i>
                <span>Manage Stock</span>
            </a>
            <?php } ?>
            <?php } else { ?>
            <a href="login.php" class="mobile-menu-item">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
            <a href="signup.php" class="mobile-menu-item">
                <i class="fas fa-user-plus"></i>
                <span>Sign Up</span>
            </a>
            <?php } ?>
        </div>
    </header>
</body>
<script>
    $(document).ready(function() {
        // Clear search functionality
        const searchInput = $('.search-input');
        const clearButton = $('.clear-search');
        
        clearButton.click(function() {
            searchInput.val('').focus();
            $(this).hide();
        });

        searchInput.on('input', function() {
            clearButton.toggle($(this).val().length > 0);
        });

        clearButton.toggle(searchInput.val().length > 0);

        // Mobile menu functionality
        const mobileMenuToggle = $('.mobile-menu-toggle');
        const mobileMenu = $('.mobile-menu');
        const body = $('body');

        mobileMenuToggle.click(function(e) {
            e.stopPropagation();
            mobileMenu.toggleClass('active');
            $(this).toggleClass('active');
        });

        // Close mobile menu when clicking outside
        $(document).click(function(e) {
            if (!$(e.target).closest('.mobile-menu, .mobile-menu-toggle').length) {
                mobileMenu.removeClass('active');
                mobileMenuToggle.removeClass('active');
            }
        });

        // Handle window resize
        let resizeTimer;
        $(window).resize(function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768) {
                    mobileMenu.removeClass('active');
                    mobileMenuToggle.removeClass('active');
                }
            }, 250);
        });

        // Prevent body scroll when mobile menu is open
        mobileMenuToggle.click(function() {
            $('body').toggleClass('menu-open');
        });

        // Close mobile menu when clicking a menu item
        $('.mobile-menu-item').click(function() {
            mobileMenu.removeClass('active');
            mobileMenuToggle.removeClass('active');
            $('body').removeClass('menu-open');
        });

        // Handle cart preview on mobile
        if (window.innerWidth <= 768) {
            $('.cart-link').click(function(e) {
                if (!$(e.target).closest('.cart-preview').length) {
                    e.preventDefault();
                    $('.cart-preview').toggle();
                }
            });
        }
    });
</script>
</html>
