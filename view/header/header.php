<?php
    ob_start(); // Fillon output buffering
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <title>DigitalSea</title>
</head>
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
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        display: flex;
        flex-direction: column;
        font-family: Arial, sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        line-height: 1.5;
        /* height: 60vh; */
        min-height: 100vh;
    }

    header {
        background-color: var(--noir-color);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        width: 100%;
    }

    header .logo {
        margin: 10px;
        padding: 0;
        height: 25px;
        width: 25px;
    }

    nav ul {
        list-style: none;
        display: flex;
        justify-content: center;
        padding: 0;
    }

    nav ul li {
        margin: 0 30px;
    }

    nav ul li i {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 25px;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        position: relative;
        display: inline-block;
    }
    
    nav ul li i::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 0;
        height: 2px;
        background-color: white;
        transform: translateX(-50%);
        transition: width 0.3s ease;
    }

    nav ul li a:hover::after {
        width: 100%;
    }

    /* ===== Auth Dropdown ===== */
    .auth-menu {
        cursor: pointer;
        font-size: 25px;
        font-weight: bold;
        color: white;
        display: inline-block;
    }

    #auth-toggle {
        display: none;
    }

    .auth-dropdown {
        position: absolute;
        right: 40px;
        top: 55px;
        background-color: #444;
        border-radius: 5px;
        padding: 10px;
        display: none;
        box-shadow: 0px 0px 6px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }

    #auth-toggle:checked + .auth-dropdown {
        display: block;
    }

    .auth-dropdown a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        transition: background 0.3s ease;
    }

    .auth-dropdown a:hover {
        background: #555;
    }

    .page-wrapper {
        flex: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
</style>
<body>
    <header>
        <div class="imazhiYne" style="margin-left: 1%; padding: 0; margin-top: 0; margin-bottom: 0;"><a href = "homepage.php"><img class="logo" src="logo.png" alt="logo e kompanise tone"></a></div>
        <nav>
            <ul>
                <li><a href="../view/homepage.php"><i class="fas fa-home"></i></a></li>
                <?php if(isset($_SESSION['user_id'])) { ?>
                <li><a href="../view/wishlist.php"><i class="fas fa-star"></i></a></li>
                <li><a href="../view/cart.php"><i class="fas fa-cart-plus"></a></i></li>
                <li><a href="../view/profile.php"><i class="fas fa-user"></a></i></li>
                <?php } ?>
                <li>
                    <label class="auth-menu" for="auth-toggle">☰</label>
                    <input type="checkbox" id="auth-toggle">
                    <div class="auth-dropdown">
                        <?php if(!isset($_SESSION['user_id'])) { ?>
                            <a href="../view/login.php">Login</a>
                            <a href="../view/signup.php">Signup</a>
                            <?php } else { ?>
                                <a href="../view/profile.php">Profile</a>
                                <a href="../view/logout.php">Log out</a>
                        <?php } ?>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
</body>
</html>