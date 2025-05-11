<?php 
include_once "controller/function.php";
include_once "controller/home.inc.php";
session_start();


if (isLoggedIn($_SESSION['user_id'])) {
    $res = returnWishList($_SESSION['user_id']);
    include "header/header.php";

    if(isset($_POST['add'])) {
        $result = addToCart($_SESSION['user_id'], $_POST['product_id'], 1, $_POST['price']);
        if($result) {
            $_SESSION['show_cart_notification'] = true;
        }
    }
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
            grid-template-columns: repeat(3, minmax(320px, 1fr));
        }
    }
    @media (max-width: 1000px) {
        .wishlist-grid {
            grid-template-columns: repeat(2, minmax(320px, 1fr));
        }
    }
    @media (max-width: 700px) {
        .wishlist-grid {
            grid-template-columns: 1fr;
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

    // Handle add to cart button clicks
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const price = this.dataset.price;
            
            // Disable button to prevent double clicks
            this.disabled = true;
            this.textContent = 'Adding...';
            
            fetch('controller/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&price=${price}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    const cartLink = document.querySelector('a[href="cart.php"]');
                    const notification = document.createElement('span');
                    notification.className = 'cart-notification';
                    cartLink.classList.add('cart-link');
                    cartLink.appendChild(notification);

                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'scale(0.5)';
                        setTimeout(() => {
                            notification.remove();
                        }, 300);
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Error adding to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error adding to cart. Please try again.');
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.textContent = 'Add to Cart';
            });
        });
    });
});

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
});
</script>

<?php include "footer/footer.php"; ?>

<?php } else {    header("Location: index.php"); } ?>
