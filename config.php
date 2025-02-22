<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'mic');
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);


$servername = "localhost"; // Your database server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "mic"; // Your database name

$erp_conn = new mysqli($servername, $username, $password, $dbname);
if ($erp_conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}


$servername = "localhost"; // Your database server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "mic"; // Your database name


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

// Razorpay Configuration
// $razorpay_key_id = 'rzp_test_391iEOtkV2VfwQ';
// $razorpay_key_secret = 'Opp1WqSlUh6imsCiGUhBATB0';

$razorpay_key_id = 'rzp_live_l4FiJPPin5b1sf';
$razorpay_key_secret = 'ykJ5qefEgvmKvX6tSasLXkHH';