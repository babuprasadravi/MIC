<?php
include("config.php");
include("session.php");

if (isset($_POST['reload'])) {
    $status =  mysqli_real_escape_string($db, $_POST['status']);
    $query = "SELECT * FROM booking where status='$status'";

    $result = mysqli_query($db, $query);

    $dataarray = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dataarray[] = $row;
        }
    }
    echo json_encode($dataarray);
    return;
}
if (isset($_POST['approve'])) {

    $id = mysqli_real_escape_string($db, $_POST['id']);
    $query = "SELECT * FROM booking WHERE id='$id'";

    $query_run = mysqli_query($db, $query);

    if (mysqli_num_rows($query_run) > 0) {
        $row = mysqli_fetch_assoc($query_run);
        $user = $row['user'];
        $userid = $row['userid'];
        $hall = $row['hall'];
        $time = $row['time'];
        $event = $row['event'];

        $secondQuery = "INSERT INTO notifications (user,userid,hall,event,time,reason,status,viewed) VALUES ('$user','$userid','$hall','$event','$time','your request has been approved','approved',1)";
        $secondQuery_run = mysqli_query($db, $secondQuery);

        $query = "UPDATE booking   SET status = 'approved' WHERE id='$id'";
        $query_run = mysqli_query($db, $query);

        if ($secondQuery_run && $query_run) {
            $res = [
                'status' => 200,
                'message' => 'Approved Successfully'
            ];
            echo json_encode($res);
            return;
        } else {
            $res = [
                'status' => 500,
                'message' => 'Error in Approving'
            ];
            echo json_encode($res);
            return;
        }
    } else {
        $res = [
            'status' => 500,
            'message' => 'Error in Approving'
        ];
        echo json_encode($res);
        return;
    }
}

if (isset($_POST['reject'])) {

    $id = mysqli_real_escape_string($db, $_POST['id']);
    $reason = mysqli_real_escape_string($db, $_POST['reason']);
    $query = "SELECT * FROM booking WHERE id='$id'";

    $query_run = mysqli_query($db, $query);

    if (mysqli_num_rows($query_run) > 0) {
        $row = mysqli_fetch_assoc($query_run);
        $user = $row['user'];
        $userid = $row['userid'];
        $hall = $row['hall'];
        $time = $row['time'];
        $event = $row['event'];

        $secondQuery = "INSERT INTO notifications (user,userid,hall,event,time,reason,status,viewed) VALUES ('$user','$userid','$hall','$event','$time','$reason','rejected',1)";
        $secondQuery_run = mysqli_query($db, $secondQuery);

        $query = "DELETE  FROM  booking WHERE id='$id'";
        $query_run = mysqli_query($db, $query);

        if ($secondQuery_run && $query_run) {
            $res = [
                'status' => 200,
                'message' => 'Rejected Successfully'
            ];
            echo json_encode($res);
            return;
        } else {
            $res = [
                'status' => 500,
                'message' => 'Error in Rejecting'
            ];
            echo json_encode($res);
            return;
        }
    } else {
        $res = [
            'status' => 500,
            'message' => 'Error in Rejecting'
        ];
        echo json_encode($res);
        return;
    }
}


// if (isset($_POST['filter'])) {
//     $hall = mysqli_real_escape_string($db, $_POST['hall']);
//     // $status = mysqli_real_escape_string($db, $_POST['status']);
//     $query = "SELECT * FROM booking WHERE hall = '$hall' AND status='approved'";

//     $result = mysqli_query($db, $query);

//     $dataarray = array();

//     if ($result) {
//         while ($row = mysqli_fetch_assoc($result)) {
//             $dataarray[] = $row;
//         }
//     }


//     header("Content-Type: application/json");
//     echo json_encode($dataarray);
// }

if (isset($_POST['notify'])) {
    $user = mysqli_real_escape_string($db, $_POST['userName']);
    $query = "SELECT * FROM notifications WHERE user = '$user'  AND viewed=1";
    $result = mysqli_query($db, $query);
    $dataarray = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dataarray[] = $row['reason'];
        }
    }


    $query = "UPDATE notifications SET viewed=0 WHERE user='$user' AND viewed=1";
    $query_run = mysqli_query($db, $query);


    header("Content-Type: application/json");
    echo json_encode($dataarray);
}

if (isset($_POST['getNotifications'])) {
    $user = mysqli_real_escape_string($db, $_POST['user']);
    $query = "SELECT * FROM notifications WHERE user='$user'";

    $result = mysqli_query($db, $query);

    $dataarray = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $dataarray[] = $row;
        }
    }

    $query = "UPDATE notifications SET viewed=0 WHERE user='$user' AND viewed=1";
    $result = mysqli_query($db, $query);



    header("Content-Type: application/json");
    echo json_encode($dataarray);
}
