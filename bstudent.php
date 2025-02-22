<?php
require 'config.php';
include("session.php");

?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIC</title>
    <link rel="icon" type="image/png" sizes="32x32" href="image/icons/mkce_s.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-5/bootstrap-5.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 60px;
            --footer-height: 60px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --dark-bg: #1a1c23;
            --light-bg: #f8f9fc;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* General Styles with Enhanced Typography */

        /* Content Area Styles */
        .content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* Content Navigation */
        .content-nav {
            background: linear-gradient(45deg, #4e73df, #1cc88a);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .content-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 20px;
            overflow-x: auto;
        }

        .content-nav li a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .content-nav li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar.collapsed+.content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .breadcrumb-area {
            background: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin: 20px;
            padding: 15px 20px;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: #224abe;
        }

        /* Table Styles */
        .gradient-header {
            --bs-table-bg: transparent;
            --bs-table-color: white;
            background: linear-gradient(135deg, #4CAF50, #2196F3) !important;

            text-align: center;
            font-size: 0.9em;
        }

        td {
            text-align: left;
            font-size: 0.9em;
            vertical-align: middle;
            /* For vertical alignment */
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }

            .sidebar.mobile-show {
                transform: translateX(0);
            }

            .topbar {
                left: 0 !important;
            }

            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .mobile-overlay.show {
                display: block;
            }

            .content {
                margin-left: 0 !important;
            }

            .brand-logo {
                display: block;
            }

            .user-profile {
                margin-left: 0;
            }

            .sidebar .logo {
                justify-content: center;
            }

            .sidebar .menu-item span,
            .sidebar .has-submenu::after {
                display: block !important;
            }

            body.sidebar-open {
                overflow: hidden;
            }

            .footer {
                left: 0 !important;
            }

            .content-nav ul {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 5px;
            }

            .content-nav ul::-webkit-scrollbar {
                height: 4px;
            }

            .content-nav ul::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 2px;
            }
        }

        .container-fluid {
            padding: 20px;
        }


        /* loader */
        .loader-container {
            position: fixed;
            left: var(--sidebar-width);
            right: 0;
            top: var(--topbar-height);
            bottom: var(--footer-height);
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            /* Changed from 'none' to show by default */
            justify-content: center;
            align-items: center;
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .sidebar.collapsed+.content .loader-container {
            left: var(--sidebar-collapsed-width);
        }

        @media (max-width: 768px) {
            .loader-container {
                left: 0;
            }
        }

        /* Hide loader when done */
        .loader-container.hide {
            display: none;
        }

        /* Loader Animation */
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid var(--primary-color);
            border-right: 5px solid var(--success-color);
            border-bottom: 5px solid var(--primary-color);
            border-left: 5px solid var(--success-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Hide content initially */
        .content-wrapper {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Show content when loaded */
        .content-wrapper.show {
            opacity: 1;
        }

        .custom-gradient {
            background: linear-gradient(to bottom, rgb(255, 255, 255), #00f2fe);
            /* Vertical gradient */
            padding: 10px 15px;
            /* Adjust padding as needed */
            border-radius: 5px;
            /* Optional: Rounded corners */
        }

        .custom-table {
            border-radius: 10px;
        }

        .btn-sm {
            padding: 2px 8px;
            font-size: 12px;
            line-height: 1;
            height: 40px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">

        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
        </div>

        <!-- Topbar -->
        <?php include 'topbar.php'; ?>

        <!-- Breadcrumb -->
        <div class="breadcrumb-area custom-gradient">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">

                    <li class="breadcrumb-item active" aria-current="page">Bonafide Application </li>
                </ol>
            </nav>
        </div>
        <div class="container-fluid">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs ms-2" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="family-tab" id="apply_bonafide_tab" data-bs-toggle="tab" href="#apply_bonafide1" role="tab" aria-controls="apply_bonafide" aria-selected="true">
                        <i class="fa fa-file-alt tab-icon"></i> Apply Bonafide
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="parents-tab" id="accepted_bonafide_tab" data-bs-toggle="tab" href="#accepted_bonafide2" role="tab" aria-controls="accepted_bonafide" aria-selected="false">
                        <i class="fa fa-check-circle tab-icon"></i> Accepted Bonafide
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="swot-tab" id="rejected_bonafide_tab" data-bs-toggle="tab" href="#rejected_bonafide3" role="tab" aria-controls="rejected_bonafide" aria-selected="false">
                        <i class="fa fa-times-circle tab-icon"></i> Rejected Bonafide
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="medical-tab" id="waiting_for_approval_tab" data-bs-toggle="tab" href="#waiting_for_approval4" role="tab" aria-controls="waiting_for_approval" aria-selected="false">
                        <i class="fa fa-hourglass-half tab-icon"></i> Waiting for Approval
                    </a>
                </li>
            </ul>


            <!-- Apply Bonafide Tab -->

            <div class="tab-content">
                <div class="tab-pane p-3 fade show active" id="apply_bonafide1" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card" style="border: none;">
                                <div class="card-header" style="border: none;">
                                    <button type="button" style="float: right;" class="btn btn-secondary ms-auto" data-bs-toggle="modal" data-bs-target="#add_user" id="style-yQg7i">Add Student Details</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="user" class="table table-striped table-bordered">
                                            <thead class="gradient-header">
                                                <tr>
                                                    <th><b>S.No</b></th>
                                                    <th><b>Student Name</b></th>
                                                    <th><b>Reg no</b></th>
                                                    <th><b>Department</b></th>
                                                    <th><b>Apply for Certificate</b></th>
                                                    <th><b>Details</b></th>
                                                    <th><b>Bonafide Proof</b></th>
                                                    <th><b>Fees Structure Proof</b></th>


                                                    <!-- New column for file view -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $s_no = 1;
                                                $query = "SELECT * FROM bonafide WHERE Register_No='$s'";

                                                $result = mysqli_query($db, $query);
                                                while ($row = mysqli_fetch_array($result)) {
                                                ?>

                                                    <tr>
                                                        <td><?php echo $s_no; ?></td>
                                                        <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Register_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Department']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['certificate']); ?></td>

                                                        <!-- View Details -->

                                                        <td>
                                                            <button class="btn btn-primary small-btn viewDetails" data-toggle="modal" data-target="#viewDetailsModal"
                                                                data-student='<?= json_encode($row); ?>'>View Details</button>

                                                        </td>


                                                        <!-- Upload File -->

                                                        <td style="padding-left: 40px;">
                                                            <button type="button" class="btn btn-info btn-sm view_student2"
                                                                data-file-path1="<?= htmlspecialchars($row['upload_file_1'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                View File
                                                            </button>
                                                        </td>
                                                        <td style="padding-left: 40px;">
                                                            <button type="button" class="btn btn-info btn-sm view_data1"
                                                                data-file-path2="<?= htmlspecialchars($row['upload_file_2'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                View File
                                                            </button>
                                                        </td>

                                                    </tr>
                                                <?php
                                                    $s_no++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Modal Structure -->

                <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTitle">Bonafide Application Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">

                                    <tr>
                                        <th>Father's Name</th>
                                        <td id="modalFatherName"></td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td id="modalDOB"></td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td id="modalGender"></td>
                                    </tr>
                                    <tr>
                                        <th>Applied Date</th>
                                        <td id="modalAppliedDate"></td>
                                    </tr>
                                    <tr>
                                        <th>Contact No</th>
                                        <td id="modalContactNo"></td>
                                    </tr>
                                    <tr>
                                        <th>Batch</th>
                                        <td id="modalBatch"></td>
                                    </tr>
                                    <tr>
                                        <th>Year Level</th>
                                        <td id="modalYearLevel"></td>
                                    </tr>
                                    <tr>
                                        <th>Academic Year</th>
                                        <td id="modalAcademicYear"></td>
                                    </tr>
                                    <tr>
                                        <th>Admission Type</th>
                                        <td id="modalAdmissionType"></td>
                                    </tr>
                                    <tr>
                                        <th>First Graduate</th>
                                        <td id="modalFirstGraduate"></td>
                                    </tr>
                                    <tr>
                                        <th>Boarding Info</th>
                                        <td id="modalBoardingInfo"></td>
                                    </tr>
                                    <tr>
                                        <th>Purpose</th>
                                        <td id="modalPurpose"></td>
                                    </tr>
                                    <tr>
                                        <th>Others</th>
                                        <td id="modalOthers"></td>
                                    </tr>
                                    <tr>
                                        <th>Education Loan</th>
                                        <td id="modalLoanInfo"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Modal for Viewing File 1 -->
                <div class="modal fade" id="viewpatentModal89" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Uploaded Document</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center" id="viewpatentModalBody34">
                                <!-- PDF will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for Viewing File 2 -->
                <div class="modal fade" id="viewpatent67" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewpatentModalLabel67">Uploaded Document</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center" id="viewpatentModalBody78">
                                <!-- PDF will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accepted Bonafide Tab -->

                <div class="tab-pane p-20" id="accepted_bonafide2" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card" style="border: none;">

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="gradient-header">
                                                <tr>
                                                    <th><b>S.No</b></th>
                                                    <th><b>Name</b></th>
                                                    <th><b>Register Number</b></th>
                                                    <th><b>Bonafide Type</b></th>
                                                    <th><b>Certificate</b></th>
                                                    <th><b>Reference No</b></th>
                                                    <th><b>Applied Date</b></th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                $sql2 = "SELECT * FROM `bonafide` WHERE Register_No='$s' AND Status = '1'";
                                                $result2 = mysqli_query($conn, $sql2);
                                                $s_no = 1;
                                                while ($row = mysqli_fetch_array($result2)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $s_no; ?></td>
                                                        <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Register_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Purpose_of_Certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['academic_year'] . "/MKCE/ADMIN/" . $row['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Applied_Date']); ?></td>

                                                    </tr>
                                                <?php
                                                    $s_no++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejected Bonafide Tab -->

                <div class="tab-pane p-3 fade" id="rejected_bonafide3" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card" style="border: none;">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="gradient-header">
                                                <tr>
                                                    <th><b>S.No</b></th>
                                                    <th><b>Name</b></th>
                                                    <th><b>Register Number</b></th>
                                                    <th><b>Bonafide Type</b></th>
                                                    <th><b>Certificate</b></th>
                                                    <th><b>Applied Date</b></th>
                                                    <th><b>Reference No</b></th>

                                                    <th><b>Feedback</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                $sql3 = "SELECT * FROM `bonafide` WHERE  Status = '7' AND Register_No='$s'";
                                                $result3 = mysqli_query($conn, $sql3);
                                                $s_no = 1;

                                                while ($row = mysqli_fetch_array($result3)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $s_no; ?></td>
                                                        <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Register_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Purpose_of_Certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Applied_Date']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['academic_year'] . "/MKCE/ADMIN/" . $row['id']); ?></td>

                                                        <td>
                                                            <button class="btn btn-danger viewFeedback" data-feedback="<?php echo htmlspecialchars($row['feedback']); ?>">
                                                                View Feedback
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $s_no++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Structure -->
                <div class="modal fade" id="dynamicModal4" tabindex="-1" aria-labelledby="dynamicModalLabel4" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="dynamicModalLabel4">View Feedback</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="dynamicModalBody4">
                                <!-- Dynamic content will be injected here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waiting for Approval Tab -->


                <div class="tab-pane p-3 fade" id="waiting_for_approval4" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card" style="border: none;">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="gradient-header">
                                                <tr>
                                                    <th><b>S.No</b></th>
                                                    <th><b>Name</b></th>
                                                    <th><b>Register Number</b></th>
                                                    <th><b>Bonafide Type</b></th>
                                                    <th><b>Applied Date</b></th>
                                                    <th><b>Reference No</b></th>
                                                    <th><b>Approval Pending</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                $sql2 = "SELECT * FROM `bonafide` WHERE Register_No='$s' AND Status = '0'";
                                                $result2 = mysqli_query($conn, $sql2);
                                                $s_no = 1;

                                                while ($row = mysqli_fetch_array($result2)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $s_no; ?></td>
                                                        <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Register_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Purpose_of_Certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Applied_Date']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['academic_year'] . "/MKCE/ADMIN/" . $row['id']); ?></td>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            <i class="fas fa-clock" style="font-size: 20px; color: #077fff;" aria-label="Waiting for Approval"></i>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $s_no++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!--Student Details Form -->

                <div class="modal fade" id="add_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Bonafide Application</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="save_user">
                                <div class="modal-body">
                                    <div id="errorMessage" class="alert alert-warning d-none"></div>

                                    <div class="row">
                                        <!-- Father Name -->
                                        <div class="form-group col-md-6">
                                            <label for="fatherName" class="form-label">Father Name*</label>
                                            <input type="text" name="Father_Name" class="form-control" placeholder="Name" required oninput="this.value = this.value.toUpperCase();">
                                        </div>
                                        <!-- Upload an Image -->
                                        <div class="form-group col-md-6">
                                            <label for="validatedCustomFile" class="form-label">Upload an Image *</label>
                                            <input type="file" class="form-control" name="image" id="validatedCustomFile" required>
                                        </div>
                                    </div>


                                    <div class="row ">
                                        <!-- Applied Date -->
                                        <div class="form-group col-md-6">
                                            <label for="appliedDate" class="form-label">Applied Date*</label>
                                            <input type="date" id="appliedDate" name="Applied_Date" class="form-control" required>
                                        </div>
                                        <!-- Batch -->
                                        <div class="form-group col-md-6">
                                            <label for="batch" class="form-label">Batch*</label>
                                            <select class="form-select" name="batch" id="batch" required>
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>


                                    <!-- Year of Study (Left Side) -->
                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <label for="yearOfStudy" class="form-label">Year Level*</label>
                                            <select class="form-select" name="Year_Level" id="yearOfStudy" required>
                                                <option value="">Select Year</option>
                                                <option value="I">I</option>
                                                <option value="II">II</option>
                                                <option value="III">III</option>
                                                <option value="IV">IV</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="admissionCategory" class="form-label">Admission Type*</label>
                                            <select class="form-select" name="Admission_Type" id="admissionCategory" required onchange="handleAdmissionTypeChange()">
                                                <option value="">Select Category</option>
                                                <option value="Management">MANAGEMENT</option>
                                                <option value="Counselling">COUNSELLING</option>
                                                <option value="Counselling (7.5% Special)">COUNSELING (7.5% Special)</option>

                                            </select>
                                        </div>
                                    </div>



                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <label for="firstGraduate" class="form-label">First Graduate*</label>
                                            <select class="form-select" name="First_Graduate" id="firstGraduate" required>
                                                <option value="">Select Option</option>
                                                <option value="Yes">YES</option>
                                                <option value="No">NO</option>
                                            </select>
                                            <!-- Hidden field for disabled input -->
                                            <input type="hidden" name="First_Graduate" id="hiddenFirstGraduate" value="">
                                        </div>

                                        <!-- Academic Year -->
                                        <div class="form-group col-md-6">
                                            <label for="academicYear" class="form-label">Academic Year*</label>
                                            <select class="form-select" name="academic_year" id="academicYear">
                                                <?php
                                                // Set the default academic year to 2024-2025
                                                $defaultYear = '2024-2025';

                                                // Generate only the desired academic year
                                                $academicYears = [
                                                    '2024-2025'
                                                ];

                                                // Loop through the years and create options
                                                foreach ($academicYears as $year) {
                                                    $selected = ($year === $defaultYear) ? 'selected' : ''; // Select the default year
                                                    echo "<option value=\"$year\" $selected>$year</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <!-- Left Side -->
                                        <div class="col-md-6">
                                            <!-- Boarding Category -->
                                            <div class="form-group">
                                                <label for="boardingCategory">Boarding*</label>
                                                <select class="form-control" name="Boarding" id="boardingCategory" onchange="handleBoardingChange()">
                                                    <option value="">Select Boarding</option>
                                                    <option value="Hostel">Hostel</option>
                                                    <option value="Day Scholar">Day Scholar</option>
                                                    <option value="Bus Commer">Bus Commer</option>
                                                </select>
                                            </div>

                                            <!-- Hostel Options -->
                                            <div id="hostelOptions" class="mt-3" style="display: none;">
                                                <label for="hostelType" class="form-label">Hostel Type</label>
                                                <select class="form-select" name="Hostel_Type" id="hostelType">
                                                    <option value="">Select Type</option>
                                                    <option value="A/C">A/C</option>
                                                    <option value="Non A/C">Non A/C</option>
                                                </select>
                                            </div>

                                            <!-- Out Bus Options -->
                                            <div id="outBusOptions" class="mt-3" style="display: none;">
                                                <div class="form-group">
                                                    <label for="outBusNo" class="form-label">Bus Number</label>
                                                    <input type="text" class="form-control" name="Bus_No" id="outBusNo" placeholder="Enter Bus Number">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label for="stopName" class="form-label">Stop Name</label>
                                                    <input type="text" class="form-control" name="Stop_Name" id="stopName" placeholder="Enter Stop Name">
                                                </div>
                                            </div>

                                            <!-- Education Loan -->
                                            <div class="form-group mt-3">
                                                <label for="loanApplicable">Education Loan Applicable?</label>
                                                <select class="form-control" name="education_loan" id="loanApplicable" onchange="toggleLoanDetails()">
                                                    <option value="">Select an option</option>
                                                    <option value="Applicable">Applicable</option>
                                                    <option value="Not Applicable">Not Applicable</option>
                                                </select>
                                            </div>

                                            <!-- Loan Details -->
                                            <div id="loanDetails" style="display:none;">
                                                <div class="form-group">
                                                    <label for="bankName">Bank Name:</label>
                                                    <input type="text" class="form-control" name="bankname" id="bankName" placeholder="Enter Bank Name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="branchName">Branch Name:</label>
                                                    <input type="text" class="form-control" name="branchname" id="branchName" placeholder="Enter Branch Name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="district">District:</label>
                                                    <input type="text" class="form-control" name="district" id="district" placeholder="Enter District">
                                                </div>
                                            </div>
                                            <!-- Certificate Application Option -->
                                            <div class="form-group mt-3" id="certificateApplyOptions">
                                                <label for="certificateOption">Select Certificate Application Type*</label>
                                                <select class="form-control" name="certificate" id="certificateOption" onchange="handleCertificateOptionChange()" required>
                                                    <option value="">Select an option</option>
                                                    <option value="Bonafide Certificate">Bonafide Certificate </option>
                                                    <option value="Fees Structure">Fees Structure </option>

                                                </select>
                                            </div>
                                        </div>


                                        <!-- Right Side -->
                                        <div class="form-group col-md-6">

                                            <!-- Purpose of Certificate -->
                                            <div class="form-group">
                                                <label for="certificatePurpose" class="form-label">Purpose of Certificate*</label>
                                                <select class="form-select" name="Purpose_of_Certificate" id="certificatePurpose" onchange="toggleOtherPurposeField()" required>
                                                    <option value="">Select Purpose</option>
                                                    <option value="Labour Welfare">Labour Welfare</option>
                                                    <option value="Ulavar Pathukappu Thittam">Ulavar Pathukappu Thittam</option>
                                                    <option value="Chief Minister Scholarship">Chief Minister Scholarship</option>
                                                    <option value="USA Foundation Scholarship">USA Foundation Scholarship</option>
                                                    <option value="First Graduate Apply">First Graduate Apply</option>
                                                    <option value="Neet Counseling">Neet Counseling</option>
                                                    <option value="Passport Apply">Passport Apply</option>
                                                    <option value="Passport Renewal">Passport Renewal</option>
                                                    <option value="Certificate Corrections">Certificate Corrections</option>
                                                    <option value="Medium Of Institutions Certificate">Medium Of Institutions Certificate</option>
                                                    <option value="Bank Account Opening">Bank Account Opening</option>
                                                    <option value="World Organisation Women Rural Development Scholarship">World Organisation Women Rural Development Scholarship</option>
                                                    <option value="CRP Education Fund Scholarship">CRP Education Fund Scholarship</option>
                                                    <option value="PMSS BC/MBC/DNC Scholarship">PMSS BC/MBC/DNC Scholarship</option>
                                                    <option value="PMSS-SC/ST Scholarship">PMSS-SC/ST Scholarship</option>
                                                    <option value="Education Loan">Education Loan</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>

                                            <div class="form-group mt-3" id="otherPurposeField" style="display: none;">
                                                <label for="otherPurpose">Please Specify:</label>
                                                <input type="text" class="form-control" name="Others" id="otherPurpose" placeholder="Enter purpose">
                                            </div>

                                            <!-- Upload File -->
                                            <div class="form-group">
                                                <label for="uploadFileField1" class="form-label">Bonafide Proof</label>
                                                <input type="file" class="form-control" name="upload_file_1" id="uploadFileField1" accept="application/pdf">
                                            </div>
                                            <div class="form-group">
                                                <label for="uploadFileField2" class="form-label">Fees Structure Proof</label>
                                                <input type="file" class="form-control" name="upload_file_2" id="uploadFileField2" accept="application/pdf">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="row mt-4">
                                        <div class="col text-end">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Footer -->
    <?php include 'footer.php'; ?>


    <script>
        const loaderContainer = document.getElementById('loaderContainer');

        function showLoader() {
            loaderContainer.classList.add('show');
        }

        function hideLoader() {
            loaderContainer.classList.remove('show');
        }

        //    automatic loader
        document.addEventListener('DOMContentLoaded', function() {
            const loaderContainer = document.getElementById('loaderContainer');
            const contentWrapper = document.getElementById('contentWrapper');
            let loadingTimeout;

            function hideLoader() {
                loaderContainer.classList.add('hide');
                contentWrapper.classList.add('show');
            }

            function showError() {
                console.error('Page load took too long or encountered an error');
                // You can add custom error handling here
            }

            // Set a maximum loading time (10 seconds)
            loadingTimeout = setTimeout(showError, 10000);

            // Hide loader when everything is loaded
            window.onload = function() {
                clearTimeout(loadingTimeout);

                // Add a small delay to ensure smooth transition
                setTimeout(hideLoader, 500);
            };

            // Error handling
            window.onerror = function(msg, url, lineNo, columnNo, error) {
                clearTimeout(loadingTimeout);
                showError();
                return false;
            };
        });

        // Toggle Sidebar
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const body = document.body;
        const mobileOverlay = document.getElementById('mobileOverlay');

        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-show');
                mobileOverlay.classList.toggle('show');
                body.classList.toggle('sidebar-open');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }
        hamburger.addEventListener('click', toggleSidebar);
        mobileOverlay.addEventListener('click', toggleSidebar);
        // Toggle User Menu
        const userMenu = document.getElementById('userMenu');
        const dropdownMenu = userMenu.querySelector('.dropdown-menu');

        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            dropdownMenu.classList.remove('show');
        });

        // Toggle Submenu
        const menuItems = document.querySelectorAll('.has-submenu');
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                const submenu = item.nextElementSibling;
                item.classList.toggle('active');
                submenu.classList.toggle('active');
            });
        });

        // Handle responsive behavior
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('mobile-show');
                mobileOverlay.classList.remove('show');
                body.classList.remove('sidebar-open');
            } else {
                sidebar.style.transform = '';
                mobileOverlay.classList.remove('show');
                body.classList.remove('sidebar-open');
            }
        });

        // Function to update the file name in the label
        function updateFileName() {
            const fileInput = document.getElementById('validatedCustomFile');
            const fileLabel = document.getElementById('fileLabel');

            // Check if a file was selected and update the label text
            if (fileInput.files.length > 0) {
                fileLabel.textContent = fileInput.files[0].name;
            } else {
                fileLabel.textContent = "Choose file...";
            }
        }



        function fileValidation(inputElement) {
            const file = inputElement.files[0];
            const fileSizeLimit = 2 * 1024 * 1024; // 2 MB in bytes
            const labelElement = inputElement.nextElementSibling;
            const errorElement = inputElement.parentElement.nextElementSibling;
            if (file) {
                const fileType = file.type;
                // Check if the file is a PDF
                if (fileType !== 'application/pdf') {
                    errorElement.textContent = 'Only PDF files are allowed';
                    labelElement.textContent = 'Choose file';
                    inputElement.value = ""; // Clear the input
                    return;
                }
                // Check file size
                if (file.size > fileSizeLimit) {
                    errorElement.textContent = 'File size exceeds 2 MB';
                    labelElement.textContent = 'Choose file';
                    inputElement.value = ""; // Clear the input
                } else {
                    errorElement.textContent = '';
                    labelElement.textContent = file.name; // Display the file name
                }
            }
        }

        function toggleLoanDetails() {
            var loanApplicable = document.getElementById("loanApplicable").value;
            var loanDetails = document.getElementById("loanDetails");

            if (loanApplicable === "Applicable") {
                loanDetails.style.display = "block";
            } else {
                loanDetails.style.display = "none";
            }
        }


        document.addEventListener("DOMContentLoaded", function() {
            const selectElement = document.getElementById('batch'); // Match the HTML id here
            const startYear = 2024; // Start from the desired highest range year
            const numberOfRanges = 4; // Number of ranges to display

            // Add the specific ranges first
            const specificRanges = ['2024-2026', '2023-2025'];
            specificRanges.forEach(range => {
                const option = document.createElement('option');
                option.value = range;
                option.textContent = range;
                selectElement.appendChild(option);
            });

            // Dynamically generate other ranges
            for (let i = 0; i < numberOfRanges; i++) {
                const yearStart = startYear - i; // Calculate the starting year
                const yearEnd = yearStart + 4; // Add 4 to get the ending year
                const range = `${yearStart}-${yearEnd}`;

                // Avoid duplicating specific ranges
                if (!specificRanges.includes(range)) {
                    const option = document.createElement('option');
                    option.value = range;
                    option.textContent = range;
                    selectElement.appendChild(option);
                }
            }
        });


        function handleAdmissionTypeChange() {
            const admissionType = document.getElementById('admissionCategory').value;
            const firstGraduate = document.getElementById('firstGraduate');
            const hiddenFirstGraduate = document.getElementById('hiddenFirstGraduate');

            if (admissionType === 'Management') {
                firstGraduate.value = 'No'; // Set default value to 'No'
                firstGraduate.setAttribute('disabled', true); // Make it read-only
                hiddenFirstGraduate.value = 'No'; // Update hidden input
            } else {
                firstGraduate.removeAttribute('disabled'); // Allow user selection
                firstGraduate.value = ""; // Reset value
                hiddenFirstGraduate.value = ""; // Clear hidden input
            }

            // Update hidden input whenever the dropdown value changes
            firstGraduate.addEventListener('change', function() {
                hiddenFirstGraduate.value = firstGraduate.value;
            });
        }


        function handleBoardingChange() {
            const boardingCategory = document.getElementById("boardingCategory").value;
            const hostelOptions = document.getElementById("hostelOptions");
            const outBusOptions = document.getElementById("outBusOptions");

            // Hide both sections initially
            if (hostelOptions) hostelOptions.style.display = "none";
            if (outBusOptions) outBusOptions.style.display = "none";

            // Show the relevant section based on selection
            if (boardingCategory === "Hostel" && hostelOptions) {
                hostelOptions.style.display = "block";
            } else if (boardingCategory === "Bus Commer" && outBusOptions) {
                outBusOptions.style.display = "block";
            }
        }


        document.getElementById('appliedDate').value = new Date().toISOString().split('T')[0];

        function toggleOtherPurposeField() {
            const certificatePurpose = document.getElementById('certificatePurpose');
            const otherPurposeField = document.getElementById('otherPurposeField');
            otherPurposeField.style.display = certificatePurpose.value === 'Other' ? 'block' : 'none';
        }

        $(document).on('submit', '#save_user', function(e) {
            e.preventDefault(); // Prevent the form from submitting normally
            var formData = new FormData(this); // Collect form data, including files

            // Append the action to the form data
            formData.append("action", "save_newuser");

            $.ajax({
                type: "POST",
                url: "bonafide.php", 
                data: formData, 
                processData: false, 
                contentType: false, 
                success: function(response) {
                    var result = JSON.parse(response); 
                    if (result.status === 'error') {
                       
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: result.message,
                        }).then(() => {
                            
                            $('#add_user').modal('hide'); 
                            $('#save_user')[0].reset(); 

                            // Optionally, refresh the user list
                            $('#user').load(location.href + " #user");

                           
                        });

                    } else {
                       
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message, 
                        }).then(() => {
                           
                            $('#add_user').modal('hide');
                            $('#save_user')[0].reset();

                            // Optionally, refresh the user list
                            $('#user').load(location.href + " #user");

                           
                        });
                    }
                },
                error: function() {
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong, please try again later.'
                    });
                }
            });
        });


        document.querySelector("form").addEventListener("submit", function(event) {
            event.preventDefault(); 

            let formData = new FormData(this);

            fetch("bonafide.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "error") {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: data.message
                        });
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: data.message
                        }).then(() => {
                            location.reload(); 
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Handle View File 1 Button
            document.querySelectorAll('.view_student2').forEach(button => {
                button.addEventListener('click', function() {
                    const filePath = this.getAttribute('data-file-path1');
                    const modalBody = document.getElementById('viewpatentModalBody34');
                    if (filePath) {
                        modalBody.innerHTML = `<iframe src="${filePath}" style="width:100%; height:500px;" frameborder="0"></iframe>`;
                    } else {
                        modalBody.innerHTML = '<p>No file uploaded</p>';
                    }
                });
            });


            // Handle View File 2 Button
            document.querySelectorAll('.view_data1').forEach(button => {
                button.addEventListener('click', function() {
                    const filePath = this.getAttribute('data-file-path2');
                    const modalBody = document.getElementById('viewpatentModalBody78');
                    if (filePath) {
                        modalBody.innerHTML = `<iframe src="${filePath}" style="width:100%; height:500px;" frameborder="0"></iframe>`;
                    } else {
                        modalBody.innerHTML = '<p>No file uploaded</p>';
                    }
                });
            });
        });

        // Upload file 1

        $(document).on('click', '.view_student2', function() {
            var filePath = $(this).data('file-path1');
            $('#viewpatentModalLabel45').text('View File'); 

            if (filePath) {
                $('#viewpatentModalBody34').html('<iframe src="' + filePath + '" frameborder="0" style="width:100%; height:500px;"></iframe>');
            } else {
                $('#viewpatentModalBody34').html('<p>No file uploaded</p>');
            }

            $('#viewpatentModal89').modal('show'); 
        });

        //  Upload file 2

        $(document).on('click', '.view_data1', function() {
            var filePath = $(this).data('file-path2'); 
            $('#viewpatentModalLabel67').text('Uploaded Document'); 

            if (filePath) {
                $('#viewpatentModalBody78').html('<iframe src="' + filePath + '" frameborder="0" style="width:100%; height:500px;"></iframe>');
            } else {
                $('#viewpatentModalBody78').html('<p>No file uploaded</p>'); 
            }

            $('#viewpatent67').modal('show');
        });


        $(document).on('click', '.viewFeedback', function() {
            var feedback = $(this).data('feedback');
            $('#dynamicModalLabel4').text('Feedback');
            $('#dynamicModalBody4').html('<input type="text" class="form-control" value="' + feedback + '" readonly>');
            // Show the modal
            $('#dynamicModal4').modal('show');
        });

 
        // Student Form Model

        $(document).ready(function() {
            $(document).on("click", ".viewDetails", function() {
                let studentData = $(this).data("student");

                $("#modalFatherName").text(studentData?.Father_Name || "N/A");
                $("#modalDOB").text(studentData?.DOB || "N/A");
                $("#modalGender").text(studentData?.Gender || "N/A");
                $("#modalAppliedDate").text(studentData?.Applied_Date || "N/A");
                $("#modalContactNo").text(studentData?.Contact_No || "N/A");
                $("#modalBatch").text(studentData?.batch || "N/A");
                $("#modalYearLevel").text(studentData?.Year_Level || "N/A");
                $("#modalAcademicYear").text(studentData?.academic_year || "N/A");
                $("#modalAdmissionType").text(studentData?.Admission_Type || "N/A");
                $("#modalFirstGraduate").text(studentData?.First_Graduate || "N/A");

                let boardingInfo = (studentData?.Boarding === "Hostel") ? [studentData?.Boarding, studentData?.Hostel_Type, studentData?.Bus_No, studentData?.Stop_Name].filter(Boolean).join(" / ") : [studentData?.Boarding, studentData?.Bus_No, studentData?.Stop_Name].filter(Boolean).join(" / ");
                $("#modalBoardingInfo").text(boardingInfo || "N/A");

                $("#modalPurpose").text(studentData?.Purpose_of_Certificate || "N/A");
                $("#modalOthers").text(studentData?.Others || "N/A");

                let loanInfo = [studentData?.education_loan, studentData?.bankname, studentData?.branchname, studentData?.district].filter(Boolean).join(" / ");
                $("#modalLoanInfo").text(loanInfo || "N/A");

                let modal = new bootstrap.Modal(document.getElementById("viewDetailsModal"));
                modal.show();
            });
        });
    </script>
</body>

</html>