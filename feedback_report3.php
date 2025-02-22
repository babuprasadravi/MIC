<?php
require_once 'feed_db_connection.php';
require_once 'config.php';

// Include your existing functions here
// calculateSubjectSummaryData() and other functions remain the same

function calculateSubjectSummaryData($data) {
    // Keep existing summary calculation function
    $summary = [];
    foreach ($data as $subject_name => $subject_data) {
        $summary[$subject_name] = [];
        $totalStudents = count($subject_data['student_responses']);

        foreach ($subject_data['student_responses'] as $student_info) {
            foreach ($student_info['responses'] as $coIndex => $response) {
                if (!isset($summary[$subject_name][$coIndex])) {
                    $summary[$subject_name][$coIndex] = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0];
                }
                if (in_array($response, [1, 2, 3, 4])) {
                    $summary[$subject_name][$coIndex][$response]++;
                    $summary[$subject_name][$coIndex]['total']++;
                }
            }
        }

        foreach ($summary[$subject_name] as &$coData) {
            foreach ($coData as $rating => &$count) {
                if ($rating !== 'total') {
                    $percentage = ($count / $coData['total']) * 100;
                    $count = [$count, round($percentage, 2)];
                }
            }
        }

        $summary[$subject_name]['totalStudents'] = $totalStudents;
    }
    return $summary;
}

function generateReport($feedback_id, $subject_id)
{
    global $fconn;
    $data = [];

    // First, let's get the subjects and their question counts
    $subject_query = "
        SELECT DISTINCT subj.subject_name, COUNT(DISTINCT q.id) as question_count
        FROM feedback_subjects fs
        JOIN subjects subj ON fs.subject_id = subj.id
        LEFT JOIN questions q ON q.subject_id = subj.id
        WHERE fs.feedback_id = ? AND fs.subject_id = ?
        GROUP BY subj.subject_name
        ORDER BY subj.subject_name
    ";
    $stmt = mysqli_prepare($fconn, $subject_query);
    mysqli_stmt_bind_param($stmt, "ii", $feedback_id, $subject_id);
    mysqli_stmt_execute($stmt);
    $subject_result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($subject_result)) {
        $subject_name = $row['subject_name'];
        $question_count = $row['question_count'];
        $data[$subject_name] = [
            'student_responses' => [],
            'column_indices' => [],
            'question_count' => $question_count
        ];
    }

    $report_query = "
        SELECT
            sr.student_id,
            s.sname AS student_name,
            s.dept AS student_department,
            subj.subject_name,
            q.id AS question_id,
            sr.response
        FROM feedback_subjects fs
        JOIN subjects subj ON fs.subject_id = subj.id
        JOIN questions q ON q.subject_id = subj.id
        JOIN student_responses sr ON sr.question_id = q.id AND sr.feedback_id = fs.feedback_id
        JOIN mic.student s ON sr.student_id = s.sid
        WHERE fs.feedback_id = ? AND fs.subject_id = ?
        ORDER BY s.sname, subj.subject_name, q.id
    ";
    $stmt = mysqli_prepare($fconn, $report_query);
    mysqli_stmt_bind_param($stmt, "ii", $feedback_id, $subject_id);
    mysqli_stmt_execute($stmt);
    $report_result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($report_result)) {
        $student_id = $row['student_id'];
        $student_name = $row['student_name'];
        $student_department = $row['student_department'];
        $subject_name = $row['subject_name'];
        $question_id = $row['question_id'];

        if (!isset($data[$subject_name]['column_indices'][$question_id])) {
            $data[$subject_name]['column_indices'][$question_id] = count($data[$subject_name]['column_indices']);
        }
        $column_index = $data[$subject_name]['column_indices'][$question_id];

        if (!isset($data[$subject_name]['student_responses'][$student_id])) {
            $data[$subject_name]['student_responses'][$student_id] = [
                'student_name' => $student_name,
                'student_department' => $student_department,
                'responses' => array_fill(0, $data[$subject_name]['question_count'], '')
            ];
        }
        $data[$subject_name]['student_responses'][$student_id]['responses'][$column_index] = $row['response'];
    }

    $max_columns = max(array_map(function($subject) {
        return $subject['question_count'];
    }, $data));

    return [
        'data' => $data,
        'subjectSummary' => calculateSubjectSummaryData($data),
        'max_columns' => $max_columns
    ];
}

