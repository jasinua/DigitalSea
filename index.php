<?php 
    session_start();
    include 'model/dbh.inc.php';
    include 'controller/home.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalSea</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<style>
    .page-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    #container {
        display: flex;
        flex: 1;
        min-height: calc(100vh - 120px);
    }

    #container #moreItemsText {
        text-align: center;
    }

    #filters {
        width: 280px;
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateX(-100%);
        opacity: 0;
        transition: var(--transition);
    }

    #filters.open {
        transform: translateX(0);
        opacity: 1;
    }

    #filter-toggle {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        margin: 20px 0;
        display: inline-block;
    }

    #filter-toggle:hover {
        background-color: var(--button-color-hover);
    }

    .filter {
        width: fit-content;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 10px;
        color: var(--page-text-color);
    }

    #filtOpts {
        display: flex;
        width: auto;
        justify-content: space-between;
    }

    #filtOpts input {
        width: auto;
        border-radius: 10px;
        font-size: 12px;
        height: 30px;
        background-color: var(--button-color);
        border: none;
        color: white;
        font-size: 0.9em;
        padding: 5px 10px;
        margin: 10px;
        transition: background-color 0.5s;
    }

    #filtOpts input:hover {
        background-color: var(--noir-color);
        cursor: pointer;
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

    #items {
        background-color: var(--ivory-color);
        min-width: 100%;
        padding: 0 20px;
        flex: 1;
        transform: translateX(-280px);
        transition: var(--transition);
    }

    #filters.open + #items {
        min-width:0px;
        transform: translateX(0px);
    }

    .itemLine {
        display: flex;
        width: auto;
        margin: 20px;
        overflow-x: auto;
        padding-bottom: 20px;
    }

    .itemBox {
        align-items: center;
        display: flex;
        justify-content: center;
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
        box-shadow: 0 0 10px #55555563;
        transition: var(--transition);
        position: relative;
    }

    .item:hover {
        transform: translateY(-6px);
    }

    .item img {
        width: 100%;
        padding: 20px;
        transition: var(--transition);
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
        text-align: right;
        font-weight: 600;
    }

    .item:hover {
        cursor: pointer;
    }

    #newItemsItem {
        width: 330px;
        min-width: 300px;
        height: 440px;
        margin: 15px;
        background-color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-radius: 10px;
        box-shadow: 0 0 10px #55555563;
        transition: var(--transition);
        position: relative;
        padding-bottom: 50px; /* Make more bottom space */
        overflow: hidden; /* optional: prevent overflow */
    }

    #newItemsItem img {
        height: 280px;
        object-fit: contain;
        width: 100%;
        padding: 20px;
    }

    #newItemsItem .title {
        font-size: 15px;
        text-align: center;
        margin: 10px 10px 0 10px; /* some breathing room */
        min-height: 50px; /* enough space for 2 lines */
        overflow: hidden;
    }

    #newItemsItem .price {
        position: absolute;
        bottom: 10px;
        right: 10px;
        font-size: 17px;
        font-weight: 600;
        color: black;
    }       

    #container h1 {
        color: var(--noir-color);
        margin-left: 20px;
    }

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

    a {
        text-decoration:none;
        color:var(--page-text-color);
    }

    .wheel-carousel {
        width: 100%;
        overflow: hidden;
        position: relative;
        height: 500px;
        padding: 0; /* No padding */
        box-sizing: border-box;
    }

    .wheel-track {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .wheel-item {
        width: 300px;
        height: 400px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 15px #aaa;
        transition: var(--transition);
        padding: 15px;
        position: absolute;
        top: 50px;
        left: 600px;
        opacity: 0;
    }

    .wheel-item img {
        width: 100%;
        height: 300px;
        object-fit: contain;
        transition: var(--transition);
    }

    .wheel-item img:hover {
        transform: scale(1.1);
    }

    .wheel-item.active {
        transform: translateY(-20px) scale(1);
        z-index: 4;
        opacity: 1;
    }

    .wheel-item.left {
        transform: scale(0.9) translateX(-340px);
        opacity: 0.7;
        z-index: 3;
    }

    .wheel-item.right {
        transform: scale(0.9) translateX(340px);
        opacity: 0.7;
        z-index: 3;
    }

    .wheel-item.far-left {
        transform: scale(0.8) translateX(-730px) translateY(25px);
        opacity: 0.5;
        z-index: 2;
    }

    .wheel-item.far-right {
        transform: scale(0.8) translateX(730px) translateY(25px);
        opacity: 0.5;
        z-index: 2;
    }

    .wheel-item.hidden {
        opacity: 0;
        pointer-events: none;
    }

    #newItems {
        overflow-x: hidden;
        display: flex;
        margin: 20px;
        padding-bottom: 20px;
    }

    .new-badge {
        position: absolute;
        color: rgb(42, 175, 169);
        font-size: 15px;
        font-weight: bold;
        padding: 3px 6px;
        border-radius: 5px;
        top: 10px;
        left: 10px;
    }
