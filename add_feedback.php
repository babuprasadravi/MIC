<!DOCTYPE html>
<html lang="en">

<head>

</head>


<body>

    <!-- Feedback Management Page -->
    <div class="container-fluid">
        <!-- Button to Add Feedback -->
         <div class="row mb-3">
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeedbackModal">Add Feedback</button>
        </div>
         </div>

        <!-- Feedback Table -->
        <table class="table table-striped table-bordered py-4" id="feedbackTable">
            <thead class="gradient-header">
                <tr>
                    <th>Feedback Name</th>
                    <th>Department</th>
                    <th>Year</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Feedback rows will be populated here -->
            </tbody>
        </table>
    </div>
    
    <!-- Add this script at the bottom of your page -->
   
    <script>
        $(document).ready(function() {
            // Fetch and display feedbacks
            var table = $('#feedbackTable').DataTable();

            function loadFeedbacks() {
                $.get('feedback_management.php', {
                    action: 'get_feedbacks'
                }, function(data) {
                    let feedbacks = JSON.parse(data);
                    let tableBody = $('#feedbackTable tbody');
                    tableBody.empty();
                    feedbacks.forEach(feedback => {
                        let row = `<tr>
                            <td>${feedback.feedback_name}</td>
                            <td>${feedback.department}</td>
                            <td>${feedback.year}</td>
                            <td>${feedback.deadline}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="deleteFeedback(${feedback.id})">Delete</button>
                            </td>
                        </tr>`;
                        tableBody.append(row);
                    });
                    $('#feedbackTable').DataTable().clear().rows.add(tableBody.find('tr')).draw();
                });
            }

            // Load subjects for the select box
            function loadSubjects() {
                let department = $('#department').val();

                $.get('feedback_management.php', {
                    action: 'get_subjects_drop',
                    department: department
                }, function(data) {
                    let subjects = JSON.parse(data);
                    let selectBox = $('#subjects');
                    selectBox.empty();

                    // Group subjects by code
                    let groupedSubjects = {};
                    subjects.forEach(subject => {
                        if (!groupedSubjects[subject.subject_code]) {
                            groupedSubjects[subject.subject_code] = {
                                subject_name: subject.subject_name,
                                departments: []
                            };
                        }
                        groupedSubjects[subject.subject_code].departments.push(subject.department);
                    });

                    // Add subjects to select box
                    $.each(groupedSubjects, function(code, data) {
                        let departments = [...new Set(data.departments)]; // Unique departments
                        let option = `<option value="${code}">${data.subject_name} (${departments.join(', ')})</option>`;
                        selectBox.append(option);
                    });
                });
            }

            loadFeedbacks();
            $('#department').change(function() {
                loadSubjects();
            });

            // Call loadSubjects initially to load subjects for the default selected department
            loadSubjects();

            // Add Feedback Form Submission
            $('#addFeedbackForm').on('submit', function(e) {
                e.preventDefault();
                $.post('feedback_management.php', $(this).serialize() + '&action=add_feedback', function(response) {
                    let result = JSON.parse(response);
                    if (result.success) {
                        loadFeedbacks();
                        $('#addFeedbackModal').modal('hide');
                        $('#addFeedbackForm')[0].reset();
                    } else {
                        alert('Error: ' + result.error);
                    }
                });
            });

            // Placeholder functions for edit and delete actions
            window.editFeedback = function(feedbackId) {
                // Implement edit logic here
                alert('Edit feedback with ID: ' + feedbackId);
            };

            window.deleteFeedback = function(feedbackId) {
                if (confirm('Are you sure you want to delete this Feedback?')) {
                    $.ajax({
                        url: 'feedback_management.php',
                        type: 'POST',
                        data: {
                            id: feedbackId,
                            action: 'delete_feed'
                        },
                        dataType: 'json', // Expect JSON response
                        success: function(response) {
                            console.log(response);
                            if (response.status === 200) { // Corrected typo here
                                alert('Feedback Deleted Successfully.');
                                loadFeedbacks(); // Refresh the subject list
                            } else {
                                alert('Error deleting subject: ' + (response.message || 'Unknown error'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Server response:', xhr.responseText);
                            alert('An error occurred. Please check the console for details.');
                        }
                    });
                }
            };
        });
    </script>

</body>

</html>