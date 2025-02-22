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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
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
            margin: 0;
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


        /* others page css starts */

        .header {
            text-align: center;
            margin-bottom: 2rem;
            color: #1a237e;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            color: #1a237e;
        }

        .card-header i {
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 0 2px rgba(26, 35, 126, 0.2);
        }

        .row {
            display: flex;
            gap: 1rem;
        }

        .col-6 {
            flex: 1;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            background: #1a237e;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #283593;
        }

        .manual-punch {
            background: linear-gradient(135deg, #FF6B6B, #FF8E8E);
        }

        .shift-change {
            background: linear-gradient(135deg, #4E54C8, #8F94FB);
        }

        .punch-timings {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }

        .card-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            margin-right: 1rem;
        }

        /* Toast notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 2rem;
            border-radius: 8px;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .toast.success {
            background: #4CAF50;
        }

        .toast.error {
            background: #f44336;
        }

        .toast.show {
            opacity: 1;
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
                    <li class="breadcrumb-item active" aria-current="page">Other Informations</li>
                </ol>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="container-fluid">

            <div class="cards-container">
                <!-- Manual Punch Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon manual-punch">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <h3>Manual Punch</h3>
                    </div>
                    <form id="manualPunchForm">
                        <div class="form-group">
                            <label class="form-label">Staff ID</label>
                            <input type="text" name="id" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date</label>
                            <input type="date" name="mdate" class="form-input" required>
                        </div>
                        <button type="submit" class="btn">Validate Punch</button>
                    </form>
                </div>

                <!-- Shift Change Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon shift-change">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Shift Change</h3>
                    </div>
                    <form id="shiftChangeForm">
                        <div class="form-group">
                            <label class="form-label">Date</label>
                            <input type="date" name="sdate" class="form-input" required>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Shift In</label>
                                    <input type="text" name="sin" class="form-input" value="08:40:00" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Shift Out</label>
                                    <input type="text" name="sout" class="form-input" value="17:00:00" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn">Assign Shift</button>
                    </form>
                </div>

                <!-- Punch Timings Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon punch-timings">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3>Punch Timings</h3>
                    </div>
                    <form id="punchTimingsForm">
                        <div class="form-group">
                            <label class="form-label">Staff ID</label>
                            <input type="text" name="id" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date</label>
                            <input type="date" name="mdate" class="form-input" required>
                        </div>
                        <button type="submit" class="btn">View Timings</button>
                    </form>
                </div>
            </div>
        </div>

    <div id="toast" class="toast"></div>
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
            // Show/hide loader
            function toggleLoader(show) {
                $('#loader').css('display', show ? 'flex' : 'none');
            }

            // Show toast message
            function showToast(message, type) {
                const toast = $('#toast');
                toast.text(message)
                    .removeClass('success error')
                    .addClass(type)
                    .addClass('show');

                setTimeout(() => {
                    toast.removeClass('show');
                }, 3000);
            }

            // Manual Punch Form
            $('#manualPunchForm').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                toggleLoader(true);
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'manualpunch_data',
                        uid: formData.get('id'),
                        mdate: formData.get('mdate')
                    },
                    success: function (response) {
                        
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if (response.status === 200) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        showToast('An error occurred. Please try again.', 'error');
                    },
                    complete: function () {
                        toggleLoader(false);
                    }
                });
            });

            // Shift Change Form
            $('#shiftChangeForm').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                toggleLoader(true);
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'shift_change',
                        sdate: formData.get('sdate'),
                        sin: formData.get('sin'),
                        sout: formData.get('sout')
                    },
                    success: function (response) {

                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status === 200) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        showToast('An error occurred. Please try again.', 'error');
                    },
                    complete: function () {
                        toggleLoader(false);
                    }
                });
            });

            // Punch Timings Form
            $('#punchTimingsForm').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                toggleLoader(true);
                $.ajax({
                    url: 'hradmin_back.php',
                    method: 'POST',
                    data: {
                        action: 'multipunch_data',
                        uid: formData.get('id'),
                        mdate: formData.get('mdate')
                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === 200) {
                            const punchTimings = response.data.map(date =>
                                new Date(date).toLocaleString()
                            );

                            Swal.fire({
                                title: 'Punch Timings',
                                html: punchTimings.map(time =>
                                    `<p style="margin: 0.5rem 0;">${time}</p>`
                                ).join(''),
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Punch Timings',
                                text: response.message,
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function (xhr) {
                        showToast('An error occurred. Please try again.', 'error');
                    },
                    complete: function () {
                        toggleLoader(false);
                    }
                });
            });
        });
    </script>
</body>

</html>