<?php

// Only set JSON header if this is an API call
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    header("Content-Type: application/json");
}
require 'api.dbh.php';

// Define the protected JSON URL
$jsonUrl = "https://auth-89876-default-rtdb.firebaseio.com/.json";



if(isset($_POST['add_to_wishlist'])){
    add_to_wishlist($_POST['product_id'], $_POST['user_id']);
}

if(isset($_POST['addToCart'])){
    add_to_cart($_POST['prodID'], $_POST['user_id'],$_POST['quantity']);
}


function authenticate_user() {
    global $pdo;

    // Check if this is a direct function call (no HTTP request)
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        // For direct function calls, you can either:
        // 1. Skip authentication
        // 2. Use a default user
        // 3. Require credentials to be passed as parameters
        
        // For now, we'll skip authentication for direct calls
        return true;
    }

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

    // Only authenticate for API calls
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        authenticate_user();
    }

    $jsonData = file_get_contents($jsonUrl);
    if ($jsonData === false) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            http_response_code(500);
            echo json_encode(["message" => "Failed to retrieve JSON data"]);
        }
        return [];
    }

    $data = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            http_response_code(500);
            echo json_encode(["message" => "Invalid JSON format"]);
        }
        return [];
    }

    // If this is an API call, echo the JSON response
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        echo json_encode($data['products']);
        return;
    }

    // For internal calls, return the data
    return $data['products'] ?? [];
}

function get_products_by_id($id) {
    global $jsonUrl;

    // Only authenticate for API calls
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        authenticate_user();
    }

    $jsonData = file_get_contents($jsonUrl);
    if ($jsonData === false) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            http_response_code(500);
            echo json_encode(["message" => "Failed to retrieve JSON data"]);
        }
        return null;
    }

    $data = json_decode($jsonData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            http_response_code(500);
            echo json_encode(["message" => "Invalid JSON format"]);
        }
        return null;
    }

    $foundProduct = null;

    foreach ($data['products'] as $product) {
        if (isset($product['product_id']) && $product['product_id'] == $id) {
            $foundProduct = $product;
            break;
        }
    }

    // If this is an API call, echo the JSON response
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        if ($foundProduct) {
            echo json_encode($foundProduct);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Product not found"]);
        }
        return;
    }

    // For internal calls, return the data
    return $foundProduct ?? [];
}


function add_to_cart($id, $user_id, $quantity = 1) {
    global $jsonUrl;
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

    $userId = $user_id;
    $foundProduct = null;

    foreach ($data['products'] as $product) {
        if (isset($product['product_id']) && $product['product_id'] == $id) {
            $foundProduct = $product;
            break;
        }
    }

    if (!$foundProduct) {
        http_response_code(404);
        echo json_encode(["message" => "Product not found"]);
        return;
    }

    $price = $foundProduct['price'] ?? null;

    if (!$userId || $quantity <= 0 || $price === null) {
        http_response_code(400);
        echo json_encode(["message" => "Missing or invalid parameters (user_id, quantity, price)"]);
        return;
    }

    try {
        // First, check if the product exists in the products table
        $checkProductStmt = $pdo->prepare("SELECT product_id FROM products WHERE product_id = ?");
        $checkProductStmt->execute([$id]);
        $existingProduct = $checkProductStmt->fetch(PDO::FETCH_ASSOC);

        // If product doesn't exist in products table, create a temporary entry
        if (!$existingProduct) {
            $insertProductStmt = $pdo->prepare("INSERT INTO products (product_id, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)");
            $insertProductStmt->execute([
                $id,
                $foundProduct['description'] ?? 'API Product',
                $price,
                $foundProduct['stock'] ?? 1,
                $foundProduct['image_url']['main_image'] ?? 'images/default-product.jpg'
            ]);
        }

        // Now check if the product exists in the cart
        $checkStmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $checkStmt->execute([$userId, $id]);
        $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Product exists, update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ?, price = ? WHERE user_id = ? AND product_id = ?");
            $updateStmt->execute([$newQuantity, $price, $userId, $id]);

            http_response_code(200);
            echo json_encode([
                "message" => "Cart updated",
                "product_id" => $id,
                "quantity" => $newQuantity,
                "price" => $price
            ]);

        } else {
            // Product does not exist, insert new record
            $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $insertStmt->execute([$userId, $id, $quantity, $price]);

            http_response_code(201);
            echo json_encode([
                "message" => "Product added to cart",
                "product_id" => $id,
                "quantity" => $quantity,
                "price" => $price,
                "user_id" => $userId,
            ]);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}

function add_to_wishlist($id,$user_id) {

    global $jsonUrl;
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
    $quantity = 1;
    // $price = $data['products']['price'];

    // $foundProduct = null;
    //  foreach ($data['products'] as $product) {
    //     if (isset($product['product_id']) && $product['product_id'] === $id) {
    //         $foundProduct = $product;
    //         break;
    //     }
    // }

    // $price = $foundProduct['price'];
    // if (!$userId || $quantity <= 0 || !$price) {
    //     http_response_code(400);
    //     echo json_encode(["message" => "Missing or invalid parameters (user_id, quantity, price)"]);
    //     return;
    // }

    try {
        // Check if the product already exists in the cart for the user
        $checkStmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $checkStmt->execute([$userId, $id]);
        $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Product exists, update quantity
            // $newQuantity = $existingItem['quantity'] + $quantity;
            // $updateStmt = $pdo->prepare("UPDATE wishlist SET quantity = ?, price = ? WHERE user_id = ? AND product_id = ?");
            // $updateStmt->execute([$newQuantity, $price, $userId, $id]);

            // http_response_code(200);
            echo json_encode([
                "message" => "Already in wishlist",
                "product_id" => $id,
                "userid" => $userId
            ]);

        } else {
            // Product does not exist, insert new record
            $insertStmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES ( ?, ?)");
            $insertStmt->execute([$userId, $id]);

            http_response_code(201);
            echo json_encode([
                "message" => "Product added to wishlist",
                "product_id" => $id,
                "user_id" => $userId,
            ]);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }

}

function update_api_after_purchase($id) {

}


