<?php 
    session_start();
    include_once "../controller/function.php";
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
  <title>Offer Thy Tribute</title>
  <style>

    
    h2 {
        margin: 15px;
        text-align:center;
        color: var(--page-text-color);
    }

/* f5f5f0, f2efe9, f4f1ea */
    form {
      background-color: white;
      color: var(--page-text-color);
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      margin: auto;
      margin-bottom: 40px;
      box-shadow: 0 0px 5px var(--navy-color);
    }

    input[type="text"], input[type="number"]{
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      background-color: var(--ivory-color);
      color: var(--page-text-color);
      border-radius: 5px;
      border: 1px solid var(--navy-color)
    }

    input[type="submit"] {
      background-color: var(--navy-color);
      cursor: pointer;
      font-size: 15px;
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: none;
      color: var(--text-color);
      border-radius: 5px;
    }

    .paymentStuff{
        display: flex;
        flex-direction: row;
        margin: 10px 300px;
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
        width: 170px;
    }

    .cvv input {
        width: 170px;
    }

     /* ===== Card Visual ===== */
     .card-container {
        perspective: 1000px;
        width: 100%;
        max-width: 350px;
        margin: auto auto;
    }

    .card {
        width: 100%;
        height: 200px;
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
        padding: 1.5rem;
        color: #ffffff;
        font-family: 'Courier New', Courier, monospace;
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
        font-size: 1.2rem;
        letter-spacing: 2px;
    }

    .card-details {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
    }

    .card-chip {
        width: 40px;
        height: 30px;
        background: #d4d4d4;
        border-radius: 4px;
    }

    .card-cvc {
        font-size: 1.2rem;
        letter-spacing: 2px;
    }

    .magnetic-strip {
        width: 100%;
        height: 30px;
        background: #2d3748;
        margin-bottom: 1rem;
    }
</style>
</head>
<body>
    <div class="page-wrapper">
    <h2>Present Thy Payment</h2>
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
</script>
</html>
