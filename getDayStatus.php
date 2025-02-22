<?php
require "config.php";
require "expand.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if ($data === null) {
        // JSON parsing failed
        echo "Error parsing JSON data.";
    } else {
        $today = $data["date"];
        $hallName = $data["hallName"];

        $query = "SELECT time FROM booking WHERE hall = '$hallName' AND time LIKE '%$today%'";
        $result = mysqli_query($db, $query);

        if ($result->num_rows == 0) {
            echo json_encode([
                'status' => -1,
                'message' => 'none booked'
            ]);

            return;
        }
        $row = $result->fetch_assoc();
        $time = $row["time"];

        $timeSplit = explode("|", $time)[0];


        $expandDay = expand($timeSplit);
        $count = count($expandDay);

        if ($count == 15) {
            $dataarray = [
                'status' => 1,
                'message' => 'fully booked',
                'data' => $expandDay,
                'row' => $row,
            ];
        } else if ($count > 0 && $count < 16) {
            $dataarray =
                [
                    'status' => 0,
                    'message' => 'partially booked',
                    'data' => $expandDay,
                    'row' => $row,
                ];
        } else {
            $dataarray =
                [
                    'status' => -1,
                    'message' => 'none booked',
                    'data' => $expandDay,
                    'row' => $row,
                ];
        }


        header("Content-Type: application/json");
        echo json_encode($dataarray);
    }
}
