<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database credentials (replace with your own when running locally)
$servername = "localhost";            
$username = "your_username";           
$password = "your_password";         
$dbname = "your_database_name";            

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
