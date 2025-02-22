$(document).ready(function () {
  // Configure Toastr
  toastr.options = {
    closeButton: true,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    timeOut: 3000, // 5 seconds
    extendedTimeOut: 1000, //this is for the progress bar
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  loadCourses();
  getPendingLessonPlanEditRequest();
  getTimeTableEditRequest();
  getAttendanceLockedRequest();
});

/**
 * Fetch courses based on the department of the HOD stored in sessionStorage.
 */
function loadCourses() {
  var sessionUser = sessionStorage.getItem("userData");
  if (!sessionUser) {
    console.error("User session data not found!");
    return;
  }

  var user = JSON.parse(sessionUser);
  var dept = user.dept;

  $.ajax({
    url: "backend.php",
    type: "GET",
    dataType: "json",
    data: {
      action: "getLmsCourses",
      dept: dept,
    },
    success: function (response) {
      if (response.status === "success") {
        const container = $("#lms-approval-request-container");
        container.empty();

        if (response.data.approvals && response.data.approvals.length > 0) {
          //toast the message
          toastr.success(
            "You have " +
              response.data.approvals.length +
              " new LMS Approval Requests",
            "LMS Approval Requests"
          );

          const style = `
            <style>
              .lesson-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                border: none;
                margin-bottom: 20px;
                min-height: 60px;
              }
              .lesson-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
              }
              .action-buttons {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                display: flex;
                gap: 10px;
              }
              .action-btn {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: none;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                padding: 0;
              }
              .review-btn {
                background: linear-gradient(45deg, #2196F3, #64B5F6);
              }
              .approve-btn {
                background: linear-gradient(45deg, #4CAF50, #45a049);
              }
              .reject-btn {
                background: linear-gradient(45deg, #f44336, #e57373);
              }
              .action-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
              }
              .course-info span {
                display: block;
                margin: 8px 0;
                color: #666;
                font-size: 0.9rem;
              }
              .course-name {
                color: #2196F3;
                font-weight: 600;
                font-size: 1.2rem;
                margin-bottom: 12px;
              }
              .progress-line {
                height: 3px;
                background: linear-gradient(90deg, #2196F3, #64B5F6);
                margin-top: 15px;
                border-radius: 3px;
              }
            </style>
          `;
          container.append(style);

          const rowHtml = '<div class="row" style="margin-top: 20px;"></div>';
          container.append(rowHtml);
          const row = container.find(".row");

          response.data.approvals.forEach(function (course) {
            const cardHtml = `
              
              <div class="col-xl-4 col-sm-6 col-12" data-course-id="${course.course_id}" 
                 >
                <div class="card lesson-card">
                  <div class="card-content">
                    <div class="card-body" style="padding: 20px; position: relative;">
                      <div class="media d-flex">
                        <div class="media-body course-info" style="padding-right: 140px;">
                          <h3 class="course-name">${course.course_name}</h3>
                          <span>
                            <i class="fas fa-user-tie" style="color: #4CAF50; margin-right: 8px;"></i>
                            Faculty: ${course.staff_name}
                          </span>
                          <span>
                            <i class="fas fa-calendar-alt" style="color: #FFC107; margin-right: 8px;"></i>
                            Academic Year: ${course.ayear}
                          </span>
                          <span>
                            <i class="fas fa-graduation-cap" style="color: #2196F3; margin-right: 8px;"></i>
                            Semester: ${course.semester}
                          </span>
                          <span>
                            <i class="fas fa-info-circle" style="color: #9C27B0; margin-right: 8px;"></i>
                            Status: ${course.status}
                          </span>
                        </div>
                        <div class="action-buttons">
                          <button class="action-btn review-btn" onclick="loadCourseDetails('${course.course_id}', '${course.course_name}')">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button class="action-btn approve-btn" onclick="ApproveCourse(${course.course_id})">
                            <i class="fas fa-check"></i>
                          </button>
                          <button class="action-btn reject-btn" onclick="showRejectModal(${course.course_id})">
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                      </div>
                      <div class="progress-line"></div>
                    </div>
                  </div>
                </div>
              </div>
            `;
            row.append(cardHtml);
          });
        } else {
          const emptyStateHtml = `
            <div style="
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              min-height: 60vh;
              text-align: center;
              padding: 20px;
            ">
              <div style="
                width: 80px;
                height: 80px;
                background: #f8f9fa;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
              ">
                <i class="fas fa-clipboard-check" style="font-size: 2.5rem; color: #6c757d;"></i>
              </div>
              <h4 style="color: #495057; font-weight: 600; margin-bottom: 10px;">
                No Pending Course Approvals
              </h4>
              <p style="color: #6c757d; font-size: 1.1rem; max-width: 500px; line-height: 1.5;">
                All courses have been reviewed. You're up to date!
              </p>
            </div>
          `;
          container.html(emptyStateHtml);
        }
      } else {
        console.error("Error fetching courses: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
    },
  });
}

/**
 * Opens the Review Modal for a course.
 * (Extend this function to fetch and populate course details if needed.)
 */
function openReviewModal(courseId) {
  console.log("Opening Review Modal for course:", courseId);
  // Populate the modal with course data based on courseId as needed.
  $("#reviewModal").modal("show");
}

/**
 * Opens the Reject Modal and sets the course ID for rejection.
 */
function openRejectModal(courseId) {
  $("#rejectCourseId").val(courseId);
  $("#rejectModal").modal("show");
}

/**
 * Opens the Course Video or Detail Modal.
 * (Extend this function to load video details or further course information.)
 */
function openCourseModal(courseId) {
  console.log("Opening Course Modal for course:", courseId);
  $("#courseVideoModal").modal("show");
}

function ApproveCourse(courseId) {
  console.log("Approving Course:", courseId);
  $.ajax({
    url: "backend.php",
    type: "POST",
    dataType: "json",
    data: {
      action: "ApproveCourse",
      courseId: courseId,
    },
    success: function (response) {
      console.log("Response:", response);
      if (response.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Approved!",
          text: response.message,
          confirmButtonText: "OK",
        }).then(() => {
          loadCourses();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error!",
          text: response.message,
          confirmButtonText: "OK",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      Swal.fire({
        icon: "error",
        title: "AJAX Error",
        text: error,
        confirmButtonText: "OK",
      });
    },
  });
}
function loadCourseDetails(courseId, courseName) {
  console.log(courseId, courseName);
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getLms",
      course_id: courseId,
    },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        const data = response.data;
        console.log(data);
        document.getElementById("modalCourseName").textContent = courseName;
        const unitsContainer = document.getElementById("unitsContainer");
        unitsContainer.innerHTML = "";

        // Enhanced CSS for better UI
        const style = document.createElement("style");
        style.textContent = `
          .unit-section {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
          }
          .unit-header {
            background: #f8f9fa;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
          }
          .unit-header:hover {
            background: #f1f3f5;
            border-left-color: #007bff;
          }
          .unit-section.active .unit-header {
            background: #e9ecef;
            border-left-color: #0056b3;
          }
          .topics-container {
            display: none;
            padding: 20px;
            background: #fff;
          }
          .unit-section.active .topics-container {
            display: block;
            animation: slideDown 0.3s ease-out;
          }
          .topic-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
          }
          .topic-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
          }
          .topic-buttons {
            gap: 8px;
          }
          .topic-button {
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
          }
          .topic-button:hover {
            transform: scale(1.05);
          }
          .topic-name {
            font-weight: 500;
            color: #495057;
          }
          .unit-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #343a40;
          }
          .unit-co {
            font-size: 0.9rem;
            color: #6c757d;
            margin-left: 8px;
          }
          @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
          }
        `;
        document.head.appendChild(style);

        data.units.forEach((unit, index) => {
          const unitElement = document.createElement("div");
          unitElement.className = "unit-section";
          unitElement.innerHTML = `
            <div class="unit-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="fas fa-book-open me-3 text-primary"></i>
                <div>
                  <span class="unit-title">${unit.unit_name}</span>
                  <span class="unit-co">${unit.CO}</span>
                </div>
              </div>
              <i class="fas fa-chevron-down chevron-icon"></i>
            </div>
            <div class="topics-container">
              <div class="row g-3">
                ${unit.topics
                  .map(
                    (topic) => `
                  <div class="col-12">
                    <div class="topic-item d-flex justify-content-between align-items-center">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-circle-dot me-3 text-primary"></i>
                        <span class="topic-name">${topic.topic_name}</span>
                      </div>
                      <div class="topic-buttons d-flex">
                        <button class="btn btn-primary topic-button" 
                                onclick="watchVideo('${topic.video_link}', '${topic.topic_name}')"
                                title="Watch Video">
                          <i class="fas fa-play me-2"></i>Watch
                        </button>
                        <button class="btn btn-info topic-button" 
                                onclick="showUnitNotes('${topic.notes}', '${topic.topic_name}')"
                                title="View Notes">
                          <i class="fas fa-file-alt me-2"></i>Notes
                        </button>
                      </div>
                    </div>
                  </div>
                `
                  )
                  .join("")}
              </div>
            </div>
          `;
          unitsContainer.appendChild(unitElement);

          // Add click event for accordion behavior
          const header = unitElement.querySelector(".unit-header");
          header.addEventListener("click", () => {
            document.querySelectorAll(".unit-section").forEach((section) => {
              if (section !== unitElement) {
                section.classList.remove("active");
              }
            });
            unitElement.classList.toggle("active");
          });

          // Show first unit by default
          if (index === 0) {
            unitElement.classList.add("active");
          }
        });

        // Show modal with proper backdrop handling
        const reviewModal = new bootstrap.Modal(
          document.getElementById("reviewModal"),
          {
            backdrop: true,
            keyboard: true,
          }
        );
        reviewModal.show();

        // Add event listener for modal close
        document
          .getElementById("reviewModal")
          .addEventListener("hidden.bs.modal", function () {
            const backdrop = document.querySelector(".modal-backdrop");
            if (backdrop) {
              backdrop.remove();
            }
            document.body.classList.remove("modal-open");
          });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Failed to load course details",
          timer: 3000,
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "An error occurred while loading course details",
        timer: 3000,
      });
    },
  });
}
function watchVideo(url, topicName) {
  // Hide review modal
  const reviewModal = bootstrap.Modal.getInstance(
    document.getElementById("reviewModal")
  );
  reviewModal.hide();

  // Setup video modal
  const videoModal = new bootstrap.Modal(
    document.getElementById("courseVideoModal")
  );
  const videoFrame = document.getElementById("courseVideoFrame");
  const videoTitle = document.querySelector("#courseVideoModal .modal-title");

  // Update modal title
  videoTitle.textContent = topicName;

  // Convert URL if it's a YouTube link
  if (url.includes("youtube.com") || url.includes("youtu.be")) {
    const videoId = url.match(
      /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i
    )?.[1];
    if (videoId) {
      console.log(videoId);
      url = `https://www.youtube.com/embed/${videoId}`;
    }
  }

  // Set iframe source
  videoFrame.src = url;

  // Show video modal
  videoModal.show();
}
// Function to show unit notes
function showUnitNotes(filePath, unitTitle) {
  console.log(filePath, unitTitle);
  // Hide review modal
  const reviewModal = bootstrap.Modal.getInstance(
    document.getElementById("reviewModal")
  );
  reviewModal.hide();

  // Setup notes modal
  const notesModal = new bootstrap.Modal(
    document.getElementById("unitNotesModal")
  );
  const notesFrame = document.getElementById("unitNotesFrame");
  const notesTitle = document.getElementById("unitNotesModalLabel");

  // Update modal title
  notesTitle.textContent = `Notes: ${unitTitle}`;

  // Set iframe source to file serving endpoint
  notesFrame.src = `backend.php?action=serveFile&file=${encodeURIComponent(
    filePath.trim()
  )}`;
  // Show notes modal
  notesModal.show();
}
function showRejectModal(courseId) {
  // Clear previous reason
  document.getElementById("rejectReason").value = "";
  // Set course ID
  document.getElementById("rejectCourseId").value = courseId;
  // Show modal
  const rejectModal = new bootstrap.Modal(
    document.getElementById("rejectModal")
  );
  rejectModal.show();
}

