<?php
    session_start();

    // --- NEW: Fetch products from database ---
    include_once "controller/function.php"; // for $conn
    include_once "model/dbh.inc.php";
    $products = [];
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    $file = 'controller/products.json';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Product Management</title>
</head>
<style>
    /* Header search bar fix */
    header input[type="text"], .header-search input[type="text"] {
        width: 300px !important;
        max-width: 90vw;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1.5px solid var(--mist-color);
        font-size: 1rem;
        background: #fff;
        color: var(--noir-color);
        margin: 0 auto;
        display: block;
        box-sizing: border-box;
    }

    header .header-search {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin: 0 auto;
    }

    .body-container {
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        padding: 32px 24px;
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
        background: var(--background-color);
        padding: 20px 18px 18px 18px;
        border-radius: 18px;
        width: 100%;
        max-width: 600px;
        margin: 60px auto 30px auto;
        position: relative;
        box-shadow: 0 12px 40px rgba(21,49,71,0.18), 0 2px 8px rgba(44,62,80,0.08);
        border: 1.5px solid #e9ecef;
        animation: modal-slide-in 0.4s cubic-bezier(.4,1.4,.6,1);
        overflow: visible;
    }

    @keyframes modal-slide-in {
        from { opacity: 0; transform: translateY(-40px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .close-btn {
        position: absolute;
        top: 12px;
        right: 16px;
        font-size: 20px;
        border: none;
        background: none;
        cursor: pointer;
        color: #888;
        transition: color 0.2s;
        z-index: 2;
    }

    .close-btn:hover {
        color: var(--text-color);
    }

    .modal-content h2, .modal-content h3 {
        text-align: center;
        color: #153147;
        margin-top: 12px;
        margin-bottom: 24px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .modal-content label {
        font-weight: 600;
        color: #2c3e50;
        margin-top: 12px;
        margin-bottom: 4px;
        display: block;
    }

    .modal-content input[type="text"],
    .modal-content input[type="number"],
    .modal-content input[type="url"],
    .modal-content textarea {
        width: 100%;
        padding: 7px 10px;
        margin-bottom: 10px;
        border-radius: 6px;
        border: 1.2px solid #d1d8e0;
        background-color: #f8f8f8;
        color: #2c3e50;
        font-size: 0.93rem;
        transition: border 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    .modal-content input:focus,
    .modal-content textarea:focus {
        border-color: #153147;
        box-shadow: 0 0 0 2px rgba(21,49,71,0.10);
        outline: none;
    }

    .modal-content .btn, .modal-content input[type="submit"] {
        color: #fff;
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        margin-top: 16px;
        margin-bottom: 0;
        transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
        box-shadow: 0 2px 8px rgba(21,49,71,0.10);
        display: block;
        width: 100%;
    }

    .modal-content .btn:hover, .modal-content input[type="submit"]:hover {
        transform: translateY(-2px);
    }

    @media (max-width: 600px) {
        .modal-content {
            padding: 10px 2vw 10px 2vw;
            max-width: 98vw;
        }
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
        padding: 12px 16px;
        margin-top: 5px;
        margin-bottom: 14px;
        border-radius: 10px;
        border: 1.5px solid var(--mist-color);
        background-color: var(--ivory-color);
        color: var(--noir-color);
        font-size: 1rem;
        transition: border 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="url"]:focus,
    textarea:focus {
        border-color: var(--button-color);
        box-shadow: 0 0 0 2px rgba(21,49,71,0.08);
        outline: none;
    }

    button, .btn, input[type="submit"] {
        background-color: var(--button-color);
        color: var(--text-color);
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        margin-top: 10px;
        transition: background 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(21,49,71,0.06);
    }

    button:hover, .btn:hover, input[type="submit"]:hover {
        background-color: var(--button-color-hover);
    }

    table {
        width: 100%;
        max-width: 1200px;
        margin: 30px auto;
        border-collapse: collapse;
        background: var(--modal-bg-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border-radius: 15px;
        overflow: hidden;
    }

    th, td {
        color: var(--page-text-color);
        padding: 8px;
        border-bottom: 1px solid var(--mist-color);
        text-align: left;
        font-size: 0.97rem;
    }

    th {
        background-color: var(--button-color);
        color: var(--text-color);
        font-size: 1.05rem;
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
        background-color:var(--button-color);
    }
    
    .detail-button:hover {
        background-color:var(--button-color-hover);
    }

    .div {
        display:flex;
        justify-content:right;
    }

    .product-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    @media (max-width: 900px) {
        .body-container {
            width: 98%;
            padding: 12px 4px;
        }
        table, th, td {
            font-size: 0.95rem;
        }
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .notification.success {
        background-color: #28a745;
    }

    .notification.error {
        background-color: #dc3545;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
<body>
    <?php include "header/header.php"; ?>

    <div class="body-container">
        <h1>Manage Your Products</h1>
        <div id="notification" class="notification" style="display: none;"></div>

        <div class="search-add">
            <input type="text" placeholder="Search..."> <button type="button" class="btn" onclick="toggleForm()">Add Product</button></div>

        <!-- Modal -->
        <div id="modal">
            <div class="modal-content">
                <div class="modal-accent"></div>
                <button class="close-btn" onclick="closeForm()">&times;</button>
                <form method="post" id="productForm">
                    <input type="hidden" name="product_id" id="product_id">
                    <h2>Add a New Product</h2>

                    <label>Name:</label>
                    <input type="text" name="name" required>

                    <label>Description:</label>
                    <textarea name="description" required></textarea>

                    <label>Type:</label>
                    <input type="text" name="type">

                    <label>Price:</label>
                    <input type="number" name="price" step="0.01" required>

                    <label>Stock:</label>
                    <input type="number" name="stock" required>

                    <label>API Source:</label>
                    <input type="text" name="api_source">

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
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>API Source</th>
                    <th>Discount</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $product): ?>
                        <tr data-index="<?= $index ?>">
                        <td>
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" class="product-img" alt="">
                            </td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['description']) ?></td>
                            <td><strong><?= htmlspecialchars(number_format($product['price'], 2)) ?>€</strong></td>
                            <td><?= htmlspecialchars($product['stock']) ?></td>
                            <td><?= htmlspecialchars($product['api_source']) ?></td>
                            <td>
                                <?php if (!empty($product['discount'])): ?>
                                    <strong><?= htmlspecialchars($product['discount']) ?>%</strong>
                                <?php else: ?>
                                    No discount
                                <?php endif; ?>
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
                            <td>
                                <button type="button" class="btn edit-btn" onclick="editProduct(<?= $index ?>)">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">No products available.</td></tr>
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

    function showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const isEdit = document.getElementById('edit-index') !== null;
        
        // Log form data for debugging
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch('controller/update_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Server response:', data); // Debug log
            if (data.success) {
                showNotification(data.message, 'success');
                closeForm();
                location.reload();
            } else {
                showNotification(data.message || 'Error updating product', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating product: ' + error.message, 'error');
        });
    });

    function editProduct(index) {
        // Get product data from PHP array (rendered as JS object)
        const products = <?php echo json_encode($products); ?>;
        const product = products[index];
        if (!product) return;

        // Open modal
        toggleForm();

        // Fill modal fields
        const form = document.getElementById('productForm');
        form.product_id.value = product.product_id;
        form.name.value = product.name;
        form.description.value = product.description;
        // form.type.value = product.type || '';
        form.price.value = product.price;
        form.stock.value = product.stock;
        form.api_source.value = product.api_source || '';
        form.discount.value = product.discount;
        
        // --- Fix for main image field ---
        if (typeof product.image_url === 'string') {
            form.main_image.value = product.image_url;
            form.image_1.value = '';
            form.image_2.value = '';
        } else if (typeof product.image_url === 'object' && product.image_url !== null) {
            form.main_image.value = product.image_url.main_image || '';
            form.image_1.value = product.image_url[1] || '';
            form.image_2.value = product.image_url[2] || '';
        } else {
            form.main_image.value = '';
            form.image_1.value = '';
            form.image_2.value = '';
        }

        // Remove old details fields
        const detailsContainer = document.getElementById('detailsContainer');
        detailsContainer.innerHTML = '';
        if (product.details) {
            Object.entries(product.details).forEach(([key, value]) => {
                const div = document.createElement('div');
                div.style.marginBottom = "10px";
                div.style.display = "flex";
                div.style.alignItems = "center";
                div.innerHTML = `
                    <input type="text" name="details_key[]" placeholder="Key" required style="flex: 1; margin-right: 10px;" value="${key}">
                    <input type="text" name="details_value[]" placeholder="Value" required style="flex: 1; margin-right: 10px;" value="${value}">
                    <button type="button" onclick="this.parentNode.remove()" style="background: none; border: none; cursor: pointer;">
                        <i class="fa-solid fa-trash" style="color:red; font-size:20px;"></i>
                    </button>
                `;
                detailsContainer.appendChild(div);
            });
        }

        // Change submit button to Update
        document.getElementById('submit-btn').value = 'Update Product';
        // Store index in a hidden field
        if (!document.getElementById('edit-index')) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'edit_index';
            hidden.id = 'edit-index';
            form.appendChild(hidden);
        }
        document.getElementById('edit-index').value = index;
    }

    // On modal close, reset form to add mode
    function closeForm() {
        document.getElementById('modal').style.display = "none";
        document.body.style.overflow = "auto";
        // Reset form
        const form = document.getElementById('productForm');
        form.reset();
        document.getElementById('detailsContainer').innerHTML = '';
        document.getElementById('submit-btn').value = 'Insert Product';
        const editIndex = document.getElementById('edit-index');
        if (editIndex) editIndex.remove();
    }
</script>
</html>
