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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">



    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>







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

        .small-btn {
            padding: 1px 1px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'side.php'; ?>

    <!-- Main Content -->
    <div class="content">

        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
        </div>

        <!-- Topbar -->
        <?php include 'ftopbar.php'; ?>
        <div class="container-fluid">

            <!-- Breadcrumb -->
            <div class="breadcrumb-area custom-gradient">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page">Office </li>
                    </ol>
                </nav>
            </div>




            <!-- Apply Bonafide Tab -->
            <div class="tab-pane p-3 fade show active" id="apply_bonafide" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card" style="border: none;">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="user" class="table table-striped table-bordered">
                                        <thead class="gradient-header">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Student Name</th>
                                                <th>Register No</th>
                                                <th>Department</th>

                                                <th>Apply for Certificate</th>
                                                <th>Details</th>
                                                <th>Fees Structure</th>
                                                <th>Bonafide Proof</th>
                                                <th>Fees Structure Proof</th>
                                                <th>Bonafide</th>
                                                <th>Status</th>



                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM bonafide";
                                            $result = mysqli_query($conn, $sql);
                                            $s = 1;
                                            while ($row = mysqli_fetch_array($result)) {
                                            ?>
                                                <tr>
                                                    <td><?= $s; ?></td>
                                                    <td><?= $row['Student_Name']; ?></td>

                                                    <td><?= $row['Register_No']; ?></td>
                                                    <td><?= $row['Department']; ?></td>
                                                    <td><?php echo htmlspecialchars($row['certificate']); ?></td>

                                                    <!-- View Details -->

                                                    <td>
                                                        <button class="btn btn-primary small-btn viewDetails" data-toggle="modal" data-target="#viewDetailsModal"
                                                            data-student='<?= json_encode($row); ?>'>View Details</button>

                                                    </td>


                                                    <!-- Bus Commer Icon -->

                                                    <td style="display: flex; align-items: center; justify-content: flex-start; padding-top: 40px; padding-left: 20px;">
                                                        <?php if ($row['Boarding'] === 'Bus Commer'): ?>
                                                            <span class="btn custom-btn me-1"
                                                                data-id="<?php echo isset($row['id']) ? $row['id'] : ''; ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#feesModal"
                                                                data-bs-toggle="tooltip"
                                                                title="Print Bus Details">
                                                                <i class="fas fa-bus" style="font-size: 25px;"></i>
                                                            </span>
                                                        <?php endif; ?>

                                                        <!-- Fees Print Icon -->

                                                        <span class="printModal2"
                                                            style="cursor: pointer; margin-left: 20px; /* Moves the icon 2 steps to the right */<?php echo ($row['Purpose_of_Certificate'] === 'Education Loan')
                                                                                                                                                    ? 'color:#007bff;' // Blue color when applicable
                                                                                                                                                    : 'pointer-events: none; color: #ccc;'; ?>"
                                                            data-id="<?php echo isset($row['id']) ? $row['id'] : ''; ?>"
                                                            data-bs-toggle="tooltip"
                                                            title="<?php echo ($row['Purpose_of_Certificate'] === 'Education Loan') ? 'Print Fees Structure' : 'Not Applicable'; ?>">
                                                            <i class="bi bi-printer-fill" style="font-size: 25px;"></i>
                                                        </span>
                                                    </td>
                                                    <td>

                                                        <button type="button" class="btn btn-info btn-sm view_student2"
                                                            style="padding: 2px 8px; font-size: 12px;"
                                                            data-file-path1="<?= htmlspecialchars($row['upload_file_1'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            View File
                                                        </button>
                                                    </td>
                                                    <td style="padding-left: 40px;">
                                                        <button type="button" class="btn btn-info btn-sm view_data1"
                                                            style="padding: 5px 6px; font-size: 12px;"
                                                            data-file-path2="<?= htmlspecialchars($row['upload_file_2'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            View File
                                                        </button>
                                                    </td>



                                                    <!-- Bonafide print-->

                                                    <td>
                                                        <div style="margin-left: 20px;">
                                                            <?php if ($row['status'] == 1): ?>
                                                                <span class="printModal" style="cursor: pointer;" data-id="<?= $row['id'] ?? ''; ?>">
                                                                    <i class="bi bi-printer-fill" style="font-size: 25px; color:#0d6efd;"></i>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="printModal" style="pointer-events: none; opacity: 0.5;" data-id="<?= $row['id'] ?? ''; ?>">
                                                                    <i class="bi bi-printer-fill" style="font-size: 25px;"></i>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>


                                                    <!-- status -->

                                                    <td style="padding-left: 60px;">
                                                        <div class="d-flex justify-content-start">
                                                            <?php if ($row['status'] == 1): ?>
                                                                <button class="btn btn-success btn-icon me-2" aria-label="Accepted" disabled>
                                                                    <i class="fas fa-check" style="font-size: 16px;"></i>
                                                                </button>
                                                            <?php elseif ($row['status'] == 7): ?>
                                                                <button class="btn btn-danger btn-icon" aria-label="Rejected" disabled>
                                                                    <i class="fas fa-times" style="font-size: 16px;"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <button class="btn btn-success btn-icon me-2" aria-label="Accept" data-id="<?= $row['id']; ?>" onclick="updateStatus(1, '<?= $row['id']; ?>')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button class="btn btn-danger btn-icon" aria-label="Reject" data-id="<?= $row['id']; ?>" onclick="updateStatus(7, '<?= $row['id']; ?>')">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>




                                                </tr>
                                            <?php $s++;
                                            } ?>
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

            <!-- Fees Structure Modal -->

            <div class="modal fade" id="feesModal" tabindex="-1" aria-labelledby="feesModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="feesModalLabel">Fees Structure Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="feesForm" method="post" action="backend.php">
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id" value="">

                                <!-- Student Name -->
                                <div class="mb-3">
                                    <label for="studentName" class="form-label">Student Name</label>
                                    <input type="text" class="form-control" name="Student_Name" id="studentName" readonly>
                                </div>

                                <!-- Bus No -->
                                <div class="mb-3">
                                    <label for="busNo" class="form-label">Bus No</label>
                                    <input type="text" class="form-control" name="Bus_No" id="busNo" readonly>
                                </div>

                                <!-- Stop Name -->
                                <div class="mb-3">
                                    <label for="stopName" class="form-label">Stop Name</label>
                                    <input type="text" class="form-control" name="Stop_Name" id="stopName" readonly>
                                </div>

                                <!-- Bus Fees -->
                                <div class="mb-3">
                                    <label for="daysScholar" class="form-label">Bus Fees</label>
                                    <input type="number" class="form-control" name="days_scholar" id="daysScholar" placeholder="Enter Bus Fees Amount">
                                </div>
                            </div>


                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
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

            function toggleLoanDetails() {
                var loanApplicable = document.getElementById('loanApplicable').value;
                var printIcon = document.querySelector('.printModal2'); // Select the first occurrence

                // Check if the print icon exists in the document
                if (printIcon) {
                    // Enable or disable the print icon based on the selection
                    if (loanApplicable === 'Applicable') {
                        printIcon.style.pointerEvents = 'auto';
                        printIcon.style.opacity = '1';
                    } else {
                        printIcon.style.pointerEvents = 'none';
                        printIcon.style.opacity = '0.5';
                    }
                }
            }
            // For print (Opens print.php)
            $(document).on('click', '.printModal', function() {
                var applicantId = $(this).data('id');
                if (applicantId) {
                    // Open print.php in a new tab with the applicant ID as a query parameter
                    window.open('bprint.php?id=' + applicantId, '_blank');
                } else {
                    console.warn('Applicant ID is missing!');
                }
            });


            // For print 2 (Opens copy.php)
            $(document).on('click', '.printModal2', function() {
                var applicantId = $(this).data('id');
                if (applicantId) {
                    // Open copy.php in a new tab with the applicant ID as a query parameter
                    window.open('bcopy.php?id=' + applicantId, '_blank');
                } else {
                    console.warn('Applicant ID is missing!');
                }
            });
        </script>
        <script>
            function updateStatus(status, id) {
                let action = status === 1 ? 'Accept' : 'Reject';

                Swal.fire({
                    icon: 'warning',
                    title: `Are you sure you want to ${action}?`,
                    showCancelButton: true,
                    confirmButtonText: `Yes, ${action}`,
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) {
                        if (status === 7) {
                            Swal.fire({
                                title: 'Reject Reason',
                                input: 'textarea',
                                inputPlaceholder: 'Enter rejection reason here...',
                                showCancelButton: true,
                                confirmButtonText: 'Submit',
                                cancelButtonText: 'Cancel',
                                inputValidator: value => {
                                    if (!value) return 'Rejection reason is required!';
                                }
                            }).then(reasonResult => {
                                if (reasonResult.isConfirmed) {
                                    sendRequest(id, status, reasonResult.value);
                                }
                            });
                        } else {
                            sendRequest(id, status);
                        }
                    }
                });
            }

            function sendRequest(id, status, feedback = null) {
                const payload = {
                    id,
                    status_no: status,
                    feedback
                };

                fetch('status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 200) {
                            // Show success message based on the action (Accept or Reject)
                            if (status === 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Status Accepted successfully'
                                }).then(() => location.reload());
                            } else if (status === 7) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Rejected!',
                                    text: 'Status Rejected successfully'
                                }).then(() => location.reload());
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: `Failed to update status: ${data.message}`
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: `An error occurred: ${error.message}`
                        });
                    });
            }
            $(document).ready(function() {
                // When a button with the 'custom-btn' class is clicked
                $('.btn.custom-btn').click(function() {
                    const id = $(this).data('id'); // Get the 'data-id' attribute value
                    $('#id').val(id); // Set the hidden input value

                    // Perform AJAX request to fetch student details
                    $.ajax({
                        url: 'saveFees.php', // Backend PHP file
                        type: 'POST', // HTTP method
                        data: {
                            id: id
                        }, // Data sent to the backend
                        dataType: 'json', // Expect JSON response
                        success: function(data) {
                            if (!data.error) {
                                // Populate fields with the fetched data
                                $('#studentName').val(data.Student_Name || '');
                                $('#busNo').val(data.Bus_No || '');
                                $('#stopName').val(data.Stop_Name || '');
                            } else {
                                alert(data.error); // Show error if any
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data:', error);
                            alert('An error occurred while fetching data.');
                        }
                    });
                });

                // Handle form submission
                $('#feesForm').submit(function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    // Serialize the form data
                    const formData = $(this).serialize();

                    // Perform AJAX request
                    $.ajax({
                        url: 'saveFees.php', // PHP script URL
                        type: 'POST',
                        data: formData,
                        dataType: 'json', // Expect JSON response
                        success: function(response) {
                            console.log(response); // Check the response in the browser console
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.success,
                                }).then(() => {
                                    $('#feesModal').modal('hide'); // Hide the modal
                                    location.reload(); // Reload the page
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.error || 'Unexpected response format.',
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('XHR:', xhr); // Log XHR object
                            console.error('Status:', status); // Log status
                            console.error('Error:', error); // Log error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'An unexpected error occurred: ' + error,
                            });
                        }
                    });
                });
            });
            // Bootstrap Tooltip Initialization
            document.addEventListener("DOMContentLoaded", function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
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

                    // Bootstrap 5 Modal Show
                    let modal = new bootstrap.Modal(document.getElementById("viewDetailsModal"));
                    modal.show();
                });
            });
        </script>
</body>

</html>