function closeVideoModal() {
  document.getElementById("courseVideoFrame").src = "";

  const videoModal = bootstrap.Modal.getInstance(
    document.getElementById("courseVideoModal")
  );
  videoModal.hide();

  const reviewModal = new bootstrap.Modal(
    document.getElementById("reviewModal")
  );
  reviewModal.show();
}

// Add this after your existing code
document.addEventListener("DOMContentLoaded", function () {
  // Get the reject modal element
  const rejectModal = document.getElementById("rejectModal");

  // Add event listener for when the modal is hidden
  rejectModal.addEventListener("hidden.bs.modal", function () {
    // Remove backdrop and reset body
    const backdrop = document.querySelector(".modal-backdrop");
    if (backdrop) {
      backdrop.remove();
    }
    document.body.classList.remove("modal-open");
    document.body.style.overflow = "";
    document.body.style.paddingRight = "";
  });
});

class TablePagination {
  constructor(tableId, rowsPerPage = 5) {
    this.table = document.getElementById(tableId);
    this.rowsPerPage = rowsPerPage;
    this.currentPage = 1;
    this.rows = Array.from(
      this.table.getElementsByTagName("tbody")[0].getElementsByTagName("tr")
    );
    this.totalRows = this.rows.length;
    this.totalPages = Math.ceil(this.totalRows / this.rowsPerPage);
    this.searchTerm = "";

    this.init();
  }