</style>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php" ?>
        <div id='container'>
            <div id='filters'>
                <form id='filterForm' action="index.php" method='post'>
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
                <button id="filter-toggle">Filters</button>
                <?php if(!isset($_POST['filter'])) { ?>   
                    <h1 id='topItemsHeader'>Top Products</h1>
                    <div class='wheel-carousel'>
                        <div class='wheel-track' id='topItems'>
                            <?php foreach (getData("SELECT * FROM products WHERE products.price>900") as $prod) { ?>
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>">
                                    <div class='wheel-item'>
                                        <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="<?php echo $prod['description'] ?>">
                                        <p class='title'><?php echo $prod['description'] ?>
                                        <!-- <p class='price'><?php echo number_format($prod['price'], 0, '.', ',') ?>€</p> -->
                                </div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <h1 id='newItemsHeader'>New Products</h1>
                    <div class='itemLine' id='newItems'>
                        <?php foreach (getData("SELECT * FROM products ORDER BY product_id DESC LIMIT 8") as $prod) { ?>
                            <div class='item' id="newItemsItem">
                                <div class="new-badge">NEW</div>
                                <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                                <p class='price'><?php echo number_format($prod['price'], 0, '.', ',') ?>€</p>
                            </div>
                        <?php } ?>
                    </div>

                    <h1 id="moreItemsText">More Products</h1>
                    <div class='itemBox' id='randomItems'>
                        <?php foreach (getData("SELECT * FROM products") as $prod) { ?>
                            <div class='item' id="newItems">
                                <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                                <p class='price'><?php echo number_format($prod['price'], 0, '.', ',') ?>€</p>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <h1>Filtered Products</h1>
                    <div class='itemBox' id='randomItems'>
                        <?php foreach($_POST['filter'] as $filter) { ?>
                            <script>
                                document.getElementById("<?php echo $filter?>").checked = true;
                            </script>
                            <?php foreach (getData("SELECT * FROM products WHERE products.name='$filter'") as $prod) { ?>
                                <div class='item'>
                                    <img onclick='window.location="product.php?product=<?php echo $prod['product_id'] ?>"' src="<?php echo $prod['image_url'] ?>" alt="">
                                    <a href="product.php?product=<?php echo $prod['product_id'] ?>" class='title'><?php echo $prod['description'] ?></a>
                                    <p class='price'><?php echo number_format($prod['price'], 0, '.', ',') ?>€</p>
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
<script>
    // Filter toggle functionality
    const filterToggle = document.getElementById('filter-toggle');
    const filters = document.getElementById('filters');

    filterToggle.addEventListener('click', () => {
        filters.classList.toggle('open');
    });

    // Close filter panel when clicking outside
    document.addEventListener('click', (e) => {
        if (!filters.contains(e.target) && e.target !== filterToggle) {
            filters.classList.remove('open');
        }
    });

    // Wheel carousel functionality
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.getElementById('topItems');
        const items = track.querySelectorAll('.wheel-item');
        const itemCount = items.length;
        
        if (itemCount === 0) return;
        
        let currentIndex = 0;

        function updateCarousel() {
            items.forEach((item, index) => {
                item.classList.remove('active', 'left', 'right', 'far-left', 'far-right', 'hidden');
                
                const relativePos = (index - currentIndex + itemCount) % itemCount;
                
                if (relativePos === 0) {
                    item.classList.add('active');
                } else if (relativePos === itemCount - 1) {
                    item.classList.add('left');
                } else if (relativePos === 1) {
                    item.classList.add('right');
                } else if (relativePos === itemCount - 2) {
                    item.classList.add('far-left');
                } else if (relativePos === 2) {
                    item.classList.add('far-right');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
        
        // Initialize carousel
        updateCarousel();
        
        // Auto-rotate every 3 seconds
        setInterval(() => {
            currentIndex = (currentIndex + 1) % itemCount;
            updateCarousel();
        }, 3000);
        
        // Manual navigation
        document.querySelector('.wheel-carousel').addEventListener('click', (e) => {
            const clickedItem = e.target.closest('.wheel-item');
            if (!clickedItem) return;
            
            if (clickedItem.classList.contains('left')) {
                currentIndex = (currentIndex - 1 + itemCount) % itemCount;
            } else if (clickedItem.classList.contains('right')) {
                currentIndex = (currentIndex + 1) % itemCount;
            } else if (clickedItem.classList.contains('far-left')) {
                currentIndex = (currentIndex - 2 + itemCount) % itemCount;
            } else if (clickedItem.classList.contains('far-right')) {
                currentIndex = (currentIndex + 2) % itemCount;
            }
            updateCarousel();
        });
    });
    
    // Infinite scroll for new items
    window.addEventListener('load', () => {
        const container = document.getElementById('newItems');
        const clone = container.innerHTML;
        container.innerHTML += clone;
        let scrollSpeed = -1;

        function scrollLoop() {
            container.scrollLeft += scrollSpeed;
            if (container.scrollLeft <= 0) {
                container.scrollLeft = container.scrollWidth / 2;
            }
            requestAnimationFrame(scrollLoop);
        }

        scrollLoop();
    });  
</script>
</html>