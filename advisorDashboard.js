$(document).ready(function () {
  loadAdvisorData();
});

// Load past and current advisor data from backend.php
function loadAdvisorData() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getAdvisorData",
    },
    dataType: "json",
    success: function (data) {
      if (data.status === "success") {
        populateTable(data.records);
      } else {
        console.error("Error:", data.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      alert("Failed to load advisor data. Please try refreshing the page.");
    },
  });
}

// Function to populate the table with the advisor data once the data is fetched from backend.php
function populateTable(records) {
  const tbody = $(".table tbody");
  tbody.empty();

  records.forEach((record, index) => {
    tbody.append(`
            <tr>
                <td>${index + 1}.</td>
                <td>${record.department}</td>
                <td>${record.batch}</td>
                <td>${record.academic_year}</td>
                <td>${record.section}</td>
                <td>${record.semester}</td>
                <td>${record.start_date}</td>
                <td>${record.end_date}</td>
                <td>${record.advisor_name}</td>
                <td>
                    <button class="btn btn-sm btn-primary advisor-select" 
                        data-batch="${record.batch}"
                        data-academic-year="${record.academic_year}"
                        data-section="${record.section}"
                        data-semester="${record.semester}">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </td>
            </tr>
        `);
  });

  // Add click handler for advisor selection
  $(".advisor-select").on("click", function () {
    // Get the row data from the button for further processing
    const rowData = {
      batch: $(this).data("batch"),
      academicYear: $(this).data("academic-year"),
      section: $(this).data("section"),
      department: $(this).closest("tr").find("td:nth-child(2)").text(),
      semester: $(this).data("semester"),
    };

    // Store the row data in sessionStorage for further processing
    sessionStorage.setItem("advisorData", JSON.stringify(rowData));

    // Send row data to the server to store in session
    $.ajax({
      url: "backend.php",
      type: "POST",
      data: {
        action: "storeAdvisorData",
        advisorData: rowData,
      },
      success: function (response) {
        console.log("Session data stored successfully:", response);
      },
      error: function (xhr, status, error) {
        console.error("Error storing session data:", error);
      },
    });

    // Redirect to advisor page
    window.location.href = "advisor.php";
  });
}
