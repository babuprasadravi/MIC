let currentCourseId = "";

// Initialize the timetable when the page loads
document.addEventListener("DOMContentLoaded", function () {
  createTimetable();

  loadPendingAttendance();

  loadHourAlterations(); // Manually load hour alterations on page load.
  loadIncomingAlterations();
  // Also attach the Bootstrap tab event if needed.
  const hourAlterationTab = document.querySelector(
    'a[href="#hourAlterationTab"]'
  );
  if (hourAlterationTab) {
    hourAlterationTab.addEventListener("shown.bs.tab", loadHourAlterations);
  }

  // Bind back button
  const backBtn = document.getElementById("backToTimetable");
  if (backBtn) {
    backBtn.addEventListener("click", backToTimetable);
  }

  // Bind the "Incoming Alteration Requests" tab to load its data when shown.
  const incomingTabTrigger = document.getElementById(
    "incoming-alterations-tab"
  );
  if (incomingTabTrigger) {
    incomingTabTrigger.addEventListener("shown.bs.tab", function () {
      loadIncomingAlterations();
    });
  }

  // Load courses when the Course Info tab is shown
  $('a[href="#courseInfoTab"]').on("shown.bs.tab", function () {
    document.getElementById("courseReportsView").classList.remove("d-none");
    loadFacultyCourses();
  });

  $(document).on("input", ".mark-input", function () {
    validateMarkInput($(this));
  });
  $(document).on("change", "#marksTable select", function () {
    handleAttendanceChange($(this));
  });

  $("#saveMarks").click(saveMarks);
  $("#marksFile").change(handleFileUpload);
  $("#uploadMarks").click(function () {
    $("#marksFile").click();
  });

  // download mark template
  $("#downloadMarksTemplate").click(downloadMarksTemplate);
  $("#downloadMarkSummary").click(downloadMarkSummary);

  // Initialize card hover effects
  document.querySelectorAll(".admin-card").forEach((card) => {
    card.addEventListener("mouseenter", () => {
      card.style.transform = "translateY(-5px)";
    });
    card.addEventListener("mouseleave", () => {
      card.style.transform = "translateY(0)";
    });
  });

  document
    .getElementById("downloadPDFAttendaceSummary")
    .addEventListener("click", function () {
      const doc = new jspdf.jsPDF("l", "pt", "a4");
      const pageWidth = doc.internal.pageSize.getWidth();
      const printDate = "Printed on: " + new Date().toLocaleString();
      const userData = JSON.parse(sessionStorage.getItem("userData"));
      const selectedFacultyData = JSON.parse(
        sessionStorage.getItem("selectedFacultyData")
      );

      // Get DataTables API
      var table = $("#attendanceSummaryTable").closest("table").DataTable();
      var allData = table.rows().data().toArray();

      // Extract headers from <thead>
      var headers = [];
      $("#attendanceSummaryTable")
        .closest("table")
        .find("thead th")
        .each(function () {
          headers.push($(this).text());
        });

      // Convert table data to array
      var tableData = allData.map((row) => Object.values(row));

      // Add College Logo
      const logo = "image/icons/mkce_s.png";
      doc.addImage(logo, "PNG", 30, 20, 80, 80);

      // College Name
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      doc.text(
        "M.KUMARASAMY COLLEGE OF ENGINEERING, KARUR - 639 113",
        pageWidth / 2,
        40,
        { align: "center" }
      );

      // Tagline
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
      doc.text("Attendance Percentage", pageWidth / 2, 80, { align: "center" });

      // Faculty and Department Details
      doc.setFontSize(12);
      doc.text(
        "Faculty Name ID & Name: " + userData.id + " & " + userData.name,
        210,
        120,
        { align: "center" }
      );
      doc.text("Department: " + userData.dept, 210, 140, { align: "center" });

      // Academic Year and Batch
      doc.setFontSize(12);
      doc.text(
        "Academic Year: " + selectedFacultyData.academicYear,
        pageWidth - 40,
        120,
        { align: "right" }
      );
      doc.text("Batch: " + selectedFacultyData.batch, pageWidth - 40, 140, {
        align: "right",
      });

      // Print Date
      doc.setFontSize(10);
      doc.text(printDate, pageWidth - 40, 70, { align: "right" });

      // Generate Table
      doc.autoTable({
        startY: 170,
        head: [headers], // Headers from the table
        body: tableData, // Extracted row data
        styles: {
          font: "helvetica",
          fontSize: 10,
          cellPadding: 5,
          overflow: "linebreak",
        },
        headStyles: {
          fillColor: [32, 178, 170],
          textColor: [255],
          fontStyle: "bold",
          halign: "center",
        },
        bodyStyles: {
          halign: "center",
          valign: "middle",
        },
        tableLineColor: [189, 195, 199],
        tableLineWidth: 0.75,
      });

      doc.save("AttendanceSummary.pdf");
    });
});

let timetableData = {};
let currentCourseData = null;

function getTimeTable() {
  const advisorData = JSON.parse(sessionStorage.getItem("selectedFacultyData"));
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getFacultyTimeTable",
      facultyId: advisorData.facultyId,
      semesterType: advisorData.semesterType,
      academicYear: advisorData.academicYear,
    },
    success: function (response) {
      const data = JSON.parse(response);
      console.log("response", data);
      if (data.status === "success") {
        if (data.timetable.length === 0) {
          timetableData = {
            Monday: Array(8).fill({ name: "...", teacher: "" }),
            Tuesday: Array(8).fill({ name: "...", teacher: "" }),
            Wednesday: Array(8).fill({ name: "...", teacher: "" }),
            Thursday: Array(8).fill({ name: "...", teacher: "" }),
            Friday: Array(8).fill({ name: "...", teacher: "" }),
            Saturday: Array(8).fill({ name: "...", teacher: "" }),
          };
        } else {
          timetableData = data.timetable;
          console.log("timetableData", timetableData);
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

function getDayOrder() {
  const days = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  const today = new Date();
  const currentDay = today.getDay();
  // const currentDay = 1;
  console.log("asdfasdf", currentDay);

  let orderedDays = [];

  for (let i = currentDay; i <= 6; i++) {
    if (i !== 0 && timetableData[days[i]]) {
      const date = new Date(today);
      date.setDate(today.getDate() + (i - currentDay));
      orderedDays.push({
        day: days[i],
        date: date,
      });
    }
  }

  // Add days from start of week to yesterday
  for (let i = 1; i < currentDay; i++) {
    if (timetableData[days[i]]) {
      const date = new Date(today);
      date.setDate(today.getDate() + (i - currentDay + 7));
      orderedDays.push({
        day: days[i],
        date: date,
      });
    }
  }

  return orderedDays;
}

function formatDate(date) {
  const options = {
    day: "numeric", // 1-31
    month: "long", // January, February, etc.
    year: "numeric", // 2024
  };
  return date.toLocaleDateString("en-US", options);
}

function createTimetable() {
  const timetableGrid = document.getElementById("timetableGrid");
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

  // Get ordered days starting from current day
  const orderedDays = getDayOrder();

  // Create a row for each day
  orderedDays.forEach((dayInfo) => {
    const row = document.createElement("div");
    row.className = "tt-row";

    // Day cell - highlight if current day
    const dayCell = document.createElement("div");
    dayCell.className = "tt-day-cell";
    if (dayInfo.day === orderedDays[0].day) {
      dayCell.className += " tt-current-day";
    }
    dayCell.innerHTML = `
            <div class="tt-day-name">${dayInfo.day}</div>
            <div class="tt-day-date">${formatDate(dayInfo.date)}</div>
        `;
    row.appendChild(dayCell);

    // Add course cells for each time slot
    timetableData[dayInfo.day].forEach((period, timeIndex) => {
      const cell = document.createElement("div");
      cell.className = "tt-course-cell";
      const isCurrentDay = dayInfo.day === orderedDays[0].day;

      if (isCurrentDay) {
        cell.className += " tt-current-day-cell";
      }
      cell.dataset.day = dayInfo.day;
      cell.dataset.index = timeIndex;

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
              margin-bottom: 4px;
              font-size: 11px;
              color: #000;
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
          ${
            period.name === "..."
              ? ""
              : `
          <div class="tt-course-dept" style="font-size: 10px; color: #666; font-weight: 500; text-align: center; line-height: 1.2; width: 100%; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; word-break: break-word;">${period.dept}</div>
          <div class="tt-course-semester" style="font-size: 10px; color: #666; font-weight: 500; text-align: center; line-height: 1.2; width: 100%; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; word-break: break-word;">(${period.batch})-${period.semester}  ${period.section}</div>
          `
          }
      </div>
  `;

      // Only add click handler if it's current day and not a FREE HOUR
      if (isCurrentDay && period.name !== "...") {
        // get faclty if from session storage
        const facultyData = JSON.parse(
          sessionStorage.getItem("selectedFacultyData")
        );
        const facultyId = facultyData.facultyId;
        cell.classList.add("tt-clickable");
        const userData = JSON.parse(sessionStorage.getItem("userData"));
        console.log("userData", userData.name);
        cell.addEventListener("click", () => {
          console.log("period", period.semester);
          sessionStorage.setItem("semester", period.semester);
          showCourseDetails(
            {
              name: period.name,
              courseId: period.course_id,
              day: dayInfo.day,
              timeIndex: timeIndex,
              teacher: userData.name,
            },
            "normal"
          );
        });
      }

      row.appendChild(cell);
    });

    grid.appendChild(row);
  });

  timetableGrid.appendChild(grid);
}
function showCourseDetails(courseData, type) {
  // Store courseData for later use
  currentCourseData = courseData;
  let date = null;
  if (courseData.date) {
    date = courseData.date;
  } else {
    date = new Date().toISOString().split("T")[0];
  }
  // First check if attendance is already marked
  //AND batch = ?
  // AND academic_year = ?
  // AND semester = ?
  // AND section = ?;
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "checkAttendanceStatus",
      courseId: courseData.courseId,
      facultyId: JSON.parse(sessionStorage.getItem("selectedFacultyData"))
        .facultyId,
      date: date,
      day: courseData.day,
      hour: courseData.timeIndex + 1,
      type: type,
    },
    success: function (response) {
      const data = JSON.parse(response);
      console.log("data", data);

      // Check if the returned status is not 0 (0 means attendance not marked)
      if (data.status !== 0) {
        let message = "";
        switch (data.status) {
          case 1:
            message =
              "Attendance for this period has already been recorded by Faculty.";
            break;
          case 2:
            message = "Attendance not required (Public Holiday).";
            break;
          case 3:
            message = "Special attendance already recorded by Advisor.";
            break;
          case 10:
            message = "No attendance session found for this period.";
            break;
          default:
            message = "Unknown attendance status.";
            break;
        }
        Swal.fire({
          title: "Attendance Status",
          text: message,
          icon: "info",
        }).then(() => {
          backToTimetable();
        });
        return;
      }

      // Continue with showing attendance form if attendance status is 0 (not marked)
      document.getElementById("timetableView").classList.add("d-none");
      document.getElementById("courseDetailsView").classList.remove("d-none");

      $.ajax({
        url: "backend.php",
        type: "POST",
        data: {
          action: "getLessonPlanData",
          courseId: courseData.courseId,
        },
        success: function (response) {
          const data = JSON.parse(response);
          if (data.status === "success") {
            console.log("data", data);
            populateLessonPlan(data.data);
          }
        },
      });

      const courseDetailsContent = document.getElementById(
        "courseDetailsContent"
      );
      courseDetailsContent.innerHTML = `
          <div class="attendance-container">
              <!-- Header Section -->
              <div class="attendance-header">
                  <div class="d-flex justify-content-between align-items-center">
                      <div class="course-info">
                          <h4 class="mb-0">Attendance Section</h4>
                          <p class="text-muted mb-0">
                              <i class="fas fa-calendar-alt me-2"></i>${new Date().toLocaleDateString(
                                "en-US",
                                {
                                  weekday: "long",
                                  year: "numeric",
                                  month: "long",
                                  day: "numeric",
                                }
                              )}
                          </p>
                      </div>
                      <div class="d-flex gap-2 align-items-center">
                          <div class="btn-group">
                              <button class="btn btn-light btn-sm" onclick="markAllPresent()">
                                  <i class="fas fa-check-circle me-1"></i>All Present
                              </button>
                              <button class="btn btn-light btn-sm" onclick="markAllAbsent()">
                                  <i class="fas fa-times-circle me-1"></i>All Absent
                              </button>
                          </div>
                          <button class="btn btn-outline-secondary" onclick="backToTimetable()">
                              <i class="fas fa-arrow-left me-2"></i>Back to Timetable
                          </button>
                      </div>
                  </div>
              </div>

              <div class="row g-4">
                  <!-- Left Side: Course Info & Form -->
                  <div class="col-lg-4">
                      <div class="course-details-card">
                          <div class="card shadow-sm">
                              <div class="card-header bg-gradient text-white">
                                  <h5 class="mb-1">${courseData.name}</h5>
                                  <div class="d-flex align-items-center">
                                      <i class="fas fa-user-tie me-2"></i>
                                      <span>${courseData.teacher}</span>
                                  </div>
                              </div>
                              <div class="card-body">
                                  <form id="attendanceForm">
                                      <div class="form-group mb-3">
                                          <label class="form-label fw-semibold">Hour Type</label>
                                          <select class="form-select form-select-sm" id="hourType" required>
                                              <option value="regular">Regular</option>
                                              <option value="tutorial">Tutorial</option>
                                          </select>
                                      </div>
                                      
                                      <div class="form-group mb-3">
                                          <label class="form-label fw-semibold">Unit/Chapter Name</label>
                                          <select class="form-select form-select-sm" id="unitSelect" required>
                                              <option value="">Select Unit</option>
                                          </select>
                                      </div>
                                      
                                      <div class="form-group mb-3">
                                          <label class="form-label fw-semibold">Topic Name</label>
                                          <select class="form-select form-select-sm" id="topicSelect" required>
                                              <option value="">Select Topic</option>
                                          </select>
                                      </div>
                                      
                                      <div class="form-group mb-3">
                                          <label class="form-label fw-semibold">Mode of Delivery</label>
                                          <textarea class="form-control form-control-sm" id="description" rows="3"></textarea>
                                      </div>

                                      ${
                                        hasConsecutivePeriods(courseData)
                                          ? `
                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                          <button type="button" class="btn btn-success" onclick="saveAttendance()">
                                            <i class="fas fa-save me-1"></i>Save Attendance
                                          </button>
                                          <button type="button" class="btn btn-outline-success" onclick="toggleContinuousPeriods()">
                                            <i class="fas fa-clock me-1"></i>Continuous Periods
                                          </button>
                                          <input class="form-check-input d-none" type="checkbox" id="continuousPeriods">
                                        </div>
                                      `
                                          : `
                                        <div class="d-flex align-items-center mt-3">
                                          <button type="button" class="btn btn-success" onclick="saveAttendance()">
                                            <i class="fas fa-save me-1"></i>Save Attendance
                                          </button>
                                        </div>
                                      `
                                      }
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>

                  <!-- Right Side: Student List -->
                  <div class="col-lg-8">
                      <div class="card shadow-sm h-100">
                          <div class="attendance-wrapper">
                              <table class="table table-hover mb-0">
                                  <thead class="table-light sticky-top">
                                      <tr>
                                          <th>S.No</th>
                                          <th>Student Details</th>
                                          <th class="text-center">Status</th>
                                      </tr>
                                  </thead>
                                  <tbody id="studentsList">
                                      <!-- Students will be populated here -->
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      `;

      fetchStudentsList(courseData);
    },
  });
}

function markAllPresent() {
  const inputs = document.querySelectorAll('input[value="present"]');
  inputs.forEach((input) => (input.checked = true));
}

function markAllAbsent() {
  const inputs = document.querySelectorAll('input[value="absent"]');
  inputs.forEach((input) => (input.checked = true));
}

function saveCourseChanges(day, timeIndex) {
  const newCourseName = document.getElementById("courseName").value;
  const newFacultyName = document.getElementById("facultyName").value;

  if (newCourseName && newFacultyName) {
    timetableData[day][timeIndex] = {
      name: newCourseName,
      teacher: newFacultyName,
    };

    // Recreate timetable to reflect changes
    const timetableGrid = document.getElementById("timetableGrid");
    timetableGrid.innerHTML = "";
    createTimetable();

    // Go back to timetable view
    backToTimetable();
  }
}

function backToTimetable() {
  document.getElementById("courseDetailsView").classList.add("d-none");
  document.getElementById("timetableView").classList.remove("d-none");

  // Refresh the pending attendance list when going back
  loadPendingAttendance();
}

function fetchStudentsList(courseData) {
  const facultyData = JSON.parse(sessionStorage.getItem("selectedFacultyData"));
  const facultyId = facultyData.facultyId;

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getStudentAttendance",
      courseId: courseData.courseId,
      facultyId: facultyId,
    },
    success: function (response) {
      let date;
      if (courseData.date) {
        date = courseData.date;
      } else {
        date = new Date().toISOString().split("T")[0];
      }
      const selectedFacultyData = JSON.parse(
        sessionStorage.getItem("selectedFacultyData")
      );
      const data = JSON.parse(response);
      if (data.status === "success") {
        const tbody = document.getElementById("studentsList");
        $.ajax({
          url: "backend.php",
          type: "POST",
          data: {
            action: "getStudentLeaveList",
            semesterType: selectedFacultyData.semesterType,
            ayear: selectedFacultyData.academicYear,
            date: date,
          },
          success: function (leaveResponse) {
            const leaveData = JSON.parse(leaveResponse);
            if (leaveData.status === "success") {
              // Create a map of student roll numbers who are on leave
              const absenteeMap = new Map();
              leaveData.leaveHistory.forEach((leave) => {
                absenteeMap.set(leave.student_roll_no, {
                  type: leave.leave_type,
                  reason: leave.reason || "Not specified",
                });
              });

              // Render the student list with absentee information
              tbody.innerHTML = data.students
                .map((student, index) => {
                  const isAbsent = absenteeMap.has(student.rollNo);
                  const absenteeInfo = absenteeMap.get(student.rollNo);
                  const isOD = isAbsent && absenteeInfo.type === "OD";
                  const isLeave = isAbsent && absenteeInfo.type === "Leave";

                  return `
            <tr class="student-row ${
              isOD ? "table-info" : isLeave ? "table-warning" : ""
            }">
                <td class="student-number">${index + 1}</td>
                <td>
                    <div class="student-info">
                        <div class="roll-number">${student.rollNo}</div>
                        <div class="student-name">${student.name}</div>
                        ${
                          isAbsent
                            ? `
                            <div class="small ${
                              isOD ? "text-info" : "text-warning"
                            }">
                                <i class="fas ${
                                  isOD ? "fa-business-time" : "fa-house-leave"
                                }"></i> 
                                ${absenteeInfo.type}: ${absenteeInfo.reason}
                            </div>
                        `
                            : ""
                        }
                    </div>
                </td>
                <td class="text-center attendance-toggle">
                    <div class="attendance-switch">
                        ${
                          isOD
                            ? `
                            <input type="hidden" name="attendance_${student.rollNo}" value="od">
                            <label class="switch-label od">
                                <i class="fas fa-business-time"></i>
                            </label>
                        `
                            : isLeave
                            ? `
                            <input type="hidden" name="attendance_${student.rollNo}" value="leave">
                            <label class="switch-label leave">
                                <i class="fas fa-house-leave"></i>
                            </label>
                        `
                            : `
                            <input type="radio" class="btn-check" 
                                name="attendance_${student.rollNo}" 
                                id="present_${student.rollNo}" 
                                value="present" checked>
                            <label class="switch-label present" 
                                for="present_${student.rollNo}">
                                <i class="fas fa-check"></i>
                            </label>

                            <input type="radio" class="btn-check" 
                                name="attendance_${student.rollNo}" 
                                id="absent_${student.rollNo}" 
                                value="absent">
                            <label class="switch-label absent" 
                                for="absent_${student.rollNo}">
                                <i class="fas fa-times"></i>
                            </label>
                        `
                        }
                    </div>
                </td>
            </tr>
        `;
                })
                .join("");
            }
          },
          error: function (xhr, status, error) {
            console.error("Leave data fetch error:", error);
          },
        });
      } else {
        console.error("Error:", data.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
    },
  });
}

// Function to populate units and topics from lesson plan data
function populateLessonPlan(lessonPlan) {
  console.log("lessonPlan", lessonPlan);
  const unitSelect = document.getElementById("unitSelect");
  const topicSelect = document.getElementById("topicSelect");

  // Populate units
  lessonPlan.forEach((unit) => {
    unitSelect.innerHTML += `<option value="${unit.unit_id}">${unit.unit_name}</option>`;
  });

  // Add event listener for unit selection
  unitSelect.addEventListener("change", function () {
    const selectedUnitId = this.value;
    populateTopics(selectedUnitId, lessonPlan);
  });
}

// Function to populate topics based on selected unit
function populateTopics(unitId, lessonPlan) {
  const topicSelect = document.getElementById("topicSelect");
  topicSelect.innerHTML = '<option value="">Select Topic</option>'; // Reset topics

  const selectedUnit = lessonPlan.find((unit) => unit.unit_id == unitId);
  if (selectedUnit && selectedUnit.topics) {
    selectedUnit.topics.forEach((topic) => {
      topicSelect.innerHTML += `<option value="${topic.name}">${topic.name}</option>`;
    });
  }
}

// Updated saveAttendance function with hour information
function saveAttendance() {
  const students = document.querySelectorAll("#studentsList .student-row");
  const facultyData = JSON.parse(sessionStorage.getItem("selectedFacultyData"));

  const isContinuous =
    document.getElementById("continuousPeriods")?.checked || false;
  const hasConsecutive = hasConsecutivePeriods(currentCourseData);

  const hourType = document.getElementById("hourType").value;
  const unitId = document.getElementById("unitSelect").value;
  const unitName =
    document.getElementById("unitSelect").options[
      document.getElementById("unitSelect").selectedIndex
    ].text;
  const topicName = document.getElementById("topicSelect").value;
  const description = document.getElementById("description").value;

  // If continuous is checked but no consecutive periods exist, use normal save
  const useMultipleSave = isContinuous && hasConsecutive;

  // Collect form data
  const formData = {
    hourType: document.getElementById("hourType").value,
    unitId: document.getElementById("unitSelect").value,
    unitName:
      document.getElementById("unitSelect").options[
        document.getElementById("unitSelect").selectedIndex
      ].text,
    topicName: document.getElementById("topicSelect").value,
    description: document.getElementById("description").value,
  };

  // Collect attendance data
  let attendanceData = [];
  let presentCount = 0;
  let absentCount = 0;
  let odCount = 0;
  let leaveCount = 0;
  let statusSummary = [];

  students.forEach((student) => {
    const rollNo = student.querySelector(".roll-number").textContent;
    const studentRow = student.closest(".student-row");
    let status;

    // First check for OD or Leave (hidden inputs)
    const hiddenInput = studentRow.querySelector(
      `input[type="hidden"][name="attendance_${rollNo}"]`
    );

    if (hiddenInput) {
      // Handle OD and Leave cases
      switch (hiddenInput.value) {
        case "od":
          status = 2;
          odCount++;
          statusSummary.push(`${rollNo} (OD)`);
          break;
        case "leave":
          status = 1;
          leaveCount++;
          statusSummary.push(`${rollNo} (Leave)`);
          break;
      }
    } else {
      // Handle regular attendance (present/absent radio buttons)
      const radioInput = studentRow.querySelector(
        `input[name="attendance_${rollNo}"]:checked`
      );

      if (radioInput) {
        if (radioInput.value === "present") {
          status = 3;
          presentCount++;
        } else {
          status = 0;
          absentCount++;
          statusSummary.push(`${rollNo} (Absent)`);
        }
      } else {
        // Default to absent if no selection
        status = 0;
        absentCount++;
        statusSummary.push(`${rollNo} (No Selection - Marked Absent)`);
      }
    }

    attendanceData.push({
      rollNo: rollNo,
      status: status,
    });

    // Debug log
    console.log(
      `Student ${rollNo}: Status = ${status} (${
        hiddenInput ? "OD/Leave" : "Regular Attendance"
      })`
    );
  });

  if (sessionStorage.getItem("semester")) {
    semester = sessionStorage.getItem("semester");
  } else if (currentCourseData && currentCourseData.semester) {
    semester = currentCourseData.semester;
  } else {
    Swal.fire({
      title: "Error",
      text: "Semester information not found",
      icon: "error",
    });
    return;
  }

  // Get hours only if using multiple save
  let hours = [];
  if (useMultipleSave) {
    const currentDay = currentCourseData.day;
    const currentIndex = currentCourseData.timeIndex;
    const daySchedule = timetableData[currentDay];

    // Get all consecutive periods
    hours.push(currentIndex + 1); // Current period

    // Check previous periods
    let prevIndex = currentIndex - 1;
    while (
      prevIndex >= 0 &&
      daySchedule[prevIndex].course_id === currentCourseData.courseId
    ) {
      hours.unshift(prevIndex + 1);
      prevIndex--;
    }

    // Check next periods
    let nextIndex = currentIndex + 1;
    while (
      nextIndex < daySchedule.length &&
      daySchedule[nextIndex].course_id === currentCourseData.courseId
    ) {
      hours.push(nextIndex + 1);
      nextIndex++;
    }
  }

  // Show confirmation dialog with detailed summary
  Swal.fire({
    title: "Attendance Summary",
    html: `
      <strong>Attendance Details:</strong><br>
      Present Students: ${presentCount}<br>
      Absent Students: ${absentCount}<br>
      OD Students: ${odCount}<br>
      Leave Students: ${leaveCount}<br>
      <br>
      <strong>Status Summary:</strong><br>
      ${statusSummary.join("<br>")}
      ${
        useMultipleSave
          ? `Marking attendance for periods: ${hours.join(", ")}`
          : ""
      }
    `,

    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Confirm",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      const endpoint = useMultipleSave
        ? "saveMultipleAttendance"
        : "saveAttendance";

      // Prepare data for submission
      const submitData = {
        action: endpoint,
        facultyId: facultyData.facultyId,
        courseId: currentCourseData.courseId,
        date: currentCourseData.date,
        semester: semester,
        ...formData,
        attendanceData: JSON.stringify(attendanceData),
      };

      // Add hours array for multiple attendance or single hour
      if (useMultipleSave) {
        submitData.hours = JSON.stringify(hours);
      } else {
        submitData.hour = currentCourseData.timeIndex + 1;
      }

      // Send AJAX request
      $.ajax({
        url: "backend.php",
        type: "POST",
        data: submitData,
        success: function (response) {
          const data = JSON.parse(response);
          if (data.status === "success") {
            Swal.fire({
              title: "Success!",
              text: "Attendance saved successfully",
              icon: "success",
            }).then(() => {
              backToTimetable();
              loadPendingAttendance();
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: data.message || "Failed to save attendance",
              icon: "error",
            });
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX error:", error);
          Swal.fire({
            title: "Error!",
            text: "Failed to save attendance",
            icon: "error",
          });
        },
      });
    }
  });
}

// Function to show new alteration form
function showNewAlterationForm() {
  Swal.fire({
    title: "New Hour Alteration Request",
    html: `
            <form id="alterationForm" class="text-start">
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" id="alteration_date" required 
                           min="${new Date().toISOString().split("T")[0]}" 
                           onchange="loadFacultyClasses(this.value)">
                </div>
                <div class="mb-3">
                    <label class="form-label">Select Class</label>
                    <select class="form-control" id="class_selection" required onchange="updateSubstituteFaculty(this.value)">
                        <option value="">Select Class</option>
                    </select>
                    <input type="hidden" id="selected_timetable_id">
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea class="form-control" id="alteration_reason" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Substitute Faculty</label>
                    <select class="form-control" id="substitute_faculty" required>
                        <option value="">Select Faculty</option>
                    </select>
                </div>
            </form>
        `,
    showCancelButton: true,
    confirmButtonText: "Submit Request",
    cancelButtonText: "Cancel",
    didOpen: () => {
      // Initialize any necessary data
    },
  }).then((result) => {
    if (result.isConfirmed) {
      submitAlterationRequest();
    }
  });
}

function loadIncomingAlterations() {
  const facultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData") || "{}"
  );
  if (!facultyData.facultyId) {
    return;
  }

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "loadIncomingAlterations",
      facultyId: facultyData.facultyId,
    },
    success: function (response) {
      let data;
      try {
        data = JSON.parse(response);
      } catch (error) {
        return;
      }

      const incomingTable = document.getElementById("incomingAlterationsList");
      let alterations = Array.isArray(data.alterations) ? data.alterations : [];

      alterations = alterations.filter(
        (a) => Number(a.new_faculty_id) === Number(facultyData.facultyId)
      );

      if (alterations.length > 0) {
        incomingTable.innerHTML = alterations
          .map((alteration) => {
            const dateStr = new Date(alteration.date).toLocaleDateString();
            const period = alteration.period
              ? "Period " + alteration.period
              : "N/A";
            const course = alteration.course_name || "N/A";
            const requestedBy =
              Number(alteration.original_faculty_id) ===
              Number(alteration.new_faculty_id)
                ? "Self"
                : alteration.original_faculty_id;

            return `
              <tr>
                <td>${dateStr}</td>
                <td>${period}</td>
                <td>${course}</td>
                <td>${requestedBy}</td>
                <td>${alteration.reason}</td>
                <td>
                  <span class="badge bg-${getStatusBadgeColor(
                    alteration.status
                  )}">
                    ${alteration.status}
                  </span>
                </td>
                <td>${getIncomingActionButtons(alteration)}</td>
              </tr>
            `;
          })
          .join("");
      } else {
        incomingTable.innerHTML = `<tr><td colspan="7">No incoming requests found.</td></tr>`;
      }
    },
  });
}

// Function to load faculty classes for selected date
function loadFacultyClasses(selectedDate) {
  const facultyData = JSON.parse(sessionStorage.getItem("selectedFacultyData"));

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getFacultyClasses",
      facultyId: facultyData.facultyId,
      semesterType: facultyData.semesterType,
      date: selectedDate,
      academicYear: facultyData.academicYear,
    },
    success: function (response) {
      const data = JSON.parse(response);
      const classSelect = document.getElementById("class_selection");
      classSelect.innerHTML = '<option value="">Select Class</option>';

      if (data.status === "success") {
        data.classes.forEach((cls) => {
          const optionText = `Period ${cls.period} | ${cls.course_code} - ${cls.course_name} | Section ${cls.section}`;

          const optionValue = `${cls.timetable_id}|${cls.course_id}`;
          classSelect.innerHTML += `
            <option value="${optionValue}">
              ${optionText}
            </option>
          `;
        });
      } else {
        Swal.fire("Error", data.message || "Failed to load classes", "error");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error loading classes:", error);
      Swal.fire("Error", "Failed to load classes", "error");
    },
  });
}

// Function to update substitute faculty options based on selected class
function updateSubstituteFaculty(timetableId) {
  if (!timetableId) return;

  // Get the selected class details from the dropdown
  const selectedOption = document.querySelector(
    `#class_selection option[value="${timetableId}"]`
  );
  const selectedClassText = selectedOption
    ? selectedOption.textContent.split("|")[1].split("-")[1].trim()
    : "";

  document.getElementById("selected_timetable_id").value = timetableId;

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getFacultyList",
      timetableId: timetableId,
      date: document.getElementById("alteration_date").value,
    },
    success: function (response) {
      const data = JSON.parse(response);
      console.log("dataaaaaaaa", data);
      console.log("faculty list", data);
      const facultySelect = document.getElementById("substitute_faculty");
      facultySelect.innerHTML = '<option value="">Select Faculty</option>';

      if (data.status === "success") {
        // Filter out the faculty-course combination that matches the selected class
        const filteredFaculty = data.faculty.filter((faculty) => {
          const facultyOptionText = `${faculty.course_name}`;
          return facultyOptionText !== selectedClassText;
        });

        // Populate dropdown with filtered faculty list
        filteredFaculty.forEach((faculty) => {
          facultySelect.innerHTML += `
            <option value="${faculty.id}|${faculty.course_id}">
              ${faculty.name} - ${faculty.course_name} (${faculty.course_code})
            </option>
          `;
        });
      } else {
        console.error("Error:", data.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

function submitAlterationRequest() {
  const formDate = document.getElementById("alteration_date").value;

  // Validate date is not in the past
  const selectedDate = new Date(formDate);
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  if (selectedDate < today) {
    Swal.fire("Error", "Cannot select a past date", "error");
    return;
  }

  const formData = {
    timetable_id: document.getElementById("selected_timetable_id").value,
    date: formDate,
    reason: document.getElementById("alteration_reason").value,
    substitute_faculty: document.getElementById("substitute_faculty").value,
    faculty_id: JSON.parse(sessionStorage.getItem("selectedFacultyData"))
      .facultyId,
    //       ALTER TABLE alteration
    // ADD COLUMN original_faculty_course_id INT(11) NOT NULL AFTER original_faculty_id,
    // ADD COLUMN new_faculty_course_id INT(11) NOT NULL AFTER new_faculty_id;
    original_faculty_course_id: document
      .getElementById("selected_timetable_id")
      .value.split("|")[1],
    new_faculty_course_id: document
      .getElementById("substitute_faculty")
      .value.split("|")[1],
  };

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "submitAlterationRequest",
      formData: JSON.stringify(formData),
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status === "success") {
        Swal.fire(
          "Success",
          "Alteration request submitted successfully",
          "success"
        );
        loadHourAlterations();
      } else {
        Swal.fire(
          "Error",
          result.message || "Failed to submit request",
          "error"
        );
      }
    },
  });
}

function loadIncomingAlterations() {
  const facultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData") || "{}"
  );
  if (!facultyData.facultyId) {
    return;
  }

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "loadIncomingAlterations",
      facultyId: facultyData.facultyId,
    },
    success: function (response) {
      let data;
      try {
        data = JSON.parse(response);
      } catch (error) {
        return;
      }

      const incomingTable = document.getElementById("incomingAlterationsList");
      let alterations = Array.isArray(data.alterations) ? data.alterations : [];

      alterations = alterations.filter(
        (a) => Number(a.new_faculty_id) === Number(facultyData.facultyId)
      );

      if (alterations.length > 0) {
        incomingTable.innerHTML = alterations
          .map((alteration) => {
            const dateStr = new Date(alteration.date).toLocaleDateString();
            const period = alteration.period
              ? "Period " + alteration.period
              : "N/A";
            const course = alteration.course_name || "N/A";
            const requestedBy =
              Number(alteration.original_faculty_id) ===
              Number(alteration.new_faculty_id)
                ? "Self"
                : alteration.original_faculty_id;

            return `
              <tr>
                <td>${dateStr}</td>
                <td>${period}</td>
                <td>${course}</td>
                <td>${requestedBy}</td>
                <td>${alteration.reason}</td>
                <td>
                  <span class="badge bg-${getStatusBadgeColor(
                    alteration.status
                  )}">
                    ${alteration.status}
                  </span>
                </td>
                <td>${getIncomingActionButtons(alteration)}</td>
              </tr>
            `;
          })
          .join("");
      } else {
        incomingTable.innerHTML = `<tr><td colspan="7">No incoming requests found.</td></tr>`;
      }
    },
  });
}

