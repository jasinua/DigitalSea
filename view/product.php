<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductNameqtu</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="style.css">
</head>
<?php include '../controller/home.inc.php'; ?>
<?php 
$product = $_GET["product"];
$data = getProductData($product);
$details = getProductDetails($product);
?>

<body>

<style>
    #container{
        display: flex;
        justify-content: center;
        align-items: center;
        width:100%;
        height:calc(100vh - 45px);
        background-color:var(--ivory-color);
        margin:auto;
    }

    #prodContainer{
        margin:20px;
        width:1200px;
        height:600px;
        background-color: white;
        border-radius:20px;
        display: flex;
        overflow:hidden;
        box-shadow: 0 0 5px var(--navy-color);
    }

    #productImg{
        width:60%;
        background-color: black;
        height:auto;
    }

    #info{
        width:100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: var(--page-text-color);
    }

    #name{
        width:100%;
        padding:10px;
        font-size:1.5em;
    }

    #details{
        width:100%;
        padding:10px;
        border-top: solid 3px var(--navy-color);
        border-bottom: solid 3px var(--navy-color);
    }

    .detail{
        font-size:1.1em;
        display: flex;
        justify-content: space-between;
    }

    #infoSide{
        width:40%;
        display: flex;
        flex-direction: column;
        padding:20px;
        padding-right:25px;
    }

    #buyForm{
        margin:10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height:100%;
    }

    #stock{
        width:100px;
        height:50px;
        font-size:25px;
        text-align: center;
        border-radius:10px;
        border: solid 2px var(--navy-color);
    }

    #buy{
        height:50px;
        border-radius:10px;
        margin-bottom:20px;
        background-color: var(--button-color);
        color:var(--text-color);
        border:none;
    }

    #buy:hover{
        cursor:pointer
    }

    #stockWrapper{
        height:auto;
    }

    #controlStock{
        display: flex;
        margin:auto;
        width:auto;
        align-self: center;
    }

    #controlStock button{
        width:50px;
        height:50px;
        margin: 0px 10px;
        border-radius:10px;
        font-size:30px;
        background-color:var(--navy-color);
        color:var(--text-color);
        border:none;
    }

    #controlStock button:hover{
        cursor: pointer;
    }

    /* i vjedha prej google qto per mi hek arrows */
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }

</style>

<?php include 'header/header.php' ?>
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
    <?php include 'footer/footer.php' ?>
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