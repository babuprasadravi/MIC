<?php
require 'config.php';
include("session.php");
// Get the applicant ID from the query parameter
$applicant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
//$applicant_id=1;
$imagePath = isset($applicant['image']) ? htmlspecialchars($applicant['image']) : '';

if ($applicant_id > 0) {
    // Prepare and execute the SQL statement to fetch applicant data
    $sql = "SELECT *, image FROM bonafide WHERE `id`=?";
    $sql = "SELECT * FROM bonafide WHERE `id`=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = mysqli_fetch_assoc($result);
    // $stmt->close();
} else {
    die("No applicant ID provided");
}

//$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonafide Certificate</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
            /* Light background for contrast */
            font-size: 15px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo_con {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .college-name-img {
            width: 250px;
            /* Set the width for the college name image */
            display: block;
            margin-top: 10px;
            /* Add space between images if needed */
        }

        .kr-logo {
            width: 90px;
            /* Set the width for the KR logo */
            display: block;
            margin-top: 10px;
            /* Add space between images if needed */
        }

        .college_name h3 {
            font-size: 24px;
            /* Main title size */
            margin: 0;
            color: #0056b3;
            /* Title color */
        }

        .college_name h4 {
            font-size: 18px;
            /* Sub-title size */
            margin: 5px 0;
            /* Margin around subtitle */
            color: #dc3545;
            /* Subtitle color */
        }

        .college_name h5 {
            font-size: 14px;
            /* Additional info size */
            margin: 3px 0;
            /* Margin around additional info */
            color: #333;
            /* Additional info color */
        }

        h3 {
            text-align: center;
            text-decoration: underline;
            margin: 0;
            padding-top: 30px;
            /* Move it down one step */

        }

        p {
            text-align: justify;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .signature-section {
            margin-top: 40px;
        }

        .signature-section label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }

        .signature-section span {
            font-weight: bold;
        }

        button {
            display: block;
            margin: 30px auto;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px dotted #999;
        }

        table {
            width: 50%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #6610f2;
            /* Header background color */
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            /* Border for header */
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
            /* Border for cells */
            background-color: #f9f9f9;
            /* Light background for cells */
            transition: background-color 0.3s ease;
            /* Smooth background color transition */
        }

        td:hover {
            background-color: #e9e9e9;
            /* Change color on hover for better interactivity */
        }

        .signature-section {
            margin-top: 40px;
            text-align: right;
            /* Aligns text to the right */
        }

        .signature-section label {
            display: inline-block;
            width: auto;
            /* Adjust width if needed */
            font-weight: bold;
        }

        .signature-section span {
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .photo-box {
            position: relative;
            width: 100%;
            height: 100%;
            margin-left: -3%;
        }
    </style>
</head>

<body>

    <div class="form-header">
        <div class="logo_con">

            <!-- College Name Image (Below MKCE Logo) -->
            <img src="image/mkce.jpg" alt="College Name" class="college-name-img">

            <!-- KR Logo -->
            <img src="image/kr.jpg" alt="KR Logo" class="kr-logo">
        </div>
    </div>
    </div>
    <div style="margin-left: 1px;">
        <p style="margin: 0; padding-left: 1px;">
            <strong style="font-style: italic;">Dr. B.S. MURUGAN, M.Tech., Ph.D.,</strong><br>
            <strong style="padding-left: 1px; font-style: italic;">Principal</strong>
        </p>
    </div>
    <div class="top-left-info" style="float: left; margin-top: 20px;">
        <p style="margin: 0;">
            <strong>Ref. No:</strong>
            <?php
            echo isset($applicant['academic_year'], $applicant['id'])
                ? htmlspecialchars($applicant['academic_year']) . "/MKCE/ADMIN/" . htmlspecialchars($applicant['id'])
                : 'N/A';
            ?>
        </p>
    </div>

    </div>
    <div class="top-right-info" style="float: right;">
        <p style="margin-top: -20px;">
            <strong>Date:</strong>
            <?php
            echo isset($applicant['Applied_Date'])
                ? date('d/m/Y', strtotime($applicant['Applied_Date']))  // Format Applied_Date as Day Month Year
                : date('d/m/Y');  // Defaults to current date in Day Month Year format if not available
            ?>
        </p>
    </div>

    <div style="clear: both;"></div>

    <h3>BONAFIDE CERTIFICATE</h3>
    <?php
    // Determine the prefix based on gender
    $gender_prefix = 'N/A';
    $genders_prefix = 'N/A';

    if (isset($applicant['Gender'])) {
        $gender = strtolower(trim($applicant['Gender'])); // Ensure case insensitivity
        if ($gender === 'female') {
            $gender_prefix = 'Ms.';
            $genders_prefix = 'D/o.';
        } elseif ($gender === 'male') {
            $gender_prefix = 'Mr.';
            $genders_prefix = 'S/o.';
        }
    }

    // Determine the degree prefix based on department
    $degree_prefix = 'N/A'; // Default value
    if (isset($applicant['Department'])) {
        switch (trim($applicant['Department'])) {
            case 'Civil Engineering':
            case 'Computer Science and Engineering':
            case 'Electrical and Electronics Engineering':
            case 'Electronics Engineering (VLSI Design)':
            case 'Electronics and Communication Engineering':
            case 'Freshmen Engineering':
            case 'Mechanical Engineering':
            case 'EE VLSI':
            case 'CSE(Artificial Intelligence and Machine Learning)':
                $degree_prefix = 'B.E';
                break;

            case 'Artificial Intelligence and Data Science':
            case 'Artificial Intelligence and Machine Learning':
            case 'Computer Science and Business Systems':
            case 'Information Technology':
            case 'Electronics and Communication':
            case 'Electrical Engineering':
            case 'Master of Business Administration':
            case 'Master of Computer Applications':
                $degree_prefix = 'B.Tech';
                break;
            case 'ME':
                $degree_prefix = ''; // No prefix for ME
                break;

            default:
                $degree_prefix = 'N/A'; // For any other department
                break;
        }
    }
    ?>


    <p style="text-align: justify;">
        This is to certify that <strong><?php echo $gender_prefix; ?></strong>
        <b><?php echo isset($applicant['Student_Name']) ? htmlspecialchars($applicant['Student_Name']) : 'N/A'; ?></b>
        (<b><?php echo isset($applicant['Register_No']) ? htmlspecialchars($applicant['Register_No']) : 'N/A'; ?></b>)
        <?php echo $genders_prefix; ?>
        <strong><?php echo $gender_prefix; ?></strong>
        <b><?php echo isset($applicant['Father_Name']) ? htmlspecialchars($applicant['Father_Name']) : 'N/A'; ?></b>
        is a bonafide student of our college, studying <b><?php echo isset($applicant['Year_Level']) ? htmlspecialchars($applicant['Year_Level']) : 'N/A'; ?></b>
        <b>Year</b> <strong><?php echo $degree_prefix; ?></strong>
        <b><?php echo isset($applicant['Department']) ? htmlspecialchars($applicant['Department']) : 'N/A'; ?></b> during the academic year
        <b><?php echo isset($applicant['academic_year']) ? htmlspecialchars($applicant['academic_year']) : 'N/A'; ?></b>.
        This certificate is issued for the purpose of <b>
            <?php
            if (isset($applicant['Purpose_of_Certificate'])) {
                if ($applicant['Purpose_of_Certificate'] === 'Other') {
                    echo htmlspecialchars(isset($applicant['Others']) ? $applicant['Others'] : 'N/A');
                } else {
                    echo htmlspecialchars($applicant['Purpose_of_Certificate']);
                }
            } else {
                echo 'N/A';
            }
            ?>
        </b>.
    </p>

    <br>
    <table>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 10px;"><b>DOB</b></td>
                <td style="border: 1px solid #000; padding: 10px;"><?php echo isset($applicant['DOB']) ? htmlspecialchars($applicant['DOB']) : 'N/A'; ?></td>
            </tr>

            <tr>
                <td style="border: 1px solid #000; padding: 10px; text-transform: uppercase;"><b>Boarding</b></td>
                <td style="border: 1px solid #000; padding: 10px; text-transform: uppercase;">
                    <?php
                    echo ($applicant['Boarding'] === 'Out Bus')
                        ? htmlspecialchars($applicant['Out_bus'])
                        : htmlspecialchars($applicant['Boarding']);
                    ?>
                </td>
            </tr>


            <tr>
                <td style="border: 1px solid #000; padding: 10px; text-transform: uppercase;"><b>Admission Type</b></td>
                <td style="border: 1px solid #000; padding: 10px; text-transform: uppercase;"><?php echo isset($applicant['Admission_Type']) ? htmlspecialchars($applicant['Admission_Type']) : 'N/A'; ?></td>
            </tr>
            <tr>


                <td style="border: 1px solid #000; padding: 10px; text-transform: uppercase;"><b>First Graduate</b></td>

                <td style="border: 1px solid #000; padding: 10px; text-transform: uppercase;"><?php echo isset($applicant['First_Graduate']) ? htmlspecialchars($applicant['First_Graduate']) : 'N/A'; ?></td>
            </tr>
        </tbody>
    </table>

    </div>
    <div style="float:right;width:47%;">

        <table style="margin-top:-10%;">
            <div class="photo-box" style="    margin-top: -50%;">
                <?php if (!empty($applicant['image'])): ?>
                    <img src="<?php echo htmlspecialchars($applicant['image']); ?>" alt="Applicant Photo" style="width:130px;padding-left: 40%;">
                <?php else: ?>
                    <p>Affix your Passport Size Photograph</p>
                <?php endif; ?>
            </div>
            <br>
            <p style="padding-left: 50%; margin-top: 150px;"><b>Principal</b></p>

        </table>
    </div>

    <!-- Signature Section -->
    <div class="form-section signature-section">
        <div style="float: left;">
        </div>
        <br>


    </div>
    <button onclick="printForm()" class="no-print">Print Bonafide Certificate</button>
    </div>
    <script>
        function printForm() {
            window.print();
        }
    </script>
</body>

</html>
<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 200px;">
    <div style="text-align: left;">
        <p style="font-size: 12px;">
            <strong>Generated On:</strong>
            <?php
            // Set timezone if not already set (example: UTC or your local timezone)
            date_default_timezone_set('Asia/Kolkata'); // Set the timezone as per your need

            // Display the current date and time
            echo date('d/m/Y h:i A'); // Current date and time
            ?>
        </p>
    </div>
</div>



