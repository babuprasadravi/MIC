<?php include __DIR__ . '/utils/ui/header.php'; ?>
<!-- Page Content -->
<div class="container-fluid">
    <div class="custom-tabs">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" id="course-creation-tab" href="#create-course" role="tab" aria-selected="true">
                    <span class="hidden-xs-down"><i class="fas fa-book tab-icon"></i><b> Course Creation</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="available-courses-tab" href="#available-courses" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-graduation-cap tab-icon"></i><b> Available Courses</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="time-table-tab" href="#time-table" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-graduation-cap tab-icon"></i><b> Time Table</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="attendance-view-tab" href="#attendance-view" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-clipboard-check tab-icon"></i><b>Attendance View</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="academic-administration-tab" href="#academic-administration" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-clock tab-icon"></i><b>Academic Administration</b></span>
                </a>
            </li>
        </ul>
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Course Creation Tab -->
            <div class="tab-pane fade show active" id="create-course" role="tabpanel">
                <div class="row g-4 justify-content-center">
                    <!-- Manual Course Creation Card -->
                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-float hover-card">
                            <div class="card-header border-0 position-relative overflow-hidden p-4">
                                <div class="header-bg bg-gradient-cool"></div>
                                <div class="position-relative d-flex align-items-center gap-3">
                                    <div class="icon-bubble">
                                        <i class="fas fa-edit fa-lg text-green"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title text-white mb-1">Manual Course Creation</h5>
                                        <p class="text-white text-opacity-75 mb-0 small">Create individual courses</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex flex-column h-100">
                                    <div class="text-center">
                                        <div class="d-flex justify-content-center mb-3">
                                            <div class="icon-3d">
                                                <i class="fas fa-book-open fa-2x text-white"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-2">Create Single Course</h5>
                                        <p class="text-muted small mb-4">Design your course structure with our interactive form</p>
                                    </div>

                                    <!-- Steps Section -->
                                    <div class="steps-section d-flex justify-content-around mb-4">
                                        <div class="step text-center">
                                            <div class="step-number">1</div>
                                            <small>Basic Info</small>
                                        </div>
                                        <div class="step text-center">
                                            <div class="step-number">2</div>
                                            <small>Faculty</small>
                                        </div>
                                        <div class="step text-center">
                                            <div class="step-number">3</div>
                                            <small>Students</small>
                                        </div>
                                    </div>

                                    <!-- Create Button -->
                                    <div class="mt-auto">
                                        <button class="btn btn-green btn-lg w-100 ripple-btn" data-bs-toggle="collapse"
                                            data-bs-target="#manualCourseForm" aria-expanded="false">
                                            <i class="fas fa-plus-circle me-2"></i>Start Creating
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Import Card -->
                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-float hover-card">
                            <div class="card-header border-0 position-relative overflow-hidden p-4">
                                <div class="header-bg bg-gradient-warm"></div>
                                <div class="position-relative d-flex align-items-center gap-3">
                                    <div class="icon-bubble">
                                        <i class="fas fa-cloud-upload-alt fa-lg text-orange"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title text-white mb-1">Bulk Import Courses</h5>
                                        <p class="text-white text-opacity-75 mb-0 small">Import multiple courses at once</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <!-- Template Download Section -->
                                <div class="template-section mb-4">
                                    <div class="download-card bg-light-warm rounded-4 p-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-wrapper-sm bg-orange bg-opacity-10 rounded-circle">
                                                <i class="fas fa-file-excel text-orange"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-dark">Course Template</h6>
                                                <p class="text-muted small mb-2">Download standardized format</p>
                                                <a href="templates/course_import_template.xlsx" download
                                                    class="btn btn-orange btn-sm px-3 ripple-btn">
                                                    <i class="fas fa-download me-2"></i>Get Template
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Upload Section -->
                                <form id="bulkImportForm">
                                    <div class="upload-zone bg-light rounded-4 p-4 text-center">
                                        <div class="upload-icon-wrapper mb-3">
                                            <i class="fas fa-cloud-arrow-up fa-2x text-orange opacity-75"></i>
                                        </div>
                                        <h6 class="mb-2">Upload Your File</h6>
                                        <p class="text-muted small mb-3">Drag & drop your file or click to browse</p>
                                        <div class="input-group">
                                            <input type="file" class="form-control border-orange-light" id="fileUpload" accept=".csv, .xls, .xlsx">
                                            <button type="submit" class="btn btn-orange px-4 ripple-btn" disabled>
                                                <i class="fas fa-upload me-2"></i>Import
                                            </button>
                                        </div>
                                        <div class="supported-formats mt-3">
                                            <span class="badge bg-orange bg-opacity-10 text-orange me-2">CSV</span>
                                            <span class="badge bg-orange bg-opacity-10 text-orange me-2">XLS</span>
                                            <span class="badge bg-orange bg-opacity-10 text-orange">XLSX</span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Manual Course Form -->
                <div class="collapse mt-4" id="manualCourseForm">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="form-title mb-4">Course Details</h4>
                            <form>
                                <!-- Course Basic Info Section -->
                                <div class="form-section mb-4">
                                    <h5 class="section-title mb-3">
                                        <i class="fas fa-book-open text-primary me-2"></i>Basic Information
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="courseName" placeholder="Course Name" required>
                                                <label for="courseName">Course Name</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="courseCode" placeholder="Course Code" required>
                                                <label for="courseCode">Course Code</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" id="courseCredit" min="1" max="6" placeholder="Credits" required>
                                                <label for="courseCredit">Course Credits</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="form-floating">
                                                <select class="form-select" id="courseType" required>
                                                    <option value="">Select Type</option>
                                                    <option value="Theory">Theory</option>
                                                    <option value="Theory_Lab">Theory cum Lab</option>
                                                    <option value="Theory_Project">Theory cum Project</option>
                                                    <option value="Lab">Lab</option>
                                                </select>
                                                <label for="courseType">Course Type</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Details Section -->
                                <div class="form-section mb-4">
                                    <h5 class="section-title mb-3">
                                        <i class="fas fa-info-circle text-success me-2"></i>Course Details
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-6 col-sm-4 col-lg-2">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light" id="batch" value="" readonly>
                                                <label for="batch">Batch</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-4 col-lg-2">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light" id="academicYear" value="" readonly>
                                                <label for="academicYear">Academic Year</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-4 col-lg-2">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light" id="semester" value="" readonly>
                                                <label for="semester">Semester</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8 col-lg-4">
                                            <div class="form-floating">
                                                <select class="form-select" id="department" required>
                                                    <option value="">Department</option>
                                                </select>
                                                <label for="department">Course Faculty's Department</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-4 col-lg-2">
                                            <div class="form-floating">
                                                <select class="form-select" id="facultyCount" required>
                                                    <option value="">Select</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                                <label for="facultyCount">Faculty Count</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Faculty Selection Section -->
                                <div class="form-section mb-4">
                                    <h5 class="section-title mb-3">
                                        <i class="fas fa-chalkboard-teacher text-info me-2"></i>Faculty Assignment
                                    </h5>
                                    <div class="row" id="facultySelectionContainer">
                                        <!-- Faculty dropdowns will be added here dynamically -->
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary btn-lg px-4 me-2" id="finalSubmitBtn">
                                        <i class="fas fa-save me-2"></i>Save Course
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary btn-lg px-4">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Available Courses Tab -->
            <div class="tab-pane fade" id="available-courses" role="tabpanel">
                <div class="container mt-4">
                    <!-- Available Courses will be added here dynamically -->
                </div>
            </div>
            <!-- Time Table Tab -->
            <div class="tab-pane fade" id="time-table" role="tabpanel">
                <!-- put all buttons in a row it flex box and give gap 10px between them -->


                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-primary" id="generateTimeTableBtn">Generate Time-Table</button>
                    <button class="btn " id="editTimeTableBtn" style="display: none;">Edit Time-Table</button>
                    <button class="btn btn-success" id="saveTimeTableBtn" style="display: none;">Save Time-Table</button>
                </div>
            </div>
            <!-- Attendance View Tab -->
            <div class="tab-pane fade" id="attendance-view" role="tabpanel">
                <!-- Original Cards View -->
                <div id="attendance-cards-view">
                    <div class="row g-4 py-4">
                        <!-- Hour Attendance Card -->
                        <div class="col-md-4">
                            <div class="admin-card attendance-card">
                                <div class="admin-card-header">
                                    <div class="admin-header-bg admin-gradient-primary"></div>
                                    <div class="position-relative d-flex align-items-center gap-3">
                                        <div class="admin-icon-wrapper">
                                            <i class="fas fa-clock fa-lg text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="admin-text-white mb-1">Hour Attendance</h5>
                                            <p class="admin-text-white opacity-75 mb-0 small">View hourly attendance details</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-card-content">
                                    <button class="btn btn-primary w-100" id="viewHourAttendanceBtn">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Faculty Summary Card -->
                        <div class="col-md-4">
                            <div class="admin-card faculty-card">
                                <div class="admin-card-header">
                                    <div class="admin-header-bg admin-gradient-success"></div>
                                    <div class="position-relative d-flex align-items-center gap-3">
                                        <div class="admin-icon-wrapper">
                                            <i class="fas fa-chalkboard-teacher fa-lg text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="admin-text-white mb-1">Faculty Summary</h5>
                                            <p class="admin-text-white opacity-75 mb-0 small">Faculty attendance overview</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-card-content">
                                    <button class="btn btn-success w-100" id="viewFacultySummaryBtn">
                                        <i class="fas fa-chart-bar me-2"></i>View Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Student Summary Card -->
                        <div class="col-md-4">
                            <div class="admin-card student-card">
                                <div class="admin-card-header">
                                    <div class="admin-header-bg admin-gradient-info"></div>
                                    <div class="position-relative d-flex align-items-center gap-3">
                                        <div class="admin-icon-wrapper">
                                            <i class="fas fa-user-graduate fa-lg text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="admin-text-white mb-1">Student Summary</h5>
                                            <p class="admin-text-white opacity-75 mb-0 small">Student attendance analytics</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-card-content">
                                    <button class="btn btn-info w-100" id="viewStudentSummaryBtn">
                                        <i class="fas fa-chart-pie me-2"></i>View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hour Attendance View -->
                <div id="hour-attendance-view" style="display: none;">
                    <div class="card shadow-lg rounded-4 border-0">
                        <!-- Header Section -->
                        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-4 rounded-top-4">
                            <div class="d-flex align-items-center">
                                <div class="header-icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                    <i class="fas fa-clock fa-lg text-white"></i>
                                </div>
                                <h5 class="mb-0">Hour Attendance Details</h5>
                            </div>
                            <button id="back-to-attendance-cards" class="btn btn-light btn-sm px-3 rounded-pill">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                        </div>

                        <div class="card-body p-4">
                            <!-- Date Selection - Below Header -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="text-secondary mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>Select Date:
                                    </label>
                                    <div class="input-group input-group-sm" style="width: auto;">
                                        <input type="date"
                                            class="form-control form-control-sm"
                                            id="attendanceDate"
                                            max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Existing Content -->
                            <div class="existing-attendance-content">
                                <!-- Faculty Marking Status -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="card-title mb-0 text-secondary" style="font-size: 0.9rem;">
                                            <i class="fas fa-user-check me-2 text-primary"></i>Faculty Marking Status
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Hour</th>
                                                        <th>Faculty Name</th>
                                                        <th>Course</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="facultyMarkingStatus">
                                                    <!-- Will be populated dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Attendance Summary -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="card-title mb-0 text-secondary" style="font-size: 0.9rem;">
                                            <i class="fas fa-chart-pie me-2 text-success"></i>Section Attendance Summary
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr id="attendanceHeader">
                            <!-- Dynamically populated -->
                        </tr>
                                                </thead>
                                                <tbody id="sectionAttendanceSummary">
                                                    <!-- Will be populated dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student Wise Attendance -->
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white py-2">
                                        <h6 class="card-title mb-0 text-secondary" style="font-size: 0.9rem;">
                                            <i class="fas fa-users me-2 text-info"></i>Student Wise Attendance
                                        </h6>
                                    </div>
                                    <div class="card-body">



                                        <button class="btn btn-sm btn-info  mt-5 mb-5" id="downloadStudentAttendanceBtn" style="width: 150px; padding: 5px 10px;">
                                            <i class="fas fa-download me-2"></i>Download
                                        </button>
                                        <div class="table-responsive">


                                            <table class="table table-bordered" id="studentWiseAttendance">
                                                <thead>
                                                    <tr>
                                                        <th>Roll No</th>
                                                        <th>Student Name</th>
                                                        <th>Hour 1</th>
                                                        <th>Hour 2</th>
                                                        <th>Hour 3</th>
                                                        <th>Hour 4</th>
                                                        <th>Hour 5</th>
                                                        <th>Hour 6</th>
                                                        <th>Hour 7</th>
                                                        <th>Hour 8</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Will be populated dynamically -->
                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>
                                <!-- Pagination -->
                                <div id="attendancePagination" class="mt-3">
                                    <!-- Pagination will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faculty Summary View -->
                <div id="faculty-summary-view" style="display: none;">
                    <div class="card shadow-lg rounded-4 border-0">
                        <!-- Header Section -->
                        <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center p-4 rounded-top-4">
                            <div class="d-flex align-items-center">
                                <div class="header-icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                    <i class="fas fa-chalkboard-teacher fa-lg text-white"></i>
                                </div>
                                <h5 class="mb-0">Faculty Attendance Summary</h5>
                            </div>
                            <button id="back-to-attendance-cards-faculty" class="btn btn-light btn-sm px-3 rounded-pill">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <!-- Table Section -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3">S.No.</th>
                                            <th class="py-3">Course Name</th>
                                            <th class="py-3">Faculty Name</th>
                                            <th class="py-3">Total Hours</th>
                                            <th class="py-3">Pending Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody id="facultySummaryTable" class="border-top-0">
                                        <!-- Table content will be dynamically populated -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Empty State Message -->
                            <div id="facultySummaryEmptyState" class="text-center py-5" style="display: none;">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted">No faculty summary data available</h6>
                                <p class="small text-muted mb-0">There are no records to display at this time</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Summary View -->
                <div id="student-summary-view" style="display: none;">
                    <div class="card shadow-lg rounded-4 border-0">
                        <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center p-4 rounded-top-4">
                            <div class="d-flex align-items-center">
                                <div class="header-icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-graduate fa-lg text-white"></i>
                                </div>
                                <h5 class="mb-0">Student Attendance Summary</h5>
                            </div>
                            <button id="back-to-attendance-cards-student" class="btn btn-light btn-sm px-3 rounded-pill">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                        </div>

                        <div class="card-body p-4">
                            <!-- Download Button -->
                            <button class="btn btn-info" id="downloadStudentAttendancePercentageBtn">
                                <i class="fas fa-download me-2"></i>Download
                            </button>
                            
                            <!-- Overall Attendance Table -->
                            <h5 class="mb-3">Overall Attendance Summary</h5>
                            <div class="table-responsive mb-4">
                                <table id="studentOverallAttendanceTable" class="table table-bordered">
                                    <!-- Table content will be populated by DataTable -->
                                </table>
                            </div>

                            <!-- Subject-wise Attendance Table -->
                            <h5 class="mb-3">Subject-wise Attendance Summary</h5>
                            <div class="table-responsive">
                                <table id="studentSubjectWiseAttendanceTable" class="table table-bordered">
                                    <!-- Table content will be populated by DataTable -->
                                </table>
                            </div>

                            <!-- Empty State -->
                            <div id="studentSummaryEmptyState" class="text-center py-5" style="display: none;">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-inbox fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted">No data available</h6>
                                <p class="small text-muted mb-0">There are no records to display</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Administration Tab Content -->
            <div class="tab-pane fade" id="academic-administration" role="tabpanel">
                <div class="container-fluid py-4">
                    <!-- Original Cards View -->
                    <div id="academic-cards-view">
                        <div class="row g-4">
                            <!-- OD/Leave Approval Card -->
                            <div class="col-md-4">
                                <div class="admin-card od-card">
                                    <div class="admin-card-header">
                                        <div class="admin-header-bg admin-gradient-od"></div>
                                        <div class="position-relative d-flex align-items-center gap-3">
                                            <div class="admin-icon-wrapper">
                                                <i class="fas fa-calendar-check fa-lg text-white"></i>
                                            </div>
                                            <div>
                                                <h5 class="admin-text-white mb-1">OD/Leave Management</h5>
                                                <p class="admin-text-white opacity-75 mb-0 small">Process student requests</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-card-content">
                                        <button class="btn btn-primary w-100" id="viewODRequestsBtn">
                                            <i class="fas fa-tasks me-2"></i>View Requests
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Day Order Change Card -->
                            <div class="col-md-4">
                                <div class="admin-card dayorder-card">
                                    <div class="admin-card-header">
                                        <div class="admin-header-bg admin-gradient-dayorder"></div>
                                        <div class="position-relative d-flex align-items-center gap-3">
                                            <div class="admin-icon-wrapper">
                                                <i class="fas fa-exchange-alt fa-lg text-white"></i>
                                            </div>
                                            <div>
                                                <h5 class="admin-text-white mb-1">Day Order Change</h5>
                                                <p class="admin-text-white opacity-75 mb-0 small">Manage timetable changes</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-card-content">
                                        <button class="btn btn-success w-100">
                                            <i class="fas fa-calendar-alt me-2"></i>Modify Day Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Special Attendance Card -->
                            <div class="col-md-4">
                                <div class="admin-card attendance-card">
                                    <div class="admin-card-header">
                                        <div class="admin-header-bg admin-gradient-attendance"></div>
                                        <div class="position-relative d-flex align-items-center gap-3">
                                            <div class="admin-icon-wrapper">
                                                <i class="fas fa-user-check fa-lg text-white"></i>
                                            </div>
                                            <div>
                                                <h5 class="admin-text-white mb-1">Special Attendance</h5>
                                                <p class="admin-text-white opacity-75 mb-0 small">Mark special cases</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-card-content">
                                        <button class="btn btn-warning w-100">
                                            <i class="fas fa-clipboard-check me-2"></i>Manage Special Cases
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- New: Mark Holidays Card -->
                            <div class="col-md-4">
                                <div class="admin-card holiday-card">
                                    <div class="admin-card-header">
                                        <!-- Holiday gradient header -->
                                        <div class="admin-header-bg admin-gradient-holiday"></div>
                                        <div class="position-relative d-flex align-items-center gap-3">
                                            <div class="admin-icon-wrapper">
                                                <i class="fas fa-calendar-alt fa-lg text-white"></i>
                                            </div>
                                            <div>
                                                <h5 class="admin-text-white mb-1">Mark Holidays</h5>
                                                <p class="admin-text-white opacity-75 mb-0 small">Manage holiday schedule</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-card-content">
                                        <button class="btn  btn-holiday w-100" onclick="showHolidayModal()">
                                            <i class="fas fa-calendar-times me-2"></i>Mark Holidays
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Faculty Change Card -->
                            <div class="col-md-4">
                                <div class="admin-card faculty-change-card">
                                    <div class="admin-card-header">
                                        <div class="admin-header-bg admin-gradient-faculty-change"></div>
                                        <div class="position-relative d-flex align-items-center gap-3">
                                            <div class="admin-icon-wrapper">
                                                <i class="fas fa-exchange-alt fa-lg text-white"></i>
                                            </div>
                                            <div>
                                                <h5 class="admin-text-white mb-1">Change Course-Faculty</h5>
                                                <p class="admin-text-white opacity-75 mb-0 small">Manage course assignments</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-card-content">
                                        <button class="btn btn-faculty-change w-100">
                                            <i class="fas fa-exchange-alt me-2"></i>Change Course-Faculty
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!--- add time table --->
                            <div class="col-md-4">
                                <div class="admin-card timeTable-card">
                                    <div class="admin-card-header">
                                        <div class="admin-header-bg admin-gradient-timeTable"></div>
                                        <div class="position-relative d-flex align-items-center gap-3">
                                            <div class="admin-icon-wrapper">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            </div>
                                            <div>
                                                <h5 class="admin-text-white mb-1">Exam time table</h5>
                                                <p class="admin-text-white opacity-75 mb-0 small">Manage Exam time table</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="admin-card-content">
                                        <button class="btn btn-timeTable w-100">
                                        <i class="fas fa-calendar-alt me-2"></i>Add exam time table
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OD/Leave Requests View -->
                    <div id="od-requests-view" style="display: none;">
                        <div class="card shadow-lg rounded-4 border-0">
                            <!-- Header Section -->
                            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-4 rounded-top-4">
                                <div class="d-flex align-items-center">
                                    <div class="header-icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                        <i class="fas fa-calendar-check fa-lg text-white"></i>
                                    </div>
                                    <h5 class="mb-0">OD/Leave Requests</h5>
                                </div>
                                <button id="back-to-admin-cards" class="btn btn-light btn-sm px-3 rounded-pill">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                            </div>

                            <div class="card-body p-4">
                                <!-- Filters Section -->
                                <div class="row mb-4 g-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-3 align-items-center">
                                                <!-- Request Type Filter -->
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label mb-0 me-2 text-muted">Request Type:</label>
                                                    <div class="input-group input-group-sm" style="width: 150px;">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-tag text-primary"></i>
                                                        </span>
                                                        <select class="form-select border-start-0" id="requestTypeFilter">
                                                            <option value="all">All Requests</option>
                                                            <option value="OD">OD Requests</option>
                                                            <option value="Leave">Leave Requests</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Status Filter -->
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label mb-0 me-2 text-muted">Status:</label>
                                                    <div class="input-group input-group-sm" style="width: 150px;">
                                                        <span class="input-group-text bg-light border-end-0">
                                                            <i class="fas fa-check-circle text-primary"></i>
                                                        </span>
                                                        <select class="form-select border-start-0" id="statusFilter">
                                                            <option value="all">All Status</option>
                                                            <option value="pending">Pending</option>
                                                            <option value="approved">Approved</option>
                                                            <option value="rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Requests Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="py-3">Request ID</th>
                                                <th class="py-3">Student Name</th>
                                                <th class="py-3">Roll No</th>
                                                <th class="py-3">Type</th>
                                                <th class="py-3">From Date</th>
                                                <th class="py-3">To Date</th>
                                                <th class="py-3">Reason</th>
                                                <th class="py-3">Status</th>
                                                <th class="py-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="odRequestsTable" class="border-top-0">
                                            <!-- Table content will be dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Empty State Message -->
                                <div id="emptyState" class="text-center py-5" style="display: none;">
                                    <div class="empty-state-icon mb-3">
                                        <i class="fas fa-inbox fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="text-muted">No requests found</h6>
                                    <p class="small text-muted mb-0">There are no requests matching your filters</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty Change View -->
                    <div id="faculty-change-view" style="display: none;">
                        <div class="card shadow-lg rounded-4 border-0">
                            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-4">
                                <div class="d-flex align-items-center">
                                    <div class="header-icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                        <i class="fas fa-exchange-alt fa-lg text-white"></i>
                                    </div>
                                    <h5 class="mb-0">Change Course-Faculty</h5>
                                </div>
                                <button id="back-to-academic-cards-btn" class="btn btn-light btn-sm px-3 rounded-pill px-3 rounded-pill">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                            </div>

                            <div class="card-body p-4">
                                <div class="card border-0 shadow-hover">
                                    <div class="card-body p-4">
                                        <!-- Current Faculty Section -->
                                        <div class="mb-5">
                                            <h6 class="section-title d-flex align-items-center mb-4">
                                                <span class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="fas fa-user-graduate text-primary"></i>
                                                </span>
                                                Current Faculty Details
                                            </h6>

                                            <form id="facultyChangeForm" class="needs-validation" novalidate>
                                                <div class="row g-4">
                                                    <!-- Course Selection -->
                                                    <div class="col-md-6">
                                                        <label class="form-label">Select Course</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light">
                                                                <i class="fas fa-book text-primary"></i>
                                                            </span>
                                                            <select class="form-select" id="currentCourse" required>
                                                                <option value="">Choose course...</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-text">Select the course you want to change faculty for</div>
                                                    </div>

                                                    <!-- Current Faculty Display -->
                                                    <div class="col-md-6">
                                                        <label class="form-label">Current Faculty</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light">
                                                                <i class="fas fa-user-tie text-primary"></i>
                                                            </span>
                                                            <select class="form-select" id="currentFaculty" required disabled>
                                                                <option value="">Choose faculty...</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- New Faculty Section -->
                                        <div class="mt-4">
                                            <h6 class="section-title d-flex align-items-center mb-4">
                                                <span class="icon-wrapper bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="fas fa-user-plus text-success"></i>
                                                </span>
                                                New Faculty Assignment
                                            </h6>

                                            <div class="row g-4">
                                                <!-- Department Selection -->
                                                <div class="col-md-6">
                                                    <label class="form-label">Select Department</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light">
                                                            <i class="fas fa-building text-success"></i>
                                                        </span>
                                                        <select class="form-select" id="newDepartment" required>
                                                            <option value="">Choose department...</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- New Faculty Selection -->
                                                <div class="col-md-6">
                                                    <label class="form-label">Select New Faculty</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light">
                                                            <i class="fas fa-user-tie text-success"></i>
                                                        </span>
                                                        <select class="form-select" id="newFaculty" required>
                                                            <option value="">Choose faculty...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="d-flex justify-content-center mt-5">
                                                <button type="button" id="saveFacultyChange" class="btn btn-primary btn-lg px-5">
                                                    <i class="fas fa-save me-2"></i>
                                                    Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Table View -->
                    <div id="timeTable-view" style="display: none;">
                        <div class="card shadow-lg rounded-4 border-0">
                            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-4">
                                <div class="d-flex align-items-center">
                                    <div class="header-icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                        <i class="fas fa-exchange-alt fa-lg text-white"></i>
                                    </div>
                                    <h5 class="mb-0">Add Time Table</h5>
                                </div>
                                <button id="back-to-admin-btn" class="btn btn-light btn-sm px-3 rounded-pill">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                            </div>

                        <div class="card-body p-4">
                            <div class="card border-0 shadow-hover mb-4">
                                <div class="card-body p-4">
                                    <h6 class="text-primary mb-4"><i class="fas fa-info-circle me-2"></i>Time Table Information</h6>
                                    
                                    <form id="timeTableForm">
                                        <!-- Exam Name Field -->
                                        <div class="mb-4">
                                        <select id="examName" class="form-select">
                                                <option disabled selected>Select Exam Name</option>
                                                <optgroup label="Theory">
                                                    <option value="CIA 1">CIA 1</option>
                                                    <option value="CIA 2">CIA 2</option>
                                                    <option value="Model Exam">Model Exam</option>
                                                    <option value="SSA 1">SSA 1</option>
                                                    <option value="SSA 2">SSA 2</option>
                                                    <option value="AL 1">AL 1</option>
                                                    <option value="AL 2">AL 2</option>
                                                </optgroup>
                                                <optgroup label="Practical">
                                                    <option value="CIA 1">CIA 1</option>
                                                    <option value="CIA 2">CIA 2</option>
                                                    <option value="Model Exam">Model Exam</option>
                                                    <option value="SSA 1">SSA 1</option>
                                                    <option value="SSA 2">SSA 2</option>
                                                    <option value="Laboratory 1">Laboratory 1</option>
                                                    <option value="Laboratory 2">Laboratory 2</option>
                                                    <option value="Model Lab">Model Lab</option>
                                                </optgroup>
                                                <optgroup label="Project">
                                                    <option value="CIA 1">CIA 1</option>
                                                    <option value="CIA 2">CIA 2</option>
                                                    <option value="Model Exam">Model Exam</option>
                                                    <option value="SSA 1">SSA 1</option>
                                                    <option value="SSA 2">SSA 2</option>
                                                    <option value="Review 1">Review 1</option>
                                                    <option value="Review 2">Review 2</option>
                                                    <option value="Review 3">Review 3</option>
                                                </optgroup>
                                                <optgroup label="Lab">
                                                    <option value="Cycle 1">Cycle 1</option>
                                                    <option value="Cycle 2">Cycle 2</option>
                                                </optgroup>
                                            </select>

                                        </div>
                                        
                                       
                                        
                                        <!-- Course Dates Section -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="text-primary mb-0"><i class="fas fa-calendar-alt me-2"></i>Course Exams Schedule</h6>
                                                <button type="button" id="addCourseBtn" class="btn btn-sm btn-timeTable">
                                                    <i class="fas fa-plus me-2"></i>Add Course
                                                </button>
                                            </div>
                                            
                                            <div id="coursesContainer">
                                                <!-- Initial course item -->
                                                
                                            </div>
                                        </div>
                                        
                                        <!-- Submit Button -->
                                        <div class="d-grid gap-2 mt-4">
                                            <button type="submit" class="btn btn-lg btn-timeTable">
                                                <i class="fas fa-save me-2"></i>Save Time Table
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div id="timetableList" class="mt-5">
                                <h5 class="mb-3">Saved Exam Time Tables</h5>
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Course Details</th>
                                        <th>Created On</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody id="timetableBody">
                                    <!-- Timetable rows will be dynamically added here -->
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>        
                </div>

                <!-- Day Order Modification View -->
                <div id="dayorder-modification-view" style="display: none;">
                    <div class="card shadow-sm w-100">
                        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Saturday Day Order Management
                            </h5>
                            <button id="back-to-cards-btn" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <!-- Day Order Override Form -->
                            <div class="card border-0 shadow-hover">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="feature-icon-small d-inline-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10 rounded-3 me-3">
                                            <i class="fas fa-exchange-alt fa-2x p-3"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-0">Change Saturday Day Order</h5>
                                            <p class="text-muted small mb-0">Select a Saturday to modify its day order</p>
                                        </div>
                                    </div>

                                    <form id="dayOrderChangeForm" class="needs-validation" novalidate>
                                        <div class="row justify-content-center">
                                            <div class="col-md-5">
                                                <!-- Date Selection -->
                                                <div class="mb-3">
                                                    <label class="form-label d-flex align-items-center">
                                                        <i class="fas fa-calendar-day text-primary me-2"></i>
                                                        Select Saturday
                                                    </label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-light">
                                                            <i class="fas fa-calendar-alt text-muted"></i>
                                                        </span>
                                                        <input type="date"
                                                            class="form-control"
                                                            id="dayChangeDate"
                                                            required>
                                                    </div>
                                                    <div class="invalid-feedback">Please select a valid Saturday</div>
                                                    <div id="nonSaturdayWarning" class="text-danger mt-2" style="display: none;">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Only Saturdays can be selected for day order changes.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <!-- Day Order Selection -->
                                                <div class="mb-3">
                                                    <label class="form-label d-flex align-items-center">
                                                        <i class="fas fa-exchange-alt text-primary me-2"></i>
                                                        Change To
                                                    </label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-light">
                                                            <i class="fas fa-clock text-muted"></i>
                                                        </span>
                                                        <select class="form-select" id="newDayOrder" required>
                                                            <option value="">Select day order...</option>
                                                            <option value="Monday">Monday</option>
                                                            <option value="Tuesday">Tuesday</option>
                                                            <option value="Wednesday">Wednesday</option>
                                                            <option value="Thursday">Thursday</option>
                                                            <option value="Friday">Friday</option>
                                                        </select>
                                                    </div>
                                                    <div class="invalid-feedback">Please select a day order</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="d-flex justify-content-center mt-4">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fas fa-save me-2"></i>
                                                Apply Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Move Special Attendance View here -->
                <div id="special-attendance-view" style="display: none;">
                    <div class="card shadow-lg rounded-4 border-0">
                        <!-- Header Section -->
                        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-4 rounded-top-4">
                            <div class="d-flex align-items-center">
                                <div class="header-icon-wrapper bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-check fa-lg text-white"></i>
                                </div>
                                <h5 class="mb-0">Special Attendance Management</h5>
                            </div>
                            <button id="back-to-admin-cards-special" class="btn btn-light btn-sm px-3 rounded-pill">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                        </div>

                        <div class="card-body p-4">
                            <!-- Special Attendance Form -->
                            <div class="special-attendance-form mb-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-calendar-plus me-2"></i>Mark Special Attendance
                                        </h6>
                                    </div>
                                    <form id="specialAttendanceForm" class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Date</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="fas fa-calendar text-primary"></i>
                                                </span>
                                                <input type="date" class="form-control border-start-0" id="specialAttendanceDate" onchange="fetchAndRenderStudents()" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Event Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="fas fa-bookmark text-primary"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0" id="eventName" required placeholder="Enter event name">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small text-muted">Select Periods</label>
                                            <div class="period-checkboxes-container bg-light p-3 rounded-3">
                                                <div class="d-flex gap-2 mb-3">
                                                    <button type="button" class="btn btn-sm btn-primary" id="selectAllPeriodsBtn">
                                                        <i class="fas fa-check-double me-2"></i>Select All
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary" id="clearPeriodsBtn">
                                                        <i class="fas fa-times me-2"></i>Clear All
                                                    </button>
                                                </div>
                                                <div class="period-checkboxes d-flex flex-wrap gap-3">
                                                    <!-- Periods will be dynamically added here -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small text-muted">Event Description</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="fas fa-align-left text-primary"></i>
                                                </span>
                                                <textarea class="form-control border-start-0" id="eventDescription" rows="3" required placeholder="Enter event description"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Mark Special Attendance
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Recent Special Attendance Records -->


                            <!-- Add this inside your special attendance form -->
                            <div class="col-12 mt-4">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-users me-2"></i>Students List
                                </h6>
                                <div id="studentsListContainer">
                                    <!-- Students list will be rendered here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Faculty Selection Template (hidden) -->
