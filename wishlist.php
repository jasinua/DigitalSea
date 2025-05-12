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

<?php include "css/wishlist-css.php"; ?>

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
