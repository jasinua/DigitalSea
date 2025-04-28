<?php
// THIS PAGE IS FOR ADDING PRODUCTS INTO products.json TEMPORARILY
session_start();

$file = '../controller/products.json';

// Handle form submission
if (isset($_POST['submit'])) {
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

    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        $data = json_decode($json_data, true);
    } else {
        $data = ["products" => []];
    }

    $data["products"][] = $product;

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    echo "<script>alert('Product added successfully!'); window.location.href=window.location.href;</script>";
    exit();
}

$products = [];
if (file_exists($file)) {
    $json_data = file_get_contents($file);
    $data = json_decode($json_data, true);
    if (isset($data['products'])) {
        $products = $data['products'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Product Management</title>
</head>
<style>
    .body-container {
        width: 70%;
        margin: auto;
        display: flex;
        flex-direction: column;
    }

    h1, h2 {
        margin-top: 20px;
        text-align: center;
        color: var(--noir-color);
    }

    /* Modal styles */
    #modal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
        overflow: auto;
    }

    .modal-content {
        background: var(--modal-bg-color);
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        margin: 50px auto;
        position: relative;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 25px;
        border: none;
        background: none;
        cursor: pointer;
        color: var(--button-color);
    }

    form {
        background: var(--modal-bg-color);
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
        color: var(--noir-color);
    }

    input[type="text"],
    input[type="number"],
    input[type="url"],
    textarea {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 10px;
        border-radius: 6px;
        border: 1px solid var(--mist-color);
        background-color: var(--ivory-color);
        color: var(--noir-color);
    }

    button, .btn, input[type="submit"] {
        background-color: var(--button-color);
        color: var(--text-color);
        padding: 8px 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        align-self: center;
        transition: background-color 0.3s ease;
    }

    button:hover, .btn:hover, input[type="submit"]:hover {
        background-color: var(--button-color-hover);
    }

    table {
        width: 95%;
        margin: 30px auto;
        border-collapse: collapse;
        background: var(--modal-bg-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-radius: 15px;
        overflow: hidden;
    }

    th, td {
        color:var(--page-text-color);
        padding: 15px;
        border-bottom: 1px solid var(--mist-color);
        text-align: left;
    }

    th {
        background-color: var(--button-color);
        color: var(--text-color);
    }

    tr:hover {
        background-color: var(--almond-color);
    }

    .details-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .details-list li {
        margin-bottom: 5px;
    }

    .link-button {
        color: var(--button-color);
        text-decoration: none;
    }

    .link-button:hover {
        text-decoration: underline;
    }

    .search-add {
        display: flex;
        justify-content: space-center;
        align-items: center;
        margin: 20px auto;
        width: 95%;
    }

    .search-add input[type="text"] {
        width: 60%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid var(--mist-color);
        border-radius: 6px;
        margin: auto;
    }

    .search-add .btn {
        width: 30%;
        padding: 12px;
        margin:auto;
    }

    #detailsContainer div {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    #detailsContainer input[type="text"] {
        flex: 1;
        padding: 8px;
        margin-right: 10px;
        border-radius: 6px;
        border: 1px solid var(--mist-color);
        background-color: var(--ivory-color);
        color: var(--noir-color);
    }

    #detailsContainer button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    #detailsContainer button img {
        width: 20px;
        height: 20px;
    }

    .detail-button {
        background-color:var(--button-color-hover);
    }
    .detail-button:hover {
        background-color:var(--navy-color-lighter);
    }

    .div {
        display:flex;
        justify-content:right;
    }
</style>
<body>

<?php include "header/header.php"; ?>

<div class="body-container">
    <h1>Manage Your Products</h1>

    <div class="search-add">
        <input type="text" placeholder="Search..."> <button type="button" class="btn" onclick="toggleForm()">Add Product</button></div>

    <!-- Modal -->
    <div id="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeForm()">&times;</button>
            <form method="post" id="productForm">
                <h2>Add a New Product</h2>

                <label>Name:</label>
                <input type="text" name="name" required>

                <label>Description:</label>
                <textarea name="description" required></textarea>

                <label>Type:</label>
                <input type="text" name="type" required>

                <label>Price:</label>
                <input type="number" name="price" step="0.01" required>

                <label>Stock:</label>
                <input type="number" name="stock" required>

                <label>API Source:</label>
                <input type="text" name="api_source" required>

                <label>Main Image URL:</label>
                <input type="url" name="main_image" required>

                <label>Image 1 URL:</label>
                <input type="url" name="image_1">

                <label>Image 2 URL:</label>
                <input type="url" name="image_2">

                <h3>Product Details</h3>
                <div id="detailsContainer"></div>
                <button type="button" onclick="addDetailField()" class="detail-button" >Add Detail</button>

                <br><br>
                <div class="div"><input type="submit" name="submit" value="Insert Product" id="submit-btn" class="btn">
                </div>
                  </form>
        </div>
    </div>

    <h2>Products List</h2>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Stock</th>
                <th>API Source</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                    <td>
                            <img src="<?= htmlspecialchars($product['image_url']['main_image']) ?>" widht="100px" height='100px'alt="">
                            <!-- <a class="link-button" href="<?= htmlspecialchars($product['image_url']['main_image']) ?>" target="_blank">Main</a> |
                            <a class="link-button" href="<?= htmlspecialchars($product['image_url']['1']) ?>" target="_blank">1</a> |
                            <a class="link-button" href="<?= htmlspecialchars($product['image_url']['2']) ?>" target="_blank">2</a> -->
                        </td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['type']) ?></td>
                        <td><strong><?= htmlspecialchars(number_format($product['price'], 2)) ?>€</strong></td>
                        <td><?= htmlspecialchars($product['stock']) ?></td>
                        <td><?= htmlspecialchars($product['api_source']) ?></td>
                        
                        <td>
                            <?php if (!empty($product['details'])): ?>
                                <ul class="details-list">
                                    <?php foreach ($product['details'] as $key => $value): ?>
                                        <li><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($value) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                No details
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">No products available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
<script>
function addDetailField() {
    const container = document.getElementById('detailsContainer');
    const div = document.createElement('div');
    div.style.marginBottom = "10px";
    div.style.display = "flex";  // Align input fields horizontally
    div.style.alignItems = "center";  // Center the items vertically

    div.innerHTML = `
        <input type="text" name="details_key[]" placeholder="Key" required style="flex: 1; margin-right: 10px;">
        <input type="text" name="details_value[]" placeholder="Value" required style="flex: 1; margin-right: 10px;">
        <button type="button" onclick="this.parentNode.remove()" style="background: none; border: none; cursor: pointer;">
            <i class="fa-solid fa-trash" style="color:red; font-size:20px;"></i>
        </button>
    `;
    container.appendChild(div);
}

function toggleForm() {
    document.getElementById('modal').style.display = "block";
    document.body.style.overflow = "hidden";
}

function closeForm() {
    document.getElementById('modal').style.display = "none";
    document.body.style.overflow = "auto";
}

// Close modal if clicked outside
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        closeForm();
    }
}
</script>
</html>
