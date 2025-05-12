<?php 
include_once "controller/function.php";
include_once "controller/home.inc.php";
session_start();


if (isLoggedIn($_SESSION['user_id'])) {
    $res = returnWishList($_SESSION['user_id']);
    include "header/header.php";

    // We'll remove the PHP form processing for adding to cart since we'll use AJAX
    // if(isset($_POST['add'])) {
    //     $result = addToCart($_SESSION['user_id'], $_POST['product_id'], 1, $_POST['price']);
    //     if($result) {
    //         $_SESSION['show_cart_notification'] = true;
    //     }
    // }
?>
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
<div class="page-wrapper">
    <div class="remove-notification" style="display:none;"></div>
    <div class="wishlist-container">
        <div class="wishlist-header">
            <h2>My Wishlist ✎</h2>
            <span class="wishlist-count">
                <?php 
                $count = 0;
                if($res) {
                    $count = $res->num_rows;
                }
                echo $count . ' ' . ($count == 1 ? 'item' : 'items');
                ?>
            </span>
        </div>

        <?php if($res && $res->num_rows > 0): ?>
        <div class="wishlist-grid">
            <?php 
            while($wishlist = $res->fetch_assoc()) {
                $product_result = returnProduct($wishlist['product_id']);
                if($product_result && $product = $product_result->fetch_assoc()) {
                    $discount = isset($product['discount']) ? $product['discount'] : 0;
            ?>
                <div class="wishlist-item">
                    <form method="post" action="controller/remove_from_wishlist.php" class="remove-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <button class="remove-btn" type="submit">&times;</button>
                    </form>

                    <a href="product.php?product=<?php echo $product['product_id'];?>" class="product-link">
                        <div class="product-info">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="name"><?php echo htmlspecialchars($product['name']); ?></div>
                        </div>
                    </a>
                    <!-- <div class="product-details">
                        
                    </div> -->
                    <div class="product-details">
                        <div class="stock-status <?php echo ($product['stock'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                            <?php echo ($product['stock'] > 0) ? 'In Stock' : 'Out of Stock'; ?>
                        </div>
                        <div class="product-price-container">
                            <?php 
                            if($discount > 0) {
                                $original_price = $product['price'];
                                $discounted_price = $original_price * (1 - $discount/100);
                                ?>
                                <span class="original-price"><?php echo number_format($original_price, 2); ?>€</span>
                                <span class="product-price"><?php echo number_format($discounted_price, 2); ?>€</span>
                                    <?php } else { ?>
                                <span class="product-price"><?php echo number_format($product['price'], 2); ?>€</span>
                            <?php } ?>
                        </div>
                    </div>
                    <form method="post" action="" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="price" value="<?php echo ($discount > 0) ? $discounted_price : $product['price']; ?>">
                        <button type="button" class="add-to-cart-btn" data-product-id="<?php echo $product['product_id']; ?>" data-price="<?php echo ($discount > 0) ? $discounted_price : $product['price']; ?>" <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>
                            Add to Cart
                        </button>
                    </form>
                </div>
            <?php 
                }
            }
            ?>
        </div>
        <?php else: ?>
        <div class="empty-wishlist">
            <i class="fas fa-heart"></i>
            <p>Your wishlist is empty</p>
            <a href="index.php" class="continue-shopping">Continue Shopping</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['show_cart_notification']) && $_SESSION['show_cart_notification']): ?>
    const cartLink = document.querySelector('a[href="cart.php"]');
    const notification = document.createElement('span');
    notification.className = 'cart-notification';
    cartLink.classList.add('cart-link');
    cartLink.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'scale(0.5)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);

    <?php 
    unset($_SESSION['show_cart_notification']);
    ?>
    <?php endif; ?>

    // Add clear-search button logic for wishlist page
    $(document).ready(function() {
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

        // Handle remove from wishlist
        $('.wishlist-grid').on('submit', '.remove-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $item = $form.closest('.wishlist-item');
            var productId = $form.find('input[name="product_id"]').val();

            $.ajax({
                url: 'controller/remove_from_wishlist.php',
                type: 'POST',
                data: { product_id: productId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            showRemoveNotification('Item removed from wishlist.');
                        });
                    }
                }
            });
        });

        function showRemoveNotification(message) {
            var $notif = $('.remove-notification');
            $notif.text(message).addClass('show').show();
            setTimeout(function() {
                $notif.removeClass('show').fadeOut(400);
            }, 2500);
        }

        // Attach AJAX handler to all "Add to Cart" buttons
        $('.add-to-cart-btn').click(function(e) {
            e.preventDefault();
            
            var productId = $(this).data('product-id');
            var price = $(this).data('price');
            var button = $(this);
            
            // Change button text/style to indicate loading
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Adding...');
            
            // Send AJAX request to add product to cart
            $.ajax({
                url: 'controller/add_to_cart.php',
                type: 'POST',
                data: {
                    product_id: productId,
                    price: price
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update cart count in header
                        if (response.cartCount) {
                            $('.cart-count').text(response.cartCount);
                        }
                        
                        // Show success message
                        button.html('<i class="fas fa-check"></i> Added to Cart');
                        setTimeout(function() {
                            button.html('Add to Cart');
                            button.prop('disabled', false);
                        }, 2000);
                        
                        // Update cart preview
                        updateCartPreview();
                        
                        // Add notification dot to cart icon
                        const cartLink = document.querySelector('a[href="cart.php"]');
                        if (cartLink && !cartLink.querySelector('.cart-notification')) {
                            const notification = document.createElement('span');
                            notification.className = 'cart-notification';
                            cartLink.classList.add('cart-link');
                            cartLink.appendChild(notification);
                            
                            // Remove notification after a few seconds
                            setTimeout(() => {
                                notification.style.opacity = '0';
                                notification.style.transform = 'scale(0.5)';
                                setTimeout(() => {
                                    notification.remove();
                                }, 300);
                            }, 3000);
                        }
                    } else {
                        // Show error message
                        button.html('<i class="fas fa-times"></i> Failed');
                        console.error(response.message);
                        setTimeout(function() {
                            button.html('Add to Cart');
                            button.prop('disabled', false);
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    button.html('<i class="fas fa-times"></i> Error');
                    console.error('AJAX Error: ' + status + ' - ' + error);
                    setTimeout(function() {
                        button.html('Add to Cart');
                        button.prop('disabled', false);
                    }, 2000);
                }
            });
        });
        
        // Function to update cart preview
        function updateCartPreview() {
            console.log('Updating cart preview...');
            
            $.ajax({
                url: 'controller/get_cart_preview.php',
                type: 'GET',
                dataType: 'html',
                beforeSend: function() {
                    console.log('Sending request to get_cart_preview.php...');
                    // Show loading content in the preview
                    $('.cart-preview').html('<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                    // Don't show the preview
                },
                success: function(response) {
                    console.log('Cart preview response received:', response.length, 'characters');
                    
                    if (response.trim() === '') {
                        console.error('Empty response received from cart preview');
                        $('.cart-preview').html('<div class="empty-cart-message">Error loading cart preview</div>');
                        return;
                    }
                    
                    // Replace cart preview content
                    $('.cart-preview').html(response);
                    console.log('Cart preview HTML updated');
                    
                    // Ensure the parent li has the cart-link class for hover functionality
                    const cartLi = $('a[href="cart.php"]').closest('li');
                    if (!cartLi.hasClass('cart-link')) {
                        cartLi.addClass('cart-link');
                        console.log('Added cart-link class to parent li');
                    }
                    
                    // Add mouseenter/mouseleave handlers to ensure preview works
                    cartLi.off('mouseenter mouseleave'); // Remove existing handlers to prevent duplicates
                    cartLi.on('mouseenter', function() {
                        console.log('Mouse entered cart link');
                        $(this).find('.cart-preview').stop().fadeIn(200);
                    }).on('mouseleave', function() {
                        console.log('Mouse left cart link');
                        $(this).find('.cart-preview').stop().fadeOut(200);
                    });
                    
                    // Don't show the cart preview automatically, only on hover
                    $('.cart-preview').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Error updating cart preview:', status, error);
                    console.log('Response text:', xhr.responseText);
                    $('.cart-preview').html('<div class="empty-cart-message">Error loading cart preview</div>');
                }
            });
        }
    });
});
</script>

<?php include "footer/footer.php"; ?>

<?php } else { header("Location: login.php"); } ?>
