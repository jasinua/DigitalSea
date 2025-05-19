<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdministrator']) || $_SESSION['isAdministrator'] != 1) {
    header("Location: index.php");
    exit();
}

// Fetch products from database
include_once "controller/function.php"; // for $conn
include_once "model/dbh.inc.php";
$products = [];
$sql = "SELECT * FROM products WHERE api_source IS NULL";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fetch details for each product
        $sql11 = "SELECT * FROM product_details WHERE product_id = ?";
        $stmt11 = $conn->prepare($sql11);
        $stmt11->bind_param("i", $row['product_id']);
        $stmt11->execute();
        $result11 = $stmt11->get_result();
        
        $details = [];
        if ($result11 && $result11->num_rows > 0) {
            while ($detail = $result11->fetch_assoc()) {
                $details[] = $detail;
            }
        }
        
        $row['details'] = $details;
        $products[] = $row;
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = strtolower($search);

if (!empty($search)) {
    $sql = "SELECT * FROM products WHERE LOWER(description) LIKE '%$search%'";
    $result = $conn->query($sql);
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Product Management</title>
    <?php include "css/managestock-css.php"; ?>
</head>
<body>
    <?php include "header/header.php"; ?>

    <div class="body-container">
        <h1>Manage Your Products</h1>
        <div id="notification" class="notification" style="display: none;"></div>

        <div class="search-add">
            <input type="text" id="searchInput" placeholder="Search products..." onkeyup="searchProducts()">
            <button type="button" class="btn" onclick="toggleForm()">Add Product</button>
        </div>

        <!-- Modal -->
        <div id="modal">
            <div class="modal-content">
                <div class="modal-accent"></div>
                <button class="close-btn" onclick="closeForm()">×</button>
                <form method="post" id="productForm" onsubmit="return handleFormSubmit(event)">
                    <input type="hidden" name="product_id" id="product_id">
                    <h2 id='modal-title'>Add a New Product</h2>

                    <label>Name:</label>
                    <input type="text" name="name" required>

                    <label>Description:</label>
                    <textarea name="description"></textarea>

                    <label>Price:</label>
                    <input type="number" name="price" step="0.01" required>

                    <label>Stock:</label>
                    <input type="number" name="stock" required>

                    <label>Product Image:</label>
                    <div class="image-input-container">
                        <div class="image-input-option">
                            <input type="radio" name="image_source" value="file" id="image_file" checked>
                            <label for="image_file">Upload File</label>
                            <input type="file" name="product_image" accept="image/*" id="file_input" style="color: var(--page-text-color);">
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
                    <button type="button" onclick="addDetailField()" class="detail-button">Add Detail</button>

                    <br><br>
                    <div class="div">
                        <input type="submit" name="submit" value="Insert Product" id="submit-btn" class="btn">
                        <button type="button" id="delete-btn" class="btn delete-btn" style="display: none; background-color: #dc3545; margin-left: 10px;" onclick="deleteProduct()">Delete Product</button>
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
                    <th>Discount</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr data-index="<?= htmlspecialchars($product['product_id']) ?>">
                            <td>
                                <img src="<?php echo getImageSource($product['product_id'], $product['image_url']); ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            </td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td style="width: 20%;"><?= htmlspecialchars($product['description']) ?></td>
                            <td><strong><?= htmlspecialchars(number_format($product['price'], 2)) ?>€</strong></td>
                            <td><?= htmlspecialchars($product['stock']) ?></td>
                            <td>
                                <?php if (!empty($product['discount'])): ?>
                                    <strong><?= htmlspecialchars($product['discount']) ?>%</strong>
                                <?php else: ?>
                                    No discount
                                <?php endif; ?>
                            </td>
                            <td style="width: 25%;">
                                <?php 
                                    if (!empty($product['details'])) {
                                        $details = [];
                                        foreach ($product['details'] as $detail) {
                                            $details[] = htmlspecialchars($detail['prod_desc1']) . ": " . htmlspecialchars($detail['prod_desc2']);
                                        }
                                        echo implode("<br>", $details);
                                    } else {
                                        echo "No details";
                                    }
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn edit-btn" onclick="editProduct(<?= $product['product_id'] ?>)">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">No products available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <button id="scrollToTop" class="scroll-to-top" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
    function searchProducts() {
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput.value.toLowerCase();
        const products = <?php echo json_encode($products); ?>;

        const filteredProducts = products.filter(product => 
            product.description.toLowerCase().includes(searchValue) ||
            product.name.toLowerCase().includes(searchValue)
        );

        const tableBody = document.querySelector('tbody');
        tableBody.innerHTML = '';

        if (filteredProducts.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No products found.</td></tr>';
            return;
        }

        filteredProducts.forEach(product => {   
            const row = document.createElement('tr');
            row.setAttribute('data-index', product.product_id);

            let detailsHtml = 'No details';
            if (product.details && product.details.length > 0) {
                detailsHtml = product.details.map(detail => 
                    `${detail.prod_desc1}: ${detail.prod_desc2}`
                ).join('<br>');
            }

            let discountHtml = 'No discount';
            if (product.discount && parseFloat(product.discount) > 0) {
                discountHtml = `<strong>${product.discount}%</strong>`;
            }

            row.innerHTML = `
                <td><img src="images/product_${product.product_id}.png" alt="${product.name}" class="product-img" onerror="this.src='${product.image_url}'"></td>
                <td>${product.name}</td>
                <td style="width: 20%;">${product.description}</td>
                <td><strong>${Number(product.price).toFixed(2)}€</strong></td>
                <td>${product.stock}</td>
                <td>${discountHtml}</td>
                <td style="width: 25%;">${detailsHtml}</td>
                <td>
                    <button type="button" class="btn edit-btn" onclick="editProduct(${product.product_id})">Edit</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    function addDetailField() {
        const container = document.getElementById('detailsContainer');
        const div = document.createElement('div');
        div.style.marginBottom = "10px";
        div.style.display = "flex";
        div.style.alignItems = "center";

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
        const form = document.getElementById('productForm');
        form.reset();
        document.getElementById('detailsContainer').innerHTML = '';
        document.getElementById('submit-btn').value = 'Insert Product';
        document.getElementById('delete-btn').style.display = 'none';
        document.getElementById('modal-title').innerHTML = 'Add a New Product';
        const currentImage = document.querySelector('.image-input-container').previousElementSibling;
        if (currentImage && currentImage.tagName === 'DIV') {
            currentImage.remove();
        }
    }

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

    function editProduct(productId) {
        // Fetch the latest product data from the server
        fetch('controller/get_product.php?product_id=' + productId)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    showNotification(data.message || 'Error fetching product', 'error');
                    return;
                }

                const product = data.product;
                toggleForm();

                const form = document.getElementById('productForm');
                form.product_id.value = product.product_id;
                form.name.value = product.name;
                form.description.value = product.description;
                form.price.value = product.price;
                form.stock.value = product.stock;
                form.discount.value = product.discount || '';

                const currentImage = document.createElement('div');
                currentImage.style.marginBottom = '10px';
                const imagePath = `images/product_${product.product_id}.png`;
                currentImage.innerHTML = `
                    <label>Current Image:</label>
                    <img src="${imagePath}" 
                         alt="Current product image" 
                         style="max-width: 200px; margin-top: 5px;"
                         onerror="this.src='${product.image_url}'">
                `;
                const imageContainer = document.querySelector('.image-input-container');
                imageContainer.parentNode.insertBefore(currentImage, imageContainer);

                document.getElementById('delete-btn').style.display = 'inline-block';

                const detailsContainer = document.getElementById('detailsContainer');
                detailsContainer.innerHTML = '';
                
                if (product.details && product.details.length > 0) {
                    product.details.forEach(detail => {
                        const div = document.createElement('div');
                        div.style.marginBottom = "10px";
                        div.style.display = "flex";
                        div.style.alignItems = "center";
                        div.innerHTML = `
                            <input type="text" name="details_key[]" placeholder="Key" required style="flex: 1; margin-right: 10px;" value="${detail.prod_desc1}">
                            <input type="text" name="details_value[]" placeholder="Value" required style="flex: 1; margin-right: 10px;" value="${detail.prod_desc2}">
                            <button type="button" onclick="this.parentNode.remove()" style="background: none; border: none; cursor: pointer;">
                                <i class="fa-solid fa-trash" style="color:red; font-size:20px;"></i>
                            </button>
                        `;
                        detailsContainer.appendChild(div);
                    });
                }

                document.getElementById('submit-btn').value = 'Update Product';
                document.getElementById('modal-title').innerHTML = 'Edit Product';
            })
            .catch(error => {
                console.error('Error fetching product:', error);
                showNotification('Error fetching product data', 'error');
            });
    }

    function deleteProduct() {
        const productId = document.getElementById('product_id').value;
        if (!productId) return;

        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            fetch('controller/delete_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeForm();
                    const row = document.querySelector(`tr[data-index="${productId}"]`);
                    if (row) row.remove();
                } else {
                    showNotification(data.message || 'Error deleting product', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting product', 'error');
            });
        }
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

    function handleFormSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const isUpdate = form.product_id.value !== '';
        
        const submitBtn = document.getElementById('submit-btn');
        const originalBtnText = submitBtn.value;
        submitBtn.value = 'Processing...';
        submitBtn.disabled = true;

        fetch('controller/update_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeForm();
                
                if (isUpdate) {
                    updateTableRow(data.product);
                } else {
                    addNewTableRow(data.product);
                }
            } else {
                showNotification(data.message || 'Error processing request', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error processing request', 'error');
        })
        .finally(() => {
            submitBtn.value = originalBtnText;
            submitBtn.disabled = false;
        });

        return false;
    }

    function updateTableRow(product) {
        const row = document.querySelector(`tr[data-index="${product.product_id}"]`);
        if (!row) {
            console.error('Row not found for product:', product.product_id);
            return;
        }

        const cells = row.cells;
        
        const imagePath = `images/product_${product.product_id}.png`;
        cells[0].innerHTML = `
            <img src="${imagePath}" 
                 class="product-img" 
                 alt="${product.name}"
                 onerror="this.src='${product.image_url}'">
        `;
        
        cells[1].textContent = product.name;
        cells[2].textContent = product.description;
        cells[3].innerHTML = `<strong>${parseFloat(product.price).toFixed(2)}€</strong>`;
        cells[4].textContent = product.stock;
        cells[5].innerHTML = product.discount > 0 ? 
            `<strong>${product.discount}%</strong>` : 
            'No discount';
        
        cells[6].innerHTML = product.details && product.details.length > 0 ?
            product.details.map(detail => 
                `${detail.prod_desc1}: ${detail.prod_desc2}`
            ).join('<br>') :
            'No details';
    }

    function addNewTableRow(product) {
        const tbody = document.querySelector('tbody');
        const row = document.createElement('tr');
        row.setAttribute('data-index', product.product_id);
        
        const imagePath = `images/product_${product.product_id}.png`;
        row.innerHTML = `
            <td>
                <img src="${imagePath}" 
                     class="product-img" 
                     alt="${product.name}"
                     onerror="this.src='${product.image_url}'">
            </td>
            <td>${product.name}</td>
            <td style="width: 20%;">${product.description}</td>
            <td><strong>${parseFloat(product.price).toFixed(2)}€</strong></td>
            <td>${product.stock}</td>
            <td>${product.discount > 0 ? `<strong>${product.discount}%</strong>` : 'No discount'}</td>
            <td style="width: 25%;">${product.details && product.details.length > 0 ? 
                product.details.map(detail => 
                    `${detail.prod_desc1}: ${detail.prod_desc2}`
                ).join('<br>') : 'No details'}</td>
            <td>
                <button type="button" class="btn edit-btn" onclick="editProduct(${product.product_id})">Edit</button>
            </td>
        `;
        
        tbody.insertBefore(row, tbody.firstChild);
    }

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    window.onscroll = function() {
        const scrollBtn = document.getElementById('scrollToTop');
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    };
    </script>
</body>
</html>