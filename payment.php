<?php 
    session_start();
    include_once "controller/function.php";
    include_once "model/dbh.inc.php";
    require_once 'vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    require_once 'vendor/stripe/stripe-php/init.php';
    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Get user ID
    $userId = $_SESSION['user_id'];

    // Get user info from database
    $userQuery = "SELECT first_name, last_name, email, address FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $userData = $userResult->fetch_assoc();

    $userFullName = $userData['first_name'] . ' ' . $userData['last_name'];
    $userEmail = $userData['email'];
    $userAddress = $userData['address'];

    // Handle address update if submitted
    if (isset($_POST['update_address']) && isset($_POST['new_address'])) {
        $newAddress = trim($_POST['new_address']);
        $updateStmt = $conn->prepare("UPDATE users SET address = ? WHERE user_id = ?");
        $updateStmt->bind_param("si", $newAddress, $userId);
        if ($updateStmt->execute()) {
            $userAddress = $newAddress;
            $success_message = "Address updated successfully";
        } else {
            $error_message = "Failed to update address";
        }
    }

    // Get cart items for this user
    $cartItems = returnCart($userId);
    
    if (!$cartItems || $cartItems->num_rows === 0) {
        header("Location: cart.php?error=emptycart");
        exit;
    }

    // Check stock levels before proceeding
    $stockIssues = [];
    $cartItems->data_seek(0); // Reset the result pointer
    while ($item = $cartItems->fetch_assoc()) {
        if($item['order_id'] == null) {
            $pid = $item['product_id'];
            $qty = $item['quantity'];
            
            // Check stock level
            $stockQuery = "SELECT name, stock FROM products WHERE product_id = ?";
            $stockStmt = $conn->prepare($stockQuery);
            $stockStmt->bind_param("i", $pid);
            $stockStmt->execute();
            $stockResult = $stockStmt->get_result();
            $productData = $stockResult->fetch_assoc();
            
            if ($productData['stock'] < $qty) {
                $stockIssues[] = sprintf(
                    "%s: Only %d items available (you requested %d)",
                    $productData['name'],
                    $productData['stock'],
                    $qty
                );
            }
        }
    }

    //If there are stock issues, redirect to cart with error message
    if (!empty($stockIssues)) {
        $_SESSION['error'] = "Stock issues detected: " . implode(", ", $stockIssues);
        header("Location: cart.php");
        exit();
    }

    // Calculate total amount
    $subtotal = 0;
    $discount = 0;
    $cartItems->data_seek(0); // Reset the result pointer again
    while ($item = $cartItems->fetch_assoc()) {
        if($item['order_id'] == null){
            $pid = $item['product_id'];
            $qty = $item['quantity'];
            
            $productResult = returnProduct($pid);
            if ($productResult && $product = $productResult->fetch_assoc()) {
                $price = $product['price'];
                $productDiscount = $product['discount'];
                
                if ($productDiscount > 0) {
                    $finalPrice = $price - ($price * $productDiscount / 100);
                    $discount += ($price - $finalPrice) * $qty;
                } else {
                    $finalPrice = $price;
                }
                
                $subtotal += $price * $qty;
            }
        }
    }
    $tax = $subtotal * 0.18; // 18% VAT
    $totalAmount = $subtotal + $tax - $discount;

    // Create Payment Intent
    try {
        $minimumAmount = 0.50; // Minimum amount in EUR (50 cents)
        $amountInCents = round($totalAmount * 100);

        if ($totalAmount < $minimumAmount) {
            // Create a Setup Intent for small amounts
            $paymentIntent = \Stripe\SetupIntent::create([
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $userId,
                    'email' => $userEmail,
                    'amount' => $amountInCents
                ]
            ]);
        } else {
            // Create a regular Payment Intent for normal amounts
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => $_ENV['STRIPE_CURRENCY'],
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => $userId,
                    'email' => $userEmail
                ]
            ]);
        }
        $clientSecret = $paymentIntent->client_secret;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        error_log("Stripe Error: " . $e->getMessage());
        $_SESSION['error'] = "Payment setup failed: " . $e->getMessage();
        header("Location: payment.php");
        exit();
    }

    include "header/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>

