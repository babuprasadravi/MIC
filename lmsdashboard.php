<?php include __DIR__ . '/utils/ui/header.php'; ?>
<!-- Page Content -->

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="reviewModalLabel">Course Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Course Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Course Name:</label>
                        <p id="modalCourseName" class="border rounded p-2 bg-light"></p>
                    </div>

                </div>
                <!-- <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Syllabus:</label>
                                <p id="modalSyllabus" class="border rounded p-2 bg-light"></p>
                            </div>
                        </div> -->

                <!-- Units and Topics will be dynamically inserted here -->
                <div id="unitsContainer"></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Course Video Modal -->
<div class="modal fade" id="courseVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Video</h5>
                <button type="button" class="btn-close" onclick="closeVideoModal()"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe id="courseVideoFrame" src="" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeVideoModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <input type="hidden" id="rejectCourseId">
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectReason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this toast container instead -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
</div>

<div class="container-fluid">

    <div class="custom-tabs">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist">

            <li class="nav-item" role="presentation">
                <a class="nav-link active " data-bs-toggle="tab" id="lesson-plan-edit-request-tab" href="#lesson-plan-edit-request" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-book tab-icon"></i><b> Lesson Plan Edit Request</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="attendance-approval-tab" href="#attendance-approval" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-graduation-cap tab-icon"></i><b> Pending Attendance Approvals</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" id="time-table-approval-tab" href="#time-table-approval" role="tab" aria-selected="false">
                    <span class="hidden-xs-down"><i class="fas fa-graduation-cap tab-icon"></i><b> TimeTable Edit Request</b></span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link " data-bs-toggle="tab" id="lms-approval-request-tab" href="#lms-approval-request" role="tab" aria-selected="true">
                    <span class="hidden-xs-down"><i class="fas fa-graduation-cap tab-icon"></i><b>LMS Approval Request</b></span>
                </a>
            </li>

        </ul>
        <!-- Tab Content -->
        <div class="tab-content">

            <!-- Lesson Plan Edit Request Tab -->
            <div class="tab-pane fade  show active" id="lesson-plan-edit-request" role="tabpanel">
                <div class="row g-4 justify-content-center ">
                    <div id="lesson-plan-edit-request-container" class="col-lg-12">
                    </div>
                </div>


            </div>
            <!-- Attendance Approval Tab -->
            <div class="tab-pane fade" id="attendance-approval" role="tabpanel">
                <div class="row g-4 justify-content-center">
                    <div id="attendance-locked-request-container" class="col-lg-12">
                    </div>
                </div>
            </div>
            <!-- Time Table Approval Tab -->
            <div class="tab-pane fade" id="time-table-approval" role="tabpanel">
                <div class="row g-4 justify-content-center">
                    <div id="time-table-edit-request-container" class="col-lg-12">
                    </div>
                </div>
            </div>
            <!-- LMS Approval Request Tab -->
            <div class="tab-pane fade" id="lms-approval-request" role="tabpanel">
                <div class="row g-4 justify-content-center">
                    <div id="lms-approval-request-container" class="col-lg-12">
                        <!-- Cards will be dynamically inserted here -->
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="lmsdashboard.js"></script>
<?php include __DIR__ . '/utils/ui/footermain.php'; ?>