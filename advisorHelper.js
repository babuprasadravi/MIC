let advisorStudents = [];
let studentList = [];
let facultyStudentMap = {};
let mappedStudentsSet = new Set();
let advisorCourses = [];
let editMode = false;
var subjectLoader = $("#subjectLoader");  // Adjust the selector to match your markup
var subjectSelect = $("#subjectSelect");

let availableCOs = ["CO1", "CO2", "CO3", "CO4", "CO5"];
let selectedCOs = new Map();

document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM fully loaded");
  getTimeTable();
  loadAdvisorCourses();
  createTimetable();
  fetchExamTimetables();

});

function fetchExamTimetables() {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getExamTimeTables",
      academicYear : advisorData.academicYear,
      batch : advisorData.batch,
      section : advisorData.section,
      department : advisorData.department,
      semester : advisorData.semester
    },
    success: function(response) {
      try {
        response = typeof response === 'string' ? JSON.parse(response) : response;
        if (response.status === "success") {
          renderTimetableList(response.timetables);
        } else {
          console.error("Error fetching timetables:", response.message);
        }
      } catch (e) {
        console.error("Error parsing timetables response:", e);
      }
    },
    error: function(xhr, status, error) {
      console.error("AJAX Error fetching timetables:", error);
    }
  });
}

// Function to render the timetable list into the table
function renderTimetableList(timetables) {
  const $tbody = $('#timetableBody');
  $tbody.empty();
  
  if (!timetables || timetables.length === 0) {
    $tbody.append(`<tr><td colspan="4" class="text-center">No timetables found.</td></tr>`);
    return;
  }
  
  // Loop through each timetable record
  timetables.forEach(timetable => {
    // Build a summary of the courses – for example, join course names with exam dates and times.
    let courseDetails = '';
    timetable.courses.forEach(course => {
      courseDetails += `<strong>${course.course_name}</strong><br>
                        Date: ${course.exam_date} | Time: ${course.exam_time}<br>
                        ${course.description ? 'Details: ' + course.description : ''}<hr>`;
    });
    
    const row = `
      <tr>
        <td>${timetable.exam_name}</td>
        <td>${courseDetails}</td>
        <td>${timetable.created_at}</td>
      </tr>
    `;
    $tbody.append(row);
  });
}

// Reusable function for fetching available courses for a specific dropdown
// Reusable function for fetching available courses for a specific dropdown
function fetchAvailableCoursesForElement($subjectSelect, $subjectLoader) {
  $subjectLoader.removeClass('d-none');
  $subjectSelect.addClass('subject-select-loading').prop('disabled', true);

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAdvisorCourses"
    },
    success: function (response) {
      try {
        response = JSON.parse(response);
        console.log("AJAX Response:", response.courses);

        const courses = response.courses;

        // Clear existing options
        $subjectSelect.empty();

        // Add default option
        const defaultOption = $("<option>", {
          value: "",
          text: "Select a subject",
          selected: true,
          disabled: true
        });
        $subjectSelect.append(defaultOption);

        // Add options from the courses array
        if (courses && Array.isArray(courses) && courses.length > 0) {
          $.each(courses, function(index, course) {
            const option = $("<option>", {
              value: course.course_id,
              text: course.course_name
            });
            $subjectSelect.append(option);
          });
        } else {
          const noCoursesOption = $("<option>", {
            value: "",
            text: "No courses available",
            disabled: true
          });
          $subjectSelect.append(noCoursesOption);
        }
      } catch (e) {
        console.error("Error parsing response:", e);
        showErrorOption($subjectSelect, "Failed to load courses. Try again.");
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      showErrorOption($subjectSelect, "Failed to load courses. Try again.");
    },
    complete: function () {
      $subjectLoader.addClass("d-none");
      $subjectSelect.removeClass("subject-select-loading").prop("disabled", false);
    }
  });

  function showErrorOption($select, message) {
    $select.empty();
    const errorOption = $("<option>", {
      value: "",
      text: message,
      disabled: true,
      selected: true
    });
    $select.append(errorOption);

    // Optionally add a retry option
    $select.append($("<option>", {
      value: "retry",
      text: "↻ Click to retry"
    }));
  }
}