<template id="facultyTemplate">
    <div class="col-md-6 faculty-group mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="faculty-number mb-3 text-muted"></h6>
                <div class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <select class="form-select faculty-select" required>
                            <option value="">Select Faculty</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-info pick-students-btn">
                        <i class="fas fa-users me-2"></i>Pick Students
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Students Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Select Students</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3">
                    <button id="selectAllBtn" type="button" class="btn btn-primary">
                        <i class="fas fa-check-double"></i> Select All
                    </button>
                    <button id="selectFirstHalfBtn" type="button" class="btn btn-secondary">
                        <i class="fas fa-check"></i> Select First Half
                    </button>
                    <button id="resetSelectBtn" type="button" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reset Selection
                    </button>
                </div>
                <div id="studentCheckboxes" class="student-grid"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStudentsBtn">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Loader -->

<div id="loader" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px; color: white; text-align: center; z-index: 9999;">
    <p>Processing... Please wait.</p>
    <div class="spinner-border text-light" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<!--- holiday modal -->
<div class="modal fade" id="holidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Mark Holiday</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="holidayForm">
                    <div class="mb-3">
                        <label class="form-label">Select Date</label>
                        <input type="date" class="form-control" id="holidayDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Holiday Description</label>
                        <input type="text" class="form-control" id="holidayDescription"
                            placeholder="Enter holiday description" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="markHoliday()">
                    Mark as Holiday
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Add this before advisorHelper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="advisorHelper.js"></script>



<?php include __DIR__ . '/utils/ui/footermain.php'; ?>