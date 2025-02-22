$(document).ready(function () {
  getUniqueBatches();
  getFacultyMembers();
});
document.getElementById('batch').addEventListener('change', function() {
    const batch = this.value;
    const sectionSelect = document.getElementById('section');
    
    // Clear existing options
    sectionSelect.innerHTML = '<option value="">Select section</option>';
    
    if (batch) {
        $.ajax({
    url: 'backend.php',
    type: 'GET',
    data: { batch: batch, action: 'getSections' },
    dataType: 'json',
    success: function(data) {
        console.log('Received data:', data); // Debug log
        if (data.sections && Array.isArray(data.sections)) {
            data.sections.forEach(function(section) {
                const option = document.createElement('option');
                option.value = section;
                option.textContent = section;
                sectionSelect.appendChild(option);
            });
        }
    },
    error: function(xhr, status, error) {
        console.error('AJAX request failed:', status, error);
    }
});

    }
});
// Function to load departments from the faculty table in backend.php once the page is loaded
function getUniqueBatches() {
  $.ajax({
    url: "backend.php",
    type: "POST",
    data: {
      action: "getUniqueBatches",
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        if (data.status === "success") {
          const batchSelect = $("#batch");
          batchSelect.empty();
          batchSelect.append(
            '<option value="">Select Batch</option>'
          );

          data.batches.forEach((batch) => {
            batchSelect.append(`<option value="${batch}">${batch}</option>`);
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

// Function to get faculty members
function getFacultyMembers() {
    $.ajax({
        url: "backend.php",
        type: "GET",
        data: {
            action: "getFacultyMembers"
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === "success") {
                const facultySelect = $("#faculty");
                facultySelect.empty();
                facultySelect.append('<option value="">Select Faculty</option>');
                
                response.faculty.forEach((faculty) => {
                    facultySelect.append(
                        `<option value="${faculty.uid}">${faculty.name} - ${faculty.design}</option>`
                    );
                });
            } else {
                console.error("Error:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}

// Function to handle form submission
$('#advisorMappingForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        batch: $('#batch').val(),
        academic_year: $('#academic_year').val(),
        semester: $('#semester').val(),
        section: $('#section').val(),
        faculty_id: $('#faculty').val(),
        sem_start_date: $('#start_date').val(),
        sem_end_date: $('#end_date').val(),
        action: 'assignAdvisor'
    };

    $.ajax({
        url: 'backend.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: 'Advisor assigned successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to assign advisor. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
});
