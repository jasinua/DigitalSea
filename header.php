<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
        <title>Header</title>
    </head>
        <style>
           /* Light Mode Colors (default) */
            :root {
                --header-color:rgb(213, 237, 230);
    
                --background-color: #f5f5fa;         /* Light background with a soft, neutral tone */
                --text-color: #3a3a3a;        /* Dark gray for easy readability */
            }
            /* Dark Mode Colors */
            body.dark-mode {
                --background-color: #2b2b2e;        /* Deep charcoal for balanced contrast */
                --modal-text-color: #f0f0f0;        /* Light gray text for clear readability */
            }

            body {
                font-family: Arial, sans-serif;
                background-color: var(--background-color);
                color: var(--modal-text-color);
                line-height: 1.5;
            }

            header {
                background-color: var(--header-color);
                color: white;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 20px;
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
                /* padding-bottom: 5px; Adds a little space below the text */
            }
            
            nav ul li i::after {
                content: "";
                position: absolute;
                left: 50%;
                bottom: 0;
                width: 0;
                height: 2px; /* Thickness of the underline */
                background-color: white; /* Color of the underline */
                transform: translateX(-50%);
                transition: width 0.3s ease; /* Controls the animation speed and timing */
            }

            nav ul li a:hover::after {
                width: 100%; /* Expands the underline from left to right */
            }

        </style>
    <body>
        <header>
            <div class="imazhiYne" style="margin-left: 1%; padding: 0; margin-top: 0; margin-bottom: 0;"><img class="logo" src="logo.png" alt="logo e kompanise tone"></div>
            <nav>
                <ul>
                    <li><a href="wishlist.php"><i class="fas fa-heart"></i></a></li>
                    <li><a href="cart.php"><i class="fas fa-cart-plus"></a></i></li>
                    <li><a href="profile.php"><i class="fas fa-user"></a></i></li>
                </ul>
            </nav>
        </header>
    </body>
</html>