$(document).ready(function () {
  const advisorData = sessionStorage.getItem("advisorData");
  if (advisorData) {
    const data = JSON.parse(advisorData);
    $("#batch").val(data.batch);
    $("#academicYear").val(data.academicYear);
    $("#semester").val(data.semester);
    getTimeTable();
    initializeAttendanceView();
  }

  // call the function to load departments once the page is loaded
  loadDepartments();

  // Add department change handler to load faculty by department when department is changed or selected
  $("#department").on("change", function () {
    const selectedDept = $(this).val();
    if (selectedDept) {
      loadFacultyByDepartment(selectedDept);
    }
  });

  // Load advisor students when the page is loaded
  loadAdvisorStudents().then(() => {
    studentList = advisorStudents;
  });

  // Add faculty count change handler
  $("#facultyCount").on("change", function () {
    const count = parseInt($(this).val());
    generateFacultyFields(count);
  });
  $('#timeTableForm').on('submit', function (e) {
    e.preventDefault();
    
    // Get the exam name from the input field
    const examName = $('#examName').val().trim();
    
    // Initialize an array to collect course details
    const coursesData = [];
    
    // Loop through each course item to collect its data
    $('.course-item').each(function () {
      const $courseItem = $(this);
      
      const courseId = $courseItem.find('.subject-select').val();
      const courseName = $courseItem.find('.subject-select option:selected').text();
      const examDate = $courseItem.find('.course-date').val();
      const examTime = $courseItem.find('.course-time').val();
      const description = $courseItem.find('.course-details').val().trim();
      
      coursesData.push({
        course_id: courseId,
        course_name: courseName,
        exam_date: examDate,
        exam_time: examTime,
        description: description
      });
    });
    
    // Prepare the final form data object
    const formData = {
      examName: examName,
      courses: coursesData
    };
    const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
    
    // Make the AJAX request to submit the exam timetable
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "addExamTimeTable",
        data: JSON.stringify(formData),
        academicYear: advisorData.academicYear,
        batch: advisorData.batch,
        department : advisorData.department,
        section : advisorData.section,
        semester : advisorData.semester

      },
      success: function (response) {
        try {
          // If the response is a JSON string, parse it
          response = typeof response === 'string' ? JSON.parse(response) : response;
          if (response.status && response.status === "success") {
            console.log("Exam timetable saved successfully:", response);
            // Reload the exam timetable list
            fetchExamTimetables();
            // Reset the form
            resetTimeTableForm();
          } else {
            console.error("Failed to save exam timetable:", response.message);
            // Optionally, display an error message to the user
          }
        } catch (e) {
          console.error("Error parsing server response:", e);
        }
      },      
      error: function (xhr, status, error) {
        console.error("AJAX Error while saving exam timetable:", error);
      }
    });
  });

  function resetTimeTableForm() {
    // Reset exam name
    $('#examName').prop('selectedIndex', 0);
    
    // Remove all course items (if any) and add a fresh course item if needed
    $('#coursesContainer').empty();
    
    // Optionally, add a default course item or show a message
    // e.g., addCourseItem(); 
  }
  
  

  // Pick Student button click handler
  $("#pickStudentBtn").on("click", function () {
    const selectedFacultyId = $("#facultySelect").val();
    if (!selectedFacultyId) {
      Swal.fire({
        title: "Error",
        text: "Please select a faculty first",
        icon: "error",
      });
      return;
    }

    // Show student selection modal
    $("#studentSelectionModal").modal("show");
    loadStudentsForFaculty(selectedFacultyId);
  });

  // Load students for selected faculty
  function loadStudentsForFaculty(facultyId) {
    const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "getStudentsForFaculty",
        facultyId: facultyId,
        batch: advisorData.batch,
        section: advisorData.section,
      },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.status === "success") {
          displayStudentCheckboxes(data.students);
        } else {
          Swal.fire({
            title: "Error",
            text: data.message,
            icon: "error",
          });
        }
      },
    });
  }

  // Display student checkboxes in modal
  function displayStudentCheckboxes(students) {
    const container = $("#studentCheckboxes");
    container.empty();

    students.forEach((student) => {
      container.append(`
        <div class="form-check">
          <input class="form-check-input student-checkbox" 
                 type="checkbox" 
                 value="${student.uid}" 
                 id="student${student.uid}">
          <label class="form-check-label" for="student${student.uid}">
            ${student.roll_no} - ${student.name}
          </label>
        </div>
      `);
    });
  }

  $("#saveStudentsBtn").on("click", function () {
    let selectedFaculty = null;
    let selectedStudents = [];

    $(".student-checkbox:checked").each(function () {
      const studentId = $(this).data("student-id");
      const facultyId = $(this).data("faculty-id");

      if (!selectedFaculty) {
        selectedFaculty = facultyId;
      }

      if (facultyId === selectedFaculty) {
        selectedStudents.push(studentId);
      }
    });

    if (!selectedFaculty || selectedStudents.length === 0) {
      Swal.fire({
        title: "No Selection",
        text: "Please select at least one student.",
        icon: "warning",
      });
      return;
    }

    // Show confirmation dialog
    Swal.fire({
      title: "Confirm Selection",
      html: `
        <p>You have selected <strong>${selectedStudents.length}</strong> students.</p>
        <p>Are you sure you want to assign these students to the selected faculty?</p>
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Yes, Save",
      cancelButtonText: "No, Review",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        // Store in facultyStudentMap
        if (!facultyStudentMap[selectedFaculty]) {
          facultyStudentMap[selectedFaculty] = [];
        }
        facultyStudentMap[selectedFaculty] =
          facultyStudentMap[selectedFaculty].concat(selectedStudents);

        // Add students to mapped set to prevent future selection
        selectedStudents.forEach((studentId) =>
          mappedStudentsSet.add(studentId)
        );

        Swal.fire({
          title: "Success!",
          text: `${selectedStudents.length} students have been assigned successfully!`,
          icon: "success",
        }).then(() => {
          $("#studentsModal").modal("hide");
        });
      }
      // If not confirmed, modal stays open with current selections
    });
  });

  $("#finalSubmitBtn").on("click", function (e) {
    e.preventDefault();
    const section = JSON.parse(sessionStorage.getItem("advisorData")).section;
    console.log("section", section);


    const formData = {
      action: "mapStudentsToFaculty",
      batch: $("#batch").val(),
      academicYear: $("#academicYear").val(),
      semester: $("#semester").val(),
      department: $("#department").val(),
      courseName: $("#courseName").val(),
      courseCode: $("#courseCode").val(),
      courseCredit: $("#courseCredit").val(),
      courseType: $("#courseType").val(),
      section: section,
      facultyStudentMap: facultyStudentMap,
    };
   
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: formData,
      success: function (response) {
        try {
          const result = JSON.parse(response);
          if (result.status === "success") {
            Swal.fire({
              title: "Success!",
              text: result.message,
              icon: "success",
            }).then(() => {
              // Append the hash to the URL before reloading
              window.location.href =
                window.location.href.split("#")[0] + "#time-table";
              location.reload();
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: result.message,
              icon: "error",
            });
          }
        } catch (e) {
          console.error("Error parsing response:", e);
          Swal.fire({
            title: "Error!",
            text: "An unexpected error occurred",
            icon: "error",
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        Swal.fire({
          title: "Error!",
          text: "Failed to submit data. Please try again.",
          icon: "error",
        });
      },
    });
  });

  // View Student Summary button click
  $("#viewStudentSummaryBtn").on("click", function () {
    $("#attendance-cards-view").hide();
    $("#student-summary-view").show();
    loadStudentAttendanceSummary(); 
  });

  // Back button click
  $("#back-to-attendance-cards-student").on("click", function () {
    $("#student-summary-view").hide();
    $("#attendance-cards-view").show();
  });
});

document
  .getElementById("viewODRequestsBtn")
  .addEventListener("click", function () {
    document.getElementById("academic-cards-view").style.display = "none";
    document.getElementById("od-requests-view").style.display = "block";
    loadODRequests();
  });

document
  .getElementById("back-to-admin-cards")
  .addEventListener("click", function () {
    document.getElementById("od-requests-view").style.display = "none";
    document.getElementById("academic-cards-view").style.display = "block";
  });

let allRequests = []; // Store all requests globally

function loadODRequests() {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  const userData = JSON.parse(sessionStorage.getItem("userData"));

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getODRequests",
      academicYear: advisorData.academicYear,
      section: advisorData.section,
      batch: advisorData.batch,
      dept: userData.dept,
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        const tbody = document.getElementById("odRequestsTable");
        tbody.innerHTML = ""; // Clear existing rows

        if (data.status === "success") {
          allRequests = data.requests; // Store all requests
          displayRequests(allRequests); // Display all requests initially
        } else {
          console.error("Error fetching requests:", data.message);
          tbody.innerHTML = `<tr><td colspan="9" class="text-center">No requests found.</td></tr>`;
        }
      } catch (e) {
        console.error("Error parsing response:", e);
        tbody.innerHTML = `<tr><td colspan="9" class="text-center">Error loading requests.</td></tr>`;
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      const tbody = document.getElementById("odRequestsTable");
      tbody.innerHTML = `<tr><td colspan="9" class="text-center">Failed to connect to the server.</td></tr>`;
    },
  });
}

// Function to display requests in the table
function displayRequests(requests) {
  const tbody = document.getElementById("odRequestsTable");
  tbody.innerHTML = ""; // Clear existing rows

  requests.forEach((request) => {
    const row = `
            <tr>
                <td>${request.leave_id}</td>
                <td>${request.student_name}</td>
                <td>${request.student_roll_no}</td>
                <td><span class="badge bg-${
                  request.leave_type === "OD" ? "info" : "warning"
                }">${request.leave_type}</span></td>
                <td>${request.start_date}</td>
                <td>${request.end_date || "N/A"}</td>
                <td>${request.reason || "N/A"}</td>
                <td><span class="badge bg-${getStatusBadgeColor(
                  request.status
                )}">${request.status || "Pending"}</span></td>
                <td>
                    ${
                      request.status === "Pending"
                        ? `
                        <button class="btn btn-sm btn-success approve-btn" data-request-id="${request.leave_id}">
                            Approve
                        </button>
                        <button class="btn btn-sm btn-danger reject-btn" data-request-id="${request.leave_id}">
                            Reject
                        </button>
                    `
                        : request.approved_at
                    }
                </td>
            </tr>
        `;
    tbody.innerHTML += row;
  });

  // Add event listeners to approve and reject buttons
  document.querySelectorAll(".approve-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const requestId = this.dataset.requestId;
      handleRequestApproval(requestId);
    });
  });

  document.querySelectorAll(".reject-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const requestId = this.dataset.requestId;
      handleRequestRejection(requestId);
    });
  });
}

// Filter requests based on selected type and status
function filterRequests() {
  const requestType = document.getElementById("requestTypeFilter").value; // Get selected request type
  const statusFilter = document.getElementById("statusFilter").value; // Get selected status

  const filteredRequests = allRequests.filter((request) => {
    const typeMatch =
      requestType === "all" || request.leave_type === requestType;
    const statusMatch =
      statusFilter === "all" || request.status.toLowerCase() === statusFilter;
    return typeMatch && statusMatch;
  });

  displayRequests(filteredRequests); // Display filtered requests
}

// Event listeners for filters
document
  .getElementById("requestTypeFilter")
  .addEventListener("change", filterRequests);
document
  .getElementById("statusFilter")
  .addEventListener("change", filterRequests);

function handleRequestApproval(requestId) {
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  // Implement the approval logic
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "approveRequest",
      leave_id: requestId,
      advisorId: userData.id,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        alert("Request approved successfully!");
        loadODRequests(); // Reload the requests
      } else {
        alert("Error approving request: " + data.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      alert("Failed to approve request. Please try again.");
    },
  });
}

function handleRequestRejection(requestId) {
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  // Implement the rejection logic
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "rejectRequest",
      leave_id: requestId,
      advisorId: userData.id,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        alert("Request rejected successfully!");
        loadODRequests(); // Reload the requests
      } else {
        alert("Error rejecting request: " + data.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      alert("Failed to reject request. Please try again.");
    },
  });
}

function getStatusBadgeColor(status) {
  switch (status.toLowerCase()) {
    case "pending":
      return "warning";
    case "approved":
      return "success";
    case "rejected":
      return "danger";
    default:
      return "secondary";
  }
}

function showRequestDetails(requestId) {
  // Show modal with request details
  const modal = new bootstrap.Modal(
    document.getElementById("requestDetailsModal")
  );
  modal.show();
}

// Function to load departments from the faculty table in backend.php once the page is loaded
function loadDepartments() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getDepartments",
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          const departmentSelect = $("#department");
          departmentSelect.empty();
          departmentSelect.append(
            '<option value="">Select Department</option>'
          );

          data.departments.forEach((dept) => {
            departmentSelect.append(`<option value="${dept}">${dept}</option>`);
          });
        } else {
          console.error("Error:", data.message);
        }
      } catch (e) {
        console.error("Error parsing response:", e);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}
// attendance fetching functionlaity
function initializeAttendanceView() {
  // Set default date to today and max date
  const today = new Date().toISOString().split("T")[0];
  const dateInput = $("#attendanceDate");

  // Set today as default and max date
  dateInput.val(today);
  dateInput.attr("max", today);

  // Add change event listener for date validation
  dateInput.on("change", function () {
    const selectedDate = new Date(this.value);
    const currentDate = new Date();

    // Reset time portion for accurate date comparison
    selectedDate.setHours(0, 0, 0, 0);
    currentDate.setHours(0, 0, 0, 0);

    if (selectedDate > currentDate) {
      Swal.fire({
        title: "Invalid Date",
        text: "Future dates cannot be selected",
        icon: "warning",
      });
      // Reset to today's date
      $(this).val(today);
      return;
    }

    // If date is valid, load the marking status
    loadFacultyMarkingStatus(this.value);
  });

  // Load initial data
  loadFacultyMarkingStatus(today);
}

function fetchAttendanceData(date) {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAttendanceView",
      date: date,
      batch: advisorData.batch,
      semester: advisorData.semester,
      section: advisorData.section,
      advisorId: userData.id, // Adding advisor ID
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        displayAttendanceData(data.attendance);
      } else {
        Swal.fire({
          title: "Error",
          text: data.message,
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

function displayAttendanceData(data) {
  const tbody = $("#hourWiseAttendance");
  tbody.empty();

  // Initialize hour-wise counters
  let hourWiseSummary = {};
  for (let i = 1; i <= 8; i++) {
    hourWiseSummary[i] = { present: 0, absent: 0, od: 0, leave: 0, na: 0 };
  }

  // Generate rows for each student
  data.students.forEach((student) => {
    let row = `<tr>
          <td>${student.roll_no}</td>
          <td>${student.sname}</td>`;

    // Add status for each hour
    for (let i = 1; i <= 8; i++) {
      const status = student.hours[i] || "NA";
      let statusClass = "";
      let statusText = status;

      switch (status) {
        case "P":
          statusClass = "bg-success text-white";
          statusText = "Present";
          hourWiseSummary[i].present++;
          break;
        case "A":
          statusClass = "bg-danger text-white";
          statusText = "Absent";
          hourWiseSummary[i].absent++;
          break;
        case "L":
          statusClass = "bg-warning text-white";
          statusText = "Leave";
          hourWiseSummary[i].leave++;
          break;
        case "OD":
          statusClass = "bg-info text-white";
          statusText = "OD";
          hourWiseSummary[i].od++;
          break;

        default:
          statusClass = "bg-secondary text-white";
          statusText = "NA";
          hourWiseSummary[i].na++;
          break;
      }

      row += `<td class="${statusClass}">${statusText}</td>`;
    }

    row += "</tr>";
    tbody.append(row);
  });

  // Add summary row at the bottom
  let summaryHtml = `
        <div class="mt-4">
            <h5>Hour-wise Summary</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            ${Array.from(
                              { length: 8 },
                              (_, i) => `<th>Hour ${i + 1}</th>`
                            ).join("")}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-success">Present</span></td>
                            ${Array.from(
                              { length: 8 },
                              (_, i) =>
                                `<td>${hourWiseSummary[i + 1].present}</td>`
                            ).join("")}
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">Absent</span></td>
                            ${Array.from(
                              { length: 8 },
                              (_, i) =>
                                `<td>${hourWiseSummary[i + 1].absent}</td>`
                            ).join("")}
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning text-dark">Leave</span></td>
                            ${Array.from(
                              { length: 8 },
                              (_, i) =>
                                `<td>${hourWiseSummary[i + 1].leave}</td>`
                            ).join("")}
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">OD</span></td>
                            ${Array.from(
                              { length: 8 },
                              (_, i) => `<td>${hourWiseSummary[i + 1].od}</td>`
                            ).join("")}
                        </tr>
                        <tr>
                            <td><span class="badge bg-secondary">Not Marked</span></td>
                            ${Array.from(
                              { length: 8 },
                              (_, i) => `<td>${hourWiseSummary[i + 1].na}</td>`
                            ).join("")}
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    `;
  // Remove old summary cards and add new hour-wise summary
  $(".summary-cards").remove();
  $("#attendanceViewContent").append(summaryHtml);

  // Show the attendance view content
  $("#attendanceViewContent").show();
}

// New function to generate faculty fields
function generateFacultyFields(count) {
  const container = document.getElementById("facultySelectionContainer");
  container.innerHTML = ""; // Clear existing fields

  for (let i = 0; i < count; i++) {
    const template = document.getElementById("facultyTemplate");
    const clone = template.content.cloneNode(true);

    const facultyGroup = clone.querySelector(".faculty-group");
    facultyGroup.id = `faculty-group-${i + 1}`;

    const facultySelect = clone.querySelector(".faculty-select");
    facultySelect.id = `faculty-select-${i + 1}`;

    // Add faculty number label
    const facultyLabel = clone.querySelector(".faculty-number");
    facultyLabel.textContent = `Faculty ${i + 1}`;

    // Add "Pick Students" button handler
    const pickStudentsBtn = clone.querySelector(".pick-students-btn");
    pickStudentsBtn.onclick = function () {
      const selectedFaculty = facultySelect.value;
      if (!selectedFaculty) {
        alert("Please select a faculty first.");
        return;
      }
      showStudentSelectionModal(selectedFaculty);
    };

    container.appendChild(clone);
  }

  // Load faculty options if department is selected
  const selectedDepartment = $("#department").val();
  if (selectedDepartment) {
    loadFacultyByDepartment(selectedDepartment);
  }
}

// Function to load faculty by department
function loadFacultyByDepartment(department) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getFacultyByDepartment",
      department: department,
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          // Update ALL faculty select dropdowns in the container
          const facultySelects = document.querySelectorAll(
            "#facultySelectionContainer .faculty-select"
          );

          facultySelects.forEach((select) => {
            // Clear and update each dropdown
            select.innerHTML = '<option value="">Select Faculty</option>';

            data.faculty.forEach((faculty) => {
              const option = document.createElement("option");
              option.value = faculty.id;
              option.textContent = `${faculty.name} - ${faculty.designation}`;
              select.appendChild(option);
            });
          });
        } else {
          console.error("Error:", data.message);
        }
      } catch (e) {
        console.error("Error parsing response:", e);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

//Function to load advisor students
function loadAdvisorStudents() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "getAdvisorStudents",
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.status === "success") {
            advisorStudents = data.students; // Store students in the declared variable
            // console.log("Students loaded inside function:", advisorStudents);
            resolve(advisorStudents);
          } else {
            console.error("Error:", data.message);
            reject(data.message);
          }
        } catch (e) {
          console.error("Error parsing response:", e);
          reject(e);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        reject(error);
      },
    });
  });
}

// Update the showStudentSelectionModal function to properly handle checkbox selection
function showStudentSelectionModal(facultyId) {
  const modalBody = $("#studentsModal .modal-body");
  modalBody.find("#studentCheckboxes").empty();

  let availableStudents = studentList.filter(
    (student) => !mappedStudentsSet.has(student.id)
  );

  if (availableStudents.length === 0) {
    modalBody
      .find("#studentCheckboxes")
      .append("<p>No students available for selection.</p>");
    return;
  }

  availableStudents.forEach((student) => {
    const studentCard = `
      <div class="form-check">
        <input class="form-check-input student-checkbox" type="checkbox" 
               data-student-id="${student.id}" 
               data-faculty-id="${facultyId}" 
               id="student-${student.id}">
        <label class="form-check-label" for="student-${student.id}">
          ${student.name}<br>
          <small class="text-muted">${student.studentId}</small>
        </label>
      </div>
    `;
    modalBody.find("#studentCheckboxes").append(studentCard);
  });

  // Clear previous event handlers
  $("#selectAllBtn").off("click");
  $("#selectFirstHalfBtn").off("click");
  $("#resetSelectBtn").off("click");

  // Add functionality for Select All button
  $("#selectAllBtn").on("click", function () {
    $(".student-checkbox").prop("checked", true);
  });

  // Add functionality for Select First Half button
  $("#selectFirstHalfBtn").on("click", function () {
    const checkboxes = $(".student-checkbox").toArray();
    const halfCount = Math.ceil(checkboxes.length / 2);

    // Uncheck all checkboxes first
    checkboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });

    // Check the first half
    for (let i = 0; i < halfCount; i++) {
      checkboxes[i].checked = true;
    }
  });

  // Add Reset Select button functionality
  $("#resetSelectBtn").on("click", function () {
    $(".student-checkbox").prop("checked", false);
  });

  const modal = new bootstrap.Modal(document.getElementById("studentsModal"));
  modal.show();
}

$("#saveStudentsBtn").on("click", function () {
  let selectedFaculty = null;
  let selectedStudents = [];

  $(".student-checkbox:checked").each(function () {
    const studentId = $(this).data("student-id");
    const facultyId = $(this).data("faculty-id");

    if (!selectedFaculty) {
      selectedFaculty = facultyId;
    }

    if (facultyId === selectedFaculty) {
      selectedStudents.push(studentId);
    }
  });

  if (!selectedFaculty || selectedStudents.length === 0) {
    Swal.fire({
      title: "No Selection",
      text: "Please select at least one student.",
      icon: "warning",
    });
    return;
  }

  // Show confirmation dialog
  Swal.fire({
    title: "Confirm Selection",
    html: `
      <p>You have selected <strong>${selectedStudents.length}</strong> students.</p>
      <p>Are you sure you want to assign these students to the selected faculty?</p>
    `,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Save",
    cancelButtonText: "No, Review",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      // Store in facultyStudentMap
      if (!facultyStudentMap[selectedFaculty]) {
        facultyStudentMap[selectedFaculty] = [];
      }
      facultyStudentMap[selectedFaculty] =
        facultyStudentMap[selectedFaculty].concat(selectedStudents);

      // Add students to mapped set to prevent future selection
      selectedStudents.forEach((studentId) => mappedStudentsSet.add(studentId));

      Swal.fire({
        title: "Success!",
        text: `${selectedStudents.length} students have been assigned successfully!`,
        icon: "success",
      }).then(() => {
        $("#studentsModal").modal("hide");
      });
    }
    // If not confirmed, modal stays open with current selections
  });
});

function loadAdvisorCourses() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAdvisorCourses",
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        console.log("Received advisor courses data:", data);

        if (data.status === "success") {
          advisorCourses = data.courses;

          renderCourseCards(data.courses);
        } else {
          console.error("Error:", data.message);
          // Show error message to user
          const availableCoursesContainer = $(
            "#available-courses .container.mt-4"
          );
          availableCoursesContainer.html(`
            <div class="alert alert-danger text-center p-4 rounded-3 shadow-sm">
              <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
              <h4>Error Loading Courses</h4>
              <p class="mb-0">${data.message}</p>
            </div>
          `);
        }
      } catch (e) {
        console.error("Error parsing response:", e);
        // Show parsing error to user
        const availableCoursesContainer = $(
          "#available-courses .container.mt-4"
        );
        availableCoursesContainer.html(`
          <div class="alert alert-danger text-center p-4 rounded-3 shadow-sm">
            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
            <h4>Error</h4>
            <p class="mb-0">Failed to load course data. Please try again later.</p>
          </div>
        `);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      // Show network error to user
      const availableCoursesContainer = $("#available-courses .container.mt-4");
      availableCoursesContainer.html(`
        <div class="alert alert-danger text-center p-4 rounded-3 shadow-sm">
          <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
          <h4>Network Error</h4>
          <p class="mb-0">Failed to connect to the server. Please check your connection and try again.</p>
        </div>
      `);
    },
  });
}

function showErrorState(message) {
  const container = $("#available-courses .container.mt-4");
  console.log(`[UI] Showing error state: ${message}`);
  container.html(`
        <div class="alert alert-danger text-center p-4 rounded-3 shadow-sm">
            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
            <h4>Error Loading Courses</h4>
            <p class="mb-0">${message}</p>
        </div>
    `);
}

// Update the renderCourseCards function to include section information
function renderCourseCards(courses) {
  const availableCoursesContainer = $("#available-courses .container.mt-4");
  availableCoursesContainer.empty();

  if (courses.length === 0) {
    const noCoursesMessage = `
      <div class="alert alert-info text-center p-5 rounded-3 shadow-sm">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h4>No Courses Available</h4>
        <p class="mb-0">There are no courses assigned for your section this semester.</p>
      </div>
    `;
    availableCoursesContainer.append(noCoursesMessage);
  } else {
    const headerSection = `
      <div class="courses-header">
        <h3 class="courses-title">Your Courses</h3>
        <p class="text-muted">Available ${courses.length} course${
      courses.length > 1 ? "s" : ""
    } this semester</p>
      </div>
      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
    `;
    availableCoursesContainer.append(headerSection);

    courses.forEach((course) => {
      // Generate faculty list HTML
      const facultyList = course.faculty
        .map(
          (faculty) => `
        <div class="faculty-member">
          <i class="fas fa-user-tie me-1"></i>
          <span class="faculty-name">${faculty.name}</span>
          <small class="faculty-designation">(${faculty.designation})</small>
        </div>
      `
        )
        .join("");

      const courseCard = `
        <div class="col">
          <div class="course-card h-100">
            <div class="course-card-header">
              <span class="course-type-badge ${course.course_type.toLowerCase()}">${
        course.course_type
      }</span>
              <span class="credits-pill">
                <i class="fas fa-award"></i> ${course.course_credit} Credits
              </span>
            </div>
            <div class="course-card-body">
              <h4 class="course-name">${course.course_name}</h4>
              <div class="course-code">${course.course_code}</div>
              
              <div class="course-info-grid">
                <div class="info-item">
                  <i class="fas fa-building text-primary"></i>
                  <span>${course.department}</span>
                </div>
                <div class="info-item">
                  <i class="fas fa-calendar text-success"></i>
                  <span>${course.academic_year}</span>
                </div>
                <div class="info-item">
                  <i class="fas fa-users text-info"></i>
                  <span>${course.batch} - ${course.section}</span>
                </div>
                <div class="info-item">
                  <i class="fas fa-clock text-warning"></i>
                  <span>Semester ${course.semester}</span>
                </div>
              </div>

              <div class="faculty-section mt-3">
                <h6 class="faculty-section-title">
                  <i class="fas fa-chalkboard-teacher text-primary"></i>
                  Faculty Members
                </h6>
                <div class="faculty-list">
                  ${facultyList}
                </div>
              </div>
            </div>
            <div class="course-card-footer">
              <div class="course-actions">
               
                <button class="btn btn-sm btn-outline-info" onclick='showLessonPlan(${JSON.stringify(
                  course
                ).replace(/'/g, "\\'")})' title="Lesson Plan">
                  <i class="fas fa-book"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      availableCoursesContainer.find(".row").append(courseCard);
    });

    availableCoursesContainer.append("</div>"); // Close row div
  }
}

// timetable  start

