<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
        <title>Header</title>
    </head>
        <style>
            :root {
                --background-color: #f5f5fa;       
                --text-color: white;
                --page-text-color: #232a2f;
                                
                --button-color: #153147;
                --button-color-hover:rgb(26, 78, 118);

                --noir-color: #232a2f;
                --navy-color: #153147;
                --mist-color: #adb8bb;
                --almond-color: #edeae4;
                --ivory-color: #f9f8f7;


            }

            body {
                font-family: Arial, sans-serif;
                background-color: white;
                color: var(--text-color);
                line-height: 1.5;
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
            <div class="imazhiYne" style="margin-left: 1%; padding: 0; margin-top: 0; margin-bottom: 0;"><a href = "homepage.php"><img class="logo" src="logo.png" alt="logo e kompanise tone"></a></div>
            <nav>
                <ul>
                    <li><a href="wishlist.php"><i class="fas fa-star"></i></a></li>
                    <li><a href="cart.php"><i class="fas fa-cart-plus"></a></i></li>
                    <li><a href="profile.php"><i class="fas fa-user"></a></i></li>
                </ul>
            </nav>
        </header>
    </body>
</html>