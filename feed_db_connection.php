<?php
$servername = "localhost";  // Replace with your database server name
$username = "root";         // Replace with your database username
$password = "";             // Replace with your database password
$database = "micc"; // Replace with your database name

// Create connection
$fconn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($fconn->connect_error) {
    die("Connection failed: " . $fconn->connect_error);
}
?>
