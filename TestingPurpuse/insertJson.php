<?php

    // KJO FAQE ESHTE PER ME SHTU TE DHENA NE PRODUCTS.JSON
    // DERI SA BOJNA NAJ ZGJIDHJE TJETER



if (isset($_POST['sumbit'])) {
    $product = [
        "name" => $_POST['name'],
        "description" => $_POST['description'],
        "type" => $_POST['type'],
        "price" => (float) $_POST['price'],
        "stock" => (int) $_POST['stock'],
        "api_source" => $_POST['api_source'],
        "image_url" => [
            "main_image" => $_POST['main_image'],
            "1" => $_POST['image_1'],
            "2" => $_POST['image_2']
        ],
        "details" => []
    ];

    foreach ($_POST['details_key'] as $index => $key) {
        if (!empty($key) && !empty($_POST['details_value'][$index])) {
            $product["details"][$key] = $_POST['details_value'][$index];
        }
    }

    // Read existing JSON file
    $file = '../controller/products.json';
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        $data = json_decode($json_data, true);
    } else {
        $data = ["products" => []];
    }

    // Append new product to the products array
    $data["products"][] = $product;

    // Save back to JSON file
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    echo "Product added to JSON file successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert Product</title>
    <script>
        function addDetailField() {
            const container = document.getElementById('detailsContainer');
            const div = document.createElement('div');
            div.innerHTML = '<input type="text" name="details_key[]" placeholder="Detail Key" required> ' +
                            '<input type="text" name="details_value[]" placeholder="Detail Value" required> ' +
                            '<button type="button" onclick="this.parentNode.remove()">Remove</button><br>';
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <form method="post" action ="">
        <label>Name:</label> <input type="text" name="name" required><br>
        <label>Description:</label> <textarea name="description" required></textarea><br>
        <label>Type:</label> <input type="text" name="type" required><br>
        <label>Price:</label> <input type="number" name="price" step="0.01" required><br>
        <label>Stock:</label> <input type="number" name="stock" required><br>
        <label>API Source:</label> <input type="text" name="api_source" required><br>
        <label>Main Image URL:</label> <input type="url" name="main_image" required><br>
        <label>Image 1 URL:</label> <input type="url" name="image_1"><br>
        <label>Image 2 URL:</label> <input type="url" name="image_2"><br>
        
        <h3>Details</h3>
        <div id="detailsContainer"></div>
        <button type="button" onclick="addDetailField()">Add Detail</button>
        <br><br>
        
        <input type="submit" name = "submit" value="Insert Product">
    </form>
</body>
</html>
