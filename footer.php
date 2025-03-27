<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/> -->
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
            /* height: 100%; */
            /* width: 100%; */
            /* color: ; */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
            /* align-self: flex-end; */
        }
        /* qitu per root i ndrrojm*/
        :root {
            /* Footer Colors */
            --footer-bg-color: #3a3a3a;         /* Dark gray footer background for modern feel */
            --footer-text-color: #ffffff;       /* White footer text for strong contrast */
        }
        .footer {
            display: flex;
            justify-content: center;
            padding: 40px 0;
            text-align: left;
            margin:0;
            background-color: #4a7c68;
        }
        .footer-column {
            flex: 1;
            margin-left:5%;
        }
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .footer-column a {
            color: #fff;
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
            background-color: #4a7c68;
            margin: 0;
        }
        .social-icons a {
            color: #fff;
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