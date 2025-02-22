<?php
include("config.php");
include("session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIC</title>
    <link rel="icon" type="image/png" sizes="32x32" href="image/icons/mkce_s.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-5/bootstrap-5.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
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

        td {
            text-align: left;
            font-size: 0.9em;
            vertical-align: middle;
            /* For vertical alignment */
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
            padding: 20px;
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

        .custom-gradient {
            background: linear-gradient(to bottom, rgb(255, 255, 255), #00f2fe);
            /* Vertical gradient */
            padding: 10px 15px;
            /* Adjust padding as needed */
            border-radius: 5px;
            /* Optional: Rounded corners */
        }

        .custom-table {
            border-radius: 10px;
        }

        .breadcrumb-area {
            background-image: linear-gradient(to top, #fff1eb 0%, #ace0f9 100%);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin: 20px;
            padding: 15px 20px;
        }

        .breadcrumb {
            --bs-breadcrumb-padding-x: 0;
            --bs-breadcrumb-padding-y: 0;
            --bs-breadcrumb-margin-bottom: 1rem;
            --bs-breadcrumb-bg: ;
            --bs-breadcrumb-border-radius: ;
            --bs-breadcrumb-divider-color: var(--bs-secondary-color);
            --bs-breadcrumb-item-padding-x: 0.5rem;
            --bs-breadcrumb-item-active-color: var(--bs-secondary-color);
            display: flex;
            flex-wrap: wrap;
            padding: var(--bs-breadcrumb-padding-y) var(--bs-breadcrumb-padding-x);
            margin-bottom: var(--bs-breadcrumb-margin-bottom);
            font-size: var(--bs-breadcrumb-font-size);
            list-style: none;
            background-color: var(--bs-breadcrumb-bg);
            border-radius: var(--bs-breadcrumb-border-radius);
        }
        
    </style>

    <style>
        td {
            cursor: pointer;
        }

        .month {
            border: 1px solid black;
            padding: 5px;
            width: 80px;
            height: 100px;
            text-align: center;
        }

        .th {
            padding: 25px;
        }

        td {
            user-select: none;
        }

        .mon-cal-wrapper {
            width: 80%;
            margin: 0 auto;
        }

        .mon-cal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: white;
            padding: 15px;
        }

        #prevMonth,
        #nextMonth {
            background-color: transparent;
            color: white;
            border: none;
            cursor: pointer;
        }


        table {
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
            table-layout: fixed;
        }

        .day-cal {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .day-cal td {
            border-left: 1px solid #ddd;
        }

        .day-cal-table {
            width: 100%;
            border-collapse: collapse;
        }

        .day-cal-table th {
            background-color: black;
            color: #fff;
            padding: 10px;
            text-align: center;
        }



        .day-cal-table td {
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .day-cal-table tr:last-child td {
            border-bottom: none;
        }

        .alertDialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 550px;
            height: 650px;
            align-items: center;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 10px;
            display: none;
            z-index: 2;
        }

        @media screen and (max-width: 500px) {
            .alertDialog {
                width: 350px;
            }
        }

        .dialog-body {
            margin: 20px 20px;

        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }



        .dialog-head {
            background-color: black;
            color: white;
            height: 70px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
        }

        .alert-data {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100px;
        }

        .data-div {
            color: #64748b;
            font-size: 20px;
            font-weight: bold;
        }

        .add-details-header {
            color: #3f3f46;
            font-size: 20px;
            font-weight: bold;
        }

        .custom-field {
            position: relative;
            font-size: 14px;
            border-top: 20px solid transparent;
            margin-bottom: 5px;
            display: inline-block;
            --field-padding: 12px;
        }

        .custom-field input {
            border: none;
            -webkit-appearance: none;
            -ms-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: #f2f2f2;
            padding: var(--field-padding);
            border-radius: 3px;
            width: 300px;
            outline: none;
            font-size: 14px;
        }

        .custom-field .placeholder {
            position: absolute;
            left: var(--field-padding);
            width: calc(100% - (var(--field-padding) * 2));
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            top: 22px;
            line-height: 100%;
            transform: translateY(-50%);
            color: #aaa;
            transition: top 0.3s ease, color 0.3s ease, font-size 0.3s ease;
        }

        .custom-field input.dirty+.placeholder,
        .custom-field input:focus+.placeholder,
        .custom-field input:not(:placeholder-shown)+.placeholder {
            top: -10px;
            font-size: 10px;
            color: #222;
        }

        .custom-field.two input {
            border-radius: 0;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            background: linear-gradient(90deg, #222, #222) center bottom/0 0.15em no-repeat,
                linear-gradient(90deg, #ccc, #ccc) left bottom/100% 0.15em no-repeat,
                linear-gradient(90deg, #fafafa, #fafafa) left bottom/100% no-repeat;
            transition: background-size 0.3s ease;
        }

        .requirement-details {
            margin: 50px 0px;
            font-weight: bold;
        }

        .mic-select {
            display: flex;
            flex-direction: column;
            font-size: 20px;
            margin: 20px 20px;
            color: #64748b;
        }

        .mic-select input {
            margin: 10px 0px;
        }

        .dialog-footer {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin: 20px 0px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'side.php'; ?>
    <div class="overlay"></div>
    <!-- Main Content -->
    <div class="content">

        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
        </div>

        <!-- Topbar -->
        <?php include 'ftopbar.php'; ?>

        <!-- Breadcrumb -->
        <div class="breadcrumb-area custom-gradient">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">

                    <li class="breadcrumb-item active" aria-current="page">Dashboard (Welcome <?php echo $s; ?>)</li>
                </ol>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div>

                        <div class="seven" style="display: flex; justify-content: center;">
                            <h1 id="hallTitle"></h1>
                        </div>
                        <div class="mon-cal" id="monCal" style="background-color: #fff;">

                            <div style="display: flex;justify-content:space-evenly">
                                <div style="display: flex;align-items:center">
                                    <button class="btn btn-secondary" onclick="toggleDayCal()" style="padding: 10px 20px;margin:5px">Select</button>
                                </div>
                                <div class="mon-legend" style="display: flex; justify-content:flex-start">
                                    <div class="mon-legend-white" style="display:flex;margin:20px 20px; ">
                                        <div style="width: 20px;height:20px;background-color:white;border-style: ridge;"></div>
                                        <div style="margin-left:20px;">Fully available</div>
                                    </div>
                                    <div class="mon-legend-partial" style="display:flex;margin:20px 20px;">
                                        <div style="width: 20px;height:20px;background-image:linear-gradient(to top, #f57676 , #ffff);border-style: ridge;"></div>
                                        <div style="margin-left:20px;">Some timeslots available</div>
                                    </div>
                                    <div class="mon-legend-red " style="display:flex;margin:20px 20px;">
                                        <div style="width: 20px;height:20px;background-color:#f64c4c;border-style: ridge;"></div>
                                        <div style="margin-left:20px;">Fully booked</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mon-cal-wrapper">
                                <div class="mon-cal-header">
                                    <button id="prevMonth">Previous</button>
                                    <h2 id="monthName">October 2023</h2>
                                    <button id="nextMonth">Next</button>
                                </div>
                                <table id="mon-table">
                                    <thead id="mon-table-header">
                                        <tr>
                                            <th class="th">sunday</th>
                                            <th class="th">Monday</th>
                                            <th class="th">Tuesday</th>
                                            <th class="th">Wednesday</th>
                                            <th class="th">Thursday</th>
                                            <th class="th">Friday</th>
                                            <th class="th">Saturday</th>
                                        </tr>
                                    </thead>
                                    <tbody id="monTableBody">
                                        <tr class="mon-table-body-row">
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                        </tr>
                                        <tr class="mon-table-body-row">
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                        </tr>
                                        <tr class="mon-table-body-row">
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                        </tr>
                                        <tr class="mon-table-body-row">
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                        </tr>
                                        <tr class="mon-table-body-row">
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                            <td class="month"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div id="selectedCellInfo"></div>
                        </div>

                        <div class="day-cal" id="dayCal" style="display: none;">
                            <div class="day-legend" style="display: flex; justify-content:flex-end">
                                <div class="day-legend-white" style="display:flex;margin:20px 20px; ">
                                    <div style="width: 20px;height:20px;background-color:white;border-style: ridge;"></div>
                                    <div style="margin-left:20px;">Not yet booked</div>
                                </div>
                                <div class="day-legend-yellow" style="display:flex;margin:20px 20px;">
                                    <div style="width: 20px;height:20px;background-color:yellow;border-style: ridge;"></div>
                                    <div style="margin-left:20px;">Requested</div>
                                </div>
                                <div class="day-legend-green " style="display:flex;margin:20px 20px;">
                                    <div style="width: 20px;height:20px;background-color:green;border-style: ridge;"></div>
                                    <div style="margin-left:20px;">Accepted</div>
                                </div>
                            </div>
                            <div style="display: flex;justify-content:space-between">
                            <button class="btn btn-secondary px-4 py-2 m-1" onclick="toggleMonCal()">Go back</button>
                        <button class="btn btn-secondary px-4 py-2 m-1" type="button" id="bookbtn">Book Now</button>

                            </div>
                            <table class="day-cal-table" id="dayTable">
                                <thead class="day-table-head">
                                    <tr id="dayTableHeadRow">
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody id="dayTableBody">
                                    <tr>
                                        <td>5 AM - 6 AM</td>
                                    </tr>
                                    <tr>
                                        <td>6 AM - 7 AM</td>
                                    </tr>
                                    <tr>
                                        <td>7 AM - 8 AM</td>
                                    </tr>
                                    <tr>
                                        <td>8 AM - 9 AM</td>
                                    </tr>
                                    <tr>
                                        <td>9 AM - 10 AM</td>
                                    </tr>
                                    <tr>
                                        <td>10 AM - 11 AM</td>
                                    </tr>
                                    <tr>
                                        <td>11 AM - 12 PM</td>
                                    </tr>
                                    <tr>
                                        <td>12 PM - 1 PM</td>
                                    </tr>
                                    <tr>
                                        <td>1 PM - 2 PM</td>
                                    </tr>
                                    <tr>
                                        <td>2 PM - 3 PM</td>
                                    </tr>
                                    <tr>
                                        <td>3 PM - 4 PM</td>
                                    </tr>
                                    <tr>
                                        <td>4 PM - 5 PM</td>
                                    </tr>
                                    <tr>
                                        <td>5 PM - 6 PM</td>
                                    </tr>
                                    <tr>
                                        <td>6 PM - 7 PM</td>
                                    </tr>
                                    <tr>
                                        <td>7 PM - 8 PM</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div id="selectedCellInfo1">

                            </div>

                            <div class="alertDialog" id="alertDialog">
                                <div class="dialog-head">
                                    <h2>Hall Booking</h2>
                                </div>
                                <div class="dialog-body">
                                    <div class="alert-data">
                                        <div id="bookedBy" class="data-div"></div>
                                        <div id="hallName" class="data-div"></div>
                                    </div>
                                    <form id="myForm">
                                        <div class="add-detail">
                                            <p class="add-details-header">Add Event Details</p>
                                            <label class="custom-field two">
                                                <input type="text" id="eventLabel" name="eventName" placeholder="" required />
                                                <span class="placeholder">Enter Event Name</span>
                                            </label>
                                        </div>
                                        <div class="requirement-details">
                                            <label for="micType" class="form-label" style="font-size: 20px">Select your requirements</label>
                                            <div class="mic-select">
                                                <label for="wired"><input type="checkbox" name="wired" /> &nbsp;wired mic</label>
                                                <label for="wireless"><input type="checkbox" name="wireless" /> &nbsp;wireless
                                                    mic</label>
                                                <label for="podium"><input type="checkbox" name="podium" /> &nbsp;podium
                                                    mic</label>
                                            </div>
                                        </div>
                                        <div class="dialog-footer">
                                            <button class="btn btn-lg btn-success" type="submit" id="submitButton">
                                                Submit
                                            </button>
                                            <button class="btn btn-lg btn-danger" type="button" id="closeBtn">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
        document.addEventListener('DOMContentLoaded', function() {
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
            window.onload = function() {
                clearTimeout(loadingTimeout);

                // Add a small delay to ensure smooth transition
                setTimeout(hideLoader, 500);
            };

            // Error handling
            window.onerror = function(msg, url, lineNo, columnNo, error) {
                clearTimeout(loadingTimeout);
                showError();
                return false;
            };
        });

        document.addEventListener("DOMContentLoaded", function() {
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
        document.getElementById("hallTitle").textContent = JSON.parse(sessionStorage.getItem("hallData")).hallName
    </script>

    <script>
        class CustomTime {

            static expand(time) {
                let t = {
                    "5AM": "5AM-6AM",
                    "6AM": "6AM-7AM",
                    "7AM": "7AM-8AM",
                    "8AM": "8AM-9AM",
                    "9AM": "9AM-10AM",
                    "10AM": "10AM-11AM",
                    "11AM": "11AM-12PM",
                    "12PM": "12PM-1PM",
                    "1PM": "1PM-2PM",
                    "2PM": "2PM-3PM",
                    "3PM": "3PM-4PM",
                    "4PM": "4PM-5PM",
                    "5PM": "5PM-6PM",
                    "6PM": "6PM-7PM",
                    "7PM": "7PM-8PM",
                    "8PM": "8PM-9PM"
                }


                let keys = Object.keys(t)

                let from = time.split("-")[0]
                let to = time.split("-")[1]

                let range = []

                let start = keys.indexOf(from)
                let end = keys.indexOf(to) - 1;
                for (let i = start; i <= end; i++) {
                    range.push(t[keys[i]])
                }

                return range;

            }

            static findContinuous(time) {
                let start = time[0].split("-")[0]
                let end;
                let continuous = []
                for (let i = 0; i < time.length; i++) {
                    try {
                        if (time[i].split("-")[1] != time[i + 1].split("-")[0]) {
                            end = time[i].split("-")[1]
                            continuous.push(`${start}-${end}`)
                            start = time[i + 1].split("-")[0]
                        }
                    } catch (e) {
                        end = time[i].split("-")[1]
                        continuous.push(`${start}-${end}`)
                    }
                }
                return continuous;
            }

            static sort(times) {
                let t = ["5AM-6AM", "6AM-7AM", "7AM-8AM", "8AM-9AM", "9AM-10AM", "10AM-11AM", "11AM-12PM", "12PM-1PM", "1PM-2PM", "2PM-3PM", "3PM-4PM", "4PM-5PM", "5PM-6PM", "6PM-7PM", "7PM-8PM", ]
                let sorted = []
                for (let i = 0; i < t.length; i++) {
                    if (times.includes(t[i]))
                        sorted.push(t[i])
                }
                return sorted;
            }

            static group(selectedTimes) {
                let group = {}
                for (let i = 0; i < selectedTimes.length; i++) {
                    let string = selectedTimes[i].replace(/ /g, "")
                    let data = string.split("|")
                    let date = data[1]
                    let time = data[0]
                    if (group[date]) {
                        group[date].push(time)
                    } else {
                        group[date] = [time]
                    }
                }

                let keys = Object.keys(group)

                for (let key of keys) {
                    group[key] = this.findContinuous(this.sort(group[key]))

                }

                return group;
            }
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            const prevMonthButton = document.getElementById("prevMonth");
            const nextMonthButton = document.getElementById("nextMonth");
            const currentMonthHeader = document.getElementById("monthName");


            let currentDate = new Date();
            await displayCalendar(currentDate);

            prevMonthButton.addEventListener("click", async function() {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
                await displayCalendar(currentDate);
            });

            // Event listener for next month button
            nextMonthButton.addEventListener("click", async function() {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
                await displayCalendar(currentDate);
            });

            async function displayCalendar(date) {
                const monthOptions = {
                    year: "numeric",
                    month: "long"
                };
                currentMonthHeader.textContent = date.toLocaleDateString(undefined, monthOptions);

                if (selectedCells.length > 0) {
                    selectedCells.forEach(cell => {
                        cell.style.backgroundColor = 'white';
                        console.log("reset")
                    });
                    selectedCells.length = 0
                    updateSelectedCellInfo()
                }

                var table = document.getElementById("mon-table");
                var tbody = table.getElementsByTagName("tbody")[0];

                for (var i = 0, row; row = tbody.rows[i]; i++) {
                    for (var j = 0, cell; cell = row.cells[j]; j++) {
                        cell.innerHTML = ""
                        cell.setAttribute('data-time', '');
                        cell.setAttribute('selected', 'false')
                        cell.style.backgroundColor = 'white'
                        cell.style.backgroundImage = 'none'
                    }
                }


                // Create a new date object for the first day of the month
                const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);

                // Get the number of days in the month
                const daysInMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

                // Determine the day of the week for the first day of the month (0 = Sunday, 1 = Monday, etc.)
                const firstDayOfWeek = firstDayOfMonth.getDay();

                let d = new Date(date.getFullYear(), date.getMonth(), 1);
                let n = 1;
                for (var i = 0, row; row = tbody.rows[i]; i++) {
                    for (var j = 0, cell; cell = row.cells[j]; j++) {
                        if (i == 0 && j < firstDayOfWeek) {
                            continue;
                        }
                        if (n <= daysInMonth) {
                            d.setDate(d.getDate() + 1);
                            cell.innerHTML = n;
                            cell.setAttribute('data-time', d.toISOString().slice(0, 10));
                            cell.setAttribute('selected', 'false')


                            let todayDate = cell.getAttribute('data-time');

                            let todayRes = await fetch("getDayStatus.php", {
                                method: "POST",
                                body: JSON.stringify({
                                    "date": todayDate,
                                    "hallName": JSON.parse(sessionStorage.getItem("hallData")).hallName
                                })
                            })
                            let todayData = await todayRes.json()

                            // console.log(todayData)

                            if (todayData.status == 0) {
                                // ("if......partial....yellow")
                                // cell.style.backgroundColor = "linear-gradient(rgb(255, 234, 84), rgb(255, 87, 87))"
                                cell.setAttribute('data-status', '0')
                                cell.style.backgroundImage = "linear-gradient(to top, #f57676 , #ffff)"

                            } else if (todayData.status == 1) {
                                // ("if....full....red")
                                cell.style.backgroundColor = "#f57676";
                                cell.setAttribute('data-status', '1')
                                // cell.style.backgroundImage = "#ff5757"
                            } else {
                                cell.setAttribute('data-status', '-1')
                            }


                            n++;
                        } else {
                            break;
                        }
                    }
                }

            }

        });
    </script>


    <script>
        const selectedCells = [];
        const cells = document.querySelectorAll('.month');
        const selectedCellInfo = document.getElementById('selectedCellInfo');
        let isMouseDown = false;

        async function handleMouseDown(cell) {
            isMouseDown = true
            cell.getAttribute('selected') === 'false' ? cell.setAttribute('selected', true) : cell.setAttribute('selected', false);
            if (cell.getAttribute('selected') === 'true' && cell.getAttribute('data-time')) {
                selectedCells.push(cell);
                cell.style.backgroundColor = 'lightblue';
                cell.style.backgroundImage = 'none'
            } else if (cell.getAttribute('data-time')) {
                let i = selectedCells.indexOf(cell);
                selectedCells.splice(i, 1);

                const status = cell.getAttribute('data-status')

                if (status == 0) {
                    // "if......partial"
                    // cell.style.backgroundColor = '#e9eb7e';
                    cell.style.backgroundImage = "linear-gradient(to top, #f57676 , #ffff)"
                } else if (status == 1) {
                    // "if....full"
                    cell.style.backgroundColor = '#ed6868';
                } else {
                    // "if....none"
                    cell.style.backgroundColor = '#fff';
                }
            }
            updateSelectedCellInfo();
        }

        async function handleMouseOver(cell) {
            if (!isMouseDown) return;
            if (cell.getAttribute('selected') === 'false' && !selectedCells.includes(cell) && cell.getAttribute('data-time')) {
                selectedCells.push(cell);
                cell.style.backgroundColor = 'lightblue';
                cell.style.backgroundImage = 'none'
                cell.style.boxShadow = "rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;"
                cell.setAttribute('selected', 'true')
                updateSelectedCellInfo();
            } else if (cell.getAttribute('data-time')) {
                let i = selectedCells.indexOf(cell);
                selectedCells.splice(i, 1);

                const status = cell.getAttribute('data-status')

                if (status == 0) {
                    // "if......partial"
                    // cell.style.backgroundColor = '#e9eb7e';
                    cell.style.backgroundImage = "linear-gradient(to top, #f57676 , #ffff)"
                } else if (status == 1) {
                    // "if....full"
                    cell.style.backgroundColor = '#ed6868';
                } else {
                    // "if....none"
                    cell.style.backgroundColor = '#fff';
                }

                cell.setAttribute('selected', 'false')
                updateSelectedCellInfo();
            }
        }

        function handleMouseUp(cell) {
            isMouseDown = false;

        }
        let selectedTimes = []

        function updateSelectedCellInfo() {
            selectedTimes = selectedCells.map(cell => cell.getAttribute('data-time'));
            selectedCellInfo.innerHTML = `Selected Time Range: ${selectedTimes.join(' - ')}`;
        }

        cells.forEach(cell => {
            cell.addEventListener('mousedown', async () => {
                await handleMouseDown(cell);
            });

            cell.addEventListener('mouseover', async () => {
                await handleMouseOver(cell);
            });

            cell.addEventListener('mouseup', () => {
                handleMouseUp(cell);
            });
        });
    </script>

    <script>
        function onSelectDate(date) {
            var table = document.getElementById("dayTable");

            // Iterate through each section of the table (thead and tbody)
            var sections = [table.tHead, table.tBodies[0]]; // Get the first tbody

            sections.forEach(function(section) {
                // Iterate through each row in the section
                for (var i = 0; i < section.rows.length; i++) {
                    // Get the current row
                    var row = section.rows[i];

                    // Iterate through each cell in the row (except the first one)
                    for (var j = row.cells.length - 1; j > 0; j--) {
                        // Remove the cell from the row
                        row.deleteCell(j);
                    }
                }
            });
            let headRow = document.getElementById("dayTableHeadRow")
            for (let i = 0; i < date.length; i++) {
                const th = document.createElement('th');
                // th.textContent = date[i];
                th.innerHTML = `<div><p>${date[i]}</p><input type="checkbox" class="dayColumn" index="${i+1}">select all</input></div>`
                headRow.appendChild(th);
            }

            let tableBody = document.getElementById("dayTableBody")
            for (var i = 0, row; row = tableBody.rows[i]; i++) {
                for (let j = 0; j < date.length; j++) {
                    const newCell = document.createElement('td');
                    newCell.className = 'day'
                    newCell.textContent = '';
                    row.appendChild(newCell)
                }
            }
        }

        // onSelectDate(['2023-10-06', '2023-10-07', '2023-10-08', '2023-10-09'])
    </script>
    <script>
        let selectedCells1 = [];
        async function toggleDayCal() {

            if (selectedTimes.length < 1) {
                alertify.set("notifier", "position", "top-center")
                alertify.error('Please select atleast one Date');
                return;
            }




            document.getElementById("monCal").style.display = 'none'
            document.getElementById("dayCal").style.display = 'block'
            onSelectDate(selectedTimes)



            const bookingData = await fetch("fetch.php", {
                method: "Post",
                body: JSON.stringify({
                    "selectedDate": selectedTimes,
                    "hallName": JSON.parse(sessionStorage.getItem("hallData")).hallName
                })
            }).then(res => res.json()).then((data) => {
                let expanded = []
                for (let row of data) {
                    // console.log(row.time.split("|")[0])
                    let times = CustomTime.expand(row.time.split("|")[0])

                    for (let time of times) {
                        expanded.push({
                            "time": `${time}|${row.time.split("|")[1]}`,
                            "user": row.user,
                            "status": row.status,
                            "event": row.event
                        })
                    }
                }
                return expanded;
            })

            // console.log(bookingData)

            function findObjectByTime(array, time) {
                for (const obj of array) {
                    if (obj.time === time) {
                        return obj; // Found the object with the desired time
                    }
                }
                return null; // If the object with the desired time is not found
            }

            let headRow = document.getElementById("dayTableHeadRow")
            const thElements = headRow.getElementsByTagName('th');
            let tbody = document.getElementById("dayTableBody")
            for (var i = 0, row; row = tbody.rows[i]; i++) {
                let data = row.cells[0].textContent;
                // console.log(data)
                for (var j = 1, cell; cell = row.cells[j]; j++) {

                    const foundObject = findObjectByTime(bookingData, `${data.replace(/ /g,"")}|${thElements[j].getElementsByTagName('p')[0].textContent}`);
                    if (foundObject) {
                        cell.setAttribute('data-userName', foundObject.user)
                        cell.setAttribute('data-status', foundObject.status)
                        cell.setAttribute('data-event', foundObject.event)
                        if (foundObject.status == 'requested')
                            cell.style.backgroundColor = '#e9eb7e';
                        if (foundObject.status == 'approved')
                            cell.style.backgroundColor = '#86e45b';
                        cell.style.borderBottom = "none";
                        cell.innerHTML = `<div><p>${foundObject.event}</p></div>`
                    }

                    cell.setAttribute('data-time', `${data}|${thElements[j].getElementsByTagName('p')[0].textContent}`);
                    cell.setAttribute('selected', 'false')
                }
            }

            const cells1 = document.querySelectorAll('.day');

            cells1.forEach(cell => {
                cell.addEventListener('mousedown', () => {
                    handleMouseDown1(cell);
                });

                cell.addEventListener('mouseover', () => {
                    handleMouseOver1(cell);
                });

                cell.addEventListener('mouseup', () => {
                    handleMouseUp1(cell);
                });
            });


            const columnCheckboxes = document.querySelectorAll('.dayColumn');
            columnCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', selectAll);
            });
        }

        function toggleMonCal() {
            selectedCells1 = []
            document.getElementById("dayCal").style.display = 'none'
            document.getElementById("monCal").style.display = 'block'

        }






        let selectedTimes1 = []
        let payload = {}




        const selectedCellInfo1 = document.getElementById('selectedCellInfo1');
        let isMouseDown1 = false;

        function handleMouseDown1(cell) {
            isMouseDown1 = true
            // cell.getAttribute('selected') === 'false' ? cell.setAttribute('selected', true) : cell.setAttribute('selected', false);
            if (!cell.getAttribute("data-status") && cell.getAttribute('selected') === 'false' && cell.getAttribute('data-time')) {
                selectedCells1.push(cell);
                cell.style.backgroundColor = 'lightblue';
                cell.style.boxShadow = "rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;"
                // cell.className = "shadow-lg p-3 mb-5 bg-body rounded"
                cell.style.borderBottom = "none";
                cell.setAttribute('selected', 'true')
            } else if (!cell.getAttribute("data-status")) {
                let i = selectedCells1.indexOf(cell);
                selectedCells1.splice(i, 1);
                cell.style.backgroundColor = 'white';
                cell.style.borderBottom = "1px solid #ddd";
                cell.setAttribute('selected', 'false')
            }
            updateSelectedCellInfo1();
        }

        function handleMouseOver1(cell) {
            if (!isMouseDown1) return;
            if (!cell.getAttribute("data-status") && cell.getAttribute('selected') === 'false' && !selectedCells1.includes(cell) && cell.getAttribute('data-time')) {
                selectedCells1.push(cell);
                cell.style.backgroundColor = 'lightblue';
                cell.style.boxShadow = "rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;"
                // cell.className = "shadow-lg p-3 mb-5 bg-body rounded"
                cell.style.borderBottom = "none";
                cell.setAttribute('selected', 'true')
                updateSelectedCellInfo1();
            } else if (!cell.getAttribute('data-status')) {
                let i = selectedCells1.indexOf(cell);
                selectedCells1.splice(i, 1);
                cell.style.backgroundColor = 'white';
                cell.style.borderBottom = "1px solid #ddd";
                cell.setAttribute('selected', 'false')
                updateSelectedCellInfo1();
            }
        }

        function handleMouseUp1(cell) {
            isMouseDown1 = false;
        }


        function updateSelectedCellInfo1() {
            selectedTimes1 = selectedCells1.map(cell => cell.getAttribute('data-time'));
            // selectedCellInfo1.innerHTML = `Selected Time Range: ${selectedTimes1.join(' * ')} ${selectedTimes1.length}`;
            console.log(CustomTime.group(selectedTimes1));
            payload = CustomTime.group(selectedTimes1);
        }





        function selectAll() {

            let tbody = document.getElementById("dayTableBody")
            let rows = tbody.getElementsByTagName("tr");
            if (this.checked) {
                for (let i = 0; i < rows.length; i++) {
                    let cell = rows[i].cells[parseInt(this.getAttribute('index'))];
                    if (cell.getAttribute("data-status")) continue;
                    // cell.getAttribute('selected') === 'false' ? cell.setAttribute('selected', true) : cell.setAttribute('selected', false);
                    if (cell.getAttribute('data-time') && !selectedCells1.includes(cell)) {
                        selectedCells1.push(cell);
                        cell.style.backgroundColor = 'lightblue';
                        cell.style.boxShadow = "rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;"
                        // cell.className = "shadow-lg p-3 mb-5 bg-body rounded"
                        cell.style.borderBottom = "none";
                        cell.setAttribute('selected', 'true')

                        updateSelectedCellInfo1();
                    }
                }
            } else {
                for (let i = 0; i < rows.length; i++) {
                    let cell = rows[i].cells[parseInt(this.getAttribute('index'))];
                    if (cell.getAttribute("data-status")) continue;
                    // cell.getAttribute('selected') === 'false' ? cell.setAttribute('selected', true) : cell.setAttribute('selected', false);
                    let ind = selectedCells1.indexOf(cell);
                    selectedCells1.splice(ind, 1);
                    cell.style.backgroundColor = 'white';
                    cell.style.borderBottom = "1px solid #ddd";
                    cell.setAttribute('selected', 'false')

                    updateSelectedCellInfo1();
                }
            }
        }


        // Retrieve the JSON data from session storage
        const jsonData = sessionStorage.getItem("hallData");

        if (jsonData) {
            const data = JSON.parse(jsonData);

            // Render the data on the page
            const outputDiv1 = document.getElementById("bookedBy");
            outputDiv1.innerHTML = `${data.userName}`;


            const outputDiv2 = document.getElementById("hallName");
            outputDiv2.innerHTML = `${data.hallName}`;
        }

        const myForm = document.getElementById('myForm')

        myForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent the default form submission behavior

            console.log("event")

            // Get the form data
            const formData = new FormData(this);

            // Create an array
            const myArray = [];

            let keeys = Object.keys(payload);

            for (let key of keeys) {
                let value = payload[key];
                for (let i = 0; i < value.length; i++) {
                    myArray.push(`${value[i]}|${key}`)
                }
            }



            const hallData = JSON.parse(sessionStorage.getItem("hallData"));

            formData.set('myArray', JSON.stringify(myArray));

            formData.set('userName', hallData.userName);
            formData.set('hallName', hallData.hallName);

            const url = 'book.php';
            console.log(formData)

            formData.append("bookingRequest", true)

            try {
                const resData = await fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())

                alertify.set("notifier", "position", "top-center")
                if (resData.status == '200')
                    alertify.success(resData.message);
                else if (resData.status == '500')
                    alertify.error(resData.message);
            } catch (error) {
                console.log(error)
            }
            selectedCells1 = []
            updateSelectedCellInfo1()
            closeModal()
            toggleDayCal()
        });





        // Get references to the modal and button
        const modal = document.getElementById('alertDialog');
        const openModalBtn = document.getElementById('bookbtn');
        const closeModalBtn = document.getElementById('closeBtn');
        const overlay = document.querySelector('.overlay');
        // Function to open the modal
        function openModal() {
            if (selectedTimes1.length < 1) {
                alertify.set("notifier", "position", "top-right")
                alertify.error('Please select atleast one time slot');
                return;
            }
            modal.style.display = 'block';
            overlay.style.display = 'block';
            // Disable scrolling on the body
            document.body.style.overflow = 'hidden';
        }

        // Function to close the modal
        function closeModal() {
            modal.style.display = 'none';
            overlay.style.display = 'none';
            // Enable scrolling on the body
            document.body.style.overflow = 'auto';
        }

        if (!openModalBtn.getAttribute("data-eventAdded") && !closeModalBtn.getAttribute("data-eventAdded") && !overlay.getAttribute("data-eventAdded")) {
            // Event listeners
            openModalBtn.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);

            // Close the modal when clicking outside the modal content or on the overlay
            overlay.addEventListener('click', closeModal);
            openModalBtn.setAttribute("data-eventAdded", 'true')
            closeModalBtn.setAttribute("data-eventAdded", 'true')
            overlay.setAttribute("data-eventAdded", 'true')
        }
    </script>
</body>

</html>