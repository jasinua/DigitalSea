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

<?php include "css/product-css.php"; ?>

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
                                    <span id="original-price"><?php echo number_format($originalPrice, 2) ?>€</span>
                                    <span id="discounted-price"><?php echo number_format($discountedPrice, 2) ?>€</span>
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
        const discount = <?php echo $data['discount'] ?>;

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
            <?php if ($data['discount'] > 0) { ?>
                const discountedPrice = (price * (1 - discount/100) * amount).toFixed(2);
                document.getElementById('discounted-price').innerHTML = discountedPrice + "&euro;";
                document.getElementById('original-price').innerHTML = totalPrice + "&euro;";
            <?php } else { ?>
                document.querySelector('.price-value').innerHTML = totalPrice + "&euro;";
            <?php } ?>
        }
    </script>
</body>
</html>
