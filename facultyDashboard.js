$(document).ready(function () {
  populateFacultyAcademics();
});

function populateFacultyAcademics() {
  $.ajax({
    url: "backend.php",
    type: "GET",
    data: {
      action: "getFacultyAcademics",
    },
    success: function (response) {
      console.log("Response DEMOOOO: ", response); // For debugging
      try {
        console.log("Response:", response); // For debugging
        const data = JSON.parse(response);
        if (data.status === "success") {
          populateTable(data.academics);
        } else {
          console.error("Error:", data.message);
        }
      } catch (e) {
        console.error("Error parsing response:", e);
        console.log("Raw responses:", response);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      console.log("XHR:", xhr.responseText);
    },
  });
}

function populateTable(academics) {
  const tbody = $(".table tbody");
  tbody.empty();

  if (!academics || academics.length === 0) {
    tbody.append(`
      <tr>
        <td colspan="4" class="text-center">No academic records found</td>
      </tr>
    `);
    return;
  }

  // Group the records by academic year and separate even and odd semesters
  const groupedRecords = {};

  academics.forEach((record) => {
    const year = record.academic_year;
    if (!groupedRecords[year]) {
      groupedRecords[year] = {
        odd: null,
        even: null,
      };
    }
    if (record.semester % 2 === 0) {
      groupedRecords[year].even = record;
    } else {
      groupedRecords[year].odd = record;
    }
  });

  // Sort academic years in descending order
  const sortedYears = Object.keys(groupedRecords).sort((a, b) =>
    b.localeCompare(a)
  );
  let index = 1;

  sortedYears.forEach((year) => {
    const group = groupedRecords[year];

    // Append row for EVEN semester first if present
    if (group.even) {
      tbody.append(`
        <tr>
          <td>${index++}</td>
          <td>${year}</td>
          <td>Even</td>
          <td>
            <button class="btn btn-primary btn-sm view-courses"
              data-batch="${group.even.batch}"
              data-academic-year="${year}"
              data-semester="${group.even.semester}"
              data-faculty-id="${group.even.faculty_id}">
              <i class="fas fa-eye"></i> View Courses
            </button>
          </td>
        </tr>
      `);
    }

    // Append row for ODD semester next if present
    if (group.odd) {
      tbody.append(`
        <tr>
          <td>${index++}</td>
          <td>${year}</td>
          <td>Odd</td>
          <td>
            <button class="btn btn-primary btn-sm view-courses"
              data-batch="${group.odd.batch}"
              data-academic-year="${year}"
              data-semester="${group.odd.semester}"
              data-faculty-id="${group.odd.faculty_id}">
              <i class="fas fa-eye"></i> View Courses
            </button>
          </td>
        </tr>
      `);
    }
  });

  // Add click handler for viewing courses
  $(".view-courses").on("click", function () {
    const semester = $(this).data("semester");
    // Determine semester type based on whether the semester number is even or odd
    const semesterType = semester % 2 === 0 ? "Even" : "Odd";

    const rowData = {
      batch: $(this).data("batch"),
      academicYear: $(this).data("academic-year"),
      semesterType: semesterType,
      facultyId: $(this).data("faculty-id"),
    };

    // Store in browser's sessionStorage
    sessionStorage.setItem("selectedFacultyData", JSON.stringify(rowData));

    // Store in PHP session
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "storeFacultyData",
        facultyData: rowData,
      },
      success: function (response) {
        window.location.href = "faculty.php";
      },
    });
  });
}