// Function to load both incoming and outgoing hour alterations
// for the "My Hour Alteration Requests" tab.
function loadHourAlterations() {
  const facultyData =
    JSON.parse(sessionStorage.getItem("selectedFacultyData")) || {};
  const currentFacultyName = facultyData.facultyName
    ? facultyData.facultyName.toLowerCase()
    : "";

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getHourAlterations",
      facultyId: facultyData.facultyId,
      semesterType: facultyData.semesterType,
      academicYear: facultyData.academicYear,
    },
    success: function (response) {
      let data;
      try {
        data = JSON.parse(response);
        console.log("data", data);
      } catch (error) {
        console.error("Error parsing response:", error, response);
        return;
      }

      if (data.status === "success") {
        const myAlterationsTable = document.getElementById("myAlterationsList");
        const incomingAlterationsTable = document.getElementById(
          "incomingAlterationsList"
        );

        let myAlterations = [];
        let incomingAlterations = [];

        if (Array.isArray(data.alterations)) {
          data.alterations.forEach((alteration) => {
            // If facultyName is available and matches the substitute_name, treat it as an incoming request.
            if (
              currentFacultyName &&
              alteration.substitute_name &&
              alteration.substitute_name.toLowerCase() === currentFacultyName
            ) {
              incomingAlterations.push(alteration);
            } else {
              myAlterations.push(alteration);
            }
          });
        }

        // If facultyName is not set, assume all records are outgoing.
        if (!currentFacultyName) {
          myAlterations = data.alterations;
        }

        // Populate outgoing alterations table.
        if (myAlterations.length > 0) {
          myAlterationsTable.innerHTML = myAlterations
            .map(
              (alteration) => `
              <tr>
                <td>${new Date(alteration.date).toLocaleDateString()}</td>
                <td>Period ${alteration.period}</td>
                <td>${alteration.course_name}</td>
                <td>${alteration.substitute_name}</td>
                <td>${alteration.reason}</td>
                <td>
                  <span class="badge bg-${getStatusBadgeColor(
                    alteration.status
                  )}">
                    ${alteration.status}
                  </span>
                </td>
                <td>${getMyActionButtons(alteration)}</td>
              </tr>
            `
            )
            .join("");
        } else {
          myAlterationsTable.innerHTML = `<tr><td colspan="7">No requests found.</td></tr>`;
        }
      } else {
        console.error("Failed to load alterations:", data.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error loading alterations:", error);
    },
  });
}

// Returns a Bootstrap badge color based on the status.
function getStatusBadgeColor(status) {
  switch (String(status).toLowerCase()) {
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

// Generates action buttons for outgoing alterations (e.g., cancel request).
function getMyActionButtons(alteration) {
  if (alteration.status.toLowerCase() === "pending") {
    return `
      <button class="btn btn-sm btn-danger" title="Cancel Request" onclick="deleteAlteration(${alteration.alteration_id})">
        <i class="fas fa-times"></i>
      </button>
    `;
  }
  return "";
}

// Generates action buttons for incoming alterations (e.g., accept/reject).
function getIncomingActionButtons(alteration) {
  if (String(alteration.status).toLowerCase() === "pending") {
    return `
      <button class="btn btn-sm btn-success" title="Accept Request" onclick="acceptAlteration(${alteration.alteration_id})">
        <i class="fas fa-check"></i>
      </button>
      <button class="btn btn-sm btn-danger" title="Reject Request" onclick="openRejectModal(${alteration.alteration_id})">
        <i class="fas fa-times"></i>
      </button>
    `;
  }
  return "";
}

function deleteAlteration(alterationId) {
  Swal.fire({
    title: "Confirm Deletion",
    text: "Are you sure you want to delete this alteration request?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      const facultyData = JSON.parse(
        sessionStorage.getItem("selectedFacultyData")
      );
      $.ajax({
        url: "backend.php",
        type: "POST",
        data: {
          action: "deleteHourAlteration",
          alteration_id: alterationId,
          facultyId: facultyData.facultyId,
        },
        success: function (response) {
          const data = JSON.parse(response);
          if (data.status === "success") {
            Swal.fire("Deleted!", data.message, "success").then(() => {
              loadHourAlterations(); // Refresh the alterations list
            });
          } else {
            Swal.fire("Error", data.message, "error");
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
          Swal.fire("Error", "Failed to delete the request.", "error");
        },
      });
    }
  });
}

function getRoleBadgeColor(role) {
  switch (role.toLowerCase()) {
    case "requested":
      return "primary";
    case "substitute":
      return "info";
    default:
      return "secondary";
  }
}

function loadPendingAttendance() {
  const facultyData = JSON.parse(sessionStorage.getItem("userData"));
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getFacultyPendingAttendance",
      facultyId: selectedFacultyData.facultyId,
      academicYear: selectedFacultyData.academicYear,
      semesterType: selectedFacultyData.semesterType,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        displayPendingAttendance(data.pendingList);
      }
    },
  });
}

function displayPendingAttendance(pendingList) {
  const tbody = $("#pendingAttendanceList");
  tbody.empty();

  if (pendingList.length === 0) {
    tbody.append(`
            <tr>
                <td colspan="8" class="text-center">No pending attendance entries</td>
            </tr>
        `);
    return;
  }

  pendingList.forEach((entry) => {
    const row = `
            <tr>
                <td>${new Date(entry.class_date).toLocaleDateString()}</td>
                <td>${entry.period}</td>
                <td>${entry.course_name} (${entry.course_code})</td>
                <td>${entry.batch}</td>
                <td>${entry.academic_year}</td>
                <td>${entry.semester}</td>
                <td>${entry.section}</td>
                <td>
                    ${
                      entry.attendance_status === 4
                        ? `
                       
                        <button class="btn btn-danger btn-sm ms-2" onclick="Swal.fire({
                            title: 'Attendance Locked',
                            text: 'The attendance is locked due to delay in marking the attendance.The request is automatically send to HOD.',
                            icon: 'info',
                        })">
                            LOCKED
                        </button>
                    `
                        : `
                        <button class="btn btn-primary btn-sm" 
                                onclick="handlePendingAttendance({
                                    date: '${entry.class_date}',
                                    semester: '${entry.semester}',
                                    courseId: '${entry.course_id}',
                                    name: '${entry.course_name}',
                                    timeIndex: ${entry.period - 1},
                                    day: '${new Date(
                                      entry.class_date
                                    ).toLocaleDateString("en-US", {
                                      weekday: "long",
                                    })}',
                                    batch: '${entry.batch}',
                                    section: '${entry.section}',
                                    teacher: '${
                                      JSON.parse(
                                        sessionStorage.getItem("userData")
                                      ).name
                                    }'
                                })">
                            Take Attendance
                        </button>
                    `
                    }
                </td>
            </tr>
        `;
    tbody.append(row);
  });
}

function handlePendingAttendance(courseData) {
  // Switch to timetable tab first
  console.log(courseData);
  $("#time-table-tab").tab("show");

  // Hide the timetable view and show course details
  document.getElementById("timetableView").classList.add("d-none");
  document.getElementById("courseDetailsView").classList.remove("d-none");

  // Then show the course details
  showCourseDetails(courseData, "pendingAttendance");
}

function takeAttendance(timetableId, date, period) {
  // Store the current course data
  const currentCourseData = {
    timetableId: timetableId,
    date: date,
    timeIndex: period - 1, // Adjust period to timeIndex (if your system uses 0-based index)
  };

  // Store in session storage for the attendance form to use
  sessionStorage.setItem(
    "currentCourseData",
    JSON.stringify(currentCourseData)
  );
}

function toggleContinuousPeriods() {
  const checkbox = document.getElementById("continuousPeriods");
  const button = event.currentTarget;

  checkbox.checked = !checkbox.checked;

  // Toggle button appearance
  if (checkbox.checked) {
    button.classList.remove("btn-outline-success");
    button.classList.add("btn-success");
  } else {
    button.classList.add("btn-outline-success");
    button.classList.remove("btn-success");
  }
}

