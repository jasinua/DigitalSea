<?php
if (!class_exists('Database')) {
    class Database {
        private static $instance = null;
        private $conn;
        
        private function __construct() {
            $servername = "sql7.freesqldatabase.com";
            $username = "sql7769680"; 
            $password = "6egMd5J3XE";      
            $dbname = "sql7769680";  

            $this->conn = mysqli_connect($servername, $username, $password, $dbname);

            if (!$this->conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            
            // Set connection to use prepared statements
            mysqli_set_charset($this->conn, "utf8mb4");
        }

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function getConnection() {
            return $this->conn;
        }
    }
}

// Create a single connection instance only if it hasn't been created
if (!isset($conn)) {
    $db = Database::getInstance();
    $conn = $db->getConnection();
}
?>
