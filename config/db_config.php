<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pms');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// PayMongo API configuration
define('PAYMONGO_PUBLIC_KEY', 'pk_test_zyoK8ve7TuYXny6DRV94ySSE');
define('PAYMONGO_SECRET_KEY', 'sk_test_xVXomCAUQbF689j6LPuGo4DP');
?>
