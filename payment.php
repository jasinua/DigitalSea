<?php 
    session_start();
    include_once "controller/function.php";
    include "header/header.php";

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        var_dump($_SESSION); // Debug: This will output all session variables
        die("Session user_id is not set. Please log in.");
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
        margin: 50px 0 40px 0;
        font-size: 2.5rem;
        text-align:center;
        color: var(--page-text-color);
    }

    /* f5f5f0, f2efe9, f4f1ea */
    .paymentStuff form {
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
            margin: 25px 0 20px 0;
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
</style>
<body>
    <div class="page-wrapper">
    <h2>Make Your Payment</h2>
        <div class="paymentStuff">
            <form action="process.php" method="POST">
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
</script>
</html>
