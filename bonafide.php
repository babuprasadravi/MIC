<?php
require 'config.php';
include("session.php");

// Retrieve the action parameter from POST
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'save_newuser':
        try {
            // Sanitize input data
         
            $Father_Name = mysqli_real_escape_string($conn, $_POST['Father_Name']);
            $Applied_Date = mysqli_real_escape_string($conn, $_POST['Applied_Date']);
            $batch = mysqli_real_escape_string($conn, $_POST['batch']);
            $Year_Level = mysqli_real_escape_string($conn, $_POST['Year_Level']);
            $Admission_Type = mysqli_real_escape_string($conn, $_POST['Admission_Type']);
            $Boarding = mysqli_real_escape_string($conn, $_POST['Boarding']);
            $First_Graduate = mysqli_real_escape_string($conn, $_POST['First_Graduate']);
            $Hostel_Type = mysqli_real_escape_string($conn, $_POST['Hostel_Type']);
            $Bus_No = mysqli_real_escape_string($conn, $_POST['Bus_No']);
            $Stop_Name = mysqli_real_escape_string($conn, $_POST['Stop_Name']);
            $Purpose_of_Certificate = mysqli_real_escape_string($conn, $_POST['Purpose_of_Certificate']);
            $education_loan = mysqli_real_escape_string($conn, $_POST['education_loan']);
            $bankname = mysqli_real_escape_string($conn, $_POST['bankname']);
            $branchname = mysqli_real_escape_string($conn, $_POST['branchname']);
            $district = mysqli_real_escape_string($conn, $_POST['district']);
            $academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
            $certificate = mysqli_real_escape_string($conn, $_POST['certificate']);
            $Others = mysqli_real_escape_string($conn, $_POST['Others']);

            if ($certificate === "Fees Structure") {
                $checkQuery = "SELECT * FROM `bonafide` WHERE Register_No = ? AND Certificate = ? AND Status = '1'";
                $stmt = mysqli_prepare($conn, $checkQuery);

                if ($stmt) {
                    $certificateType = "Fees Structure";
                    mysqli_stmt_bind_param($stmt, "ss", $sid, $certificateType);
                    mysqli_stmt_execute($stmt);
                    $checkResult = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($checkResult) > 0) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'You have already applied for this Fees Structure certificate, and your application is accepted. You cannot apply again.'
                        ]);
                        exit;
                    }
                    mysqli_stmt_close($stmt);
                }
            }


            // Handle image upload
            $upload_dir = 'image/';
            $image = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $filename = $sid. '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $upload_file = $upload_dir . basename($filename);

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                    $image = $upload_file;
                } else {
                    throw new Exception("Failed to move uploaded file: " . $filename);
                }
            }

// Handle additional file uploads (upload_file_1 and upload_file_2)
$targetDir1 = "uploads2/";
$targetDir2 = "uploads3/";

if (!is_dir($targetDir1)) mkdir($targetDir1, 0777, true);
if (!is_dir($targetDir2)) mkdir($targetDir2, 0777, true);

$allowedTypes = ['application/pdf'];
$file1Path = $file2Path = '';

// Check if upload_file_1 is provided and handle the file upload
if (!empty($_FILES['upload_file_1']['name']) && $_FILES['upload_file_1']['error'] === UPLOAD_ERR_OK) {
    if (!in_array($_FILES['upload_file_1']['type'], $allowedTypes)) {
        echo json_encode(['status' => 400, 'message' => "Only PDF files are allowed for upload_file_1."]);
        exit;
    }
    $file1Name = uniqid() . "_" . basename($_FILES['upload_file_1']["name"]);
    $file1Path = $targetDir1 . $file1Name;
    if (!move_uploaded_file($_FILES['upload_file_1']["tmp_name"], $file1Path)) {
        echo json_encode(['status' => 500, 'message' => "Failed to move uploaded file: upload_file_1."]);
        exit;
    }
}

