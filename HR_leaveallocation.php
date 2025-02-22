<?php
require 'config.php';
require 'session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIC HR</title>
    <!-- Remove old DataTables -->
    <!-- Add Simple DataTables CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>

    <!-- Keep other resources as is -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIC HR</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-5/bootstrap-5.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 60px;
            --footer-height: 60px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --dark-bg: #1a1c23;
            --light-bg: #f8f9fc;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* General Styles with Enhanced Typography */
        body {
            min-height: 100vh;
            margin: 0 20px;
            background: var(--light-bg);
            overflow-x: hidden;
            padding-bottom: var(--footer-height);
            position: relative;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Content Area Styles */
        .content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* Content Navigation */
        .content-nav {
            background: linear-gradient(45deg, #4e73df, #1cc88a);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .content-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 20px;
            overflow-x: auto;
        }

        .content-nav li a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .content-nav li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar.collapsed+.content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .breadcrumb-area {
            background-image: linear-gradient(to top, #fff1eb 0%, #ace0f9 100%);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin: 20px;
            padding: 15px 20px;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: #224abe;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }

            .sidebar.mobile-show {
                transform: translateX(0);
            }

            .topbar {
                left: 0 !important;
            }

            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .mobile-overlay.show {
                display: block;
            }

            .content {
                margin-left: 0 !important;
            }

            .brand-logo {
                display: block;
            }

            .user-profile {
                margin-left: 0;
            }

            .sidebar .logo {
                justify-content: center;
            }

            .sidebar .menu-item span,
            .sidebar .has-submenu::after {
                display: block !important;
            }

            body.sidebar-open {
                overflow: hidden;
            }

            .footer {
                left: 0 !important;
            }

            .content-nav ul {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 5px;
            }

            .content-nav ul::-webkit-scrollbar {
                height: 4px;
            }

            .content-nav ul::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 2px;
            }
        }

        .container-fluid {
            padding: 10px;
        }


        /* loader */
        .loader-container {
            position: fixed;
            left: var(--sidebar-width);
            right: 0;
            top: var(--topbar-height);
            bottom: var(--footer-height);
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            /* Changed from 'none' to show by default */
            justify-content: center;
            align-items: center;
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .sidebar.collapsed+.content .loader-container {
            left: var(--sidebar-collapsed-width);
        }

        @media (max-width: 768px) {
            .loader-container {
                left: 0;
            }
        }

        /* Hide loader when done */
        .loader-container.hide {
            display: none;
        }

        /* Loader Animation */
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid var(--primary-color);
            border-right: 5px solid var(--success-color);
            border-bottom: 5px solid var(--primary-color);
            border-left: 5px solid var(--success-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Hide content initially */
        .content-wrapper {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Show content when loaded */
        .content-wrapper.show {
            opacity: 1;
        }



        /* leaveallocation style starts */

        .btn-toggle {
            padding: 10px 20px;
            font-size: 16px;
        }

        .btn-toggle.active {
            background-color: #4CAF50;
            color: white;
            border: none;
        }

        .btn-toggle.inactive {
            background-color: #e9ecef;
            color: black;
            border: none;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }

        .table-container {
            margin-top: 20px;
        }

        /* Table Styles */
        .table {
            margin: 0;
        }

        .table thead tr {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .table thead th {
            background: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: white;
            text-align: center;
        }

        .table tbody td {
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(42, 82, 152, 0.05);
        }

        /* Add these styles to your existing CSS */
        .dataTable-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .dataTable-container {
            overflow-x: auto;
        }

        .dataTable-table {
            width: 100%;
            max-width: 100%;
            border-spacing: 0;
        }

        .dataTable-table>thead>tr>th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .dataTable-pagination {
            margin-top: 1rem;
        }

        .dataTable-pagination a {
            padding: 0.375rem 0.75rem;
        }

        .dataTable-search {
            margin-bottom: 1rem;
        }

        .dataTable-input {
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .dataTable-selector {
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .btn-toggle.inactive {
            background-color: #e9ecef;
            color: black;
            border: none;
        }

        .btn-toggle.active {
            background-color: green !important;
            color: white !important;
            border: none !important;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'hrsidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">

        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
        </div>

        <!-- Topbar -->
        <?php include 'ftopbar.php'; ?>

        <!-- Breadcrumb -->
        <div class="breadcrumb-area">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leave Allocation</li>
                </ol>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="container-fluid">
            <div class="d-flex justify-content-center mb-4">
                <button class="btn btn-toggle active me-2" data-option="option1">
                    Enable Leave for All Employees
                </button>
                <button class="btn btn-toggle inactive" data-option="option2">
                    Enable Leave for Specific Employee
                </button>
            </div>
            <!-- All Employees Form -->
            <div class="form-container" id="option1Content">
                <form id="allEmployeesForm">
                    <div class="mb-4">
                        <label class="form-label">Leave Type:</label>
                        <select class="form-select" id="leaveType1" name="ltype" required>
                            <option value="">Select Leave Type</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Number of Days:</label>
                        <input type="number" class="form-control" id="numDays1" name="tdays" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success">
                            Assign to All Employees
                        </button>
                    </div>
                </form>
            </div>

            <!-- Specific Employee Form -->
            <div class="form-container" id="option2Content" style="display: none;">
                <form id="specificEmployeeForm">
                    <div class="mb-4">
                        <label class="form-label">Leave Type:</label>
                        <select class="form-select" id="leaveType2" name="ltype" required>
                            <option value="">Select Leave Type</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Employee ID:</label>
                        <input type="text" class="form-control" id="employeeID" name="empid" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Number of Days:</label>
                        <input type="number" class="form-control" id="numDays2" name="tDays2" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success">
                            Assign to Employee
                        </button>
                    </div>
                </form>
            </div>


            <!-- Leave Balance Table -->
            <div class="table-container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Leave Balances</h5>
                        <button id="resetButton" class="btn btn-danger">
                            <i class="fas fa-redo"></i> Reset All
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="leaveTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>CL</th>
                                        <th>COL</th>
                                        <th>ODB</th>
                                        <th>ODR</th>
                                        <th>ODP</th>
                                        <th>ODO</th>
                                        <th>VL</th>
                                        <th>ML</th>
                                        <th>MAL</th>
                                        <th>MTL</th>
                                        <th>PTL</th>
                                        <th>SL</th>
                                        <th>SPL</th>
                                        <th>PER</th>
                                        <th>PER 2</th>
                                    </tr>
                                </thead>
                                <tbody id="leaveTableBody">
                                    <!-- Table data will be dynamically populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>


        <!-- Footer -->
        <?php include 'footer.php'; ?>
    </div>

    <script>

        const loaderContainer = document.getElementById('loaderContainer');

        function showLoader() {
            loaderContainer.classList.add('show');
        }

        function hideLoader() {
            loaderContainer.classList.remove('show');
        }

        //    automatic loader
        document.addEventListener('DOMContentLoaded', function () {
            const loaderContainer = document.getElementById('loaderContainer');
            const contentWrapper = document.getElementById('contentWrapper');
            let loadingTimeout;

            function hideLoader() {
                loaderContainer.classList.add('hide');
                contentWrapper.classList.add('show');
            }

            function showError() {
                console.error('Page load took too long or encountered an error');
                // You can add custom error handling here
            }

            // Set a maximum loading time (10 seconds)
            loadingTimeout = setTimeout(showError, 10000);

            // Hide loader when everything is loaded
            window.onload = function () {
                clearTimeout(loadingTimeout);

                // Add a small delay to ensure smooth transition
                setTimeout(hideLoader, 500);
            };

            // Error handling
            window.onerror = function (msg, url, lineNo, columnNo, error) {
                clearTimeout(loadingTimeout);
                showError();
                return false;
            };
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Cache DOM elements
            const elements = {
                hamburger: document.getElementById('hamburger'),
                sidebar: document.getElementById('sidebar'),
                mobileOverlay: document.getElementById('mobileOverlay'),
                menuItems: document.querySelectorAll('.menu-item'),
                submenuItems: document.querySelectorAll('.submenu-item') // Add submenu items to cache
            };

            // Set active menu item based on current path
            function setActiveMenuItem() {
                const currentPath = window.location.pathname.split('/').pop();

                // Clear all active states first
                elements.menuItems.forEach(item => item.classList.remove('active'));
                elements.submenuItems.forEach(item => item.classList.remove('active'));

                // Check main menu items
                elements.menuItems.forEach(item => {
                    const itemPath = item.getAttribute('href')?.replace('/', '');
                    if (itemPath === currentPath) {
                        item.classList.add('active');
                        // If this item has a parent submenu, activate it too
                        const parentSubmenu = item.closest('.submenu');
                        const parentMenuItem = parentSubmenu?.previousElementSibling;
                        if (parentSubmenu && parentMenuItem) {
                            parentSubmenu.classList.add('active');
                            parentMenuItem.classList.add('active');
                        }
                    }
                });

                // Check submenu items
                elements.submenuItems.forEach(item => {
                    const itemPath = item.getAttribute('href')?.replace('/', '');
                    if (itemPath === currentPath) {
                        item.classList.add('active');
                        // Activate parent submenu and its trigger
                        const parentSubmenu = item.closest('.submenu');
                        const parentMenuItem = parentSubmenu?.previousElementSibling;
                        if (parentSubmenu && parentMenuItem) {
                            parentSubmenu.classList.add('active');
                            parentMenuItem.classList.add('active');
                        }
                    }
                });
            }

            // Handle mobile sidebar toggle
            function handleSidebarToggle() {
                if (window.innerWidth <= 768) {
                    elements.sidebar.classList.toggle('mobile-show');
                    elements.mobileOverlay.classList.toggle('show');
                    document.body.classList.toggle('sidebar-open');
                } else {
                    elements.sidebar.classList.toggle('collapsed');
                }
            }

            // Handle window resize
            function handleResize() {
                if (window.innerWidth <= 768) {
                    elements.sidebar.classList.remove('collapsed');
                    elements.sidebar.classList.remove('mobile-show');
                    elements.mobileOverlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                } else {
                    elements.sidebar.style.transform = '';
                    elements.mobileOverlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                }
            }

            // Toggle User Menu
            const userMenu = document.getElementById('userMenu');
            const dropdownMenu = userMenu.querySelector('.dropdown-menu');
            userMenu.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', () => {
                dropdownMenu.classList.remove('show');
            });

            // Enhanced Toggle Submenu with active state handling
            const menuItems = document.querySelectorAll('.has-submenu');
            menuItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent default if it's a link
                    const submenu = item.nextElementSibling;

                    // Toggle active state for the clicked menu item and its submenu
                    item.classList.toggle('active');
                    submenu.classList.toggle('active');

                    // Handle submenu item clicks
                    const submenuItems = submenu.querySelectorAll('.submenu-item');
                    submenuItems.forEach(submenuItem => {
                        submenuItem.addEventListener('click', (e) => {
                            // Remove active class from all submenu items
                            submenuItems.forEach(si => si.classList.remove('active'));
                            // Add active class to clicked submenu item
                            submenuItem.classList.add('active');
                            e.stopPropagation(); // Prevent event from bubbling up
                        });
                    });
                });
            });

            // Initialize event listeners
            function initializeEventListeners() {
                // Sidebar toggle for mobile and desktop
                if (elements.hamburger && elements.mobileOverlay) {
                    elements.hamburger.addEventListener('click', handleSidebarToggle);
                    elements.mobileOverlay.addEventListener('click', handleSidebarToggle);
                }
                // Window resize handler
                window.addEventListener('resize', handleResize);
            }

            // Initialize everything
            setActiveMenuItem();
            initializeEventListeners();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTable variable in the global scope
            let dataTable;

            // Leave Types Array
            const LEAVE_TYPES = [
                "CL", "COL", "ODB", "ODR", "ODP", "ODO", "VL",
                "ML", "MAL", "MTL", "PTL", "SL", "SPL", "PM", "TENPM"
            ];

            // Populate Leave Type Dropdowns
            function populateLeaveTypes() {
                LEAVE_TYPES.forEach(type => {
                    $('#leaveType1, #leaveType2').append(
                        $('<option>', { value: type, text: type })
                    );
                });
            }

            // Function to update the leave table
            function updateLeaveTable(data) {
                // If DataTable exists, destroy it
                if (dataTable) {
                    dataTable.destroy();
                }

                const tbody = $('#leaveTableBody');
                tbody.empty();

                // Populate the table body with new data
                data.forEach((item, index) => {
                    tbody.append(`
            <tr>
                <td>${index + 1}</td>
                <td>${item.id}</td>
                <td>${item.name}</td>
                <td>${item.cl}</td>
                <td>${item.col}</td>
                <td>${item.odb}</td>
                <td>${item.odr}</td>
                <td>${item.odp}</td>
                <td>${item.odo}</td>
                <td>${item.vl}</td>
                <td>${item.ml}</td>
                <td>${item.mal}</td>
                <td>${item.mtl}</td>
                <td>${item.ptl}</td>
                <td>${item.sl}</td>
                <td>${item.spl}</td>
                <td>${item.pm}</td>
                <td>${item.tenpm}</td>
            </tr>
        `);
                });

                // Initialize new DataTable
                dataTable = new simpleDatatables.DataTable("#leaveTable", {
                    searchable: true,
                    fixedHeight: true,
                    perPage: 10,
                    perPageSelect: [10, 25, 50, 100],
                    columns: [
                        { select: [0], sortable: true },
                        { select: [1], sortable: true },
                        { select: [2], sortable: true },
                        { select: [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17], sortable: true }
                    ]
                });
            }

            // Function to fetch leave data
            function fetchLeaveData() {
                showLoader();
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'GET',
                    data: { action: 'get_leave_balance_details' },
                    success: function (response) {
                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            if (response.status === 200) {
                                updateLeaveTable(response.data);
                            } else {
                                showNotification('Error fetching data', 'error');
                            }
                        } catch (error) {
                            console.error('Error processing response:', error);
                            showNotification('Error processing data', 'error');
                        }
                        hideLoader();
                    },
                    error: function (xhr, status, error) {
                        console.error('Ajax error:', error);
                        showNotification('Server error occurred', 'error');
                        hideLoader();
                    }
                });
            }

            // Function to show notifications
            function showNotification(message, type) {
                Swal.fire({
                    icon: type === 'success' ? 'success' : 'error',
                    title: type === 'success' ? 'Success' : 'Error',
                    text: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            // Handle form toggle buttons
            $('.btn-toggle').click(function () {
                $('.btn-toggle').removeClass('active').addClass('inactive');
                $(this).removeClass('inactive').addClass('active');
                const option = $(this).data('option');
                $('.form-container').hide();
                $(`#${option}Content`).show();
            });

            // Handle All Employees Form Submission
            $('#allEmployeesForm').submit(function (e) {
                e.preventDefault();
                showLoader();
                const formData = new FormData(this);
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'assign_all_leave_details',
                        ltype: formData.get('ltype'),
                        days: formData.get('tdays')
                    },
                    success: function (response) {
                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            if (response.status === 200) {
                                showNotification(response.message, 'success');
                                $('#allEmployeesForm')[0].reset();
                                fetchLeaveData();
                            } else {
                                showNotification(response.message || 'Operation failed', 'error');
                            }
                        } catch (error) {
                            console.error('Error processing response:', error);
                            showNotification('Error processing response', 'error');
                        }
                        hideLoader();
                    },
                    error: function (xhr, status, error) {
                        console.error('Ajax error:', error);
                        showNotification('Server error occurred', 'error');
                        hideLoader();
                    }
                });
            });

            // Handle Specific Employee Form Submission
            $('#specificEmployeeForm').submit(function (e) {
                e.preventDefault();
                showLoader();
                const formData = new FormData(this);
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'assign_spec_leave_details',
                        ltype: formData.get('ltype'),
                        uid: formData.get('empid'),
                        days: formData.get('tDays2')
                    },
                    success: function (response) {
                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            if (response.status === 200) {
                                showNotification(response.message, 'success');
                                $('#specificEmployeeForm')[0].reset();
                                fetchLeaveData();
                            } else {
                                showNotification(response.message || 'Operation failed', 'error');
                            }
                        } catch (error) {
                            console.error('Error processing response:', error);
                            showNotification('Error processing response', 'error');
                        }
                        hideLoader();
                    },
                    error: function (xhr, status, error) {
                        console.error('Ajax error:', error);
                        showNotification('Server error occurred', 'error');
                        hideLoader();
                    }
                });
            });

            // Handle Reset Button
            $('#resetButton').click(function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will reset leave data for all employees!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reset it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoader();
                        $.ajax({
                            url: 'hradmin_back.php',
                            method: 'POST',
                            data: { action: 'reset_leave_details' },
                            success: function (response) {
                                try {
                                    if (typeof response === 'string') {
                                        response = JSON.parse(response);
                                    }
                                    if (response.status === 200) {
                                        showNotification(response.message, 'success');
                                        fetchLeaveData();
                                    } else {
                                        showNotification(response.message || 'Reset failed', 'error');
                                    }
                                } catch (error) {
                                    console.error('Error processing response:', error);
                                    showNotification('Error processing response', 'error');
                                }
                                hideLoader();
                            },
                            error: function (xhr, status, error) {
                                console.error('Ajax error:', error);
                                showNotification('Server error occurred', 'error');
                                hideLoader();
                            }
                        });
                    }
                });
            });

            // Initialize the page
            populateLeaveTypes();
            fetchLeaveData();
        });



    </script>
</body>

</html>