function hasConsecutivePeriods(courseData) {
  const currentDay = courseData.day;
  const currentIndex = courseData.timeIndex;
  const currentCourseId = courseData.courseId;

  // Get day's schedule from timetableData
  const daySchedule = timetableData[currentDay];

  // Check all previous periods until we find a different course
  let hasPreviousPeriod = false;
  let prevIndex = currentIndex - 1;
  while (prevIndex >= 0) {
    if (daySchedule[prevIndex].course_id === currentCourseId) {
      hasPreviousPeriod = true;
      prevIndex--;
    } else {
      break;
    }
  }

  // Check all next periods until we find a different course
  let hasNextPeriod = false;
  let nextIndex = currentIndex + 1;
  while (nextIndex < daySchedule.length) {
    if (daySchedule[nextIndex].course_id === currentCourseId) {
      hasNextPeriod = true;
      nextIndex++;
    } else {
      break;
    }
  }

  return hasPreviousPeriod || hasNextPeriod;
}

// Add this function to load course data
function loadFacultyCourses() {
  // display none

  document.getElementById("courseReportsView").classList.add("d-none");
  const facultyData = JSON.parse(sessionStorage.getItem("selectedFacultyData"));
  if (!facultyData) {
    console.error("No faculty data found");
    return;
  }

  $.ajax({
    url: "backend.php", // Updated URL
    type: "POST",
    data: {
      action: "getFacultyCourses", // Added action parameter
      faculty_id: facultyData.facultyId,
      academic_year: facultyData.academicYear,
      semester_type: facultyData.semesterType,
    },
    success: function (response) {
      try {
        const result =
          typeof response === "string" ? JSON.parse(response) : response;
        if (result.status === "success") {
          renderCourseCards(result.data);
        } else {
          Swal.fire({
            title: "Error",
            text: result.message || "Failed to load courses",
            icon: "error",
          });
        }
      } catch (e) {
        console.error("Error parsing response:", e);
        Swal.fire({
          title: "Error",
          text: "Failed to process course data",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      Swal.fire({
        title: "Error",
        text: "Failed to fetch course data",
        icon: "error",
      });
    },
  });
}

// Function to render course cards with an improved UI
function renderCourseCards(courses) {
  coursesGrid.innerHTML = "";

  if (!courses.length) {
    coursesGrid.innerHTML = `
      <div class="alert alert-info text-center p-5 rounded-3 shadow-sm">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h4>No Courses Available</h4>
        <p class="mb-0">There are no courses assigned to you this semester.</p>
      </div>
    `;
    return;
  }

  // Add header section
  const headerSection = `
    <div class="courses-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="courses-title mb-0">Your Courses</h3>
        </div>
        <div>
            <span class="badge bg-primary rounded-pill">
                ${courses.length} Course${
    courses.length !== 1 ? "s" : ""
  } available
            </span>
        </div>
    </div>
    <div class="row justify-content-center g-4">
  `;
  coursesGrid.innerHTML = headerSection;

  courses.forEach((course) => {
    const courseCard = `
      <div class="col-12 col-md-6 col-xl-4">
        <div class="course-card h-100 border mx-auto" style="max-width: 400px;">
          <div class="course-card-header p-3" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
            <h4 class="course-name mb-0">${course.course_name}</h4>
            <span class="status-badge ${getStatusClass(course.status)}">
              ${getStatusIcon(course.status)}
              ${course.status}
            </span>
            <div class="course-code text-muted">${course.course_code}</div>
          </div>
          <div class="course-card-body">
            <div class="course-info-grid">
              <div class="info-item d-flex align-items-center mb-2">
                <div class="icon-box me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 6px;">
                  <i class="fas fa-building text-primary"></i>
                </div>
                <div class="info-text">
                  <small class="text-muted d-block">Department</small>
                  <span class="fw-medium">${course.department}</span>
                </div>
              </div>
              <div class="info-item d-flex align-items-center mb-2">
                <div class="icon-box me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 6px;">
                  <i class="fas fa-users text-success"></i>
                </div>
                <div class="info-text">
                  <small class="text-muted d-block">Batch</small>
                  <span class="fw-medium">${course.batch}</span>
                </div>
              </div>
              <div class="info-item d-flex align-items-center mb-2">
                <div class="icon-box me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 6px;">
                  <i class="fas fa-layer-group text-info"></i>
                </div>
                <div class="info-text">
                  <small class="text-muted d-block">Section</small>
                  <span class="fw-medium">${course.section}</span>
                </div>
              </div>
              <div class="info-item d-flex align-items-center">
                <div class="icon-box me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 6px;">
                  <i class="fas fa-graduation-cap text-danger"></i>
                </div>
                <div class="info-text">
                  <small class="text-muted d-block">Semester</small>
                  <span class="fw-medium">${course.semester}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="course-card-footer bg-light p-3">
            <div class="course-actions">
              <div class="row g-2 ">
                <div class="col flex-grow-1">
                  <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" 
                          onclick="viewMarksDetails(${course.course_id})">
                    <i class="fas fa-chart-bar me-2"></i>
                    <span>Marks</span>
                  </button>
                </div>
                <div class="col flex-grow-1 mx-2">
                  <button class="btn btn-info text-white w-100 d-flex align-items-center justify-content-center" 
                          onclick="viewCourseReports(${course.course_id})">
                    <i class="fas fa-file-alt me-2"></i>
                    <span>Reports</span>
                  </button>
                </div>
                <div class="col flex-grow-1">
                  <button class="btn btn-success w-100 d-flex align-items-center justify-content-center" 
                          onclick="showLmsPlan('${course.course_id}', '${
      course.course_name
    }', '${course.course_code}')">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span>LMS</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    // Use insertAdjacentHTML for better performance
    document.querySelector(".row").insertAdjacentHTML("beforeend", courseCard);
  });

  // Add hover effect
  document.querySelectorAll(".course-card").forEach((card) => {
    card.addEventListener("mouseenter", () => {
      card.style.transform = "translateY(-5px)";
      card.style.boxShadow = "0 10px 20px rgba(0,0,0,0.1)";
    });
    card.addEventListener("mouseleave", () => {
      card.style.transform = "translateY(0)";
      card.style.boxShadow = "0 2px 4px rgba(0,0,0,0.05)";
    });
  });
}

function viewCourseReports(courseId) {
  currentCourseId = courseId;
  // Store the courseId in a hidden input so that it can be accessed later.
  document.getElementById("courseReportsCourseId").value = courseId;

  // Optionally, display the courseId on the reports view for user information.
  document.getElementById("displayCourseId").innerText = courseId;

  // Hide course info content and show reports view.
  document.getElementById("courseInfoContent").classList.add("d-none");
  document.getElementById("courseReportsView").classList.remove("d-none");
}
function backToCourseInfo() {
  // Show course info content, hide reports view
  document.getElementById("courseInfoContent").classList.remove("d-none");
  document.getElementById("marksView").classList.add("d-none");
  document.getElementById("componentsContainer").classList.add("d-none");
  document.getElementById("courseReportsView").classList.add("d-none");
}

function showStudentListView() {
  document.getElementById("courseReportsView").classList.add("d-none");
  document.getElementById("studentListView").classList.remove("d-none");

  fetchCourseStudentsList(currentCourseId);
}

function backToCourseReports() {
  document.getElementById("studentListView").classList.add("d-none");
  document.getElementById("attendanceSummaryView").classList.add("d-none");
  document.getElementById("attendancePercentageView").classList.add("d-none");
  document.getElementById("courseReportsView").classList.remove("d-none");
}

function showAttendanceSummaryView() {
  document.getElementById("courseReportsView").classList.add("d-none");
  document.getElementById("attendanceSummaryView").classList.remove("d-none");
  loadAttendanceSummary();
}

function populateAttendacePercentage(students) {
  // If the DataTable is already initialized, destroy it before reinitializing.
  if ($.fn.DataTable.isDataTable("#attendancePercentageTable")) {
    $("#attendancePercentageTable").DataTable().clear().destroy();
  }

  // Initialize DataTable with the students data.
  $("#attendancePercentageTable").DataTable({
    data: students,
    columns: [
      {
        title: "S.no",
        data: null,
        className: "text-center small fw-medium py-3",
        render: function (data, type, row, meta) {
          return meta.row + 1; // Auto index numbering.
        },
      },
      {
        title: "Roll No",
        data: "register_number",
        className: "text-center small fw-medium py-3",
      },
      {
        title: "Student Name",
        data: "student_name",
        className: "text-center small fw-medium py-3",
      },
      {
        title: "Total Hours",
        data: "total_hours",
        className: "text-center small fw-medium py-3",
      },
      {
        title: "Present Hours",
        data: "present_hours",
        className: "text-center small fw-medium py-3",
      },
      {
        title: "Attendance Percentage",
        data: "attendance_percentage",
        className: "text-center small fw-medium py-3",
        render: function (data, type, row, meta) {
          return data + "%";
        },
      },
    ],
    paging: true,
    pageLength: 10, // Number of records per page.
    lengthChange: false, // Hides the page length menu.
    searching: false, // Disable search if not needed.
    ordering: false, // Disable ordering if not needed.
    info: true,
    language: {
      info: "Showing _START_ to _END_ of _TOTAL_ entries",
      zeroRecords: "No attendance percentage data available",
      paginate: {
        previous: "&laquo;",
        next: "&raquo;",
      },
    },
  });
}

// Function to fetch the student list for a given course ID
function fetchCourseStudentsList(courseId) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getCourseStudents",
      courseId: courseId,
    },
    success: function (response) {
      let res;
      try {
        res = typeof response === "string" ? JSON.parse(response) : response;
      } catch (e) {
        console.error("Error parsing response:", e);
        return;
      }
      if (res.status === "success") {
        renderStudentsList(res.students);
      } else {
        console.error("Failed to load students:", res.message);
        // Optionally, you could show an error message on the UI here.
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

function renderStudentsList(students) {
  // If no students, display a message in the table body.
  if (students.length === 0) {
    $("#studentListTable tbody").html(`
      <tr>
        <td colspan="4" class="text-center py-5 text-muted">
          <i class="fas fa-users-slash fa-2x mb-3"></i>
          <p class="mb-0">No students found</p>
        </td>
      </tr>
    `);
    return;
  }

  // If the DataTable has been already initialized, destroy it first
  if ($.fn.DataTable.isDataTable("#studentListTable")) {
    $("#studentListTable").DataTable().destroy();
  }

  // Clear the table body
  $("#studentListTable tbody").empty();

  // Initialize DataTable with the student data.
  $("#studentListTable").DataTable({
    data: students,
    columns: [
      {
        data: null,
        title: "SNO",
        render: function (data, type, row, meta) {
          return meta.row + 1; // auto index numbering
        },
        className: "text-center",
      },
      {
        data: "register_number",
        title: "Register Number",
        className: "text-center",
      },
      {
        data: "name",
        title: "Name",
      },
      {
        data: "batch",
        title: "Batch",
        className: "text-center",
      },
    ],
    pageLength: 10, // change this to your desired page size
    lengthChange: false, // hides the page size dropdown
    language: {
      emptyTable: "No students found",
    },
    // You can add more DataTable options here as needed.
  });
}

function loadAttendanceSummary(page = 1) {
  console.log("Loading attendance summary", currentCourseId);
  const courseId = currentCourseId;
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAttendanceSummary",
      courseId: courseId,
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          console.log(data.attendanceSummary);
          populateAttendanceSummaryView(data.attendanceSummary);
        } else {
          Swal.fire({
            title: "Error",
            text: data.message,
            icon: "error",
          });
        }
      } catch (err) {
        console.error("Error parsing response:", err);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

function populateAttendanceSummaryView(attendanceSummary) {
  // If the DataTable is already initialized, destroy it so we can reinitialize with new data.
  if ($.fn.DataTable.isDataTable("#attendanceSummaryTable")) {
    $("#attendanceSummaryTable").DataTable().clear().destroy();
  }

  // Initialize DataTable with attendanceSummary data (pagination disabled)
  $("#attendanceSummaryTable").DataTable({
    data: attendanceSummary,
    columns: [
      {
        title: "S.no",
        data: null,
        className: "text-center",
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      {
        title: "Class Date",
        data: "class_date",
        className: "text-center",
      },
      {
        title: "Day",
        data: "day",
        className: "text-center",
      },
      {
        title: "Hour",
        data: "hour",
        className: "text-center",
      },
      {
        title: "Leave",
        data: "leave",
        className: "text-center",
      },
      {
        title: "Absent",
        data: "absent",
        className: "text-center",
      },
      {
        title: "OD",
        data: "od",
        className: "text-center",
      },
      {
        title: "Description",
        data: "description",
      },
      {
        title: "Total Students",
        data: "total_students",
        className: "text-center",
      },
    ],
    // Disable pagination
    paging: true,

    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    language: {
      info: "Showing all entries",
      zeroRecords: "No attendance summary found",
    },
  });
}

function showLmsPlan(courseId, courseName, courseCode) {
  // Store courseId globally for later use in updates
  window.currentCourseId = courseId;
  console.log(courseId, courseName, courseCode);

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getLms",
      course_id: courseId,
    },
    dataType: "json",
    success: function (result) {
      if (result.status === "success") {
        const availableCoursesContainer = $("#coursesGrid");
        availableCoursesContainer.empty();

        const customStyles = `
          <style>
            .lms-container {
              background: #f8f9fa;
              min-height: 100vh;
              padding: 2rem;
            }
            .course-header {
              background: linear-gradient(135deg, #0061f2 0%, #6900f2 100%);
              color: white;
              padding: 1rem 1.5rem;
              border-radius: 15px;
              margin-bottom: 1rem;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
            .course-stats {
              background: rgba(255, 255, 255, 0.1);
              padding: 0.5rem 1rem;
              border-radius: 8px;
              backdrop-filter: blur(5px);
            }
            .unit-card {
              background: white;
              border-radius: 15px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
              margin-bottom: 2rem;
              overflow: hidden;
            }
            .unit-header {
              background: #f8f9fa;
              padding: 1.5rem;
              border-bottom: 1px solid #eee;
            }
            .unit-number {
              width: 45px;
              height: 45px;
              background: #0061f2;
              color: white;
              border-radius: 12px;
              display: flex;
              align-items: center;
              justify-content: center;
              font-size: 1.2rem;
              font-weight: 600;
            }
            .unit-content {
              padding: 1.5rem;
            }
            .topic-card {
              background: #f8f9fa;
              border-radius: 12px;
              padding: 1.5rem;
              height: 100%;
              transition: all 0.3s ease;
            }
            .topic-card:hover {
              transform: translateY(-5px);
              box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            }
            .topic-icon {
              width: 50px;
              height: 50px;
              background: #e3f2fd;
              border-radius: 12px;
              display: flex;
              align-items: center;
              justify-content: center;
              color: #0061f2;
              font-size: 1.2rem;
            }
            .input-group {
              margin-bottom: 1rem;
            }
            .input-group-text {
              background: white;
              border: 1px solid #dee2e6;
              border-right: none;
            }
            .form-control {
              border: 1px solid #dee2e6;
              padding: 0.75rem 1rem;
            }
            .form-control:focus {
              border-color: #0061f2;
              box-shadow: none;
            }
            .btn-upload {
              color: white;
              background: #0061f2;
              border: none;
              padding: 0.75rem 1.5rem;
              border-radius: 8px;
              transition: all 0.3s ease;
            }
            .btn-upload:hover {
              background: #0056e0;
              transform: translateY(-2px);
            }
            .badge {
              padding: 0.5rem 1rem;
              border-radius: 6px;
              font-weight: 500;
            }
            .badge-co {
              background: #e3f2fd;
              color: #0061f2;
            }
            .badge-topics {
              background: #fff3e0;
              color: #f57c00;
            }
            .unit-action-btn {
              background: linear-gradient(135deg, #0061f2 0%, #6900f2 100%);
              border: none;
              padding: 0.8rem 2rem;
              transition: all 0.3s ease;
              box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            .unit-action-btn:hover {
              transform: translateY(-2px);
              box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
              background: linear-gradient(135deg, #0056e0 0%, #5c00d2 100%);
            }

            /* Improved Mobile Responsive Styles */
            @media (max-width: 768px) {
              .lms-container {
                padding: 1rem;
              }
              .course-header {
                padding: 1rem;
                margin-bottom: 1rem;
              }
              .course-header h2 {
                font-size: 1.25rem;
                margin-bottom: 0.5rem;
              }
              .course-stats {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
              }
              .d-flex.gap-3 {
                gap: 0.5rem !important;
              }
              .unit-card {
                margin-bottom: 1rem;
              }
              .unit-header {
                padding: 1rem;
              }
              .unit-number {
                width: 35px;
                height: 35px;
                font-size: 1rem;
                border-radius: 8px;
              }
              .unit-content {
                padding: 1rem;
              }
              .topic-card {
                padding: 1rem;
                margin-bottom: 1rem;
              }
              .topic-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
              }
              .topic-card h5 {
                font-size: 1rem;
                margin-bottom: 0.25rem;
              }
              .badge {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
              }
              .input-group {
                margin-bottom: 0.75rem;
              }
              .form-control {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
              }
              .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
              }
              .video-btn, .pdf-btn {
                width: 100%;
                margin-bottom: 0.5rem;
              }
              .edit-topic-btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
              }
              .topic-inputs {
                margin-top: 1rem;
              }
              .form-label {
                font-size: 0.9rem;
                margin-bottom: 0.25rem;
              }
              #saveAllUnitsBtn {
                width: 100%;
                margin-top: 1rem;
                padding: 0.75rem;
              }
              .d-flex.align-items-center.gap-3 {
                flex-wrap: wrap;
              }
              .d-flex.gap-2 {
                flex-wrap: wrap;
              }
              .topic-card .d-flex.align-items-center.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
              }
              .topic-card .d-flex.align-items-center.gap-3 {
                width: 100%;
              }
              .edit-topic-btn {
                width: 100%;
              }
            }

            /* Additional styles for very small screens */
            @media (max-width: 375px) {
              .course-header h2 {
                font-size: 1.1rem;
              }
              .course-stats {
                font-size: 0.8rem;
              }
              .unit-header h4 {
                font-size: 1rem;
              }
              .badge {
                font-size: 0.7rem;
              }
            }
          </style>
        `;

        // Build the LMS plan view. For each unit we check the topics.
        const lessonPlanView = `
          ${customStyles}
          <div class="lms-container">
            <div class="course-header">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                  <h2 class="mb-2">${courseName}</h2>
                  <div class="d-flex gap-3">
                    <div class="course-stats">
                      <i class="fas fa-code me-2"></i>${courseCode}
                    </div>
                    <div class="course-stats">
                      <i class="fas fa-layer-group me-2"></i>${
                        result.data.total_units
                      } Units
                    </div>
                  </div>
                </div>
                <button class="btn btn-light" onclick="loadFacultyCourses();">
                  <i class="fas fa-arrow-left me-2"></i>Back
                </button>
              </div>
            </div>
            <div class="units-container">
              ${result.data.units
                .map((unit) => {
                  return `
                    <div class="unit-card" data-unit-id="${unit.unit_id}">
                      <div class="unit-header">
                        <div class="d-flex align-items-center gap-3">
                          <div class="unit-number">${unit.unit_number}</div>
                          <div>
                            <h4 class="mb-2">${unit.unit_name}</h4>
                            <div class="d-flex gap-2">
                              <span class="badge badge-co">
                                <i class="fas fa-bullseye me-2"></i>${unit.CO}
                              </span>
                              <span class="badge badge-topics">
                                <i class="fas fa-list me-2"></i>${
                                  unit.topics.length
                                } Topics
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="unit-content">
                        <div class="row g-4">
                          ${unit.topics
                            .map((topic) => {
                              // Determine if both video and pdf data are available
                              const hasData = topic.video_link && topic.notes;
                              return `
                              <div class="col-md-4 col-sm-6">
                                <div class="topic-card" data-topic-id="${
                                  topic.topic_id
                                }">
                                  <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                      <div class="topic-icon">
                                        <i class="fas fa-book"></i>
                                      </div>
                                      <div>
                                        <h5 class="mb-1">${
                                          topic.topic_name
                                        }</h5>
                                        <small class="text-muted">Topic ${
                                          topic.topic_id
                                        }</small>
                                      </div>
                                    </div>
                                    ${
                                      hasData
                                        ? `<button class="btn btn-sm btn-primary edit-topic-btn" onclick="toggleTopicEdit(${topic.topic_id})">
                                             <i class="fas fa-edit"></i> Edit
                                           </button>`
                                        : ""
                                    }
                                  </div>
                                  <div class="topic-inputs">
                                    ${
                                      hasData
                                        ? `
                                          <!-- View Mode: Show buttons for video and PDF -->
                                          <div class="topic-view" data-topic-id="${topic.topic_id}">
                                            <button class="btn btn-primary video-btn" onclick="window.open('${topic.video_link}', '_blank')">
                                              <i class="fas fa-play me-2"></i>Watch Video
                                            </button>
                                            <button class="btn btn-secondary pdf-btn" onclick="window.open('${topic.notes}', '_blank')">
                                              <i class="fas fa-file-pdf me-2"></i>View PDF
                                            </button>
                                          </div>
                                        `
                                        : ""
                                    }
                                    <!-- Edit Mode: Show input fields -->
                                    <div class="topic-editing" data-topic-id="${
                                      topic.topic_id
                                    }" style="display: ${
                                hasData ? "none" : "block"
                              }">
                                      <div class="form-group mb-3">
                                        <label class="form-label">Video Link</label>
                                        <div class="input-group">
                                          <span class="input-group-text">
                                            <i class="fas fa-video text-primary"></i>
                                          </span>
                                          <input type="text" class="form-control video-link-input" placeholder="Enter video URL" value="${
                                            topic.video_link || ""
                                          }" data-topic-id="${topic.topic_id}">
                                        </div>
                                      </div>
                                      <div class="form-group mb-3">
                                        <label class="form-label">Notes PDF</label>
                                        <div class="input-group">
                                          <span class="input-group-text">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                          </span>
                                          <input type="file" class="form-control pdf-input" accept="application/pdf" data-topic-id="${
                                            topic.topic_id
                                          }">
                                        </div>
                                      </div>
                                      ${
                                        hasData
                                          ? `
                                      <button class="btn btn-success mt-3 save-topic-btn w-100" onclick="saveTopicChanges(${topic.topic_id})">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                      </button>
                                      `
                                          : ""
                                      }
                                    </div>
                                  </div>
                                </div>
                              </div>
                            `;
                            })
                            .join("")}
                        </div>
                      </div>
                    </div>
                  `;
                })
                .join("")}
            </div>
            <!-- Global save button for all units -->
            <div class="text-end mt-4">
            
              <button id="saveAllUnitsBtn" onclick="saveAllUnitsContent('${courseId}', '${courseName}', '${courseCode}');" class="btn btn-primary btn-lg" style="min-width: 150px;">
                <i class="fas fa-save me-2"></i>Save All Changes
              </button>
            </div>
          </div>
        `;

        availableCoursesContainer.html(lessonPlanView);
        setupTopicEventListeners();

        // Hide the global Save All Changes button if no topic is in edit mode (all topics have both video and notes available)
        if ($(".topic-editing:visible").length === 0) {
          $("#saveAllUnitsBtn").hide();
        } else {
          $("#saveAllUnitsBtn").show();
        }
      } else {
        Swal.fire({
          title: "Error",
          text: result.message || "Failed to load LMS data",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      Swal.fire({
        title: "Error",
        text: "Failed to load LMS data",
        icon: "error",
      });
    },
  });
}

// Toggle between view mode (buttons) and edit mode (input fields) for a topic
function toggleTopicEdit(topicId) {
  const topicCard = $(`.topic-card[data-topic-id='${topicId}']`);
  const viewSection = topicCard.find(".topic-view");
  const editSection = topicCard.find(".topic-editing");
  const editBtn = topicCard.find(".edit-topic-btn");

  if (viewSection.is(":visible")) {
    viewSection.hide();
    editSection.show();
    editBtn.html('<i class="fas fa-times"></i> Cancel Edit');
    editBtn.removeClass("btn-primary").addClass("btn-danger");
  } else {
    viewSection.show();
    editSection.hide();
    editBtn.html('<i class="fas fa-edit"></i> Edit');
    editBtn.removeClass("btn-danger").addClass("btn-primary");
  }
}

function saveTopicChanges(topicId) {
  const topicCard = $(`.topic-card[data-topic-id='${topicId}']`);
  const videoLink = topicCard.find(".video-link-input").val();
  const pdfFile = topicCard.find(".pdf-input")[0].files[0];
  console.log(topicId, videoLink, pdfFile);
  const formData = new FormData();
  formData.append("action", "editLmsTopic");
  formData.append("topic_id", topicId);
  formData.append("video_link", videoLink);
  if (pdfFile) {
    formData.append("pdf", pdfFile);
  }
  console.log(formData);
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      Swal.fire({
        title: "Success",
        text: response.message,
        icon: "success",
        confirmButtonText: "OK",
        timer: 1500,
      });
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });

  // Optionally, if you want to revert back to view mode after a successful save:
  const viewSection = topicCard.find(".topic-view");
  if (videoLink && pdfFile) {
    // Here you might update the view-mode buttons or their associated events.
    // For now, we just revert to view mode.
    topicCard
      .find(".edit-topic-btn")
      .html('<i class="fas fa-edit"></i> Edit')
      .removeClass("btn-danger")
      .addClass("btn-primary");
    viewSection.show();
    topicCard.find(".topic-editing").hide();
  }
}

function setupTopicEventListeners() {
  $(document)
    .off("click", ".watch-video-btn")
    .on("click", ".watch-video-btn", function () {
      const videoLink = $(this).data("video-link");
      $("#videoContainer").html(
        `<iframe src="${videoLink}" frameborder="0" width="100%" height="500px" allowfullscreen></iframe>`
      );
      $("#videoModal").modal("show");
    });

  $(document)
    .off("click", ".show-pdf-btn")
    .on("click", ".show-pdf-btn", function () {
      const pdfLink = $(this).data("pdf");
      $("#pdfContainer").attr("src", pdfLink);
      $("#pdfModal").modal("show");
    });

  $(document)
    .off("click", ".edit-topic-btn")
    .on("click", ".edit-topic-btn", function () {
      const topicId = $(this).data("topic-id");
      const topicCard = $(`.topic-card[data-topic-id="${topicId}"]`);

      if ($(this).text().trim() === "Edit") {
        // Switch to edit mode
        topicCard.find(".topic-readonly").hide();
        topicCard.find(".topic-editing").show();

        // Hide "Watch Video" and "Show PDF" buttons
        topicCard.find(".watch-video-btn, .show-pdf-btn").hide();

        // Pre-fill video input field with current video link
        const currentVideoLink =
          topicCard.find(".topic-readonly .video-link").attr("href") || "";
        topicCard.find("input.video-link-input").val(currentVideoLink);

        // Show current PDF link
        const currentPdfLink = topicCard
          .find(".topic-readonly .pdf-link")
          .attr("href");

        if (currentPdfLink) {
          topicCard
            .find(".current-pdf-info")
            .html(
              `Current PDF: <a href="${currentPdfLink}" target="_blank">View PDF</a>`
            );
        } else {
          topicCard.find(".current-pdf-info").empty();
        }

        // Change button text to "Save Changes"
        $(this).text("Save Changes");
      } else {
        // Save changes
        const unitId = topicCard.closest(".unit-card").data("unit-id");
        const updatedVideoLink = topicCard
          .find("input.video-link-input")
          .val()
          .trim();
        const fileInput = topicCard.find("input.pdf-input")[0];
        const updatedFile =
          fileInput && fileInput.files.length > 0 ? fileInput.files[0] : null;

        const formData = new FormData();
        formData.append("action", "editLmsTopic");
        formData.append("topic_id", topicId);
        formData.append("unit_id", unitId);
        formData.append("video_link", updatedVideoLink);

        if (updatedFile) {
          formData.append("pdf", updatedFile);
        }

        $.ajax({
          url: "backend.php",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (response) {
            let result;
            try {
              result =
                typeof response === "object" ? response : JSON.parse(response);
            } catch (e) {
              Swal.fire({
                title: "Error",
                text: "Invalid response from server",
                icon: "error",
              });
              return;
            }
            if (result.status === "success") {
              // Update read-only view with new video link
              topicCard
                .find(".topic-readonly .video-link")
                .text(updatedVideoLink);
              topicCard
                .find(".topic-readonly .video-link")
                .attr("href", updatedVideoLink);

              if (updatedFile && result.new_pdf) {
                topicCard
                  .find(".topic-readonly .pdf-link")
                  .attr("href", result.new_pdf);
              }

              // Switch back to read-only mode
              topicCard.find(".topic-editing").hide();
              topicCard.find(".topic-readonly").show();
              topicCard.find(".watch-video-btn, .show-pdf-btn").show();

              // Change button text back to "Edit"
              $(this).text("Edit");

              Swal.fire({
                title: "Success",
                text: result.message,
                icon: "success",
              });
            } else {
              Swal.fire({
                title: "Error",
                text: result.message || "Failed to update topic.",
                icon: "error",
              });
            }
          }.bind(this), // Ensure `this` refers to the button
          error: function () {
            Swal.fire({
              title: "Error",
              text: "Could not update the topic.",
              icon: "error",
            });
          },
        });
      }
    });

  $(document)
    .off("click", ".unit-action-btn")
    .on("click", ".unit-action-btn", function () {
      const unitId = $(this).data("unit-id");
      const btn = $(this);
      if (btn.text().trim().startsWith("Edit")) {
        $(`.unit-card[data-unit-id="${unitId}"]`)
          .find(".topic-readonly")
          .hide();
        $(`.unit-card[data-unit-id="${unitId}"]`).find(".topic-editing").show();
        btn.html('<i class="fas fa-save me-2"></i>Save Changes');
      } else {
        saveUnitContent(unitId);
      }
    });
}

function saveAllUnitsContent(courseId, courseName, courseCode) {
  var formData = new FormData();
  // Set the backend action for saving all topics at once.
  formData.append("action", "saveAllLmsTopics");

  let topicsData = [];
  let isValid = true;

  // Loop over each unit card.
  $(".unit-card").each(function (unitIndex, unitElem) {
    const unitId = $(unitElem).data("unit-id");

    // For each topic card within the current unit.
    $(unitElem)
      .find(".topic-card")
      .each(function (topicIndex, topicElem) {
        const topicId = $(topicElem).data("topic-id");
        // Read the video link input.
        const videoLink = $(topicElem)
          .find("input.video-link-input")
          .val()
          .trim();
        // Read the PDF file input.
        let fileInput = $(topicElem).find("input.pdf-input")[0];
        let file =
          fileInput && fileInput.files.length > 0 ? fileInput.files[0] : null;

        // Log the pdf file for debugging.
        console.log("File for topic", topicId, ":", file);

        // Validate the inputs.
        if (!videoLink) {
          Swal.fire({
            title: "Validation Error",
            text: "Please provide a video link for topic id " + topicId,
            icon: "error",
          });
          isValid = false;
          return false; // Break out of the inner loop.
        }
        if (!file) {
          Swal.fire({
            title: "Validation Error",
            text: "Please upload a PDF for topic id " + topicId,
            icon: "error",
          });
          isValid = false;
          return false; // Break out of the inner loop.
        }

        // Append the file to formData.
        formData.append("pdf_files[" + topicId + "]", file);
        // Push the topic data along with its parent unit_id.
        topicsData.push({
          topic_id: topicId,
          video_link: videoLink,
          unit_id: unitId,
          pdf: file.name, // Add file name for debugging purposes.
        });
      });

    // If validation fails in any unit, stop processing further.
    if (!isValid) {
      return false;
    }
  });

  // If validation failed or nothing was collected, do not send the request.
  if (!isValid || topicsData.length === 0) {
    console.log("Validation failed or no topics found.", topicsData);
    return;
  }

  // Log the complete topicsData for debugging.
  console.log("Submitting topics data:", topicsData);
  // Append the aggregated topics data as a JSON string.
  formData.append("topics_data", JSON.stringify(topicsData));

  // Log all the key/value pairs in formData for debugging.
  for (let [key, value] of formData.entries()) {
    console.log(key, value);
  }

  // Send the entire data to the backend via AJAX.
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      let result;
      try {
        result = typeof response === "object" ? response : JSON.parse(response);
      } catch (error) {
        Swal.fire({
          title: "Error",
          text: "Invalid response from server",
          icon: "error",
        });
        return;
      }

      if (result.status === "success") {
        Swal.fire({
          title: "Success",
          text: result.message,
          icon: "success",
        }).then(() => {
          //call the function to get the topics again
          showLmsPlan(courseId, courseName, courseCode);
        });
      } else {
        Swal.fire({
          title: "Error",
          text: result.message || "Failed to update topics",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      Swal.fire({
        title: "Error",
        text: "Failed to update topics.",
        icon: "error",
      });
    },
  });
}

// Toggle between View mode and Edit mode on the topic card.
function editContent() {
  console.log("Editing content");
  const button = $(this);
  const topicCard = button.closest(".topic-card");

  // If the button currently reads "Edit", switch to editing mode.
  console.log(button.text().trim());
  if (button.text().trim() === "Edit") {
    // Hide the read-only view and show the editing fields.
    topicCard.find(".topic-readonly").hide();
    topicCard.find(".topic-editing").show();

    // Pre-fill the video link input with the current value.
    const currentVideoLink = topicCard
      .find(".topic-readonly .video-link")
      .text()
      .trim();
    topicCard.find("input.video-link-input").val(currentVideoLink);

    // Display the current PDF link to let the user know which file is set.
    const currentPdfLink = topicCard
      .find(".topic-readonly .pdf-link")
      .attr("href");
    if (currentPdfLink) {
      topicCard
        .find(".current-pdf-info")
        .html(
          'Current PDF: <a href="' +
            currentPdfLink +
            '" target="_blank">View PDF</a>'
        );
    } else {
      topicCard.find(".current-pdf-info").empty();
    }

    // Change the button text to "Save Changes".
    button.text("Save Changes");
  } else if (button.text().trim() === "Save Changes") {
    // Else, the button text is "Save Changes" so collect and send the updated data.
    const topicId = topicCard.data("topic-id");
    const unitId = topicCard.closest(".unit-card").data("unit-id");
    const updatedVideoLink = topicCard
      .find("input.video-link-input")
      .val()
      .trim();
    const fileInput = topicCard.find("input.pdf-input")[0];
    const updatedFile =
      fileInput && fileInput.files && fileInput.files.length > 0
        ? fileInput.files[0]
        : null;

    // Build the FormData payload.
    const formData = new FormData();
    formData.append("action", "editLmsTopic");
    formData.append("topic_id", topicId);
    formData.append("unit_id", unitId);
    formData.append("video_link", updatedVideoLink);
    // Append the new PDF file only if provided.
    if (updatedFile) {
      formData.append("pdf", updatedFile);
    }

    // Send the AJAX request to update the topic.
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        let result;
        try {
          result =
            typeof response === "object" ? response : JSON.parse(response);
        } catch (e) {
          Swal.fire({
            title: "Error",
            text: "Invalid response from server",
            icon: "error",
          });
          return;
        }
        if (result.status === "success") {
          // Update the read-only view with the new video link.
          topicCard.find(".topic-readonly .video-link").text(updatedVideoLink);
          // If a new PDF was provided (and returned), update the PDF link.
          if (updatedFile && result.new_pdf) {
            topicCard
              .find(".topic-readonly .pdf-link")
              .attr("href", result.new_pdf);
          }
          // Return back to the read-only mode.
          topicCard.find(".topic-editing").hide();
          topicCard.find(".topic-readonly").show();
          // Change the button text back to "Edit".
          button.text("Edit");
          Swal.fire({
            title: "Success",
            text: result.message,
            icon: "success",
          });
        } else {
          Swal.fire({
            title: "Error",
            text: result.message || "Failed to update topic.",
            icon: "error",
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire({
          title: "Error",
          text: "Could not update the topic.",
          icon: "error",
        });
      },
    });
  }
}

// Add these helper functions
function getStatusIcon(status) {
  switch (status.toLowerCase()) {
    case "approved":
      return '<i class="fas fa-check-circle me-1"></i>';
    case "pending":
      return '<i class="fas fa-clock me-1"></i>';
    case "rejected":
      return '<i class="fas fa-times-circle me-1"></i>';
    default:
      return '<i class="fas fa-info-circle me-1"></i>';
  }
}

function getStatusClass(status) {
  switch (status.toLowerCase()) {
    case "approved":
      return "text-success";
    case "pending":
      return "text-warning";
    case "rejected":
      return "text-danger";
    default:
      return "text-secondary";
  }
}

function showAttendancePercentageView() {
  // Hide other views
  document.getElementById("courseInfoContent").classList.add("d-none");
  document.getElementById("courseReportsView").classList.add("d-none");
  document.getElementById("studentListView").classList.add("d-none");
  document.getElementById("attendanceSummaryView").classList.add("d-none");

  // Show attendance percentage view
  document
    .getElementById("attendancePercentageView")
    .classList.remove("d-none");

  // Get the current course ID from the hidden input
  const courseId = currentCourseId;

  // Load attendance percentage data
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAttendancePercentage",
      courseId: courseId,
    },
    success: function (response) {
      let result;
      try {
        result = typeof response === "object" ? response : JSON.parse(response);
      } catch (e) {
        console.error("Failed to parse response:", e);
        return;
      }

      if (result.status === "success") {
        console.log(result.data);
        populateAttendacePercentage(result.data);
      } else {
        console.log(result.message);
        console.error("Error fetching attendance data:", result.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

function viewMarksDetails(courseId) {
  currentCourseId = courseId;
  document.getElementById("courseReportsCourseId").value = courseId;
  document.getElementById("displayCourseId").innerText = courseId;
  document.getElementById("courseInfoContent").classList.add("d-none");
  document.getElementById("marksView").classList.remove("d-none");
  document.getElementById("componentsContainer").classList.remove("d-none");
  document.getElementById("courseReportsView").classList.add("d-none");
  loadExamComponents(currentCourseId);
  // fetchCourseDetails(courseId);
}
const theory = [
  "CIA 1",
  "CIA 2",
  "Model Exam",
  "SSA 1",
  "SSA 2",
  "AL 1",
  "AL 2",
];
const practical = [
  "CIA 1",
  "CIA 2",
  "Model Exam",
  "SSA 1",
  "SSA 2",
  "Laboratory 1",
  "Laboratory 2",
  "Model Lab",
];
const project = [
  "CIA 1",
  "CIA 2",
  "Model Exam",
  "SSA 1",
  "SSA 2",
  "Review 1",
  "Review 2",
  "Review 3",
];
const lab = ["Cycle 1", "Cycle 2"];

// Function to populate the component name dropdown based on course type
function populateComponentNameDropdown(courseType) {
  const componentNameDropdown = $(
    '#componentModal select[name="component_name"]'
  );
  componentNameDropdown.empty();

  let componentNames = [];

  // Select appropriate array based on course type
  switch (courseType.toLowerCase()) {
    case "Theory":
      componentNames = theory;
      break;
    case "Theory_Lab":
      componentNames = practical;
      break;
    case "Theory_Project":
      componentNames = project;
      break;
    case "Lab":
      componentNames = lab;
      break;
    default:
      console.warn("Unknown course type:", courseType);
      componentNames = theory; // Default to theory if type unknown
  }

  // Add options for each component name
  componentNames.forEach((name) => {
    componentNameDropdown.append(`<option value="${name}">${name}</option>`);
  });
}

// Function to fetch course details and populate the component name dropdown
function fetchCourseDetails(courseId) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getCourseDetails",
      courseId: courseId,
    },
    success: function (response) {
      console.log("success da mone", response);

      let result;
      try {
        result = typeof response === "object" ? response : JSON.parse(response);
      } catch (e) {
        console.error("Failed to parse response:", e);
        return;
      }

      if (result.status === "success") {
        const courseType = result.data.course_type;
        populateComponentNameDropdown(courseType);
      } else {
        console.error("Failed to fetch course details:", result.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
    },
  });
}

// Call fetchCourseDetails when the component modal is shown
$("#componentModal").on("show.bs.modal", function () {
  fetchCourseDetails(currentCourseId);
});

// Add form submit handler
$("#componentForm").on("submit", function (e) {
  e.preventDefault();

  const formData = {
    action: "addExamComponent",
    course_id: currentCourseId,
    component_name: $(this).find('[name="component_name"]').val(),
    component_type: $(this).find('[name="component_type"]').val(),
    conducted_marks: $(this).find('[name="conducted_marks"]').val(),
    weightage_marks: 1,
    exam_date: $(this).find('[name="exam_date"]').val(),
  };

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: formData,
    success: function (response) {
      console.log("response da mone", response);
      let result;
      try {
        result = typeof response === "object" ? response : JSON.parse(response);
      } catch (e) {
        console.log("error da mone", e);
        Swal.fire({
          title: "Error",
          text: "Invalid response from server",
          icon: "error",
        });
        return;
      }

      if (result.status === "success") {
        Swal.fire({
          title: "Success",
          text: "Component added successfully",
          icon: "success",
        }).then(() => {
          $("#componentModal").modal("hide");
          $("#componentForm")[0].reset();
          loadExamComponents(currentCourseId);
        });
      } else {
        Swal.fire({
          title: "Error",
          text: result.message || "Failed to add component",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        title: "Error",
        text: "Failed to add component. Please try again.",
        icon: "error",
      });
    },
  });
});

// Add this after your existing code

function loadExamComponents(courseId) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getExamComponents",
      course_id: courseId,
    },
    success: function (response) {
      let result;
      try {
        result = typeof response === "object" ? response : JSON.parse(response);
      } catch (e) {
        console.error("Failed to parse response:", e);
        return;
      }

      if (result.status === "success") {
        toggleComponentButtons();
        renderExamComponents(result.data);
      } else {
        Swal.fire({
          title: "Error",
          text: result.message || "Failed to load components",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      toggleComponentButtons();
      Swal.fire({
        title: "Error",
        text: "Failed to load components. Please try again.",
        icon: "error",
      });
    },
  });
}

function renderExamComponents(components) {
  const container = document.getElementById("componentsContainer");
  const template = document.getElementById("componentCardTemplate");

  container.innerHTML = "";

  components.forEach((component) => {
    const card = template.content.cloneNode(true);

    // Set component name
    card.querySelector(".card-title").textContent = component.component_name;

    // Set status badge
    const statusBadge = card.querySelector(".status-badge");
    statusBadge.textContent = component.status.replace("_", " ").toUpperCase();
    statusBadge.classList.add(`bg-${getStatusBadgeColor(component.status)}`);

    // Set exam date
    card.querySelector(".exam-date").textContent = new Date(
      component.exam_date
    ).toLocaleDateString();

    // Set marks
    card.querySelector(".conducted-marks").textContent =
      component.conducted_marks;

    // Add action buttons based on status
    const actionButtons = card.querySelector(".action-buttons");
    const isEditable =
      component.days_remaining > 0 || component.status === "draft";

    if (component.status === "draft") {
      actionButtons.innerHTML = `
                <button class="btn btn-primary btn-sm create-template-btn" 
                        data-component-id="${component.component_id}">
                    <i class="fas fa-file-alt me-2"></i>Create Template
                </button>
            `;
      actionButtons.innerHTML += `
      <button class="btn btn-sm btn-danger delete-component-btn" onclick="deleteExamComponent(${component.component_id})">
        <i class="fas fa-trash"></i> Delete
      </button>
    `;
      actionButtons
        .querySelector(".create-template-btn")
        .addEventListener("click", () => {
          createTemplate(component.component_id);
        });
      actionButtons
        .querySelector(".delete-component-btn")
        .addEventListener("click", () => {
          deleteExamComponent(component.component_id);
        });
    } else if (component.status === "pending_marks") {
      actionButtons.innerHTML = `
                <button class="btn btn-success btn-sm enter-marks-btn" 
                        data-component-id="${component.component_id}">
                    <i class="fas fa-edit me-2"></i>Enter Marks
                </button>
            `;

      // Add event listener for enter marks button
      actionButtons
        .querySelector(".enter-marks-btn")
        .addEventListener("click", () => {
          enterMarks(component.component_id);
        });
    } else if (component.status === "marks_entered") {
      actionButtons.innerHTML += `
  <button class="btn btn-sm btn-warning me-2" onclick="enterMarks(${component.component_id})">
      <i class="fas fa-edit"></i> Edit Marks
  </button>
  <button class="btn btn-sm btn-success" onclick="approveMarks(${component.component_id})">
      <i class="fas fa-check"></i> Approve
  </button>`;
    } else if (component.status === "approved") {
      actionButtons.innerHTML += `
 <button class="btn btn-sm btn-info me-2" onclick="downloadInternal(${component.component_id})">
    <i class="fas fa-download"></i> Download
</button>

`;
    } else {
      actionButtons.innerHTML += `
      <button class="btn btn-sm btn-danger">
        <i class="fas fa-lock"></i> Locked
      </button>
      `;
    }

    container.appendChild(card);
  });
}

function getStatusBadgeColor(status) {
  const colors = {
    draft: "secondary",
    pending_marks: "warning",
    marks_entered: "info",
    approved: "success",
    locked: "dark",
  };
  return colors[status] || "secondary";
}

// Add these new helper functions
function createTemplate(componentId) {
  console.log("Creating template for component:", componentId);
  // Reset form and update component ID
  resetTemplateForm();
  $("#templateComponentId").val(componentId);

  // Get the conducted marks from the component card
  const conductedMarks = document
    .querySelector(`[data-component-id="${componentId}"]`)
    .closest(".card")
    .querySelector(".conducted-marks").textContent;

  // Set the required marks and show modal
  $("#requiredMarks").text(conductedMarks);
  $("#templateModal").modal("show");
}

function getNextQuestionNumber() {
  const existingNumbers = [];
  $(".question-card").each(function () {
    const questionText = $(this).find(".question-number").text();
    const number = parseInt(questionText.replace("Q", ""));
    existingNumbers.push(number);
  });

  // Find the first missing number starting from 1
  let nextNumber = 1;
  while (existingNumbers.includes(nextNumber)) {
    nextNumber++;
  }
  return nextNumber;
}

function addQuestionToTemplate() {
  const questionNumber = getNextQuestionNumber();
  const questionHtml = `
        <div class="question-card mb-3 bg-white rounded shadow-sm">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 question-number">Q${questionNumber}</h6>
                    <button type="button" class="btn btn-danger remove-question" style="width: 40px; height: 40px; padding: 0;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Marks</label>
                        <input type="number" 
                               class="form-control marks-input" 
                               name="questions[${questionNumber}][marks]" 
                               required 
                               step="0.01"
                               min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CO Number</label>
                        <select class="form-select" name="questions[${questionNumber}][co_number]" required>
                            <option value="">Select CO</option>
                            <option value="1">CO1</option>
                            <option value="2">CO2</option>
                            <option value="3">CO3</option>
                            <option value="4">CO4</option>
                            <option value="5">CO5</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    `;
  $("#questionsContainer").append(questionHtml);
  updateTotalMarks();
}

function updateTotalMarks() {
  let total = 0;
  $(".marks-input").each(function () {
    const marks = parseFloat($(this).val()) || 0;
    total += marks;
  });

  const requiredMarks = parseFloat($("#requiredMarks").text());
  $("#totalMarks").text(total.toFixed(2));

  // Add validation feedback
  const totalMarksElement = $("#totalMarks");
  if (total > requiredMarks) {
    totalMarksElement.addClass("text-danger");
    Swal.fire({
      title: "Warning",
      text: `Total marks (${total.toFixed(
        2
      )}) exceed required marks (${requiredMarks.toFixed(2)})`,
      icon: "warning",
    });
  } else {
    totalMarksElement.removeClass("text-danger");
  }
}

function resetTemplateForm() {
  $("#templateForm")[0].reset();
  $("#questionsContainer").empty();
  $("#totalMarks").text("0");
  $("#totalMarks").removeClass("text-danger");
}

function handleTemplateSubmit(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  const totalMarks = parseFloat($("#totalMarks").text());
  const requiredMarks = parseFloat($("#requiredMarks").text());

  if (totalMarks !== requiredMarks) {
    Swal.fire({
      title: "Error",
      text: `Total marks (${totalMarks}) must equal required marks (${requiredMarks})`,
      icon: "error",
    });
    return;
  }

  // Add action to formData
  formData.append("action", "createTemplate");

  // Submit via AJAX
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      try {
        const result = JSON.parse(response);
        if (result.status === "success") {
          Swal.fire({
            title: "Success",
            text: "Template created successfully",
            icon: "success",
          }).then(() => {
            $("#templateModal").modal("hide");
            // Optionally refresh the component list
            loadExamComponents(currentCourseId); // You'll need to implement this if needed
          });
        } else {
          Swal.fire({
            title: "Error",
            text: result.message || "Failed to create template",
            icon: "error",
          });
        }
      } catch (e) {
        console.error("Parse error:", e);
        Swal.fire({
          title: "Error",
          text: "Failed to process server response",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        title: "Error",
        text: "Failed to submit template: " + error,
        icon: "error",
      });
    },
  });
}

// Add event listeners when document is ready
$(document).ready(function () {
  // Add question button handler
  $("#addQuestionBtn").click(function (e) {
    e.preventDefault();
    addQuestionToTemplate();
  });

  $(document)
    .on("click", "#cqiQuestionsContainer .remove-question", function () {
      $(this).closest(".question-card").remove();
      updateCQITotalMarks(); // Only update CQI total
    })
    .on("input", "#cqiQuestionsContainer .marks-input", function () {
      updateCQITotalMarks(); // Only update CQI total
    });

  // Normal Template specific handlers
  $(document)
    .on("click", "#questionsContainer .remove-question", function () {
      $(this).closest(".question-card").remove();
      updateTotalMarks(); // Only update normal template total
    })
    .on("input", "#questionsContainer .marks-input", function () {
      updateTotalMarks(); // Only update normal template total
    });

  // Template form submission
  $("#templateForm").submit(handleTemplateSubmit);

  // Reset form when template modal is closed
  $("#templateModal").on("hidden.bs.modal", function () {
    resetTemplateForm();
  });
});

function editComponent(componentId) {
  // TODO: Implement edit functionality
  console.log("Edit component:", componentId);
}

function deleteComponent(componentId) {
  Swal.fire({
    title: "Are you sure?",
    text: "This action cannot be undone.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      // TODO: Implement delete functionality
      console.log("Delete component:", componentId);
    }
  });
}

// CQI Template Modal
$(document).ready(function () {
  $("#createCqiTemplateBtn")
    .off("click")
    .on("click", function () {
      // Hide the CQI Test modal
      $("#cqiTestModal").modal("hide");
      // Reset the CQI Template form and questions container
      var form = $("#cqiTemplateForm")[0];
      if (form) {
        form.reset();
      }
      // Explicitly clear the hidden component id field
      $("#cqiTemplateComponentId").val("");
      $("#cqiQuestionsContainer").empty();
      $("#cqiTotalMarks").text("0.00");
      $("#cqiRequiredMarks").text("100.00");
      // Show the CQI Template Modal
      $("#cqiTemplateModal").modal("show");
    });
});

// **************** CQI Template Modal UI Functions ****************

// Counter for CQI Template questions
var cqiQuestionCounter = 0;

// Function to add a new question to the CQI Template
function addQuestionToCQITemplate() {
  cqiQuestionCounter = getNextQuestionNumber();
  var questionHtml = `
        <div class="question-card mb-3 bg-white rounded shadow-sm">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 question-number">Q${cqiQuestionCounter}</h6>
                    <button type="button" class="btn btn-danger remove-question" style="width: 40px; height: 40px; padding: 0;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Marks</label>
                        <input type="number" 
                               class="form-control marks-input" 
                               name="questions[${cqiQuestionCounter}][marks]" 
                               required 
                               step="0.01"
                               min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CO Number</label>
                        <select class="form-select" name="questions[${cqiQuestionCounter}][co_number]" required>
                            <option value="">Select CO</option>
                            <option value="1">CO1</option>
                            <option value="2">CO2</option>
                            <option value="3">CO3</option>
                            <option value="4">CO4</option>
                            <option value="5">CO5</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    `;
  $("#cqiQuestionsContainer").append(questionHtml);
  updateCQITotalMarks();
}

// Function to update the total marks in the CQI Template modal
function updateCQITotalMarks() {
  let total = 0;
  $(".marks-input").each(function () {
    const marks = parseFloat($(this).val()) || 0;
    total += marks;
  });
  const requiredMarks = 100;
  $("#cqiTotalMarks").text(total.toFixed(2));

  // Add validation feedback
  const totalMarksElement = $("#totalMarks");
  if (total > requiredMarks) {
    totalMarksElement.addClass("text-danger");
    Swal.fire({
      title: "Warning",
      text: `Total marks (${total.toFixed(
        2
      )}) exceed required marks (${requiredMarks.toFixed(2)})`,
      icon: "warning",
    });
  } else {
    totalMarksElement.removeClass("text-danger");
  }
}

// Function to reset the CQI Template form
function resetCqiTemplateForm() {
  cqiQuestionCounter = 0;
  var form = $("#cqiTemplateForm")[0];
  if (form) {
    form.reset();
  }
  // Explicitly clear the hidden component id field
  $("#cqiTemplateComponentId").val("");
  $("#cqiQuestionsContainer").empty();
  $("#cqiTotalMarks").text("0.00");
  $("#cqiRequiredMarks").text("100.00");
}

// Replace the existing handleTemplateSubmitCqi function
function handleTemplateSubmitCqi(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  const totalMarks = parseFloat($("#cqiTotalMarks").text());
  const requiredMarks = 100; // CQI test is always 100 marks

  // Validate total marks
  if (totalMarks !== requiredMarks) {
    Swal.fire({
      title: "Error",
      text: `Total marks (${totalMarks}) must equal required marks (${requiredMarks})`,
      icon: "error",
    });
    return;
  }

  // Add CQI specific data
  formData.append("action", "createCqiTemplate");
  formData.append("course_id", currentCourseId);
  formData.append("component_name", "CQI Test");
  formData.append("conducted_marks", "100");
  formData.append("weightage_marks", "1");
  formData.append("exam_date", new Date().toISOString().split("T")[0]); // Current date in YYYY-MM-DD format

  // Submit via AJAX
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      try {
        const result = JSON.parse(response);
        if (result.status === "success") {
          Swal.fire({
            title: "Success",
            text: "CQI Template created successfully",
            icon: "success",
          }).then(() => {
            $("#cqiTemplateModal").modal("hide");
            // Refresh the component list
            loadExamComponents(currentCourseId);
          });
        } else {
          Swal.fire({
            title: "Error",
            text: result.message || "Failed to create CQI template",
            icon: "error",
          });
        }
      } catch (e) {
        console.error("Parse error:", e);
        Swal.fire({
          title: "Error",
          text: "Failed to process server response",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        title: "Error",
        text: "Failed to submit CQI template: " + error,
        icon: "error",
      });
    },
  });
}

// Attach event listeners for the CQI Template Modal
$(document).ready(function () {
  // When the CQI Add Question button is clicked
  $("#cqiAddQuestionBtn").click(function (e) {
    e.preventDefault();
    addQuestionToCQITemplate();
  });

  // Delegate removal of a question card within the CQI Questions Container
  $(document).on(
    "click",
    "#cqiQuestionsContainer .remove-question",
    function () {
      $(this).closest(".question-card").remove();
      updateCqiTotalMarks();
    }
  );

  // Update total marks on input in the CQI Template
  $(document).on("input", "#cqiQuestionsContainer .marks-input", function () {
    updateCqiTotalMarks();
  });

  // Handle CQI Template form submission
  $("#cqiTemplateForm").submit(handleTemplateSubmitCqi);

  // Reset the CQI Template form when modal is hidden
  $("#cqiTemplateModal").on("hidden.bs.modal", function () {
    resetCqiTemplateForm();
  });
});

function renderRegularMarkEntryTable(template, students) {
  // Clear existing headers and keep only the first row
  let headerRow = $("#marksTable thead tr:first-child");
  headerRow.empty();

  // Add fixed columns
  headerRow.append(`
      <th style="min-width: 120px;">Register No</th>
      <th style="min-width: 200px;">Student Name</th>
  `);

  // Add question columns
  template.forEach(function (question) {
    headerRow.append(`
          <th style="min-width: 100px;">
              Q${question.question_number} (${question.marks})<br>CO${question.co_number}
          </th>
      `);
  });

  // Add total and status columns at the end
  headerRow.append(`
      <th style="min-width: 80px;">Total</th>
      <th style="min-width: 100px;">Status</th>
  `);

  // Clear and populate tbody
  const tbody = $("#marksTable tbody");
  tbody.empty();

  students.forEach(function (student) {
    // Use student.student_id instead of student.uid
    let row = `
      <tr data-student-id="${student.rollNo}">
          <td>${student.rollNo}</td>
          <td>${student.name}</td>`;

    // Add input cells for each question
    template.forEach(function (question) {
      row += `
          <td>
              <input type="number"
                  class="form-control form-control-sm mark-input"
                  data-max="${question.marks}"
                  data-question="${question.question_number}">
          </td>`;
    });

    // Add total and status cells
    row += `
      <td class="total">0</td>
      <td>
          <select class="form-select form-select-sm">
              <option value="present">Present</option>
              <option value="absent">Absent</option>
          </select>
      </td>
  </tr>`;

    tbody.append(row);
  });
}

function loadExistingMarks(componentId) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: { action: "get_existing_marks", component_id: componentId },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        populateExistingMarks(response.data);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error fetching existing marks:", error);
    },
  });
}

// Helper function to fetch roll numbers from the server
function fetchRollNumbers(uids, callback) {
  $.ajax({
    url: "backend.php", // The server endpoint
    method: "POST",
    data: { uids: JSON.stringify(uids), action: "getRollNumbers" },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        callback(response.data); // response.data is the UID->roll mapping object
      } else {
        console.error("Error fetching roll numbers:", response.error);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
    },
  });
}

// Main function to populate marks once roll numbers are available
function populateExistingMarks(marksData) {
  // Get unique UIDs from the marksData
  let uids = marksData.map((mark) => mark.student_id);
  uids = Array.from(new Set(uids)); // Remove duplicates

  // Fetch the mapping from UID to roll number from the database
  fetchRollNumbers(uids, function (uidToRollMapping) {
    marksData.forEach(function (mark) {
      // Get the roll number using the UID from marksData
      const rollNo = uidToRollMapping[mark.student_id];
      if (!rollNo) {
        console.log("No roll number found for UID:", mark.student_id);
        return; // Skip this mark if no roll number is found
      }

      // Find the table row based on the roll number stored in the data attribute.
      // (Assume that your table rows are rendered like <tr data-student-id="ROLL_NO">)
      var $row = $("#marksTable tbody tr").filter(function () {
        return String($(this).data("student-id")) === String(rollNo);
      });

      if ($row.length) {
        mark.marks.forEach(function (questionMark) {
          // Find the input element for the question number.
          var $input = $row.find(".mark-input").filter(function () {
            return (
              String($(this).data("question")) ===
              String(questionMark.question_number)
            );
          });

          if ($input.length) {
            $input.val(questionMark.marks_obtained);
          }
        });

        // Set the attendance status (assumes select options are 'absent' and 'present')
        $row.find("select").val(mark.is_absent ? "absent" : "present");

        // Recalculate the row total (assuming calculateRowTotal is defined)
        calculateRowTotal($row);
      } else {
        console.log("No row found for roll number:", rollNo);
      }
    });
  });
}

function calculateRowTotal(row) {
  if (!$("#autoCalculate").is(":checked")) return;

  let total = 0;
  let marks = [];
  let processedQuestions = new Set(); // Track which questions we've processed

  row.find(".mark-input").each(function () {
    const questionNum = $(this).data("question");

    // Log duplicate check

    // Only process each question once
    if (!$(this).prop("disabled") && !processedQuestions.has(questionNum)) {
      const value = parseFloat($(this).val()) || 0;
      marks.push(value);
      total += value;
      processedQuestions.add(questionNum);
    }
  });

  // console.log('Processed questions:', [...processedQuestions]);

  row.find(".total").text(total.toFixed(2));
}
function validateMarkInput(input) {
  const maxMarks = parseFloat(input.data("max"));
  let value = parseFloat(input.val());

  if (value > maxMarks) {
    input.addClass("is-invalid");
    showNotification("Error", `Maximum marks allowed is ${maxMarks}`, "danger");
    input.val(maxMarks);
    value = maxMarks;
  } else {
    input.removeClass("is-invalid");
  }

  calculateRowTotal(input.closest("tr"));
}
function validateMarkInputDebounced(input) {
  clearTimeout(markInputTimeout);
  markInputTimeout = setTimeout(() => {
    validateMarkInput(input);
  }, 300);
}
function showNotification(title, message, type) {
  const toast = `
<div class="toast align-items-center text-white bg-${type} border-0" role="alert">
    <div class="d-flex">
        <div class="toast-body">
            <strong>${title}:</strong> ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
</div>
`;
  $(".toast-container").append(toast);
  const toastEl = $(".toast").last();
  const bsToast = new bootstrap.Toast(toastEl);
  bsToast.show();
  setTimeout(() => toastEl.remove(), 5000);
}
function handleAttendanceChange(select) {
  const row = select.closest("tr");
  if (select.val() === "absent") {
    row.find(".mark-input").val(0).prop("disabled", true);
  } else {
    row.find(".mark-input").prop("disabled", false);
  }
  calculateRowTotal(row);
}

function saveMarks() {
  const marksData = collectMarksData();

  if (validateMarksData(marksData)) {
    console.log(
      "c......................................................",
      marksData
    );
    submitMarks(marksData);
  }
}
function collectMarksData() {
  const componentId = $("#markEntryModal").data("componentId");
  const academicYear = $("#academicYear").val();
  const isCQI = $("#markEntryModal").data("isCQI") ? 1 : 0; // Ensure it's 1 or 0

  console.log("Is CQI:", isCQI); // Debug log

  const marksData = [];

  $("#marksTable tbody tr").each(function () {
    const studentId = $(this).data("student-id");
    const isAbsent = $(this).find("select").val() === "absent";

    $(this)
      .find(".mark-input")
      .each(function () {
        const input = $(this);
        const isDisabled = input.prop("disabled");

        if (isCQI && isDisabled) {
          return;
        }

        const markData = {
          student_id: studentId,
          component_id: parseInt(componentId),
          question_number: parseInt(input.data("question")),
          marks: parseFloat(input.val()) || 0,
          is_absent: isAbsent,
          academic_year: parseInt(academicYear),
          is_cqi: isCQI, // Use the numeric value
          disabled: isDisabled,
        };

        console.log("Mark data with CQI:", markData); // Debug log for each mark
        marksData.push(markData);
      });
  });

  return marksData;
}

function submitMarks(marksData) {
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );
  console.log("marksData:", marksData);
  console.log("selectedFacultyData", selectedFacultyData);

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "save_marks",
      marks: JSON.stringify(marksData),
      academicYear: selectedFacultyData.academicYear,
    },
    dataType: "json",
    success: function (response) {
      console.log("Server response:", response);

      if (response.status === "success") {
        $("#markEntryModal").modal("hide");
        loadExamComponents(currentCourseId);
        showNotification(
          "Success",
          response.message || "Marks saved successfully",
          "success"
        );
      } else {
        let errorMessage = response.message || "Failed to save marks";
        if (response.errors && response.errors.length > 0) {
          errorMessage += "\n" + response.errors.join("\n");
        }
        showNotification("Error", errorMessage, "danger");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error details:", {
        status: status,
        error: error,
        response: xhr.responseText,
      });

      showNotification("Error", "Failed to save marks", "danger");
    },
  });
}
function validateMarksData(marksData) {
  console.log("hii", marksData);
  if (!marksData.length) {
    showNotification("Error", "No marks data to save", "danger");
    return false;
  }

  let hasErrors = false;
  const isCQI = $("#markEntryModal").data("isCQI");

  marksData.forEach(function (mark) {
    const input = $(`.mark-input[data-question="${mark.question_number}"]`);
    const maxMarks = parseFloat(input.data("max"));

    // Skip validation for disabled inputs in CQI mode
    if (isCQI && input.prop("disabled")) {
      return;
    }

    if (!mark.is_absent && mark.marks > maxMarks) {
      hasErrors = true;
      input.addClass("is-invalid");
      return false;
    }
  });

  if (hasErrors) {
    showNotification(
      "Error",
      "Please correct the marks before saving",
      "danger"
    );
    return false;
  }
  return true;
}

function enterMarks(componentId) {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "get_component_details",
      componentId: componentId,
      action: "get_component_details",
      componentId: componentId,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        if (data.data.is_cqi == 1) {
          console.log("In CQI");
          showCQIMarkEntry(componentId);
        } else {
          console.log("In Regular");
          showRegularMarkEntry(componentId);
        }
      } else {
        Swal.fire({
          title: "Error!",
          text: data.message || "Failed to retrieve component details",
          icon: "error",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
      Swal.fire({
        title: "Error!",
        text: "Failed to retrieve component details",
        icon: "error",
      });
    },
  });
}

function showRegularMarkEntry(componentId) {
  // Reset mark entry modal
  $(
    "#marksTable thead tr:first-child th:not(:first-child):not(:last-child):not(:nth-last-child(2))"
  ).remove();
  $("#marksTable tbody").empty();

  // Get component details including academic year
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: { action: "get_component_details", component_id: componentId },
    dataType: "json",
    success: function (componentResponse) {
      if (componentResponse.status === "success") {
        const componentDetails = componentResponse.data;
        const courseId = currentCourseId;
        const selectedFacultyData = JSON.parse(
          sessionStorage.getItem("selectedFacultyData")
        );

        $.ajax({
          url: "backend.php",
          type: "POST",
          data: { action: "get_template", component_id: componentId },
          dataType: "json",
          success: function (templateResponse) {
            if (templateResponse.status === "success") {
              const template = templateResponse.data;

              $.ajax({
                url: "backend.php",
                type: "POST",
                data: {
                  action: "getCourseStudent",
                  courseId: courseId,
                  facultyId: selectedFacultyData.facultyId,
                },
                dataType: "json",
                success: function (studentsResponse) {
                  if (studentsResponse.status === "success") {
                    const students = studentsResponse.data;
                    const academic_year = selectedFacultyData.academicYear;

                    // Store component details in modal data
                    $("#markEntryModal").data("componentId", componentId);
                    $("#markEntryModal").data("academicYear", academic_year);

                    renderRegularMarkEntryTable(template, students);
                    $("#markEntryModal").modal("show");
                    $("#markEntryModal").data("isCQI", false);

                    // Load existing marks if any
                    loadExistingMarks(componentId);
                  }
                },
              });
            }
          },
        });
      }
    },
  });
}

function downloadMarksTemplate() {
  const componentId = $("#markEntryModal").data("componentId");

  // Assume isCQI is stored as a data attribute on the modal or elsewhere.
  const isCQI = $("#markEntryModal").data("isCQI");
  console.log(isCQI);
  console.log(componentId);
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );

  // Choose the proper action based on whether the component is CQI
  const studentAction = isCQI ? "get_cqi_students" : "getCourseStudent";
  let studentData; // Declare studentData outside the if/else

  if (isCQI) {
    studentData = {
      action: studentAction,
      subject_id: currentCourseId, // Using subject_id for CQI as per your logic
      facultyId: selectedFacultyData.facultyId,
      component_id: componentId,
    };
  } else {
    studentData = {
      action: studentAction,
      courseId: currentCourseId, // Using courseId for non-CQI
      facultyId: selectedFacultyData.facultyId,
    };
  }

  // First get the student list
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: studentData,
    dataType: "json",
    success: function (studentResponse) {
      if (studentResponse.status === "success") {
        console.log("Student data:", studentResponse.data);
        const students = studentResponse.data; // Array of student objects

        // Now get the question template (question list)
        $.ajax({
          type: "POST",
          url: "backend.php",
          data: {
            action: "get_excel_template",
            component_id: componentId, // note: ensure your backend expects this key as component_id (case sensitive)
          },
          dataType: "json",
          success: function (templateResponse) {
            if (templateResponse.status === "success") {
              console.log("Template data:", templateResponse.data);
              const questions = templateResponse.data; // Array of question objects

              // Build the header row
              // Example header: ["Register No", "Student Name", "Q1", "Q2", ..., "Status"]
              const headers = ["Register No", "Student Name"];
              questions.forEach((q) => {
                headers.push(
                  `Q${q.question_number} (${parseFloat(q.marks).toFixed(2)})`
                );
              });
              headers.push("Status");

              // Build rows based on the students data returned
              const dataRows = students.map((student) => {
                // Customize based on your student object (rollNo, name, etc.)
                let row = [
                  student.rollNo ? student.rollNo : student.sid,
                  student.name,
                ];

                // For each question, add an empty cell (or prepopulate if needed)
                questions.forEach(() => {
                  row.push("");
                });
                // Add a status cell, for example "Pending"

                return row;
              });

              // Combine headers and rows into one 2D array
              const excelData = [headers, ...dataRows];

              // Generate the Excel file using SheetJS
              const wb = XLSX.utils.book_new();
              const ws = XLSX.utils.aoa_to_sheet(excelData);
              XLSX.utils.book_append_sheet(wb, ws, "Marks Template");

              // Download the file
              XLSX.writeFile(wb, "Marks_Template.xlsx");
            } else {
              console.error(
                "Error fetching template:",
                templateResponse.message
              );
            }
          },
          error: function (xhr, status, error) {
            console.error("Error fetching template:", error);
          },
        });
      } else {
        console.error("Error fetching students:", studentResponse.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error fetching students:", error);
    },
  });
}

function handleFileUpload(e) {
  const file = e.target.files[0];
  if (!validateFile(file)) return;

  showProcessingModal();
  readFile(file);
}
function showProcessingModal() {
  $("#excelProcessingModal").modal("show");
}
function hideProcessingModal() {
  $("#excelProcessingModal").modal("hide");
}
function readFile(file) {
  const reader = new FileReader();
  reader.onload = function (e) {
    try {
      processExcelData(e.target.result);
    } catch (error) {
      hideProcessingModal();
      showNotification(
        "Error",
        "Failed to process file: " + error.message,
        "danger"
      );
    }
  };

  reader.readAsBinaryString(file);
}
function processExcelData(data) {
  const workbook = XLSX.read(data, { type: "binary" });
  const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
  const jsonData = XLSX.utils.sheet_to_json(firstSheet);

  const validationErrors = validateExcelData(jsonData);
  if (validationErrors.length > 0) {
    hideProcessingModal();
    showValidationErrors(validationErrors);
    return;
  }

  populateMarksTable(jsonData);
  hideProcessingModal();
}
function showValidationErrors(errors) {
  const errorList = $("#validationErrors");
  errorList.empty();
  errors.forEach((error) => {
    errorList.append(`<li>${error}</li>`);
  });
  $("#validationModal").modal("show");
}

function populateMarksTable(data) {
  data.forEach((row) => {
    // Standardize the register number format from data
    const registerNo = row["Register No"]?.trim().toUpperCase();
    let matchedRow = null;

    // Loop over each table row and check if its data-student-id matches
    $("#marksTable tbody tr").each(function () {
      const currentStudentId = $(this)
        .attr("data-student-id")
        ?.trim()
        .toUpperCase();
      if (currentStudentId === registerNo) {
        matchedRow = $(this);
        return false; // break out of the each() loop
      }
    });

    if (matchedRow) {
      populateMarks(matchedRow, row);
      setAttendance(matchedRow, row);
      calculateRowTotal(matchedRow);
    } else {
      console.warn(`No matching row found for Register No: ${registerNo}`);
    }
  });
  $("#excelProcessingModal").modal("hide");
}

function getMarkFromRow(rowData, qNum) {
  const prefix = `Q${qNum}`;
  // Find a key in rowData that starts with the expected prefix.
  for (const key in rowData) {
    if (key.trim().startsWith(prefix)) {
      return rowData[key];
    }
  }
  return 0;
}

function populateMarks(tableRow, rowData) {
  console.log("Table row:", tableRow);
  console.log("Row data:", rowData);
  
  tableRow.find(".mark-input").each(function () {
    const qNum = $(this).data("question");
    const mark = getMarkFromRow(rowData, qNum);
    $(this).val(mark);
  });
}

function setAttendance(tableRow, rowData) {
  if (rowData["Status"]?.toLowerCase() === "absent") {
    tableRow.find("select").val("absent").trigger("change");
  }
}

function validateFile(file) {
  if (!file) return false;

  const allowedTypes = [
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "text/csv",
  ];

  if (!allowedTypes.includes(file.type)) {
    showNotification("Error", "Please upload Excel or CSV file only", "danger");
    return false;
  }
  return true;
}
function validateExcelData(data) {
  const errors = [];
  validateRequiredColumns(data, errors);
  validateRows(data, errors);
  return errors;
}
function validateRequiredColumns(data, errors) {
  const requiredColumns = ["Register No", "Student Name"];
  requiredColumns.forEach((column) => {
    if (!data[0]?.hasOwnProperty(column)) {
      errors.push(`Missing required column: ${column}`);
    }
  });
}
function validateRows(data, errors) {
  const questionColumns = getQuestionColumns();

  data.forEach((row, index) => {
    const rowNum = index + 2; // Excel row number (accounting for header)
    validateQuestionMarks(row, rowNum, questionColumns, errors);
    validateRegisterNumber(row, rowNum, errors);
  });
}
function getQuestionColumns() {
  const columns = [];
  $("#marksTable th").each(function () {
    // Trim to remove any leading/trailing whitespace or newlines
    const text = $(this).text().trim();
    // Check if the text starts with 'Q'
    if (text.startsWith("Q")) {
      // If your header is like "Q1 (15.00)CO1", splitting by space gives ["Q1", "(15.00)CO1"]
      // You might then want just the first part, "Q1"
      const questionColumn = text.split(" ")[0];
      columns.push(questionColumn);
    }
  });
  return columns;
}

function validateQuestionMarks(row, rowNum, questionColumns, errors) {
  questionColumns.forEach((qCol) => {
    const maxMark = parseFloat(
      $(`#marksTable th:contains('${qCol}')`)
        .text()
        .match(/\((\d+\.?\d*)\)/)[1]
    );
    if (row[qCol] > maxMark) {
      errors.push(
        `Row ${rowNum}: ${qCol} has marks ${row[qCol]} which exceeds maximum ${maxMark}`
      );
    }
  });
}
function validateRegisterNumber(row, rowNum, errors) {
  const registerNo = row["Register No"]?.trim(); // Ensure it's a string and remove extra spaces

  if (!registerNo || !/^\d{6}[A-Z]{3}\d{3}$/.test(registerNo)) {
    errors.push(`Row ${rowNum}: Invalid Register Number format`);
  }
}
function approveMarks(componentId) {
  if (
    confirm(
      "Are you sure you want to approve these marks? This action cannot be undone without admin approval."
    )
  ) {
    $.ajax({
      url: "backend.php", // Change this URL to your actual API endpoint
      method: "POST",
      data: {
        action: "approve_marks",
        component_id: componentId,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          showNotification("Success", "Marks approved successfully", "success");
          loadExamComponents(currentCourseId); // Refresh the component list
        } else {
          showNotification(
            "Error",
            response.message || "Failed to approve marks",
            "danger"
          );
        }
      },
      error: function (xhr, status, error) {
        showNotification(
          "Error",
          "Failed to approve marks: " + error,
          "danger"
        );
      },
    });
  }
}