  init() {
    // Create table controls container
    const controls = document.createElement("div");
    controls.className = "table-controls";
    this.table.parentNode.insertBefore(controls, this.table);

    // Add entries dropdown
    const entriesDiv = document.createElement("div");
    entriesDiv.className = "entries-dropdown";
    const entriesLabel = document.createElement("label");
    entriesLabel.textContent = "Show ";
    const entriesSelect = document.createElement("select");
    [5, 10, 15, 20].forEach((value) => {
      const option = new Option(value, value);
      entriesSelect.appendChild(option);
    });
    const entriesLabelEnd = document.createElement("label");
    entriesLabelEnd.textContent = " entries";

    entriesSelect.value = this.rowsPerPage;
    entriesSelect.addEventListener("change", (e) => {
      this.rowsPerPage = parseInt(e.target.value);
      this.currentPage = 1;
      this.updateVisibleRows();
      this.updateTable();
      this.updatePagination();
    });

    entriesDiv.appendChild(entriesLabel);
    entriesDiv.appendChild(entriesSelect);
    entriesDiv.appendChild(entriesLabelEnd);
    controls.appendChild(entriesDiv);

    // Add search box (moved to right)
    const searchBox = document.createElement("div");
    searchBox.className = "search-box";
    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.className = "search-input";
    searchInput.placeholder = "Search...";
    searchInput.addEventListener("input", (e) =>
      this.handleSearch(e.target.value)
    );
    searchBox.appendChild(searchInput);
    controls.appendChild(searchBox);

    // Create pagination container
    const container = document.createElement("div");
    container.className = "custom-pagination";
    this.table.parentNode.insertBefore(container, this.table.nextSibling);

    // Add entries info
    const info = document.createElement("div");
    info.className = "entries-info";
    container.appendChild(info);

    // Add pagination buttons
    const buttons = document.createElement("div");
    buttons.className = "pagination-buttons";
    container.appendChild(buttons);

    this.updateTable();
    this.updatePagination();
  }

