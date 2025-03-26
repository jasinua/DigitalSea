<?php
$servername = "sql7.freesqldatabase.com";
$username = "sql7769680"; 
$password = "6egMd5J3XE";      
$dbname = "sql7769680";  

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
?>
