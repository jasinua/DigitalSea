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
        justify-content: center;
        align-items: center;
    }

    .cart-wrapper {
        display: flex;
        width: 100%;
        max-width: 1200px;
        gap: 40px;
    }

    .cart-left, .cart-right {
        background-color: white;
        color: var(--page-text-color);
        border-radius: 50px;
        box-shadow: 0 0 5px var(--navy-color);
        padding: 20px;
    }

    .cart-left {
        display: flex;
        flex-direction: column;
        border-radius: 10px;
        flex: 1 1 60%;
        max-height: 500px;
        justify-content: space-between;
        /* overflow-y: auto; */
    }

    .itemsTable {
        max-height: 340px;
        overflow-y: auto;
    }

    .cart-right {
        display: flex;
        flex-direction: column;
        border-radius: 10px;
        color: var(--page-text-color);
        flex: 1 1 35%;
        max-height: 500px;
    }

    .cart-right h3 {
        background-color: var(--background-color);
        padding: 10px;
        border-bottom: 1px solid var(--mist-color);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead tr {
        background-color: var(--background-color);
    }

    th, td {
        padding: 12px;
        color: var(--page-text-color);
        text-align: center;
        border-bottom: 1px solid var(--mist-color);
    }

    td:first-child {
        text-align: left;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .product-info img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border-radius: 8px;
    }

    .product-info h4 {
        margin: 0;
        font-size: 0.95rem;
    }

    .product-info .desc {
        font-size: 0.8rem;
        color: var(--navy-color);
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .quantity-controls input {
        width: 40px;
        height: 30px;
        text-align: center;
        border: 1px solid var(--ivory-color);
        border-radius: 4px;
    }

    .remove-btn {
        background: none;
        border: none;
        font-size: 1.4rem;
        color: #888;
        cursor: pointer;
    }

    .summary-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 40px;   
        flex: 1; 
    }

    .summary-box h3 {
        margin-bottom: 15px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin: 10px 0;
        font-size: 0.95rem;
    }

    .summary-item.total {
        font-weight: bold;
        font-size: 1.2rem;
    }

    .checkout-btn {
        background-color: var(--navy-color);
        color: var(--text-color);
        border: none;
        padding: 15px;
        width: 100%;
        font-size: 1rem;
        border-radius: 8px;
        cursor: pointer;
        margin-top: auto;
        align-self: flex-end;
        transition:ease-out 0.2s;
    }
    .checkout-btn:hover {
        background-color:var(--button-color-hover);
        transition:ease-out 0.2s;
    }

    #prodNameXprice {
        overflow-y: auto;
        max-height: 180px;
    }
</style>
<div class="page-wrapper">
    <form action="" method="post" id="cartForm">
        <div class="cart-wrapper">

            <!-- Left: Cart Table -->
            <div class="cart-left">
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
                <div class="itemsTable">
                    <table>
                        <tbody>
                            <?php 
                            $subtotal = 0;
                            foreach ($res as $cart) {
                                $product_result = returnProduct($cart['product_id']);
                                $product = $product_result->fetch_assoc();
                                $total = $product['price'] * $cart['quantity'];
                                $subtotal += $total;
                            ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <input type="hidden" name="prod_id[]" value="<?php echo $product['product_id']; ?>">
                                        <img src="<?php echo $product['image_url']; ?>" alt="Product Image">
                                        <div>
                                            <h4><?php echo $product['name']; ?></h4>
                                            <div class="desc"><?php echo $product['description']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo number_format($product['price'], 2); ?>€</td>
                                <td>
                                    <div class="quantity-controls">
                                        <input 
                                            type="number" 
                                            name="quantity[]" 
                                            class="quantity-input" 
                                            min="1" 
                                            value="<?php echo $cart['quantity']; ?>" 
                                            data-price="<?php echo $product['price']; ?>" 
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

                <!-- Save Changes Button -->
                <button type="submit" class="checkout-btn" name="save">Ruaj Ndryshimet</button>
            </div>

            <!-- Right: Summary Box -->
            <div class="cart-right">
                <h3>Totali i porosisë:</h3>
                
                <div class="summary-box">
                    <div>
                        <div id="prodNameXprice">
                            <?php 
                                $subtotal = 0;
                                foreach ($res as $cartItem) {
                                    $product = returnProduct($cartItem['product_id'])->fetch_assoc();
                                    $qty = $cartItem['quantity'];
                                    $prc = $product['price'];
                                    $ttl = $qty * $prc;
                                    $subtotal += $ttl;
                                ?>
                                <div class="summary-item" data-product-id="<?php echo $product['product_id']; ?>">
                                    <span><?php echo $product['name']; ?></span>
                                    <span class="total-price"><?php echo $qty; ?> x <?php echo number_format($prc, 2); ?>€ = <?php echo number_format($ttl, 2); ?>€</span>
                                </div>
                            <?php } ?>
                        </div>
                        
                        <div class="summary-item"><span>Nëntotali:</span> <span><?php echo number_format($subtotal, 2); ?>€</span></div>
                        <div class="summary-item"><span>TVSH 18%:</span> <span><?php echo number_format($subtotal * 0.18, 2); ?>€</span></div>
                        <div class="summary-item"><span>Zbritje:</span> <span style="color:red">- <?php echo '0.00'.'€';?></span></div>

                    </div>
                    <div>
                        <div class="summary-item total">
                            <span>Total:</span>
                            <span><?php echo number_format($subtotal+$subtotal*0.18, 2); ?>€</span>
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
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const quantityInputs = document.querySelectorAll('.quantity-input');

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

        function updateCartSummary() {
            const summaryTotals = document.querySelectorAll('.summary-item .total-price');
            let subtotal = 0;

            summaryTotals.forEach(item => {
                const match = item.textContent.match(/= ([\d.]+)€/);
                if (match) subtotal += parseFloat(match[1]);
                console.log(match[1])
            });

            const tvsh = subtotal * 0.18;
            const discount = 0;
            const finalTotal = subtotal + tvsh;

            const summaryItems = document.querySelectorAll('.summary-box .summary-item');
            summaryItems[summaryItems.length - 3].querySelector('span:last-child').textContent = subtotal.toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + "€";
            summaryItems[summaryItems.length - 2].querySelector('span:last-child').textContent = tvsh.toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + "€";
            summaryItems[summaryItems.length - 1].querySelector('span:last-child').textContent = finalTotal.toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + "€";
        }
    });
</script>
<?php } else { header("Location: index.php"); } ?>
