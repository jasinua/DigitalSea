<?php 
include_once "model/dbh.inc.php";
include_once "controller/function.php";

function getImageSource($product_id, $image_url) {
    $local_image = "images/product_$product_id.png";
    return file_exists($local_image) ? $local_image : htmlspecialchars($image_url);
}

session_start();

if (isLoggedIn($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Handle cart updates
    if (isset($_POST['prod_id'], $_POST['quantity'], $_POST['price'])) {
        $product_ids = $_POST['prod_id'];
        $quantities = $_POST['quantity'];
        $prices = $_POST['price'];
    
        foreach ($product_ids as $index => $product_id) {
            $qty = (int)$quantities[$index];
            $prc = (float)$prices[$index];
            if ($qty >= 1) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ?, price = ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("idii", $qty, $prc, $userId, $product_id);
                $stmt->execute();
            }
        }
    }
    
    // Handle product removal
    if (isset($_POST['remove'])) {
        $remove_id = (int)$_POST['remove'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $userId, $remove_id);
        $stmt->execute();
    }

    // Handle checkout
    if (isset($_POST['continue'])) {
        // Redirect to checkout page
        header("Location: payment.php");
        exit;
    }

    // Fetch cart again after updates
    $rawCart = returnCart($userId);

    // Filter and count items with order_id == null
    $filteredCart = [];
    $count = 0;
    foreach ($rawCart as $item) {
        if (!isset($item['order_id']) || is_null($item['order_id'])) {
            $filteredCart[] = $item;
            $count++;
        }
    }

    $res = array_values($filteredCart);

    include "header/header.php";
?>

<?php include "css/cart-css.php"; ?>

<div class="page-wrapper">
    <div class="save-message">Ndryshimet u ruajtën me sukses!</div>
    <?php if (empty($res)): ?>
        <div class="empty-cart-container" style="text-align: center; padding: 50px 20px;">
            <div class="empty-cart-icon" style="font-size: 48px; margin-bottom: 20px; color: var(--mist-color);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2 style="margin-bottom: 20px; color: var(--mist-color);">Your cart is empty</h2>
            <p style="margin-bottom: 20px; color: var(--mist-color);">Looks like you haven't added any items to your cart yet.</p>
            <a href="index.php" class="continue-shopping-btn">Continue Shopping</a>
        </div>
    <?php else: ?>
    <form action="" method="post" id="cartForm">
        <div class="cart-wrapper">

            <!-- Left: Cart Table -->
            <div class="cart-left">
                <div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Remove</th>
                    </tr>

                </thead>
            </table>
                <div class="itemsTable" style='max-height: 400px;'>
                    <table>
                            <tbody style='overflow: hidden; overflow-y: auto;'>
                            <?php 
                            $subtotal = 0;
                            foreach ($res as $cart) {
                                $product_result = returnProduct($cart['product_id']);
                                $product = $product_result->fetch_assoc();
                                $discount = $product['discount'];
                                $price = $product['price'];
                                $pricedsc = $price - ($price * $discount/100);
                                $total = $product['price'] * $cart['quantity'];
                                $subtotal += $total;
                            ?>
                            <tr>
                                    <td style="width: 60%;">
                                    <div class="product-info">
                                        <input type="hidden" name="prod_id[]" value="<?php echo $product['product_id']; ?>">
                                        <img src="<?php echo getImageSource($product['product_id'], $product['image_url']); ?>" alt="Product Image">
                                        <div class="product-details">
                                            <h4><?php echo $product['name']; ?></h4>
                                            <div class="desc mobile-desc"><?php echo $product['description']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                    <td style="width: 20%;">
                                        <div class="price-info">
                                            <?php if($discount) { ?>
                                                <span class="discounted-price"><?php echo number_format($pricedsc, 2); ?>€</span>
                                                <span class="original-price"><?php echo number_format($price, 2); ?>€</span>
                                            <?php } else { ?>
                                                <span class="discounted-price"><?php echo number_format($price, 2); ?>€</span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                <td style="width: 10%;">
                                    <div class="quantity-controls">
                                        <input 
                                            type="number" 
                                            name="quantity[]" 
                                            class="quantity-input" 
                                            min="1" 
                                            value="<?php echo $cart['quantity']; ?>" 
                                            data-price="<?php echo $discount ? $pricedsc : $price; ?>"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                        >
                                    </div>
                                </td>
                                <input type="hidden" name="price[]" value="<?php echo $total; ?>">
                                
                                <td style="width: 10%;">
                                    <button class="remove-btn" type="button" data-product-id="<?php echo $product['product_id']; ?>">&times;</button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
                <button type="button" class="save-btn" id="saveChanges">Save Changes</button>
            </div><!-- Save Changes Button -->

            <!-- Right: Summary Box -->
            <div class="cart-right">
                <h3>Order Total:</h3>
                
                <div class="summary-box">
                    <div>
                        <div id="prodNameXprice">
                            <?php 
                                $subtotal = 0;
                                $alldiscount = 0;
                                foreach ($res as $cartItem) {
                                    $product = returnProduct($cartItem['product_id'])->fetch_assoc();
                                    $qty = $cartItem['quantity'];
                                    $prc = $product['price'];
                                    $discount = $product['discount'];
                                    $prcwdisc = $prc - ($prc * $discount/100);
                                    $ttldsc = $qty * $prcwdisc;
                                    $alldiscount += ($prc * $discount/100) * $qty;
                                    $ttl = $qty * $prc;
                                    $subtotal += $ttl;
                                ?>

                                <div class="summary-item" data-product-id="<?php echo $product['product_id']; ?>">
                                    <div class="emri-me-zbritje">
                                        <p class="product-name"><?php echo $product['name']; ?> </p>
                                        <?php if($discount > 0){ ?> 
                                                <p class="zbritja">/ -<?php echo $discount;?>%</p>
                                        <?php } ?>
                                    </div>
                                    <?php if($discount > 0){?>
                                        <div class="me-zbritje" style="margin-top: 0">
                                            <div>
                                                <p class="total-price"><?php echo $qty; ?> x <?php echo number_format($prcwdisc, 2); ?>€ = <?php echo number_format($ttldsc, 2); ?>€</p>
                                            </div>
                                        </div>
                                    <?php } else {?>
                                            <p class="total-price"><?php echo $qty; ?> x <?php echo number_format($prc, 2); ?>€ = <?php echo number_format($ttl, 2); ?>€</p>
                                        <?php } ?>
                                    </div>
                            <?php } ?>
                        </div>
                        
                        <div class="summary-item"><span>Subtotal:</span> <span><?php echo number_format($subtotal, 2); ?>€</span></div>
                        <div class="summary-item"><span>VAT 18%:</span> <span><?php echo number_format($subtotal * 0.18, 2); ?>€</span></div>
                        <div class="summary-item"><span>Discount:</span> <span style="color: red">- <?php echo number_format($alldiscount, 2);?>€</span></div>
                        <input class="input-for-discount" type="hidden" name="discount" value="<?php echo $alldiscount; ?>">
                    </div>
                    <div>
                        <div class="summary-item total">
                            <span>Total:</span>
                            <span><?php echo number_format($subtotal + $subtotal * 0.18 - $alldiscount, 2); ?>€</span>
                        </div>
                        <!-- Checkout Button -->
                        <button class="checkout-btn" type="submit" name="continue">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include "footer/footer.php" ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const saveBtn = document.getElementById('saveChanges');
        const saveMessage = document.querySelector('.save-message');
        let saveTimeout;
        let hasUnsavedChanges = false;
        let originalValues = new Map();
        const checkoutBtn = document.querySelector('.checkout-btn');

        // Store original values when page loads
        document.querySelectorAll('.quantity-controls input').forEach(input => {
            originalValues.set(input.dataset.productId, input.value);
        });

        // Monitor changes in quantity inputs
        document.querySelectorAll('.quantity-controls input').forEach(input => {
            input.addEventListener('change', () => {
                const productId = input.dataset.productId;
                const originalValue = originalValues.get(productId);
                const currentValue = input.value;
                
                // Only mark as unsaved if the value is different from original
                hasUnsavedChanges = Array.from(originalValues.entries()).some(([pid, origVal]) => {
                    const currentInput = document.querySelector(`input[data-product-id="${pid}"]`);
                    return currentInput && currentInput.value !== origVal;
                });
                
                updateCheckoutButton();
            });
        });

        // Monitor changes in remove buttons
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                hasUnsavedChanges = true;
                updateCheckoutButton();
            });
        });

        // Update checkout button state
        function updateCheckoutButton() {
            checkoutBtn.disabled = hasUnsavedChanges;
        }

        // Reset unsaved changes after saving
        saveBtn.addEventListener('click', () => {
            // Update original values after saving
            document.querySelectorAll('.quantity-controls input').forEach(input => {
                originalValues.set(input.dataset.productId, input.value);
            });
            hasUnsavedChanges = false;
            updateCheckoutButton();
        });

        // Initial state
        updateCheckoutButton();

        // Handle remove buttons
        const removeButtons = document.querySelectorAll('.remove-btn');
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const row = this.closest('tr');
                
                // Show loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                
                // Send AJAX request to remove item
                $.ajax({
                    url: 'controller/remove_from_cart.php',
                    type: 'POST',
                    data: { product_id: productId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Fade out and remove the row
                            row.style.opacity = '0.5';
                            setTimeout(() => {
                                row.remove();
                                
                                // Remove the item from the summary
                                const summaryItem = document.querySelector(`.summary-item[data-product-id="${productId}"]`);
                                if (summaryItem) {
                                    summaryItem.remove();
                                }
                                
                                // Update cart summary
                                updateCartSummary();
                                
                                // Update cart count in header if available
                                if (response.cartCount !== undefined) {
                                    const cartCount = document.querySelector('.cart-count');
                                    if (cartCount) {
                                        cartCount.textContent = response.cartCount;
                                    }
                                }
                                
                                // Show empty cart message if no items left
                                const remainingItems = document.querySelectorAll('tbody tr');
                                if (remainingItems.length === 0) {
                                    const cartTable = document.querySelector('.itemsTable');
                                    cartTable.innerHTML = '<div class="empty-cart">Your cart is empty. <a href="index.php">Continue shopping</a></div>';
                                    
                                    const summaryBox = document.querySelector('#prodNameXprice');
                                    summaryBox.innerHTML = '<div class="empty-cart-summary">There are no products in the cart.</div>';
                                    
                                    // Update totals
                                    updateCartTotals(0, 0);
                                }
                            }, 300);
                        } else {
                            // Show error
                            button.innerHTML = '&times;';
                            button.disabled = false;
                            alert('Error deleting product. Please try again.');
                        }
                    },
                    error: function() {
                        button.innerHTML = '&times;';
                        button.disabled = false;
                        alert('Error deleting product. Please try again.');
                    }
                });
            });
        });

        // Update quantity inputs
        quantityInputs.forEach(input => {
            input.addEventListener('change', () => {
                const min = parseInt(input.getAttribute('min'));
                const val = parseInt(input.value);
                if (val < min) {
                    input.value = min;
                }
                
                // Highlight save button to indicate unsaved changes
                // saveBtn.style.backgroundColor = '#ff6b6b';
                // saveBtn.textContent = 'Ruaj Ndryshimet';
                
                updateProductTotal(input);
            });
        });
        
        // Handle save changes button
        saveBtn.addEventListener('click', () => {
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;
            
            // Submit the form via AJAX
            const formData = new FormData(document.getElementById('cartForm'));
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                saveBtn.innerHTML = 'Save Changes';
                saveBtn.disabled = false;
                saveBtn.style.backgroundColor = 'var(--button-color)';
                
                // Show save message
                saveMessage.classList.add('show');
                
                // Hide message after 3 seconds
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    saveMessage.classList.remove('show');
                }, 3000);
                
                // Update cart totals based on current values
                updateCartSummary();
            })
            .catch(error => {
                saveBtn.innerHTML = 'Save Changes';
                saveBtn.disabled = false;
                alert('Error saving changes. Please try again.');
            });
        });
        
        // Function to update product total in summary
        function updateProductTotal(input) {
            const productId = input.dataset.productId;
            const quantity = parseInt(input.value);
            const price = parseFloat(input.dataset.price);
            const total = (quantity * price).toFixed(2);
            
            // Update the summary item
            const summaryItem = document.querySelector(`.summary-item[data-product-id="${productId}"] .total-price`);
            if (summaryItem) {
                // Extract the price pattern and keep it the same, just update quantity and total
                const priceText = summaryItem.textContent;
                const newText = priceText.replace(/^\d+/, quantity).replace(/\d+\.\d+€$/, total + '€');
                summaryItem.textContent = newText;
            }

            updateCartSummary();
        }
        
        // Function to update the cart summary totals
        function updateCartSummary() {
            let subtotal = 0;
            let totalDiscount = 0;
            
            // Calculate subtotal and discount from current values
            document.querySelectorAll('.quantity-input').forEach(input => {
                const productId = input.dataset.productId;
                const quantity = parseInt(input.value);
                const price = parseFloat(input.dataset.price);
                
                // Find if this product has a discount
                const summaryItem = document.querySelector(`.summary-item[data-product-id="${productId}"]`);
                if (summaryItem) {
                    const discountText = summaryItem.querySelector('.zbritja');
                    if (discountText) {
                        // Extract discount percentage
                        const discountMatch = discountText.textContent.match(/\d+/);
                        if (discountMatch) {
                            const discountPct = parseInt(discountMatch[0]);
                            // Calculate original price and discount amount
                            const originalPrice = price / (1 - discountPct/100);
                            const discountAmount = originalPrice - price;
                            totalDiscount += discountAmount * quantity;
                            subtotal += originalPrice * quantity;
                        } else {
                            subtotal += price * quantity;
                        }
                    } else {
                        subtotal += price * quantity;
                    }
                }
            });
            
            updateCartTotals(subtotal, totalDiscount);
        }
        
        // Update the cart totals in the UI
        function updateCartTotals(subtotal, discount) {
            const tax = subtotal * 0.18;
            const total = subtotal + tax - discount;
            
            // Update the summary values
            const summaryItems = document.querySelectorAll('.summary-item:not([data-product-id])');
            if (summaryItems.length >= 3) {
                // Subtotal
                summaryItems[0].querySelector('span:last-child').textContent = subtotal.toFixed(2) + '€';
                // Tax
                summaryItems[1].querySelector('span:last-child').textContent = tax.toFixed(2) + '€';
                // Discount
                summaryItems[2].querySelector('span:last-child').textContent = '- ' + discount.toFixed(2) + '€';
                // Total
                const totalElement = document.querySelector('.summary-item.total span:last-child');
                if (totalElement) {
                    totalElement.textContent = total.toFixed(2) + '€';
                }
            }
        }
        
        // Initialize responsive elements
        function initResponsive() {
            const isMobile = window.innerWidth <= 580;
            const descriptionElements = document.querySelectorAll('.mobile-desc');
            
            descriptionElements.forEach(desc => {
                if (isMobile) {
                    // Truncate description on mobile
                    const fullText = desc.textContent;
                    if (fullText.length > 40) {
                        const shortText = fullText.substring(0, 40) + '...';
                        desc.setAttribute('data-full-text', fullText);
                        desc.textContent = shortText;
                        
                        // Add click handler to expand/collapse
                        desc.addEventListener('click', function() {
                            const isExpanded = this.classList.contains('expanded');
                            if (isExpanded) {
                                this.textContent = shortText;
                                this.classList.remove('expanded');
                            } else {
                                this.textContent = this.getAttribute('data-full-text');
                                this.classList.add('expanded');
                            }
                        });
                    }
                }
            });
        }
        
        // Run on page load
        initResponsive();
        
        // Listen for window resize events
        window.addEventListener('resize', initResponsive);
    });
</script>

<?php
} else {
    header("Location: login.php");
    exit;
}
?>