// Updated checkCqiStatus returns a boolean true/false
async function checkCqiStatus(courseId) {
  try {
    const response = await $.ajax({
      url: "backend.php",
      type: "POST",
      dataType: "json",
      data: {
        action: "checkCqiStatus",
        course_id: courseId,
      },
    });
    return !!response.hasCqi;
  } catch (error) {
    console.error("CQI check failed:", error);
    return false; // Fallback: Show the buttons if check fails.
  }
}

// Updated toggleComponentButtons function using the boolean result from checkCqiStatus
async function toggleComponentButtons() {
  try {
    const response = await $.ajax({
      url: "backend.php",
      type: "POST",
      dataType: "json",
      data: {
        action: "checkCqiStatus",
        course_id: currentCourseId,
      },
    });

    if (response.status === "success") {
      if (!response.hasCqi) {
        // No CQI exists - show both buttons
        $("#createCqiBtn").show();
        $("#addComponentBtn").show();
      } else {
        // CQI exists but marks not entered - hide both buttons
        $("#createCqiBtn").hide();
        $("#addComponentBtn").hide();
      }

      if (!response.hasCQIMarksEntered) {
        $("#viewCoPoAttainmentBtn").hide();
      } else {
        $("#viewCoPoAttainmentBtn").show();
      }

      if (!response.hasRegularMarksEntered) {
        $("#viewInternalMarksBtn").hide();
        $("#viewCoAttainmentBtn").hide();
      } else {
        $("#viewInternalMarksBtn").show();
        $("#viewCoAttainmentBtn").show();
      }
    }
  } catch (error) {
    console.error("Error checking CQI status:", error);
    // Default to showing buttons if check fails
    $("#createCqiBtn").show();
    $("#addComponentBtn").show();
  }
}
var selectedTimetableCourseData = null;
function fetchCourseDetailsforTimetable(courseId) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "backend.php",
      method: "POST",
      data: { action: "fetchCourseDetails", course_id: courseId },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          selectedTimetableCourseData = response.data;
          resolve(response.data);
        } else {
          console.error("Error:", response.message);
          reject(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        reject(error);
      },
    });
  });
}

