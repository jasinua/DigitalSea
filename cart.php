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
<style>
    .page-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: calc(100vh - 120px);
        background-color: var(--ivory-color);
        padding: 20px;
    }

    .cart-wrapper {
        display: flex;
        width: 100%;
        max-width: 1400px;
        gap: 20px;
        margin: auto;
    }

    .cart-left, .cart-right {
        background-color: white;
        color: var(--page-text-color);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 20px;
        min-height: 400px;
    }

    .cart-left {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex: 1 1 65%;
        max-height: 600px;
    }

    .itemsTable {
        max-height: 500px;
        overflow-y: auto;
        margin-bottom: 20px;
    }

    .cart-right {
        display: flex;
        flex-direction: column;
        flex: 1 1 35%;
        max-height: 600px;
    }

    .cart-right h3 {
        padding: 15px 0;
        margin: 0;
        font-size: 1.2em;
        color: var(--noir-color);
        border-bottom: 1px solid #eee;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    thead tr {
        background-color: white;
    }

    th, td {
        padding: 12px 15px;
        color: var(--page-text-color);
        text-align: center;
        vertical-align: middle;
    }

    th {
        font-weight: 500;
        color: var(--noir-color);
    }

    th:nth-child(1) { width: 40%; }
    th:nth-child(2) { width: 20%; }
    th:nth-child(3) { width: 20%; }
    th:nth-child(4) { width: 20%; }

    td:first-child {
        text-align: left;
    }

    tr{
        border-bottom: 1px solid #eee;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
    }

    .product-info img {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border-radius: 8px;
        background-color: white;
        padding: 5px;
        flex-shrink: 0;
    }

    .product-info > div {
        flex: 1;
        min-width: 0;
    }

    .product-info h4 {
        margin: 0;
        font-size: 0.95rem;
        color: var(--noir-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-info .desc {
        font-size: 0.85rem;
        color: #666;
        margin-top: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .price-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .price-info .original-price {
        color: red;
        font-size: 0.85rem;
        text-decoration: line-through;
    }

    .price-info .discounted-price {
        font-weight: 500;
        color: var(--noir-color);
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-right: 40px;
    }

    .quantity-controls input {
        width: 45px;
        height: 32px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        background-color: white;
    }

    .remove-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #888;
        cursor: pointer;
        transition: color 0.2s;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: auto;
        margin-right: 50px;
    }

    .remove-btn:hover {
        color: #ff4444;
    }

    td{
        justify-content: center;
        align-items: center;
        display: flex;
    }

    tr{
        display: flex;
        justify-content: space-between;
    }

    .summary-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 20px;
        flex: 1; 
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 0.95rem;
        color: #555;
    }

    .summary-item.total {
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--noir-color);
        border-top: 1px solid #eee;
        padding-top: 15px;
        margin-top: 5px;
    }

    .emri-me-zbritje {
        display: flex; 
        justify-content: space-between;
    }

    .zbritja {
        margin-left: 20px;
    }

    .me-zbritje {
        display: flex;
        flex-direction: column;
    }

    .checkout-btn {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        margin-top: auto;
        transition: all 0.3s;
    }

    .checkout-btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    #prodNameXprice {
        overflow-y: auto;
        max-height: 200px;
    }

    @media (max-width: 1200px) {
        .cart-wrapper {
            flex-direction: column;
            max-width: 800px;
        }

        .cart-left, .cart-right {
            width: 100%;
            max-height: none;
        }

        .itemsTable {
            max-height: 400px;
        }
    }

    @media (max-width: 850px) {
        .cart-wrapper {
            max-width: 95%;
        }
    }

    .save-btn {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 20px;
        transition: all 0.3s;
    }

    .save-btn:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }

    .save-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .save-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        background-color: #4CAF50;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-100%);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .save-message.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>
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
                            <tbody style='overflow:hidden;overflow-y: auto;'>
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
                                    <button class="remove-btn" type="submit" name="remove" value="<?php echo $product['product_id']; ?>">&times;</button>
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

        quantityInputs.forEach(input => {
            input.addEventListener('input', () => {
                const price = parseFloat(input.dataset.price);
                const productId = input.dataset.productId;
                const quantity = parseInt(input.value) || 1;
                const total = price * quantity;

                // Update the corresponding summary item
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
            const productIds = [];
            const quantities = [];
            const prices = [];

            quantityInputs.forEach(input => {
                productIds.push(input.dataset.productId);
                quantities.push(input.value);
                prices.push(parseFloat(input.dataset.price) * parseInt(input.value));
            });

            formData.append('prod_id', JSON.stringify(productIds));
            formData.append('quantity', JSON.stringify(quantities));
            formData.append('price', JSON.stringify(prices));

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                saveBtn.textContent = 'Ruaj Ndryshimet';
                saveBtn.disabled = false;
                
                // Show success message
                saveMessage.classList.add('show');
                
                // Hide message after 3 seconds
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    saveMessage.classList.remove('show');
                }, 3000);

                // Update cart summary
                updateCartSummary();
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

            // Calculate subtotal
            summaryTotals.forEach(item => {
                const cleanedText = item.textContent.replace(/,/g, '');
                const match = cleanedText.match(/= ([\d.]+)/);
                if (match && match[1]) {
                    subtotal += parseFloat(match[1]);
                }
            });

            // Recalculate discount only for products with a discount
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

            // Update summary items
            const summaryItems = document.querySelectorAll('.summary-item');
            summaryItems[summaryItems.length - 4].querySelector('span:last-child').textContent = subtotal.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "€";
            summaryItems[summaryItems.length - 3].querySelector('span:last-child').textContent = tvsh.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "€";
            summaryItems[summaryItems.length - 2].querySelector('span:last-child').textContent = totalDiscount > 0 ? `- ${totalDiscount.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}€` : "0.00€";
            summaryItems[summaryItems.length - 1].querySelector('span:last-child').textContent = finalTotal.toLocaleString('us', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "€";
        }
    });

    // Add clear-search button logic for cart page
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
    });
</script>
<?php } else { header("Location: index.php"); } ?>
