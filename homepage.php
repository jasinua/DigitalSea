<?php 
    session_start();
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="style.css">
</head>
<?php include 'includes/home.inc.php'; ?>
<body>

<?php include 'header.php' ?>
    <div id='container'>

    
        <div id='filters'>
            <form id='filterForm' action="homepage.php" method='post'>
                <div id='filtOpts'>
                    <input style='padding:5px 10px' type='reset' value='Clear Filters' onclick='window.location.reload()'>
                    <input style='padding:5px 10px' type='submit' value='Apply Filters'>
                </div>
                <ul>
                    <?php foreach (getData("SELECT DISTINCT name FROM products") as $prod) { ?>
                    <li class='filter'><input type="checkbox" name='filter[]' id="<?php echo $prod['name'] ?>" value="<?php echo $prod['name'] ?>"><?php echo $prod['name'] ?></li>
                    <?php } ?>
                </ul>
            </form>
        </div>


        <div id='items'>
        <?php if(!isset($_POST['filter'])){ ?>   
             
            <h1 id='topItemsHeader'>Top items</h1>
            <div class='itemLine' id='topItems'>
                <?php foreach (getData("SELECT * FROM products WHERE products.price>900") as $prod) { ?>
                    <div class='item'>
                        <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>";' src="<?php echo $prod['image_url'] ?>" alt="">
                        <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                        <p class='price'><?php echo $prod['price'] ?>&euro;</p>
                    </div>
                <?php } ?>
            </div>
            <h1>More items</h1>
            <div class='itemBox' id='randomItems'>

                <?php foreach (getData("SELECT * FROM products") as $prod) { ?>
                    <div class='item'>
                        <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>";' src="<?php echo $prod['image_url'] ?>" alt="">
                        <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                        <p class='price'><?php echo $prod['price'] ?>&euro;</p>
                    </div>
                <?php } ?>

            </div>
        <?php }else{?>
            <h1>Filtered Items</h1>
            <div class='itemBox' id='randomItems'>
            <?php foreach($_POST['filter'] as $filter) { ?>
                <script>
                    document.getElementById("<?php echo $filter?>").checked = true;
                </script>
                <?php foreach (getData("SELECT * FROM products WHERE products.name='$filter'") as $prod) { ?>
                    <div class='item'>
                        <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>";' src="<?php echo $prod['image_url'] ?>" alt="">
                        <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                        <p class='price'><?php echo $prod['price'] ?>&euro;</p>
                    </div>
                <?php } ?>
            <?php }?>
                

            </div>
        <?php }?>

            
        </div>
    </div>
    <?php //include 'footer.php'?>
</body>

</html>