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
                    <a class="nav-link active" id="purchaseorder-tab" id="apply_bonafide_tab" data-bs-toggle="tab" href="#apply_bonafide1" role="tab" aria-controls="apply_bonafide" aria-selected="true">
                        <i class="fa fa-file-alt tab-icon"></i> Apply Bonafide
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="manager-tab" id="accepted_bonafide_tab" data-bs-toggle="tab" href="#accepted_bonafide2" role="tab" aria-controls="accepted_bonafide" aria-selected="false">
                        <i class="fa fa-check-circle tab-icon"></i> Accepted Bonafide
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="edit-bus-tab" id="rejected_bonafide_tab" data-bs-toggle="tab" href="#rejected_bonafide3" role="tab" aria-controls="rejected_bonafide" aria-selected="false">
                        <i class="fa fa-times-circle tab-icon"></i> Rejected Bonafide
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="add-bus-tab" id="waiting_for_approval_tab" data-bs-toggle="tab" href="#waiting_for_approval4" role="tab" aria-controls="waiting_for_approval" aria-selected="false">
                        <i class="fa fa-hourglass-half tab-icon"></i> Waiting for Approval
                    </a>
                </li>
            </ul>


            <div class="tab-content">
                <!-- Apply Bonafide Tab -->
                <div class="tab-pane p-3 fade show active" id="apply_bonafide1" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-secondary ms-auto" data-bs-toggle="modal" data-bs-target="#add_user" id="style-yQg7i">Add Student Details</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="user" class="table table-striped table-bordered">
                                            <thead class="gradient-header">
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Student Name</th>
                                                    <th>Father Name</th>
                                                    <th>DOB</th>
                                                    <th>Gender</th>
                                                    <th>Reg no</th>
                                                    <th>Department</th>
                                                    <th>Contact No</th>
                                                    <th>Applied Date</th>
                                                    <th>Batch</th>
                                                    <th>Present Year</th>
                                                    <th>Academic Year</th>
                                                    <th>Admission Type</th>
                                                    <th>First Graduate</th>
                                                    <th>Boarding</th>
                                                    <th>Bus No</th>
                                                    <th>Stop Name</b></th>
                                                    <th>Bonafide Type</th>
                                                    <th>Others</th>
                                                    <th>Education Loan</th>
                                                    <th>Bank Name</th>
                                                    <th>Branch Name</th>
                                                    <th>District</th>
                                                    <th>Apply for Certificate</th>
                                                    <th>Bonafide Proof</th>
                                                    <th>Fees Structure Proof</th>


                                                    <!-- New column for file view -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sid = 1;
                                                $query = "SELECT * FROM bonafide";
                                                $result = mysqli_query($db, $query);
                                                while ($row = mysqli_fetch_array($result)) {
                                                ?>

                                                    <tr>
                                                        <td><?php echo $s; ?></td>
                                                        <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Father_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['DOB']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Gender']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Register_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Department']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Contact_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Applied_Date']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['batch']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Year_Level']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['academic_year']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Admission_Type']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['First_Graduate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Boarding'] === 'Out Bus' ? $row['Out_bus'] : $row['Boarding']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Bus_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Stop_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Purpose_of_Certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Others']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['education_loan']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['bankname']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['branchname']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['district']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['certificate']); ?></td>
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
                                                    $s++;
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


                <!-- Modal for Viewing File 1 -->
                <div class="modal fade" id="viewpatentModal89" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" >Uploaded Document</h5>
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
                            <div class="card">

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
                                                $sql2 = "SELECT * FROM `bonafide` WHERE Status='1'";
                                                $result2 = mysqli_query($conn, $sql2);
                                                $s = 1;
                                                while ($row = mysqli_fetch_array($result2)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $s; ?></td>
                                                        <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Register_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Purpose_of_Certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['academic_year'] . "/MKCE/ADMIN/" . $row['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Applied_Date']); ?></td>

                                                    </tr>
                                                <?php
                                                    $s++;
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
                            <div class="card">
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
                                                $sql2 = "SELECT * FROM `bonafide` WHERE Status='7'";
                                                $result2 = mysqli_query($conn, $sql2);
                                                $s = 1;

                                                while ($row = mysqli_fetch_array($result2)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $s; ?></td>
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
                                                    $s++;
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
                            <div class="card">
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
                                                $sql2 = "SELECT * FROM `bonafide` WHERE Status='0'";
                                                $result2 = mysqli_query($conn, $sql2);
                                                $s = 1;

                                                while ($row = mysqli_fetch_array($result2)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $s; ?></td>
                                                        <td><?php echo htmlspecialchars($row['Student_Name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Register_No']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Purpose_of_Certificate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['Applied_Date']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['academic_year'] . "/MKCE/ADMIN/" . $row['sid']); ?></td>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            <i class="fas fa-clock" style="font-size: 20px; color: #077fff;" aria-label="Waiting for Approval"></i>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $s++;
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
                                        <!-- Student Name (Left Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="studentName" class="form-label">Student Name*</label>
                                            <input type="text" name="Student_Name" class="form-control" placeholder="Name" required id="studentName" oninput="this.value = this.value.toUpperCase();">
                                        </div>

                                        <!-- Father Name (Right Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="fatherName" class="form-label">Father Name*</label>
                                            <input type="text" name="Father_Name" class="form-control" placeholder="Name" required oninput="this.value = this.value.toUpperCase();">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Register No (Left Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="registerNo" class="form-label">Register No*</label>
                                            <input type="text" name="Register_No" class="form-control" placeholder="Name" required oninput="this.value = this.value.toUpperCase();">
                                        </div>

                                        <!-- Department (Right Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="department" class="form-label">Department *</label>
                                            <select class="form-select" name="Department" required>
                                                <option value="">Select</option>
                                                <option value="Artificial Intelligence and Data Science">Artificial Intelligence and Data Science</option>
                                                <option value="Artificial Intelligence and Machine Learning">Artificial Intelligence and Machine Learning</option>
                                                <option value="Civil Engineering">Civil Engineering</option>
                                                <option value="Computer Science and Business Systems">Computer Science and Business Systems</option>
                                                <option value="Computer Science and Engineering">Computer Science and Engineering</option>
                                                <option value="Electrical and Electronics Engineering">Electrical and Electronics Engineering</option>
                                                <option value="Electronics Engineering (VLSI Design)">Electronics Engineering (VLSI Design)</option>
                                                <option value="Electronics and Communication Engineering">Electronics and Communication Engineering</option>
                                                <option value="Information Technology">Information Technology</option>
                                                <option value="Mechanical Engineering">Mechanical Engineering</option>
                                                <option value="Master of Business Administration">Master of Business Administration</option>
                                                <option value="Master of Computer Applications">Master of Computer Applications</option>
                                                <option value="CSE(Artificial Intelligence and Machine Learning)">CSE(Artificial Intelligence and Machine Learning)</option>
                                                <option value="ME">ME</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Date of Birth (Left Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="dob" class="form-label">DOB*</label>
                                            <input type="date" id="dob" name="DOB" class="form-control" required>
                                        </div>

                                        <!-- Gender (Right Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="gender" class="form-label">Gender*</label>
                                            <select class="form-select" id="gender" name="Gender" required>
                                                <option value="">Select</option>
                                                <option value="Male">MALE</option>
                                                <option value="Female">FEMALE</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Upload an Image (Left Side) -->
                                        <div class="form-group col-md-6">
                                            <div class="mb-3">
                                                <label for="Upload an Image" class="form-label">Upload an Image *</label>

                                                <div class="input-group">
                                                    <input type="file" class="form-control" name="image" id="validatedCustomFile" onchange="updateFileName()" required>

                                                </div>
                                            </div>
                                        </div>


                                        <!-- Applied Date (Right Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="appliedDate" class="form-label">Applied Date*</label>
                                            <input type="date" id="appliedDate" name="Applied_Date" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Contact Number (Left Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="contactNumber" class="form-label">Contact No</label>
                                            <input type="tel" name="Contact_No" class="form-control" placeholder="Enter Contact Number" pattern="[0-9]{10}" required>
                                        </div>

                                        <!-- Batch(Right Side) -->
                                        <div class="form-group col-md-6">
                                            <label for="batch" class="form-label">Batch*</label>
                                            <select class="form-select" name="batch" id="batch" required>
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>



                                    <div class="row">
                                        <!-- Year of Study (Left Side) -->
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
                                                <option value="Counseling (7.5% Special)">COUNSELING (7.5% Special)</option>

                                            </select>
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
                                                            <option value="Bonafide Certificate ">Bonafide Certificate </option>
                                                            <option value="Fees Structure ">Fees Structure </option>

                                                        </select>
                                                    </div>
                                                </div>


                                                <!-- Right Side -->
                                                <div class="col-md-6">
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
                url: "bonafide.php", // Replace with your actual PHP script URL
                data: formData, // Send the FormData object to the backend
                processData: false, // Important: Prevent jQuery from processing the data
                contentType: false, // Important: Prevent jQuery from setting content-type
                success: function(response) {
                    var result = JSON.parse(response); // Parse the JSON response from PHP
                    if (result.status === 'error') {
                        // SweetAlert error for failure case
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: result.message, // Display the error message from the response
                        }).then(() => {
                            // Hide the form after successful submission
                            $('#add_user').modal('hide'); // Hide the modal
                            $('#save_user')[0].reset(); // Reset the form

                            // Optionally, refresh the user list
                            $('#user').load(location.href + " #user");

                            // Alternatively, you could redirect or update the page as needed
                        });

                    } else {
                        // SweetAlert success for success case
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message, // Display success message
                        }).then(() => {
                            // Hide the form after successful submission
                            $('#add_user').modal('hide'); // Hide the modal
                            $('#save_user')[0].reset(); // Reset the form

                            // Optionally, refresh the user list
                            $('#user').load(location.href + " #user");

                            // Alternatively, you could redirect or update the page as needed
                        });
                    }
                },
                error: function() {
                    // SweetAlert error for AJAX failure case
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong, please try again later.'
                    });
                }
            });
        });



        $(document).on('click', '.view_student2', function() {
            var filePath = $(this).data('file-path1'); // Getting the file path from the data attribute
            $('#viewpatentModalLabel45').text('View File'); // Set modal title
            $('#viewpatentModalBody34').html('<iframe src="' + filePath + '" frameborder="0" style="width:100%; height:500px;"></iframe>'); // Set iframe content
            $('#viewpatentModal89').modal('show'); // Show the modal
        });

        $(document).on('click', '.view_data1', function() {
            var filePath = $(this).data('file-path2'); // Getting the file path from the data attribute
            $('#viewpatentModalLabel67').text('Uploaded Document'); // Set modal title
            $('#viewpatentModalBody78').html('<iframe src="' + filePath + '" frameborder="0" style="width:100%; height:500px;"></iframe>'); // Set iframe content
            $('#viewpatent67').modal('show'); // Show the modal
        });

        $(document).on('click', '.viewFeedback', function() {
            var feedback = $(this).data('feedback');

            // Set the modal title and body content
            $('#dynamicModalLabel4').text('Feedback');
            $('#dynamicModalBody4').html('<input type="text" class="form-control" value="' + feedback + '" readonly>');
            // Show the modal
            $('#dynamicModal4').modal('show');
        });
    </script>
</body>

</html>