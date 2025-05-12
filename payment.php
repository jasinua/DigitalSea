<?php 
    session_start();
    include_once "controller/function.php";
    include_once "config/stripe-config.php";
    include_once "model/dbh.inc.php";

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Get user ID
    $userId = $_SESSION['user_id'];

    // Get user info from database
    $userQuery = "SELECT first_name, last_name, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $userData = $userResult->fetch_assoc();

    $userFullName = $userData['first_name'] . ' ' . $userData['last_name'];
    $userEmail = $userData['email'];

    // Get cart items for this user
    $cartItems = returnCart($userId);
    if (!$cartItems || $cartItems->num_rows === 0) {
        header("Location: cart.php?error=emptycart");
        exit;
    }

    // Calculate total amount
    $subtotal = 0;
    $discount = 0;
    $mergedCart = [];

    // Process cart items
    while ($item = $cartItems->fetch_assoc()) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        
        // Get product details
        $productResult = returnProduct($pid);
        if ($productResult && $product = $productResult->fetch_assoc()) {
            $price = $product['price'];
            $productDiscount = $product['discount'];
            
            // Calculate discounted price
            if ($productDiscount > 0) {
                $finalPrice = $price - ($price * $productDiscount / 100);
                $discount += ($price - $finalPrice) * $qty;
            } else {
                $finalPrice = $price;
            }
            
            // Add to subtotal
            $subtotal += $price * $qty;
        }
    }

    // Calculate tax and final total
    $tax = $subtotal * 0.18; // 18% VAT
    $totalAmount = $subtotal + $tax - $discount;

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
<style>
    .page-wrapper {
        width: 100%;
        color: var(--page-text-color);
    }
    
    h2 {
        margin: 40px 0 30px 0;
        font-size: 2.2rem;
        text-align: center;
        color: var(--page-text-color);
    }
    
    .container {
        max-width: 100%;
        padding: 20px;
        font-family: 'Roboto', sans-serif;
    }

    .payment-part {
        margin-top: 40px;
        width: 100%;
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 50px;
    }
    
    .payment-summary {
        width: 500px;
        background-color: var(--ivory-color);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .payment-summary h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: var(--page-text-color);
        font-size: 1.5rem;
    }

    .payment-main {
        width: 500px;
        background-color: var(--ivory-color);
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .payment-crypto {
        width: 500px;
        background-color: var(--ivory-color);
        margin: auto;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 1rem;
    }
    
    .summary-item.total {
        border-top: 1px solid #ddd;
        margin-top: 15px;
        padding-top: 15px;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .payment-form {
        background-color: white;
        padding: 30px;
    }
    
    .form-row {
        margin-bottom: 20px;
    }
    
    .form-row label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--page-text-color);
    }
    
    .form-row input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        background-color: var(--ivory-color);
    }
    
    #card-element {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: white;
    }
    
    #payment-form button {
        display: block;
        width: 100%;
        padding: 15px;
        background-color: var(--button-color);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 20px;
    }
    
    #payment-form button:hover {
        background-color: var(--button-color-hover);
        transform: translateY(-2px);
    }
    
    #payment-form button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    #card-errors {
        color: #e53935;
        margin-top: 10px;
        font-size: 0.9rem;
    }
    
    .success-message {
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        border-radius: 4px;
        text-align: center;
        margin-bottom: 20px;
        display: none;
    }
    
    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
        margin-left: 10px;
        vertical-align: middle;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Card visual styles */
    .card-container {
        perspective: 1000px;
        width: 100%;
        max-width: 480px;
        min-width: 350px;
        margin: 0 auto 20px auto;
    }

    .card {
        width: 100%;
        height: 250px;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.6s;
    }

    .card.flipped {
        transform: rotateY(180deg);
    }

    .card-front,
    .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        padding: 20px;
        color: #ffffff;
    }

    .card-front {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-back {
        background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .card-number {
        font-size: 1.6rem;
        letter-spacing: 2px;
    }

    .card-details {
        display: flex;
        justify-content: space-between;
        font-size: 1.1rem;
    }

    .card-chip {
        width: 60px;
        height: 40px;
        background: #d4d4d4;
        border-radius: 4px;
    }

    .card-cvc {
        font-size: 1.4rem;
        letter-spacing: 2px;
    }

    .magnetic-strip {
        width: 100%;
        height: 40px;
        background: #2d3748;
        margin-bottom: 1rem;
    }
    
    /* Responsive styles */
    @media screen and (max-width: 768px) {
        .container {
            max-width: 95%;
            padding: 15px;
        }
        
        h2 {
            font-size: 1.8rem;
        }
        
        .payment-form {
            padding: 20px;
        }
        
        .card {
            height: 240px;
        }
        
        .card-front, .card-back {
            padding: 1.8rem;
        }
        
        .card-number {
            font-size: 1.4rem;
        }
        
        .card-details {
            font-size: 0.9rem;
        }
        
        .card-cvc {
            font-size: 1.2rem;
        }
        
        .card-chip {
            width: 50px;
            height: 35px;
        }
        
        h2 {
            font-size: 1.6rem;
            /* margin: 25px 0 20px 0; */
        }
    }
    
    @media screen and (max-width: 480px) {
        h2 {
            font-size: 1.5rem;
        }
        
        .payment-form {
            padding: 15px;
        }
        
        .form-row label {
            font-size: 0.9rem;
        }
        
        .form-row input {
            padding: 10px;
        }
        
        #payment-form button {
            padding: 12px;
            font-size: 1rem;
        }
        
        .card {
            height: 200px;
        }
        
        .card-number {
            font-size: 1.3rem;
        }
    }
    
    @media screen and (max-width: 400px) {
        .card-container {
            min-width: unset;
        }
        
        .card {
            height: 180px;
        }
        
        .card-front, .card-back {
            padding: 15px;
        }
        
        .card-number {
            font-size: 1.1rem;
        }
    }

    .pay-with {
        width: 40%;
        margin: 10px auto;
        background-color: var(--button-color);
        /* padding: 20px; */
        border-radius: 10px;
        display: flex;
        overflow: hidden;
    }

    .payment-something {
        width: 50%;
        text-align: center;
        padding: 10px;
        font-size: 1.5rem;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        /* background-color: #4a5568; */
    }

    .payment-something.active {
        width: 50%;
        text-align: center;
        padding: 10px;
        font-size: 1.5rem;
        color: #fff;
        /* border-radius: 10px; */
        background-color: var(--button-color-hover);
        cursor: pointer;
        transition: all 0.3s ease;
    }










      :root {
    --success-color: #00c853;
    --warning-color: #ff9100;
    --error-color: #ff5252;
    --border-color: #ddd;
    --border-radius: 8px;
    --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.payment-container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.payment-header {
    padding: 10px;
    text-align: center;
    background: var(--navy-color);
    color: white;
}

.payment-header h1 {
    font-weight: 500;
    margin-bottom: 8px;
}

.payment-header p {
    opacity: 0.9;
    font-weight: 300;
}

.payment-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 500;
    color: var(--page-text-color);
}

.form-group input,
.form-group textarea {
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 16px;
    transition: border 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--navy-color);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.btn {
    background-color: var(--navy-color);
    color: white;
    border: none;
    padding: 14px;
    border-radius: var(--border-radius);
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s;
}

.btn:hover {
    background-color: #1244c4;
}

.alert {
    padding: 12px;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
}

.alert.error {
    background-color: #ffebee;
    color: var(--error-color);
    border-left: 4px solid var(--error-color);
}

.payment-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.payment-summary {
    /* background: var(--secondary-color); */
    padding: 10px;
    border-radius: var(--border-radius);
}

.payment-summary h2 {
    margin-bottom: 16px;
    font-weight: 500;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: var(--page-text-color);
}

.detail-value {
    color: var(--page-text-color);
}

.status-new {
    color: var(--warning-color);
}

.status-pending {
    color: var(--warning-color);
}

.status-completed {
    color: var(--success-color);
}

.status-failed {
    color: var(--error-color);
}

.coinbase-button {
    display: block;
    text-align: center;
    margin: 20px 0;
}

.coinbase-button img {
    max-width: 200px;
}

.small-text {
    font-size: 14px;
    color: var(--page-text-color);
    text-align: center;
}

.payment-alternatives {
    text-align: center;
    margin-top: 20px;
}

.qr-code {
width: 220px;
height: 220px;
    margin: 20px auto;
    padding: 10px;
    background: white;
    border-radius: var(--border-radius);
    display: inline-block;
    border: 1px solid var(--border-color);
}

.payment-footer {
    padding: 20px;
    text-align: center;
    color: var(--page-text-color);
    font-size: 14px;
    border-top: 1px solid var(--border-color);
}

@media (max-width: 480px) {
    .payment-header {
        padding: 20px;
    }
    
    .payment-main {
        padding: 20px;
    }
    
    .detail-row {
        flex-direction: column;
        gap: 4px;
    }
}


#crypto-payment {margin-bottom:10px;
}


