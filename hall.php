<?php
include("config.php");
include("session.php");
$s = $_SESSION['login_user'];
$query2 = "SELECT name FROM faculty WHERE id='$s'";
$query_run2 = mysqli_query($db, $query2);

if (mysqli_num_rows($query_run2) > 0) {
    $frow = mysqli_fetch_assoc($query_run2);

    $fname = $frow['name'];
}
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


        .card-hover {
            transition: transform 0.3s ease-in-out;
        }

        .card-hover:hover {
            transform: scale(1.1);
        }

        .hall-card {
            transition: transform 0.2s;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .hall-card:hover {
            transform: translateY(-5px);
        }

        .hall-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }


        .notification-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 1px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .notification-btn .material-icons {
            font-size: 24px;
        }

        .notification-badge {
            background-color: white;
            color: red;
            font-size: 14px;
            font-weight: bold;
            border-radius: 50%;
            padding: 2px 6px;
            position: absolute;
            top: 5px;
            right: 5px;
        }

        #notificationContainer {
            position: absolute;
            top: 50px;
            right: 0;
            width: 250px;
            background-color: white;
            border: 1px solid #ddd;
            padding: 10px;
            display: none;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .notifications {
            color: black;
            font-size: 14px;
            max-height: 200px;
            overflow-y: auto;
            /* Scrollable if notifications exceed limit */
            padding: 5px;
        }

        .d-flex {
            display: flex;
            justify-content: end;
            margin-bottom: 20px;
            position: relative;
        }

        .msg-box {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .msg-box.unread {
            background-color: #f0f0f0;
        }

        .msg-hall-event h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }

        .msg-time-status {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .msg-reason {
            font-size: 14px;
            color: #666;
        }

        .notification-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 3px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            /* Slightly rounded for a modern look */
            position: relative;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
            /* Smooth hover effect */
        }

        .notification-btn:hover {
            background-color: darkred;
            /* Slightly darker on hover */
        }

        .notification-btn .material-icons {
            font-size: 26px;
            /* Slightly larger for better visibility */
        }

        .notification-badge {
            background-color: white;
            color: red;
            font-size: 12px;
            /* Reduced size slightly for better fit */
            font-weight: bold;
            border-radius: 50%;
            padding: 4px 8px;
            position: absolute;
            top: -3px;
            /* Adjusted position for better alignment */
            right: -3px;
            border: 1px solid red;
            /* Added border for better visibility */
        }
    </style>


</head>

