<?php

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

// Default database parameters if .env is missing
$servername = $_ENV['DatabaseServername'] ?? 'localhost';
$username = $_ENV['DatabaseUsername'] ?? 'root';
$password = $_ENV['DatabasePassword'] ?? '';
$dbname = $_ENV['DatabaseName'] ?? 'sql7769680';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