  handleSearch(term) {
    this.searchTerm = term.toLowerCase();
    this.currentPage = 1;

    this.rows.forEach((row) => {
      const text = Array.from(row.cells)
        .map((cell) => cell.textContent)
        .join(" ")
        .toLowerCase();

      const match = text.includes(this.searchTerm);
      row.classList.toggle("search-hidden", !match);
    });

    this.updateVisibleRows();
    this.updateTable();
    this.updatePagination();
  }

  updateVisibleRows() {
    const visibleRows = this.rows.filter(
      (row) => !row.classList.contains("search-hidden")
    );
    this.totalRows = visibleRows.length;
    this.totalPages = Math.ceil(this.totalRows / this.rowsPerPage);
    if (this.currentPage > this.totalPages) {
      this.currentPage = this.totalPages || 1;
    }
  }

  updateTable() {
    const visibleRows = this.rows.filter(
      (row) => !row.classList.contains("search-hidden")
    );

    // Hide all rows first
    this.rows.forEach((row) => {
      row.style.display = "none";
    });

    if (visibleRows.length === 0) {
      const info = this.table.nextSibling.querySelector(".entries-info");
      info.textContent = "Showing 0 to 0 of 0 entries";
      return;
    }

    // Calculate start and end indices based on current page and rowsPerPage
    const start = (this.currentPage - 1) * this.rowsPerPage;
    const end = Math.min(start + this.rowsPerPage, visibleRows.length);

    // Show rows for current page
    visibleRows.slice(start, end).forEach((row) => {
      row.style.display = "";
    });

    // Update entries info
    const info = this.table.nextSibling.querySelector(".entries-info");
    info.textContent = `Showing ${start + 1} to ${end} of ${
      visibleRows.length
    } entries`;
  }

  updatePagination() {
    const buttons = this.table.nextSibling.querySelector(".pagination-buttons");
    buttons.innerHTML = "";

    if (this.totalRows === 0) return;

    // Previous button
    const prevBtn = document.createElement("button");
    prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
    prevBtn.disabled = this.currentPage === 1;
    prevBtn.onclick = () => this.goToPage(this.currentPage - 1);
    buttons.appendChild(prevBtn);

    // Calculate total pages based on current rowsPerPage
    const totalPages = Math.ceil(this.totalRows / this.rowsPerPage);

    // Page buttons
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement("button");
      pageBtn.textContent = i;
      pageBtn.className = this.currentPage === i ? "active" : "";
      pageBtn.onclick = () => this.goToPage(i);
      buttons.appendChild(pageBtn);
    }

    // Next button
    const nextBtn = document.createElement("button");
    nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
    nextBtn.disabled = this.currentPage === totalPages;
    nextBtn.onclick = () => this.goToPage(this.currentPage + 1);
    buttons.appendChild(nextBtn);
  }

  goToPage(page) {
    this.currentPage = page;
    this.updateTable();
    this.updatePagination();
  }
}

// Initialize pagination for both tables
document.addEventListener("DOMContentLoaded", function () {
  new TablePagination("approval_table");
  new TablePagination("available_table");
});

