<?php
require 'config.php';


// Query to select academic years from mic table
$query = "SELECT batch FROM abatch ORDER BY batch DESC";
$result = mysqli_query($db, $query);

// Generate options for select dropdown
$options = '<option value="">Select type</option>';
while ($row = mysqli_fetch_assoc($result)) {
    $options .= '<option value="'.$row['batch'].'">'.$row['batch'].'</option>';
}

// Update the select element with options
echo $options;


?>