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
    $mergedCart = [];
    
    foreach ($rawCart as $item) {
        $pid = $item['product_id'];
        if (isset($mergedCart[$pid])) {
            $mergedCart[$pid]['quantity'] = $item['quantity'];
        } else {
            $mergedCart[$pid] = $item;
        }
    }
    $res = array_values($mergedCart);

    

    include "header/header.php";
?>

<?php include "css/cart-css.php"; ?>

<div class="page-wrapper">
    <div class="save-message">Ndryshimet u ruajtën me sukses!</div>
    <form action="" method="post" id="cartForm">
        <div class="cart-wrapper">

            <!-- Left: Cart Table -->
            <div class="cart-left">
                <div>
            <table>
                <thead>
                    <tr>
                        <th>Produkti</th>
                        <th>Çmimi</th>
                        <th>Sasia</th>
                        <th>Fshi</th>
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
                                    <td style="width: 304px;">
                                    <div class="product-info">
                                        <input type="hidden" name="prod_id[]" value="<?php echo $product['product_id']; ?>">
                                        <img src="<?php echo $product['image_url']; ?>" alt="Product Image">
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
                                <input type="hidden" name="price[]" value="<?php echo $total; ?>">
                                
                                <td>
                                    <button class="remove-btn" type="button" data-product-id="<?php echo $product['product_id']; ?>">&times;</button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
                <button type="button" class="save-btn" id="saveChanges">Ruaj Ndryshimet</button>
            </div><!-- Save Changes Button -->

            <!-- Right: Summary Box -->
            <div class="cart-right">
                <h3>Totali i porosisë:</h3>
                
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
                        
                        <div class="summary-item"><span>Nëntotali:</span> <span><?php echo number_format($subtotal, 2); ?>€</span></div>
                        <div class="summary-item"><span>TVSH 18%:</span> <span><?php echo number_format($subtotal * 0.18, 2); ?>€</span></div>
                        <div class="summary-item"><span>Zbritje:</span> <span style="color: red">- <?php echo number_format($alldiscount, 2);?>€</span></div>
                        <input class="input-for-discount" type="hidden" name="discount" value="<?php echo $alldiscount; ?>">
                    </div>
                    <div>
                        <div class="summary-item total">
                            <span>Total:</span>
                            <span><?php echo number_format($subtotal + $subtotal * 0.18 - $alldiscount, 2); ?>€</span>
                        </div>
                        <!-- Checkout Button -->
                        <button class="checkout-btn" type="submit" name="continue">Vazhdo ne checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include "footer/footer.php" ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const saveBtn = document.getElementById('saveChanges');
        const saveMessage = document.querySelector('.save-message');
        let saveTimeout;

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
                                    cartTable.innerHTML = '<div class="empty-cart">Karta juaj është bosh. <a href="index.php">Vazhdo blerjet</a></div>';
                                    
                                    const summaryBox = document.querySelector('#prodNameXprice');
                                    summaryBox.innerHTML = '<div class="empty-cart-summary">Nuk ka produkte në kartë.</div>';
                                    
                                    // Update totals
                                    updateCartTotals(0, 0);
                                }
                            }, 300);
                        } else {
                            // Show error
                            button.innerHTML = '&times;';
                            button.disabled = false;
                            alert('Gabim gjatë fshirjes së produktit. Ju lutem provoni përsëri.');
                        }
                    },
                    error: function() {
                        button.innerHTML = '&times;';
                        button.disabled = false;
                        alert('Gabim gjatë komunikimit me serverin. Ju lutem provoni përsëri.');
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
                saveBtn.style.backgroundColor = '#ff6b6b';
                saveBtn.textContent = 'Ruaj Ndryshimet';
                
                updateProductTotal(input);
            });
        });
        
        // Handle save changes button
        saveBtn.addEventListener('click', () => {
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Duke Ruajtur...';
            saveBtn.disabled = true;
            
            // Submit the form via AJAX
            const formData = new FormData(document.getElementById('cartForm'));
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                saveBtn.innerHTML = 'Ruaj Ndryshimet';
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
                saveBtn.innerHTML = 'Ruaj Ndryshimet';
                saveBtn.disabled = false;
                alert('Gabim gjatë ruajtjes së ndryshimeve. Ju lutem provoni përsëri.');
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
