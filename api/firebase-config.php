<?php
    // Firebase configuration
    $firebaseConfig = [
        'databaseURL' => 'https://auth-89876-default-rtdb.firebaseio.com',
        'apiKey' => 'AIzaSyCif5CiVmFDv-vbBmtZiml3PIIuU7_AOS8',
        'authDomain' => 'auth-89876.firebaseapp.com',
        'projectId' => 'auth-89876',
        'storageBucket' => 'auth-89876.firebasestorage.app',
        'messagingSenderId' => '955052187840',
        'appId' => '1:955052187840:web:22ad7bb7a1c7ff7f814d25',
        'measurementId' => 'G-66MY7DRXV7'
    ];

// Function to get Firebase authentication token
function getFirebaseAuthToken() {
    // You can implement your authentication logic here
    // For example, using Firebase Admin SDK or custom authentication
    return 'YOUR_AUTH_TOKEN'; // Replace with your authentication token
}

// Function to update Firebase data
function updateFirebaseData($path, $data) {
    global $firebaseConfig;
    
    $url = $firebaseConfig['databaseURL'] . $path . '.json';
    // $authToken = getFirebaseAuthToken();
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        // 'Authorization: Bearer ' . $authToken
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'success' => ($httpCode >= 200 && $httpCode < 300),
        'response' => $response,
        'httpCode' => $httpCode
    ];
}
?> 