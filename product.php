<?php
session_start();
include 'controller/home.inc.php';

$productID = $_GET["product"];
$data = getProductData($productID);
$details = getProductDetails($productID);

// Get wishlist items for the current user
$wishlist_items = isset($_SESSION['user_id']) ? getWishlistItems($_SESSION['user_id']) : [];
$is_in_wishlist = in_array($productID, $wishlist_items);

if (isset($_POST['addToCart'])) {
    addToCart($_SESSION['user_id'], $productID, $_POST['quantity'], $_POST['quantity'] * $data['price']);
    header("Location: cart.php");
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['name']) ? htmlspecialchars($data['name']) : 'Product'; ?></title>
</head>
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

    .original-price {
        color: var(--error-color);
        text-decoration: line-through;
        font-size: 14px;
        font-weight: normal;
    }

    .discounted-price {
        color: var(--noir-color);
        font-weight: 600;
        font-size: 1.2em;
    }

    @media (max-width: 1400px) {
        #prodContainer {
            width: 95%;
        }
    }

    @media (max-width: 1200px) {
        #prodContainer {
            flex-direction: column;
            width: 95%;
            max-width: 600px;
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
    }
</style>

<body>
    <?php include 'header/header.php' ?>
    <div id='container'>
        <div id='prodContainer'>
            
            <button class="wishlist-btn <?php echo $is_in_wishlist ? 'active' : ''; ?>" data-product-id="<?php echo $productID; ?>">
                <i class="<?php echo $is_in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
            </button>
            <div style='width:50%;display: flex; justify-content: center; align-items: center; position: relative;'>
                <img id='productImg' src="<?php echo $data['image_url']; ?>" alt="<?php echo $data['description']; ?>" draggable='false'>
                <?php if ($data['discount'] > 0) { ?>
                    <div class="discount-badge">-<?php echo $data['discount'] ?>%</div>
                <?php } ?>
            </div>
            <div id='infoSide'>
                <div id='info'>
                    <p id='name'><?php echo $data['description'] ?></p>
                    <div id='details'>
                        <?php foreach ($details as $detail) { ?>
                            <div class='detail'>
                                <p><?php echo $detail[0] ?>:</p>
                                <p><?php echo $detail[1] ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <form action='product.php?product=<?php echo $productID ?>' id='buyForm' method='post'>
                    <div id='stockWrapper'>
                        <p class='price-label'>Quantity:</p>
                        <div id='controlStock'>
                            <button type='button' class='stockController' onclick='addToQuantity(-1)'>-</button>
                            <input id='stock' value='1' min='1' max='<?php echo $data['stock']; ?>' name='quantity' type="number" placeholder='1'>
                            <button type='button' class='stockController' onclick='addToQuantity(1)'>+</button>
                        </div>
                    </div>

                    <input type="hidden" value='<?php echo $productID ?>' name='prodID'>
                    <div class='price-section'>
                        <div class='price-row'>
                            <p class='price-label'>Price:</p>
                            <?php if ($data['discount'] > 0) { 
                                $originalPrice = $data['price'];
                                $discountedPrice = $originalPrice * (1 - $data['discount'] / 100);
                            ?>
                                <div class='price-value'>
                                    <span class="original-price"><?php echo number_format($originalPrice, 2) ?>€</span>
                                    <span class="discounted-price"><?php echo number_format($discountedPrice, 2) ?>€</span>
                                </div>
                            <?php } else { ?>
                                <p class='price-value'><?php echo number_format($data['price'], 2) ?>€</p>
                            <?php } ?>
                        </div>
                        <input id='buy' type='submit' name='addToCart' value='Add to cart'>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer/footer.php' ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check initial wishlist status
            const productId = <?php echo $productID; ?>;
            $.ajax({
                url: 'controller/check_wishlist.php',
                method: 'POST',
                data: { product_id: productId },
                success: function(response) {
                    if (response.trim() === 'true') {
                        $('.wishlist-btn').addClass('active').find('i').removeClass('far').addClass('fas');
                    }
                }
            });

            // Handle wishlist button click
            $('.wishlist-btn').click(function(e) {
                e.preventDefault();
                const button = $(this);
                
                $.ajax({
                    url: 'controller/add_to_wishlist.php',
                    method: 'POST',
                    data: { product_id: productId },
                    success: function(response) {
                        if (response.trim() === 'added') {
                            button.addClass('active').find('i').removeClass('far').addClass('fas');
                        } else if (response.trim() === 'removed') {
                            button.removeClass('active').find('i').removeClass('fas').addClass('far');
                        }
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
        });

        const price = <?php echo $data['price'] ?>;

        function addToQuantity(add) {
            var amount = parseInt(document.getElementById('stock').value);
            if (amount == 1 && add < 0) {
                document.getElementById('stock').value = 1;
                updatePrice(1);
            } else if (amount >= 1) {
                document.getElementById('stock').value = amount + add;
                updatePrice(amount + add);
            }
        }

        function updatePrice(amount) {
            const totalPrice = (price * amount).toFixed(2);
            document.getElementById('stockPrice').innerHTML = totalPrice + "&euro;";
        }
    </script>
</body>
</html>