<?php 
    include "header.php"
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
      background-color:white;
      color: var(--page-text-color);
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      margin: auto;
      margin-bottom: 40px;
      box-shadow: 0 0 5px #153147; 
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
  </style>
</head>
<body>
    <h2>Present Thy Payment</h2>
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

        <input type="submit" value="Submit Tribute">
    </form>

    <?php include "footer.php"?>
</body>
</html>
