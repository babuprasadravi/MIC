<?php include __DIR__ . '/utils/ui/header.php'; ?>
<?php include 'side.php'; ?>

<!-- Page Content -->
<div class="container-fluid">
    <div class="custom-tabs">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" id="time-table-tab" href="#add-bus" role="tab" aria-selected="true">
                    <span class="hidden-xs-down"><i class="fas fa-book tab-icon"></i><b> Advisor Dashboard</b></span>
                </a>
            </li>
        </ul>   
        <!-- Tab Content -->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="add-bus" role="tabpanel">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Department</th>
                                <th>Batch</th>
                                <th>Academic Year</th>
                                <th>Section</th>
                                <th>Semester</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
                                <th>Advisor Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows are dynamically populated by staffHelper.js from backend.php -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="advisorDashboard.js"></script>

<?php include __DIR__ . '/utils/ui/footermain.php'; ?>