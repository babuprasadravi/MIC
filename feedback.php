<!DOCTYPE html>
<html lang="en">

<head>

</head>

<body>

    <div class="container-fluid">
        <!-- Tabs Navigation -->

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="Feedbacks-tab" data-bs-toggle="tab" data-bs-target="#add-subjects" type="button" role="tab"  aria-selected="true">
                    <i class="fas fa-book tab-icon "></i> Add Subjects
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sem1-tab" data-bs-toggle="tab" data-bs-target="#add-feedbacks" type="button" role="tab"  aria-selected="false">
                    <i class="fas fa-star tab-icon"></i> Add Feedback
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="Asse-tab" data-bs-toggle="tab" data-bs-target="#add-reports" type="button" role="tab"  aria-selected="false">
                    <i class="fas fa-file-alt tab-icon"></i> Feedback Report
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="Co-Curr-tab" data-bs-toggle="tab" data-bs-target="#ns-reports" type="button" role="tab" aria-selected="false">
                    <i class="fas fa-exclamation-circle tab-icon"></i> Not Submitted Report
                </button>
            </li>
        </ul>

        <!-- Tabs Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Add Subjects Tab -->
                <div class="tab-pane fade show active" id="add-subjects" role="tabpanel" >

                    <div class="row mb-3">
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">Add
                                Subject</button>

                        </div>
                    </div>

                    <table class="table table-striped table-bordered py-4" id="subjectsTable">
                        <thead class="gradient-header">
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Department</th>
                                <th>Questions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show" id="add-feedbacks" role="tabpanel">
                    <?php include "add_feedback.php"; ?>
                </div>
           
            <div class="tab-pane fade show" id="add-reports" role="tabpanel" >
                <?php include "feedback_report3.php"; ?>
            </div>

            <div class="tab-pane fade show" id="ns-reports" role="tabpanel">
                <?php include "notsubmitted.php"; ?>
            </div>
        </div>
    </div>



    <!-- Add Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel"><strong> Add Subject </strong></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSubjectForm">
                        <div class="mb-3">
                            <label for="subjectCode" class="form-label">Subject Code:</label>
                            <input type="text" class="form-control" id="subjectCode" name="subjectCode" required>
                        </div>
                        <div class="mb-3">
                            <label for="subjectName" class="form-label">Subject Name:</label>
                            <input type="text" class="form-control" id="subjectName" name="subjectName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Departments:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="IT" name="departments[]" value="IT">
                                    <label class="form-check-label" for="IT">IT</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="CSE" name="departments[]" value="CSE">
                                    <label class="form-check-label" for="CSE">CSE</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="MECH" name="departments[]" value="MECH">
                                    <label class="form-check-label" for="MECH">MECH</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="ECE" name="departments[]" value="ECE">
                                    <label class="form-check-label" for="ECE">ECE</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="EE-VLSI" name="departments[]" value="EE(VLSI)">
                                    <label class="form-check-label" for="EE-VLSI">EE(VLSI)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="EEE" name="departments[]" value="EEE">
                                    <label class="form-check-label" for="EEE">EEE</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="AIDS" name="departments[]" value="AIDS">
                                    <label class="form-check-label" for="AIDS">AIDS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="AIML" name="departments[]" value="AIML">
                                    <label class="form-check-label" for="AIML">AIML</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="CIVIL" name="departments[]" value="CIVIL">
                                    <label class="form-check-label" for="CIVIL">CIVIL</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="CSBS" name="departments[]" value="CSBS">
                                    <label class="form-check-label" for="CSBS">CSBS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="MBA" name="departments[]" value="MBA">
                                    <label class="form-check-label" for="MBA">MBA</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="MCA" name="departments[]" value="MCA">
                                    <label class="form-check-label" for="MCA">MCA</label>
                                </div>
                            </div>
                        </div>
                        <div id="questionsContainer">
                            <div class="mb-3">
                                <label for="question" class="form-label">Question:</label>
                                <input type="text" class="form-control question-input" name="questions[]" placeholder="Enter a question">
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" id="addQuestionBtn" class="btn btn-secondary">+ Add Question</button>
                            <button type="submit" class="btn btn-primary">Add Subject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Modal -->
    <div class="modal fade" id="questionsModal" tabindex="-1" aria-labelledby="questionsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="questionsModalLabel"><strong> Questions</strong></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Questions will be dynamically loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
  
     <!--add_feedback.php Add Feedback Modal -->
     <div class="modal fade" id="addFeedbackModal" tabindex="-1" aria-labelledby="addFeedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFeedbackModalLabel"><strong> Add Feedback </strong></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFeedbackForm">
                        <div class="mb-3">
                            <label for="feedbackName" class="form-label">Feedback Name:</label>
                            <input type="text" class="form-control" id="feedbackName" name="feedbackName" required>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Year:</label>
                            <input type="text" class="form-control" id="year" name="year" placeholder="ex:2023-2027" required>
                        </div>
                        <div class="mb-3">
                            <label for="department" class="form-label">Department:</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="all">All Departments</option>
                                <option value="AIDS">Artificial Intelligence and Data Science</option>
                                <option value="AIML">Artificial Intelligence and Machine Learning</option>
                                <option value="CIVIL">Civil Engineering</option>
                                <option value="CSE">Computer Science</option>
                                <option value="CSBS">Computer Science and Business Systems</option>
                                <option value="ECE">Electronics and Communication Engineering</option>
                                <option value="EE(VLSI)">Electronics Engineering(VLSI Design and Technology)</option>
                                <option value="EEE">Electrical and Electronics Engineering</option>
                                <option value="IT">Information Technology</option>
                                <option value="MECH">Mechanical Engineering</option>
                                <option value="MBA">Master of Business Administration</option>
                                <option value="MCA">Master of Computer Applications</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subjects" class="form-label">Subjects:</label>
                            <select multiple class="form-select" id="subjects" name="subjects[]" required>
                                <!-- Options will be populated here -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Deadline:</label>
                            <input type="date" class="form-control" id="deadline" name="deadline" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#subjectsTable').DataTable();

            // Fetch and display subjects
            function loadSubjects() {
                $.ajax({
                    url: 'feedback_management.php',
                    type: 'GET',
                    data: {
                        action: 'get_subjects'
                    },
                    success: function(data) {

                        let subjects = JSON.parse(data);
                        let tableBody = $('#subjectsTable tbody');
                        tableBody.empty(); // Clear existing rows

                        // Loop through subjects and add rows
                        subjects.forEach(subject => {
                            // Join questions into a single string with <br> tags
                            let questions = subject.questions.map(q => q.replace(/</g, "&lt;").replace(/>/g, "&gt;")).join('<br>');
                            let row = `
                    <tr>
                        <td>${subject.subject_code}</td>
                        <td>${subject.subject_name}</td>
                        <td>${subject.department}</td>
                        <td>
                            <button class="btn btn-info btn-sm" data-questions="${questions}" onclick="viewQuestions(this)"><i class="fas fa-eye"></i></button>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteSubject(${subject.id})">Delete</button>
                        </td>
                    </tr>`;
                            tableBody.append(row);
                        });

                        // Redraw the table to reflect new data
                        $('#subjectsTable').DataTable().clear().rows.add(tableBody.find('tr')).draw();
                    }
                });
            }

            loadSubjects();

            // Add Subject
            $('#addSubjectForm').on('submit', function(e) {
                e.preventDefault();
                $.post('feedback_management.php', $(this).serialize() + '&action=add_subject', function(response) {
                    let result = JSON.parse(response);
                    if (result.success) {
                        loadSubjects();
                        $('#addSubjectModal').modal('hide');
                        $('#addSubjectForm')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Subject added successfully.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            loadSubjects(); // Refresh the subject list after closing the alert
                        });
                    } else {
                        alert('Error: ' + result.error);
                    }
                });
            });

            // Add Question Button
            $('#addQuestionBtn').on('click', function() {
                $('#questionsContainer').append(
                    `<div class="form-group">
            <label for="question"class="mb-2">Question:</label>
            <div class="d-flex">
                <input type="text" class="form-control question-input mb-2 " name="questions[]" placeholder="Enter a question" >
                <button type="button" class="btn btn-danger mb-2  remove-question"><i class="fa fa-trash"></i></button>
            </div>
        </div>`
                );
            });
            // Remove question field when delete button is clicked
            $(document).on('click', '.remove-question', function() {
                $(this).closest('.form-group').remove();
            });
            // Placeholder functions for edit and delete actions
            window.editSubject = function(subjectId) {
                // Implement edit logic here
                alert('Edit subject with ID: ' + subjectId);
            };
            window.deleteSubject = function(subjectId) {
                if (confirm('Are you sure you want to delete this subject?')) {
                    $.ajax({
                        url: 'feedback_management.php',
                        type: 'POST',
                        data: {
                            id: subjectId,
                            action: 'delete_sub'
                        },
                        dataType: 'json', // Expect JSON response
                        success: function(response) {
                            console.log(response);
                            if (response.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Subject deleted successfully.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    loadSubjects(); // Refresh the subject list after closing the alert
                                });
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



        function viewQuestions(button) {
            let questions = $(button).data('questions').split('<br>');
            let modalBody = $('#questionsModal .modal-body');

            // Create a list of questions with bullet points
            let questionList = '<ul>';
            questions.forEach(question => {
                questionList += `<li>${question}</li>`;
            });
            questionList += '</ul>';

            modalBody.html(questionList); // Set the content with bullet points
            $('#questionsModal').modal('show');
        }
    </script>
</body>

</html>