<?php

$link = basename($_SERVER['REQUEST_URI']);
$link = preg_replace('/\.php$/', '', $link);
$link = preg_replace('/\.php\?.*$/', '', $link);

switch ($link) {
    case '':
        $page = 'user_manuals/User Manual.pdf#page=6';
        break;
    case 'index':
        $page = 'user_manuals/User Manual.pdf#page=6';
        break;
    case 'product':
        $page = 'user_manuals/User Manual.pdf#page=7';
        break;
    case 'profile':
        $page = 'user_manuals/User Manual.pdf#page=9';
        break;
    case 'cart':
        $page = 'user_manuals/User Manual.pdf#page=8';
        break;
    case 'payment':
        $page = 'user_manuals/User Manual.pdf#page=9';
        break;
    case 'wishlist':
        $page = 'user_manuals/User Manual.pdf#page=8';
        break;
}

echo $page;

?>