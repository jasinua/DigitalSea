<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductNameqtu</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="style.css">
</head>
<?php include 'includes/home.inc.php'; ?>
<?php 
$product = $_GET["product"];
$data = getProductData($product);
$details = getProductDetails($product);
?>

<body>

<?php include 'header.php' ?>
    <div id='container'>
        <div id='prodContainer'>
            <img id='productImg' src="<?php echo $data[0]['image_url']; ?>" alt="">
            <div id='infoSide'>
                <div id='info'>
                    <p id='name'><?php echo $data[0]['description']?></p>
                    <div id='details'>
                        <?php foreach($details as $detail){?>
                        <div class='detail'><p><?php echo $detail['prod_desc1']?>:</p><p><?php echo $detail['prod_desc2']?></p>
                        </div>
                        <?php }?>
                    </div>
                </div>
                <form action='product.php' id='buyForm' method='post'>
                
                <div id='stockWrapper'>
                    <div id='controlStock'>
                        <button type='button' class='stockController' onclick='addToQuantity(-1)'>-</button>
                            <input id='stock' value='1' min='1' max='<?php echo $data[0]['stock']; ?>' name='quant' type="number">
                        <button type='button' class='stockController' onclick='addToQuantity(1)'>+</button>
                    </div>
                </div>
                
                        
                    <input id='buy' type='submit' value='Add to cart'>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php' ?>
    <script>
        function addToQuantity(add){
            var amount = parseInt(document.getElementById('stock').value);
            if(amount==1 && add<0){
                document.getElementById('stock').value = 1;
            }else if(amount>=1){
                document.getElementById('stock').value = amount+add;
            }
        }
    </script>
</body>

</html>