function submitRejection() {
  const courseId = document.getElementById("rejectCourseId").value;
  const reason = document.getElementById("rejectReason").value.trim();

  if (!courseId || !reason) {
    Swal.fire({
      icon: "warning",
      title: "Warning",
      text: "Please provide both course ID and reason for rejection",
      timer: 3000,
    });
    return;
  }

  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "updateStatus",
      courseId: courseId,
      status: "Rejected",
      reason: reason,
    },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        // Close the modal
        const rejectModal = bootstrap.Modal.getInstance(
          document.getElementById("rejectModal")
        );
        rejectModal.hide();

        // Show success message
        Swal.fire({
          icon: "success",
          title: "Success",
          text: "Course rejected successfully",
          timer: 3000,
        }).then(() => {
          loadCourses();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.message || "Failed to reject the course",
          timer: 3000,
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "An error occurred while rejecting the course",
        timer: 3000,
      });
    },
  });
}
function closeUnitNotesModal() {
  // Get modal instances
  const notesModal = bootstrap.Modal.getInstance(
    document.getElementById("unitNotesModal")
  );
  const notesFrame = document.getElementById("unitNotesFrame");

  // Clear notes source
  notesFrame.src = "";

  // Hide notes modal
  notesModal.hide();

  // Show review modal again
  const reviewModal = new bootstrap.Modal(
    document.getElementById("reviewModal")
  );
  reviewModal.show();
}
// First add this HTML for the notes modal
const unitNotesModalHTML = `
<div class="modal fade" id="unitNotesModal" tabindex="-1" aria-labelledby="unitNotesModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="unitNotesModalLabel">Unit Notes</h5>
            <button type="button" class="btn-close" onclick="closeUnitNotesModal()"></button>
        </div>
        <div class="modal-body">
            <div class="ratio ratio-16x9">
                <iframe id="unitNotesFrame" src="" allowfullscreen></iframe>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeUnitNotesModal()">Close</button>
        </div>
    </div>
</div>
</div>`;

document.body.insertAdjacentHTML("beforeend", unitNotesModalHTML);

function getPendingLessonPlanEditRequest() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getPendingLessonPlanEditRequest",
    },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        const data = response.data;
        const container = $("#lesson-plan-edit-request-container");
        container.empty();

        if (data.length > 0) {
          toastr.info(
            `You have ${data.length} pending lesson plan requests`,
            "Lesson Plan Requests"
          );

          const style = `
            <style>
            
              .lesson-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                border: none;
                margin-bottom: 20px;
              }
              .lesson-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
              }
              .approve-btn {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                background: linear-gradient(45deg, #4CAF50, #45a049);
                border: none;
                color: white;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
              }
              .approve-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
              }
              .course-info span {
                display: block;
                margin: 8px 0;
                color: #666;
                font-size: 0.9rem;
              }
              .course-name {
                color: #2196F3;
                font-weight: 600;
                font-size: 1.2rem;
                margin-bottom: 12px;
              }
              .progress-line {
                height: 3px;
                background: linear-gradient(90deg, #2196F3, #64B5F6);
                margin-top: 15px;
                border-radius: 3px;
              }
            </style>
          `;
          container.append(style);

          const rowHtml = '<div class="row" style="margin-top: 20px;"></div>';
          container.append(rowHtml);
          const row = container.find(".row");

          data.forEach(function (item) {
            const cardHtml = `
              <div class="col-xl-4 col-sm-6 col-12" data-course-id="${item.course_id}" 
                  >
                <div class="card lesson-card">
                  <div class="card-content">
                    <div class="card-body" style="padding: 20px;">
                      <div class="media d-flex" style="position: relative;">
                        <div class="media-body course-info" style="padding-right: 60px;">
                          <h3 class="course-name">${item.course_name}</h3>
                          <span>
                            <i class="fas fa-graduation-cap" style="color: #2196F3; margin-right: 8px;"></i>
                            Semester: ${item.semester}
                          </span>
                          <span>
                            <i class="fas fa-user-tie" style="color: #4CAF50; margin-right: 8px;"></i>
                            Faculty: ${item.faculty_name}
                          </span>
                          <span>
                            <i class="fas fa-calendar-alt" style="color: #FFC107; margin-right: 8px;"></i>
                           ${item.ayear}
                          </span>
                        </div>
                        <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%);">
                          <button onclick="approveLessonPlan('${item.course_id}')" 
                                  class="approve-btn">
                            <i class="fas fa-check" style="font-size: 1.2rem;"></i>
                          </button>
                        </div>
                      </div>
                      <div class="progress-line"></div>
                    </div>
                  </div>
                </div>
              </div>
            `;
            row.append(cardHtml);
          });
        } else {
          const emptyStateHtml = `
            <div style="
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              min-height: 60vh;
              text-align: center;
              padding: 20px;
            ">
              <div style="
                width: 80px;
                height: 80px;
                background: #f8f9fa;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
              ">
                <i class="fas fa-clipboard-check" style="
                  font-size: 2.5rem;
                  color: #6c757d;
                "></i>
              </div>
              <h4 style="
                color: #495057;
                font-weight: 600;
                margin-bottom: 10px;
              ">No Pending Requests</h4>
              <p style="
                color: #6c757d;
                font-size: 1.1rem;
                max-width: 500px;
                line-height: 1.5;
              ">
                All lesson plan edit requests have been reviewed. You're up to date!
                <br>
              
              </p>
            </div>
          `;
          container.html(emptyStateHtml);
        }
      } else {
        console.error("Error: " + response.message);
        // Show error state
        const errorStateHtml = `
          <div style="
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            padding: 20px;
          ">
            <div style="
              width: 80px;
              height: 80px;
              background: #fff3f3;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              margin-bottom: 20px;
              box-shadow: 0 4px 15px rgba(220,53,69,0.1);
            ">
              <i class="fas fa-exclamation-triangle" style="
                font-size: 2.5rem;
                color: #dc3545;
              "></i>
            </div>
            <h4 style="
              color: #dc3545;
              font-weight: 600;
              margin-bottom: 10px;
            ">Unable to Load Requests</h4>
            <p style="
              color: #6c757d;
              font-size: 1.1rem;
              max-width: 500px;
              line-height: 1.5;
            ">
              There was an issue loading the lesson plan requests. Please try refreshing the page.
              <br>
              <span style="font-size: 0.9rem; color: #868e96; margin-top: 8px; display: block;">
                If the problem persists, please contact technical support.
              </span>
            </p>
          </div>
        `;
        container.html(errorStateHtml);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      // Show network error state
      const networkErrorHtml = `
        <div style="
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 60vh;
          text-align: center;
          padding: 20px;
        ">
          <div style="
            width: 80px;
            height: 80px;
            background: #fff3f3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(220,53,69,0.1);
          ">
            <i class="fas fa-wifi" style="
              font-size: 2.5rem;
              color: #dc3545;
            "></i>
          </div>
          <h4 style="
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 10px;
          ">Connection Error</h4>
          <p style="
            color: #6c757d;
            font-size: 1.1rem;
            max-width: 500px;
            line-height: 1.5;
          ">
            Unable to connect to the server. Please check your internet connection.
            <br>
            <span style="font-size: 0.9rem; color: #868e96; margin-top: 8px; display: block;">
              Try refreshing the page or contact IT support if the issue persists.
            </span>
          </p>
        </div>
      `;
      container.html(networkErrorHtml);
    },
  });
}

