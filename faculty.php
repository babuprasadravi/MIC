<?php include __DIR__ . '/utils/ui/header.php'; ?>
<!-- Page Content -->
<div class="container-fluid">
    <div class="custom-tabs">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" id="time-table-tab" href="#timetableTab" role="tab" aria-selected="true">
                    <span class="hidden-xs-down"><i class="fas fa-book tab-icon"></i><b>Time Table</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="course-info-tab" href="#courseInfoTab" role="tab">
                    <span><i class="fas fa-calendar-alt"></i> Course Info</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="pending-attendance-tab" href="#pending-attendance" role="tab">
                    <span><i class="fas fa-clock"></i> Pending Attendance</span>
                </a>
            </li>
            <!-- <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="available-courses-tab" href="#hourAlterationTab" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-exchange-alt tab-icon"></i><b>Hour Alteration</b></span>
                </a>
            </li> -->
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Timetable Tab --->
            <div class="tab-pane fade show active" id="timetableTab" role="tabpanel">
                <div class="container-fluid py-4">
                    <div class="card shadow-sm">
                        <div id="timetableView">
                            <div class="card-body" id="timetableGrid">
                                <!-- Timetable will be populated by JavaScript -->
                            </div>
                        </div>

                        <div id="courseDetailsView" class="d-none">
                            <div class="card-body">
                                <div id="courseDetailsContent">
                                    <!-- Course details will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hour Alteration Tab -->
            <div class="tab-pane fade b" id="hourAlterationTab" role="tabpanel">
                <div class="container-fluid py-4">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="alterationTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="my-alterations-tab" data-bs-toggle="tab" data-bs-target="#myAlterations" type="button" role="tab" aria-controls="myAlterations" aria-selected="true">
                                My Hour Alteration Requests
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="incoming-alterations-tab" data-bs-toggle="tab" data-bs-target="#incomingAlterations" type="button" role="tab" aria-controls="incomingAlterations" aria-selected="false">
                                Incoming Alteration Requests
                            </button>
                        </li>
                    </ul>
                    <!-- Tab content -->
                    <div class="tab-content mt-3" id="alterationTabContent">
                        <!-- My Alteration Requests Tab -->
                        <div class="tab-pane fade show active" id="myAlterations" role="tabpanel" aria-labelledby="my-alterations-tab">
                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">My Hour Alteration Requests</h5>
                                    <button class="btn btn-primary btn-sm" onclick="showNewAlterationForm()">
                                        <i class="fas fa-plus me-2"></i>New Alteration Request
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Period</th>
                                                    <th>Course</th>
                                                    <th>Substitute Faculty</th>
                                                    <th>Reason</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="myAlterationsList">
                                                <!-- Outgoing requests will be dynamically loaded here via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Incoming Alteration Requests Tab -->
                        <div class="tab-pane fade" id="incomingAlterations" role="tabpanel" aria-labelledby="incoming-alterations-tab">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Incoming Alteration Requests</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Period</th>
                                                    <th>Course</th>
                                                    <th>Requested By</th>
                                                    <th>Reason</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="incomingAlterationsList">
                                                <!-- Incoming requests will be dynamically loaded here via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Tab Content -->
                </div><!-- End Container-fluid -->
            </div>
            <!-- Pending Attendance Tab -->
            <div class="tab-pane fade" id="pending-attendance" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Hour</th>
                                        <th>Course</th>
                                        <th>Batch</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Section</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingAttendanceList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Info Tab -->
            <div class="tab-pane fade" id="courseInfoTab" role="tabpanel">
                <!-- Courses Grid Container -->
                <div class="container-fluid py-4" id="courseInfoContent">
                    <input type="hidden" id="courseReportsCourseId" value="" />
                    <div class="row g-4" id="coursesGrid">
                        <!-- Course cards will be dynamically populated here -->
                    </div>
                </div>

                <!-- Student List View -->
                <div class="container-fluid py-4 d-none" id="studentListView">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h4 class="text-muted mb-0">Student List</h4>
                            <button class="btn btn-outline-primary btn-sm ms-auto" onclick="backToCourseReports()">
                                <i class="fas fa-arrow-left me-2"></i>Back to Reports
                            </button>
                        </div>
                    </div>
                    <div class="row g-4 py-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm student-list-table">
                                <div class="card-body p-4">
                                    <button id="downloadPDFBtn" class="btn btn-secondary" onclick="downloadStudentPDF()">
                                        <i class="fas fa-download"></i> Download PDF
                                    </button>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0" id="studentListTable">
                                            <thead class="bg-gradient-primary">
                                                <tr>
                                                    <th class="text-center small fw-medium py-3">S.No</th>
                                                    <th class="text-center small fw-medium py-3">Register Number</th>
                                                    <th class="text-center small fw-medium py-3">Name</th>
                                                    <th class="text-center small fw-medium py-3">Batch</th>
                                                </tr>
                                            </thead>
                                            <tbody class="border-top-0">
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted">
                                                        <i class="fas fa-users-slash fa-2x mb-3"></i>
                                                        <p class="mb-0">No students found in this batch</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="pdf-container">

                                        </div>
                                        <div id="paginationControls" class="pagination"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary View -->
                <div class="container-fluid py-4 d-none" id="attendanceSummaryView">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h4 class="text-muted mb-0">Attendance Summary</h4>
                            <button class="btn btn-outline-primary btn-sm ms-auto" onclick="backToCourseReports()">
                                <i class="fas fa-arrow-left me-2"></i>Back to Reports
                            </button>
                        </div>
                    </div>
                    <div class="row g-4 py-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm attendance-summary-table">
                                <div class="card-body p-4">
                                <button id="downloadPDFAttendaceSummary" onclick="downloadAttendaceSummary()" class="btn btn-secondary">
                                    <i class="fas fa-download"></i> Download PDF
                                </button>
                                    <div class="table-responsive">
                                        <table id="attendanceSummaryTable" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>S.no</th>
                                                    <th>Class Date</th>
                                                    <th>Day</th>
                                                    <th>Hour</th>
                                                    <th>Leave</th>
                                                    <th>Absent</th>
                                                    <th>OD</th>
                                                    <th>Description</th>
                                                    <th>Total students</th>
                                                </tr>
                                            </thead>
                                        </table>




                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Percentage View -->
                <div class="container-fluid py-4 d-none" id="attendancePercentageView">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h4 class="text-muted mb-0">Attendance Percentage</h4>
                            <button class="btn btn-outline-primary btn-sm ms-auto" onclick="backToCourseReports()">
                                <i class="fas fa-arrow-left me-2"></i>Back to Reports
                            </button>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm attendance-percentage-table">
                <div class="pdf-container text-start">
                    <button id="downloadPDFAttendace" onclick="downloadAttendacePercentage()" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Download PDF
                    </button>
  </div>
  <div class="card-body p-4">
    <div class="table-responsive">
      <table id="attendancePercentageTable" class="table table-striped table-hover align-middle mb-0">
        <thead class="bg-gradient-info">
          <tr>
            <th class="text-center small fw-medium py-3">S.No</th>
            <th class="text-center small fw-medium py-3">Register No</th>
            <th class="text-center small fw-medium py-3">Name</th>
            <th class="text-center small fw-medium py-3">Total Hours</th>
            <th class="text-center small fw-medium py-3">Present</th>
            <th class="text-center small fw-medium py-3">Attendance %</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="fas fa-percentage fa-2x mb-3"></i>
              <p class="mb-0">No attendance percentage data available</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

                </div>

                <!-- Course Reports View -->
                <div class="container-fluid py-4 d-none" id="courseReportsView">
                    <div class="row mb-4">
                        <input type="hidden" id="displayCourseId">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h4 class="text-muted mb-0">Course Reports</h4>
                            <button class="btn btn-outline-primary btn-sm ms-auto" onclick="backToCourseInfo()">
                                <i class="fas fa-arrow-left me-2"></i>Back to Courses
                            </button>
                        </div>
                    </div>
                    <div class="row g-4 py-4">
                        <!-- Student List Card -->
                        <div class="col-md-4">
                            <div class="admin-card student-list-card">
                                <div class="admin-card-header">
                                    <div class="admin-header-bg admin-gradient-primary"></div>
                                    <div class="position-relative d-flex align-items-center gap-3">
                                        <div class="admin-icon-wrapper">
                                            <i class="fas fa-users fa-lg text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="admin-text-white mb-1">Student List</h5>
                                            <p class="admin-text-white opacity-75 mb-0 small">View enrolled students</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-card-content">
                                    <button class="btn btn-primary w-100" id="viewStudentListBtn" onclick="showStudentListView()">
                                        <i class="fas fa-list me-2"></i>View List
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Summary Card -->
                        <div class="col-md-4">
                            <div class="admin-card attendance-summary-card">
                                <div class="admin-card-header">
                                    <div class="admin-header-bg admin-gradient-success"></div>
                                    <div class="position-relative d-flex align-items-center gap-3">
                                        <div class="admin-icon-wrapper">
                                            <i class="fas fa-chart-bar fa-lg text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="admin-text-white mb-1">Attendance Summary</h5>
                                            <p class="admin-text-white opacity-75 mb-0 small">View attendance overview</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-card-content">
                                    <button class="btn btn-success w-100" id="viewAttendanceSummaryBtn" onclick="showAttendanceSummaryView()">
                                        <i class="fas fa-chart-line me-2"></i>View Summary
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Student Attendance Percentage Card -->
                        <div class="col-md-4">
                            <div class="admin-card percentage-card">
                                <div class="admin-card-header">
                                    <div class="admin-header-bg admin-gradient-info"></div>
                                    <div class="position-relative d-flex align-items-center gap-3">
                                        <div class="admin-icon-wrapper">
                                            <i class="fas fa-percentage fa-lg text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="admin-text-white mb-1">Attendance Percentage</h5>
                                            <p class="admin-text-white opacity-75 mb-0 small">View student percentages</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="admin-card-content">
                                    <button class="btn btn-info w-100" id="viewAttendancePercentageBtn" onclick="showAttendancePercentageView()">
                                        <i class="fas fa-calculator me-2"></i>View Percentages
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Marks View -->
                <div class="container-fluid py-4 d-none" id="marksView">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-gradient-info py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" id="viewCoAttainmentBtn">
                                        <i class="fas fa-chart-line me-2"></i>View CO Attainment Report
                                    </button>
                                    <button class="btn btn-success btn-sm" id="viewCoPoAttainmentBtn">
                                        <i class="fas fa-chart-bar me-2"></i>View CO Attainment Report After CQI
                                    </button>
                                    <button class="btn btn-warning btn-sm" id="viewInternalMarksBtn">
                                        <i class="fas fa-chart-line me-2"></i>Internal Marks
                                    </button>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success btn-sm" id="createCqiBtn" data-bs-toggle="modal" data-bs-target="#cqiTestModal">
                                        <i class="fas fa-plus me-2"></i>Create CQI Test
                                    </button>
                                    <button id="addComponentBtn" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#componentModal">
                                        <i class="fas fa-plus me-2"></i>Add Component
                                    </button>
                                    <button class="btn btn-light btn-sm" onclick="backToCourseInfo()">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Courses
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="row" id="componentsContainer">
                                    <!-- Components will be dynamically added here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Component Card Template -->
                <template id="componentCardTemplate">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card component-card h-100">
                            <div class="card-header border-0 bg-gradient-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title mb-0"></h5>
                                    <span class="badge status-badge"></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="component-details">
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <strong>Exam Date:</strong>
                                        </div>
                                        <div class="col-6 text-end">
                                            <span class="exam-date"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Conducted Marks:</strong>
                                        </div>
                                        <div class="col-6 text-end">
                                            <span class="conducted-marks"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="component-type-badge"></span>
                                        <div class="action-buttons">
                                            <!-- Buttons will be added dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<!-- Reject Request Modal for Incoming Requests -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Hour Alteration Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reject_request_id" name="request_id">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Reason for Rejection:</label>
                        <textarea id="reject_reason" name="reject_reason" class="form-control" placeholder="Enter reason ..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Submit Rejection</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- mark entry modal -->