let timetableData = {};
function getTimeTable() {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getTimeTable",
      batch: advisorData.batch,
      semester: advisorData.semester,
      section: advisorData.section,
      academicYear: advisorData.academicYear,
      dept: JSON.parse(sessionStorage.getItem("userData")).dept,
      id: JSON.parse(sessionStorage.getItem("userData")).id,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        if (data.timetable.length === 0) {
          timetableData = {
            Monday: Array(8).fill({ name: "...", teacher: "" }),
            Tuesday: Array(8).fill({ name: "...", teacher: "" }),
            Wednesday: Array(8).fill({ name: "...", teacher: "" }),
            Thursday: Array(8).fill({ name: "...", teacher: "" }),
            Friday: Array(8).fill({ name: "...", teacher: "" }),
          };
          editMode = true;
          timetableData = defaultTimetable;
        } else {
          console.log("Received timetable data:", data.timetable);
          timetableData = data.timetable;
          updateTimeTableButton(data.timetable_edit_status);
        }
      } else {
        console.error("Error fetching timetable:", data.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}
getTimeTable();

const timeSlots = [
  { label: "Hour 1", time: "08:45 AM - 09:45 AM" },
  { label: "Hour 2", time: "09:45 AM - 10:45 AM" },
  { label: "Hour 3", time: "11:00 AM - 12:00 PM" },
  { label: "Hour 4", time: "12:00 PM - 01:00 PM" },
  { label: "Hour 5", time: "01:55 PM - 02:45 PM" },
  { label: "Hour 6", time: "02:45 PM - 03:45 PM" },
  { label: "Hour 7", time: "03:45 PM - 04:45 PM" },
  { label: "Hour 8", time: "04:45 PM - 05:30 PM" },
];

function createTimetable() {
  const timetableGrid = document.getElementById("time-table");
  const grid = document.createElement("div");
  grid.className = "tt-grid";

  // First row: Hours as headers
  const headerRow = document.createElement("div");
  headerRow.className = "tt-row";

  // Empty cell for top-left corner
  const cornerCell = document.createElement("div");
  cornerCell.className = "tt-header-cell";
  cornerCell.textContent = "Day/Hours";
  headerRow.appendChild(cornerCell);

  // Add time slots as column headers
  timeSlots.forEach((slot) => {
    const timeCell = document.createElement("div");
    timeCell.className = "tt-header-cell";
    timeCell.innerHTML = `
            ${slot.label}<br>
            <span class="tt-time-slot">${slot.time}</span>
        `;
    headerRow.appendChild(timeCell);
  });

  grid.appendChild(headerRow);

  // Create a row for each day
  Object.entries(timetableData).forEach(([day, schedule]) => {
    const row = document.createElement("div");
    row.className = "tt-row";

    // Day cell
    const dayCell = document.createElement("div");
    dayCell.className = "tt-day-cell";
    dayCell.textContent = day;
    row.appendChild(dayCell);

    // Add course cells for each time slot
    schedule.forEach((period, timeIndex) => {
      const cell = document.createElement("div");
      cell.className = "tt-course-cell";
      cell.dataset.day = day;
      cell.dataset.index = timeIndex;

      let teacherHtml = "";
      if (Array.isArray(period.teacher)) {
        teacherHtml = period.teacher
          .map(
            (t) => `
            <div class="tt-teacher-name" style="
                font-size: 10px;
                color: #666;
                text-align: center;
                line-height: 1.2;
                width: 100%;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                word-break: break-word;
            ">${t.name}</div>
        `
          )
          .join("");
      } else if (period.teacher) {
        teacherHtml = `<div class="tt-teacher-name">${period.teacher}</div>`;
      }

      cell.innerHTML = `
    <div style="
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4px;
    ">
        <div style="
            font-size: 11px;
            color: #666;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            width: 100%;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-break: break-word;
        ">${period.name}</div>
        ${teacherHtml}
    </div>
`;

      cell.addEventListener("click", () => {
        const popupContent = document.createElement("div");
        popupContent.id = "coursePopup";
        popupContent.className = "course-popup";
        popupContent.style.cssText = `
          position: absolute;
          background: white;
          border: 1px solid #ddd;
          border-radius: 4px;
          padding: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.1);
          z-index: 1000;
          max-height: 250px;
          overflow-y: auto;
          width: 180px;
          display: ${editMode ? "block" : "none"};
        `;

        const coursesList = document.createElement("div");
        coursesList.className = "courses-list";

        advisorCourses.forEach((course) => {
          const courseItem = document.createElement("div");
          courseItem.className = "course-item";
          courseItem.style.cssText = `
            padding: 6px 8px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
            font-size: 12px;
            color: #666;
          `;

          // Create HTML for all faculty members
          const facultyHtml = course.faculty
            .map(
              (faculty) => `
                  <div style="font-size: 11px; color: #666;">
                      ${faculty.name} (${faculty.designation})
                  </div>
              `
            )
            .join("");

          courseItem.innerHTML = `
              <div style="margin-bottom: 2px;">${course.course_name}</div>
              <div class="faculty-list">${facultyHtml}</div>
          `;

          courseItem.addEventListener("mouseover", () => {
            courseItem.style.backgroundColor = "#f5f5f5";
          });

          courseItem.addEventListener("mouseout", () => {
            courseItem.style.backgroundColor = "white";
          });

          courseItem.addEventListener("click", () => {
            console.log("Clicked course:", course.faculty);
            timetableData[day][timeIndex] = {
              name: course.course_name,
              teacher: course.faculty, // Store entire faculty array
            };

            cell.style.cssText = `
                  min-height: 80px;
                  padding: 6px;
                  background: #ffffff;
                  border: 1px solid #e9ecef;
                  border-radius: 6px;
                  display: flex;
                  flex-direction: column;
                  justify-content: center;
                  align-items: center;
                  cursor: pointer;
                  transition: all 0.2s ease;
                  position: relative;
                  overflow: hidden;
              `;

            // Create HTML for cell display with all faculty members
            const cellFacultyHtml = course.faculty
              .map(
                (faculty) => `
                      <div style="
                          font-size: 10px;
                          color: #666;
                          text-align: center;
                          line-height: 1.2;
                          width: 100%;
                          overflow: hidden;
                          display: -webkit-box;
                          -webkit-line-clamp: 1;
                          -webkit-box-orient: vertical;
                          word-break: break-word;
                      ">${faculty.name}</div>
                  `
              )
              .join("");

            cell.innerHTML = `
                  <div style="
                      height: 100%;
                      width: 100%;
                      display: flex;
                      flex-direction: column;
                      align-items: center;
                      justify-content: center;
                      padding: 4px;
                      gap: 2px;
                  ">
                      <div style="
                          font-size: 11px;
                          color: #444;
                          font-weight: 500;
                          text-align: center;
                          line-height: 1.2;
                          width: 100%;
                          overflow: hidden;
                          display: -webkit-box;
                          -webkit-line-clamp: 2;
                          -webkit-box-orient: vertical;
                          word-break: break-word;
                      ">${course.course_name}</div>
                      <div class="faculty-list" style="
                          width: 100%;
                          display: flex;
                          flex-direction: column;
                          align-items: center;
                          gap: 1px;
                      ">${cellFacultyHtml}</div>
                  </div>
              `;

            removePopup();
          });

          coursesList.appendChild(courseItem);
        });

        popupContent.appendChild(coursesList);

        const cellRect = cell.getBoundingClientRect();
        popupContent.style.left = `${cellRect.left}px`;
        popupContent.style.top = `${cellRect.bottom + window.scrollY + 5}px`;

        // Remove existing popup if any
        removePopup();

        // Add new popup
        document.body.appendChild(popupContent);

        // Close popup when clicking outside
        document.addEventListener("click", function closePopup(e) {
          if (!popupContent.contains(e.target) && !cell.contains(e.target)) {
            removePopup();
            document.removeEventListener("click", closePopup);
          }
        });
      });

      row.appendChild(cell);
    });

    grid.appendChild(row);
  });

  timetableGrid.appendChild(grid);
}

// Add this helper function to safely remove popup
function removePopup() {
  const existingPopup = document.getElementById("coursePopup");
  if (existingPopup) {
    existingPopup.remove();
  }
}

// Add the generate timetable function
document
  .getElementById("generateTimeTableBtn")
  .addEventListener("click", function () {
    Swal.fire({
      title: "Generate Timetable",
      text: "Please be aware that once the timetable is generated, any further edits will require approval from the HOD. Do you wish to proceed?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, generate it!",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        const timetableRecords = [];
        const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

        days.forEach((day) => {
          const periods =
            timetableData[day] || Array(8).fill({ name: "...", teacher: "" });

          for (let periodIndex = 0; periodIndex < 8; periodIndex++) {
            const period = periods[periodIndex] || {
              name: "...",
              teacher: "",
            };

            if (period && period.name) {
              const course = advisorCourses.find(
                (c) => c.course_name.trim() === period.name.trim()
              );

              if (course) {
                // Ensure period.teacher is always an array and remove duplicates
                const teachers = Array.isArray(period.teacher)
                  ? period.teacher
                  : [period.teacher];
                const uniqueTeachers = teachers.filter(
                  (teacher, index, self) =>
                    index === self.findIndex((t) => t.id === teacher.id)
                );

                uniqueTeachers.forEach((teacher) => {
                  if (teacher && teacher.id) {
                    const record = {
                      course_id: parseInt(course.course_id),
                      faculty_id: parseInt(teacher.id),
                      day: day,
                      period: periodIndex + 1,
                      batch: course.batch,
                      academic_year: course.academic_year,
                      semester: parseInt(course.semester),
                      section: course.section,
                    };
                    timetableRecords.push(record);
                  }
                });
              }
            }
          }
        });

        // Send data to backend
        if (timetableRecords.length > 0) {
          console.log("Sending timetable records to backend", timetableRecords);
          $.ajax({
            url: "backend.php",
            type: "POST",
            data: {
              action: "saveTimetable",
              timetable: timetableRecords,
            },
            success: function (response) {
              try {
                const result = JSON.parse(response);
                if (result.status === "success") {
                  Swal.fire({
                    title: "Success!",
                    text: "Timetable saved successfully!",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                  }).then(() => {
                    getTimeTable();
                  });
                } else if (result.status === "alert") {
                  Swal.fire({
                    title: "Alert!",
                    text: result.message,
                    icon: "info",
                  });
                } else {
                  Swal.fire({
                    title: "Error!",
                    text: "Error saving timetable: " + result.message,
                    icon: "error",
                  });
                  console.error("Error:", result.message);
                }
              } catch (e) {
                Swal.fire({
                  title: "Error!",
                  text: "Error processing response",
                  icon: "error",
                });
                console.error("Error parsing response:", e);
              }
            },
            error: function (xhr, status, error) {
              Swal.fire({
                title: "Error!",
                text: "Error saving timetable",
                icon: "error",
              });
              console.error("Ajax error:", error);
            },
          });
        } else {
          Swal.fire({
            title: "Warning!",
            text: "No courses to save in timetable",
            icon: "warning",
          });
        }
      }
    });
  });

// save the timetable
document
  .getElementById("saveTimeTableBtn")
  .addEventListener("click", function () {
    const timetableRecords = [];
    console.log("New Timetable Data:", timetableData);
    const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    days.forEach((day) => {
      const periods =
        timetableData[day] || Array(8).fill({ name: "...", teacher: "" });

      for (let periodIndex = 0; periodIndex < periods.length; periodIndex++) {
        const period = periods[periodIndex];

        if (period && period.name) {
          const course = advisorCourses.find(
            (c) => c.course_name.trim() === period.name.trim()
          );

          if (course) {
            const facultyIds = Array.isArray(period.teacher)
              ? period.teacher.map((faculty) => faculty.id)
              : [period.teacher.id];
            facultyIds.forEach((facultyId) => {
              if (!isNaN(facultyId) && facultyId) {
                const record = {
                  timetable_id: period.timetable_id || null,
                  course_id: parseInt(course.course_id),
                  faculty_id: parseInt(facultyId),
                  day: day,
                  period: periodIndex + 1,
                  batch: course.batch,
                  academic_year: course.academic_year,
                  semester: parseInt(course.semester),
                  section: course.section,
                };
                //localhost:7000/faculty.php
                http: timetableRecords.push(record);
              } else {
                console.error(
                  `Invalid faculty ID for period ${periodIndex + 1} on ${day}:`,
                  facultyId
                );
              }
            });
          }
        }
      }
    });

    if (timetableRecords.length > 0) {
      console.log(
        "Sending edit timetable records to backend",
        timetableRecords
      );
      $.ajax({
        url: "backend.php",
        type: "POST",
        data: {
          action: "editTimeTable",
          timetable: timetableRecords,
        },
        success: function (response) {
          try {
            const result = JSON.parse(response);
            if (result.status === "success") {
              Swal.fire({
                title: "Success!",
                text: "Timetable saved successfully!",
                icon: "success",
                confirmButtonText: "OK",
                timer: 2000,
              }).then(() => {
                editTimeTable("None");
                getTimeTable();
                document.getElementById("editTimeTableBtn").style.display =
                  "block";
              });
            }else if (result.status === "alert") {
              Swal.fire({
                title: "Alert!",
                text: result.message,
                icon: "info",
              });
            } else {
              Swal.fire({
                title: "Error!",
                text: "Error saving timetable: " + result.message,
                icon: "error",
              });
              console.error("Error:", result.message);
            }
          } catch (e) {
            Swal.fire({
              title: "Error!",
              text: "Error processing response",
              icon: "error",
            });
            console.error("Error parsing response:", e);
          }
        },
        error: function (xhr, status, error) {
          Swal.fire({
            title: "Error!",
            text: "Error saving timetable",
            icon: "error",
          });
          console.error("Ajax error:", error);
        },
      });
    } else {
      Swal.fire({
        title: "Warning!",
        text: "No courses to save in timetable",
        icon: "warning",
      });
    }
  });

// timetable end
function showLessonPlan(course) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getLessonPlanData",
      courseId: course.course_id,
    },
    success: function (response) {
      console.log("Raw Response:", response); // Log the raw response

      try {
        const result = JSON.parse(response);
        console.log("Parsed Result:", result); // Log the parsed result

        if (result.status === "success") {
          console.log("Units and Topics:", result.data); // Log the data

          // Check if we have data
          if (result.data && result.data.length > 0) {
            console.log("Has data - showing lesson plan view");
            const availableCoursesContainer = $("#available-courses");
            availableCoursesContainer.empty();

            // Generate unit cards HTML
            const unitsHTML = result.data
              .map(
                (unit, index) => `
              <div class="unit-card mb-4">
                <div class="unit-header p-3 bg-light border-start border-5 border-primary rounded">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="unit-title-section">
                      <div class="d-flex align-items-baseline">
                        <h5 class="mb-0 text-uppercase fw-bold">
                          <span class="unit-number me-3">UNIT ${String(
                            index + 1
                          )}</span>
                          <span class="unit-name">${unit.name}</span>
                        </h5>
                        
                      </div>
                      <div class="unit-topics mt-3">
                        ${unit.topics
                          .map((topic) => topic.name)
                          .join(", ")
                          .split(", ")
                          .join(" - ")}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            `
              )
              .join("");
            function getLessonPlanButtonAttributes(status) {
              // Convert status to lower case and default to "none" if not set.
              status = (status || "none").toLowerCase();
              if (status === "none") {
                return { text: "Request to Hod", btnClass: "btn-danger" };
              } else if (status === "pending") {
                return { text: "Requested to Hod", btnClass: "btn-warning" };
              } else if (status === "approved") {
                return { text: "Edit", btnClass: "btn-primary" };
              } else if (status === "editing") {
                return { text: "Save Changes", btnClass: "btn-success" };
              } else {
                return { text: "Edit Lesson Plan", btnClass: "btn-primary" };
              }
            }

            // Compute the button attributes based on the course status.
            var btnAttributes = getLessonPlanButtonAttributes(
              course.lessonplan_edit_status
            );

            const lessonPlanView = `
              <div class="container-fluid py-4">
                <div class="card shadow-sm">
                  <div class="card-body">
                    <div class="lesson-plan-container">
                      <!-- Header Section -->
                      <div class="lesson-plan-header mb-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 rounded" 
                             style="background: linear-gradient(135deg, #4CAF50, #2196F3);">
                          <div class="course-info">
                            <h4 class="mb-0 fw-bold text-white">${course.course_name}</h4>
                          </div>
                          <div>
                            <button id="edit-lesson-plan-btn" class="btn edit-lesson-plan-btn ${btnAttributes.btnClass}" data-course-id="${course.course_id}">
                          <i class="fas fa-edit"></i> ${btnAttributes.text}
                        </button>
                              <button class="btn btn-light me-2" onclick="backToAvailableCourses()">
                              <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Units Section -->
                      <div class="units-container">
                        ${unitsHTML}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            `;

            availableCoursesContainer.html(lessonPlanView);

            // Update the styles
            const styles = `
              <style data-lesson-plan-styles>
                .unit-card {
                  transition: transform 0.2s;
                }
                .unit-card:hover {
                  transform: translateY(-2px);
                }
                .unit-number {
                  color: #495057;
                  font-weight: 600;
                  font-size: 1rem;
                }
                .unit-name {
                  color: #212529;
                  font-size: 1rem;
                }
                .periods-badge {
                  background: #e9ecef;
                  padding: 2px 8px;
                  border-radius: 4px;
                  font-size: 0.875rem;
                  color: #495057;
                }
                .unit-topics {
                  color: #6c757d;
                  font-size: 1rem;
                  line-height: 1.6;
                  margin-top: 0.5rem;
                  padding-top: 0.5rem;
                  border-top: 1px dashed #dee2e6;
                  font-weight: 500;
                }
                .lesson-plan-header h4 {
                  color: #2c3e50;
                }
                .unit-title-section {
                  flex: 1;
                }
              </style>
            `;

            // Append styles if not already present
            if (!document.querySelector("style[data-lesson-plan-styles]")) {
              $("head").append(styles);
            }
          } else {
            console.log("No data - showing lesson plan form");
            const availableCoursesContainer = $("#available-courses");
            availableCoursesContainer.empty();

            const lessonPlanForm = `
              <div class="container-fluid py-4">
                <div class="card shadow-sm">
                  <div class="card-body">
                    <div class="lesson-plan-container">
                      <!-- Header Section -->
                      <div class="lesson-plan-header mb-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 rounded" 
                             style="background: linear-gradient(135deg, #4CAF50, #2196F3);">
                          <div class="course-info">
                            <h4 class="mb-0 fw-bold text-white">${
                              course.course_name
                            }</h4>
                            <p class="mb-0 text-white" style="opacity: 0.9">
                              <span class="badge bg-white text-primary me-2" style="font-size: 1rem">${
                                course.course_code
                              }</span>
                              Total Periods: 45
                            </p>
                          </div>
                          <div>
                            <button class="btn btn-light" onclick="backToAvailableCourses()">
                            <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Lesson Plan Form -->
                      <form id="lessonPlanForm">
                        <input type="hidden" id="courseId" value="${
                          course.course_id
                        }">
                        
                        <!-- Units Container -->
                        <div id="unitsContainer">
                          <!-- First unit will be added by default -->
                          <div class="unit-section p-4 mb-4 bg-white rounded-3 shadow-sm" data-unit="1">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                              <div class="d-flex align-items-center gap-2">
                                <span class="unit-number">01</span>
                                <h5 class="section-title mb-0">Unit Details</h5>
                              </div>
                            </div>
                            
                            <div class="row g-4">
                              <div class="col-md-3">
                                <div class="form-floating">
                                  <input type="text" class="form-control" id="unit1_name" name="unit1_name" placeholder="Enter unit name" required>
                                  <label for="unit1_name">Unit Name</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-floating">
                                  <select class="form-select" id="unit1_co" name="unit1_co" required onchange="handleCOChange(1, this)">
                                    ${generateCOOptions(1)}
                                  </select>
                                  <label for="unit1_co">Course Outcome</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-floating">
                                  <input type="number" class="form-control" id="unit1_weightage" name="unit1_weightage" placeholder="Enter CO weightage" min="0" max="100" required>
                                  <label for="unit1_weightage">CO Weightage</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-floating">
                                  <input type="number" class="form-control" id="co_threshold" name="co_threshold" placeholder="Enter CO weightage" min="0" max="100" required>
                                  <label for="co_threshold">CO Threshold</label>
                                </div>
                              </div>
                              
                              <div class="col-12">
                                <div class="topics-container" data-unit="1">
                                  <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                      <i class="fas fa-list-ul text-primary"></i>
                                      <h5 class="section-title mb-0">Topics</h5>
                                    </div>
                                  </div>
                                  <div class="topic-fields row g-3">
                                    <div class="col-md-6 mb-3">
                                      <div class="form-floating topic-input-group position-relative">
                                        <input type="text" class="form-control" id="unit1_topic1" name="unit1_topic1" placeholder="Enter topic" required>
                                        <label for="unit1_topic1">Topic 1</label>
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2" onclick="removeTopic(this)">
                                          <i class="fas fa-times"></i>
                                        </button>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="text-center mt-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm ripple-btn" onclick="addTopic(1)">
                                      <i class="fas fa-plus me-2"></i>Add Topic
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Add Unit Button -->
                        <div class="text-center mb-4">
                          <button type="button" class="btn btn-outline-success btn-sm ripple-btn" onclick="addUnit()">
                            <i class="fas fa-plus me-2"></i>Add Unit
                          </button>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end">
                          <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Lesson Plan
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            `;

            availableCoursesContainer.html(lessonPlanForm);

            // Initialize form handlers
            initializeLessonPlanHandlers();
          }
        } else {
          console.error("Error in response:", result.message);
          Swal.fire("Error", result.message || "Failed to load data", "error");
        }
      } catch (e) {
        console.error("Parse Error:", e);
        console.error("Response that failed to parse:", response);
        Swal.fire("Error", "Failed to process response", "error");
      }
    },
    error: function (xhr, status, error) {
      console.error("Ajax Error Details:", {
        status: status,
        error: error,
        responseText: xhr.responseText,
      });
      Swal.fire("Error", "Failed to fetch lesson plan data", "error");
    },
  });
}
function requestLessonPlanEdit(courseId, status) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    dataType: "json",
    data: {
      action: "requestLessonPlanEdit",
      courseId: courseId,
      status: status,
    },
    success: function (resp) {
      if (resp.status === "success") {
        Swal.fire("Success", resp.message, "success");
        // reload the page
        location.reload();
      } else {
        Swal.fire(
          "Error",
          resp.message || "Failed to send edit request",
          "error"
        );
      }
    },
    error: function () {
      Swal.fire("Error", "Failed to send edit request", "error");
    },
  });
}
$(document).on("click", ".edit-lesson-plan-btn", function () {
  const $btn = $(this);
  const currentText = $btn.text().trim();

  if (currentText === "Request to Hod") {
    Swal.fire({
      title: "Confirm Request",
      text: "Are you sure you want to request to edit this lesson plan?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, send request",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        requestLessonPlanEdit($btn.data("course-id"), "Pending");
      }
    });
  } else if (currentText === "Requested to Hod") {
    Swal.fire("Info", "Your request is already pending with the HoD.", "info");
  } else if (currentText === "Edit") {
    editLessonPlan($btn.data("course-id"));
  } else if (currentText === "Edit") {
    console.log("Proceed to edit lesson plan.");
  }
});