<?php include "css/payment-css.php"; ?>
<body>
    <div class="page-wrapper">
        
        <div class="container">
            <h2 id="payment-header">Complete Payment</h2>
            <?php if (isset($error_message) && $error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="pay-with" id="pay-with-switcher">
                <div class="payment-something active" id="cardd">Pay with card</div>
                <div class="payment-something" id="cryptoo">Pay with crypto</div>
            </div>
            <div class="payment-part" id="card-payment">
                <div class="payment-main">
                <header class="payment-header" id="card-header">
                            <h1>Card Payment</h1>
                            <p>Pay securely with your card</p>
                        </header>
                    <div class="payment-form">
                        <form id="payment-form">
                            <div class="form-row">
                                <label for="cardholder-name">Cardholder Name</label>
                                <input type="text" id="cardholder-name" value="<?php echo htmlspecialchars($userFullName); ?>" required>
                            </div>
                            
                            <div class="form-row">
                                <label for="email">Email</label>
                                <input type="email" id="email" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                            </div>
                            
                            <div class="form-row">
                                <label for="address">Shipping Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($userAddress); ?>" required>
                                <div id="address-update-container" style="display: none; margin-top: 10px;">
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; justify-content: center;">
                                        <input type="checkbox" id="update-address-checkbox" name="update_address" style="width: auto;">
                                        <span>Update my account address with this new address</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <label for="card-element">Credit or Debit Card</label>
                                <div id="card-element"></div>
                                <div id="card-errors" role="alert"></div>
                            </div>
                            
                            <button type="submit" id="submit-button">
                                Pay Now €<?php echo number_format($totalAmount, 2); ?>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="payment-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span>€<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>VAT (18%):</span>
                        <span>€<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <?php if ($discount > 0): ?>
                    <div class="summary-item">
                        <span>Discount:</span>
                        <span style="color: red">-€<?php echo number_format($discount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="summary-item total">
                        <span>Total:</span>
                        <span>€<?php echo number_format($totalAmount, 2); ?></span>
                    </div>
                </div>
            </div>
                    <!-- end of card payment -->

                    <!-- CRYPTO PAYMENT -->        
            <?php
            // Configuration 
            require __DIR__ . '/vendor/autoload.php';
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();

            define('COINBASE_API_KEY', $_ENV['COINBASE_API_KEY']);
            define('COINBASE_API_URL', 'https://api.commerce.coinbase.com');

            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                $description = htmlspecialchars($_POST['description'] ?? '');
                
                if ($amount && $amount > 0) {
                    $chargeId = createCharge($amount, $description);
                    if ($chargeId) {
                        header("Location: payment.php?charge_id=" . urlencode($chargeId) . "&payment_type=crypto");
                        exit;
                    }
                } else {
                    $error = "Please enter a valid amount";
                }
            }

            // Handle charge display
            $chargeData = null;
            if (isset($_GET['charge_id'])) {
                $chargeData = getCharge($_GET['charge_id']);
            }

            function makeCoinbaseRequest($endpoint, $method = 'GET', $data = null) {
                $url = COINBASE_API_URL . $endpoint;
                
                $headers = [
                    'X-CC-Api-Key: ' . COINBASE_API_KEY,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                if ($method === 'POST') {
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode === 401) {
                    throw new Exception("Authentication failed. Check your API key.");
                }
                
                return json_decode($response, true);
            }

            function createCharge($amount, $description) {
                $chargeData = [
                    'name' => 'Website Payment',
                    'description' => $description ?: 'Payment for goods/services',
                    'pricing_type' => 'fixed_price',
                    'local_price' => [
                        'amount' => number_format($amount, 2, '.', ''),
                        'currency' => 'EUR'
                    ],
                    'metadata' => [
                        'customer_id' => '12345' // Add your own metadata
                    ]
                ];
                
                try {
                    $response = makeCoinbaseRequest('/charges/', 'POST', $chargeData);
                    return $response['data']['id'];
                } catch (Exception $e) {
                    error_log("Error creating charge: " . $e->getMessage());
                    return null;
                }
            }

            function getCharge($chargeId) {
                try {
                    $response = makeCoinbaseRequest('/charges/' . $chargeId);
                    return $response['data'];
                } catch (Exception $e) {
                    error_log("Error retrieving charge: " . $e->getMessage());
                    return null;
                }
            }
            ?>


         <div class="paymentStuff" id="crypto-payment" style="display: none;">
            <div class="payment-part">
                
            <?php if (!$chargeData): ?>
                <div class="payment-main crypto">
                        <div class="payment-container">
                            <header class="payment-header" id="crypto-header">
                                <h1>Crypto Payment</h1>
                                <p>Pay securely with cryptocurrency</p>
                            </header>

                            <main class="payment-crypto">
                                <form method="POST" class="payment-form" id="crypto-form">
                                    <?php if (isset($error)): ?>
                                        <div class="alert error"><?php echo $error; ?></div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label for="amount">Amount to Pay</label>
                                        <input type="hidden" id="amount" name="amount" value="<?php echo $totalAmount; ?>" readonly>
                                        <input type="text" id="amount" name="amountjeter" value="<?php echo number_format($totalAmount, 2); ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea id="description" name="description" rows="3">Payment for order</textarea>
                                    </div>

                                    <button type="submit" class="btn">Create Payment</button>
                                </form>
                            </main>
                        </div>
                        <footer class="payment-footer">
                            <p>Powered by Coinbase Commerce</p>
                        </footer>
                    </div>
                    <div class="payment-summary" id="payment-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-item">
                                <span>Subtotal:</span>
                                <span><?php echo number_format($subtotal, 2); ?>€</span>
                            </div>
                            <div class="summary-item">
                                <span>VAT (18%):</span>
                                <span><?php echo number_format($tax, 2); ?>€</span>
                            </div>
                            <?php if ($discount > 0): ?>
                            <div class="summary-item">
                                <span>Discount:</span>
                                <span style="color: red">-<?php echo number_format($discount, 2); ?>€</span>
                            </div>
                            <?php endif; ?>
                            <div class="summary-item total">
                                <span>Total:</span>
                                <span>€<?php echo number_format($totalAmount, 2); ?></span>
                            </div>
                        </div>
                <?php else: ?>
                        <div class="payment-main crypto after">
                            <div class="payment-container after">
                                <header class="payment-header" id="crypto-header">
                                    <h1>Crypto Payment Gateway</h1>
                                    <p>Pay securely with cryptocurrency</p>
                                </header>

                                <main class="payment-crypto">
                                <div class="payment-details">
                                    <div class="payment-after crypto">
                                        <h2>Payment Details</h2>
                                        <div class="detail-row">
                                            <span class="detail-label">Amount:</span>
                                            <span class="detail-value"><?php echo $chargeData['pricing']['local']['amount']; ?>€</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Description:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($chargeData['description']); ?></span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Status:</span>
                                            <span class="detail-value status-<?php echo strtolower($chargeData['timeline'][0]['status']); ?>">
                                                <?php echo $chargeData['timeline'][0]['status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div style="display: flex; flex-direction: column; margin-top: 20px;">
                                        <div class="payment-action">
                                            <a href="<?php echo $chargeData['hosted_url']; ?>" class="coinbase-button" target="_blank">
                                                <p style="text-decoration:none;">Pay with crypto</p>
                                            </a>
                                            <p class="small-text">You'll be redirected to Coinbase to complete your payment</p>
                                        </div>

                                        <div class="payment-alternatives">
                                            <p class="small-text">Or scan QR code:</p>
                                            <div class="qr-code">
                                                <?php 
                                                if (isset($chargeData['hosted_url'])): 
                                                    $qrData = $chargeData['hosted_url'];
                                                ?>
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($qrData); ?>" alt="Payment QR Code">
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="payment-switch" style="margin-top: 20px; text-align: center;">
                                            <button onclick="switchToCard()" class="switch-button" style="background: none; border: none; color: var(--noir-color); text-decoration: underline; cursor: pointer;">
                                                Pay with Card Instead
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </main>
                        </div>
                        <footer class="payment-footer">
                            <p>Powered by Coinbase Commerce</p>
                        </footer>
                    </div>
                    <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const stripe = Stripe('<?php echo $_ENV['STRIPE_PUBLISHABLE_KEY']; ?>');
        const elements = stripe.elements();

        // Add address change listener
        const addressInput = document.getElementById('address');
        const addressUpdateContainer = document.getElementById('address-update-container');
        const originalAddress = '<?php echo htmlspecialchars($userAddress); ?>';

        addressInput.addEventListener('input', function() {
            if (this.value !== originalAddress) {
                addressUpdateContainer.style.display = 'block';
            } else {
                addressUpdateContainer.style.display = 'none';
            }
        });

        const style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        const card = elements.create('card', { style: style });
        card.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const errorElement = document.getElementById('card-errors');

        card.on('change', function(event) {
            errorElement.textContent = event.error ? event.error.message : '';
        });

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner"></span> Processing...';

            try {
                let result;
                if (<?php echo $totalAmount; ?> < <?php echo $minimumAmount; ?>) {
                    // Handle Setup Intent
                    result = await stripe.confirmCardSetup(
                        '<?php echo $clientSecret; ?>',
                        {
                            payment_method: {
                                card: card,
                                billing_details: {
                                    name: document.getElementById('cardholder-name').value,
                                    email: document.getElementById('email').value,
                                    address: {
                                        line1: document.getElementById('address').value
                                    }
                                }
                            }
                        }
                    );
                } else {
                    // Handle Payment Intent
                    result = await stripe.confirmCardPayment(
                        '<?php echo $clientSecret; ?>',
                        {
                            payment_method: {
                                card: card,
                                billing_details: {
                                    name: document.getElementById('cardholder-name').value,
                                    email: document.getElementById('email').value,
                                    address: {
                                        line1: document.getElementById('address').value
                                    }
                                }
                            }
                        }
                    );
                }

                if (result.error) {
                    errorElement.textContent = result.error.message;
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Pay Now €<?php echo number_format($totalAmount, 2); ?>';
                } else {
                    <?php
                        $_SESSION['payment_success'] = true;
                        $_SESSION['payment_timestamp'] = time();
                        $_SESSION['total_amount'] = $totalAmount;
                    ?>
                    window.location.href = 'controller/redirect-order.php';
                }
            } catch (err) {
                errorElement.textContent = 'An error occurred. Please try again.';
                submitButton.disabled = false;
                submitButton.innerHTML = 'Pay Now €<?php echo number_format($totalAmount, 2); ?>';
            }
        });

        document.getElementById('cardd').addEventListener('click', function() {
            document.getElementById('card-payment').style.display = 'flex';
            document.getElementById('crypto-payment').style.display = 'none';
            document.querySelectorAll('.payment-something').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            localStorage.removeItem('showCrypto');
        });
        document.getElementById('cryptoo').addEventListener('click', function() {
            document.getElementById('card-payment').style.display = 'none';
            document.getElementById('crypto-payment').style.display = 'flex';
            document.querySelectorAll('.payment-something').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            localStorage.setItem('showCrypto', '1');
        });

        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('payment_type') === 'crypto' || localStorage.getItem('showCrypto') === '1') {
                document.getElementById('cryptoo').click();
            }
            // Hide switcher and update header if chargeData exists
            if (document.querySelector('.payment-details')) {
                document.getElementById('pay-with-switcher').style.display = 'none';
                document.getElementById('payment-header').textContent = 'Complete Payment';
                document.getElementById('payment-summary').style.display = 'none';
            }
        });

        function switchToCard() {
            // Remove charge_id and payment_type from URL
            const url = new URL(window.location.href);
            url.searchParams.delete('charge_id');
            url.searchParams.delete('payment_type');
            // Reload the page without those parameters
            window.location.href = url.toString();
            // Optionally, clear any stored payment state
            localStorage.removeItem('showCrypto');
        }
    </script>
</body>
</html></html>