<div class="modal fade" id="markEntryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter Marks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" id="downloadMarksTemplate">
                                <i class="fas fa-download"></i> Download Template
                            </button>
                            <button class="btn btn-outline-success" id="uploadMarks">
                                <i class="fas fa-upload"></i> Upload Marks
                            </button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="autoCalculate" checked>
                            <label class="form-check-label" for="autoCalculate">
                                Auto Calculate Total
                            </label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="marksTable">
                        <thead>
                            <tr>
                                <th>Register No</th>
                                <th>Student Name</th>
                                <!-- Question columns will be added dynamically -->
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Student rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- File upload input (hidden) -->
                <input type="file" id="marksFile" accept=".xlsx,.xls,.csv" class="d-none">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveMarks">Save Marks</button>
            </div>
        </div>
    </div>
</div>
<!-- Add Component Modal -->
<div class="modal fade" id="componentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Component</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="componentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Component Name</label>
                        <select class="form-select" name="component_name" required>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Component Type</label>
                        <select class="form-select" name="component_type" required>
                            <option value="internal">Internal</option>
                            <option value="external">External</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conducted Marks</label>
                        <input type="number" class="form-control" name="conducted_marks" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Exam Date</label>
                        <input type="date" class="form-control" name="exam_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Component</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create Question Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="templateForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_template">
                    <input type="hidden" name="component_id" id="templateComponentId">

                    <div id="questionsContainer">
                        <!-- Questions will be added here dynamically -->
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>Questions</div>
                        <button type="button" class="btn btn-primary" id="addQuestionBtn">
                            + Add Question
                        </button>
                    </div>

                    <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center">
                        <div>Total Marks: <span id="totalMarks" class="fw-bold">0.00</span></div>
                        <div>Required Marks: <span id="requiredMarks" class="fw-bold">60.00</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create CQI Test Modal -->