// Initialize form handlers
function initializeLessonPlanHandlers() {
  $("#lessonPlanForm").on("submit", function (e) {
    e.preventDefault();

    // Collect all unit and topic data
    const units = [];
    $(".unit-section").each(function () {
      const unitNumber = $(this).data("unit");
      const unitName = $(this)
        .find(`input[name="unit${unitNumber}_name"]`)
        .val();
      const CO = $(this).find(`select[name="unit${unitNumber}_co"]`).val();
      const weightage = $(this)
        .find(`input[name="unit${unitNumber}_weightage"]`)
        .val();

      const co_threshold = $(this).find(`input[name="co_threshold"]`).val();

      const topics = [];
      $(this)
        .find('.topic-fields input[type="text"]')
        .each(function () {
          const topicValue = $(this).val();
          if (topicValue) {
            // Only add if there's a value
            topics.push({
              name: topicValue,
            });
          }
        });

      units.push({
        unitNumber: unitNumber,
        name: unitName,
        CO: CO,
        weightage: weightage,
        co_threshold: co_threshold,
        topics: topics,
      });
    });

    const formData = {
      courseId: $("#courseId").val(),
      units: units,
    };

    // Show loading state
    Swal.fire({
      title: "Saving...",
      text: "Please wait while we save your lesson plan",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Send to backend
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "saveLessonPlan",
        formData: JSON.stringify(formData),
      },
      success: function (response) {
        try {
          const result = JSON.parse(response);
          if (result.status === "success") {
            Swal.fire({
              title: "Success!",
              text: "Lesson plan saved successfully!",
              icon: "success",
              confirmButtonText: "OK",
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload(); // Reload the page to show updated data
              }
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: result.message || "Failed to save lesson plan",
              icon: "error",
            });
          }
        } catch (e) {
          console.error("Error parsing response:", e);
          Swal.fire({
            title: "Error!",
            text: "Error processing server response",
            icon: "error",
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Ajax error:", error);
        Swal.fire({
          title: "Error!",
          text: "Failed to save lesson plan: " + error,
          icon: "error",
        });
      },
    });
  });
}

function addUnit() {
  const unitsContainer = document.getElementById("unitsContainer");
  const unitSections = unitsContainer.querySelectorAll(".unit-section");
  const newUnitNumber = unitSections.length + 1;

  const newUnitHtml = `
    <div class="unit-section p-4 mb-4 bg-white rounded-3 shadow-sm" data-unit="${newUnitNumber}">
      <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div class="d-flex align-items-center gap-2">
          <span class="unit-number">${String(newUnitNumber).padStart(
            2,
            "0"
          )}</span>
          <h5 class="section-title mb-0">Unit Details</h5>
        </div>
        ${
          newUnitNumber > 1
            ? `
          <button type="button" class="btn btn-danger btn-sm ripple-btn" onclick="removeUnit(this)">
            <i class="fas fa-trash me-2"></i>Remove Unit
          </button>
        `
            : ""
        }
      </div>
      
      <div class="row g-4">
        <div class="col-md-3">
          <div class="form-floating">
            <input type="text" class="form-control" id="unit${newUnitNumber}_name" name="unit${newUnitNumber}_name" placeholder="Enter unit name" required>
            <label for="unit${newUnitNumber}_name">Unit Name</label>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-floating">
            <select class="form-select" id="unit${newUnitNumber}_co" name="unit${newUnitNumber}_co" required onchange="handleCOChange(${newUnitNumber}, this)">
              ${generateCOOptions(newUnitNumber)}
            </select>
            <label for="unit${newUnitNumber}_co">Course Outcome</label>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-floating">
            <input type="number" class="form-control" id="unit${newUnitNumber}_weightage" name="unit${newUnitNumber}_weightage" placeholder="Enter CO weightage" min="0" max="100" required>
            <label for="unit${newUnitNumber}_weightage">CO Weightage (%)</label>
          </div>
        </div>
         <div class="col-md-3">
                                <div class="form-floating">
                                  <input type="number" class="form-control" id="co_threshold" name="co_threshold" placeholder="Enter CO weightage" min="0" max="100" required>
                                  <label for="co_threshold">CO Threshold</label>
                                </div>
                              </div>
        
        <div class="col-12">
          <div class="topics-container" data-unit="${newUnitNumber}">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
              <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list-ul text-primary"></i>
                <h5 class="section-title mb-0">Topics</h5>
              </div>
            </div>
            <div class="topic-fields row g-3">
              <div class="col-md-6 mb-3">
                <div class="form-floating topic-input-group position-relative">
                  <input type="text" class="form-control" id="unit${newUnitNumber}_topic1" 
                         name="unit${newUnitNumber}_topic1" placeholder="Enter topic" required>
                  <label for="unit${newUnitNumber}_topic1">Topic 1</label>
                  <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2" onclick="removeTopic(this)">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="text-center mt-3">
              <button type="button" class="btn btn-outline-primary btn-sm ripple-btn" onclick="addTopic(${newUnitNumber})">
                <i class="fas fa-plus me-2"></i>Add Topic
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Add the new unit
  unitsContainer.insertAdjacentHTML("beforeend", newUnitHtml);

  // Remove any existing "Add Unit" button container
  const existingAddUnitContainer = document.querySelector(
    "#unitsContainer + .text-center.mb-4"
  );
  if (existingAddUnitContainer) {
    existingAddUnitContainer.remove();
  }

  // Add the "Add Unit" button after all units
  const addUnitButtonHtml = `
    <div class="text-center mb-4">
      <button type="button" class="btn btn-outline-success btn-sm ripple-btn" onclick="addUnit()">
        <i class="fas fa-plus me-2"></i>Add Unit
      </button>
    </div>
  `;
  unitsContainer.insertAdjacentHTML("afterend", addUnitButtonHtml);
}

function addTopic(unitNumber) {
  const topicsContainer = document.querySelector(
    `.topics-container[data-unit="${unitNumber}"] .topic-fields`
  );
  const topicCount = topicsContainer.children.length + 1;

  const newTopicHtml = `
    <div class="col-md-6 mb-3">
      <div class="form-floating topic-input-group position-relative">
        <input type="text" class="form-control" id="unit${unitNumber}_topic${topicCount}" 
               name="unit${unitNumber}_topic${topicCount}" placeholder="Enter topic" required>
        <label for="unit${unitNumber}_topic${topicCount}">Topic ${topicCount}</label>
        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2" onclick="removeTopic(this)">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
  `;

  topicsContainer.insertAdjacentHTML("beforeend", newTopicHtml);
}

// Remove unit
function removeUnit(button) {
  const unitSection = button.closest(".unit-section");
  const unitNumber = parseInt(unitSection.getAttribute("data-unit"));

  // Return CO to available pool if one was selected
  const removedCO = selectedCOs.get(unitNumber);
  if (removedCO) {
    availableCOs.push(removedCO);
    availableCOs.sort();
    selectedCOs.delete(unitNumber);
  }

  unitSection.remove();

  // Renumber remaining units and update their CO dropdowns
  const remainingUnits = document.querySelectorAll(".unit-section");
  remainingUnits.forEach((unit, index) => {
    const newUnitNumber = index + 1;
    unit.setAttribute("data-unit", newUnitNumber);
    // Update other unit elements (number, IDs, etc.)
    // ... existing renumbering code ...

    // Update CO select
    const coSelect = unit.querySelector('select[name^="unit"][name$="_co"]');
    coSelect.id = `unit${newUnitNumber}_co`;
    coSelect.name = `unit${newUnitNumber}_co`;
    coSelect.setAttribute("onchange", `handleCOChange(${newUnitNumber}, this)`);
  });

  updateAllCODropdowns();
}

// Remove topic
function removeTopic(button) {
  const topicContainer = button.closest(".col-md-6");
  if (topicContainer) {
    // Get the parent topics container
    const topicsContainer = topicContainer.closest(".topic-fields");

    // Remove the topic
    topicContainer.remove();

    // Renumber remaining topics
    const remainingTopics = topicsContainer.querySelectorAll(".col-md-6");
    remainingTopics.forEach((topic, index) => {
      const input = topic.querySelector("input");
      const label = topic.querySelector("label");
      const unitNumber = topic
        .closest(".unit-section")
        .getAttribute("data-unit");
      const newTopicNumber = index + 1;

      // Update input attributes
      input.id = `unit${unitNumber}_topic${newTopicNumber}`;
      input.name = `unit${unitNumber}_topic${newTopicNumber}`;

      // Update label
      label.setAttribute("for", `unit${unitNumber}_topic${newTopicNumber}`);
      label.textContent = `Topic ${newTopicNumber}`;
    });
  }
}

function backToAvailableCourses() {
  const availableCoursesContainer = $("#available-courses");
  availableCoursesContainer.html(`
    <div class="container mt-4">
      <!-- Available Courses will be loaded here -->
    </div>
  `);

  // Call the function to load advisor courses
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAdvisorCourses",
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          renderCourseCards(data.courses);
        } else {
          console.error("Error:", data.message);
          const container = $("#available-courses .container.mt-4");
          container.html(`
            <div class="alert alert-danger text-center p-4 rounded-3 shadow-sm">
              <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
              <h4>Error Loading Courses</h4>
              <p class="mb-0">${data.message}</p>
            </div>
          `);
        }
      } catch (e) {
        console.error("Error parsing response:", e);
        const container = $("#available-courses .container.mt-4");
        container.html(`
          <div class="alert alert-danger text-center p-4 rounded-3 shadow-sm">
            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
            <h4>Error</h4>
            <p class="mb-0">Failed to load course data. Please try again later.</p>
          </div>
        `);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      const container = $("#available-courses .container.mt-4");
      container.html(`
        <div class="alert alert-danger text-center p-4 rounded-3 shadow-sm">
          <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
          <h4>Network Error</h4>
          <p class="mb-0">Failed to connect to the server. Please check your connection and try again.</p>
        </div>
      `);
    },
  });
}

// Add this new function to handle viewing topics
function viewTopics(topics) {
  const topicsList = topics
    .map((topic) => `<li class="list-group-item">${topic.name}</li>`)
    .join("");

  Swal.fire({
    title: "Unit Topics",
    html: `
      <div class="text-start">
        <ul class="list-group">
          ${topicsList}
        </ul>
      </div>
    `,
    width: "500px",
    confirmButtonText: "Close",
    showClass: {
      popup: "animate__animated animate__fadeIn",
    },
  });
}

$("#fileUpload").on("change", function () {
  const file = this.files[0];
  const submitButton = $('button[type="submit"]');

  if (file) {
    submitButton
      .prop("disabled", false)
      .css("background-color", "#ff9800") // Orange background
      .css("border-color", "#ff9800")
      .css("color", "white");
  } else {
    submitButton
      .prop("disabled", true)
      .css("background-color", "") // Reset to default
      .css("border-color", "")
      .css("color", "");
  }
});
// Handle form submission for bulk import
$("#bulkImportForm").on("submit", function (e) {
  e.preventDefault();
  const file = $("#fileUpload")[0].files[0];
  if (!file) {
    Swal.fire({
      title: "Error!",
      text: "Please select a file first",
      icon: "error",
    });
    return;
  }
  const reader = new FileReader();
  reader.onload = function (e) {
    const data = new Uint8Array(e.target.result);
    const workbook = XLSX.read(data, { type: "array" });
    // Get both sheet names
    const sheetNames = workbook.SheetNames;
    if (sheetNames.length < 2) {
      Swal.fire({
        title: "Error!",
        text: "Excel file must contain at least 2 sheets",
        icon: "error",
      });
      return;
    }
    // Get course details from sheet 1
    const courseDetails = XLSX.utils.sheet_to_json(
      workbook.Sheets[sheetNames[0]]
    );
    console.log("Course Details:", courseDetails);
    // Get mapping data from sheet 2
    const mappingData = XLSX.utils.sheet_to_json(
      workbook.Sheets[sheetNames[1]]
    );
    console.log("Mapping Data:", mappingData);
    // Group mappings by course code
    const courseGroups = {};
    mappingData.forEach((mapping) => {
      const courseCode = mapping["CourseCode"] || mapping["Course Code"];
      if (!courseGroups[courseCode]) {
        courseGroups[courseCode] = [];
      }
      courseGroups[courseCode].push(mapping);
    });
    // Process each course
    Object.entries(courseGroups).forEach(([courseCode, courseMappings]) => {
      // Find course details - Fixed comparison
      const courseDetail =
        courseDetails.find(
          (c) => c.CourseCode === courseCode || c["Course Code"] === courseCode
        ) || {};

      // Create course data object
      const courseData = {
        Department: courseDetail.Department || $("#department").val() || "BCB",
        Batch: courseDetail.Batch || $("#batch").val(),
        AcademicYear: courseDetail.AcademicYear || $("#academicYear").val(),
        Semester: courseDetail.Semester || $("#semester").val(),
        CourseName: courseDetail.CourseName || $("#courseName").val(),
        CourseCode: courseDetail.CourseCode || courseDetail["Course Code"],
        CourseCredit: courseDetail.CourseCredit || $("#courseCredit").val(),
        CourseType: courseDetail.CourseType || $("#courseType").val(),
        Section: courseDetail.Section || $("#section").val() || "A",
      };
      // First, fetch all faculty and student UIDs for this course
      $.ajax({
        url: "backend.php",
        type: "POST",
        data: {
          action: "getFacultyStudentUIDs",
          mappingData: JSON.stringify(courseMappings),
        },
        success: function (response) {
          try {
            const result = JSON.parse(response);
            if (result.status === "success") {
              const mappings = result.mappings;
              const facultyStudentMap = {};

              mappings.forEach((mapping) => {
                if (!facultyStudentMap[mapping.faculty_uid]) {
                  facultyStudentMap[mapping.faculty_uid] = [];
                }
                facultyStudentMap[mapping.faculty_uid].push(
                  mapping.student_uid
                );
              });
              const formData = {
                action: "mapStudentsToFaculty",
                batch: courseData.Batch,
                academicYear: courseData.AcademicYear,
                semester: courseData.Semester,
                department: courseData.Department,
                courseName: courseData.CourseName,
                courseCode: courseData.CourseCode,
                courseCredit: courseData.CourseCredit,
                courseType: courseData.CourseType,
                section: courseData.Section,
                facultyStudentMap: facultyStudentMap,
              };
              console.log(`Form Data for ${courseCode}:`, formData);
              // Send the mapping data for this course
              $.ajax({
                url: "backend.php",
                type: "POST",
                data: formData,
                success: function (response) {
                  try {
                    const result = JSON.parse(response);
                    if (result.status === "success") {
                      Swal.fire({
                        title: "Success!",
                        text: `Mappings created successfully for ${courseCode}`,
                        icon: "success",
                      });
                    } else {
                      throw new Error(
                        `Failed to create mappings for ${courseCode}: ${result.message}`
                      );
                    }
                  } catch (e) {
                    Swal.fire({
                      title: "Error!",
                      text: e.message,
                      icon: "error",
                    });
                  }
                },
                error: function (xhr, status, error) {
                  Swal.fire({
                    title: "Error!",
                    text: `Failed to create mappings for ${courseCode}. Please try again.`,
                    icon: "error",
                  });
                },
              });
            } else {
              throw new Error(result.message);
            }
          } catch (e) {
            Swal.fire({
              title: "Error!",
              text: `Failed to process data for ${courseCode}: ${e.message}`,
              icon: "error",
            });
          }
        },
      });
    });
  };
  reader.onerror = function () {
    Swal.fire({
      title: "Error!",
      text: "Failed to read the file",
      icon: "error",
    });
  };
  reader.readAsArrayBuffer(file);
});

// Add event listener for the Modify Day Order button
document
  .querySelector(".dayorder-card .btn-success")
  .addEventListener("click", function () {
    document.getElementById("academic-cards-view").style.display = "none";
    document.getElementById("dayorder-modification-view").style.display =
      "block";
  });

// Add event listener for the back button
document
  .querySelector("#back-to-cards-btn")
  .addEventListener("click", function () {
    document.getElementById("dayorder-modification-view").style.display =
      "none";

    document.getElementById("academic-cards-view").style.display = "block";
  });

// Faculty Change Form Handling
// Day Order Change Form Handling
document
  .getElementById("dayChangeDate")
  .addEventListener("change", function (e) {
    const selectedDate = new Date(this.value);
    const dayOfWeek = selectedDate.getDay();
    const warningElement = document.getElementById("nonSaturdayWarning");

    // Check if it's NOT a Saturday (6)
    if (dayOfWeek !== 6) {
      warningElement.style.display = "block";
      this.value = ""; // Reset the date input

      Swal.fire({
        title: "Invalid Date Selection",
        text: "Only Saturdays can be selected for day order changes.",
        icon: "warning",
        confirmButtonText: "OK",
      });
    } else {
      warningElement.style.display = "none";
    }
  });

// Form Validation and Submission
document
  .getElementById("dayOrderChangeForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const date = document.getElementById("dayChangeDate").value;
    const newDayOrder = document.getElementById("newDayOrder").value;

    // Get advisor data from session storage
    const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

    if (!advisorData) {
      Swal.fire({
        title: "Error",
        text: "Advisor session data not found",
        icon: "error",
      });
      return;
    }

    if (!date || !newDayOrder) {
      Swal.fire({
        title: "Incomplete Form",
        text: "Please fill in all required fields",
        icon: "error",
      });
      return;
    }

    // Verify selected date is a Saturday
    const selectedDate = new Date(date);
    if (selectedDate.getDay() !== 6) {
      Swal.fire({
        title: "Invalid Date",
        text: "Only Saturdays can be selected for day order changes.",
        icon: "error",
      });
      return;
    }

    // Show loader
    $("#loader").show();

    // Prepare data for submission
    const formData = {
      action: "saveDayOrderOverride",
      override_date: date,
      assigned_day: newDayOrder,
      batch: advisorData.batch,
      academic_year: advisorData.academicYear,
      semester: advisorData.semester,
      section: advisorData.section,
    };

    // Submit via AJAX
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: formData,
      success: function (response) {
        $("#loader").hide();
        try {
          const result = JSON.parse(response);
          if (result.status === "success") {
            Swal.fire({
              title: "Success!",
              text: `Saturday day order has been changed successfully. ${result.sessions_created} attendance sessions created.`,
              icon: "success",
            }).then(() => {
              // Reset form
              document.getElementById("dayOrderChangeForm").reset();
            });
          } else {
            Swal.fire({
              title: "Error",
              text: result.message || "Failed to save day order change",
              icon: "error",
            });
          }
        } catch (e) {
          Swal.fire({
            title: "Error",
            text: "Invalid response from server",
            icon: "error",
          });
        }
      },
      error: function (xhr, status, error) {
        $("#loader").hide();
        Swal.fire({
          title: "Error",
          text: "Failed to save day order change: " + error,
          icon: "error",
        });
      },
    });
  });

function loadFacultyMarkingStatus(date) {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getFacultyMarkingStatus",
      date: date,
      batch: advisorData.batch,
      section: advisorData.section,
      semester: advisorData.semester,
      academicYear: advisorData.academicYear,
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          displayFacultyMarkingStatus(data.periods);
          displayStudentAttendance(data.students);
        } else {
          Swal.fire({
            title: "Error",
            text: data.message || "Failed to load attendance data",
            icon: "error",
          });
        }
      } catch (e) {
        console.error("Parse error:", e);
        console.log("Response that failed to parse:", response);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", { xhr, status, error });
    },
  });
}

function displayFacultyMarkingStatus(periods) {
  console.log("periods", periods);
  const tbody = $("#facultyMarkingStatus");
  tbody.empty();
  const summaryThead = $("#attendanceHeader");
  summaryThead.empty(); // Clear existing headers
  summaryThead.append(`<th>Status</th>`); // First column for Status
  periods.forEach((period) => {
    summaryThead.append(`<th>Hour ${period.period}</th>`);
  });

  periods.forEach((period) => {
    let statusBadge = "";
    // If the period has an "is_free" flag, keep that (if applicable)
    if (period.is_free) {
      statusBadge = '<span class="badge bg-secondary">Free Period</span>';
    } else {
      // Use session_status to determine the badge (convert string to integer if needed)
      const statusCode = parseInt(period.session_status, 10);
      switch (statusCode) {
        case 0:
        case 4:
          statusBadge = '<span class="badge bg-warning">Not Marked</span>';
          break;
        case 1:
          statusBadge = '<span class="badge bg-success">Marked</span>';
          break;
        case 2:
          statusBadge = '<span class="badge bg-info">Holiday</span>';
          break;
        case 3:
          statusBadge =
            '<span class="badge bg-primary">Special Attendance</span>';
          break;
        default:
          statusBadge = '<span class="badge bg-secondary">Unknown</span>';
      }
    }

    tbody.append(`
      <tr>
          <td>Hour ${period.period}</td>
          <td>${period.faculty_name || "-"}</td>
          <td>${period.course_name || "-"}</td>
          <td>${statusBadge}</td>
      </tr>
    `);
  });

  // Section Attendance Summary Table
  const summaryTbody = $("#sectionAttendanceSummary");
  summaryTbody.empty();

  const statusRows = [
    { label: "Present", class: "success", icon: "check-circle" },
    { label: "Absent", class: "danger", icon: "times-circle" },
    { label: "OD", class: "info", icon: "building" },
    { label: "Leave", class: "warning", icon: "calendar-minus" },
  ];

  statusRows.forEach((status) => {
    let row = `<tr>
      <td>
          <span class="badge bg-${status.class}">
              <i class="fas fa-${status.icon} me-1"></i>${status.label}
          </span>
      </td>`;
    row += periods
      .map((p) => {
        let cellContent = "";
        const statusCode = parseInt(p.session_status, 10);
        // For Not Marked (0 & 4)
        if (statusCode === 0 || statusCode === 4) {
          cellContent = '<span class="text-muted">Not Marked</span>';
        }
        // For Holiday (2)
        else if (statusCode === 2) {
          cellContent = '<span class="badge bg-info">Holiday</span>';
        }
        // For Marked (1) or Special Attendance (3)
        else if ((statusCode === 1 || statusCode === 3) && p.summary) {
          const key = status.label.toLowerCase() + "_count";
          cellContent = p.summary[key] || "0";
        } else {
          cellContent = '<span class="text-muted">N/A</span>';
        }
        return `<td class="text-center">${cellContent}</td>`;
      })
      .join("");
    row += "</tr>";
    summaryTbody.append(row);
  });
}

function displayStudentAttendance(studentData) {
  // Destroy the existing DataTable (if initialized) to reinitialize with new data
  if ($.fn.DataTable.isDataTable("#studentWiseAttendance")) {
    $("#studentWiseAttendance").DataTable().clear().destroy();
  }

  // Initialize DataTable with the studentData
  $("#studentWiseAttendance").DataTable({
    data: studentData.data,
    columns: [
      { title: "Roll No", data: "roll_no", className: "text-center" },
      { title: "Name", data: "sname", className: "text-center" },
      {
        title: "Hour 1",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[1]);
        },
      },
      {
        title: "Hour 2",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[2]);
        },
      },
      {
        title: "Hour 3",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[3]);
        },
      },
      {
        title: "Hour 4",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[4]);
        },
      },
      {
        title: "Hour 5",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[5]);
        },
      },
      {
        title: "Hour 6",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[6]);
        },
      },
      {
        title: "Hour 7",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[7]);
        },
      },
      {
        title: "Hour 8",
        data: "hours",
        className: "text-center",
        render: function (data, type, row) {
          return getStatusHtml(row.hours[8]);
        },
      },
    ],
    paging: true,
    searching: true,
    ordering: true,
    info: true,
    // You can also customize other settings if needed
    language: {
      zeroRecords: "No student attendance data available",
    },
  });

  // Optionally, if you still need pagination controls (outside of DataTables),
  // you can call your separate displayPagination function here:
}

function getStatusHtml(status) {
  let statusClass = "";
  let statusText = status;

  switch (status) {
    case "P":
      statusClass = "badge bg-success text-white";
      statusText = "Present";
      break;
    case "A":
      statusClass = "badge bg-danger text-white";
      statusText = "Absent";
      break;
    case "L":
      statusClass = "badge bg-warning text-white";
      statusText = "Leave";
      break;
    case "OD":
      statusClass = "badge bg-info text-white";
      statusText = "OD";
      break;
    default:
      statusClass = "badge bg-secondary text-white";
      statusText = "Not Marked";
  }

  // Return a div that fills the cell so the background color covers the entire area.
  return `<span class="${statusClass}">${statusText}</div>`;
}

// Add these event handlers after your document.ready function

// Show Hour Attendance View
document
  .getElementById("viewHourAttendanceBtn")
  .addEventListener("click", function () {
    document.getElementById("attendance-cards-view").style.display = "none";
    document.getElementById("hour-attendance-view").style.display = "block";
    // Load initial attendance data
    const today = new Date().toISOString().split("T")[0];
    loadFacultyMarkingStatus(today);
  });

// Back to Cards View
document
  .getElementById("back-to-attendance-cards")
  .addEventListener("click", function () {
    document.getElementById("hour-attendance-view").style.display = "none";
    document.getElementById("attendance-cards-view").style.display = "block";
  });

// Show Faculty Summary View
document
  .getElementById("viewFacultySummaryBtn")
  .addEventListener("click", function () {
    document.getElementById("attendance-cards-view").hide();
    document.getElementById("faculty-summary-view").style.display = "block";
    // Future function to load faculty summary data can be called here
  });

// Back to Cards View from Faculty Summary
document
  .getElementById("back-to-attendance-cards-faculty")
  .addEventListener("click", function () {
    document.getElementById("faculty-summary-view").style.display = "none";
    document.getElementById("attendance-cards-view").style.display = "block";
  });

// Add this function to handle loading faculty summary
function loadFacultyAttendanceSummary() {
  // Get advisor data from session storage
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getFacultyAttendanceSummary",
      batch: advisorData.batch,
      academicYear: advisorData.academicYear,
      semester: advisorData.semester,
      section: advisorData.section,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        displayFacultySummary(data.data);
      } else {
        Swal.fire({
          title: "Error",
          text: data.message,
          icon: "error",
        });
      }
    },
    error: function () {
      Swal.fire({
        title: "Error",
        text: "Failed to fetch faculty summary data",
        icon: "error",
      });
    },
  });
}

// Updated function to display the faculty summary data without badge styling for pending hours
function displayFacultySummary(summaryData) {
  const tbody = $("#facultySummaryTable");
  const emptyState = $("#facultySummaryEmptyState");

  tbody.empty();

  if (!summaryData || summaryData.length === 0) {
    tbody.hide();
    emptyState.show();
    return;
  }

  tbody.show();
  emptyState.hide();

  // Store original data for filtering
  window.facultySummaryData = summaryData;

  // Create filter controls if they don't exist
  if (!$("#facultySummaryFilter").length) {
    const filterHtml = `
        <div id="facultySummaryFilter" class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted">Filter:</span>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" data-filter="all">
                            All Courses
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="pending">
                            Pending Only
                        </button>
                    </div>
                </div>
                <div class="text-muted small">
                    ${summaryData.length} courses found
                </div>
            </div>
        </div>
    `;
    $("#facultySummaryTable")
      .closest(".getStudentAttendanceSummarytable-responsive")
      .before(filterHtml);
  }

  // Initial render
  filterFacultySummary("all");

  // Add filter event handlers
  $("#facultySummaryFilter [data-filter]")
    .off("click")
    .on("click", function () {
      const filter = $(this).data("filter");
      $("#facultySummaryFilter [data-filter]").removeClass("active");
      $(this).addClass("active");
      filterFacultySummary(filter);
    });
}

function filterFacultySummary(filterType) {
  const tbody = $("#facultySummaryTable");
  const emptyState = $("#facultySummaryEmptyState");
  const filteredData =
    filterType === "pending"
      ? window.facultySummaryData.filter((item) => item.pending_hours > 0)
      : window.facultySummaryData;

  tbody.empty();

  if (filteredData.length === 0) {
    tbody.hide();
    emptyState.show();
    return;
  }

  tbody.show();
  emptyState.hide();

  filteredData.forEach((item, index) => {
    const row = `
      <tr>
        <td>${index + 1}</td>
        <td>${item.course_name}</td>
        <td>${item.faculty_name}</td>
        <td>${item.total_hours}</td>
        <td>${item.pending_hours}</td>
      </tr>
    `;
    tbody.append(row);
  });
}

// Event handlers for the Faculty Summary view remain unchanged
$(document).ready(function () {
  // ... existing ready handlers ...

  $("#viewFacultySummaryBtn").on("click", function () {
    $("#attendance-cards-view").hide();
    $("#faculty-summary-view").show();
    loadFacultyAttendanceSummary();
  });

  $("#back-to-attendance-cards-faculty").on("click", function () {
    $("#faculty-summary-view").hide();
    $("#attendance-cards-view").show();
  });
});

// Add these event handlers
$(document).ready(function () {
  // View Student Summary button click
  $("#viewStudentSummaryBtn").on("click", function () {
    $("#attendance-cards-view").hide();
    $("#student-summary-view").show();
    loadStudentAttendanceSummary(); // Add this line to load data when view is shown
  });

  // Back button click
  $("#back-to-attendance-cards-student").on("click", function () {
    $("#student-summary-view").hide();
    $("#attendance-cards-view").show();
  });
});

function loadStudentAttendanceSummary() {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  if (!advisorData) {
    console.error("No advisor data found");
    return;
  }

  // Load Overall Attendance
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getStudentAttendanceSummary",
      batch: advisorData.batch,
      semester: advisorData.semester,
      section: advisorData.section,
      academicYear: advisorData.academicYear,
    },
    success: function (response) {
      console.log("Overall Attendance Summary Response:", response);
      try {
        const result =
          typeof response === "string" ? JSON.parse(response) : response;

        if (result.status === "success") {
          // Map the student data to an array of objects for DataTable
          const data = result.data.students.map((student, index) => {
            return {
              serial: index + 1,
              roll_no: student.roll_no,
              student_name: student.student_name,
              total_hours: student.total_hours,
              present_hours: student.present_hours,
              attendance_percentage:
                student.attendance_percentage.toFixed(2) + "%",
            };
          });

          // If DataTable exists, destroy it
          if ($.fn.DataTable.isDataTable("#studentOverallAttendanceTable")) {
            $("#studentOverallAttendanceTable").DataTable().clear().destroy();
          }

          // Initialize Overall Attendance DataTable
          $("#studentOverallAttendanceTable").DataTable({
            data: data,
            columns: [
              { title: "S.No", data: "serial", className: "text-center" },
              {
                title: "Roll Number",
                data: "roll_no",
                className: "text-center",
              },
              {
                title: "Student Name",
                data: "student_name",
                className: "text-center",
              },
              {
                title: "Total Hour(s)",
                data: "total_hours",
                className: "text-center",
              },
              {
                title: "Hour(s) Present",
                data: "present_hours",
                className: "text-center",
              },
              {
                title: "Attendance Percentage",
                data: "attendance_percentage",
                className: "text-center",
              },
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            language: {
              zeroRecords: "No attendance summary found",
            },
          });
        } else {
          console.error("Error:", result.message);
        }
      } catch (e) {
        console.error("Error parsing response:", e);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });

  // Load Subject-wise Attendance
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getSubjectWiseAttendance",
      batch: advisorData.batch,
      semester: advisorData.semester,
      section: advisorData.section,
      academicYear: advisorData.academicYear,
    },
    success: function (response) {
      console.log("Subject-wise Attendance Response:", response);
      try {
        const result =
          typeof response === "string" ? JSON.parse(response) : response;

        if (result.status === "success") {
          // If DataTable exists, destroy it
          if (
            $.fn.DataTable.isDataTable("#studentSubjectWiseAttendanceTable")
          ) {
            $("#studentSubjectWiseAttendanceTable")
              .DataTable()
              .clear()
              .destroy();
          }

          // Initialize Subject-wise Attendance DataTable
          $("#studentSubjectWiseAttendanceTable").DataTable({
            data: result.data,
            columns: [
              {
                title: "Roll Number",
                data: "roll_no",
                className: "text-center",
              },
              {
                title: "Student Name",
                data: "student_name",
                className: "text-center",
              },
              {
                title: "Course Code",
                data: "course_code",
                className: "text-center",
              },
              {
                title: "Course Name",
                data: "course_name",
                className: "text-center",
              },
              {
                title: "Total Hour(s)",
                data: "total_hours",
                className: "text-center",
              },
              {
                title: "Hour(s) Present",
                data: "present_hours",
                className: "text-center",
              },
              {
                title: "Attendance Percentage",
                data: "attendance_percentage",
                className: "text-center",
                render: function (data) {
                  return data.toFixed(2) + "%";
                },
              },
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            language: {
              zeroRecords: "No subject-wise attendance found",
            },
          });
        }
      } catch (e) {
        console.error("Error parsing response:", e);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

// Call the function when the student summary view button is clicked
document
  .getElementById("viewStudentSummaryBtn")
  .addEventListener("click", loadStudentAttendanceSummary);

function editLessonPlan(courseId) {
  showLoader();
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getLessonPlanData",
      courseId: courseId,
    },
    success: function (response) {
      hideLoader();
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          showLessonPlanModal(courseId, data);
        } else {
          throw new Error(data.message || "Failed to fetch lesson plan");
        }
      } catch (error) {
        showError(error.message);
      }
    },
    error: function (xhr, status, error) {
      hideLoader();
      showError("Failed to fetch lesson plan: " + error);
    },
  });
}

function showLessonPlanModal(courseId, lessonPlan) {
  const isEdit = lessonPlan !== null;
  const modalHtml = `
          <div class="modal fade" id="lessonPlanModal" tabindex="-1">
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                      <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">${
                            isEdit ? "Edit" : "Add"
                          } Lesson Plan</h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                          <form id="lessonPlanForm">
                              <input type="hidden" name="courseId" value="${courseId}">
                              
                              <!-- Units Table -->
                              <div class="mb-3">
                                  <label class="form-label">Units</label>
                                  <div class="table-responsive">
                                      <table class="table table-bordered">
                                          <thead>
                                              <tr>
                                                  <th>Unit Number</th>
                                                  <th>Unit Name</th>
                                                  <th>Course Outcome</th>
                                                  <th>CO weightage</th>
                                                  <th>CO Threshold</th>
                                                  <th>Topics</th>
                                                  <th>Actions</th>
                                              </tr>
                                          </thead>
                                          <tbody id="unitTableBody">
                                              ${generateUnitRows(
                                                lessonPlan.data
                                              )}
                                          </tbody>
                                      </table>
                                  </div>
                                  <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addUnitRow()">
                                      <i class="fas fa-plus"></i> Add Unit
                                  </button>
                              </div>
                          </form>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="button" class="btn btn-primary" onclick="saveLessonPlan()">Save Changes</button>
                      </div>
                  </div>
              </div>
          </div>
      `;

  // Remove existing modal if any
  const existingModal = document.getElementById("lessonPlanModal");
  if (existingModal) {
    existingModal.remove();
  }

  // Add new modal to body
  document.body.insertAdjacentHTML("beforeend", modalHtml);

  // Show modal
  const modal = new bootstrap.Modal(document.getElementById("lessonPlanModal"));
  modal.show();
}
function generateUnitRows(lessonPlan) {
  if (!lessonPlan || !lessonPlan.length) {
    return `
              <tr>
                  <td><input type="number" class="form-control unit-number" min="1" required></td>
                  <td><input type="text" class="form-control unit-name" required></td>
                  <td><input type="text" class="form-control unit-co" required></td>
                  <td><input type="text" class="form-control co_threshold" required></td>
                  <td>
                      <button type="button" class="btn btn-danger btn-sm" onclick="removeUnitRow(this)">
                          <i class="fas fa-trash"></i>
                      </button>
                  </td>
              </tr>
          `;
  }

  return lessonPlan
    .map(
      (unit) => `
        <tr class="unit-row" data-unit-id="${unit.unit_id}">
            <td>
                <input type="number" class="form-control unit-number" value="${
                  unit.unit_number
                }" min="1" required>
            </td>
            <td>
                <input type="text" class="form-control unit-name" value="${
                  unit.unit_name
                }" required>
            </td>
            <td>
                <input type="text" class="form-control unit-co" value="${
                  unit.CO
                }" required>
            </td>
             <td>
                <input type="text" class="form-control co_weightage" value="${
                  unit.co_weightage
                }" required>
            </td>
             <td>
                <input type="text" class="form-control co_threshold" value="${
                  unit.co_threshold
                }" required>
            </td>
            <td>
                <div class="topics-container">
                    ${generateTopicsList(unit.topics)}
                    <button type="button" class="btn btn-info btn-sm mt-2" onclick="addTopicToUnit(this)">
                        <i class="fas fa-plus"></i> Add Topic
                    </button>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeUnitRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `
    )
    .join("");
}
function generateTopicsList(topics) {
  return `
        <div class="topics-list">
            ${topics
              .map(
                (topic) => `
                <div class="input-group mb-2 topic-item">
                    <input type="text" class="form-control topic-name" value="${topic.name}" required>
                    <button type="button" class="btn btn-outline-danger" onclick="removeTopic(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `
              )
              .join("")}
        </div>
    `;
}
function removeTopic(button) {
  $(button).closest(".topic-item").remove();
}
function addTopicToUnit(button) {
  const topicsList = button.previousElementSibling; // Get the topics-list div
  const topicHtml = `
    <div class="input-group mb-2 topic-item">
        <input type="text" class="form-control topic-name" required>
        <button type="button" class="btn btn-outline-danger" onclick="removeTopic(this)">
            <i class="fas fa-times"></i>
        </button>
    </div>
  `;
  topicsList.insertAdjacentHTML("beforeend", topicHtml);
}

function addUnitRow() {
  const tbody = document.getElementById("unitTableBody");
  const tr = document.createElement("tr");

  tr.innerHTML = `
    <td><input type="number" class="form-control unit-number" min="1" required></td>
    <td><input type="text" class="form-control unit-name" required></td>
    <td><input type="text" class="form-control unit-co" required></td>
    <td><input type="text" class="form-control co_weightage" required></td>
    <td><input type="text" class="form-control co_threshold" required></td>
    <td>
        <div class="topics-container">
            <div class="topics-list"></div>
            <button type="button" class="btn btn-info btn-sm mt-2" onclick="addTopicToUnit(this)">
                <i class="fas fa-plus"></i> Add Topic
            </button>
        </div>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeUnitRow(this)">
            <i class="fas fa-trash"></i>
        </button>
    </td>
  `;

  tbody.appendChild(tr);
}

function removeUnitRow(button) {
  button.closest("tr").remove();
}

function saveLessonPlan() {
  const courseId = document.querySelector(
    '#lessonPlanForm input[name="courseId"]'
  ).value;
  const units = [];

  // Collect all unit data including topics
  document.querySelectorAll("#unitTableBody tr").forEach((row) => {
    const topics = [];
    // Get all topics for this unit
    row.querySelectorAll(".topic-item").forEach((topicItem) => {
      const topicName = topicItem.querySelector(".topic-name").value.trim();
      if (topicName) {
        topics.push({
          name: topicName,
        });
      }
    });

    units.push({
      unit_id: row.dataset.unitId || null,
      unit_number: row.querySelector(".unit-number").value,
      unit_name: row.querySelector(".unit-name").value,
      CO: row.querySelector(".unit-co").value,
      co_weightage: row.querySelector(".co_weightage").value,
      co_threshold: row.querySelector(".co_threshold").value,
      topics: topics,
    });
  });

  // Validate inputs
  if (!validateLessonPlan(units)) return;
  console.log("in save");

  showLoader();
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "updateLessonPlan",
      courseId: courseId,
      units: JSON.stringify(units), // Stringify the complex object
    },
    success: function (response) {
      hideLoader();
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          Swal.fire({
            title: "Success",
            text: "Lesson plan updated successfully",
            icon: "success",
          }).then(() => {
            $("#lessonPlanModal").modal("hide");
            requestLessonPlanEdit(courseId, "None");
            location.reload();
          });
        } else {
          throw new Error(data.message || "Failed to update lesson plan");
        }
      } catch (error) {
        showError(error.message);
      }
    },
    error: function (xhr, status, error) {
      hideLoader();
      showError("Failed to update lesson plan: " + error);
    },
  });
}
function validateLessonPlan(units) {
  if (units.length === 0) {
    showError("Please add at least one unit");
    return false;
  }

  for (const unit of units) {
    if (!unit.unit_number || !unit.unit_name || !unit.CO) {
      showError("Please fill in all unit details");
      return false;
    }
    if (unit.topics.length === 0) {
      showError("Each unit must have at least one topic");
      return false;
    }
  }
  console.log("in validate");
  return true;
}
function showHolidayModal() {
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById("holidayModal"));
  modal.show();
}

function markHoliday() {
  const date = document.getElementById("holidayDate").value;
  const description = document.getElementById("holidayDescription").value;
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

  if (!date || !description) {
    showError("Please fill in all fields");
    return;
  }

  showLoader();
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "markHoliday",
      date: date,
      description: description,
      academicYear: advisorData.academicYear,
      batch: advisorData.batch,
      section: advisorData.section,
      semester: advisorData.semester,
    },
    success: function (response) {
      hideLoader();
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          Swal.fire({
            title: "Success",
            text: "Date marked as holiday successfully",
            icon: "success",
          }).then(() => {
            $("#holidayModal").modal("hide");
            // Refresh the page or update holiday list
            location.reload();
          });
        } else {
          throw new Error(data.message);
        }
      } catch (error) {
        showError(error.message);
      }
    },
    error: function (xhr, status, error) {
      hideLoader();
      showError("Failed to mark holiday: " + error);
    },
  });
}
document
  .querySelector(".admin-card .btn-warning")
  .addEventListener("click", function () {
    document.getElementById("academic-cards-view").style.display = "none";
    document.getElementById("special-attendance-view").style.display = "block";
    initializeSpecialAttendanceView();
  });

// Back button handler for special attendance view
document
  .getElementById("back-to-admin-cards-special")
  .addEventListener("click", function () {
    document.getElementById("special-attendance-view").style.display = "none";
    document.getElementById("academic-cards-view").style.display = "block";
  });

document.addEventListener("DOMContentLoaded", function () {
  // Show special attendance view when clicking manage button
  const manageSpecialBtn = document.querySelector(
    ".admin-card.attendance-card button"
  );
  if (manageSpecialBtn) {
    manageSpecialBtn.addEventListener("click", function () {
      document.getElementById("academic-cards-view").style.display = "none";
      document.getElementById("special-attendance-view").style.display =
        "block";
    });
  }

  // Back button functionality
  const backToAdminCardsSpecial = document.getElementById(
    "back-to-admin-cards-special"
  );
  if (backToAdminCardsSpecial) {
    backToAdminCardsSpecial.addEventListener("click", function () {
      document.getElementById("special-attendance-view").style.display = "none";
      document.getElementById("academic-cards-view").style.display = "block";
    });
  }
});

// Initialize period checkboxes with proper styling
function initializePeriodCheckboxes() {
  const periodCheckboxesContainer =
    document.querySelector(".period-checkboxes");
  if (!periodCheckboxesContainer) return;

  // Clear existing checkboxes
  periodCheckboxesContainer.innerHTML = "";

  // Create period checkboxes (1 to 8)
  for (let i = 1; i <= 8; i++) {
    const checkboxDiv = document.createElement("div");
    checkboxDiv.className = "form-check";
    checkboxDiv.innerHTML = `
          <div class="period-checkbox-wrapper border rounded p-2">
              <input type="checkbox" class="form-check-input" name="periods" value="${i}" id="period${i}">
              <label class="form-check-label ms-2" for="period${i}">
                  <span class="period-number">Period ${i}</span>
                  <span class="period-time text-muted d-block small">${getPeriodTime(
                    i
                  )}</span>
              </label>
          </div>
      `;
    periodCheckboxesContainer.appendChild(checkboxDiv);
  }

  // Add event listeners for Select All and Clear All buttons
  const selectAllBtn = document.getElementById("selectAllPeriodsBtn");
  const clearAllBtn = document.getElementById("clearPeriodsBtn");

  if (selectAllBtn) {
    selectAllBtn.addEventListener("click", function () {
      document.querySelectorAll('input[name="periods"]').forEach((checkbox) => {
        checkbox.checked = true;
      });
    });
  }

  if (clearAllBtn) {
    clearAllBtn.addEventListener("click", function () {
      document.querySelectorAll('input[name="periods"]').forEach((checkbox) => {
        checkbox.checked = false;
      });
    });
  }
}

// Helper function to get period time ranges
function getPeriodTime(periodNumber) {
  const periodTimes = {
    1: "9:00 AM - 9:50 AM",
    2: "9:50 AM - 10:40 AM",
    3: "10:50 AM - 11:40 AM",
    4: "11:40 AM - 12:30 PM",
    5: "1:30 PM - 2:20 PM",
    6: "2:20 PM - 3:10 PM",
    7: "3:20 PM - 4:10 PM",
    8: "4:10 PM - 5:00 PM",
  };
  return periodTimes[periodNumber] || "";
}

// Add this CSS to style the period checkboxes
const style = document.createElement("style");
style.textContent = `
  .period-checkbox-wrapper {
      min-width: 180px;
      background-color: #f8f9fa;
      transition: all 0.3s ease;
  }

  .period-checkbox-wrapper:hover {
      background-color: #e9ecef;
  }

  .period-checkboxes {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
      padding: 1rem;
  }

  .period-number {
      font-weight: 500;
      color: #495057;
  }

  .period-time {
      font-size: 0.75rem;
      color: #6c757d;
  }

  .form-check-input:checked + .form-check-label .period-checkbox-wrapper {
      background-color: #e3f2fd;
      border-color: #90caf9;
  }
