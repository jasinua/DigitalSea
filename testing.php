<?php


//QEKJO FAQE ESHTE PER ME I SHTI TE DHANAT NE CART EDHE NE WISHLIST 

// DERI SA I NDREQNUM PJESEN E HOME PAGE E SENE


session_start();
include "header.php";
include "includes/dbh.inc.php";

    $sql = "SELECT * FROM products"; // Fixed missing semicolon
    $stmt = $conn->query($sql); // For MySQLi
    $results = $stmt->fetch_all(MYSQLI_ASSOC); // Fetch all results

    if (isset($_POST['wishlist'])) {
        $prodid = $_POST['product_id'];
        $userid = $_SESSION['user_id'];
    
        $wishlistsql = "INSERT INTO wishlist (product_id, user_id) VALUES (?, ?)";
        $wishliststmt = $conn->prepare($wishlistsql);
        $wishliststmt->bind_param("ii", $prodid, $userid);
        $wishliststmt->execute();
    } 
    
    if (isset($_POST['cart'])) {
        $prodid = $_POST['product_id'];
        $userid = $_SESSION['user_id'];
    
        $cartsql = "INSERT INTO cart (product_id, user_id) VALUES (?, ?)";
        $cartstmt = $conn->prepare($cartsql);
        $cartstmt->bind_param("ii", $prodid, $userid);
        $cartstmt->execute();
    }
    
    foreach($results as $result) {
        ?>



    
    <form action="" method="post">
        <input type="hidden" name="product_id" value="<?php echo $result['product_id'];?>">
        <img src="<?php echo $result['image_url'] ?>" alt="" width="100px", height = "100px">
        <button type = "submit" name="wishlist">Add to wishlist</button>
        <button type="submit" name="cart">Add to cart</button>
        <button ><?php echo $result['product_id'];?></button>
    </form>

<?php
    

    }


?>






<?php
include "footer.php";
?>