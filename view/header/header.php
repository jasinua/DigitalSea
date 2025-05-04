<?php
    ob_start(); // Fillon output buffering
    include_once "../controller/function.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn-uicons.flaticon.com/uicons-rounded-regular/css/uicons-rounded-regular.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <title>DigitalSea</title>
</head>
<style>

@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

    .montserrat-<uniquifier> {
        font-family: "Montserrat", sans-serif;
        font-optical-sizing: auto;
        font-weight: <weight>;
        font-style: normal;
    }
    
    :root {
        --background-color: #f5f5fa;       
        --text-color: white;
        --page-text-color: #232a2f;

        --modal-bg-color: white;
                        
        --button-color: #153147;
        --button-color-hover:rgb(26, 78, 118);

        --noir-color: #232a2f;
        --navy-color: #153147;
        --navy-color-lighter:rgb(69, 110, 142);
        --mist-color: #adb8bb;
        --almond-color: #edeae4;
        --ivory-color: #f9f8f7;

        --transition: all 0.5s ease;
        --transition-faster: all 0.3 ease;
        --border: 3px solid #153147;
        --shadow: 0 0 5px #153147;

        --error-color:#F94040
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family:"Montserrat";
    }

    body {
        display: flex;
        flex-direction: column;
        font-family: Arial, sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        line-height: 1.5;
        min-height: 100vh;
    }

    header {
        background-color: var(--noir-color);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 20px;
        width: 100%;
    }

    header .logo {
        margin: 10px;
        padding: 0;
        height: 35px;
        width: 35px;
    }

    nav ul {
        list-style: none;
        display: flex;
        justify-content: center;
        padding: 0;
    }

    nav ul li {
        margin: 0 23px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    nav ul li i:hover {
        transition: var(--transition);
        width: 100%;
        color:var(--mist-color);
    }

    /* ===== Auth Dropdown ===== */
    .auth-menu {
        cursor: pointer;
        font-size: 25px;
        font-weight: 100;
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
        transition: var(--transition);
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

    .icons {
        width: 20px;
        height: 20px;
        font-weight: bold;
        object-fit: contain;
        display: block;
        margin: 0 auto;
        transition: ease-out 0.2s;
    }

    .icons:hover {
        transform: scale(1.2);
        transition: ease-out 0.2s;
    }
</style>
<body>
    <header>
        <div class="imazhiYne" style="margin-left: 1%; padding: 0; margin-top: 0; margin-bottom: 0;"><a href = "index.php"><img class="logo" src="logo2.png" alt="logo e kompanise tone"></a></div>
        <nav>
            <ul>
                <li><a href="../view/index.php"><img src="home.png" class="icons" alt=""></a></li>
                <?php if(isset($_SESSION['user_id'])) { ?>
                <li><a href="../view/wishlist.php"><img src="heart.png" class="icons" alt=""></a></li>
                <li><a href="../view/cart.php"><img src="shopping-cart.png" class="icons" alt=""></a></li>
                <li><a href="../view/profile.php"><img src="user.png" class="icons" alt=""></a></li>
                <?php } ?>
                <li>
                    <label class="auth-menu" for="auth-toggle"><img src="menu.png" class="icons" alt=""></label>
                    <input type="checkbox" id="auth-toggle">
                    <div class="auth-dropdown">
                        <?php if(!isset($_SESSION['user_id'])) { ?>
                            <a href="../view/login.php">Login</a>
                            <a href="../view/signup.php">Signup</a>
                            <?php } else { ?>
                                <a href="../view/profile.php">Profile</a>
                                <a href="../view/logout.php">Log out</a>
                                <?php if(isAdmin($_SESSION['user_id'])) {
                                    echo "<a href='../view/manageStock.php'>Manage stock</a>";
                                } ?>
                        <?php } ?>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
</body>
</html>
