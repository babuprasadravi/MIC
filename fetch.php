<?php

require 'config.php';
include("session.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if ($data === null) {
        // JSON parsing failed
        echo "Error parsing JSON data.";
    } else {
        $selectedDates = $data["selectedDate"];
        $hallName = $data["hallName"];

        $dataarray = array(); // Initialize an empty array to store the rows

        foreach ($selectedDates as $date) {
            $query = "SELECT * FROM booking WHERE hall = '$hallName' AND time LIKE '%$date'";
            $result = mysqli_query($db, $query);


            while ($row = mysqli_fetch_assoc($result)) {
                $dataarray[] = $row;
            }
        }

        header("Content-Type: application/json");
        echo json_encode($dataarray);
    }
}
