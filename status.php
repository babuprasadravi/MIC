<?php
require 'config.php';
include("session.php");



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $status = $data['status_no'] ?? null;
        $feedback = $data['feedback'] ?? null;

        if ($id && $status) {
            $query = "UPDATE bonafide SET Status='$status'";
            if ($feedback) {
                $feedback = mysqli_real_escape_string($conn, $feedback);
                $query .= ", feedback='$feedback'";
            }
            $query .= " WHERE id='$id'";

            if (mysqli_query($conn, $query)) {
                echo json_encode(['status' => 200, 'message' => 'Details Updated Successfully']);
            } else {
                throw new Exception('Database update failed: ' . mysqli_error($conn));
            }
        } else {
            throw new Exception('Invalid request data');
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 500, 'message' => $e->getMessage()]);
    }
}
?>