function generateBatchReport($batch, $subject_id) {
    global $fconn;
    $data = [];

    // Get subject details
    $subject_query = "
        SELECT DISTINCT s.subject_name, COUNT(DISTINCT q.id) as question_count
        FROM subjects s
        LEFT JOIN questions q ON q.subject_id = s.id
        WHERE s.id = ?
        GROUP BY s.subject_name
    ";
    $stmt = mysqli_prepare($fconn, $subject_query);
    mysqli_stmt_bind_param($stmt, "i", $subject_id);
    mysqli_stmt_execute($stmt);
    $subject_result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($subject_result)) {
        $subject_name = $row['subject_name'];
        $question_count = $row['question_count'];
        $data[$subject_name] = [
            'student_responses' => [],
            'column_indices' => [],
            'question_count' => $question_count
        ];
    }

    // Get student responses for the specific batch and subject
    $report_query = "
        SELECT 
            sr.student_id,
            sr.batch,
            s.sname AS student_name,
            s.dept AS student_department,
            subj.subject_name,
            q.id AS question_id,
            sr.response
        FROM student_responses sr
        JOIN mic.student s ON sr.student_id = s.sid
        JOIN subjects subj ON sr.subject_id = subj.id
        JOIN questions q ON q.subject_id = subj.id
        WHERE sr.batch = ? AND sr.subject_id = ?
        ORDER BY s.sname, subj.subject_name, q.id
    ";
    
    $stmt = mysqli_prepare($fconn, $report_query);
    mysqli_stmt_bind_param($stmt, "si", $batch, $subject_id);
    mysqli_stmt_execute($stmt);
    $report_result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($report_result)) {
        $student_id = $row['student_id'];
        $student_name = $row['student_name'];
        $student_department = $row['student_department'];
        $subject_name = $row['subject_name'];
        $question_id = $row['question_id'];

        if (!isset($data[$subject_name]['column_indices'][$question_id])) {
            $data[$subject_name]['column_indices'][$question_id] = count($data[$subject_name]['column_indices']);
        }
        $column_index = $data[$subject_name]['column_indices'][$question_id];

        if (!isset($data[$subject_name]['student_responses'][$student_id])) {
            $data[$subject_name]['student_responses'][$student_id] = [
                'student_name' => $student_name,
                'student_department' => $student_department,
                'responses' => array_fill(0, $data[$subject_name]['question_count'], '')
            ];
        }
        $data[$subject_name]['student_responses'][$student_id]['responses'][$column_index] = $row['response'];
    }

    $max_columns = max(array_map(function($subject) {
        return $subject['question_count'];
    }, $data));

    return [
        'data' => $data,
        'subjectSummary' => calculateSubjectSummaryData($data),
        'max_columns' => $max_columns
    ];
}

//feedbackwise ajax
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'generate_report') {
        $feedback_id = $_POST['feedback_id'];
        $subject_id = $_POST['subject_id'];

        $report = generateReport($feedback_id, $subject_id);

        header('Content-Type: application/json');
        echo json_encode($report);
        exit;
    }
}