`;
document.head.appendChild(style);

// Call initializePeriodCheckboxes when the document is ready
document.addEventListener("DOMContentLoaded", function () {
  initializePeriodCheckboxes();
  // ... rest of your existing DOMContentLoaded code ...
});

// Add these functions to handle special attendance
function loadSpecialAttendanceForm() {
  const specialAttendanceView = document.getElementById(
    "special-attendance-view"
  );
  const academicCardsView = document.getElementById("academic-cards-view");

  if (specialAttendanceView && academicCardsView) {
    academicCardsView.style.display = "none";
    specialAttendanceView.style.display = "block";

    // Load students list
    fetchAndRenderStudents();
  }
}

function fetchAndRenderStudents() {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  const userData = JSON.parse(sessionStorage.getItem("userData"));

  if (!advisorData) {
    Swal.fire({
      title: "Error",
      text: "Advisor information not found",
      icon: "error",
    });
    return;
  }

  showLoader();
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "checkSpecialAttendanceStatus",
      date: date,
      advisorData: JSON.stringify(advisorData),
      periods: JSON.stringify([1, 2, 3, 4, 5, 6, 7, 8]),
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        const allMarked = data.sessions.every(
          (session) => session.attendace_status !== "0"
        );
        if (allMarked) {
          Swal.fire({
            title: "All Attendance Marked",
            text: "All attendance for the selected date has already been marked",
            icon: "info",
          });
        } else {
          $.ajax({
            url: "backend.php",
            type: "POST",
            data: {
              action: "getAdvisorStudents",
            },
            success: function (response) {
              hideLoader();
              try {
                const data = JSON.parse(response);
                if (data.status === "success") {
                  const date = document.getElementById(
                    "specialAttendanceDate"
                  ).value;

                  $.ajax({
                    url: "backend.php",
                    type: "POST",
                    data: {
                      action: "getStudentLeaveListSpecialAttendance",
                      semester: advisorData.semester,
                      batch: advisorData.batch,
                      section: advisorData.section,
                      ayear: advisorData.academicYear,
                      date: date,
                    },
                    success: function (response) {
                      const leaveData = JSON.parse(response);
                      console.log(leaveData.leaveHistory);
                      renderStudentsList(data.students, leaveData.leaveHistory);
                    },
                  });
                } else {
                  throw new Error(data.message || "Failed to fetch students");
                }
              } catch (error) {
                showError(error.message);
              }
            },
            error: function (xhr, status, error) {
              hideLoader();
              showError("Failed to connect to server");
            },
          });
        }
      }
    },
    error: function (xhr, status, error) {
      showError("Failed to connect to server");
    },
  });
}

function renderStudentsList(students, leaveHistory) {
  const container = document.getElementById("studentsListContainer");
  if (!container) return;

  let html = `
      <div class="table-responsive">
          <table class="table table-hover align-middle">
              <thead class="bg-light">
                  <tr>
                      <th class="text-center" style="width: 50px;">
                          <div class="form-check">
                              <input type="checkbox" class="form-check-input" id="selectAllStudents">
                          </div>
                      </th>
                      <th>Roll No</th>
                      <th>Name</th>
                      <th class="text-center">Status</th>
                  </tr>
              </thead>
              <tbody>
  `;

  students.forEach((student) => {
    // Find if student exists in leaveHistory
    const leaveInfo = leaveHistory.find(
      (leave) => leave.user_id === student.id
    );

    // Set default status and checkbox state
    let defaultStatus = "3"; // Default to Present
    let isChecked = "checked";
    let isDisabled = "";

    // If student has leave/OD record, update their status
    if (leaveInfo) {
      if (leaveInfo.leave_type == "OD") {
        defaultStatus = "2";
      } else {
        defaultStatus = "1";
      }
      isChecked = "";
      isDisabled = "disabled";
    }

    html += `
          <tr>
              <td class="text-center">
                  <div class="form-check">
                      <input type="checkbox" class="form-check-input student-checkbox" 
                             data-student-id="${student.id}" 
                             ${isChecked}
                             ${isDisabled}>
                  </div>
              </td>
              <td>${student.studentId}</td>
              <td>${student.name}</td>
              <td class="text-center">
                  <select class="form-select form-select-sm attendance-status" 
                          data-student-id="${student.id}"
                          ${isDisabled}>
                      <option value="3" ${
                        defaultStatus === "3" ? "selected" : ""
                      }>Present</option>
                      <option value="1" ${
                        defaultStatus === "1" ? "selected" : ""
                      }>Leave</option>
                      <option value="2" ${
                        defaultStatus === "2" ? "selected" : ""
                      }>OD</option>
                      <option value="0" ${
                        defaultStatus === "0" ? "selected" : ""
                      }>Absent</option>
                  </select>
              </td>
          </tr>
      `;
  });

  html += `
              </tbody>
          </table>
      </div>
  `;

  container.innerHTML = html;

  // Add event listener for "Select All" checkbox
  const selectAllCheckbox = document.getElementById("selectAllStudents");
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      document
        .querySelectorAll(".student-checkbox:not([disabled])")
        .forEach((checkbox) => {
          checkbox.checked = this.checked;
        });
    });
  }
}

function markAttendanceForSessions(sessions, attendanceData) {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  let completedSessions = 0;
  let errors = [];

  sessions.forEach((session) => {
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "markAttendance",
        session_id: session.timetable_id,
        semester: advisorData.semester,
        attendanceData: JSON.stringify(attendanceData),
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.status !== "success") {
            errors.push(
              `Failed to mark attendance for period ${session.period}`
            );
          }
        } catch (error) {
          errors.push(`Error processing response for period ${session.period}`);
        }
      },
      error: function () {
        errors.push(`Network error for period ${session.period}`);
      },
      complete: function () {
        completedSessions++;
        if (completedSessions === sessions.length) {
          hideLoader();
          if (errors.length === 0) {
            Swal.fire({
              title: "Success!",
              text: "Special attendance marked successfully",
              icon: "success",
            }).then(() => {
              // Optionally refresh the page or clear the form
              document.getElementById("special-attendance-view").style.display =
                "none";
              document.getElementById("academic-cards-view").style.display =
                "block";
              window.location.reload();
            });
          } else {
            showError(errors.join("\n"));
          }
        }
      },
    });
  });
}

// Add event listener for form submission
document
  .getElementById("specialAttendanceForm")
  ?.addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent form from submitting normally
    markSpecialAttendance();
  });
function markSpecialAttendance() {
  // Get form data
  const date = document.getElementById("specialAttendanceDate").value;

  // Get selected periods using the correct selector
  const selectedPeriods = [];
  document
    .querySelectorAll('.period-checkboxes input[type="checkbox"]:checked')
    .forEach((checkbox) => {
      selectedPeriods.push(checkbox.value);
    });

  // Validate form data
  if (!date) {
    Swal.fire({
      title: "Missing Date",
      text: "Please select a date",
      icon: "warning",
    });
    return;
  }

  if (selectedPeriods.length === 0) {
    Swal.fire({
      title: "No Periods Selected",
      text: "Please select at least one period",
      icon: "warning",
    });
    return;
  }

  // Get advisor data
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  if (!advisorData) {
    Swal.fire({
      title: "Error",
      text: "Advisor information not found",
      icon: "error",
    });
    return;
  }

  // Collect attendance data for each student
  const attendanceData = [];
  document.querySelectorAll(".student-checkbox").forEach((checkbox) => {
    const studentId = checkbox.dataset.studentId;
    const status = document.querySelector(
      `.attendance-status[data-student-id="${studentId}"]`
    ).value;

    attendanceData.push({
      student_id: studentId,
      attendance_status: status,
    });
  });

  // Create complete form data object
  const formData = {
    date: date,
    periods: selectedPeriods,
    advisor: {
      batch: advisorData.batch,
      section: advisorData.section,
      semester: advisorData.semester,
      academicYear: advisorData.academicYear,
    },
    attendance: attendanceData,
  };

  console.log("Form Data:", formData); // Debug log

  // Show confirmation with form details
  Swal.fire({
    title: "Confirm Attendance",
    html: `
        <div class="text-start">
            <p><strong>Date:</strong> ${date}</p>
            <p><strong>Periods:</strong> ${selectedPeriods.join(", ")}</p>
            <p><strong>Total Students:</strong> ${attendanceData.length}</p>
            <p><strong>Present Count:</strong> ${
              attendanceData.filter((a) => a.attendance_status === "3").length
            }</p>
            <p><strong>Leave Count:</strong> ${
              attendanceData.filter((a) => a.attendance_status === "1").length
            }</p>
            <p><strong>OD Count:</strong> ${
              attendanceData.filter((a) => a.attendance_status === "2").length
            }</p>
            <p><strong>Absent Count:</strong> ${
              attendanceData.filter((a) => a.attendance_status === "0").length
            }</p>
        </div>
    `,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Mark Attendance",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      submitSpecialAttendance(formData);
    }
  });
}

function submitSpecialAttendance(formData) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "markSpecialAttendance",
      formData: formData,
    },
    success: function (response) {
      hideLoader();
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          Swal.fire({
            title: "Success",
            text: "Special attendance marked successfully",
            icon: "success",
          }).then(() => {
            // Reset form
            backToAdminCardsSpecial();
          });
        } else {
          throw new Error(data.message || "Failed to mark attendance");
        }
      } catch (error) {
        showError(error.message);
      }
    },
    error: function (xhr, status, error) {
      console.log(error);
    },
  });
}

// Add form submit event listener

// Add event listener for date selection
document
  .getElementById("specialAttendanceDate")
  .addEventListener("change", function () {
    const date = this.value;
    if (date) {
      checkAttendanceStatus(date);
    }
  });

function checkAttendanceStatus(date) {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

  showLoader();

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "checkSpecialAttendanceStatus",
      date: date,
      advisorData: JSON.stringify(advisorData),
      periods: JSON.stringify([1, 2, 3, 4, 5, 6, 7, 8]), // All periods
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          // Check if any sessions exist for this date
          if (data.sessions.length === 0) {
            hideLoader();
            Swal.fire({
              title: "No Sessions",
              text: "No timetable sessions found for this date.",
              icon: "warning",
            });
            return;
          }

          // Check if all sessions are already marked (status !== '0')
          const allMarked = data.sessions.every(
            (session) => session.attendance_status !== "0"
          );

          if (allMarked) {
            hideLoader();
            Swal.fire({
              title: "Already Marked",
              text: "Attendance for all sessions on this date has already been marked.",
              icon: "warning",
            }).then(() => {
              document.getElementById("specialAttendanceForm").reset();
              document.getElementById("studentsListContainer").innerHTML = "";
            });
          } else {
            // If not all marked, proceed with fetching students
            fetchAndRenderStudents();
          }
        } else {
          throw new Error(data.message || "Failed to check attendance status");
        }
      } catch (error) {
        hideLoader();
        showError("Error processing response: " + error.message);
      }
    },
    error: function (xhr, status, error) {
      hideLoader();
      showError("Failed to check attendance status: " + error);
    },
  });
}

function fetchAndRenderStudents() {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  const date = document.getElementById("specialAttendanceDate").value;

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAdvisorStudents",
      batch: advisorData.batch,
      section: advisorData.section,
      semester: advisorData.semester,
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          // Get leave data
          $.ajax({
            url: "backend.php",
            type: "POST",
            data: {
              action: "getStudentLeaveListSpecialAttendance",
              semester: advisorData.semester,
              batch: advisorData.batch,
              section: advisorData.section,
              ayear: advisorData.academicYear,
              date: date,
            },
            success: function (leaveResponse) {
              hideLoader();
              const leaveData = JSON.parse(leaveResponse);
              renderStudentsList(data.students, leaveData.leaveHistory || []);
            },
            error: function (xhr, status, error) {
              hideLoader();
              showError("Failed to fetch leave data: " + error);
            },
          });
        } else {
          throw new Error(data.message || "Failed to fetch students");
        }
      } catch (error) {
        hideLoader();
        showError(error.message);
      }
    },
    error: function (xhr, status, error) {
      hideLoader();
      showError("Failed to fetch students: " + error);
    },
  });
}

function showLoader() {
  document.getElementById("loader").style.display = "block";
}

function hideLoader() {
  document.getElementById("loader").style.display = "none";
}

function showError(message) {
  Swal.fire({
    title: "Error",
    text: message,
    icon: "error",
  });
}
function updateTimeTableButton(data) {
  console.log(data);
  document.getElementById("generateTimeTableBtn").style.display = "none";
  document.getElementById("saveTimeTableBtn").style.display = "none";
  document.getElementById("editTimeTableBtn").style.display = "block";
  editMode = false;
  var btn = document.getElementById("editTimeTableBtn");
  btn.classList.remove(
    "btn-danger",
    "btn-warning",
    "btn-success",
    "btn-primary"
  );
  btn.disabled = false;

  if (data === "None") {
    btn.classList.add("btn-danger");
    btn.textContent = "Request To Edit";
  } else if (data === "Pending") {
    btn.textContent = "Requested To HOD";
    btn.classList.add("btn-warning");
    btn.disabled = true;
  } else if (data === "Approved") {
    document.getElementById("editTimeTableBtn").style.display = "block";
    btn.classList.add("btn-primary");
    btn.textContent = "Edit Time Table";
  } else {
    btn.textContent = "Save Time-Table";
    btn.classList.add("btn-primary");
  }
}
document
  .getElementById("editTimeTableBtn")
  .addEventListener("click", function () {
    if (this.textContent === "Request To Edit") {
      editTimeTable("Pending");
    } else if (this.textContent === "Edit Time Table") {
      document.getElementById("saveTimeTableBtn").style.display = "block";
      document.getElementById("editTimeTableBtn").style.display = "none";
      editMode = true;
    }
  });

function editTimeTable(status) {
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));
  $.ajax({
    url: "backend.php",
    type: "POST",
    dataType: "json",
    data: {
      action: "requestTimeTableEdit",
      batch: advisorData.batch,
      semester: advisorData.semester,
      section: advisorData.section,
      academicYear: advisorData.academicYear,
      id: JSON.parse(sessionStorage.getItem("userData")).id,
      status: status,
    },
    success: function (response) {
      if (response.status === "success") {
        console.log(response);
        if (status === "Pending") {
          Swal.fire({
            title: "Success",
            text: response.message,
            icon: "success",
          });
        }
        updateTimeTableButton("Pending");
      } else {
        Swal.fire({
          title: "Error",
          text: response.message || "Failed to send Time Table Edit Request",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        title: "Error",
        text: "Failed to send Time Table Edit Request",
        icon: "error",
      });
    },
  });
}

// when i click the change aculty-course
// it should  show the faculty-change-view

// Handle faculty change view
$(document).ready(function () {

  const addCourseBtn = document.getElementById('addCourseBtn');
  const coursesContainer = document.getElementById('coursesContainer');
  const timeTableForm = document.getElementById('timeTableForm');
  let courseCounter = 0; // Declare globally or at a scope that persists

addCourseBtn.addEventListener('click', function() {
  courseCounter++;  // This should increment the value each time the button is clicked.
  
  // Log the counter value to verify
  console.log("Course counter:", courseCounter);
  
  // Create the new course element using the updated counter
  const newCourseElement = document.createElement('div');
  newCourseElement.className = 'course-item card border-0 shadow-sm mb-3';
  newCourseElement.innerHTML = `
      <div class="card-body p-3">
          <div class="d-flex justify-content-between mb-2">
              <h6 class="card-title mb-0">Course #${courseCounter}</h6>
              <button type="button" class="btn-close remove-course" aria-label="Remove course"></button>
          </div>
          <div class="row g-3">
              <div class="mb-4">
                  <label for="subjectSelect${courseCounter}" class="form-label fw-medium">Select Subject</label>
                  <select class="form-select form-select-lg rounded-3 subject-select" id="subjectSelect${courseCounter}">
                      <option selected disabled>Loading subjects...</option>
                  </select>
                  <div class="spinner-border text-primary spinner-border-sm mt-2 d-none subject-loader" id="subjectLoader${courseCounter}" role="status">
                      <span class="visually-hidden">Loading subjects...</span>
                  </div>
              </div>
              <div class="col-md-4">
                  <label class="form-label">Exam Date</label>
                  <input type="date" class="form-control course-date">
              </div>
              <div class="col-md-4">
                  <label class="form-label">Time</label>
                  <input type="time" class="form-control course-time">
              </div>
              <div class="col-12">
                  <label class="form-label">Additional Details</label>
                  <textarea class="form-control course-details" rows="2" placeholder="Enter any additional details"></textarea>
              </div>
          </div>
      </div>
  `;
  coursesContainer.appendChild(newCourseElement);
  
  // Bind remove event
  const removeBtn = newCourseElement.querySelector('.remove-course');
  removeBtn.addEventListener('click', function() {
      newCourseElement.remove();
      updateCourseTitles();
  });
  
  // Get the new course select and loader as jQuery objects
  const $newCourseSelect = $(newCourseElement).find('.subject-select');
  const $newSubjectLoader = $(newCourseElement).find('.subject-loader');
  
  // Fetch courses for this new dropdown
  fetchAvailableCoursesForElement($newCourseSelect, $newSubjectLoader);
});

  
  // Event delegation for remove buttons
  coursesContainer.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-course')) {
          const courseItem = e.target.closest('.course-item');
          courseItem.remove();
          updateCourseTitles();
      }
  });
  
  // Update course numbers when a course is removed
  function updateCourseTitles() {
      const courseTitles = document.querySelectorAll('.course-item .card-title');
      courseTitles.forEach((title, index) => {
          title.textContent = `Course #${index + 1}`;
      });
      courseCounter = courseTitles.length;
  }
  

  // Show faculty change view when button is clicked
  $(".btn-faculty-change").on("click", function () {
    $("#academic-cards-view").hide();
    $("#faculty-change-view").show();
    loadDepartmentsAndCourses();
  });
    // Handle back button click
    $("#back-to-admin-btn").on("click", function () {
      $("#timeTable-view").hide();
      $("#academic-cards-view").show();
    });
  
  $(".btn-timeTable").on("click", function () { 
    $("#academic-cards-view").hide();
    $("#timeTable-view").show();
  });
  

  // Handle back button click
  $("#back-to-academic-cards-btn").on("click", function () {
    $("#faculty-change-view").hide();
    $("#academic-cards-view").show();
  });
});