// Add this function to handle the View CO Attainment Report button click
$(document).ready(function () {
  $("#viewCoAttainmentBtn").on("click", async function () {
    try {
      // Show the modal
      $("#coAttainmentModal").modal("show");

      // Wait for the data to be fetched
      const courseData = await fetchCourseDetailsforTimetable(currentCourseId);
      // Now we can safely use the data
      $("#deptName").text(courseData.department);
      $("#yearSem").text(
        courseData.academic_year + " & " + courseData.semester
      );
      $("#subjectCode").text(
        courseData.course_code + " - " + courseData.course_name
      );

      // Rest of your AJAX call...
      $.ajax({
        url: "backend.php",
        type: "POST",
        data: {
          action: "getComponentCOData",
          courseId: currentCourseId,
        },
        success: function (response) {
          let result;
          try {
            // Parse the response if it's a string
            result =
              typeof response === "string" ? JSON.parse(response) : response;
            if (result.status === "success") {
              renderMarksTableHeader(result);
              processAttainmentData(result);
            } else {
              console.error("Failed to load component data:", result.message);
            }
          } catch (e) {
            console.error("Failed to parse response:", e);
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
        },
      });
    } catch (error) {
      console.error("Error fetching course details:", error);
      showNotification("Error", "Failed to load course details", "danger");
    }
  });
});

// Add these styles to match the original modal
const styles = `
    .attainment-level-0 {
        background-color: #f8d7da;
    }

    .attainment-level-1 {
        background-color: #fff3cd;
    }

    .attainment-level-2 {
        background-color: #d1e7dd;
    }

    .attainment-level-3 {
        background-color: #198754;
        color: white;
    }

    #marksTable th,
    #attainmentTable th {
        text-align: center;
        vertical-align: middle;
        background-color: #f8f9fa;
    }

    #marksTable td,
    #attainmentTable td {
        text-align: center;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.5rem;
    }

    .modal-fullscreen {
        padding: 1rem;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }
`;

// Add styles to the document
$(document).ready(function () {
  const styleElement = document.createElement("style");
  styleElement.textContent = styles;
  document.head.appendChild(styleElement);
});

function renderMarksTableHeader(data) {
  if (!data || !data.components || !data.totalCOMarks || !data.students) {
    console.error("Invalid data structure received");
    return;
  }

  const { components, totalCOMarks, students } = data;
  const thead = $("#COMarkID thead");
  const tbody = $("#COMarkID tbody");

  // Clear existing content
  thead.empty();
  tbody.empty();

  // Create header rows
  const componentRow = $("<tr>");
  componentRow.append(
    '<th rowspan="3">S.No</th><th rowspan="3">Register Number</th><th rowspan="3">Name</th>'
  );

  // Add component headers
  Object.keys(components).forEach((compName) => {
    const coCount = Object.keys(components[compName]).length;
    componentRow.append(`<th colspan="${coCount}">${compName}</th>`);
  });

  // Add Total Marks in COs header
  const totalCOCount = Object.keys(totalCOMarks).length;
  componentRow.append(`<th colspan="${totalCOCount}">Total Marks in COs</th>`);

  // Create CO row
  const coRow = $("<tr>");
  Object.keys(components).forEach((compName) => {
    Object.keys(components[compName])
      .sort()
      .forEach((coNum) => {
        coRow.append(`<th class="co-mark">${coNum}</th>`);
      });
  });

  // Add total CO headers
  Object.keys(totalCOMarks)
    .sort()
    .forEach((coNum) => {
      coRow.append(`<th class="co-total">${coNum}</th>`);
    });

  // Create max marks row
  const maxMarksRow = $("<tr>");
  Object.keys(components).forEach((compName) => {
    Object.keys(components[compName])
      .sort()
      .forEach((coNum) => {
        maxMarksRow.append(
          `<th class="co-mark">${components[compName][coNum]}</th>`
        );
      });
  });

  // Add total max marks
  Object.keys(totalCOMarks)
    .sort()
    .forEach((coNum) => {
      maxMarksRow.append(`<th class="co-total">${totalCOMarks[coNum]}</th>`);
    });

  // Append header rows
  thead.append(componentRow);
  thead.append(coRow);
  thead.append(maxMarksRow);

  // Render student rows
  students.forEach((student, index) => {
    const row = $("<tr>");
    row.append(`<td>${index + 1}</td>`);
    row.append(`<td>${student.register_number}</td>`);
    row.append(`<td>${student.name}</td>`);

    // Add marks for each component and CO
    Object.keys(components).forEach((compName) => {
      Object.keys(components[compName])
        .sort()
        .forEach((coNum) => {
          const markData = student.marks[compName]?.[coNum] || {
            marks: "0.00",
            is_absent: false,
          };
          const cellContent = markData.is_absent
            ? '<span class="badge bg-warning">AB</span>'
            : markData.marks;
          row.append(`<td class="co-mark">${cellContent}</td>`);
        });
    });

    // Add total CO marks
    Object.keys(totalCOMarks)
      .sort()
      .forEach((coNum) => {
        const coTotal = calculateStudentCOTotal(student.marks, coNum);
        const maxMarks = totalCOMarks[coNum];
        const percentage = ((coTotal.marks / maxMarks) * 100).toFixed(2);

        const cellContent = coTotal.is_absent
          ? '<span class="badge bg-warning">AB</span>'
          : `${coTotal.marks}/${maxMarks}<br><small class="text-muted">${percentage}%</small>`;

        row.append(`<td class="co-total">${cellContent}</td>`);
      });

    tbody.append(row);
  });
}

function calculateStudentCOTotal(studentData, coNum) {
  let totalMarks = 0;

  Object.keys(studentData || {}).forEach((compName) => {
    if (studentData[compName]?.[coNum]) {
      // If absent for this component, just skip adding its marks (effectively counting as 0)
      // but continue processing other components
      if (!studentData[compName][coNum].is_absent) {
        totalMarks += parseFloat(studentData[compName][coNum].marks) || 0;
      }
    }
  });

  return {
    marks: totalMarks,
    is_absent: false, // Remove the absent flag since we're handling absences per component
  };
}

function addTableStyles() {
  const styles = `
        <style>
            #COMarkID th, #COMarkID td {
                text-align: center;
                vertical-align: middle;
                padding: 8px;
                border: 1px solid #dee2e6;
            }
            #COMarkID .co-mark {
                background-color: #f8f9fa;
            }
            #COMarkID .co-total {
                background-color: #e9ecef;
                font-weight: bold;
            }
            #COMarkID thead th {
                background-color: #dee2e6;
                position: sticky;
                top: 0;
                z-index: 1;
            }
            #COMarkID thead tr:first-child th {
                z-index: 2;
            }
            .badge.bg-warning {
                background-color: #ffc107;
                color: #000;
            }
            #COMarkID .text-muted {
                color: #6c757d;
            }
        </style>
    `;

  if (!$("#COMarkIDStyles").length) {
    $("head").append(`<div id="COMarkIDStyles">${styles}</div>`);
  }
}

// Add this function to handle the View CQI Analysis button click
$(document).ready(function () {
  $("#createCqiBtn").on("click", function () {
    // Show the modal
    $("#cqiTestModal").modal("show");

    showCQIAnalysis();
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "get_cqi_attainment",
        courseId: currentCourseId,
      },
      success: function (response) {
        console.log("Response:", response);
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
      },
    });
  });
});

