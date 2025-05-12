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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Roboto', sans-serif;
        color: var(--page-text-color);
    }
    
    h2 {
        text-align: center;
        color: var(--page-text-color);
        margin-bottom: 30px;
        font-size: 2rem;
    }
    
    .payment-summary {
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
    }
</style>
<body>
    <div class="page-wrapper">
        <div class="container">
            <h2>Complete Your Payment</h2>
            
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
            
            <div class="payment-form">
                <form id="payment-form">
                    <div class="form-row">
                        <label for="cardholder-name">Cardholder Name</label>
                        <input type="text" id="cardholder-name" value="<?php echo htmlspecialchars($userFullName); ?>" readonly>
                    </div>
                    
                    <div class="form-row">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
                    </div>
                    
                    <div class="form-row">
                        <label for="card-element">
                            Credit or Debit Card
                        </label>
                        <div id="card-element">
                            <!-- Stripe.js will insert the card Element here -->
                        </div>
                        <div id="card-errors" role="alert"></div>
                    </div>
                    
                    <button type="submit" id="submit-button">
                        Pay Now <?php echo number_format($totalAmount, 2); ?>€
                        <span class="spinner" id="spinner" style="display: none;"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include "footer/footer.php"; ?>
    
    <script>
        // Create a Stripe client
        const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
        const elements = stripe.elements();
        
        // Create an instance of the card Element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Roboto", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#e53935',
                    iconColor: '#e53935'
                }
            }
        });
        
        // Add an instance of the card Element into the `card-element` div
        cardElement.mount('#card-element');
        
        // Handle form submission
        const form = document.getElementById('payment-form');
        const cardholderName = document.getElementById('cardholder-name');
        const email = document.getElementById('email');
        const submitButton = document.getElementById('submit-button');
        const spinner = document.getElementById('spinner');
        const errorElement = document.getElementById('card-errors');
        const successMessage = document.getElementById('success-message');
        
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            
            // Disable the submit button to prevent multiple submissions
            submitButton.disabled = true;
            submitButton.innerHTML = 'Processing... <span class="spinner" id="spinner"></span>';
            
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: cardholderName.value,
                    email: email.value
                }
            });
            
            if (error) {
                // Show error to your customer
                errorElement.textContent = error.message;
                submitButton.disabled = false;
                submitButton.innerHTML = 'Pay Now <?php echo number_format($totalAmount, 2); ?>€';
                return;
            }
            
            // Send the payment method ID to your server
            const response = await fetch('process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    payment_method_id: paymentMethod.id
                })
            });
            
            const jsonResponse = await response.json();
            
            // Handle server response
            handleServerResponse(jsonResponse);
        });
        
        // Handle server response
        function handleServerResponse(response) {
            if (response.error) {
                // Show error from server
                errorElement.textContent = response.error;
                submitButton.disabled = false;
                submitButton.innerHTML = 'Pay Now <?php echo number_format($totalAmount, 2); ?>€';
            } else if (response.requires_action) {
                // Use Stripe.js to handle required card action
                handleAction(response.payment_intent_client_secret);
            } else if (response.success) {
                // Show success message
                form.style.display = 'none';
                successMessage.style.display = 'block';
                
                // Redirect to confirmation page after a delay
                setTimeout(() => {
                    window.location.href = 'order-confirmation.php';
                }, 3000);
            }
        }
        
        // Handle required actions
        async function handleAction(clientSecret) {
            // Use Stripe.js to handle the required action
            const { paymentIntent, error } = await stripe.handleCardAction(clientSecret);
            
            if (error) {
                // Show error to your customer
                errorElement.textContent = error.message;
                submitButton.disabled = false;
                submitButton.innerHTML = 'Pay Now <?php echo number_format($totalAmount, 2); ?>€';
            } else {
                // The card action has been handled
                // The PaymentIntent can be confirmed again on the server
                const response = await fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        payment_intent_id: paymentIntent.id
                    })
                });
                
                const jsonResponse = await response.json();
                
                // Handle server response
                handleServerResponse(jsonResponse);
            }
        }
    </script>
</body>
</html> 