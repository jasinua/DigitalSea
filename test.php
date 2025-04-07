<?php

function getJSON($item){
    $json = file_get_contents('products.json');
    $data = json_decode($json, true);
    return $data['products'][$item];
}

?>
