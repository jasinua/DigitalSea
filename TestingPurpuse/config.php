<?php
// config.php

 require DIR . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(DIR.'/..');
    $dotenv->load();

define('COINBASE_API_KEY', $_ENV['COINBASE_API_KEY']);
define('COINBASE_API_URL', 'https://api.commerce.coinbase.com/');
?>