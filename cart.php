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
                                        <div>
                                            <h4><?php echo $product['name']; ?></h4>
                                            <div class="desc"><?php echo $product['description']; ?></div>
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
                                        <p><?php echo $product['name']; ?> </p>
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
                        <div class="summary-item">
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
                                
                                // Update cart preview
                                updateCartPreview();
                                
                                // Show empty cart message if no items left
                                const rows = document.querySelectorAll('tbody tr');
                                if (rows.length === 0) {
                                    const table = document.querySelector('.itemsTable');
                                    table.innerHTML = '<div class="empty-cart-message" style="padding: 30px; text-align: center; color: #888;">Your cart is empty</div>';
                                    document.querySelector('.save-btn').style.display = 'none';
                                    document.querySelector('.checkout-btn').disabled = true;
                                }
                                
                                // Show success message
                                saveMessage.textContent = 'Item removed successfully';
                                saveMessage.classList.add('show');
                                clearTimeout(saveTimeout);
                                saveTimeout = setTimeout(() => {
                                    saveMessage.classList.remove('show');
                                }, 3000);
                            }, 300);
                        } else {
                            console.error('Failed to remove item:', response.message);
                            button.innerHTML = '&times;';
                            button.disabled = false;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        button.innerHTML = '&times;';
                        button.disabled = false;
                    }
                });
            });
        });

        quantityInputs.forEach(input => {
            input.addEventListener('input', () => {
                const price = parseFloat(input.dataset.price);
                const productId = input.dataset.productId;
                const quantity = parseInt(input.value) || 1;
                const total = price * quantity;

                const summaryItem = document.querySelector(`.summary-item[data-product-id="${productId}"] .total-price`);
                if (summaryItem) {
                    summaryItem.textContent = `${quantity} x ${price.toFixed(2)}€ = ${total.toFixed(2)}€`;
                }
                updateCartSummary();
            });
        });

        saveBtn.addEventListener('click', () => {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Duke ruajtur...';

            const formData = new FormData();
            quantityInputs.forEach(input => {
                formData.append('prod_id[]', input.dataset.productId);
                formData.append('quantity[]', input.value);
                formData.append('price[]', parseFloat(input.dataset.price) * parseInt(input.value));
            });

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                saveBtn.textContent = 'Ruaj Ndryshimet';
                saveBtn.disabled = false;

                saveMessage.classList.add('show');
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    saveMessage.classList.remove('show');
                    // window.location.reload(); 
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                saveBtn.textContent = 'Ruaj Ndryshimet';
                saveBtn.disabled = false;
            });
        });

        function updateCartSummary() {
            const summaryTotals = document.querySelectorAll('.summary-item .total-price');
            let subtotal = 0;
            let totalDiscount = 0;

            summaryTotals.forEach(item => {
                const cleanedText = item.textContent.replace(/,/g, '');
                const match = cleanedText.match(/= ([\d.]+)/);
                if (match && match[1]) {
                    subtotal += parseFloat(match[1]);
                }
            });

            quantityInputs.forEach(input => {
                const productId = input.dataset.productId;
                const quantity = parseInt(input.value) || 1;
                const priceWithDiscount = parseFloat(input.dataset.price);
                const summaryItem = document.querySelector(`.summary-item[data-product-id="${productId}"]`);
                const originalPriceText = summaryItem.querySelector('.zbritja')?.textContent || '';
                const discountMatch = originalPriceText.match(/\-([\d.]+)%/);

                if (discountMatch && discountMatch[1]) {
                    const discountPercent = parseFloat(discountMatch[1]);
                    const originalPrice = priceWithDiscount / (1 - discountPercent / 100);
                    const discountPerItem = (originalPrice - priceWithDiscount) * quantity;
                    totalDiscount += discountPerItem;
                }
            });

            const tvsh = subtotal * 0.18;
            const finalTotal = subtotal + tvsh - totalDiscount;

            const summaryItems = document.querySelectorAll('.summary-item');
            summaryItems[summaryItems.length - 4].querySelector('span:last-child').textContent = subtotal.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "€";
            summaryItems[summaryItems.length - 3].querySelector('span:last-child').textContent = tvsh.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "€";
            summaryItems[summaryItems.length - 2].querySelector('span:last-child').textContent = totalDiscount > 0 ? `- ${totalDiscount.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}€` : "0.00€";
            summaryItems[summaryItems.length - 1].querySelector('span:last-child').textContent = finalTotal.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "€";
        }

        // Function to update cart preview via AJAX
        function updateCartPreview() {
            $.ajax({
                url: 'controller/get_cart_preview.php',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    // Replace cart preview content in header
                    $('.cart-preview').html(response);
                    
                    // Ensure the parent li has cart-link class
                    const cartLi = $('a[href="cart.php"]').closest('li');
                    if (!cartLi.hasClass('cart-link')) {
                        cartLi.addClass('cart-link');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating cart preview:', error);
                }
            });
        }

        $('.search-input').on('input', function() {
            var $clearBtn = $(this).closest('form').find('.clear-search');
            if ($(this).val().length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }
        });

        $('.clear-search').on('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $input = $(this).closest('form').find('.search-input, .mobile-search-input');
            $input.val('');
            $(this).hide();
            $input.focus();
        });

        $('.search-input, .mobile-search-input').each(function() {
            var $clearBtn = $(this).closest('form').find('.clear-search');
            if ($(this).val().length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }
        });
    });
</script>
<?php } else { header("Location: login.php"); } ?>