// Load both departments and courses
function loadDepartmentsAndCourses() {
  // Load departments
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: { action: "getDepartments" },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        const deptSelect = $("#newDepartment");
        deptSelect
          .empty()
          .append('<option value="">Choose department...</option>');
        data.departments.forEach((dept) => {
          deptSelect.append(`<option value="${dept}">${dept}</option>`);
        });
      }
    },
  });

  // Load advisor courses with faculty
  const advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAdvisorCourses",
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        const courseSelect = $("#currentCourse");
        courseSelect
          .empty()
          .append('<option value="">Choose course...</option>');

        data.courses.forEach((course) => {
          courseSelect.append(`
                        <option value="${course.course_id}" 
                                data-faculty='${JSON.stringify(
                                  course.faculty
                                )}'>
                            ${course.course_code} - ${course.course_name}
                        </option>
                    `);
        });
      }
    },
  });
}

// Handle course selection to show current faculty
$("#currentCourse").on("change", function () {
  const selectedOption = $(this).find("option:selected");
  const facultyData = selectedOption.data("faculty");

  if (facultyData) {
    const currentFacultySelect = $("#currentFaculty");
    currentFacultySelect
      .empty()
      .append('<option value="">Choose faculty...</option>');

    // Add each faculty as an option
    facultyData.forEach((faculty) => {
      currentFacultySelect.append(`
              <option value="${faculty.id}">
                  ${faculty.name} (${faculty.designation})
              </option>
          `);
    });

    // Enable the faculty select
    currentFacultySelect.prop("disabled", false);
  } else {
    // Reset and disable faculty select if no course chosen
    $("#currentFaculty")
      .empty()
      .append('<option value="">Choose faculty...</option>')
      .prop("disabled", true);
  }
});

