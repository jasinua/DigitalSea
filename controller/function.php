<?php 

require_once dirname(__FILE__) . '/../api/api.php';


    function isLoggedIn($check) {
        if($check) {
            return true;
        } else {
            return false;
        }
    }

    function isAdmin($user) {
        if($user == 0 || $user == "0") {
            return false;
        } else {
            return true;
        }
    }

    function returnWishlist($userid) {
        $rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));
        include_once $rootPath . "/model/dbh.inc.php";
        
        global $conn;
        if (!$conn) {
            // Instead of throwing an exception, try to re-establish connection
            $servername = $_ENV['DatabaseServername'] ?? 'localhost';
            $username = $_ENV['DatabaseUsername'] ?? 'root';
            $password = $_ENV['DatabasePassword'] ?? '';
            $dbname = $_ENV['DatabaseName'] ?? 'digitalsea';
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            
            if (!$conn) {
                // Create an empty mysqli_result
                return (object)['num_rows' => 0, 'fetch_assoc' => function() { return null; }];
            }
        }
        
        $stmt = $conn->prepare("CALL showWishList(?)");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    function returnCart($userid) {
        $rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));
        include_once $rootPath . "/model/dbh.inc.php";
        
        global $conn;
        if (!$conn) {
            // Instead of throwing an exception, try to re-establish connection
            $servername = $_ENV['DatabaseServername'] ?? 'localhost';
            $username = $_ENV['DatabaseUsername'] ?? 'root';
            $password = $_ENV['DatabasePassword'] ?? '';
            $dbname = $_ENV['DatabaseName'] ?? 'digitalsea';
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            
            if (!$conn) {
                // Create an empty mysqli_result using tmpfile() method
                return (object)['num_rows' => 0, 'fetch_assoc' => function() { return null; }];
            }
        }
        
        $stmt = $conn->prepare("CALL showCartList(?)");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    function returnProduct($productId) {
        $rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));
        include_once $rootPath . "/model/dbh.inc.php";
        
        global $conn;
        if (!$conn) {
            // Instead of throwing an exception, try to re-establish connection
            $servername = $_ENV['DatabaseServername'] ?? 'localhost';
            $username = $_ENV['DatabaseUsername'] ?? 'root';
            $password = $_ENV['DatabasePassword'] ?? '';
            $dbname = $_ENV['DatabaseName'] ?? 'digitalsea';
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            
            if (!$conn) {
                // Create an empty mysqli_result
                return (object)['num_rows' => 0, 'fetch_assoc' => function() { return null; }];
            }
        }
        
        $stmt = $conn->prepare("CALL showProduct(?)");
        $stmt->bind_param("i",$productId);
        $stmt -> execute();

        return $stmt->get_result();
    }

    function returnProductImage($productId) {
        $rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));
        include_once $rootPath . "/model/dbh.inc.php";
        
        global $conn;
        if (!$conn) {
            // Instead of throwing an exception, try to re-establish connection
            $servername = $_ENV['DatabaseServername'] ?? 'localhost';
            $username = $_ENV['DatabaseUsername'] ?? 'root';
            $password = $_ENV['DatabasePassword'] ?? '';
            $dbname = $_ENV['DatabaseName'] ?? 'digitalsea';
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            
            if (!$conn) {
                // Create an empty mysqli_result
                return (object)['num_rows' => 0, 'fetch_assoc' => function() { return null; }];
            }
        }
        
        $stmt = $conn->prepare("CALL returnImages(?)");
        $stmt->bind_param("i",$productId);
        $stmt -> execute();

        return $stmt->get_result();
    }

    function getCartCount($userid) {
        $rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));
        include_once $rootPath . "/model/dbh.inc.php";
        
        global $conn;
        if (!$conn) {
            // Instead of throwing an exception, try to re-establish connection
            $servername = $_ENV['DatabaseServername'] ?? 'localhost';
            $username = $_ENV['DatabaseUsername'] ?? 'root';
            $password = $_ENV['DatabasePassword'] ?? '';
            $dbname = $_ENV['DatabaseName'] ?? 'digitalsea';
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            
            if (!$conn) {
                // Return 0 for cart count if connection fails
                return 0;
            }
        }
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    function callJsonFIle() {
        // Firebase URL to fetch data
        $firebaseUrl = 'productsPlus.json';
                
        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return data as a string

        // Execute cURL and get JSON response
        $jsonData = curl_exec($ch);

        // Check for errors
        if ($jsonData === false) {
            die("Error fetching JSON data: " . curl_error($ch));
        }

        // Close cURL session
        curl_close($ch);

        // Decode JSON data into an associative array
        $products = json_decode($jsonData, true);

        return $products;
    }

    function addProductsToDatabase($conn) {

        $file = '../controller/product.json';
        $json_data = file_get_contents($file);
        $products = json_decode($json_data, true);
                
        // Check if 'products' key exists
        if (isset($products['products'])) {
            foreach ($products['products'] as $product) {
                // Check if the product already exists in the database
                $stmt = $conn->prepare("CALL checkProducts(?)");
                $stmt->bind_param("i", $product['product_id']);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
        
                // If product does not exist, insert it
                if ($count == 0) {
                    // Prepare and bind for insert
                    $stmt = $conn->prepare("CALL insertProducts(?,?,?,?,?,?)");
                    $stmt->bind_param("issdsi", $product['product_id'], $product['name'], $product['description'], $product['price'], $product['image_url']['main_image'], $product['stock']);
        
                    // Execute the statement
                    if (!$stmt->execute()) {
                        echo "Error inserting product ID " . $product['product_id'] . ": " . $stmt->error . "\n";
                    } else {
                        echo "Product ID " . $product['product_id'] . " inserted successfully.\n";
                    }
                    $stmt->close();
                } else {
                    echo "Product ID " . $product['product_id'] . " already exists in the database.\n";
                }
            }
            echo "Products update completed.";
        } else {
            echo "No products found in JSON data.";
        }
    }

    function addDetailsToDatabase($conn) {
        $file = '../controller/product.json';
        $json_data = file_get_contents($file);
        $products = json_decode($json_data, true);
        
        if (isset($products['products'])) {
            foreach ($products['products'] as $product) {
                // Check if the product already exists in the database
                $stmt = $conn->prepare("CALL checkProducts(?)");
                $stmt->bind_param("i", $product['product_id']);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                foreach($product['details'] as $key => $value) {
                    
                    $stmt = $conn->prepare("INSERT INTO product_details (product_id, prod_desc1,prod_desc2) VALUES (?,?,?)");
                    $stmt->bind_param("iss", $product['product_id'], $key, $value);
        
                    // Execute the statement
                    if (!$stmt->execute()) {
                        echo "Error inserting product ID ";
                    } else {
                        echo "Product ID";
                    }
                    $stmt->close();
                }
            }
            echo "Products update completed.";
        } else {
            echo "No products found in JSON data.";
        }
    }



        
    function addAPIProductsToDatabase($conn) {
        $products = get_all_products();
    
        if (!is_array($products)) {
            echo "Invalid product data format.";
            return;
        }
    
        foreach ($products as $product) {
    
            if (!isset($product['product_id'])) {
                echo "Product ID missing, skipping product.";
                continue;
            }
    
            // Check if the product already exists in the database
            $stmt = $conn->prepare("CALL checkProducts(?)");
            $stmt->bind_param("i", $product['product_id']);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
    
            if ($count == 0) {
                // Insert new product
                $stmt = $conn->prepare("
                    INSERT INTO products 
                    (product_id, name, description, price, image_url, stock, discount, api_source) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
    
                $mainImage = $product['image_url']['main_image'];
                $discount = 0;
                $source = 'DigitalSeaAPI';
    
                $stmt->bind_param(
                    "issdsiis",
                    $product['product_id'],
                    $product['name'],
                    $product['description'],
                    $product['price'],
                    $mainImage,
                    $product['stock'],
                    $discount,
                    $source
                );
    
                if (!$stmt->execute()) {
                    echo "Error inserting product ID " . $product['product_id'] . ": " . $stmt->error . "\n";
                } else {
                    echo "Product ID " . $product['product_id'] . " inserted successfully.\n";
                }
    
                $stmt->close();
            } else {
                echo "Product ID " . $product['product_id'] . " already exists in the database.\n";
            }
        }
    
        echo "Products update completed.";
    }
    
    function addAPIDetailsToDatabase($conn) {
        $products = get_all_products();

        // echo $products;
        
        if (!is_array($products)) {
            echo "Invalid product data format.";
            return;
        }
    
        foreach ($products as $product) {
    
            if (!isset($product['product_id'])) {
                echo "Product ID missing, skipping product.";
                continue;
            }
    
            // Check if the product already exists in the database
            $stmt = $conn->prepare("CALL checkProducts(?)");
            $stmt->bind_param("i", $product['product_id']);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
    
            if ($count == 0) {
                echo "Product ID " . $product['product_id'] . " not found. Skipping details insertion.\n";
                continue;
            }
    
            if (!isset($product['details']) || !is_array($product['details'])) {
                echo "No details found for Product ID " . $product['product_id'] . ". Skipping.\n";
                continue;
            }
    
            foreach ($product['details'] as $key => $value) {

                if (empty($key) || empty($value)) {
                    echo "Empty key or value for Product ID " . $product['product_id'] . ". Skipping.\n";
                    continue;
                }
            
                // Check if the detail already exists
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM product_details 
                    WHERE product_id = ? AND prod_desc1 = ?
                ");
                $stmt->bind_param("is", $product['product_id'], $key);
                $stmt->execute();
                $stmt->bind_result($exists);
                $stmt->fetch();
                $stmt->close();
            
                if ($exists > 0) {
                    echo "Detail already exists for Product ID " . $product['product_id'] . ": $key\n";
                    continue;
                }
            
                // Insert the detail
                $stmt = $conn->prepare("
                    INSERT INTO product_details (product_id, prod_desc1, prod_desc2) 
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("iss", $product['product_id'], $key, $value);
            
                if (!$stmt->execute()) {
                    echo "Error inserting detail for Product ID " . $product['product_id'] . ": " . $stmt->error . "\n";
                } else {
                    echo "Inserted detail for Product ID " . $product['product_id'] . ": $key => $value\n";
                }
            
                $stmt->close();
            }
            
        }
    
        echo "Details update completed.";
    }
    
    function getWishlistCount($user_id) {
        $rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));
        include_once $rootPath . "/model/dbh.inc.php";
        global $conn;
        if (!$conn) {
            $servername = $_ENV['DatabaseServername'] ?? 'localhost';
            $username = $_ENV['DatabaseUsername'] ?? 'root';
            $password = $_ENV['DatabasePassword'] ?? '';
            $dbname = $_ENV['DatabaseName'] ?? 'digitalsea';
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            if (!$conn) {
                return 0;
            }
        }
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM wishlist WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();
        return $cnt;
    }

?>