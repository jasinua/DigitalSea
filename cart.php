<?php 
include_once "model/dbh.inc.php";
include_once "controller/function.php";

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
                $stmt = $conn->prepare("CALL updateCartItems(?,?,?,?)");
                $stmt->bind_param("idii", $qty, $prc, $userId, $product_id);
                $stmt->execute();
            }
        }
    }
    
    // Handle product removal
    if (isset($_POST['remove'])) {
        $remove_id = (int)$_POST['remove'];
        $stmt = $conn->prepare("CALL removeItemsFromCart(?,?)");
        $stmt->bind_param("ii", $userId, $remove_id);
        $stmt->execute();
    }

    // Handle checkout
    if (isset($_POST['continue'])) {
        header("Location: payment.php");
        exit;
    }

    // Fetch cart again after updates
    $rawCart = returnCart($userId);
    $mergedCart = [];

    // Filter and count items with order_id == null
    $filteredCart = [];
    $count = 0;
    foreach ($rawCart as $item) {
        if (!isset($item['order_id']) || is_null($item['order_id'])) {
            $pid = $item['product_id'];
            if (isset($mergedCart[$pid])) {
                $mergedCart[$pid]['quantity'] += $item['quantity'];
            } else {
                $mergedCart[$pid] = $item;
            }
            $count++;
        }
    }

    $res = array_values($mergedCart);

    include "header/header.php";
?>

<?php include "css/cart-css.php"; ?>

