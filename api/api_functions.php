
    <?php


    header("Content-Type: application/json");
    require 'api.dbh.php';

    // Define the protected JSON URL
    $jsonUrl = "https://auth-89876-default-rtdb.firebaseio.com/.json";


    function authenticate_user() {
        global $pdo;

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Authorization header missing"]);
            exit;
        }

        $authHeader = $headers['Authorization'];
        list($type, $credentials) = explode(" ", $authHeader);

        if ($type !== "Basic") {
            http_response_code(401);
            echo json_encode(["message" => "Unsupported authorization type"]);
            exit;
        }

        $userPass = base64_decode($credentials);
        list($username, $password) = explode(":", $userPass);

        $stmt = $pdo->prepare("SELECT password FROM AUTHusers WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $user['password'] !== $password) {
            http_response_code(403);
            echo json_encode(["message" => "Invalid credentials"]);
            exit;
        }
    }

    function get_all_products() {
        global $jsonUrl;

        authenticate_user();

        $jsonData = file_get_contents($jsonUrl);
        if ($jsonData === false) {
            http_response_code(500);
            echo json_encode(["message" => "Failed to retrieve JSON data"]);
            return;
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(500);
            echo json_encode(["message" => "Invalid JSON format"]);
            return;
        }

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        switch ($requestMethod) {
            case 'GET':
                echo json_encode($data['products']);
                break;

            case 'POST':
                http_response_code(405);
                echo json_encode(["message" => "POST method not allowed for get_all_products"]);
                break;

            default:
                http_response_code(405);
                echo json_encode(["message" => "Method not allowed"]);
                break;
        }
    }

    function get_products_by_id($id) {
        global $jsonUrl;

        authenticate_user();

        $jsonData = file_get_contents($jsonUrl);
        if ($jsonData === false) {
            http_response_code(500);
            echo json_encode(["message" => "Failed to retrieve JSON data"]);
            return;
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(500);
            echo json_encode(["message" => "Invalid JSON format"]);
            return;
        }

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        switch ($requestMethod) {
            case 'GET':
                $foundProduct = null;
                foreach ($data['products'] as $product) {
                    if (isset($product['product_id']) && $product['product_id'] === $id) {
                        $foundProduct = $product;
                        break;
                    }
                }

                if ($foundProduct) {
                    echo json_encode($foundProduct);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Product not found"]);
                }
                break;

            case 'DELETE':
                http_response_code(403);
                echo json_encode(["message" => "DELETE method not allowed for get_products_by_id"]);
                break;

            default:
                http_response_code(405);
                echo json_encode(["message" => "Method not allowed"]);
                break;
        }
    }


    function add_to_cart($id,$user_id) {
        global $pdo;
    
        authenticate_user();
    
        $requestMethod = $_SERVER['REQUEST_METHOD'];
    
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            return;
        }
    
        $jsonData = file_get_contents($jsonUrl);
        if ($jsonData === false) {
            http_response_code(500);
            echo json_encode(["message" => "Failed to retrieve JSON data"]);
            return;
        }

        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(500);
            echo json_encode(["message" => "Invalid JSON format"]);
            return;
        }

        $requestMethod = $_SERVER['REQUEST_METHOD'];
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON format"]);
            return;
        }

        $userId = $user_id;
        // $quantity = 1;
        // $price = $data['products']['price'];

        $foundProduct = null;
         foreach ($data['products'] as $product) {
            if (isset($product['product_id']) && $product['product_id'] === $id) {
                $foundProduct = $product;
                break;
            }
        }


        if (!$userId || $quantity <= 0 || !$price) {
            http_response_code(400);
            echo json_encode(["message" => "Missing or invalid parameters (user_id, quantity, price)"]);
            return;
        }
    
        try {
            // Check if the product already exists in the cart for the user
            $checkStmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $checkStmt->execute([$userId, $id]);
            $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
            if ($existingItem) {
                // Product exists, update quantity
                $newQuantity = $existingItem['quantity'] + $quantity;
                $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ?, price = ? WHERE user_id = ? AND product_id = ?");
                $updateStmt->execute([$newQuantity, $price, $userId, $id]);
    
                http_response_code(200);
                // echo json_encode([
                //     "message" => "Cart updated",
                //     "product_id" => $id,
                //     "quantity" => $newQuantity,
                //     "price" => $price
                // ]);
    
            } else {
                // Product does not exist, insert new record
                $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $insertStmt->execute([$userId, $id, $quantity, $price]);
    
                http_response_code(201);
                echo json_encode([
                    "message" => "Product added to cart",
                    "product_id" => $id,
                    "quantity" => $quantity,
                    "price" => $price
                ]);
            }
    
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    }
    

    function add_to_wishlist($id) {

    }

    function update_api_after_purchase($id) {

    }


