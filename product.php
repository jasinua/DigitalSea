<?php
    session_start();
    include 'controller/home.inc.php';

    // function getImageSource($product_id, $image_url) {
    //     $local_image = "images/product_$product_id.png";
    //     return file_exists($local_image) ? $local_image : htmlspecialchars($image_url);
    // }

    $productID = $_GET["product"];
    $data = getProductData($productID);
    $details = getProductDetails($productID);

    // Get wishlist items for the current user
    $wishlist_items = isset($_SESSION['user_id']) ? getWishlistItems($_SESSION['user_id']) : [];
    $is_in_wishlist = in_array($productID, $wishlist_items);

    if (isset($_POST['addToCart'])) {
        if(isset($_SESSION['user_id'])){
            addToCart($_SESSION['user_id'], $productID, $_POST['quantity'], $_POST['quantity'] * $data['price']);
            header("Location: cart.php");
        }else{
            $_SESSION['last_page'] = $_SERVER['HTTP_REFERER'];
            $_SESSION['redirect_back'] = true;
            header("Location: login.php");
        }
    }

    if ($data){ 
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['name']) ? htmlspecialchars($data['name']) : 'Product'; ?></title>
</head>


<?php include "css/product-css.php"; ?>

<body>
    <?php include 'header/header.php' ?>
    <div id='container'>
        <div id='prodContainer'>
            
            <button class="wishlist-btn <?php echo $is_in_wishlist ? 'active' : ''; ?>" data-product-id="<?php echo $productID; ?>">
                <i class="<?php echo $is_in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
            </button>
            <div style='width:50%;display: flex; justify-content: center; align-items: center; position: relative;'>
                <img id='productImg' src="<?php echo getImageSource($productID, $data['image_url']); ?>" alt="<?php echo $data['description']; ?>" draggable='false'>
                <?php if ($data['discount'] > 0) { ?>
                    <div class="discount-badge">-<?php echo $data['discount'] ?>%</div>
                <?php } ?>
            </div>
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
                <div id='rating' '>
                    <p>Rating:</p>
                    <?php if(isset($_SESSION['user_id'])){ ?>
                        <div id='stars'>
                            <?php include 'ratingPart.php'; ?>
                            <button id='rate' onclick="submitRating()">Rate</button>
                        </div>
                    <?php } ?>
                    <p id="average-rating"><?php 
                        $rating_sql = "SELECT AVG(rating) as avg_rating FROM product_ratings WHERE product_id = ?";
                        $rating_stmt = $conn->prepare($rating_sql);
                        $rating_stmt->bind_param("i", $productID);
                        $rating_stmt->execute();
                        $rating_result = $rating_stmt->get_result();
                        $rating_data = $rating_result->fetch_assoc();
                        echo number_format($rating_data['avg_rating'] ?? 0, 2);
                    ?></p>
                </div>
                <form action='product.php?product=<?php echo $productID ?>' id='buyForm' method='post'>
                    <div id='stockWrapper'>
                        <p class='price-label'>Quantity:</p>
                        <div id='controlStock'>
                            <button type='button' class='stockController' onclick='addToQuantity(-1)'>-</button>
                            <input id='stock' value='1' min='1' max='<?php echo $data['stock']; ?>' name='quantity' type="number" placeholder='1'>
                            <button type='button' class='stockController' onclick='addToQuantity(1)'>+</button>
                        </div>
                    </div>

                    <input type="hidden" value='<?php echo $productID ?>' name='prodID'>
                    <input type="hidden" value='<?php echo $data['price'] ?>' name='price' id='price'>
                    <input type="hidden" value='<?php echo $data['discount'] ?>' name='discount' id='discount'>
                    <div class='price-section'>
                        <div class='price-row'>
                            <p class='price-label'>Price:</p>
                            <?php if ($data['discount'] > 0) { 
                                $originalPrice = $data['price'];
                                $discountedPrice = $originalPrice * (1 - $data['discount'] / 100);
                            ?>
                                <div class='price-value'>
                                    <span id="original-price"><?php echo number_format($originalPrice, 2, '.', ',') ?>€</span>
                                    <span id="discounted-price"><?php echo number_format($discountedPrice, 2, '.', ',') ?>€</span>
                                </div>
                            <?php } else { ?>
                                <p class='price-value'><?php echo number_format($data['price'], 2, '.', ',') ?>€</p>
                            <?php } ?>
                        </div>
                        <?php if($data['stock'] > 0){ ?>
                            <input id='buy' type='submit' value='Add to cart' onclick="if(document.getElementById('stock').value <= <?php echo $data['stock'] ?>){this.disabled = true; this.value = 'Adding...'; document.getElementById('buy2').click();}" >
                            <input id='buy2' type="submit" name="addToCart" value="Add to cart" style="display: none;" hidden>
                        <?php }else{ ?>
                            <input id='buy' type='submit' name='addToCart' value='Out of stock' disabled style='background-color: #ccc; cursor: not-allowed;'>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer/footer.php' ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle wishlist button click
            $('.wishlist-btn').click(function(e) {
                e.preventDefault();
                const button = $(this);
                
                fetch('controller/add_to_wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}&url=${window.location.href}`
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.text();
                })
                .then(result => {
                    if (!result) return; // Skip if we redirected
                    result = result.trim();
                    if (result === 'added') {
                        button.addClass('active').find('i').removeClass('far').addClass('fas');
                    } else if (result === 'removed') {
                        button.removeClass('active').find('i').removeClass('fas').addClass('far');
                    } else if (result === 'error') {
                        console.error('Error updating wishlist');
                        alert('Error updating wishlist. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating wishlist. Please try again.');
                });
            });

            <?php if(isset($_SESSION['add_to_wishlist_in_product_page'])){ 
                echo "$('.wishlist-btn').click();";
                unset($_SESSION['add_to_wishlist_in_product_page']); 
            } ?>

            // Show/hide clear button based on search input
            $('.search-input').on('input', function() {
                var $clearBtn = $(this).closest('form').find('.clear-search');
                if ($(this).val().length > 0) {
                    $clearBtn.show();
                } else {
                    $clearBtn.hide();
                }
            });
            // Clear search without redirecting or reloading
            $('.clear-search').on('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $input = $(this).closest('form').find('.search-input');
                $input.val('');
                $(this).hide();
                $input.focus();
            });
            // Initialize clear button visibility for each search bar
            $('.search-input').each(function() {
                var $clearBtn = $(this).closest('form').find('.clear-search');
                if ($(this).val().length > 0) {
                    $clearBtn.show();
                } else {
                    $clearBtn.hide();
                }
            });

            // Initialize stars based on user's previous rating
            <?php
            if (isset($_SESSION['user_id'])) {
                $user_rating_sql = "SELECT rating FROM product_ratings WHERE product_id = ? AND user_id = ?";
                $user_rating_stmt = $conn->prepare($user_rating_sql);
                $user_rating_stmt->bind_param("ii", $productID, $_SESSION['user_id']);
                $user_rating_stmt->execute();
                $user_rating_result = $user_rating_stmt->get_result();
                if ($user_rating_result->num_rows > 0) {
                    $user_rating = $user_rating_result->fetch_assoc()['rating'];
                    echo "initializeStars($user_rating);";
                }
            }
            ?>
        });

        function addToQuantity(add) {
            var amount = parseInt(document.getElementById('stock').value);
            if (amount == 1 && add < 0) {
                document.getElementById('stock').value = 1;
                updatePrice(1);
            } else if (amount >= 1) {
                document.getElementById('stock').value = amount + add;
                updatePrice(amount + add);
            }
        }

        function updatePrice(amount) {
            const price = parseFloat(document.getElementById('price').value); 
            const discount = parseFloat(document.getElementById('discount').value);

            const totalPrice = (price * amount).toFixed(2);
            if(discount > 0){
                const discountedPrice = (price * (1 - discount/100) * amount).toFixed(2);
                document.getElementById('discounted-price').innerHTML = formatPrice(discountedPrice) + "€";
                document.getElementById('original-price').innerHTML = formatPrice(totalPrice) + "€";
            } else {
                document.querySelector('.price-value').innerHTML = formatPrice(totalPrice) + "€";
            }
        }

        function formatPrice(price) {
            // Convert to number and format with thousand separators
            return parseFloat(price).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function submitRating() {
            const stars = document.querySelectorAll(".star");
            let rating = 0;
            let under = true;
            
            stars.forEach(element => {
                if (!element.classList.contains("unchecked")) {
                    rating += 1;
                }
            });

            
            document.getElementById('rate').textContent = 'Rating..';


            $.ajax({
                url: 'controller/add_rating.php',
                method: 'POST',
                data: {
                    product_id: <?php echo $productID; ?>,
                    rating: rating
                },
                success: function(response) {
                    const result = response
                    if (result['status'] === 'success') {
                        updateAverageRating();
                    } else {
                        alert(result['message']);
                    }
                },
                error: function() {
                    alert('An error occurred while submitting the rating');
                }
            });
        }

        function updateAverageRating() {


            $.ajax({
                url: 'controller/get_average_rating.php',
                method: 'POST',
                data: {
                    product_id: <?php echo $productID; ?>
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        document.getElementById('average-rating').textContent = result.average_rating;
                        document.getElementById('rate').textContent = 'Rate';
                    }else{
                        alert(result.message);
                    }
                },
                error: function() {
                    alert('An error occurred while updating the average rating');
                }
            });
        }

        function initializeStars(rating) {
            const stars = document.querySelectorAll(".star");
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove("unchecked");
                    star.classList.add("checked");
                } else {
                    star.classList.remove("checked");
                    star.classList.add("unchecked");
                }
            });
        }

        localStorage.setItem('last_page', window.location.href);
    </script>
    </body>
</html>

<?php
    }else{
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>404 Product Not Found</title>
        </head>
        <body style='margin: 0; padding: 0; '>
        <?php include 'header/header.php' ?>
            <div id='container' style='background: linear-gradient(to right, rgb(69, 110, 142) 0%, rgb(26, 78, 118) 6%, var(--noir-color) 16%, 
                    var(--noir-color) 100%);'>
                <div id='prodContainer' style='display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 100px); flex-direction: column;'>
                    <p style='font-size: 160px; font-weight: bold; color: #fff; margin-bottom: 0px; margin-top: 0px;'>404</p>
                    <p style='font-size: 20px; color: #fff; margin-top: 0px;'>Product not found</p>
                    <a onclick='window.history.back();' style='font-size: 20px; color: #fff;'>Go back</a>
                </div>
            </div>
            <?php include 'footer/footer.php' ?>
            
        </body>
        <?php
    }
?>