// Add this to your existing JavaScript
function showCQIAnalysis() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getCQIAnalysis",
      courseId: currentCourseId,
    },
    success: function (response) {
      try {
        // Parse response if it's a string
        const data =
          typeof response === "string" ? JSON.parse(response) : response;

        if (data.status === "success") {
          populateCQIAnalysis(data.data);
          $("#cqiTestModal").modal("show");
        } else {
          showNotification("Error", "Failed to load CQI analysis", "danger");
        }
      } catch (error) {
        console.error("Error parsing response:", error);
        showNotification("Error", "Invalid response format", "danger");
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX error:", error);
      showNotification("Error", "Failed to load CQI analysis", "danger");
    },
  });
}

// Reuse existing populateCQIAnalysis function
function populateCQIAnalysis(data) {
  // Update summary stats with proper formatting
  $("#totalStudentsCount").text(data.total_students);
  $("#belowTargetCount").text(data.below_target_count);
  $("#mostAffectedCO").text("CO" + data.most_affected_co);
  $("#targetAchievement").text(data.target_achievement.toFixed(2) + "%");

  // Populate students table
  const tbody = $("#cqiStudentsTable tbody").empty();

  data.students.forEach(function (student) {
    const row = `
            <tr>
                <td>${student.sid}</td>
                <td>${student.name}</td>
                <td>${formatUnattainedCOs(student.unattained_cos)}</td>

                <td>${formatDetailedScores(student.current_scores)}</td>

                <td>${formatDetailedScores(student.required_scores)}</td>
                <td>${formatDetailedScores(student.gaps)}</td>
                <td>${student.recommendation}</td>
            </tr>
        `;
    tbody.append(row);
  });
}

