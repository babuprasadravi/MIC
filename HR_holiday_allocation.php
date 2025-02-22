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
    <link rel="icon" type="image/png" sizes="32x32" href="../../image/icons/mkce_s.png">
  <!-- Core Dependencies -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"/>

<!-- SweetAlert and Alertify -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-5/bootstrap-5.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />

<!-- JavaScript Dependencies (in order) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<!-- DataTables JS (in order) -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
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
            --primary-gradient: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            --secondary-gradient: linear-gradient(135deg, #FF6B6B 0%, #FF000F 100%);
            --accent-color: #4A90E2;
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


        /* Holiday allocation page styles starts */

        /* Button Styles */
        .action-buttons {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn-custom-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        .btn-custom-primary {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        .btn-custom-success:hover,
        .btn-custom-primary:hover {
            transform: translateY(-2px);
            color: white;
        }

        /* Calendar Styles */
        .calendar-container {
            max-width: 1600px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .calendar {
            background: white;
            border-radius: 8px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 8px 12px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 8px;
            color: white;
        }

        .calendar-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .calendar-header button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .calendar-header button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day {
            padding: 8px;
            text-align: center;
            border-radius: 6px;
            border: 1px solid #eee;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .day-header {
            font-weight: bold;
            background: #f8f9fa;
            color: #495057;
        }

        .calendar-day:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .holiday {
            background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
            color: white;
            border: none;
        }

        .holiday-text {
            font-size: 0.65rem;
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
        }

        .modal-content {
            background-color: white;
            width: 320px;
            margin: 50px auto;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            color: white;
            padding: 12px 15px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        #leaveForm,
        #longLeaveForm {
            padding: 15px;
        }

        .modal label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #495057;
            margin-bottom: 4px;
        }

        .modal select,
        .modal input {
            font-size: 0.9rem;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            width: 100%;
        }

        .modal select:focus,
        .modal input:focus {
            border-color: #6B73FF;
            box-shadow: 0 0 0 2px rgba(107, 115, 255, 0.1);
            outline: none;
        }

        .modal-footer {
            padding: 10px 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .modal-footer button {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        #selectedDate,
        #selectedDay {
            font-size: 0.9rem;
            color: #495057;
            margin: 0 0 8px 0;
        }

        /* Card and Table Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 25px;
            padding: 15px;
        }

        .card h4 {
            color: #2C3E50;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .search-container {
            margin: 15px 0;
            position: relative;
        }

        .search-input {
            width: 100%;
            max-width: 300px;
            padding: 8px 12px;
            font-size: 0.9rem;
            border: 2px solid #eee;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
            outline: none;
        }

        .datatable-custom thead tr {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .datatable-custom th {
            background: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: white;
            text-align: center !important;
        }

        .datatable-custom td {
            font-size: 0.85rem;
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        .btn-danger {
            background: var(--secondary-gradient);
            border: none;
            padding: 4px 12px;
            font-size: 0.8rem;
        }

        /* Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #357abd;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .calendar-container {
                margin: 10px;
                padding: 10px;
            }

            .calendar-day {
                padding: 4px;
                font-size: 0.8rem;
            }

            .btn-custom-success,
            .btn-custom-primary {
                width: 100%;
                margin-bottom: 8px;
            }

            .modal-content {
                width: 90%;
                margin: 30px auto;
            }
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
                    <li class="breadcrumb-item active" aria-current="page">Holidays Allocation</li>
                </ol>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="container-fluid">

            <div class="action-buttons">
                <button class="btn btn-custom-success mx-3" id="assignSundaysBtn">
                    <i class="fas fa-calendar-check mr-2"></i> Assign All Sundays Leave
                </button>
                <button class="btn btn-custom-primary" id="assignLongLeaveBtn">
                    <i class="fas fa-calendar-plus mr-2"></i> Assign Long Leaves
                </button>
            </div>

            <div class="calendar-container">
                <div class="calendar">
                    <div class="calendar-header">
                        <button class="prev-month"><i class="fas fa-chevron-left"></i></button>
                        <h3 id="currentMonth"></h3>
                        <button class="next-month"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
            </div>

            <!-- Leave Modal -->
            <div id="leaveModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-calendar-alt mr-2"></i>Set Leave</h5>
                    </div>
                    <form id="leaveForm" class="p-4">
                        <p id="selectedDate" class="mb-2"></p>
                        <p id="selectedDay" class="mb-4"></p>

                        <div class="mb-3">
                            <label for="leaveType" class="form-label">Choose Leave Type:</label>
                            <select id="leaveType" class="form-control" required>
                                <option value="" disabled selected>Select a leave type</option>
                                <option value="Week-Off">Week-Off</option>
                                <option value="TN Govt Holidays">TN Govt Holidays</option>
                                <option value="Special Leave">Special Leave</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="leaveFor" class="form-label">Leave For:</label>
                            <select id="leaveFor" class="form-control" required>
                                <option value="" disabled selected>Choose leave for</option>
                                <option value="ALL">ALL</option>
                                <option value="Teaching Faculty">Teaching Faculty</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-custom-primary">
                                <i class="fas fa-check mr-2"></i>Submit
                            </button>
                            <button type="button" class="btn btn-secondary close-modal">
                                <i class="fas fa-times mr-2"></i>Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Long Leave Modal -->
            <div id="longLeaveModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-calendar-week mr-2"></i>Assign Long Leave</h5>
                    </div>
                    <form id="longLeaveForm" class="p-4">
                        <div class="mb-3">
                            <label for="longLeaveType" class="form-label">Choose Leave Type:</label>
                            <select name="ltype" class="form-control" required>
                                <option value="" disabled selected>Select a leave type</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                                <option value="TN Govt Holidays">TN Govt Holidays</option>
                                <option value="Special Leave">Special Leave</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">From Date:</label>
                            <input type="date" name="fDate" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To Date:</label>
                            <input type="date" name="tDate" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="longLeaveFor" class="form-label">Leave For:</label>
                            <select name="leaveFor" class="form-control" required>
                                <option value="" disabled selected>Choose leave for</option>
                                <option value="ALL">ALL</option>
                                <option value="Teaching Faculty">Teaching Faculty</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-custom-primary">
                                <i class="fas fa-check mr-2"></i>Submit
                            </button>
                            <button type="button" class="btn btn-secondary close-modal">
                                <i class="fas fa-times mr-2"></i>Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <h4><i class="fas fa-list-alt mr-2"></i>Leave Details</h4>

                <table id="leaveTable" class="table table-striped datatable-custom">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Type</th>
                            <th>Leave For</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
        $(document).ready(function () {
            let currentDate = new Date();
            let holidays = [];
            let dataTable;

            // Initialize DataTable
            dataTable = $('#leaveTable').DataTable({
                columns: [
                    { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
                    { data: 'hdate' },
                    { data: 'days' },
                    { data: 'type' },
                    { data: 'who' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<button class="btn btn-sm btn-danger delete-leave" data-id="${row.id}" data-date="${row.hdate}">Delete</button>`;
                        }
                    }
                ],
                pageLength: 10,
                responsive: true
            });

            // Initialize alertify
            alertify.set('notifier', 'position', 'top-right');

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

            // Fetch initial data
            fetchHolidays();
            fetchLeaveData();

            // Calendar navigation
            $('.prev-month').click(function () {
                currentDate.setMonth(currentDate.getMonth() - 1);
                updateCalendar();
            });

            $('.next-month').click(function () {
                currentDate.setMonth(currentDate.getMonth() + 1);
                updateCalendar();
            });

            // Assign all Sundays
            $('#assignSundaysBtn').click(function () {
                const sundayDates = getSundayDatesForMonth(
                    currentDate.getFullYear(),
                    currentDate.getMonth()
                );

                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: { action: 'assign_sunday_leave', dates: sundayDates },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 200) {
                            // alertify.success(response.message);
                            showNotification(response.message, 'success');
                            fetchHolidays();
                            fetchLeaveData();
                        } else {
                            showNotification(response.message, 'error');
                            // alertify.error(response.message);
                        }
                    },
                    error: function () {
                        showNotification('Error storing Sunday dates', 'error');
                        //alertify.error('Error storing Sunday dates');
                    }
                });
            });

            // Handle leave form submission
            $('#leaveForm').submit(function (e) {
                e.preventDefault();
                const formData = {
                    sdate: $('#selectedDate').data('date'),
                    leaveType: $('#leaveType').val(),
                    leaveFor: $('#leaveFor').val(),
                    dayOfWeek: $('#selectedDay').text().split(': ')[1]
                };

                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'assign_dateleave',
                        sdate: formData.sdate,
                        leaveType: formData.leaveType,
                        leaveFor: formData.leaveFor,
                        dayOfWeek: formData.dayOfWeek
                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === 200) {
                            showNotification('Leave assigned successfully', 'success');
                            // alertify.success('Leave assigned successfully');
                            fetchHolidays();
                            fetchLeaveData();
                            $('#leaveModal').hide();
                        } else {
                            // alertify.error('Failed to assign leave type');
                            showNotification(response.message, 'error');
                        }
                    },
                    error: function () {
                        alertify.error('Error assigning leave type');
                        showNotification('Error assigning leave type', 'error');
                    }
                });
            });


            // Handle long leave form submission
            $('#longLeaveForm').submit(function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'assign_longleave',
                        ltype: formData.get('ltype'),
                        fdate: formData.get('fDate'),
                        tdate: formData.get('tDate'),
                        leaveFor: formData.get('leaveFor')

                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === 200) {
                            showNotification(response.message, 'success');
                            // alertify.success(response.message);
                            fetchHolidays();
                            fetchLeaveData();
                            $('#longLeaveModal').hide();
                        } else {
                            showNotification(response.message, 'error');
                            //alertify.error(response.message);
                        }
                    },
                    error: function () {
                        showNotification('Error assigning long leave', 'error');
                        //alertify.error('Error assigning long leave');
                    }
                });
            });

            // Handle delete leave
            $(document).on('click', '.delete-leave', function () {
                const id = $(this).data('id');
                const date = $(this).data('date');

                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: { action: 'delete_leave_details', uid: id, hdate: date },
                    success: function (response) {

                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 200) {
                            showNotification(response.message, 'success');
                            //alertify.error(response.message);
                            fetchHolidays();
                            fetchLeaveData();
                        }
                    },
                    error: function () {
                        //alertify.error('Error deleting leave');
                        showNotification('Error deleting leave', 'error');
                    }
                });
            });

            // Modal controls
            $('#assignLongLeaveBtn').click(function () {
                $('#longLeaveModal').show();
            });

            $('.close-modal').click(function () {
                $('.modal').hide();
            });

            $(window).click(function (e) {
                if ($(e.target).hasClass('modal')) {
                    $('.modal').hide();
                }
            });

            // Search functionality
            $('#searchInput').keyup(function () {
                dataTable.search($(this).val()).draw();
            });

            // Helper functions
            function updateCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"];

                $('#currentMonth').text(`${monthNames[month]} ${year}`);
                const calendarGrid = $('#calendarGrid');
                calendarGrid.empty();

                // Add day headers
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                days.forEach(day => {
                    calendarGrid.append(`<div class="calendar-day day-header">${day}</div>`);
                });

                // Add empty cells for days before first of month
                for (let i = 0; i < firstDay.getDay(); i++) {
                    calendarGrid.append('<div class="calendar-day"></div>');
                }

                // Add days of the month
                for (let day = 1; day <= lastDay.getDate(); day++) {
                    const date = new Date(year, month, day);
                    const formattedDate = standardizeDateFormat(date);
                    const holiday = isHoliday(date);

                    let dayClass = 'calendar-day';
                    let dayContent = day;

                    if (holiday) {
                        dayClass += ' holiday';
                        dayContent = `${day}<br><span class="holiday-text">${holiday.type}-${holiday.who}</span>`;
                    }

                    const dayElement = $(`<div class="${dayClass}" data-date="${formattedDate}">${dayContent}</div>`);
                    dayElement.click(function () {
                        handleDateClick(date);
                    });
                    calendarGrid.append(dayElement);
                }
            }

            function formatDate(date) {
                const day = date.getDate().toString();
                const month = (date.getMonth() + 1).toString();
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }

            function handleDateClick(date) {
                const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const formattedDate = formatDate(date);

                $('#selectedDate')
                    .text(`Selected Date: ${formattedDate}`)
                    .data('date', formattedDate);
                $('#selectedDay').text(`Day of the Week: ${daysOfWeek[date.getDay()]}`);
                $('#leaveType').val('');
                $('#leaveFor').val('');
                $('#leaveModal').show();
            }

            function standardizeDateFormat(dateStr) {
                if (dateStr instanceof Date) {
                    return `${dateStr.getDate()}/${dateStr.getMonth() + 1}/${dateStr.getFullYear()}`;
                }
                return dateStr;
            }

            function isHoliday(date) {
                const formattedDate = standardizeDateFormat(date);
                //console.log('Checking:', formattedDate, 'against holidays:');
                // Add some debug logging
                const found = holidays.find(holiday => {
                    const matches = holiday.hdate === formattedDate;
                    // if (matches) {
                    //   console.log('Found match:', holiday);
                    // }
                    return matches;
                });
                return found;
            }

            function getSundayDatesForMonth(year, month) {
                const sundayDates = [];
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month, day);
                    if (date.getDay() === 0) {
                        sundayDates.push(formatDate(date));
                    }
                }

                return sundayDates;
            }

            function fetchHolidays() {
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'GET',
                    data: { action: 'get_holiday_details' },
                    success: function (response) {

                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 200) {
                            holidays = response.data;
                            updateCalendar();
                        }
                    },
                    error: function () {
                        alertify.error('Error fetching holidays');
                    }
                });
            }

            function fetchLeaveData() {
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'GET',
                    data: { action: 'get_leave_details' },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === 200) {
                            dataTable.clear();
                            dataTable.rows.add(response.data);
                            dataTable.draw();
                        }
                    },
                    error: function () {
                        alertify.error('Error fetching leave data');
                    }
                });
            }

            // Initialize calendar
            updateCalendar();
        });
    </script>
</body>

</html>