<div class="modal fade" id="cqiTestModal" tabindex="-1" aria-labelledby="cqiTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>CQI Analysis - Students Below Target
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Students listed below have not attained the minimum requirement of 58% in one or more COs.
                </div>

                <!-- Summary Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Students</h6>
                                <h3 id="totalStudentsCount">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning bg-opacity-25">
                            <div class="card-body text-center">
                                <h6 class="card-title">Students Below Target</h6>
                                <h3 id="belowTargetCount">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info bg-opacity-25">
                            <div class="card-body text-center">
                                <h6 class="card-title">Most Affected CO</h6>
                                <h3 id="mostAffectedCO">-</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success bg-opacity-25">
                            <div class="card-body text-center">
                                <h6 class="card-title">Target Achievement</h6>
                                <h3 id="targetAchievement">0%</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="cqiStudentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Register No</th>
                                <th>Student Name</th>
                                <th>Unattained COs</th>
                                <th>Current Score</th>
                                <th>Required Score</th>
                                <th>Gap</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="createCqiTemplateBtn">Create CQI Template</button>
            </div>
        </div>
    </div>
</div>

<!-- CQI Template Modal -->
<div class="modal fade" id="cqiTemplateModal" tabindex="-1" aria-labelledby="cqiTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cqiTemplateModalLabel">Create CQI Template</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cqiTemplateForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_template">
                    <input type="hidden" name="component_id" id="cqiTemplateComponentId">
                    <input type="hidden" name="is_cqi" value="1">

                    <div id="cqiQuestionsContainer">
                        <!-- Questions will be added here dynamically -->
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>Questions</div>
                        <button type="button" class="btn btn-primary" id="cqiAddQuestionBtn">
                            + Add Question
                        </button>
                    </div>

                    <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center">
                        <div>Total Marks: <span id="cqiTotalMarks" class="fw-bold">0.00</span></div>
                        <div>Required Marks: <span id="cqiRequiredMarks" class="fw-bold">100.00</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CO Attainment Report Modal -->