// Handle department change to load new faculty options
$("#newDepartment").on("change", function () {
  const department = $(this).val();
  if (department) {
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "getFacultyByDepartment",
        department: department,
      },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.status === "success") {
          const facultySelect = $("#newFaculty");
          facultySelect
            .empty()
            .append('<option value="">Choose faculty...</option>');

          data.faculty.forEach((faculty) => {
            console.log(faculty);
            facultySelect.append(`
                            <option value="${faculty.id}">
                                ${faculty.name} (${faculty.designation})
                            </option>
                        `);
          });

          // Enable the faculty select
          facultySelect.prop("disabled", false);
        }
      },
    });
  } else {
    // Reset and disable faculty select if no department chosen
    $("#newFaculty")
      .empty()
      .append('<option value="">Choose faculty...</option>')
      .prop("disabled", true);
  }
});
// Handle save faculty change
$("#saveFacultyChange").on("click", function () {
  const courseId = $("#currentCourse").val();
  const oldFacultyId = $("#currentFaculty").find("option:selected").val();
  const newFacultyId = $("#newFaculty").find("option:selected").val();
  console.log(courseId, oldFacultyId, newFacultyId);

  if (!courseId || !oldFacultyId || !newFacultyId) {
    Swal.fire({
      title: "Error",
      text: "Please select course and faculty.",
      icon: "error",
    });
    return;
  }

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "changeFaculty",
      courseId: courseId,
      oldFacultyId: oldFacultyId,
      newFacultyId: newFacultyId,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        Swal.fire({
          title: "Success",
          text: data.message,
          icon: "success",
        });
        // Optionally, you can refresh the faculty list or perform other actions
        $("#faculty-change-view").hide();
        $("#academic-cards-view").show();
      } else {
        Swal.fire({
          title: "Error",
          text: data.message,
          icon: "error",
        });
      }
    },
    error: function () {
      Swal.fire({
        title: "Error",
        text: "An error occurred while changing the faculty.",
        icon: "error",
      });
    },
  });
});
document
  .getElementById("downloadStudentAttendanceBtn")
  .addEventListener("click", function () {
    // Get the DataTable instance and all rows
    var dt = $("#studentWiseAttendance").DataTable();
    var allData = [];
    dt.rows().every(function () {
      allData.push(this.data());
    });

    console.log("Retrieved data count:", allData.length);

    if (allData.length === 0) {
      alert("No data available to generate PDF!");
      return;
    }

    try {
      // Create a new jsPDF instance in landscape mode, using points and A4 size
      var doc = new jspdf.jsPDF("l", "pt", "a4");
      var pageWidth = doc.internal.pageSize.getWidth();
      var printDate = "Printed on: " + new Date().toLocaleString();
      var userData = JSON.parse(sessionStorage.getItem("userData"));
      var advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

      // Add College Logo
      const logo = "image/icons/mkce_s.png"; // Ensure the path is correct
      doc.addImage(logo, "PNG", 30, 20, 80, 80);

      // College Name and Tagline
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      doc.text(
        "M.KUMARASAMY COLLEGE OF ENGINEERING, KARUR - 639 113",
        pageWidth / 2,
        40,
        { align: "center" }
      );

      doc.setFont("helvetica", "normal");
      doc.setFontSize(12);
      doc.text(
        "(An Autonomous Institution Affiliated to Anna University, Chennai)",
        pageWidth / 2,
        60,
        { align: "center" }
      );

      // Document Title
      doc.setFont("helvetica", "bold");
      doc.setFontSize(14);
      doc.text("Student Wise Attendance Report", pageWidth / 2, 80, {
        align: "center",
      });

      // Faculty and Department Details
      doc.setFontSize(12);
      doc.text(
        "Faculty Name ID & Name: " + userData.id + " & " + userData.name,
        50,
        120,
        { align: "left" }
      );
      doc.text("Department: " + userData.dept, 50, 140, { align: "left" });
      doc.text(
        "Attendance Date: " + document.getElementById("attendanceDate").value,
        50,
        160,
        { align: "left" }
      );
// Academic Year and Batch Details
      doc.text(
        "Academic Year: " + advisorData.academicYear,
        pageWidth - 100,
        120,
        { align: "center" }
      );
      doc.text("Batch: " + advisorData.batch, pageWidth - 119, 140, {
        align: "center",
      });
      doc.text("Section: " + advisorData.section, pageWidth - 139, 160, {
        align: "center",
      });
      doc.text("Semester: " + advisorData.semester, pageWidth - 134, 180, {
        align: "center",
      });
      // Print Date (top right)
      doc.setFontSize(10);
      doc.text(printDate, pageWidth - 40, 70, { align: "right" });

      // Prepare table header and data for autoTable
      var headers = ["Roll No", "Student Name"];
      for (var i = 1; i <= 8; i++) {
        headers.push("Hour " + i);
      }

      var tableData = [];
      allData.forEach(function (row) {
        var rowData = [];
        rowData.push(row.roll_no || "N/A");
        rowData.push(row.sname || "N/A");

        // Assuming row.hours is either an array (indexed by 1..8) or an object
        for (var i = 1; i <= 8; i++) {
          var status = row.hours && row.hours[i] ? row.hours[i] : "-";
          rowData.push(status);
        }
        tableData.push(rowData);
      });

      // Generate the table with autoTable starting at Y position 200.
      // The didDrawCell callback colors each hour cell based on status.
      doc.autoTable({
        startY: 200,
        head: [headers],
        body: tableData,
        styles: {
          font: "helvetica",
          fontSize: 10,
          cellPadding: 5,
          overflow: "linebreak",
          halign: "center",
          valign: "middle",
        },
        headStyles: {
          fillColor: [32, 178, 170],
          textColor: [255],
          fontStyle: "bold",
        },
        tableLineColor: [189, 195, 199],
        tableLineWidth: 0.75,
        didDrawCell: function (data) {
          // Only apply for body cells and hour columns (columns index 2 and above)
          if (data.section === "body" && data.column.index >= 2) {
            var text = data.cell.raw;
            // Normalize text (trim spaces, convert to uppercase)
            var status = String(text).trim().toUpperCase();
            var fillColor = null;

            if (status === "P" || status === "PRESENT") {
              fillColor = [200, 255, 200]; // Light green for present
            } else if (status === "A" || status === "ABSENT") {
              fillColor = [255, 200, 200]; // Light red for absent
            } else if (status === "L" || status === "LEAVE") {
              fillColor = [255, 255, 200]; // Light yellow for leave
            } else if (status === "OD") {
              fillColor = [200, 220, 255]; // Light blue for OD
            } else if (status === "-" || status === "NOT MARKED") {
              fillColor = [220, 220, 220]; // Light grey for not marked
            }

            if (fillColor) {
              // Fill the cell background with the specified color
              doc.setFillColor(fillColor[0], fillColor[1], fillColor[2]);
              doc.rect(
                data.cell.x,
                data.cell.y,
                data.cell.width,
                data.cell.height,
                "F"
              );

              // Re-draw the text centered in the cell (reset text color to black)
              doc.setTextColor(0, 0, 0);
              doc.text(
                data.cell.text,
                data.cell.x + data.cell.width / 2,
                data.cell.y + data.cell.height / 2,
                { align: "center", baseline: "middle" }
              );
            }
          }
        },
        didDrawPage: function (data) {
          // Optionally add page number and footer info
          doc.setFontSize(10);
          var pageSize = doc.internal.pageSize;
          var pageHeight = pageSize.getHeight();
          doc.text(
            "Page " + data.pageNumber,
            data.settings.margin.left,
            pageHeight - 10
          );
          doc.text(
            "Generated: " + new Date().toLocaleString(),
            pageSize.getWidth() - 60,
            pageHeight - 10
          );
        },
        margin: { top: 170, bottom: 25 },
      });

      // Save the generated PDF
      doc.save("Student_Wise_Attendance.pdf");
      console.log("PDF generated successfully!");
    } catch (error) {
      console.error("Error generating PDF:", error);
      alert("Error generating PDF: " + error.message);
    }
  });


