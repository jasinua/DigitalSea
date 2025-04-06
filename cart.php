<?php 
include_once "includes/dbh.inc.php";
include_once "includes/function.php";
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
        header("Location: checkout.php");
        exit;
    }

    // Fetch cart again after updates
    $res = returnCart($userId);

    include "header.php";
?>
<!-- <link rel="stylesheet" href="style.css"> -->
<style>
    body { margin: 0;
        padding: 0;
        background: #f5f5f5;
        font-family: Arial, sans-serif;
    }

    .cart-wrapper {
        display: flex;
        justify-content: space-between;
        /* flex-wrap: wrap; */
        width: 95%;
        max-width: 1300px;
        margin: 40px auto;
        gap: 20px;
    }

    .cart-left, .cart-right {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        padding: 20px;
    }

    .cart-left {
        flex: 1 1 60%;
        overflow-x: auto;
    }

    .cart-right {
        flex: 1 1 35%;
        max-height: 500px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead tr {
        background-color: #f9f9f9;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    td:first-child {
        text-align: left;
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
        color: #888;
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
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .remove-btn {
        background: none;
        border: none;
        font-size: 1.4rem;
        color: #888;
        cursor: pointer;
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
        background-color: rgb(33, 35, 58);
        color: white;
        border: none;
        padding: 15px;
        width: 100%;
        font-size: 1rem;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 10px;
    }
</style>

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
                        <th>Totali</th>
                        <th>Fshi</th>
                    </tr>
                </thead>
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
                        <td>€<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <div class="quantity-controls">
                                <input 
                                    type="number" 
                                    name="quantity[]" 
                                    class="quantity-input" 
                                    min="1" 
                                    value="<?php echo $cart['quantity']; ?>" 
                                    data-price="<?php echo $product['price']; ?>" 
                                >
                            </div>
                        </td>
                        <input type="hidden" name="price[]" value="<?php echo $total; ?>">

                        <td class="total-price">€<?php echo number_format($total, 2); ?></td>
                        <td>
                            <button class="remove-btn" type="submit" name="remove" value="<?php echo $product['product_id']; ?>">&times;</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Save Changes Button -->
            <button type="submit" class="checkout-btn" name="save">Ruaj Ndryshimet</button>
        </div>

        <!-- Right: Summary Box -->
        <div class="cart-right">
            <h3>Totali i porosisë:</h3>
            <div class="summary-box">
                <div class="summary-item"><span>Nëntotali:</span> <span>€<?php echo number_format($subtotal, 2); ?></span></div>
                <div class="summary-item"><span>TVSH 18%:</span> <span>€<?php echo number_format($subtotal * 0.18, 2); ?></span></div>
                <div class="summary-item"><span>Zbritje:</span> <span style="color:red">- €50.00</span></div>
                <div class="summary-item total">
                    <span>Total:</span>
                    <span>€<?php echo number_format($subtotal * 1.18 - 50, 2); ?></span>
                </div>

                <!-- Checkout Button -->
                <button class="checkout-btn" type="submit" name="continue">Vazhdo ne checkout</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const quantityInputs = document.querySelectorAll('.quantity-input');

    quantityInputs.forEach(input => {
        input.addEventListener('input', () => {
            const price = parseFloat(input.dataset.price);
            const quantity = parseInt(input.value) || 1;
            const row = input.closest('tr');
            const totalCell = row.querySelector('.total-price');

            const total = price * quantity;
            totalCell.textContent = '€' + total.toFixed(2);

            updateCartSummary();
        });
    });

    function updateCartSummary() {
        const totalCells = document.querySelectorAll('.total-price');
        let subtotal = 0;
        totalCells.forEach(cell => {
            const amount = parseFloat(cell.textContent.replace('€', '')) || 0;
            subtotal += amount;
        });

        const tvsh = subtotal * 0.18;
        const discount = 50;
        const finalTotal = subtotal + tvsh - discount;

        document.querySelector('.summary-item:nth-child(1) span:last-child').textContent = '€' + subtotal.toFixed(2);
        document.querySelector('.summary-item:nth-child(2) span:last-child').textContent = '€' + tvsh.toFixed(2);
        document.querySelector('.summary-item.total span:last-child').textContent = '€' + finalTotal.toFixed(2);
    }
});
</script>

<?php 
} else {
    header("Location: homepage.php");
} 
?>
