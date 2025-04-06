<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    </head>
    <style>
         /* Basic Reset */
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
            /* align-self: flex-end; */
        }

        /* qitu per root i ndrrojm*/
        :root {
            /* Footer Colors */
            --background-color-test:rgb(107, 174, 156);
            --footer-bg-color: #4a7c68;
            --footer-text-color: #3a3a3a;
            --footer-items-color: #fff;

            --success-color: #4CAF50;
            --error-color: #f44336;
        }
        
        .footer {
            display: flex;
            justify-content: center;
            padding: 40px 0;
            text-align: left;
            margin:0;
            background-color: var(--footer-bg-color);
        }
        .footer-column {
            flex: 1;
            margin-left:5%;
        }
        .footer-column h3 {
            color: var(--footer-text-color);
            font-size: 18px;
            margin-bottom: 10px;
        }
        .footer-column a {
            color: var(--footer-items-color);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
        }

        .footer-column a:hover {
            text-decoration: underline;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            background-color: var(--footer-bg-color);
            margin: 0;
        }
        .social-icons a {
            color: var(--footer-items-color);
            font-size: 20px;
            margin: 0 20px;
            margin-bottom: 1%;
        }
   </style>
  </head>
  <body>
   <div class="footer">
    <div class="footer-column">
     <h3>
      Products
     </h3>
     <a href="#">
      House Shit
     </a>
     <a href="#">
      Dekstop
     </a>
     <a href="#">
      Phones
     </a>
     <a href="#">
      Laptop
     </a>
     <a href="#">
      Ora
     </a>
     <a href="#">
      TV
     </a>
    </div>
    <div class="footer-column">
     <h3>
      Resources
     </h3>
     <a href="#">
      Contact Us
     </a>
     <a href="#">
      Blog
     </a>
     <a href="#">
      FAQ
     </a>
    </div>
    <div class="footer-column">
     <h3>
      Work with DigitalSea
     </h3>
     <a href="#">
      Partners
     </a>
     <a href="#">
      Dealers
     </a>
     <a href="#">
      OEM
     </a>
    </div>
    <div class="footer-column">
     <h3>
      About
     </h3>
     <a href="#">
      DigitalSea, Inc.
     </a>
     <a href="#">
      Developers
     </a>
     <a href="#">
      Investors
     </a>
     <a href="#">
      Careers
     </a>
     <a href="#">
      Press
     </a>
     <a href="#">
      Team
     </a>
    </div>
   </div>
   <div class="social-icons">
    <a href="#">
     <i class="fab fa-facebook-f">
     </i>
    </a>
    <a href="#">
     <i class="fab fa-twitter">
     </i>
    </a>
    <a href="#">
     <i class="fab fa-instagram">
     </i>
    </a>
    <a href="#">
     <i class="fab fa-linkedin-in">
     </i>
    </a>
    <a href="#">
     <i class="fab fa-youtube">
     </i>
    </a>
   </div>
  </body>
</html>