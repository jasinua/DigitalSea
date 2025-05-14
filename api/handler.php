<?php
header("Content-Type: application/json");
require 'api.dbh.php';

// Define the protected JSON URL
$jsonUrl = "https://auth-89876-default-rtdb.firebaseio.com/.json";

// Get the Authorization header
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

// Decode the Base64 credentials
$userPass = base64_decode($credentials);
list($username, $password) = explode(":", $userPass);

// Check user in database
$stmt = $pdo->prepare("SELECT password FROM AUTHusers WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['password'] !== $password) {
    http_response_code(403);
    echo json_encode(["message" => "Invalid credentials"]);
    exit;
}

// Fetch the product ID from query parameters
$productId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Fetch the JSON data
$jsonData = file_get_contents($jsonUrl);
if ($jsonData === false) {
    http_response_code(500);
    echo json_encode(["message" => "Failed to retrieve JSON data"]);
    exit;
}

// Decode the JSON data
$data = json_decode($jsonData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(["message" => "Invalid JSON format"]);
    exit;
}

// If an ID is provided, search for the specific product
if ($productId !== null) {
    $foundProduct = null;

    // Loop through the products array
    foreach ($data['products'] as $product) {
        if (isset($product['product_id']) && $product['product_id'] === $productId) {
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

} else {
    // If no ID is provided, return all products
    echo json_encode($data['products']);
}
