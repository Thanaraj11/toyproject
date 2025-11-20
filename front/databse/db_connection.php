<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "toys";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    
}else{
    //  echo "connection";
}
?>