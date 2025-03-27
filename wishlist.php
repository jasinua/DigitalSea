<?php 

    include_once "includes/function.php";
    session_start();


    if(isLoggedIn($_SESSION['user_id'])) {
?>
<link rel="stylesheet" href="style.css">
        <div class="wishlist-container">
            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>

            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>

            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>
            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>
            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>
              <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>

            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>

            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>

            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>

            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>

            <div class="wishlist-products-container">
                <div class="wishlist-image-container"><img src="" alt=""></div>
                <div class="wishlist-product-title">Hello world</div>
            </div>
        </div>


<?php

    } else {
        header("Location: homepage.php");
    }
?>