// Check if upload_file_2 is provided and handle the file upload
if (!empty($_FILES['upload_file_2']['name']) && $_FILES['upload_file_2']['error'] === UPLOAD_ERR_OK) {
    if (!in_array($_FILES['upload_file_2']['type'], $allowedTypes)) {
        echo json_encode(['status' => 400, 'message' => "Only PDF files are allowed for upload_file_2."]);
        exit;
    }
    $file2Name = uniqid() . "_" . basename($_FILES['upload_file_2']["name"]);
    $file2Path = $targetDir2 . $file2Name;
    if (!move_uploaded_file($_FILES['upload_file_2']["tmp_name"], $file2Path)) {
        echo json_encode(['status' => 500, 'message' => "Failed to move uploaded file: upload_file_2."]);
        exit;
    }
}

            // Fetch Student Name from sbasic table using 'sid'
           


            $query = "INSERT INTO bonafide (Student_Name, Father_Name, DOB, Gender, Register_No, Department, Contact_No, Applied_Date,
            batch, Year_Level, Admission_Type, First_Graduate, Boarding, Bus_No, Stop_Name,
            Hostel_Type, Purpose_of_Certificate, education_loan, bankname, branchname, district, academic_year, certificate, Others, image,upload_file_1,upload_file_2) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new Exception("Statement preparation failed: " . $conn->error);
            }

            $stmt->bind_param(
                "sssssssssssssssssssssssssss",
                $sname,
                $Father_Name,
                $sdob,
                $sgender,
                $sid,
                $sdepartment,
                $smobile,
                $Applied_Date,
                $batch,
                $Year_Level,
                $Admission_Type,
                $First_Graduate,
                $Boarding,
                $Bus_No,
                $Stop_Name,
                $Hostel_Type,
                $Purpose_of_Certificate,
                $education_loan,
                $bankname,
                $branchname,
                $district,
                $academic_year,
                $certificate,
                $Others,
                $image,
                $file1Path,
                $file2Path
            );
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Details Added Successfully']);
            } else {
                throw new Exception("Execution failed: " . $stmt->error);
            }

            $stmt->close();
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;
    case 'save_editacc34':
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode(['status' => 400, 'message' => 'Student ID is required.']);
            exit;
        }

        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $dept = mysqli_real_escape_string($conn, $_POST['dept']);
        $year = mysqli_real_escape_string($conn, $_POST['year']);
        $m_tuition = mysqli_real_escape_string($conn, $_POST['m_tuition_fees']);
        $c_tuition = mysqli_real_escape_string($conn, $_POST['c_tuition_fees']);
        $c1g_tuition = mysqli_real_escape_string($conn, $_POST['c1g_tuition_fees']);
        $hostel_ac = mysqli_real_escape_string($conn, $_POST['hostel_ac']);
        $hostel_nonac = mysqli_real_escape_string($conn, $_POST['hostel_non_ac']);
        $bus_fee = mysqli_real_escape_string($conn, $_POST['other_fee']);
        $book_fee = mysqli_real_escape_string($conn, $_POST['book_fees']);
        $mess_fee = mysqli_real_escape_string($conn, $_POST['mess_fees']);

        // Check if the ID exists
        $id_check = $conn->prepare("SELECT id FROM fee_details WHERE id = ?");
        $id_check->bind_param("s", $id);
        $id_check->execute();
        $id_check->store_result();

        if ($id_check->num_rows == 0) {
            echo json_encode(['status' => 404, 'message' => 'Student ID not found.']);
            exit;
        }

        // Prepare SQL update query
        $query = "UPDATE fee_details SET dept=?, year=?, m_tuition_fees=?, c_tuition_fees=?, c1g_tuition_fees=?, hostel_ac=?, hostel_non_ac=?, other_fee=?, book_fees=?, mess_fees=? WHERE id=?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo json_encode(['status' => 500, 'message' => 'MySQL prepare failed: ' . $conn->error]);
            exit;
        }

        // Bind parameters
        $stmt->bind_param("sssssssssss", $dept, $year, $m_tuition, $c_tuition, $c1g_tuition, $hostel_ac, $hostel_nonac, $bus_fee, $book_fee, $mess_fee, $id);

        // Execute statement
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 200, 'message' => 'Bus Fees Updated Successfully.']);
            } else {
                echo json_encode(['status' => 400, 'message' => 'No changes made.']);
            }
        } else {
            echo json_encode(['status' => 500, 'message' => 'SQL Error: ' . $stmt->error]);
        }

        break;



    case 'edit_student':
        if (!empty($_POST['id'])) {
            $id = mysqli_real_escape_string($conn, $_POST['id']);
            $stmt = $conn->prepare("SELECT * FROM fee_details WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($data = $result->fetch_assoc()) {
                    echo json_encode(['status' => 200, 'message' => 'Details fetched successfully.', 'data' => $data]);
                } else {
                    echo json_encode(['status' => 404, 'message' => 'User not found.']);
                }
            } else {
                echo json_encode(['status' => 500, 'message' => 'Failed to fetch user details.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 400, 'message' => 'User ID is required.']);
        }
        break;

    case 'accbtnuserdelete':
        try {
            // Ensure required fields are set
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                echo json_encode(['status' => 400, 'message' => 'Student ID is required.']);
                exit;
            }

            $id = mysqli_real_escape_string($conn, $_POST['id']);
            $stmt = $conn->prepare("DELETE FROM fee_details WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 200, 'message' => 'User deleted successfully.']);
            } else {
                throw new Exception('Failed to delete user.');
            }
            $stmt->close();
        } catch (Exception $e) {
            echo json_encode(['status' => 500, 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;

        echo json_encode(['status' => 400, 'message' => 'Invalid Action']);
        break;
}
