<?php include __DIR__ . '/utils/ui/header.php';

$dept = $_SESSION['user']['dept'];
// Function to get unique batches

?>



<link rel="stylesheet" href="./utils/ui/style.css">
<!-- Page Content -->
<div class="container-fluid">
    <div class="tab-pane fade show active" id="create-course" role="tabpanel">
        <h2 class="form-title">Assign Advisor</h2>
        <form id="advisorMappingForm" method="POST">

            <div class="row g-4">
                <!-- Course Basic Info Section -->
                <div class="col-12">
                    <div class="form-section">
                        <h4 class="mb-3">Course Basic Information</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="batch" class="form-label">Batch</label>
                                    <select class="form-select" id="batch" name="batch" required>
                                        <option value="">Select Batch</option>
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academicYear" class="form-label">Academic Year</label>
                                    <select class="form-select" id="academic_year" name="academic_year" required>
                                        <option value="">Select Year</option>
                                        <option value="2021-22">2021-22</option>
                                        <option value="2022-23">2022-23</option>
                                        <option value="2023-24">2023-24</option>
                                        <option value="2024-25">2024-25</option>
                                        <option value="2025-26">2025-26</option>
                                        <option value="2026-27">2026-27</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select class="form-select" id="semester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="1">Semester 1</option>
                                        <option value="2">Semester 2</option>
                                        <option value="3">Semester 3</option>
                                        <option value="4">Semester 4</option>
                                        <option value="5">Semester 5</option>
                                        <option value="6">Semester 6</option>
                                        <option value="7">Semester 7</option>
                                        <option value="8">Semester 8</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Details Section -->
                <div class="col-12">
                    <div class="form-section">
                        <h4 class="mb-3">Course Details</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="section" class="form-label">Section</label>
                                    <select class="form-select" id="section" name="section" required>
                                        <option value="">Select Section</option>
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sem_start_date" class="form-label">Semester Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sem_end_date" class="form-label">Semester End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Section -->
                <div class="col-12">
                    <div class="form-section">
                        <h4 class="mb-3">Assign Faculty</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="faculty" class="form-label">Faculty</label>
                                    <select class="form-select" id="faculty" name="faculty_id" required>
                                        <option value="">Select Faculty</option>
                                        <!-- Options will be populated by hodHelper.js -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                        <button type="reset" class="btn btn-secondary px-4">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="hodHelper.js"></script>

    <script>
        // Replace the existing batch change event listener with this updated version
    </script>

</div>

<?php include __DIR__ . '/utils/ui/footermain.php'; ?>