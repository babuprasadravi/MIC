$(document).ready(function() {
    // Get student data from sessionStorage
    const studentData = JSON.parse(sessionStorage.getItem('userData'));
    const regno = studentData.id;
    console.log(regno);
    $('input[name="student_id"]').val(regno);
    $('#leaveHistoryTable').DataTable({
        ajax: {
            url: 'backend.php',
            type: 'POST',
            data: {
                action: 'getStudentLeaveHistory',
                studentId: studentData.id,
                batch:studentData.year
            },
            dataSrc: 'leaveHistory'  // Changed from 'data' to 'leaveHistory' to match response
        },
        columns: [
            { 
                data: 'leave_type',
                render: function(data) {
                    const badgeClass = data === 'OD' ? 'bg-info' : 'bg-warning text-dark';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'start_date',  // Changed from from_date to start_date
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'end_date',    // Changed from to_date to end_date
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    let badgeClass = '';
                    switch(data.toLowerCase()) {
                        case 'pending':
                            badgeClass = 'bg-warning text-dark';
                            break;
                        case 'approved':
                            badgeClass = 'bg-success';
                            break;
                        case 'rejected':
                            badgeClass = 'bg-danger';
                            break;
                    }
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    if(data.status.toLowerCase() === 'pending') {
                        return `
                            <button class="btn btn-danger btn-sm" onclick="deleteLeaveRequest(${data.leave_id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>`;
                    }
                    return '';
                }
            }
        ],
        order: [[1, 'desc']], // Sort by start_date by default
        pageLength: 10,
        responsive: true
    });

    // Handle form submission
    $('#leaveForm').on('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = {
            action: 'submitLeaveRequest',
            student_id: regno,
            leave_type: $('select[name="leave_type"]').val(),
            reason: $('input[name="reason"]').val(),
            start_date: $('input[name="start_date"]').val(),
            end_date: $('input[name="end_date"]').val(),
            periods: $('select[name="periods"]').val()
        };

        // Validate dates
        const startDate = new Date(formData.start_date);
        const endDate = new Date(formData.end_date);
        
        if (endDate < startDate) {
            Swal.fire({
                title: 'Error',
                text: 'End date cannot be earlier than start date',
                icon: 'error'
            });
            return;
        }

        // Send request to backend
        $.ajax({
            url: 'backend.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        Swal.fire({
                            title: 'Success',
                            text: 'Leave request submitted successfully',
                            icon: 'success'
                        }).then(() => {
                            // Reload the leave history table
                            loadLeaveHistory();
                            // Reset the form
                            $('#leaveForm')[0].reset();
                            // Re-fill student ID
                            if (studentData) {
                                $('input[name="student_id"]').val(studentData.rollNo);
                            }
                        });
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Failed to submit leave request',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to connect to server',
                    icon: 'error'
                });
            }
        });
    });

    // Function to load leave history
    function loadLeaveHistory() {
        const studentId = $('input[name="student_id"]').val();
        
        $.ajax({
            url: 'backend.php',
            type: 'POST',
            data: {
                action: 'getStudentLeaveHistory',
                studentId: studentId,
                batch:studentData.year

            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        updateLeaveHistoryTable(result.data);
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Failed to load leave history:', error);
                }
            }
        });
    }

    // delete to leave history
    function deleteLeaveRequest(leaveId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this leave request?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'backend.php',
                    type: 'POST',
                    data: {
                        action: 'deleteLeaveRequest',
                        leaveId: leaveId
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if(data.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    'Your leave request has been deleted.',
                                    'success'
                                );
                                // Refresh the table
                                $('#leaveHistoryTable').DataTable().ajax.reload();
                            } else {
                                throw new Error(data.message || 'Failed to delete leave request');
                            }
                        } catch(error) {
                            Swal.fire(
                                'Error!',
                                error.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Failed to connect to server',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // Function to update leave history table
    function updateLeaveHistoryTable(leaveHistory) {
        const tbody = $('.table tbody');
        tbody.empty();

        leaveHistory.forEach((leave, index) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${leave.start_date} to ${leave.end_date}</td>
                    <td>${leave.periods}</td>
                    <td>${leave.leave_type}</td>
                    <td>
                        <span class="badge bg-${getStatusBadgeClass(leave.status)}">
                            ${leave.status}
                        </span>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Helper function to get badge class based on status
    function getStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'approved':
                return 'success';
            case 'pending':
                return 'warning';
            case 'rejected':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    // Load leave history when page loads
    loadLeaveHistory();
});