<div class="page-wrapper">
    <div class="save-message">Changes saved successfully!</div>
    <?php if (empty($res)): ?>
        <div class="empty-cart-container" style="text-align: center; padding: 50px 20px;">
            <div class="empty-cart-icon" style="font-size: 48px; margin-bottom: 20px; color: var(--mist-color);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2 style="margin-bottom: 20px; color: var(--mist-color);">Your cart is empty</h2>
            <p style="margin-bottom: 20px; color: var(--mist-color);">Looks like you haven't added any items to your cart yet.</p>
            <a href="index.php" class="continue-shopping-btn">Continue Shopping</a>
        </div>
    <?php else:
        // Check for error messages
        $error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
        echo $error_message;
        unset($_SESSION['error']);
    ?>
    
    <form action="" method="post" id="cartForm">
        <div class="cart-wrapper">

            <!-- Left: Cart Table -->
            <div class="cart-left">
                <div>
                    <table class="table-headers">
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
                                    <td>
                                        <div class="product-info">
                                            <input type="hidden" name="prod_id[]" value="<?php echo $product['product_id']; ?>">
                                            <img src="<?php echo getImageSource($product['product_id'], $product['image_url']); ?>" alt="Product Image">
                                            <div class="product-details">
                                                <h4><?php echo $product['name']; ?></h4>
                                                <div class="desc mobile-desc"><?php echo $product['description']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price-info">
                                            <?php if($discount) { ?>
                                                <span class="discounted-price"><?php echo number_format($pricedsc, 2); ?>€</span>
                                                <span class="original-price"><?php echo number_format($price, 2); ?>€</span>
                                            <?php } else { ?>
                                                <span class="discounted-price"><?php echo number_format($price, 2); ?>€</span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
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
                                    <input type="hidden" name="price[]" value="<?php echo $discount ? $pricedsc : $price; ?>">
                                    <td>
                                        <button class="remove-btn" type="button" data-product-id="<?php echo $product['product_id']; ?>">×</button>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <button type="submit" class="save-btn" id="saveChanges">Save Changes</button>
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
                                                <p class="total-price">
                                                    <span class="summary-qty" data-product-id="<?php echo $product['product_id']; ?>"><?php echo $qty; ?></span>
                                                    x <?php echo number_format($prcwdisc, 2); ?>€ = 
                                                    <span class="summary-total" data-product-id="<?php echo $product['product_id']; ?>"><?php echo number_format($ttldsc, 2); ?>€</span>
                                                </p>
                                            </div>
                                        </div>
                                    <?php } else {?>
                                        <p class="total-price">
                                            <span class="summary-qty" data-product-id="<?php echo $product['product_id']; ?>"><?php echo $qty; ?></span>
                                            x <?php echo number_format($prc, 2); ?>€ = 
                                            <span class="summary-total" data-product-id="<?php echo $product['product_id']; ?>"><?php echo number_format($ttl, 2); ?>€</span>
                                        </p>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div>
                        <div class="summary-item"><span>Subtotal:</span> <span><?php echo number_format($subtotal, 2); ?>€</span></div>
                        <div class="summary-item"><span>VAT 18%:</span> <span><?php echo number_format($subtotal * 0.18, 2); ?>€</span></div>
                        <div class="summary-item"><span>Discount:</span> <span style="color: red">- <?php echo number_format($alldiscount, 2);?>€</span></div>
                        <input class="input-for-discount" type="hidden" name="discount" value="<?php echo $alldiscount; ?>">
                        <div class="summary-item total">
                            <span>Total:</span>
                            <span><?php echo number_format($subtotal + $subtotal * 0.18 - $alldiscount, 2); ?>€</span>
                        </div>
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
    saveBtn.disabled = true;
    const saveMessage = document.querySelector('.save-message');
    let saveTimeout;
    let hasUnsavedChanges = false;
    let originalValues = new Map();
    const checkoutBtn = document.querySelector('.checkout-btn');

    // Store original values when page loads
    quantityInputs.forEach(input => {
        originalValues.set(input.dataset.productId, input.value);
    });

    // Monitor changes in quantity inputs
    quantityInputs.forEach(input => {
        input.addEventListener('change', () => {
            const productId = input.dataset.productId;
            const originalValue = originalValues.get(productId);
            const currentValue = input.value;

            // Update product total and summary immediately
            updateProductTotal(input);

            // Mark as unsaved if the value is different from original
            hasUnsavedChanges = Array.from(originalValues.entries()).some(([pid, origVal]) => {
                const currentInput = document.querySelector(`input[data-product-id="${pid}"]`);
                return currentInput && currentInput.value !== origVal;
            });

            // Update button state
            if (!checkoutBtn.classList.contains('processing')) {
                updateCheckoutButton();
            }
        });
    });

    // Monitor changes in remove buttons
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const row = this.closest('tr');
            const summaryItem = document.querySelector(`.summary-item[data-product-id="${productId}"]`);

            // Disable the button and show loading state
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Send AJAX request to remove item
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `remove=${productId}`
            })
            .then(response => {
                if (response.ok) {
                    // Remove the row from the table
                    row.remove();
                    // Remove the item from summary
                    if (summaryItem) {
                        summaryItem.remove();
                    }
                    // Update cart totals
                    updateCartSummary();
                    // Check if cart is empty
                    if (document.querySelectorAll('.itemsTable tr').length === 0) {
                        location.reload(); // Reload to show empty cart message
                    }
                } else {
                    throw new Error('Failed to remove item');
                }
            })
            .catch(error => {
                this.disabled = false;
                this.innerHTML = '×';
                alert('Error removing item. Please try again.');
            });
        });
    });

    // Update checkout button state
    function updateCheckoutButton() {
        if (hasUnsavedChanges && !checkoutBtn.classList.contains('processing')) {
            checkoutBtn.disabled = true;
            saveBtn.disabled = false;
        } else if (!hasUnsavedChanges) {
            checkoutBtn.disabled = false;
            saveBtn.disabled = true;
        }
    }

    // Handle checkout button click
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.add('processing');
            this.disabled = true;
            this.style.opacity = '0.7';
            this.style.cursor = 'not-allowed';
            this.textContent = 'Processing...';
            window.location.href = 'payment.php';
        });
    }

    // Handle save changes
    saveBtn.addEventListener('click', () => {
        const formData = new FormData(document.getElementById('cartForm'));

        quantityInputs.forEach(input => {
            input.disabled = true;
        });
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.disabled = true;
        });

        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        saveBtn.disabled = true;

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            saveBtn.innerHTML = 'Save Changes';

            quantityInputs.forEach(input => {
                input.disabled = false;
            });
            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.disabled = false;
            });

            saveMessage.classList.add('show');
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                saveMessage.classList.remove('show');
            }, 3000);

            // Update original values to reflect saved state
            quantityInputs.forEach(input => {
                originalValues.set(input.dataset.productId, input.value);
            });

            hasUnsavedChanges = false;
            updateCheckoutButton();
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
        const quantity = parseInt(input.value) || 1; // Default to 1 if invalid
        const price = parseFloat(input.dataset.price);
        const total = (quantity * price).toFixed(2);

        // Update the summary quantity
        const summaryQty = document.querySelector(`.summary-qty[data-product-id="${productId}"]`);
        if (summaryQty) summaryQty.textContent = quantity;

        // Update the summary total
        const summaryTotal = document.querySelector(`.summary-total[data-product-id="${productId}"]`);
        if (summaryTotal) summaryTotal.textContent = total + '€';

        updateCartSummary();
    }

    // Function to update the cart summary totals
    function updateCartSummary() {
        let subtotal = 0;
        let totalDiscount = 0;

        quantityInputs.forEach(input => {
            const productId = input.dataset.productId;
            const quantity = parseInt(input.value) || 1;
            const price = parseFloat(input.dataset.price);

            const summaryItem = document.querySelector(`.summary-item[data-product-id="${productId}"]`);
            if (summaryItem) {
                const discountText = summaryItem.querySelector('.zbritja');
                if (discountText) {
                    const discountMatch = discountText.textContent.match(/\d+/);
                    if (discountMatch) {
                        const discountPct = parseInt(discountMatch[0]);
                        const originalPrice = price / (1 - discountPct / 100);
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

        const summaryItems = document.querySelectorAll('.summary-item:not([data-product-id])');
        if (summaryItems.length >= 3) {
            summaryItems[0].querySelector('span:last-child').textContent = subtotal.toFixed(2) + '€';
            summaryItems[1].querySelector('span:last-child').textContent = tax.toFixed(2) + '€';
            summaryItems[2].querySelector('span:last-child').textContent = '- ' + discount.toFixed(2) + '€';
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
                const fullText = desc.textContent;
                if (fullText.length > 40) {
                    const shortText = fullText.substring(0, 40) + '...';
                    desc.setAttribute('data-full-text', fullText);
                    desc.textContent = shortText;

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

    initResponsive();
    window.addEventListener('resize', initResponsive);
});
</script>

<?php
} else {
    header("Location: login.php");
    exit;
}
?>