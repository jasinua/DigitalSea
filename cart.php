<?php 

    include_once "includes/function.php";
    session_start();


    if(isLoggedIn($_SESSION['user_id'])) {


        $res = returnCart($_SESSION['user_id']);

?>

<?php include "header.php";?>
<link rel="stylesheet" href="style.css">
        <div class="wishlist-container">
            <?php foreach($res as $cart) {
                $product_result = returnProduct($cart['product_id']);


            ?>
            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="<?php echo $product_result['image_url'] ?>" alt="prod"></div>
                <div class="wishlist-product-title"><?php $product_result['name'];?></div>
            </div>
            <?php }?>


        </div>
<?php

    } else {
        header("Location: homepage.php");
    }
?>