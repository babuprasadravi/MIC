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
    <link rel="icon" type="image/png" sizes="32x32" href="images/icon/mkce_s.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-5/bootstrap-5.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css"
        rel="stylesheet">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script> -->
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


        /* Report page style starts */

        :root {
            --primary-gradient: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            --secondary-gradient: linear-gradient(135deg, #FF6B6B 0%, #FF000F 100%);
            --accent-color: #4A90E2;
        }



        .dashboard-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .forms-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .form-card {
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .form-card:hover {
            transform: translateY(-5px);
        }

        .validate-form {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
        }

        .report-form {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .form-card h2 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            text-align: center;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group label {
            display: block;
            color: white;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .validate-btn {
            background: white;
            color: #ff6b6b;
        }

        .generate-btn {
            background: white;
            color: #4facfe;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .report-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }

        .download-btn {
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .search-container input {
            padding: 0.8rem 1.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            width: 300px;
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            border-color: #4facfe;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.2);
        }


        #reportTable_wrapper {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
        }
/* 
        table.dataTable thead th {
            background: var(--primary-gradient) !important;
            color: white !important;
            font-weight: 600 !important;
            text-align: center !important;
            border: none !important;
            padding: 12px !important;
            font-size: 0.9rem !important;
        } */
        #reportTable thead tr {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        #reportTable th {
            background: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: white;
            text-align: center;
        }

        table.dataTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .dataTables_info,
        .dataTables_paginate {
            margin-top: 1rem;
        }

        .paginate_button {
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .paginate_button.current {
            background: #4facfe;
            color: white;
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
                    <li class="breadcrumb-item"><a href="dash.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leave Allocation</li>
                </ol>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="container-fluid">

      

            <div class="forms-container">
                <div class="form-card validate-form">
                    <h2>Validate Report</h2>
                    <form id="validateForm">
                        <div class="input-group">
                            <label for="validateMonth">Month:</label>
                            <input type="number" id="validateMonth" name="month" min="1" max="12" required>
                        </div>
                        <div class="input-group">
                            <label for="validateYear">Year:</label>
                            <input type="number" id="validateYear" name="year" min="2023" max="2030" required>
                        </div>
                        <button type="submit" class="btn validate-btn">Validate</button>
                    </form>
                </div>

                <div class="form-card report-form">
                    <h2>Generate Report</h2>
                    <form id="reportForm">
                        <div class="input-group">
                            <label for="reportMonth">Month:</label>
                            <input type="number" id="reportMonth" name="month" min="1" max="12" required>
                        </div>
                        <div class="input-group">
                            <label for="reportYear">Year:</label>
                            <input type="number" id="reportYear" name="year" min="2023" max="2030" required>
                        </div>
                        <button type="submit" class="btn generate-btn">Generate Report</button>
                    </form>
                </div>
            </div>

            <div class="report-actions">
                <button id="downloadBtn" class="download-btn">Download Report</button>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search by UID...">
                </div>
            </div>

            <div id="reportTableContainer">
                <table id="reportTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Total Days</th>
                            <th>Working Days</th>
                            <th>Holidays</th>
                            <th>Present</th>
                            <th>LOP</th>
                            <th>Salary Day</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>


        <!-- Footer -->
        <?php include 'footer.php'; ?>
    </div>

    <script>

        const loaderContainer = document.getElementById('loaderContainer');

        function showLoader() {
            const loaderContainer = document.getElementById('loaderContainer');
            if (loaderContainer) {
                loaderContainer.classList.remove('hide');
                loaderContainer.style.display = 'flex';
            }
        }
        function hideLoader() {

            const loaderContainer = document.getElementById('loaderContainer');
            if (loaderContainer) {
                loaderContainer.classList.add('hide');
                loaderContainer.style.display = 'none';
            }
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
            }

            loadingTimeout = setTimeout(showError, 10000);

            window.onload = function () {
                clearTimeout(loadingTimeout);

                setTimeout(hideLoader, 500);
            };

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
        $(document).ready(function () {
            let reportData = [];
            let dataTable = null;

            // Initialize DataTable
            function initializeDataTable(data) {
                if (dataTable) {
                    dataTable.destroy();
                }

                dataTable = $('#reportTable').DataTable({
                    data: data,
                    columns: [
                        { data: null, render: (data, type, row, meta) => meta.row + 1 },
                        { data: 'uid' },
                        { data: 'facultyName' },
                        { data: 'facultyRole' },
                        { data: 'totalDays' },
                        { data: 'totalWorkingDays' },
                        { data: 'totalHolidays' },
                        { data: 'totalPresentdays' },
                        { data: 'totalLopdays' },
                        { data: 'salaryDay' }
                    ],
                    responsive: true,
                    pageLength: 10,
                    order: [[0, 'asc']]
                });
            }

            // Validate Form Submit
            $('#validateForm').on('submit', function (e) {
                e.preventDefault();
                showLoader();

                const formData = new FormData(this);

                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'report_validation',
                        month: formData.get('month'),
                        year: formData.get('year')
                    },
                    success: function (response) {
                        hideLoader();
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === 200) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (error) {
                        hideLoader();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to validate report',
                            icon: 'error'
                        });
                    }
                });
            });

            // Generate Report Form Submit
            $('#reportForm').on('submit', function (e) {
                e.preventDefault();
                showLoader();

                const formData = new FormData(this);

                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'generate_report',
                        month: formData.get('month'),
                        year: formData.get('year')
                    },
                    success: function (response) {
                        hideLoader();
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 200) {
                            reportData = response.data;
                            initializeDataTable(reportData);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function (error) {
                        hideLoader();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to generate report',
                            icon: 'error'
                        });
                    }
                });
            });

            // Download Report
            $('#downloadBtn').on('click', function () {
                if (!reportData.length) {
                    Swal.fire({
                        title: 'No Data',
                        text: 'Please generate a report first',
                        icon: 'warning'
                    });
                    return;
                }

                const ws = XLSX.utils.json_to_sheet(
                    reportData.map(data => ({
                        UID: data.uid,
                        'Name': data.facultyName,
                        'Role': data.facultyRole,
                        'Total Days': data.totalDays,
                        'Total Working Days': data.totalWorkingDays,
                        'Total Holidays': data.totalHolidays,
                        'Total Present': data.totalPresentdays,
                        'LOP': data.totalLopdays,
                        'Salary Day': data.salaryDay,
                    }))
                );

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Report');
                XLSX.writeFile(wb, 'employee_report.xlsx');
            });

            // Search Functionality
            $('#searchInput').on('keyup', function () {
                dataTable.search(this.value).draw();
            });
        });
    </script>


</body>

</html>