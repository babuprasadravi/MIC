<?php
include("config.php");
include("session.php");

if (isset($_POST['bookingRequest'])) {
    // Sanitize Input
    $hallName = mysqli_real_escape_string($db, $_POST['hallName']);
    $eventName = mysqli_real_escape_string($db, $_POST['eventName']);

    // Handling checkboxes safely
    $wired = isset($_POST['wired']) ? "wiredMic,\n" : "";
    $wireless = isset($_POST['wireless']) ? "wirelessMic,\n" : "";
    $podium = isset($_POST['podium']) ? "podiumMic.\n" : "";

    // Combine mic types
    $micType = $wired . $wireless . $podium;
    $micType = trim($micType, ",\n"); // Remove trailing comma and newline

    // Decode JSON array
    $myArray = json_decode($_POST['myArray'], true);

    // Check for existing bookings
    foreach ($myArray as $cell) {
        $cell = mysqli_real_escape_string($db, $cell);
        $query = "SELECT * FROM booking WHERE hall = '$hallName' AND time = '$cell'";
        $query_run = mysqli_query($db, $query);
        
        if (mysqli_num_rows($query_run) > 0) {
            echo json_encode(['status' => 500, 'message' => 'Time slots already booked!']);
            exit;
        }
    }

    // Insert bookings
    foreach ($myArray as $time) {
        $time = mysqli_real_escape_string($db, $time);
        $query = "INSERT INTO booking (user, userid, hall, time, event, req, status) 
                  VALUES ('$fname', '$s', '$hallName', '$time', '$eventName', '$micType', 'requested')";
        $query_run = mysqli_query($db, $query);
    }

    // Response
    if ($query_run) {
        echo json_encode(['status' => 200, 'message' => 'Hall booked Successfully']);
    } else {
        echo json_encode(['status' => 500, 'message' => 'Error in booking Hall']);
    }
}
?>
