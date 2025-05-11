<?php
// KJO FAQE ESHTE PER ME SHTU TE DHENA NE PRODUCTS.JSON
// DERI SA BOJNA NAJ ZGJIDHJE TJETER

$file = '../controller/productsPlus.json';

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
        "details" => [],
        "discount" => (float) $_POST['discount']
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
<html>
<head>
    <title>Product Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            margin: 20px;
            padding: 0;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        form {
            background: white;
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="number"],
        input[type="url"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        button, .btn {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            margin-top: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        button:hover, .btn:hover {
            background-color: #0056b3;
        }
        table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
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
            color: #007bff;
            text-decoration: none;
        }
        .link-button:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function addDetailField() {
            const container = document.getElementById('detailsContainer');
            const div = document.createElement('div');
            div.style.marginBottom = "10px";
            div.innerHTML = '<input type="text" name="details_key[]" placeholder="Detail Key" required> ' +
                            '<input type="text" name="details_value[]" placeholder="Detail Value" required> ' +
                            '<button type="button" onclick="this.parentNode.remove()">Remove</button>';
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <h1>Manage Your Products</h1>

    <form method="post" action="">
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

        <label>Discount (Optional):</label>
        <input type="number" name="discount" step="0.01">

        <h3>Product Details</h3>
        <div id="detailsContainer"></div>
        <button type="button" onclick="addDetailField()">Add Detail</button>

        <br><br>
        <input type="submit" name="submit" value="Insert Product" class="btn">
    </form>

    <h2>Products List</h2>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Stock</th>
                <th>API Source</th>
                <th>Images</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['type']) ?></td>
                        <td>$<?= htmlspecialchars(number_format($product['price'], 2)) ?></td>
                        <td><?= htmlspecialchars($product['stock']) ?></td>
                        <td><?= htmlspecialchars($product['api_source']) ?></td>
                        <td><?= htmlspecialchars($product['discount']) ?></td>
                        <td>
                            <a class="link-button" href="<?= htmlspecialchars($product['image_url']['main_image']) ?>" target="_blank">Main</a> |
                            <a class="link-button" href="<?= htmlspecialchars($product['image_url']['1']) ?>" target="_blank">1</a> |
                            <a class="link-button" href="<?= htmlspecialchars($product['image_url']['2']) ?>" target="_blank">2</a>
                        </td>
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
                <tr><td colspan="7" style="text-align: center;">No products available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
