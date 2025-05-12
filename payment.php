<?php 
    session_start();
    include_once "controller/function.php";
    include "header/header.php";

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        var_dump($_SESSION); // Debug: This will output all session variables
        die("Session user_id is not set. Please log in.");
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment</title>
</head>
<style>
    .search-container input[type="text"] {
        width: 100%;
        min-width: 450px;
        padding: 10px 35px 10px 15px;
        border: none;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    h2 {
        margin: 50px 0 0px 0;
        font-size: 2.5rem;
        text-align:center;
        color: var(--page-text-color);
    }

    .h2 {
        margin: 10px auto;
    }

    /* f5f5f0, f2efe9, f4f1ea */
    .paymentStuff .form {
      color: var(--page-text-color);
      padding: 40px;
      border-radius: 18px;
      width: 520px;
      margin: auto;
      margin-bottom: 40px;
      box-shadow: 0 0px 12px var(--navy-color);
      font-size: 1.2rem;
    }

    .paymentStuff input[type="text"], .paymentStuff input[type="number"]{
      width: 100%;
      padding: 18px;
      margin-bottom: 18px;
      background-color: var(--ivory-color);
      color: var(--page-text-color);
      border-radius: 8px;
      border: 1.5px solid var(--navy-color);
    }

    input[type="submit"] {
      background-color: var(--button-color);
      cursor: pointer;
      font-size: 1.1rem;
      width: 100%;
      padding: 18px;
      margin-top: 18px;
      border: none;
      color: var(--text-color);
      transition: var(--transition);
      border-radius: 8px;
    }

    input[type="submit"]:hover {
      background-color: var(--button-color-hover);
    }

    .paymentStuff{
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        gap: 60px;
        margin: 40px auto 0 auto;
        max-width: 1200px;
        width: 100%;
    }

    .backCard {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .expDate, .cvv {
        display: flex;
        flex-direction: column;
    }

    .expDate input {
        width: 220px;
    }

    .cvv input {
        width: 220px;
    }

     /* ===== Card Visual ===== */
     .card-container {
        perspective: 1000px;
        width: 100%;
        max-width: 480px;
        min-width: 350px;
        margin: auto auto;
    }

    .card {
        width: 100%;
        height: 280px;
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
        padding: 2.5rem;
        color: #ffffff;
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.3rem;
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
    
    /* Responsive Styles */
    @media (max-width: 1200px) {
        .paymentStuff {
            max-width: 90%;
            gap: 40px;
        }
    }
    
    @media (max-width: 992px) {
        .paymentStuff {
            flex-direction: column-reverse;
            gap: 30px;
        }
        
        .card-container {
            margin-bottom: 20px;
        }
        
        h2 {
            margin: 40px 0 30px 0;
            font-size: 2.2rem;
        }
    }
    
    @media (max-width: 768px) {
        .paymentStuff form {
            width: 100%;
            max-width: 520px;
            padding: 30px;
        }
        
        h2 {
            font-size: 1.8rem;
            margin: 30px 0 25px 0;
        }
        
        .card-container {
            max-width: 420px;
        }
    }
    
    @media (max-width: 576px) {
        .paymentStuff form {
            padding: 20px;
            font-size: 1rem;
        }
        
        .paymentStuff input[type="text"], 
        .paymentStuff input[type="number"] {
            padding: 14px;
            margin-bottom: 14px;
        }
        
        .backCard {
            flex-direction: column;
        }
        
        .expDate, .cvv {
            width: 100%;
        }
        
        .expDate input, .cvv input {
            width: 100%;
        }
        
        input[type="submit"] {
            padding: 14px;
            font-size: 1rem;
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
    
    @media (max-width: 400px) {
        .card-container {
            min-width: unset;
        }
        
        .card {
            height: 200px;
        }
        
        .card-front, .card-back {
            padding: 1.5rem;
        }
        
        .card-number {
            font-size: 1.2rem;
        }
        
        .paymentStuff form {
            padding: 15px;
        }
    }

    .pay-with {
        width: 30%;
        margin: 10px auto;
        background-color: #4a5568;
        /* padding: 20px; */
        border-radius: 20px;
        display: flex;
    
    }

    .payment-something {
        width: 50%;
        text-align: center;
        padding: 20px;
        font-size: 1.5rem;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        
        
        border-radius: 20px;
        /* background-color: #4a5568; */
    }

    .payment-something.active {
        width: 50%;
        text-align: center;
        padding: 20px;
        font-size: 1.5rem;
        color: #fff;
        
        border-radius: 20px;
        background-color: var(--navy-color-lighter);
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 20px;
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

.payment-main {
    padding: 30px;
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
    <h2>Make Your Payment</h2>

    <div class="pay-with">
        <div class="payment-something active" id="cardd">Pay with card</div>
        <div class="payment-something" id="cryptoo">Pay with crypto</div>
    </div>

    <!-- CARD PAYMENT -->
        <div class="paymentStuff active" id="card-payment">
            <form action="process.php" method="POST" class="form">
                <label for="name">Name upon the card:</label>
                <input type="text" name="name" id="name" placeholder="Filan Fisteku" required>

                <label for="card">Card of fate:</label>
                <input type="text" name="card" id="card" maxlength="16" placeholder="XXXX XXXX XXXX XXXX" required>

                <div class="backCard">
                    <div class="expDate">
                        <label for="expiry">Expiration:</label>
                        <input type="text" name="expiry" id="expiry" maxlength="5" placeholder="MM/YY" required>
                    </div>
                    <div class="cvv">
                        <label for="cvv">CVV:</label>
                        <input type="text" name="cvv" id="cvv" maxlength="3" placeholder="XXX" required>
                    </div>
                </div>
                <input type="submit" value="Submit Payment">
            </form>

                <section class="card-container">
                <div class="card" id="card-visual">
                    <div class="card-front">
                        <div class="card-chip"></div>
                        <div class="card-number" id="card-number-display">**** **** **** ****</div>
                        <div class="card-details">
                            <div>
                                <span>Skadimi</span><br>
                                <span id="card-expiry-display">MM/YY</span>
                            </div>
                            <div>
                                <span id="card-name-display">Emri Juaj</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-back">
                        <div class="magnetic-strip"></div>
                        <div class="card-cvc" id="card-cvc-display">***</div>
                    </div>
                </div>
            </section>
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

        <main class="payment-main">
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







    <?php include "footer/footer.php"?>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const cardVisual = document.getElementById('card-visual');

    const cardNumberInput = document.getElementById('card');
    const expiryInput = document.getElementById('expiry');
    const cvvInput = document.getElementById('cvv');
    const nameInput = document.getElementById('name');

    const cardNumberDisplay = document.getElementById('card-number-display');
    const cardExpiryDisplay = document.getElementById('card-expiry-display');
    const cardCvcDisplay = document.getElementById('card-cvc-display');
    const cardNameDisplay = document.getElementById('card-name-display');

    // Flip the card when CVV input is focused or blurred
    cvvInput.addEventListener('focus', () => cardVisual.classList.add('flipped'));
    cvvInput.addEventListener('blur', () => cardVisual.classList.remove('flipped'));

    // Update card number display
    cardNumberInput.addEventListener('input', () => {
        let val = cardNumberInput.value.replace(/\D/g, '');
        let formatted = val.replace(/(\d{4})(?=\d)/g, '$1 ');
        cardNumberDisplay.textContent = formatted.padEnd(19, '*') || '**** **** **** ****';
    });

    // Update expiry display
    expiryInput.addEventListener('input', () => {
        cardExpiryDisplay.textContent = expiryInput.value || 'MM/YY';
    });

    // Update CVV display
    cvvInput.addEventListener('input', () => {
        cardCvcDisplay.textContent = cvvInput.value || '***';
    });

    // Update name display
    nameInput.addEventListener('input', () => {
        cardNameDisplay.textContent = nameInput.value || 'Emri Juaj';
    });

    // Add clear-search button logic for payment page
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

    //   document.querySelectorAll('.payment-something').forEach(button => {
    //     button.addEventListener('click', function() {
    //         document.querySelectorAll('.payment-something').forEach(btn => btn.classList.remove('active'));
    //         this.classList.add('active');

    //         const target = this.getAttribute('data-target');
    //         document.getElementById('card-payment').style.display = (target === 'card') ? 'flex' : 'none';
    //         document.getElementById('crypto-payment').style.display = (target === 'crypto') ? 'flex' : 'none';
    //     });
    // });

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
</html>
