<?php include __DIR__ . '/utils/ui/header.php'; ?>
<!-- Page Content -->
<div class="container-fluid">
<div class="custom-tabs">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" id="time-table-tab" href="#faculty-academics" role="tab" aria-selected="true">
                            <span class="hidden-xs-down"><i class="fas fa-book tab-icon"></i><b> Faculty Academics</b></span>
                        </a>
                    </li>
                </ul>
                <!-- Tab Content -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="faculty-academics" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Academic Year</th>
                                        <th>Semester Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Table data will go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
</div>

<script src="facultyDashboard.js"></script>
<?php include __DIR__ . '/utils/ui/footermain.php'; ?>