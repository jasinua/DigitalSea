<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<style>
    .footer {
        display: flex;
        justify-content: center;
        padding: 40px 0;
        text-align: left;
        margin:0;
        background-color: var(--noir-color);
    }

    .footer-column {
        flex: 1;
        margin-left:5%;
    }

    .footer-column h3 {
        color: var(--text-color);
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
        background-color: var(--noir-color);
        margin: 0;
    }

    .social-icons a {
        color: var(--footer-items-color);
        font-size: 25px;
        margin: 0 25px;
        margin-bottom: 1%;
    }

    #facebook:hover {
        transition:ease-out 0.3s;
        color:#1877F2;
    }
    
    #twitter:hover {
        transition:ease-out 0.3s;
        color:#1DA1F2;
    }
    
    #instagram:hover {
        transition:ease-out 0.3s;
        color:#e1306c;
    }
    
    #linkedin:hover {
        transition:ease-out 0.3s;
        color:#0a66c2;
    }

    #youtube:hover {
        transition:ease-out 0.3s;
        color:red;
    }
</style>
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
     <i id="facebook" class="fab fa-facebook-f">
     </i>
    </a>
    <a href="#">
     <i id="twitter" class="fab fa-twitter">
     </i>
    </a>
    <a href="#">
     <i id="instagram" class="fab fa-instagram">
     </i>
    </a>
    <a href="#">
     <i id='linkedin' class="fab fa-linkedin-in">
     </i>
    </a>
    <a href="#">
     <i id='youtube' class="fab fa-youtube">
     </i>
    </a>
   </div>
  </body>
</html>