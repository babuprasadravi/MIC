<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Management</title>
</head>
<body>

<div class="container mt-5">
    <div class="mb-3">
        <label for="feedback_id2" class="form-label">Select Feedback:</label>
        <select class="form-select" id="feedback_id2" name="feedback_id2">
            <option value="">Select Feedback</option>
            <?php
            // Include your database connection here
            require_once 'feed_db_connection.php';

            // Query to get all feedback names
            $feedback_query = "SELECT id, feedback_name FROM feedbacks WHERE `sid`='$s'";
            $feedback_result = mysqli_query($fconn, $feedback_query);
            while ($row = mysqli_fetch_assoc($feedback_result)) {
                echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['feedback_name']) . "</option>";
            }
            ?>
        </select>
    </div>

    <!-- Button to trigger Excel export -->
    <button id="export_excel" class="btn btn-primary mb-3">Export to Excel</button>

    <!-- Table for displaying unsubmitted students -->
    <div class="table-responsive">
        <table id="students_table" class="table table-striped table-bordered" style="width:100%; display: none;">
        <thead class="gradient-header">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>


<!-- Include XLSX JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#students_table').DataTable({
        columns: [
            { title: "Student ID" },
            { title: "Name" },
            { title: "Department" }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
        responsive: true
    });

    $('#feedback_id2').change(function() {
        var feedbackId2 = $(this).val();
        if (feedbackId2) {
            $.ajax({
                url: 'feedback_management.php',
                type: 'POST',
                data: { feedback_id2: feedbackId2, action: 'not_submitted' },
                success: function(data) {
                    table.clear();

                    if (data.trim() === "" || data.includes("colspan")) {
                        table.row.add(["No Data Available", "", ""]).draw();
                    } else {
                        var rows = $(data).filter('tr');
                        table.rows.add(rows).draw();
                    }
                    
                    $('#students_table').show();
                },
                error: function() {
                    table.clear();
                    table.row.add(["Error loading data", "", ""]).draw();
                    $('#students_table').show();
                }
            });
        } else {
            table.clear().draw();
            $('#students_table').hide();
        }
    });

    // Function to export table data to Excel
    $('#export_excel').click(function() {
        var allData = table.rows().data().toArray();

        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.json_to_sheet(allData.map(row => ({
            "Student ID": row[0],
            "Name": row[1],
            "Department": row[2]
        })));

        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'students_data.xlsx');
    });
});
</script>

</body>
</html>