</style>
<body>
    <div class="page-wrapper">


        <div class="container">
            <h2>Complete Your Payment</h2>

            <div class="pay-with">
        <div class="payment-something active" id="cardd">Pay with card</div>
        <div class="payment-something" id="cryptoo">Pay with crypto</div>
    </div>
            
            <div class="payment-part" id="card-payment">

                    <main class="payment-main">
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
                                    <label for="card-element">Credit or Debit Card</label>
                                    <div id="card-element"></div>
                                    <div id="card-errors" role="alert"></div>
                                </div>
                                
                                <button type="submit" id="submit-button">
                                    Pay Now <?php echo number_format($totalAmount, 2); ?>€
                                </button>
                            </form>
                        </div>
                    </main>

                <div class="payment-summary">
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
                        <span>-<?php echo number_format($discount, 2); ?>€</span>
                    </div>
                    <?php endif; ?>
                    <div class="summary-item total">
                        <span>Total:</span>
                        <span><?php echo number_format($totalAmount, 2); ?>€</span>
                    </div>
                </div>
                
                <div class="success-message" id="success-message">
                    Payment successful! Processing your order...
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
            header("Location: payment.php?charge_id=" . urlencode($chargeId));
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
            <div class="payment-container">
        <header class="payment-header">
            <h1>Crypto Payment Gateway</h1>
            <p>Pay securely with cryptocurrency</p>
        </header>

        <main class="payment-crypto">
            <?php if (!$chargeData): ?>
                <form method="POST" class="payment-form">
                    <?php if (isset($error)): ?>
                        <div class="alert error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="amount">Amount (USD)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn">Create Payment</button>
                </form>
            <?php else: ?>
                <div class="payment-details">
                    <div class="payment-summary">
                        <h2 class="h2">Payment Details</h2>
                        <div class="detail-row">
                            <span class="detail-label">Amount:</span>
                            <span class="detail-value">€<?php echo $chargeData['pricing']['local']['amount']; ?> EUR</span>
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

                    <div class="payment-action">
                        <a href="<?php echo $chargeData['hosted_url']; ?>" class="coinbase-button" target="_blank">
                            <!-- <img src="https://commerce.coinbase.com/buttons/button.png" alt="Pay with Crypto"> -->
                             <p style="text-decoration:none;">Pay with crypto link</p>
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

                </div>
            <?php endif; ?>
        </main>

        <footer class="payment-footer">
            <p>Powered by Coinbase Commerce</p>
        </footer>
    </div>       
        </div>
    </div>
    
    <?php include "footer/footer.php"; ?>
    
    <script>
        // Initialize Stripe with your publishable key
        const stripe = Stripe('pk_test_51RNwPwPvDVolmYEKSitKTOugjsFcQpoLHdHFVjbzVrk2S5P2ar9s5pGGZde4ErekRDipxvTjbY8oazmX6glShHlT00lFblw8mq');
        const elements = stripe.elements();

        // Create card Element
        const card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });

        // Mount the card Element
        card.mount('#card-element');

        // Handle real-time validation errors
        card.addEventListener('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            const submitButton = document.getElementById('submit-button');
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            console.log("1");
            try {
                const {paymentMethod, error} = await stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                    billing_details: {
                        name: document.getElementById('cardholder-name').value,
                        email: document.getElementById('email').value
                    }
                });

            
                if (error) {
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = error.message;
                    submitButton.disabled = false;
                    submitButton.textContent = 'Pay Now <?php echo number_format($totalAmount, 2); ?>€';
                    console.log("2");
                } else {
                    console.log(JSON.stringify({
                            payment_method_id: paymentMethod.id,
                            amount: <?php echo $totalAmount * 100; ?>, // Convert to cents
                            email: document.getElementById('email').value,
                            name: document.getElementById('cardholder-name').value
                        }));
                    // Send payment method ID to your server
                    const response = await fetch('process.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            payment_method_id: paymentMethod.id,
                            amount: <?php echo $totalAmount * 100; ?>, // Convert to cents
                            email: document.getElementById('email').value,
                            name: document.getElementById('cardholder-name').value
                        })
                    });

                    const result = await response.json();

                    if (result.error) {
                        const errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error;
                        submitButton.disabled = false;
                        submitButton.textContent = 'Pay Now <?php echo number_format($totalAmount, 2); ?>€';
                    } else {
                        // Show success message
                        document.getElementById('success-message').style.display = 'block';
                        // Redirect to success page
                        window.location.href = 'order-confirmation.php?order_id=' + result.order_id;
                    }
                }
            } catch (err) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = 'An unexpected error occurred.';
                submitButton.disabled = false;
                submitButton.textContent = 'Pay Now <?php echo number_format($totalAmount, 2); ?>€';
            }
        });

        document.getElementById('cardd').addEventListener('click', function() {
        document.getElementById('card-payment').style.display = 'flex';
        document.getElementById('crypto-payment').style.display = 'none';
        document.querySelectorAll('.payment-something').forEach(btn => btn.classList.remove('active'));
      
        this.classList.add('active');
    });
    document.getElementById('cryptoo').addEventListener('click', function() {
        document.getElementById('card-payment').style.display = 'none';
        document.getElementById('crypto-payment').style.display = 'flex';
        document.querySelectorAll('.payment-something').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
    });
</script>
</body>
</html>
