<?php 
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
?>