function approveLessonPlan(courseId) {
  Swal.fire({
    title: "Confirm Approval",
    text: "Are you sure you want to approve this lesson plan?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#2196f3",
    cancelButtonColor: "#dc3545",
    confirmButtonText: "Yes, approve it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "backend.php",
        type: "POST",
        dataType: "json",
        data: {
          action: "requestLessonPlanEdit",
          courseId: courseId,
          status: "Approved",
        },
        success: function (response) {
          if (response.status === "success") {
            // Remove the card with animation
            Swal.fire(
              "Approved!",
              "The lesson plan has been approved successfully.",
              "success"
            ).then(() => {
              requestLessonPlanEdit();
            });
          } else {
            Swal.fire(
              "Error",
              response.message || "Failed to approve lesson plan",
              "error"
            );
          }
        },
        error: function () {
          Swal.fire("Error", "Failed to approve lesson plan", "error");
        },
      });
    }
  });
}

function getTimeTableEditRequest() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getTimeTableEditRequest",
    },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        const data = response.data;
        const container = $("#time-table-edit-request-container");
        container.empty();

        if (data.length > 0) {
          toastr.warning(
            `You have ${data.length} pending timetable edit requests`,
            "Timetable Requests"
          );

          const style = `
            <style>
              .lesson-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                border: none;
                margin-bottom: 20px;
              }
              .lesson-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
              }
              .approve-btn {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                background: linear-gradient(45deg, #4CAF50, #45a049);
                border: none;
                color: white;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
              }
              .approve-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
              }
              .course-info span {
                display: block;
                margin: 8px 0;
                color: #666;
                font-size: 0.9rem;
              }
              .course-name {
                color: #2196F3;
                font-weight: 600;
                font-size: 1.2rem;
                margin-bottom: 12px;
              }
              .progress-line {
                height: 3px;
                background: linear-gradient(90deg, #2196F3, #64B5F6);
                margin-top: 15px;
                border-radius: 3px;
              }
            </style>
          `;
          container.append(style);

          const rowHtml = '<div class="row" style="margin-top: 20px;"></div>';
          container.append(rowHtml);
          const row = container.find(".row");

          data.forEach(function (item) {
            const cardHtml = `
              <div class="col-xl-4 col-sm-6 col-12" data-advisor-id="${item.advisor_id}" 
                   >
                <div class="card lesson-card">
                  <div class="card-content">
                    <div class="card-body" style="padding: 20px;">
                      <div class="media d-flex" style="position: relative;">
                        <div class="media-body course-info" style="padding-right: 60px;">
                          <h3 class="course-name">${item.faculty_name}</h3>
                          <span>
                            <i class="fas fa-graduation-cap" style="color: #2196F3; margin-right: 8px;"></i>
                            Batch: ${item.batch}
                          </span>
                          <span>
                            <i class="fas fa-layer-group" style="color: #4CAF50; margin-right: 8px;"></i>
                            Semester: ${item.semester}
                          </span>
                          <span>
                            <i class="fas fa-users" style="color: #FFC107; margin-right: 8px;"></i>
                            Section: ${item.section}
                          </span>
                          <span>
                            <i class="fas fa-calendar-alt" style="color: #FF5722; margin-right: 8px;"></i>
                            Academic Year: ${item.academic_year}
                          </span>
                          
                        </div>
                        <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%);">
                          <button onclick="approveTimeTableEdit('${item.advisor_id}')" 
                                  class="approve-btn">
                            <i class="fas fa-check" style="font-size: 1.2rem;"></i>
                          </button>
                        </div>
                      </div>
                      <div class="progress-line"></div>
                    </div>
                  </div>
                </div>
              </div>
            `;
            row.append(cardHtml);
          });
        } else {
          const emptyStateHtml = `
            <div style="
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              min-height: 60vh;
              text-align: center;
              padding: 20px;
            ">
              <div style="
                width: 80px;
                height: 80px;
                background: #f8f9fa;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
              ">
                <i class="fas fa-clipboard-check" style="
                  font-size: 2.5rem;
                  color: #6c757d;
                "></i>
              </div>
              <h4 style="
                color: #495057;
                font-weight: 600;
                margin-bottom: 10px;
              ">No Pending Requests</h4>
              <p style="
                color: #6c757d;
                font-size: 1.1rem;
                max-width: 500px;
                line-height: 1.5;
              ">
                All time table edit requests have been reviewed. You're up to date!
              </p>
            </div>
          `;
          container.html(emptyStateHtml);
        }
      } else {
        console.error("Error: " + response.message);
        const errorStateHtml = `
          <div style="
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            padding: 20px;
          ">
            <div style="
              width: 80px;
              height: 80px;
              background: #fff3f3;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              margin-bottom: 20px;
              box-shadow: 0 4px 15px rgba(220,53,69,0.1);
            ">
              <i class="fas fa-exclamation-triangle" style="
                font-size: 2.5rem;
                color: #dc3545;
              "></i>
            </div>
            <h4 style="
              color: #dc3545;
              font-weight: 600;
              margin-bottom: 10px;
            ">Unable to Load Time Table Requests</h4>
            <p style="
              color: #6c757d;
              font-size: 1.1rem;
              max-width: 500px;
              line-height: 1.5;
            ">
              There was an issue loading the time table requests. Please try refreshing the page.
              <br>
              <span style="font-size: 0.9rem; color: #868e96; margin-top: 8px; display: block;">
                If the problem persists, please contact technical support.
              </span>
            </p>
          </div>
        `;
        container.html(errorStateHtml);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      const networkErrorHtml = `
        <div style="
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 60vh;
          text-align: center;
          padding: 20px;
        ">
          <div style="
            width: 80px;
            height: 80px;
            background: #fff3f3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(220,53,69,0.1);
          ">
            <i class="fas fa-wifi" style="
              font-size: 2.5rem;
              color: #dc3545;
            "></i>
          </div>
          <h4 style="
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 10px;
          ">Connection Error</h4>
          <p style="
            color: #6c757d;
            font-size: 1.1rem;
            max-width: 500px;
            line-height: 1.5;
          ">
            Unable to connect to the server. Please check your internet connection.
            <br>
            <span style="font-size: 0.9rem; color: #868e96; margin-top: 8px; display: block;">
              Try refreshing the page or contact IT support if the issue persists.
            </span>
          </p>
        </div>
      `;
      container.html(networkErrorHtml);
    },
  });
}