// Helper functions remain the same
function formatUnattainedCOs(cos) {
  if (!Array.isArray(cos) || cos.length === 0) return "-";
  return cos.map((co) => `CO${co}`).join(", ");
}

function formatDetailedScores(scores, totalMarks = null) {
  if (!scores || Object.keys(scores).length === 0) return "-";

  return Object.entries(scores)
    .map(([co, score]) => {
      let displayScore = parseFloat(score).toFixed(2);
      if (totalMarks && totalMarks[co]) {
        // If we have total marks, show as fraction
        return `CO${co}: ${displayScore}/${totalMarks[co]}`;
      }
      return `CO${co}: ${displayScore}%`;
    })
    .join("<br>");
}

// Add these functions to facultyHelper.js

$(document).ready(function () {
  $("#viewCoPoAttainmentBtn").on("click", async function () {
    try {
      $("#cqiAttainmentModal").modal("show");

      const courseData = await fetchCourseDetailsforTimetable(currentCourseId);

      // Update modal with prefixed values
      $("#deptNameCQI").text(`Department of ${courseData.department}`);
      $("#yearSemCQI").text(`Semester ${courseData.semester}`);
      $("#subjectCodeCQI").text(
        `${courseData.course_code} - ${courseData.course_name}`
      );

      showCQIAttainmentReport(currentCourseId);
    } catch (error) {
      console.error("Error fetching course details:", error);
      showNotification("Error", "Failed to load course details", "danger");
    }
  });
});

function showCQIAttainmentReport(courseId) {
  if (!courseId) {
    showNotification("Error", "Please select a course first", "danger");
    return;
  }

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "get_cqi_attainment",
      courseId: courseId,
    },
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        console.log("success");
        populateCQIAttainmentReport(data);
        $("#cqiAttainmentModal").modal("show");
      } else {
        console.log("error");
        showNotification(
          "Error",
          "Failed to load CQI attainment data",
          "danger"
        );
      }
    },
    error: function () {
      showNotification("Error", "Server error occurred", "danger");
    },
  });
}

function populateCQIAttainmentReport(data) {
  const results = analyzeStudentImprovement(data);
  // Update summary cards
  $("#totalStudentsCQI").text(results.summary.total_students);
  $("#improvedStudentsCQI").text(results.summary.students_improved);
  $("#belowTargetAfterCQI").text(results.summary.still_below_target);
  $("#overallImprovement").text(results.summary.overall_improvement);

  // Populate original attainment table
  const originalTbody = $("#originalAttainmentTable tbody").empty();
  Object.entries(results.summary.original_attainment).forEach(([coKey, co]) => {
    originalTbody.append(`
      <tr>
        <td>${coKey}</td>
        <td>${co.original_score}%</td>
        <td>${co.gap}%</td>
      </tr>
    `);
  });

  // Populate improved attainment table
  const improvedTbody = $("#improvedAttainmentTable tbody").empty();
  Object.entries(results.summary.improved_attainment).forEach(([coKey, co]) => {
    improvedTbody.append(`
      <tr>
        <td>${coKey}</td>
        <td>${co.improved_score}%</td>
      </tr>
    `);
  });

  // Populate student details table
  const studentTbody = $("#studentCQITable tbody").empty();
  results.student_results.forEach((student) => {
    const isAllAchieved = Object.values(student.status).every(
      (status) => status === "Achieved"
    );
    const rowClass = isAllAchieved ? "" : "table-warning";
    const statusBadge = isAllAchieved ? "Achieved" : "Below Target";
    const statusClass = isAllAchieved ? "bg-success" : "bg-warning";

    studentTbody.append(`

      <tr class="${rowClass}">
        <td>${student.register_number}</td>
        <td>${student.name}</td>
        <td>${formatCOScores(student.original_cos)}</td>
        <td>${formatCQIMarks(student.cqi_marks)}</td>
        <td>${formatCOScores(student.improved_cos)}</td>
        <td>${formatImprovement(student.improvement)}</td>
        <td><span class="badge ${statusClass}">${statusBadge}</span></td>
      </tr>
    `);
  });
}

function formatCOScores(scores) {
  return Object.entries(scores)
    .map(([coKey, score]) => `${coKey}: ${score}%`)
    .join("<br>");
}

function formatCQIMarks(marks) {
  if (Object.keys(marks).length === 0) {
    return "No CQI Marks";
  }
  return Object.entries(marks)
    .map(([coKey, mark]) => `${coKey}: ${mark}`)
    .join("<br>");
}

function formatImprovement(improvements) {
  if (Object.keys(improvements).length === 0) {
    return "No Improvement";
  }
  return Object.entries(improvements)
    .map(([coKey, improvement]) => `${coKey}: +${improvement}%`)
    .join("<br>");
}

function formatStatus(statuses) {
  return Object.entries(statuses)
    .map(([coKey, status]) => `${coKey}: ${status}`)
    .join("<br>");
}

function analyzeStudentImprovement(data) {
  const TARGET_PERCENTAGE = 58;
  const students = data.students;

  // Calculate metrics for each student
  const studentResults = students.map((student) => {
    const registerNumber = student.register_number;
    const name = student.name;

    // Calculate Original CO percentages
    const originalCOs = {};
    const coKeys = Object.keys(student.totals);

    coKeys.forEach((coKey) => {
      originalCOs[coKey] = parseFloat(
        (student.totals[coKey].regular.obtained /
          student.totals[coKey].regular.total) *
          100
      ).toFixed(2);
    });

    // Calculate CQI Marks and Improvement
    const cqiMarks = {};
    const improvements = {};

    coKeys.forEach((coKey) => {
      // Skip if student already achieved target percentage in original assessment
      if (parseFloat(originalCOs[coKey]) >= TARGET_PERCENTAGE) {
        return;
      }

      // Check if student has CQI marks for this CO
      if (student.totals[coKey].has_both) {
        const cqiTotal = student.totals[coKey].cqi.total;
        const cqiObtained = student.totals[coKey].cqi.obtained;

        // Calculate the maximum possible improvement (gap to reach target)
        const gapToTarget = TARGET_PERCENTAGE - parseFloat(originalCOs[coKey]);

        // Calculate the potential maximum improvement from CQI
        // const maxPossibleImprovement = parseFloat(data.totalCOMarks[coKey].cqi / data.totalCOMarks[coKey].total * gapToTarget);

        // The actual improvement is proportional to marks obtained, but capped at the gap
        const proportionalImprovement = (cqiObtained / cqiTotal) * gapToTarget;
        const actualImprovement = proportionalImprovement;

        cqiMarks[coKey] = `${cqiObtained.toFixed(2)}/${cqiTotal.toFixed(
          2
        )} → ${actualImprovement.toFixed(2)}%`;
        improvements[coKey] = parseFloat(actualImprovement.toFixed(2));
      }
    });

    // Calculate Improved COs
    const improvedCOs = {};

    coKeys.forEach((coKey) => {
      const originalPercentage = parseFloat(originalCOs[coKey]);
      const improvementPercentage = improvements[coKey] || 0;
      improvedCOs[coKey] = parseFloat(
        originalPercentage + improvementPercentage
      ).toFixed(2);
    });

    // Determine Status
    const status = {};

    coKeys.forEach((coKey) => {
      status[coKey] =
        parseFloat(improvedCOs[coKey]) >= TARGET_PERCENTAGE
          ? "Achieved"
          : "Below Target";
    });

    return {
      register_number: registerNumber,
      name,
      original_cos: originalCOs,
      cqi_marks: cqiMarks,
      improved_cos: improvedCOs,
      improvement: improvements,
      status,
    };
  });

  // Calculate summary statistics
  const totalStudents = students.length;

  // Count students who improved from CQI and achieved target
  let studentsImproved = 0;
  let stillBelowTarget = 0;

  studentResults.forEach((student) => {
    const coKeys = Object.keys(student.status);
    let hasImprovedAndAchieved = false;
    let isBelowTarget = false;

    coKeys.forEach((coKey) => {
      // Check if this student had improvement via CQI and achieved target
      if (student.cqi_marks[coKey] && student.status[coKey] === "Achieved") {
        hasImprovedAndAchieved = true;
      }

      if (student.status[coKey] === "Below Target") {
        isBelowTarget = true;
      }
    });

    if (hasImprovedAndAchieved) {
      studentsImproved++;
    }

    if (isBelowTarget) {
      stillBelowTarget++;
    }
  });

  // Calculate original attainment and improved attainment for each CO
  const coKeys = Object.keys(data.totalCOMarks);
  const originalAttainment = {};
  const improvedAttainment = {};

  coKeys.forEach((coKey) => {
    let originalScoreSum = 0;
    let improvedScoreSum = 0;

    studentResults.forEach((student) => {
      originalScoreSum += parseFloat(student.original_cos[coKey]);
      improvedScoreSum += parseFloat(student.improved_cos[coKey]);
    });

    const originalScore = parseFloat(originalScoreSum / totalStudents).toFixed(
      2
    );
    const improvedScore = parseFloat(improvedScoreSum / totalStudents).toFixed(
      2
    );
    const gap = parseFloat(TARGET_PERCENTAGE - originalScore).toFixed(2);
    const improvement = parseFloat(improvedScore - originalScore).toFixed(2);

    originalAttainment[coKey] = {
      original_score: originalScore,
      target: TARGET_PERCENTAGE.toFixed(2),
      gap: gap > 0 ? gap : "0.00",
    };

    improvedAttainment[coKey] = {
      improved_score: improvedScore,
      improvement,
      status:
        parseFloat(improvedScore) >= TARGET_PERCENTAGE
          ? "Achieved"
          : "Below Target",
    };
  });

  // Calculate overall improvement
  let originalAvgSum = 0;
  let improvedAvgSum = 0;
  let coCount = 0;

  coKeys.forEach((coKey) => {
    originalAvgSum += parseFloat(originalAttainment[coKey].original_score);
    improvedAvgSum += parseFloat(improvedAttainment[coKey].improved_score);
    coCount++;
  });

  const originalCoAvg = originalAvgSum / coCount;
  const improvedCoAvg = improvedAvgSum / coCount;
  const overallImprovement = parseFloat(improvedCoAvg - originalCoAvg).toFixed(
    2
  );

  return {
    student_results: studentResults,
    summary: {
      total_students: totalStudents,
      students_improved: studentsImproved,
      still_below_target: stillBelowTarget,
      overall_improvement: overallImprovement,
      original_attainment: originalAttainment,
      improved_attainment: improvedAttainment,
    },
  };
}

function downloadCQIReport() {
  // Implement report download functionality
  alert("Download functionality will be implemented");
}

////// jegan ///////////////////////////////////////////

function showCQIMarkEntry(componentId) {
  const academicYear = $("#academicYear").val();
  const subjectId = $("#subject").val();

  // Get CQI specific data
  $.when(
    callAPI({
      action: "get_cqi_students",
      component_id: componentId,
      subject_id: currentCourseId,
      academic_year: JSON.parse(sessionStorage.getItem("selectedFacultyData"))
        .academicYear,
    }),
    callAPI({
      action: "get_template",
      component_id: componentId,
    })
  ).done(function (studentsResponse, templateResponse) {
    if (
      studentsResponse[0].status === "success" &&
      templateResponse[0].status === "success"
    ) {
      renderCQIMarkEntry(
        componentId,
        studentsResponse[0].data,
        templateResponse[0].data
      );
      $("#markEntryModal").modal("show");
      $("#markEntryModal").data("isCQI", true);
    }
  });
}
function showCQIMarkEntry(componentId) {
  const academicYear = $("#academicYear").val();
  const subjectId = $("#subject").val();
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );

  // Get CQI specific data
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "get_cqi_students",
      component_id: componentId,
      subject_id: currentCourseId,
      facultyId: selectedFacultyData.facultyId,
    },
    dataType: "json",
    success: function (studentsResponse) {
      console.log(studentsResponse);
      if (studentsResponse.status === "success") {
        $.ajax({
          url: "backend.php",
          type: "POST",
          data: {
            action: "get_template",
            component_id: componentId,
          },
          dataType: "json",
          success: function (templateResponse) {
            if (templateResponse.status === "success") {
              renderCQIMarkEntry(
                componentId,
                studentsResponse.data,
                templateResponse.data
              );
              $("#markEntryModal").modal("show");
              $("#markEntryModal").data("isCQI", true);
              loadExistingMarks(componentId);
            }
          },
        });
      }
    },
  });
}

function renderCQIMarkEntry(componentId, students, template) {
  $("#markEntryModal").data("componentId", componentId);

  const headerRow = $("#marksTable thead tr:first-child");
  headerRow.empty();

  // Add fixed columns
  headerRow.append(`
        <th style="min-width: 120px;">Register No</th>
        <th style="min-width: 200px;">Student Name</th>
    `);

  // Add question columns with CO info
  template.forEach(function (question) {
    headerRow.append(`
            <th class="${
              (students[0]?.unattained_cos || []).includes(
                question.co_number.toString()
              )
                ? "bg-warning bg-opacity-25"
                : ""
            }" 
                style="min-width: 120px;">
                Q${question.question_number} (${question.marks})<br>
                CO${question.co_number}
            </th>
        `);
  });

  // Add total and status columns
  headerRow.append(`
        <th style="min-width: 80px;">Total</th>
        <th style="min-width: 100px;">Status</th>
    `);

  // Populate student rows
  const tbody = $("#marksTable tbody");
  tbody.empty();

  students.forEach(function (student) {
    let row = `
            <tr data-student-id="${student.sid}">
                <td>${student.sid}</td>
                <td>${student.name}</td>`;

    // Add input cells for each question
    template.forEach(function (question) {
      const coNumber = question.co_number.toString();
      // Use a default empty array if unattained_cos is undefined
      const isUnattainedCO = (student.unattained_cos || []).includes(coNumber);

      // Retrieve previous mark from the previous_marks object
      let previousMark = student.previous_marks[coNumber] || 0;

      row += `
                <td class="${isUnattainedCO ? "bg-warning bg-opacity-10" : ""}">
                    <div class="position-relative">
                        <input type="number" 
                               class="form-control form-control-sm mark-input" 
                               data-max="${question.marks}"
                               data-question="${question.question_number}"
                               data-co="${question.co_number}"
                               ${!isUnattainedCO ? "disabled" : ""}
                               min="0"
                               step="0.5"
                        >
                        ${
                          isUnattainedCO
                            ? `
                            <small class="text-muted position-absolute top-100 start-0">
                                Previous: ${previousMark}
                            </small>
                        `
                            : ""
                        }
                    </div>
                </td>`;
    });

    // Add total and status columns to row
    row += `
            <td class="total">0</td>
            <td>
                <select class="form-select form-select-sm">
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                </select>
            </td>
        </tr>`;

    tbody.append(row);
  });

  // Add explanatory alert if not already present
  const alertDiv = $("#cqiMarkEntryAlert");
  if (alertDiv.length === 0) {
    $("#markEntryModal .modal-body").prepend(`
            <div id="cqiMarkEntryAlert" class="alert alert-info alert-dismissible fade show mb-3">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <i class="fas fa-info-circle me-2"></i>
                Yellow highlighted columns indicate unattained COs that need improvement.
                Only marks for unattained COs can be entered.
            </div>
        `);
  }
}

// Modify the existing createTemplate function to handle CQI
const originalCreateTemplate = createTemplate;
createTemplate = function (componentId) {
  const isCQI = sessionStorage.getItem("creating_cqi") === "true";
  sessionStorage.removeItem("creating_cqi"); // Clear the state

  // Add CQI indicator to the form if needed
  if (isCQI) {
    $("#templateForm").append('<input type="hidden" name="is_cqi" value="1">');
  }

  // Call original function
  originalCreateTemplate(componentId);
};

// Internal Marks
$(document).ready(function () {
  $("#viewInternalMarksBtn").on("click", async function () {
    try {
      // Show the modal
      $("#internalMarksModal").modal("show");

      // Fetch course details
      const courseData = await fetchCourseDetailsforTimetable(currentCourseId);

      // Update modal header information
      $("#deptNameInternal").text(courseData.department);
      $("#yearSemInternal").text(`Semester ${courseData.semester}`);
      $("#subjectCodeInternal").text(
        `${courseData.course_code} - ${courseData.course_name}`
      );

      // Existing AJAX call for internal marks
      $.ajax({
        url: "backend.php",
        type: "POST",
        data: {
          action: "getInternalMarks",
          courseId: currentCourseId,
        },
        success: function (response) {
          let result;
          try {
            result =
              typeof response === "string" ? JSON.parse(response) : response;
            console.log("Parsed response:", result);
            console.log("Components:", result.components);
            console.log("Total CO Marks:", result.totalCOMarks);

            if (result.status === "success") {
              renderInternalMarksTable(result);
            } else {
              console.error("Failed to load component data:", result.message);
            }
          } catch (e) {
            console.error("Failed to parse response:", e);
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
        },
      });
    } catch (error) {
      console.error("Error fetching course details:", error);
      showNotification("Error", "Failed to load course details", "danger");
    }
  });
});

function renderInternalMarksTable(data) {
  if (!data || !data.headers || !data.students) {
    console.error("Invalid data structure received");
    return;
  }

  const { headers, students } = data;
  const thead = $("#internalMarksTable thead");
  const tbody = $("#internalMarksTable tbody");

  // Clear existing content
  thead.empty();
  tbody.empty();

  // Create header rows
  const headerRow1 = $("<tr>");
  const headerRow2 = $("<tr>");
  const headerRow3 = $("<tr>");

  // First row: Main headers
  headerRow1.append(`
        <th rowspan="3">S.No</th>
        <th rowspan="3">Register Number</th>
        <th rowspan="3">Name</th>
        <th colspan="${Object.keys(headers).length}">Total CO Marks</th>
        <th rowspan="3">Internal Marks</th>
    `);

  // Second row: CO numbers
  Object.keys(headers)
    .sort()
    .forEach((coNum) => {
      headerRow2.append(`<th>${coNum}</th>`);
    });

  // Third row: Maximum marks for each CO
  Object.keys(headers)
    .sort()
    .forEach((coNum) => {
      headerRow3.append(`<th>${headers[coNum]}</th>`);
    });

  thead.append(headerRow1, headerRow2, headerRow3);

  // Add student rows
  students.forEach((student, index) => {
    const row = $("<tr>");

    // Add basic student info
    row.append(`
            <td>${index + 1}</td>
            <td>${student.register_number}</td>
            <td>${student.name}</td>
        `);

    // Add CO marks
    Object.keys(headers)
      .sort()
      .forEach((coNum) => {
        const coMark = student.co_marks[coNum]?.final_total || 0;
        row.append(`<td>${coMark.toFixed(2)}</td>`);
      });

    // Add internal marks
    row.append(`<td>${student.internal_marks.toFixed(2)}</td>`);

    tbody.append(row);
  });
}

function downloadInternal(component_id) {
  $.ajax({
    url: "backend.php", // Adjust the URL to your download endpoint
    type: "GET",
    data: { component_id: component_id, action: "download_internal" },
    success: function (response) {
      generatePDF(response);
    },
    error: function (xhr, status, error) {
      console.error("Download error:", error);
    },
  });
}
function generatePDF(reportData) {
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );
  let parsedData =
    typeof reportData === "string" ? JSON.parse(reportData) : reportData;

  if (!parsedData.data) {
    console.error("Invalid data format");
    return;
  }

  const { courseData, students, statistics, ranges } = parsedData.data;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });

  const margin = 5;
  let currentY = margin;

  // === HEADER ===
  doc.setFont("Helvetica", "bold");
  doc.setFontSize(12);
  doc.text(
    "M.KUMARASAMY COLLEGE OF ENGINEERING, KARUR - 639 113",
    doc.internal.pageSize.getWidth() / 2,
    currentY + 8,
    { align: "center" }
  );
  doc.setFontSize(9);
  doc.setFont("Helvetica", "normal");
  doc.text(
    "(An Autonomous Institution Affiliated to Anna University, Chennai)",
    doc.internal.pageSize.getWidth() / 2,
    currentY + 14,
    { align: "center" }
  );
  doc.setFont("Helvetica", "bold");
  doc.setFontSize(11);
  doc.text(
    "Mark Analysis Report",
    doc.internal.pageSize.getWidth() / 2,
    currentY + 20,
    { align: "center" }
  );

  currentY += 28;

  // === COURSE INFO ===
  doc.setFontSize(9);
  doc.setFont("Helvetica", "normal");
  const lineHeight = 6;
  const pageWidth = doc.internal.pageSize.getWidth();

  const courseInfo = [
    [
      `Course Information: ${courseData.course_code}-${courseData.course_name}`,
      `Printed On: ${courseData.print_date}`,
    ],
    [
      `Department: ${courseData.department || "-"}`,
      `Batch: ${selectedFacultyData.batch}`,
    ],
    [
      `Section: ${courseData.section || "-"}`,
      `Semester: ${selectedFacultyData.semesterType || "-"}`,
    ],
    [`Test Name: ${courseData.exam_type}`, `Max Mark: ${courseData.max_mark}`],
    [
      `Faculty Name: ${courseData.faculty_name}`,
      `Exam Date: ${courseData.exam_date}`,
    ],
  ];

  courseInfo.forEach(([left, right]) => {
    doc.text(left, margin, currentY);
    doc.text(right, pageWidth - margin, currentY, { align: "right" });
    currentY += lineHeight;
  });

  // === STUDENT TABLE HANDLING ===
  const midPoint = Math.ceil(students.length / 2);
  const leftStudents = students.slice(0, midPoint);
  const rightStudents = students.slice(midPoint);

  const tableWidth = (pageWidth - margin * 3) / 2;

  const formatStudents = (studentList, startIndex) =>
    studentList.map((student, index) => [
      index + startIndex,
      `${student.reg_no} - ${student.name}`,
      parseFloat(student.marks).toFixed(2),
    ]);

  const leftTable = formatStudents(leftStudents, 1);
  const rightTable = formatStudents(rightStudents, midPoint + 1);

  const tableOptions = {
    theme: "grid",
    headStyles: {
      fillColor: [64, 64, 64],
      textColor: [255, 255, 255],
      lineColor: [255, 255, 255],
    },
    bodyStyles: { lineColor: [64, 64, 64] },
    styles: { fontSize: 8 },
  };

  // First table (Left Side)
  doc.autoTable({
    ...tableOptions,
    head: [["S.No.", "Register No. & Name", "Marks"]],
    body: leftTable,
    startY: currentY,
    margin: { left: margin },
    tableWidth,
  });

  let leftTableHeight = doc.lastAutoTable.finalY;

  // Second table (Right Side)
  doc.autoTable({
    ...tableOptions,
    head: [["S.No.", "Register No. & Name", "Marks"]],
    body: rightTable,
    startY: currentY,
    margin: { left: margin + tableWidth + margin },
    tableWidth,
  });

  let rightTableHeight = doc.lastAutoTable.finalY;
  currentY = Math.max(leftTableHeight, rightTableHeight) + 6;

  // === RANGE ANALYSIS ===
  doc.setFont("Helvetica", "bold");
  doc.text("Range Analysis", margin, currentY);
  currentY += 5;

  const expectedRanges = [
    "0-9",
    "10-19",
    "20-29",
    "30-39",
    "40-44",
    "45-49",
    "50-54",
  ];
  const rangeRows = expectedRanges.map((rangeKey) => [
    rangeKey,
    `${ranges?.[rangeKey] ?? 0} Students`,
  ]);

  doc.autoTable({
    ...tableOptions,
    head: [["Range", "Student Count"]],
    body: rangeRows,
    startY: currentY,
    margin: { left: margin },
    tableWidth,
  });

  currentY = doc.lastAutoTable.finalY + 10;

  // === STATISTICS SECTION ===
  const statsData = [
    [
      `Average Mark: ${parseFloat(statistics.average).toFixed(2)}`,
      `No. of Absent: ${statistics.absent}`,
    ],
    [
      `No. of Present: ${statistics.present}`,
      `No. of Fail: ${statistics.present - statistics.passCount}`,
    ],
    [
      `Total Strength: ${statistics.present + statistics.absent}`,
      `Pass %: ${parseFloat(statistics.passPercentage).toFixed(2)}`,
    ],
  ];

  statsData.forEach(([left, right]) => {
    doc.text(left, pageWidth - margin - 50, currentY - 60, { align: "right" });
    doc.text(right, pageWidth - margin, currentY - 60, { align: "right" });

    currentY += lineHeight;
  });
  // === SIGNATURE SECTION ===
  const pageHeight = doc.internal.pageSize.getHeight();
  const signatureY = pageHeight - 20;

  doc.setFont("Helvetica", "normal");
  doc.text("Signature of the Faculty", margin, signatureY);
  doc.text("Class Advisor", pageWidth / 2, signatureY, { align: "center" });
  doc.text("Head of the Department", pageWidth - margin, signatureY, {
    align: "right",
  });
  // === SAVE PDF ===
  doc.save("Mark_Statement.pdf");
}


