<?php
    include "../controller/function.php";
    include "../model/dbh.inc.php";

    // addProductsToDatabase($conn);
    // addDetailsToDatabase($conn);

    
    // addAPIProductsToDatabase($conn);
    // addAPIDetailsToDatabase($conn);


    // append the current page name to the URL from the $_SERVER array only the page name
    $link = (basename($_SERVER['REQUEST_URI']));
    //use regex to remove the php from the link and anything after the .php including .php?k=whatever
    $link = preg_replace('/\.php$/', '', $link);
    $link = preg_replace('/\.php\?.*$/', '', $link);

    // Print the link
    echo $link;

?>
