<?php

// Only set JSON header if this is an API call
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    header("Content-Type: application/json");
}
require 'api.dbh.php';

// Define the protected JSON URL
$jsonUrl = "https://auth-89876-default-rtdb.firebaseio.com/.json";



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

    $stmt = $pdo->prepare("CALL getAuthUser(?)");
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



