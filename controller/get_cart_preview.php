<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Simple error handling function
    function outputError($message) {
        echo '<div class="empty-cart-message">' . $message . '</div>';
        exit;
    }

    function getImageSource($product_id, $image_url) {
        $local_image = "images/product_$product_id.png";
        return file_exists($local_image) ? $local_image : htmlspecialchars($image_url);
    }

    // Get absolute path to the root directory
    $rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));

    // Include database and function files directly
    require_once $rootPath . "/model/dbh.inc.php";
    require_once __DIR__ . "/function.php";
    require_once __DIR__ . "/home.inc.php";

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        outputError('Please log in to view your cart');
    }

    $userId = $_SESSION['user_id'];

    // Get cart items
    try {
        // Check database connection
        global $conn;
        if (!$conn) {
            outputError('Database connection error');
        }
        
        $cartItems = returnCart($userId);
        
        if (!$cartItems || $cartItems->num_rows === 0) {
            outputError('Your cart is empty');
        }
        
        // Group items by product ID and sum quantities
        $product_quantities = [];
        $count = 0;

        // Merge duplicate products by summing quantities
        while ($item = $cartItems->fetch_assoc()) {
            $pid = $item['product_id'];
            if (!isset($product_quantities[$pid])) {
                $product_quantities[$pid] = 0;
            }
            $product_quantities[$pid] += $item['quantity'];
        }

        // Display merged products, limited to 3 initially
        foreach ($product_quantities as $pid => $qty) {
            if ($count >= 3) break;
            $product_result = returnProduct($pid);
            if ($product_result && $product = $product_result->fetch_assoc()) {
                ?>
                <a class="cart-preview-item-link" href="../product.php?product=<?php echo $product['product_id']; ?>">
                    <div class="cart-preview-item">
                        <img src="<?php echo getImageSource($product['product_id'], $product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="cart-preview-item-info">
                            <div class="cart-preview-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="cart-preview-item-price">
                                <?php echo number_format($product['price'], 2); ?>€
                                <?php if ($qty > 1) { echo " <span style='color:#888;font-size:13px;'>(x$qty)</span>"; } ?>
                            </div>
                        </div>
                    </div>
                </a>
                <?php
                $count++;
            }
        }

        // Display remaining products if more than 3
        if ($count >= 3) {
            foreach (array_slice(array_keys($product_quantities), 3) as $pid) {
                $qty = $product_quantities[$pid];
                $product_result = returnProduct($pid);
                if ($product_result && $product = $product_result->fetch_assoc()) {
                    ?>
                    <a class="cart-preview-item-link" href="../product.php?product=<?php echo $product['product_id']; ?>">
                        <div class="cart-preview-item">
                            <img src="<?php echo getImageSource($product['product_id'], $product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="cart-preview-item-info">
                                <div class="cart-preview-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="cart-preview-item-price">
                                    <?php echo number_format($product['price'], 2); ?>€
                                    <?php if ($qty > 1) { echo " <span style='color:#888;font-size:13px;'>(x$qty)</span>"; } ?>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php
                }
            }
        }
    } catch (Exception $e) {
        outputError('Error: ' . $e->getMessage());
    }
?> 