// subject report ajax
// Ajax handlers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'get_batches':
                // Fetch distinct batches from student_responses table
                $batch_query = "
                    SELECT DISTINCT batch 
                    FROM student_responses 
                    WHERE batch IS NOT NULL 
                    ORDER BY batch DESC
                ";
                $result = mysqli_query($fconn, $batch_query);
                $batches = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $batches[] = $row['batch'];
                }
                header('Content-Type: application/json');
                echo json_encode($batches);
                break;

            case 'get_subjects':
                $batch = $_POST['batch'];
                // Fetch subjects for the selected batch
                $subject_query = "
                    SELECT DISTINCT subj.id, subj.subject_name
                    FROM student_responses sr
                    JOIN subjects subj ON sr.subject_id = subj.id
                    WHERE sr.batch = ?
                    ORDER BY subj.subject_name
                ";
                $stmt = mysqli_prepare($fconn, $subject_query);
                mysqli_stmt_bind_param($stmt, "s", $batch);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $subjects = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $subjects[] = [
                        'id' => $row['id'],
                        'name' => $row['subject_name']
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($subjects);
                break;

            case 'generate_report_sub':
                $batch = $_POST['batch'];
                $subject_id = $_POST['subject_id'];
                
                $report = generateBatchReport($batch, $subject_id);
                
                header('Content-Type: application/json');
                echo json_encode($report);
                break;
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Reports</title>
    <style>
        .download-btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .summary-table {
            margin-bottom: 20px;
        }
        .tab-content {
            padding: 20px 0;
        }
        .nav-tabs {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="feedback-tab" data-bs-toggle="tab" href="#feedback-report" role="tab">
                    Feedback-wise Report
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="feedback-tab" data-bs-toggle="tab" href="#batch-report" role="tab">
                    Subject-wise Report
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabsContent">
            <!-- Feedback-wise Report Tab -->
            <div class="tab-pane fade show active" id="feedback-report" role="tabpanel">
                <form id="feedback-form" class="mb-4">
                    <div class="form-group">
                        <label for="feedback_id"class="form-label">Select Feedback:</label>
                        <select class="select2 form-control form-select" id="feedback_id" name="feedback_id">
                            <option value="">Select a Feedback</option>
                            <?php
                            $feedback_query = "SELECT id, feedback_name FROM feedbacks where `sid`='$s'";
                            $feedback_result = mysqli_query($fconn, $feedback_query);
                            while ($row = mysqli_fetch_assoc($feedback_result)) {
                                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['feedback_name']) . "</option>";
                            }
                            ?>
                        </select>

                        <label for="feedback_subject_id" class="form-label mt-3">Select Subject:</label>
                        <select class="select2 form-control form-select" id="feedback_subject_id" name="feedback_subject_id">
                            <option value="">Select a Subject</option>
                        </select>
                    </div>
                    <button type="submit"  class="mt-3 btn btn-primary"><i class="fas fa-filter"></i> Generate Feedback Report</button>
                </form>

                <div id="feedback-loader" style="display: none; text-align: center;">
                    <p>Fetching feedback report, please wait...</p>
                </div>

                <div id="feedback-report-container" style="display:none;">
                    <h2>Detailed Feedback Report</h2>
                    <div id="feedback-download-buttons">
                        <button onclick="downloadFeedbackCSV()" class="btn btn-success download-btn">Download CSV</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="feedback-report-table">
                        <thead class="gradient-header">
                                <tr id="feedback-report-thead"></tr>
                            </thead>
                            <tbody id="feedback-report-tbody"></tbody>
                        </table>
                    </div>

                    <div id="feedback-summary-container">
                        <h2 class="mt-5">Feedback Summary Report</h2>
                        <div id="feedback-summary-download-buttons">
                            <button onclick="downloadFeedbackSummaryCSV()" class="btn btn-success download-btn">
                                Download Summary CSV
                            </button>
                        </div>
                        <div id="feedback-summary-tables"></div>
                    </div>
                </div>
            </div>

            <!-- Batch-wise Report Tab -->
            <div class="tab-pane fade" id="batch-report" role="tabpanel">
                <form id="batch-form" class="mb-4">
                    <div class="form-group">
                        <label for="batch"class="form-label">Select Batch:</label>
                        <select class="select2 form-control form-select" id="batch" name="batch" required>
                            <option value="">Select a Batch</option>
                        </select>

                        <label for="batch_subject_id" class="form-label mt-3">Select Subject:</label>
                        <select class="select2 form-control form-select" id="batch_subject_id" name="batch_subject_id" required>
                            <option value="">Select a Subject</option>
                        </select>
                    </div>
                    <button type="submit" class="mt-3 btn btn-primary"><i class="fas fa-filter"></i> Generate Batch Report</button>
                </form>

                <div id="batch-loader" style="display: none; text-align: center;">
                    <p>Fetching batch report, please wait...</p>
                </div>

                <div id="batch-report-container" style="display:none;">
                    <h2>Detailed Batch Report</h2>
                    <div id="batch-download-buttons">
                        <button onclick="downloadBatchCSV()" class="btn btn-success download-btn">Download CSV</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="batch-report-table">
                        <thead class="gradient-header">
                                <tr id="batch-report-thead"></tr>
                            </thead>
                            <tbody id="batch-report-tbody"></tbody>
                        </table>
                    </div>

                    <div id="batch-summary-container">
                        <h2 class="mt-5">Batch Summary Report</h2>
                        <div id="batch-summary-download-buttons">
                            <button onclick="downloadBatchSummaryCSV()" class="btn btn-success download-btn">
                                Download Summary CSV
                            </button>
                        </div>
                        <div id="batch-summary-tables"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Load batches on page load
        $.ajax({
            url: 'feedback_report3.php',
            type: 'POST',
            data: { action: 'get_batches' },
            success: function(batches) {
                let batchSelect = $('#batch');
                batches.forEach(function(batch) {
                    batchSelect.append($('<option>', {
                        value: batch,
                        text: batch
                    }));
                });
            }
        });

        // Feedback-wise report handlers
        $('#feedback_id').change(function() {
            var feedbackId = $(this).val();
            if (feedbackId) {
                $.ajax({
                    url: 'feedback_management.php',
                    type: 'POST',
                    data: {
                        feedback_id: feedbackId,
                        action: 'fetch_subjects'
                    },
                    success: function(data) {
                        $('#feedback_subject_id').html(data);
                    }
                });
            } else {
                $('#feedback_subject_id').html('<option value="">Select a Subject</option>');
            }
        });

        // Batch-wise report handlers
        $('#batch').change(function() {
            let batch = $(this).val();
            if (batch) {
                $.ajax({
                    url: 'feedback_report3.php',
                    type: 'POST',
                    data: {
                        action: 'get_subjects',
                        batch: batch
                    },
                    success: function(subjects) {
                        let subjectSelect = $('#batch_subject_id');
                        subjectSelect.empty().append('<option value="">Select a Subject</option>');
                        subjects.forEach(function(subject) {
                            subjectSelect.append($('<option>', {
                                value: subject.id,
                                text: subject.name
                            }));
                        });
                    }
                });
            } else {
                $('#batch_subject_id').html('<option value="">Select a Subject</option>');
            }
        });

        // Form submission handlers
        $('#feedback-form').submit(function(e) {
            e.preventDefault();
            let feedbackId = $('#feedback_id').val();
            let subjectId = $('#feedback_subject_id').val();

            if (!feedbackId || !subjectId) {
                alert('Please select both feedback and subject.');
                return;
            }

            $('#feedback-loader').show();
            $('#feedback-report-container').hide();

            if ($.fn.DataTable.isDataTable('#feedback-report-table')) {
                $('#feedback-report-table').DataTable().destroy();
            }

            generateFeedbackReport(feedbackId, subjectId);
        });

        $('#batch-form').submit(function(e) {
            e.preventDefault();
            let batch = $('#batch').val();
            let subjectId = $('#batch_subject_id').val();

            if (!batch || !subjectId) {
                alert('Please select both batch and subject.');
                return;
            }

            $('#batch-loader').show();
            $('#batch-report-container').hide();

            if ($.fn.DataTable.isDataTable('#batch-report-table')) {
                $('#batch-report-table').DataTable().destroy();
            }

            generateBatchReport(batch, subjectId);
        });

        // Initialize DataTables and handle tab changes
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });

function downloadFeedbackCSV() {
    var table = $('#feedback-report-table').DataTable(); // Get the DataTable instance
    var data = table.rows({ search: 'applied' }).data(); // Get all rows, including filtered data

    // Start the CSV with the dynamically generated headers
    var csv = 'Student ID,Student Name,Department,Subject';
    $('#feedback-report-table thead th').each(function(index) {
        if (index >= 4) { // Skip the first 4 columns (Student ID, Student Name, Department, Subject)
            var columnText = $(this).text(); // Get column header text (e.g., Q1, Q2, Q3)
            csv += ',' + columnText;
        }
    });
    csv += '\n'; // Add newline after the header row

    // Loop through each row of data and convert to CSV format
    data.each(function(row) {
        csv += row.join(',') + '\n'; // Convert each row to CSV format and append to the CSV string
    });

    // Create a hidden link to trigger the download
    var hiddenLink = document.createElement('a');
    hiddenLink.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv); // Encode the CSV data
    hiddenLink.target = '_blank';
    hiddenLink.download = 'feedback_report.csv'; // Set the file name for the download
    hiddenLink.click(); // Trigger the download
}

function downloadFeedbackSummaryCSV() {
        downloadSummaryToCSV('#feedback-summary-tables', 'feedback_summary_report.csv');
    }


    // Download functions for Batch Report
    function downloadBatchCSV() {
        var table = $('#batch-report-table').DataTable(); // Get the DataTable instance
      
    var data = table.rows({ search: 'applied' }).data(); // Get all rows, including filtered data

    // Start the CSV with the dynamically generated headers
    var csv = 'Student ID,Student Name,Department,Subject';
    $('#batch-report-table thead th').each(function(index) {
        if (index >= 4) { // Skip the first 4 columns (Student ID, Student Name, Department, Subject)
            var columnText = $(this).text(); // Get column header text (e.g., Q1, Q2, Q3)
            csv += ',' + columnText;
        }
    });
    csv += '\n'; // Add newline after the header row

    // Loop through each row of data and convert to CSV format
    data.each(function(row) {
        csv += row.join(',') + '\n'; // Convert each row to CSV format and append to the CSV string
    });

    // Create a hidden link to trigger the download
    var hiddenLink = document.createElement('a');
    hiddenLink.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv); // Encode the CSV data
    hiddenLink.target = '_blank';
    hiddenLink.download = 'feedback_report.csv'; // Set the file name for the download
    hiddenLink.click(); // Trigger the download
}
       
    function downloadBatchSummaryCSV() {
        downloadSummaryToCSV('#batch-summary-tables', 'batch_summary_report.csv');
    }

    // Generic download functions
    function downloadTableToCSV(tableId, filename) {
        var csv = [];
        var rows = document.querySelectorAll(tableId + " tr");
        
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (var j = 0; j < cols.length; j++) 
                row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
            
            csv.push(row.join(","));        
        }

        downloadCSVFile(csv.join("\n"), filename);
    }

    function downloadSummaryToCSV(containerId, filename) {
        var csv = [];
        var tables = document.querySelectorAll(containerId + " .summary-table");
        
        tables.forEach(function(table) {
            var subject = table.getAttribute('data-subject');
            csv.push('"Subject: ' + subject + '"');
            
            var rows = table.querySelectorAll("tr");
            for (var i = 1; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++) 
                    row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
                csv.push(row.join(","));
            }
            csv.push(""); // Empty line between subjects
        });

        downloadCSVFile(csv.join("\n"), filename);
    }

    function downloadCSVFile(csv, filename) {
        var csvFile = new Blob([csv], {type: "text/csv;charset=utf-8;"});
        var downloadLink = document.createElement("a");

        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";

        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Report generation functions
    function generateFeedbackReport(feedbackId, subjectId) {
        $.ajax({
            url: 'feedback_report3.php',
            type: 'POST',
            data: {
                action: 'generate_report',
                feedback_id: feedbackId,
                subject_id: subjectId
            },
            success: function(response) {
                handleReportResponse(response, 'feedback');
            },
            error: handleReportError
        });
    }

    function generateBatchReport(batch, subjectId) {
        $.ajax({
            url: 'feedback_report3.php',
            type: 'POST',
            data: {
                action: 'generate_report_sub',
                batch: batch,
                subject_id: subjectId
            },
            success: function(response) {
                handleReportResponse(response, 'batch');
            },
            error: handleReportError
        });
    }

    function handleReportResponse(response, prefix) {
        $(`#${prefix}-loader`).hide();

        if (response.data) {
            $(`#${prefix}-report-container`).show();

            // Clear and recreate table structure
            $(`#${prefix}-report-table`).html(`<thead class="gradient-header"><tr id="${prefix}-report-thead"></tr></thead><tbody id="${prefix}-report-tbody"></tbody>`);

            // Add header row
            var headerRow = '<th>Student ID</th><th>Student Name</th><th>Department</th><th>Subject</th>';
            for (var i = 1; i <= response.max_columns; i++) {
                headerRow += '<th>Q' + i + '</th>';
            }
            $(`#${prefix}-report-thead`).html(headerRow);

            // Add data rows
            $.each(response.data, function(subjectName, subjectData) {
                $.each(subjectData.student_responses, function(studentId, studentInfo) {
                    var row = '<tr>' +
                        '<td>' + studentId + '</td>' +
                        '<td>' + studentInfo.student_name + '</td>' +
                        '<td>' + studentInfo.student_department + '</td>' +
                        '<td>' + subjectName + '</td>';

                    for (var i = 0; i < response.max_columns; i++) {
                        row += '<td>' + (studentInfo.responses[i] || '') + '</td>';
                    }

                    row += '</tr>';
                    $(`#${prefix}-report-tbody`).append(row);
                });
            });

            // Initialize DataTable
            $(`#${prefix}-report-table`).DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "searching": true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });

            // Generate summary tables
            $(`#${prefix}-summary-tables`).empty();
            $.each(response.subjectSummary, function(subjectName, coData) {
                var summaryTable = '<table class="table table-striped table-bordered mt-3 summary-table" data-subject="' + subjectName + '">';
                summaryTable += '<thead class="gradient-header"><tr><th colspan="6">Subject: ' + subjectName + '</th></tr>';
                summaryTable += '<tr><th>CO</th><th>1</th><th>2</th><th>3</th><th>4</th><th>Total</th></tr></thead><tbody>';

                for (var coIndex in coData) {
                    if (coIndex !== 'totalStudents') {
                        summaryTable += '<tr><td>CO' + coIndex + '</td>';
                        for (var rating = 1; rating <= 4; rating++) {
                            var countData = coData[coIndex][rating];
                            summaryTable += '<td>' + countData[0] + ' (' + countData[1] + '%)</td>';
                        }
                        summaryTable += '<td>' + coData[coIndex].total + '</td></tr>';
                    }
                }

                summaryTable += '</tbody></table>';
                $(`#${prefix}-summary-tables`).append(summaryTable);
            });
        } else {
            alert('No data found for the selected parameters.');
        }
    }

    function handleReportError(xhr, status, error) {
        $('#feedback-loader, #batch-loader').hide();
        console.error('Ajax error:', status, error);
        alert('Error generating report. Please check the console for details.');
    }
    </script>
</body>
</html>