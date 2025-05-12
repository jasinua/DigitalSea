<?php
session_start();
include_once "controller/function.php";
include_once "model/dbh.inc.php";

// Function to check if an image exists
function imageExists($path) {
    return file_exists($path);
}

// Get all products
$query = "SELECT product_id, name, image_url FROM products";
$result = $conn->query($query);
$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Check if images directory exists
$imagesDir = "images";
if (!file_exists($imagesDir)) {
    mkdir($imagesDir, 0777, true);
    echo "<p>Created images directory</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-ok { color: green; }
        .status-error { color: red; }
        .image-preview { max-width: 100px; max-height: 100px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Image Check</h1>
        
        <h2>Database Images</h2>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Database Image URL</th>
                <th>Status</th>
                <th>Preview</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['image_url']); ?></td>
                    <td>
                        <?php if (imageExists($product['image_url'])): ?>
                            <span class="status-ok">Available</span>
                        <?php else: ?>
                            <span class="status-error">Not found</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <img class="image-preview" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Image Preview" onerror="this.src='images/placeholder.png'; this.alt='Not available';">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>Standard Format Images</h2>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Standard Image Path</th>
                <th>Status</th>
                <th>Preview</th>
            </tr>
            <?php foreach ($products as $product): 
                $standardPath = "images/product_" . $product['product_id'] . ".png";
            ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo $standardPath; ?></td>
                    <td>
                        <?php if (imageExists($standardPath)): ?>
                            <span class="status-ok">Available</span>
                        <?php else: ?>
                            <span class="status-error">Not found</span>
                            <?php
                            // Try to copy the image if it doesn't exist
                            if (imageExists($product['image_url'])) {
                                if (copy($product['image_url'], $standardPath)) {
                                    echo " <span class='status-ok'>(Copied)</span>";
                                } else {
                                    echo " <span class='status-error'>(Copy failed)</span>";
                                }
                            }
                            ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <img class="image-preview" src="<?php echo $standardPath; ?>" alt="Image Preview" onerror="this.src='images/placeholder.png'; this.alt='Not available';">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>Image Paths from Controller Directory</h2>
        <p>These are the paths that would be accessed from controller/get_cart_preview.php</p>
        <table>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Controller-Relative Path</th>
                <th>Status</th>
            </tr>
            <?php foreach ($products as $product): 
                $controllerPath = "../images/product_" . $product['product_id'] . ".png";
                $controllerStatus = file_exists("controller/" . substr($controllerPath, 3));
            ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo $controllerPath; ?></td>
                    <td>
                        <?php if ($controllerStatus): ?>
                            <span class="status-ok">Would be accessible</span>
                        <?php else: ?>
                            <span class="status-error">Would NOT be accessible</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html> 