function downloadStudentPDF() {
  const doc = new jspdf.jsPDF("l", "pt", "a4");
  const pageWidth = doc.internal.pageSize.getWidth();
  const printDate = "Printed on: " + new Date().toLocaleString();
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "GetCourseDetailsForPrint",
      courseID: currentCourseId,
    },
    success: function (response) {
      const courseData = response.data;

      // Get the DataTable instance and fetch data from all pages
      var table = $("#studentListTable").DataTable();
      var allData = table.rows({ page: "all" }).data().toArray();

      // Extract headers from <thead> if needed (assuming you want S.no, Register Number, Name, Batch)
      var headers = ["S.no", "Register Number", "Name", "Batch"];

      // Map only the desired fields from the table data.
      var tableData = allData.map((row, index) => [
        index + 1, // S.no
        row.register_number, // Register Number
        row.name, // Name
        row.batch, // Batch
      ]);

      // Add College Logo
      const logo = "image/icons/mkce_s.png";
      doc.addImage(logo, "PNG", 30, 20, 80, 80);

      // College Name
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      doc.text(
        "M.KUMARASAMY COLLEGE OF ENGINEERING, KARUR - 639 113",
        pageWidth / 2,
        40,
        { align: "center" }
      );

      // Tagline
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
      doc.text("Student Name List", pageWidth / 2, 80, { align: "center" });

      // Faculty and Department Details
      doc.setFontSize(12);
      doc.text(
        "Faculty Name ID & Name: " + userData.id + " & " + userData.name,
        50,
        120,
        { align: "left" }
      );
      doc.text("Department: " + userData.dept, 50, 140, { align: "left" });

      // Academic Year and Batch
      doc.text(
        "Academic Year: " + selectedFacultyData.academicYear,
        pageWidth - 100,
        120,
        { align: "center" }
      );
      doc.text("Batch: " + selectedFacultyData.batch, pageWidth - 119, 140, {
        align: "center",
      });
      

      // Print Date
      doc.setFontSize(10);
      doc.text(printDate, pageWidth - 40, 70, { align: "right" });

      // Generate Table using autoTable
      doc.autoTable({
        startY: 170,
        head: [headers],
        body: tableData,
        styles: {
          font: "helvetica",
          fontSize: 10,
          cellPadding: 5,
          overflow: "linebreak",
        },
        headStyles: {
          fillColor: [32, 178, 170],
          textColor: [255],
          fontStyle: "bold",
          halign: "center",
        },
        bodyStyles: { halign: "center", valign: "middle" },
        tableLineColor: [189, 195, 199],
        tableLineWidth: 0.75,
      });

      doc.save("Student_List.pdf");
    },
    error: function (xhr, status, error) {
      console.error("Error sending faculty ID to backend:", error);
    },
  });
}



function downloadAttendaceSummary() {
  const doc = new jspdf.jsPDF("l", "pt", "a4");
  const pageWidth = doc.internal.pageSize.getWidth();
  const printDate = "Printed on: " + new Date().toLocaleString();
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "GetCourseDetailsForPrint",
      courseID: currentCourseId,
    },
    success: function (response) {
      const courseData = response.data;

      // Get the DataTable instance and fetch data from all pages
      var table = $("#attendanceSummaryTable").DataTable();
      var allData = table.rows({ page: "all" }).data().toArray();

      // Extract headers from <thead> if needed (assuming you want S.no, Register Number, Name, Batch)
      var headers = [
        "S.no",
        "Class date",
        "Day",
        "Hour",
        "Leave",
        "Absent",
        "OD",
        "Description",
        
      ];

      // Map only the desired fields from the table data.
      var tableData = allData.map((row, index) => [
        index + 1, // S.no
        row.class_date, // Register Number
        row.day, // Name
        row.hour,
        row.leave,
        row.absent,
        row.od,
        row.description,
        
      ]);

      // Add College Logo
      const logo = "image/icons/mkce_s.png";
      doc.addImage(logo, "PNG", 30, 20, 80, 80);

      // College Name
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      doc.text(
        "M.KUMARASAMY COLLEGE OF ENGINEERING, KARUR - 639 113",
        pageWidth / 2,
        40,
        { align: "center" }
      );

      // Tagline
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
      doc.text("Student Name List", pageWidth / 2, 80, { align: "center" });

      // Faculty and Department Details
      doc.setFontSize(12);
      doc.text(
        "Faculty Name ID & Name: " + userData.id + " & " + userData.name,
        50,
        120,
        { align: "left" }
      );
      doc.text("Department: " + userData.dept, 50, 140, { align: "left" });

      // Academic Year and Batch
      doc.text(
        "Academic Year: " + selectedFacultyData.academicYear,
        pageWidth - 100,
        120,
        { align: "center" }
      );
      doc.text("Batch: " + selectedFacultyData.batch, pageWidth - 119, 140, {
        align: "center",
      });
    

      // Print Date
      doc.setFontSize(10);
      doc.text(printDate, pageWidth - 40, 70, { align: "right" });

      // Generate Table using autoTable
      doc.autoTable({
        startY: 170,
        head: [headers],
        body: tableData,
        styles: {
          font: "helvetica",
          fontSize: 10,
          cellPadding: 5,
          overflow: "linebreak",
        },
        headStyles: {
          fillColor: [32, 178, 170],
          textColor: [255],
          fontStyle: "bold",
          halign: "center",
        },
        bodyStyles: { halign: "center", valign: "middle" },
        tableLineColor: [189, 195, 199],
        tableLineWidth: 0.75,
      });

      doc.save("attendance_summary.pdf");
    },
    error: function (xhr, status, error) {
      console.error("Error sending faculty ID to backend:", error);
    },
  });
}

function downloadAttendacePercentage() {
  const doc = new jspdf.jsPDF("l", "pt", "a4");
  const pageWidth = doc.internal.pageSize.getWidth();
  const printDate = "Printed on: " + new Date().toLocaleString();
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  const selectedFacultyData = JSON.parse(
    sessionStorage.getItem("selectedFacultyData")
  );

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "GetCourseDetailsForPrint",
      courseID: currentCourseId,
    },
    success: function (response) {
      const courseData = response.data;

      // Get the DataTable instance and fetch data from all pages
      var table = $("#attendancePercentageTable").DataTable();
      var allData = table.rows({ page: "all" }).data().toArray();

      // Extract headers from <thead> if needed (assuming you want S.no, Register Number, Name, Batch)
      var headers = [
        "S.no",
        "Register number",
        "Student name",
        "Present hours",
        "total hours",
        "Attendace Percentage",
      ];

      // Map only the desired fields from the table data.
      var tableData = allData.map((row, index) => [
        index + 1, // S.no
        row.register_number, // Register Number
        row.student_name, // Name
        row.present_hours,
        row.total_hours,
        row.attendance_percentage,
      ]);

      // Add College Logo
      const logo = "image/icons/mkce_s.png";
      doc.addImage(logo, "PNG", 30, 20, 80, 80);

      // College Name
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      doc.text(
        "M.KUMARASAMY COLLEGE OF ENGINEERING, KARUR - 639 113",
        pageWidth / 2,
        40,
        { align: "center" }
      );

      // Tagline
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
      doc.text("Student Name List", pageWidth / 2, 80, { align: "center" });

      // Faculty and Department Details
      doc.setFontSize(12);
      doc.text(
        "Faculty Name ID & Name: " + userData.id + " & " + userData.name,
        50,
        120,
        { align: "left" }
      );
      doc.text("Department: " + userData.dept, 50, 140, { align: "left" });

      // Academic Year and Batch
      doc.text(
        "Academic Year: " + selectedFacultyData.academicYear,
        pageWidth - 100,
        120,
        { align: "center" }
      );
      doc.text("Batch: " + selectedFacultyData.batch, pageWidth - 119, 140, {
        align: "center",
      });
      

      // Print Date
      doc.setFontSize(10);
      doc.text(printDate, pageWidth - 40, 70, { align: "right" });

      // Generate Table using autoTable
      doc.autoTable({
        startY: 170,
        head: [headers],
        body: tableData,
        styles: {
          font: "helvetica",
          fontSize: 10,
          cellPadding: 5,
          overflow: "linebreak",
        },
        headStyles: {
          fillColor: [32, 178, 170],
          textColor: [255],
          fontStyle: "bold",
          halign: "center",
        },
        bodyStyles: { halign: "center", valign: "middle" },
        tableLineColor: [189, 195, 199],
        tableLineWidth: 0.75,
      });

      doc.save("attendance_percentage.pdf");
    },
    error: function (xhr, status, error) {
      console.error("Error sending faculty ID to backend:", error);
    },
  });
}

// Helper function to show notifications
function showNotification(title, message, type) {
  Swal.fire({
    title: title,
    text: message,
    icon: type === "success" ? "success" : "error",
    timer: type === "success" ? 2000 : undefined,
    showConfirmButton: type !== "success",
  });
}

function processAttainmentData(data) {
  if (!data || !data.totalCOMarks || !data.students || !data.components) {
    console.error("Invalid data for attainment calculation");
    return;
  }

  const attainmentResults = calculateCOAttainment(data);
  displayAttainmentTable(attainmentResults, data);
}

function calculateCOAttainment(data) {
  const { totalCOMarks, students, components } = data;
  const attainmentData = {
    components: {},
    overall: {},
  };

  // Calculate attainment for each component
  Object.keys(components).forEach((componentName) => {
    attainmentData.components[componentName] = {};

    // Get COs for this component
    Object.keys(components[componentName]).forEach((coNum) => {
      const maxMarks = parseFloat(components[componentName][coNum]);
      const threshold = maxMarks * 0.5; // 50% of component's max marks

      let studentsAboveThreshold = 0;
      students.forEach((student) => {
        if (
          student.marks[componentName]?.[coNum] &&
          !student.marks[componentName][coNum].is_absent &&
          parseFloat(student.marks[componentName][coNum].marks) > threshold
        ) {
          studentsAboveThreshold++;
        }
      });

      const percentage = (studentsAboveThreshold / students.length) * 100;
      attainmentData.components[componentName][coNum] = {
        percentage: percentage.toFixed(2),
        level: determineAttainmentLevel(percentage),
      };
    });
  });

  // Calculate overall attainment for each CO
  Object.keys(totalCOMarks).forEach((coNum) => {
    const maxMarks = totalCOMarks[coNum];
    const threshold = maxMarks * 0.5; // 50% of total max marks

    let studentsAboveThreshold = 0;
    students.forEach((student) => {
      const coTotal = calculateTotalForCO(student.marks, coNum);
      if (!coTotal.is_absent && coTotal.marks > threshold) {
        studentsAboveThreshold++;
      }
    });

    const percentage = (studentsAboveThreshold / students.length) * 100;
    attainmentData.overall[coNum] = {
      percentage: percentage.toFixed(2),
      level: determineAttainmentLevel(percentage),
    };
  });

  return attainmentData;
}

function calculateTotalForCO(studentMarks, coNum) {
  let totalMarks = 0;
  let hasAbsent = false;

  Object.keys(studentMarks || {}).forEach((component) => {
    if (studentMarks[component]?.[coNum]) {
      if (studentMarks[component][coNum].is_absent) {
        hasAbsent = true;
      } else {
        totalMarks += parseFloat(studentMarks[component][coNum].marks) || 0;
      }
    }
  });

  return {
    marks: hasAbsent ? 0 : totalMarks,
    is_absent: hasAbsent,
  };
}

function determineAttainmentLevel(percentage) {
  if (percentage >= 70) return 3;
  if (percentage >= 65) return 2;
  if (percentage >= 60) return 1;
  return 0;
}

function displayAttainmentTable(attainmentData, data) {
  const table = $("#attainmentTable");
  const thead = table.find("thead tr");
  const tbody = table.find("tbody");

  // Clear existing content
  thead.empty();
  tbody.empty();

  // Add headers
  thead.append("<th>Component</th>");
  Object.keys(data.totalCOMarks)
    .sort()
    .forEach((coNum) => {
      thead.append(`<th>${coNum}</th>`);
    });

  // Add component rows
  Object.keys(data.components).forEach((componentName) => {
    const row = $("<tr>");
    row.append(`<td>${componentName}</td>`);

    Object.keys(data.totalCOMarks)
      .sort()
      .forEach((coNum) => {
        if (data.components[componentName][coNum]) {
          const result = attainmentData.components[componentName][coNum];
          row.append(`
          <td>
            Level ${result.level}<br>
            <small>(${result.percentage}%)</small>
          </td>
        `);
        } else {
          row.append("<td>-</td>");
        }
      });

    tbody.append(row);
  });

  // Add Overall row
  const overallRow = $("<tr class='table-info'>");
  overallRow.append("<td><strong>Overall</strong></td>");

  Object.keys(data.totalCOMarks)
    .sort()
    .forEach((coNum) => {
      const result = attainmentData.overall[coNum];
      overallRow.append(`
      <td>
        <strong>Level ${result.level}</strong><br>
        <small>(${result.percentage}%)</small>
      </td>
    `);
    });

  tbody.append(overallRow);

  addAttainmentTableStyles();
}

function addAttainmentTableStyles() {
  $("#attainmentTable").addClass("table table-bordered table-hover");
  $("#attainmentTable thead").addClass("table-light");
  $("#attainmentTable th, #attainmentTable td").addClass(
    "text-center align-middle"
  );
}
function downloadReport() {
  // Create a new workbook
  const wb = XLSX.utils.book_new();

  // Helper function to convert a table to a 2D array with proper formatting
  function tableToArray(tableSelector) {
    const table = document.querySelector(tableSelector);
    if (!table) {
      console.error("Table not found:", tableSelector);
      return [];
    }

    const data = [];
    table.querySelectorAll("tr").forEach((row) => {
      const rowData = [];
      row.querySelectorAll("th, td").forEach((cell) => {
        // Preserve cell formatting by replacing newlines with space
        rowData.push(cell.innerText.replace(/\n/g, " "));
      });
      data.push(rowData);
    });
    return data;
  }

  // --- 1. Header Information ---
  let headerInfo = [
    ["M.Kumarasamy College of Engineering"],
    ["Department of", document.getElementById("deptName")?.innerText || ""],
    ["Internal Assessment Report"],
    [],
    ["Year & Sem:", document.getElementById("yearSem")?.innerText || ""],
    ["Subject:", document.getElementById("subjectCode")?.innerText || ""],
    [],
  ];

  // Add cell styles for header information
  let wsHeader = XLSX.utils.aoa_to_sheet(headerInfo);
  wsHeader["!cols"] = [{ wch: 25 }, { wch: 35 }]; // Set column widths
  XLSX.utils.book_append_sheet(wb, wsHeader, "Report Header");

  // --- 2. Attainment Table ---
  let attainmentData = tableToArray("#attainmentTable");
  if (attainmentData.length > 0) {
    let wsAttainment = XLSX.utils.aoa_to_sheet(attainmentData);
    // Auto-size columns based on content
    const attainmentCols = attainmentData[0].map((_, i) => ({
      wch: Math.max(...attainmentData.map((row) => row[i]?.length || 10)),
    }));
    wsAttainment["!cols"] = attainmentCols;
    XLSX.utils.book_append_sheet(wb, wsAttainment, "Attainment Data");
  }

  // --- 3. CO (Marks) Table ---
  let marksData = tableToArray("#COMarkID");
  if (marksData.length > 0) {
    let wsMarks = XLSX.utils.aoa_to_sheet(marksData);
    // Auto-size columns for marks data
    const marksCols = marksData[0].map((_, i) => ({
      wch: Math.max(...marksData.map((row) => (row[i]?.length || 10) + 2)),
    }));
    wsMarks["!cols"] = marksCols;
    XLSX.utils.book_append_sheet(wb, wsMarks, "Marks Data");
  }

  // Write the file with options
  try {
    const opts = {
      bookType: "xlsx",
      bookSST: false,
      type: "binary",
      cellStyles: true,
    };
    XLSX.writeFile(wb, "Internal_Assessment_Report.xlsx", opts);
    alert("Excel file downloaded successfully!");
  } catch (err) {
    console.error("Error generating Excel file:", err);
    alert("Failed to generate Excel file: " + err.message);
  }
}

// Add the delete function
function deleteExamComponent(componentId) {
  // Show confirmation dialog
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      // Send delete request to backend
      $.ajax({
        url: "backend.php",
        type: "POST",
        data: {
          action: "deleteExamComponent",
          component_id: componentId,
        },
        success: function (response) {
          let result;
          try {
            result =
              typeof response === "object" ? response : JSON.parse(response);
          } catch (e) {
            Swal.fire({
              title: "Error",
              text: "Invalid response from server",
              icon: "error",
            });
            return;
          }

          if (result.status === "success") {
            Swal.fire({
              title: "Deleted!",
              text: result.message || "Component has been deleted.",
              icon: "success",
            }).then(() => {
              // Reload the exam components
              loadExamComponents(currentCourseId);
            });
          } else {
            Swal.fire({
              title: "Error",
              text: result.message || "Failed to delete component.",
              icon: "error",
            });
          }
        },
        error: function (xhr, status, error) {
          Swal.fire({
            title: "Error",
            text: "Failed to delete component. Please try again.",
            icon: "error",
          });
        },
      });
    }
  });
}
