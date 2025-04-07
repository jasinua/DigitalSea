<?php 
    session_start();
    include '../controller/home.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        :root {
            --background-color: #f5f5fa;       
            --text-color: white;
            --page-text-color: #232a2f;
            --modal-bg-color: white;            
            --button-color: #153147;
            --button-color-hover:rgb(26, 78, 118);
            --noir-color: #232a2f;
            --navy-color: #153147;
            --mist-color: #adb8bb;
            --almond-color: #edeae4;
            --ivory-color: #f9f8f7;
            --footer-items-color: #adb8bb;
        }
        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 120px); /* Adjust based on header/footer height */
        }

        #filters {
            display: flex;
            flex-direction: column;
            background-color: white;
            width: 15%;
            min-height: 100%;
            border-right: solid 2px var(--navy-color);
            padding-bottom: 20px;
        }

        .filter {
            width: fit-content;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px;
            border-radius: 10px;
            color: var(--page-text-color);
        }

        #filtOpts {
            display: flex;
            margin: 10px;
            justify-content: space-between;
        }

        #filtOpts input {
            width: auto;
            border-radius: 10px;
            font-size: 12px;
            height: 30px;
            background-color: white;
            accent-color: var(--navy-color);
            transition: background-color ease 0.5s;
            background-color: var(--navy-color);
            border: none;
            color: white;
            font-size: 0.9em;
            padding: 5px 10px;
        }

        #filters ul {
            display: flex;
            flex-direction: column;
            list-style-type: none;
            padding: 0;
        }

        #filters ul input {
            width: 50px;
            border-radius: 10px;
            font-size: 12px;
            height: 30px;
            background-color: white;
            accent-color: var(--navy-color);
            transition: background-color ease 0.5s;
            margin-right: 10px;
        }

        #filters ul input:hover {
            background-color: black;
        }

        #filtOpts input:hover {
            background-color: var(--noir-color);
            cursor: pointer;
        }

        #items {
            background-color: var(--ivory-color);
            width: 85%;
            padding: 20px;
            flex: 1;
        }

        .itemLine {
            display: flex;
            width: auto;
            margin: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
        }

        .itemBox {
            display: flex;
            flex-wrap: wrap;
            margin: 17px;
            
        }

        .item {
            min-width: 225px;
            width: 225px;
            margin: 10px;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 10px;
            box-shadow: 0 0 5px var(--navy-color);
            transition: transform 0.3s ease;
        }

        .item:hover {
            transform: translateY(-5px);
        }

        .item img {
            width: 100%;
            padding: 20px;
            transition: padding ease 0.5s;
            object-fit: contain;
            height: 180px;
        }

        .item:hover img {
            padding: 10px;
        }

        .item .title {
            margin: 10px;
            color: grey;
            width: 100%;
            height: 40px;
            font-size: 13px;
            overflow: hidden;
            padding: 0 5px 5px 10px;
            text-decoration: none;
        }

        .item .price {
            color: black;
            width: 100%;
            height: 30px;
            font-size: 17px;
            overflow: hidden;
            padding: 0 5px 5px 10px;
        }

        .item:hover {
            cursor: pointer;
        }

        #container h1 {
            color: var(--noir-color);
            margin-left: 20px;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php" ?>
        <div id='container'>
            <div id='filters'>
                <form id='filterForm' action="homepage.php" method='post'>
                    <div id='filtOpts'>
                        <input type='reset' value='Clear Filters' onclick='window.location.reload()'>
                        <input type='submit' value='Apply Filters'>
                    </div>
                    <ul>
                        <?php foreach (getData("SELECT DISTINCT name FROM products") as $prod) { ?>
                        <li class='filter'>
                            <input type="checkbox" name='filter[]' id="<?php echo $prod['name'] ?>" value="<?php echo $prod['name'] ?>">
                            <label for="<?php echo $prod['name'] ?>"><?php echo $prod['name'] ?></label>
                        </li>
                        <?php } ?>
                    </ul>
                </form>
            </div>

            <div id='items'>
                <?php if(!isset($_POST['filter'])) { ?>   
                    <h1 id='topItemsHeader'>Top items</h1>
                    <div class='itemLine' id='topItems'>
                        <?php foreach (getData("SELECT * FROM products WHERE products.price>900") as $prod) { ?>
                            <div class='item'>
                                <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                                <p class='price'><?php echo $prod['price'] ?>&euro;</p>
                            </div>
                        <?php } ?>
                    </div>
                    <h1>More items</h1>
                    <div class='itemBox' id='randomItems'>
                        <?php foreach (getData("SELECT * FROM products") as $prod) { ?>
                            <div class='item'>
                                <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                                <p class='price'><?php echo $prod['price'] ?>&euro;</p>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <h1>Filtered Items</h1>
                    <div class='itemBox' id='randomItems'>
                        <?php foreach($_POST['filter'] as $filter) { ?>
                            <script>
                                document.getElementById("<?php echo $filter?>").checked = true;
                            </script>
                            <?php foreach (getData("SELECT * FROM products WHERE products.name='$filter'") as $prod) { ?>
                                <div class='item'>
                                    <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                    <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                                    <p class='price'><?php echo $prod['price'] ?>&euro;</p>
                                </div>
                            <?php } ?>
                        <?php } ?>   
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php include "footer/footer.php" ?>
    </div>
</body>
</html>