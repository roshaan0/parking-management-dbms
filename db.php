<?php
require __DIR__ . '/vendor/autoload.php';

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'bu_cpms');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    // Log error to a file or display a generic message in production
    // error_log("MySQL Connection Error: " . $conn->connect_error);
    die("ERROR: Could not connect. " . $conn->connect_error);
}
?>