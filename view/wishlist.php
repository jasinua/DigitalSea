<?php 
include_once "../controller/function.php";
session_start();

if (isLoggedIn($_SESSION['user_id'])) {
    $res = returnWishList($_SESSION['user_id']);
    include "header/header.php";
?>
<link rel="stylesheet" href="style.css">
<style>
    .wishlist-container {
        /* max-width: 1300px; */
        min-width: 1100px;
        margin: 50px auto;
        background-color: var(--modal-bg-color);
        color: var(--page-text-color);
        padding: 20px;
        border-radius: 10px;
    }

    .wishlist-container h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background-color: var(--background-color);
    }

    th, td {
        padding: 15px;
        text-align: center;
        border-bottom: 2px solid var(--mist-color);
    }

    td:first-child, th:first-child {
        text-align: left;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .product-info img {
        width: 70px;
        height: 90px;
        object-fit: contain;
        border-radius: 6px;
        
        transition:ease-out 0.2s;
    }

    .product-info img:hover {
        scale:1.2;
        transition:ease-out 0.2s;
    }

    .remove-btn {
        background: none;
        border: none;
        font-size: 18px;
        color: #888;
        cursor: pointer;
    }

    .add-to-cart-btn {
        background-color: var(--button-color);
        color: var(--text-color);
        border: none;
        padding: 10px 18px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        /* text-transform: uppercase; */
    }

    .original-price {
        text-decoration: line-through;
        color: var(--page-text-color);
        margin-right: 5px;
    }

    .discounted-price {
        color: #000;
        font-weight: bold;
    }
</style>
<div class="page-wrapper">
    <div class="wishlist-container">
        <h2>My Wishlist ✎</h2>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Product Name</th>
                    <th>Unit Price</th>
                    <th>Stock Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($res as $wishlist) {
                    $product_result = returnProduct($wishlist['product_id']);
                    $product = $product_result->fetch_assoc();
                ?>
                <tr>
                    <td>
                        <form method="post" action="remove_from_wishlist.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <button class="remove-btn" type="submit">&times;</button>
                        </form>
                    </td>
                    <td>
                        <a style="text-decoration: none; color:black;" href="product.php?product=<?php echo $product['product_id'];?>">
                            <div class="product-info">
                                <img src="<?php echo $product['image_url']; ?>" alt="Product">
                                <span><?php echo $product['name']; ?></span>
                            </div>
                        </a>
                    </td>
                    <td>
                            €<?php echo number_format($product['price'], 2); ?>
                    
                    </td>
                    <td><?php if($product['stock'] > 0) { echo "In stock";} else { echo "Out of stock";}?></td>
                    <td>
                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "footer/footer.php"; ?>

<?php } else {    header("Location: homepage.php"); } ?>
