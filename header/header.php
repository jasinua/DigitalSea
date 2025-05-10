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
        --background-color: #f5f5fa;       
        --text-color: white;
        --page-text-color: #232a2f;

        --modal-bg-color: white;
                        
        --button-color: #153147;
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

        --error-color:#F94040
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
        max-width: 500px;
        margin: 0 20px;
        position: relative;
    }

    .search-container form {
        display: flex;
        align-items: center;
    }

    .search-container input[type="text"] {
        width: 100%;
        padding: 10px 15px;
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

    .search-container button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-container button:hover {
        color: var(--mist-color);
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

    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 1000;
        width: 400px !important;
    }
    
    .ui-menu-item {
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        background: none !important;
    }
    
    .ui-menu-item div {
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .ui-menu-item div:hover {
        background-color: #f5f5f5;
    }
    
    .search-suggestion-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .search-suggestion-content {
        flex: 1;
        min-width: 0;
    }
    
    .search-suggestion-title {
        font-weight: 500;
        color: #333;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.9em;
    }
    
    .search-suggestion-price {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9em;
    }
    
    .search-suggestion-final-price {
        color: #333;
        font-weight: 600;
    }
    
    .search-suggestion-original-price {
        color: #e44d26;
        text-decoration: line-through;
        font-size: 0.85em;
    }
    
    .search-suggestion-discount {
        background-color: #e44d26;
        color: white;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.8em;
        font-weight: 600;
    }
</style>
<body>
    <header>
        <div class="imazhiYne" style="margin-left: 1%; padding: 0; margin-top: 0; margin-bottom: 0;"><a href = "index.php"><img class="logo" src="logo2.png" alt="logo e kompanise tone"></a></div>
        <div class="search-container">
            <form action="index.php" method="GET">
                <input type="text" name="search" id="search-input" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <nav>
            <ul>
                <li><a href="index.php"><img src="home.png" class="icons" alt=""></a></li>
                <?php if(isset($_SESSION['user_id'])) { ?>
                <li><a href="wishlist.php"><img src="heart.png" class="icons" alt=""></a></li>
                <li class="cart-link">
                    <a href="cart.php"><img src="shopping-cart.png" class="icons" alt=""></a>
                    <div class="cart-preview">
                        <?php
                        if(isset($_SESSION['user_id'])) {
                            $cart_items = returnCart($_SESSION['user_id']);
                            $count = 0;
                            if($cart_items) {
                                while($count < 3 && ($item = $cart_items->fetch_assoc())) {
                                    $product_result = returnProduct($item['product_id']);
                                    if($product_result && $product = $product_result->fetch_assoc()) {
                                        ?>
                                        <div class="cart-preview-item">
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <div class="cart-preview-item-info">
                                                <div class="cart-preview-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                                <div class="cart-preview-item-price"><?php echo number_format($product['price'], 2); ?>€</div>
                                            </div>
                                        </div>
                                        <?php
                                        $count++;
                                    }
                                }
                            }
                            if($count === 0) {
                                echo '<div class="cart-preview-item">Your cart is empty</div>';
                            }
                        }
                        ?>
                    </div>
                </li>
                <li><a href="profile.php"><img src="user.png" class="icons" alt=""></a></li>
                <?php } ?>
                <li>
                    <label class="auth-menu" for="auth-toggle"><img src="menu.png" class="icons" alt=""></label>
                    <input type="checkbox" id="auth-toggle">
                    <div class="auth-dropdown">
                        <?php if(!isset($_SESSION['user_id'])) { ?>
                            <a href="login.php">Login</a>
                            <a href="signup.php">Signup</a>
                            <?php } else { ?>
                                <a href="profile.php">Profile</a>
                                <a href="logout.php">Log out</a>
                                <?php if(isAdmin($_SESSION['user_id'])) {
                                    echo "<a href='manageStock.php'>Manage stock</a>";
                                } ?>
                        <?php } ?>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
</body>
<script>
$(document).ready(function() {
    $("#search-input").autocomplete({
        minLength: 2,
        source: function(request, response) {
            $.ajax({
                url: "search_suggestions.php",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    if (data.error) {
                        console.error('Search error:', data.error);
                        response([]);
                        return;
                    }
                    response(data);
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    response([]);
                }
            });
        },
        focus: function(event, ui) {
            event.preventDefault();
            $("#search-input").val(ui.item.value);
        },
        select: function(event, ui) {
            event.preventDefault();
            window.location.href = 'product.php?product=' + ui.item.id;
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        let priceHtml = '';
        if (item.discount > 0) {
            priceHtml = `
                <div class="search-suggestion-price">
                    <span class="search-suggestion-final-price">${item.price}&euro;</span>
                    <span class="search-suggestion-original-price">${item.originalPrice}&euro;</span>
                    <span class="search-suggestion-discount">-${item.discount}%</span>
                </div>
            `;
        } else {
            priceHtml = `
                <div class="search-suggestion-price">
                    <span class="search-suggestion-final-price">${item.price}&euro;</span>
                </div>
            `;
        }

        return $("<li>")
            .append("<div>" +
                "<img src='" + (item.image || 'placeholder.jpg') + "' class='search-suggestion-image' alt='" + item.value + "'>" +
                "<div class='search-suggestion-content'>" +
                    "<div class='search-suggestion-title'>" + item.value + "</div>" +
                    priceHtml +
                "</div>" +
            "</div>")
            .appendTo(ul);
    };
});
</script>
</html>
