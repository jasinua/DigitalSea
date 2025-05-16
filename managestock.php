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
        $name = $_POST['name'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $price = (float) $_POST['price'];
        $stock = (int) $_POST['stock'];
        $api_source = $_POST['api_source'];
        $discount = (float) $_POST['discount'];
        
        // Get the next product ID
        $sql = "SELECT MAX(product_id) as max_id FROM products";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;
        
        $image_url = '';
        
        // Handle image based on source
        if ($_POST['image_source'] === 'file' && isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'images/';
            $temp_name = $_FILES['product_image']['tmp_name'];
            $new_filename = "product_{$next_id}.png";
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($temp_name, $upload_path)) {
                $image_url = $upload_path;
            } else {
                echo "<script>alert('Error uploading image');</script>";
                exit();
            }
        } elseif ($_POST['image_source'] === 'url' && !empty($_POST['image_url'])) {
            $image_url = $_POST['image_url'];
        } else {
            echo "<script>alert('Please provide either an image file or URL');</script>";
            exit();
        }
        
        // Insert product into database
        $sql = "INSERT INTO products (name, description, type, price, stock, api_source, discount, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdissd", $name, $description, $type, $price, $stock, $api_source, $discount, $image_url);
        
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            
            // Process product details
            if (isset($_POST['details_key']) && isset($_POST['details_value'])) {
                foreach ($_POST['details_key'] as $index => $key) {
                    if (!empty($key) && !empty($_POST['details_value'][$index])) {
                        $value = $_POST['details_value'][$index];
                        $detail_sql = "INSERT INTO product_details (product_id, detail_key, detail_value) VALUES (?, ?, ?)";
                        $detail_stmt = $conn->prepare($detail_sql);
                        $detail_stmt->bind_param("iss", $product_id, $key, $value);
                        $detail_stmt->execute();
                    }
                }
            }
            
            echo "<script>alert('Product added successfully!'); window.location.href=window.location.href;</script>";
            exit();
        } else {
            echo "<script>alert('Error adding product: " . $conn->error . "');</script>";
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

<?php include "css/managestock-css.php"; ?>

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

                    <label>Product Image:</label>
                    <div class="image-input-container">
                        <div class="image-input-option">
                            <input type="radio" name="image_source" value="file" id="image_file" checked>
                            <label for="image_file">Upload File</label>
                            <input type="file" name="product_image" accept="image/*" id="file_input">
                        </div>
                        <div class="image-input-option">
                            <input type="radio" name="image_source" value="url" id="image_url">
                            <label for="image_url">Image URL</label>
                            <input type="url" name="image_url" id="url_input" placeholder="https://example.com/image.jpg" disabled>
                        </div>
                    </div>

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
                                <img src="images/product_<?= htmlspecialchars($product['product_id']) ?>.png" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
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

    document.querySelectorAll('input[name="image_source"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const fileInput = document.getElementById('file_input');
            const urlInput = document.getElementById('url_input');
            
            if (this.value === 'file') {
                fileInput.disabled = false;
                urlInput.disabled = true;
                fileInput.required = true;
                urlInput.required = false;
            } else {
                fileInput.disabled = true;
                urlInput.disabled = false;
                fileInput.required = false;
                urlInput.required = true;
            }
        });
    });
</script>
</html>