<body>
    <!-- Sidebar -->
    <?php include 'side.php'; ?>

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

            <div class="d-flex justify-content-end mb-4">
                <button type="button" class="notification-btn" id="notificationBtn">
                    <span class="material-icons">notifications</span>
                    <span class="notification-badge" id="notificationCount">0</span>
                </button>

                <div id="notificationContainer">
                    <div class="notifications" id="notifications">No new notifications</div>
                </div>
            </div>
            <div class="row g-4 mt-4">

                <?php
                $halls = [
                    ['name' => 'Valluvar Hall', 'image' => 'Valluvar.jpg', 'block' => 'APJ'],
                    ['name' => 'Bharathiyar Hall', 'image' => 'Bharathiyar Hall.jpg', 'block' => 'APJ'],
                    ['name' => 'Sir C.V. Raman Hall', 'image' => 'Sir C.V. Raman Hall.jpg', 'block' => 'APJ'],
                    ['name' => 'G.D. Naidu Hall', 'image' => 'G.D. Naidu Hall.jpg', 'block' => 'APJ'],
                    ['name' => 'Ramanujan Hall', 'image' => 'Ramanujan Hall.jpg', 'block' => 'APJ'],
                    ['name' => 'Visvesvaraya Hall', 'image' => 'Visvesvaraya Hall.jpg', 'block' => 'APJ'],
                    ['name' => 'Vivekananda Hall', 'image' => 'Vivekananda Hall.png', 'block' => 'RK'],
                    ['name' => 'Valluvar Hall', 'image' => 'Vivekananda Hall.png', 'block' => 'APJ']
                ];
                ?>
                <div class="row">
                    <?php foreach ($halls as $hall): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="hall-card bg-white">
                                <img src="images/<?php echo htmlspecialchars($hall['image']); ?>"
                                    class="hall-image"
                                    alt="<?php echo htmlspecialchars($hall['name']); ?>">
                                <div class="p-4">
                                    <h4 class="mb-2"><?php echo htmlspecialchars($hall['name']); ?></h4>
                                    <div class="block-label <?php echo strtolower(htmlspecialchars($hall['block'])); ?>-block">
                                        IN <?php echo htmlspecialchars($hall['block']); ?> BLOCK
                                    </div>
                                    <button type="button"
                                        class="btn btn-outline-success btn-rounded shadow-sm w-100 fw-bold yeahbutton"
                                        data-hall-name="<?php echo htmlspecialchars($hall['name']); ?>"
                                        onclick="book(this)">
                                        Book Now!
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>

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
        document.getElementById("notificationBtn").addEventListener('click', async (e) => {
            let reqBody = new FormData();
            reqBody.append('notify', true);
            reqBody.append('userName', "<?php echo htmlspecialchars($fname); ?>");

            try {
                let res = await fetch("handleApproval.php", {
                    method: "POST",
                    body: reqBody
                });

                let data = await res.json();
                console.log(data);

                function displayNotifications(index) {
                    if (index < data.length) {
                        alertify.notify(data[index], 'success', 1, function() {
                            displayNotifications(index + 1);
                        });
                    }
                }

                alertify.set('notifier', 'position', 'top-right');
                displayNotifications(0);
                document.getElementById("notificationCount").innerText = '0';
            } catch (error) {
                console.error("Error fetching notifications:", error);
            }
        });

        function book(button) {
            let hallName = button.getAttribute("data-hall-name");
            let userName = "<?php echo htmlspecialchars($fname); ?>"; // Enclosed in quotes

            let sendData = {
                hallName: hallName,
                userName: userName
            };

            sessionStorage.setItem("hallData", JSON.stringify(sendData));

            // Redirect to integrate.php
            window.location.href = "integrate.php";
        }
    </script>
    <script>
        // Toggle notification container
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationContainer = document.getElementById('notificationContainer');

        notificationBtn.addEventListener('click', function() {
            notificationContainer.style.display =
                notificationContainer.style.display === 'none' ? 'block' : 'none';
        });

        // Close notifications when clicking outside
        document.addEventListener('click', function(event) {
            if (!notificationBtn.contains(event.target) &&
                !notificationContainer.contains(event.target)) {
                notificationContainer.style.display = 'none';
            }
        });

        // Update notification count
        function updateNotificationCount(count) {
            document.getElementById('notificationCount').textContent = count;
        }

        // Fetch and display notifications
        async function fetchNotifications() {
            try {
                const form = new FormData();
                form.append('getNotifications', true);
                form.append('user', '<?php echo htmlspecialchars($fname); ?>');

                const response = await fetch('handleApproval.php', {
                    method: 'POST',
                    body: form
                });

                const data = await response.json();
                const notificationsDiv = document.getElementById('notifications');

                if (data.length === 0) {
                    notificationsDiv.innerHTML = '<h3 style="text-align:center">No Notifications</h3>';
                    updateNotificationCount(0);
                } else {
                    notificationsDiv.innerHTML = '';
                    updateNotificationCount(data.filter(item => item.viewed == 1).length);

                    data.reverse().forEach(item => {
                        const backgroundColor = item.viewed == 1 ?
                            (item.status === 'rejected' ? '#ffa1a1' : '#6dffa5') :
                            '';

                        notificationsDiv.innerHTML += `
                            <div class="msg-box" style="background-color: ${backgroundColor}">
                                <div class="msg-hall-event">
                                    <h3>Hall Name: ${item.hall}</h3>
                                    <p>Event Name: ${item.event}</p>
                                </div>
                                <div class="msg-time-status">
                                    <p>Time: ${item.time}</p>
                                    <p style="color:white;background-color:${
                                        item.status === 'approved' ? 'green' : 'red'
                                    }">Status: ${item.status}</p>
                                </div>
                                <div class="msg-reason">
                                    <p>Reason: ${item.reason}</p>
                                </div>
                            </div>`;
                    });
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        }

        // Initial fetch when page loads
        document.addEventListener('DOMContentLoaded', fetchNotifications);
    </script>

</body>

</html>