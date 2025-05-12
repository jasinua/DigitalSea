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
        margin: 0 auto;
        padding: 20px;
        font-family: 'Roboto', sans-serif;
    }

    .payment-part {
        width: 100%;
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
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
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            height: 230px;
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
</style>
<body>
    <div class="page-wrapper">
        <div class="container">
            <h2>Complete Your Payment</h2>
            
            <div class="payment-part">


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
                } else {
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
    </script>
</body>
</html>
