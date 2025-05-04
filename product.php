<?php
session_start();
include 'controller/home.inc.php';

$productID = $_GET["product"];
$data = getProductData($productID);
$details = getProductDetails($productID);

if (isset($_POST['addToCart'])) {
    addToCart($_SESSION['user_id'], $productID, $_POST['quantity'], $_POST['quantity'] * $data['price']);
    header("Location: cart.php");
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductNameqtu</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="style.css">
</head>
<style>
    #container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: calc(100vh - 45px);
        background-color: var(--ivory-color);
        margin: auto;
    }

    #prodContainer {
        margin: 20px;
        width: 1200px;
        height: 600px;
        background-color: white;
        border-radius: 20px;
        display: flex;
        overflow: hidden;
        box-shadow: 0 0 5px var(--navy-color);
        color: var(--page-text-color);
    }

    #productImg {
        width: auto;
        margin: auto;
        height: inherit;
    }

    #info {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #name {
        width: 100%;
        padding: 10px;
        font-size: 1.5em;
    }

    #details {
        width: 100%;
        padding: 10px;
        border-top: solid 3px var(--navy-color);
        border-bottom: solid 3px var(--navy-color);
    }

    .detail {
        font-size: 1.1em;
        display: flex;
        justify-content: space-between;
    }

    #infoSide {
        width: 40%;
        display: flex;
        flex-direction: column;
        padding: 20px;
        padding-right: 25px;
    }

    #buyForm {
        margin: 10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    #stock {
        width: auto;
        height: 100%;
        font-size: 20px;
        text-align: center;
        border: none;
    }

    #buy {
        height: 50px;
        border-radius: 10px;
        margin-bottom: 20px;
        background-color: var(--button-color);
        color: var(--text-color);
        border: none;
        font-size:18px;
    }

    #buy:hover {
        cursor: pointer
    }

    #stockWrapper {
        height: 35px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #controlStock {
        display: flex;
        width: auto;
        height: 100%;
        align-self: center;

    }

    #controlStock button {
        min-width: 35px;
        height: 100%;
        margin: 0px 3px;
        border-radius: 8px;
        font-size: 20px;
        background-color: var(--navy-color);
        color: var(--text-color);
        border: none;
    }

    #controlStock button:hover {
        cursor: pointer;
    }

    /* i vjedha prej google qto per mi hek arrows */
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<body>
    <?php include 'header/header.php' ?>
    <div id='container'>
        <div id='prodContainer'>
            <img id='productImg' src="<?php echo $data['image_url']; ?>" alt="">
            <div id='infoSide'>
                <div id='info'>
                    <p id='name'><?php echo $data['description'] ?></p>
                    <div id='details'>
                        <?php foreach ($details as $detail) { ?>
                            <div class='detail'>
                                <p><?php echo $detail[0] ?>:</p>
                                <p><?php echo $detail[1] ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <form action='product.php?product=<?php echo $productID ?>' id='buyForm' method='post'>

                    <div id='stockWrapper'>
                        <p style="font-weight:bold;font-size:1.3em;" class='detail'>Quantity:</p>
                        <div id='controlStock'>
                            <button type='button' class='stockController' onclick='addToQuantity(-1)'>-</button>
                            <input id='stock' value='1' min='1' max='<?php echo $data['stock']; ?>' name='quantity' type="number" placeholder='1'>
                            <button type='button' class='stockController' onclick='addToQuantity(1)'>+</button>
                        </div>
                    </div>

                    <input type="hidden" value='<?php echo $productID ?>' name='prodID'>
                    <div style='display:flex;flex-direction:column;'>
                        <div style='display:flex;justify-content:space-between'>
                            <p style="font-weight:bold;font-size:1.3em;" class='detail'>Price:</p>
                            <p id='stockPrice' style="font-weight:bold;font-size:1.3em;"><?php echo $data['price'] ?>&euro;</p>
                        </div>
                        <input id='buy' type='submit' name='addToCart' value='Add to cart'>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <?php include 'footer/footer.php' ?>
    <script>
        const price = <?php echo $data['price'] ?>.toFixed(2)
        console.log(price)

        function addToQuantity(add) {
            var amount = parseInt(document.getElementById('stock').value);
            if (amount == 1 && add < 0) {
                document.getElementById('stock').value = 1;
                document.getElementById('stockPrice').innerHTML = (price*amount).toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + "&euro;"
                
            } else if (amount >= 1) {
                document.getElementById('stock').value = amount + add;
                document.getElementById('stockPrice').innerHTML = (price*parseInt(document.getElementById('stock').value)).toLocaleString('us', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + "&euro;"
            }
        }
    </script>
</body>


</html>