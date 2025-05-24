<?php
// Try to load dotenv but don't fail if it's not available
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    
    // Check if Dotenv class exists before trying to use it
    if (class_exists('Dotenv\Dotenv')) {
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
            $dotenv->load();
        } catch (Exception $e) {
            // Silently fail if .env file is missing - we'll use default values below
        }
    }
}

// // Default database parameters if .env is missing
// $servername = $_ENV['DatabaseServername'] ?? 'localhost';
// $username = $_ENV['DatabaseUsername'] ?? 'root';
// $password = $_ENV['DatabasePassword'] ?? '';
// $dbname = $_ENV['DatabaseName'] ?? 'digitalsea';

// Default database parameters if .env is missing
$servername = $_ENV['DatabaseServername'] ?? 'localhost';
$username = $_ENV['DatabaseUsername'] ?? 'root';
$password = $_ENV['DatabasePassword'] ?? '';
$dbname = $_ENV['DatabaseName'] ?? 'sql7769680';

// Create a single connection if it doesn't already exist
if (!isset($conn)) {
    try {
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        
        if (!$conn) {
            throw new Exception("Database connection failed: " . mysqli_connect_error());
        }
        
        // Set connection to use prepared statements
        mysqli_set_charset($conn, "utf8mb4");
    } catch (Exception $e) {
        // Log the error but don't display it to end users
        error_log("Database connection error: " . $e->getMessage());
    }
}
?>