function approveTimeTableEdit(id) {
  Swal.fire({
    title: "Confirm Approval",
    text: "Are you sure you want to approve this time table?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#2196f3",
    cancelButtonColor: "#dc3545",
    confirmButtonText: "Yes, approve it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "backend.php",
        type: "POST",
        dataType: "json",
        data: {
          action: "setTimeTableEditStatus",
          id: id,
          status: "Approved",
        },
        success: function (response) {
          if (response.status === "success") {
            Swal.fire(
              "Approved!",
              "The attendance has been unlocked successfully.",
              "success"
            ).then(() => {
              getTimeTableEditRequest();
            });
          } else {
            Swal.fire(
              "Error",
              response.message || "Failed to approve time table",
              "error"
            );
          }
        },
        error: function () {
          Swal.fire("Error", "Failed to approve time table", "error");
        },
      });
    }
  });
}

function getAttendanceLockedRequest() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAttendanceLockedRequest",
    },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        const data = response.data;
        const container = $("#attendance-locked-request-container");
        container.empty();

        if (data.length > 0) {
          toastr.error(
            `You have ${data.length} pending attendance lock requests`,
            "Attendance Requests"
          );

          const style = `
            <style>
              .lesson-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                border: none;
                margin-bottom: 20px;
              }
              .lesson-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
              }
              .approve-btn {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                background: linear-gradient(45deg, #4CAF50, #45a049);
                border: none;
                color: white;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
              }
              .approve-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 15px rgba(76, 175, 80, 0.4);
              }
              .course-info span {
                display: block;
                margin: 8px 0;
                color: #666;
                font-size: 0.9rem;
              }
              .course-name {
                color: #2196F3;
                font-weight: 600;
                font-size: 1.2rem;
                margin-bottom: 12px;
              }
              .progress-line {
                height: 3px;
                background: linear-gradient(90deg, #2196F3, #64B5F6);
                margin-top: 15px;
                border-radius: 3px;
              }
            </style>
          `;
          container.append(style);

          const rowHtml = '<div class="row" style="margin-top: 20px;"></div>';
          container.append(rowHtml);
          const row = container.find(".row");

          data.forEach(function (item) {
            const cardHtml = `
              <div class="col-xl-4 col-sm-6 col-12" data-session-id="${
                item.session_id
              }" 
                  >
                <div class="card lesson-card">
                  <div class="card-content">
                    <div class="card-body" style="padding: 20px;">
                      <div class="media d-flex" style="position: relative;">
                        <div class="media-body course-info" style="padding-right: 60px;">
                          <h3 class="course-name">${item.course_name}</h3>
                          <span>
                            <i class="fas fa-user-tie" style="color: #4CAF50; margin-right: 8px;"></i>
                            Faculty: ${item.faculty_name}
                          </span>
                          <span>
                            <i class="fas fa-graduation-cap" style="color: #2196F3; margin-right: 8px;"></i>
                            Batch: ${item.batch}
                          </span>
                          <span>
                            <i class="fas fa-layer-group" style="color: #FFC107; margin-right: 8px;"></i>
                            Semester: ${item.semester}
                          </span>
                          <span>
                            <i class="fas fa-users" style="color: #FF5722; margin-right: 8px;"></i>
                            Section: ${item.section}
                          </span>
                          <span>
                            <i class="fas fa-calendar-alt" style="color: #9C27B0; margin-right: 8px;"></i>
                            Class Date: ${new Date(
                              item.class_date
                            ).toLocaleDateString()}
                          </span>
                        </div>
                        <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%);">
                          <button onclick="approveAttendanceLock('${
                            item.session_id
                          }')" 
                                  class="approve-btn">
                            <i class="fas fa-check" style="font-size: 1.2rem;"></i>
                          </button>
                        </div>
                      </div>
                      <div class="progress-line"></div>
                    </div>
                  </div>
                </div>
              </div>
            `;
            row.append(cardHtml);
          });
        } else {
          const emptyStateHtml = `
            <div style="
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              min-height: 60vh;
              text-align: center;
              padding: 20px;
            ">
              <div style="
                width: 80px;
                height: 80px;
                background: #f8f9fa;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
              ">
                <i class="fas fa-clipboard-check" style="
                  font-size: 2.5rem;
                  color: #6c757d;
                "></i>
              </div>
              <h4 style="
                color: #495057;
                font-weight: 600;
                margin-bottom: 10px;
              ">No Pending Requests</h4>
              <p style="
                color: #6c757d;
                font-size: 1.1rem;
                max-width: 500px;
                line-height: 1.5;
              ">
                All attendance lock requests have been reviewed. You're up to date!
              </p>
            </div>
          `;
          container.html(emptyStateHtml);
        }
      }
    },
    error: function (xhr, status, error) {
      // ... existing error handling code ...
    },
  });
}
function approveAttendanceLock(sessionId) {
  Swal.fire({
    title: "Confirm Approval",
    text: "Are you sure you want to approve this attendance lock?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#2196f3",
    cancelButtonColor: "#dc3545",
    confirmButtonText: "Yes, approve it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "backend.php",
        type: "POST",
        dataType: "json",
        data: {
          action: "approveAttendanceLock",
          sessionId: sessionId,
        },
        success: function (response) {
          if (response.status === "success") {
            Swal.fire(
              "Approved!",
              "The attendance has been unlocked successfully.",
              "success"
            ).then(() => {
              getAttendanceLockedRequest();
            });
          } else {
            Swal.fire(
              "Error",
              response.message || "Failed to approve attendance lock",
              "error"
            );
          }
        },
        error: function () {
          Swal.fire("Error", "Failed to approve attendance lock", "error");
        },
      });
    }
  });
}