<div class="modal fade" id="coAttainmentModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Internal Assessment Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- College and Department Headers -->
                    <div class="text-center mb-4">
                        <h4>M.Kumarasamy College of Engineering</h4>
                        <h5>Department of <span id="deptName"></span></h5>
                        <h6>Internal Assessment Report</h6>
                    </div>

                    <!-- Subject and Semester Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-start">
                                <strong>Year & sem : </strong><span id="yearSem" class="ms-2"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <strong>Subject : </strong><span id="subjectCode" class="ms-2"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Attainment Level Info Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm" id="CorelationTable">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Description</th>
                                    <th>Correlation level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>60% and above students scoring more than 50% of maximum marks in the relevant COs</td>
                                    <td>Low (1)</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>65% and above students scoring more than 50% of maximum marks in the relevant COs</td>
                                    <td>Medium (2)</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>70% and above students scoring more than 50% of maximum marks in the relevant COs</td>
                                    <td>High (3)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Marks Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="COMarkID">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Register Number</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive mt-4">
                            <table class="table table-bordered table-sm" id="attainmentTable">
                                <thead>
                                    <tr></tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                    </div>      
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button class="btn btn-primary" onclick="downloadReport()">
                    <i class="fas fa-download"></i> Download Report
                </button> -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- CQI Attainment Report Modal -->
