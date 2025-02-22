<?php include __DIR__ . '/utils/ui/header.php'; ?>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Apply OD & Leave</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <a href="#" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-calendar"></i> View Timetable
                        </a>
                    </div>
                </div>
            

                    <!-- Updated Form Inputs -->
                    <form id="leaveForm">
                        <div class="row g-3">
                        <div class="col-md-6">
                                <label class="form-label">Student ID:</label>
                                <input type="text" class="form-control" name="student_id" disabled>
                            </div>
                            <div class="col-md-6">

                                <label class="form-label">Type:</label>


                                <select class="form-select" name="leave_type" required>
                                    <option value="">None</option>
                                    <option>OD</option>
                                    <option>Leave</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description/Reason:</label>
                                <input type="text" class="form-control" name="reason" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Starting Date:</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ending Date:</label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hour(s):</label>
                                <select class="form-select" name="periods" required>
                                    <option value="">None</option>
                                    <option>Full Day(1,2,3,4,5,6,7)</option>
                                    <option>Full Day(1,2,3,4,5,6,7,8)</option>
                                    <option>Full Day(1,2,3,4,5,6,7,8,9)</option>
                                    <option>F.N.(1,2,3)</option>
                                    <option>F.N.(1,2,3,4)</option>
                                    <option>F.N.(1,2,3,4,5)</option>
                                    <option>A.N.(4,5,6,7)</option>
                                    <option>A.N.(5,6,7)</option>
                                    <option>A.N.(5,6,7,8)</option>
                                    <option>A.N.(5,6,7,8,9)</option>
                                </select>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                    <div class="card mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Leave & OD History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="leaveHistoryTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="studentHelper.js"></script>
<?php include __DIR__ . '/utils/ui/footermain.php'; ?>