document
  .getElementById("downloadStudentAttendancePercentageBtn")
  .addEventListener("click", function () {
    console.log("Starting PDF generation with jsPDF...");

    // Get the DataTable instance and retrieve all data (across all pages)
    var dt = $("#studentOverallAttendanceTable").DataTable();
    var allData = dt.rows({ page: "all" }).data().toArray();

    console.log("Retrieved data count:", allData.length);

    if (allData.length === 0) {
      alert("No data available to generate PDF!");
      return;
    }

    try {
      // Create a new jsPDF instance in landscape mode with A4 size (points units)
      var doc = new jspdf.jsPDF("l", "pt", "a4");
      var pageWidth = doc.internal.pageSize.getWidth();
      var printDate = "Printed on: " + new Date().toLocaleString();
      var userData = JSON.parse(sessionStorage.getItem("userData"));
      var advisorData = JSON.parse(sessionStorage.getItem("advisorData"));

      // Add College Logo (ensure the path is correct)
      const logo = "image/icons/mkce_s.png";
      doc.addImage(logo, "PNG", 30, 20, 80, 80);

      // College Name and Tagline
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      doc.text(
        "M.KUMARASAMY COLLEGE OF ENGINEERING, KARUR - 639 113",
        pageWidth / 2,
        40,
        { align: "center" }
      );

      doc.setFont("helvetica", "normal");
      doc.setFontSize(12);
      doc.text(
        "(An Autonomous Institution Affiliated to Anna University, Chennai)",
        pageWidth / 2,
        60,
        { align: "center" }
      );

      // Document Title
      doc.setFont("helvetica", "bold");
      doc.setFontSize(14);
      doc.text("Student Attendance Percentage Report", pageWidth / 2, 80, {
        align: "center",
      });

      doc.setFontSize(12);
      doc.text("Advisor name : " + userData.name, 50, 120, { align: "left" });
      doc.text("Section: " + advisorData.section, 50, 140, { align: "left" });
      doc.text("Semester: " + advisorData.semester, 50, 160, { align: "left" });

      // Academic Year and Batch Details (from advisorData)
      doc.text(
        "Academic Year: " + advisorData.academicYear,
        pageWidth - 100,
        120,
        { align: "center" }
      );
      doc.text("Batch: " + advisorData.batch, pageWidth - 119, 140, {
        align: "center",
      });
     

      // Print Date
      doc.setFontSize(10);
      doc.text(printDate, pageWidth - 40, 70, { align: "right" });

      // Prepare table headers and data
      // If your DataTable already includes a serial (S.No) column, use it;
      // Otherwise, we'll generate one here.
      var headers = [
        "S.No",
        "Roll Number",
        "Student Name",
        "Total Hour(s)",
        "Hour(s) Present",
        "Attendance Percentage",
      ];

      // Build table data by looping through the DataTable data array.
      var tableData = [];
      for (var i = 0; i < allData.length; i++) {
        var row = allData[i];
        // Use row.serial if it exists; otherwise, use (i+1) as the serial number.
        var serial = row.serial || i + 1;
        tableData.push([
          serial,
          row.roll_no,
          row.student_name,
          row.total_hours,
          row.present_hours,
          row.attendance_percentage,
        ]);
      }

      // Generate the table using autoTable; start at Y position 170.
      doc.autoTable({
        startY: 170,
        head: [headers],
        body: tableData,
        styles: {
          font: "helvetica",
          fontSize: 10,
          cellPadding: 5,
          overflow: "linebreak",
          halign: "center",
          valign: "middle",
        },
        headStyles: {
          fillColor: [32, 178, 170],
          textColor: [255],
          fontStyle: "bold",
        },
        tableLineColor: [189, 195, 199],
        tableLineWidth: 0.75,
      });

      // Save the generated PDF
      doc.save("Student_Attendance_Summary.pdf");
      console.log("PDF generated successfully!");
    } catch (error) {
      console.error("Error generating PDF:", error);
      alert("Error generating PDF: " + error.message);
    }
  });

// Function to generate CO options HTML
function generateCOOptions(unitNumber) {
  let options = '<option value="">Select</option>';
  availableCOs.forEach((co) => {
    options += `<option value="${co}">${co}</option>`;
  });
  return options;
}

// Function to handle CO selection change
function handleCOChange(unitNumber, selectElement) {
  const newValue = selectElement.value;
  const previousValue = selectedCOs.get(unitNumber);

  // If there was a previous selection, add it back to available COs
  if (previousValue) {
    availableCOs.push(previousValue);
    availableCOs.sort(); // Keep COs in order
  }

  // If a new CO is selected, remove it from available COs
  if (newValue) {
    selectedCOs.set(unitNumber, newValue);
    availableCOs = availableCOs.filter((co) => co !== newValue);
  } else {
    selectedCOs.delete(unitNumber);
  }

  // Update all CO dropdowns
  updateAllCODropdowns();
}

// Function to update all CO dropdowns
function updateAllCODropdowns() {
  const allSelects = document.querySelectorAll('select[id^="unit"][id$="_co"]');
  allSelects.forEach((select) => {
    const unitNumber = parseInt(select.id.match(/unit(\d+)_co/)[1]);
    const currentValue = selectedCOs.get(unitNumber);

    let options = '<option value="">Select</option>';
    // Add currently selected value for this unit
    if (currentValue) {
      options += `<option value="${currentValue}" selected>${currentValue}</option>`;
    }
    // Add available COs
    availableCOs.forEach((co) => {
      options += `<option value="${co}">${co}</option>`;
    });

    select.innerHTML = options;
  });
}

// Initialize when the page loads
document.addEventListener("DOMContentLoaded", function () {
  // Initialize CO handling for the first unit
  const firstUnitSelect = document.getElementById("unit1_co");
  if (firstUnitSelect) {
    firstUnitSelect.addEventListener("change", () =>
      handleCOChange(1, firstUnitSelect)
    );
  }
});



// Function to hide all views in academic administration
function hideAllAcademicViews() {
  $("#academic-cards-view").show();
  $("#od-requests-view").hide();
  $("#faculty-change-view").hide();
  $("#timeTable-view").hide();
  $("#dayorder-modification-view").hide();
  $("#special-attendance-view").hide();

}

// Add event listener for tab clicks
$('.nav-link').on('click', function() {
  hideAllAcademicViews();
});