<div class="modal fade" id="cqiAttainmentModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>CO-PO Attainment Report with CQI Improvements
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- College and Department Headers -->
                    <div class="text-center mb-4">
                        <h4>M.Kumarasamy College of Engineering</h4>
                        <h5>Department of <span id="deptNameCQI"></span></h5>
                        <h6>CO-PO Attainment Report with CQI</h6>
                    </div>

                    <!-- Course Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-start">
                                <strong>Year & sem : </strong><span id="yearSemCQI" class="ms-2"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <strong>Subject : </strong><span id="subjectCodeCQI" class="ms-2"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container mr-3">
                                            <h4 class="card-value mb-0" id="totalStudentsCQI"></h4>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0">Total Students</h6>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container mr-3">
                                        <h4 class="card-value mb-0" id="improvedStudentsCQI"></h4>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0">Improved Students</h6>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container mr-3">
                                        <h4 class="card-value mb-0" id="belowTargetAfterCQI"></h4>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0">Below Target After CQI</h6>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container mr-3">
                                        <h4 class="card-value mb-0" id="overallImprovement"></h4>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-0">Overall Improvement</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attainment Tables -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Original Attainment</h5>
                            <table class="table table-bordered" id="originalAttainmentTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>CO</th>
                                        <th>Score</th>
                                        <th>Gap</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Improved Attainment</h5>
                            <table class="table table-bordered" id="improvedAttainmentTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>CO</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Student Details Table -->
                    <div class="table-responsive mt-4">
                        <h5>Student-wise Analysis</h5>
                        <table class="table table-bordered table-hover" id="studentCQITable">
                            <thead class="table-light">
                                <tr>
                                    <th>Register No</th>
                                    <th>Student Name</th>
                                    <th>Original COs</th>
                                    <th>CQI Marks</th>
                                    <th>Improved COs</th>
                                    <th>Improvement</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button class="btn btn-success" onclick="downloadCQIReport()">
                    <i class="fas fa-download"></i> Download Report
                </button> -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Internal Marks Modal -->
<div class="modal fade" id="internalMarksModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Internal Marks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- College and Department Headers -->
                    <div class="text-center mb-4">
                        <h4>M.Kumarasamy College of Engineering</h4>
                        <h5>Department of <span id="deptNameInternal"></span></h5>
                        <h6>Internal Assessment Report</h6>
                    </div>

                    <!-- Subject and Semester Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-start">
                                <strong>Year & sem : </strong><span id="yearSemInternal" class="ms-2"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <strong>Subject : </strong><span id="subjectCodeInternal" class="ms-2"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Marks Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="internalMarksTable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Register Number</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button class="btn btn-primary" onclick="downloadInternalMarks()">
                    <i class="fas fa-download"></i> Download Report
                </button> -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="facultyHelper.js"></script>
<style>
    /* Update existing styles */
    .bg-gradient-info {
        background: linear-gradient(45deg, #36b9cc, #258391) !important;
    }

    #marksView .btn {
        font-weight: 500;
        letter-spacing: 0.3px;
        transition: all 0.2s ease;
    }

    #marksView .btn-light {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
    }

    #marksView .btn-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        color: white;
    }

    #marksView .btn-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
        border: none;
        color: white;
    }

    #marksView .btn-warning {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        border: none;
        color: white;
    }

    #marksView .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    #marksView .admin-icon-wrapper {
        background: rgba(54, 185, 204, 0.1);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #marksView .text-info {
        color: #36b9cc !important;
    }
</style>

<style>
    .course-card {
        border: 1px solid #e9ecef !important;
        /* Light border color */
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .course-card-header {
        background-color: #f8f9fa;
        /* Light header background */
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .course-card:hover {
        border-color: #ced4da;
        /* Slightly darker border on hover */
    }

    @media (min-width: 1200px) {
        .course-card {
            width: 100%;
            max-width: 400px !important;
        }
    }

    .summary-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 120px;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }

    .summary-card .card-title {
        color: #6c757d;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .summary-card .card-value {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .icon-container {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<?php include __DIR__ . '/utils/ui/footermain.php'; ?>