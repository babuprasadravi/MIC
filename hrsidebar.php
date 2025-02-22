
<style>
    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: var(--dark-bg);
        transition: var(--transition);
        z-index: 1000;
        overflow-y: auto;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        background-image: url('../../image/pattern_h.png');
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }

    .sidebar .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
        color: white;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar .logo img {
        max-height: 90px;
        width: auto;
    }

    .sidebar .s_logo {
        display: none;
    }

    .sidebar.collapsed .logo img {
        display: none;
    }

    .sidebar.collapsed .logo .s_logo {
        display: flex;
        max-height: 50px;
        width: auto;
        align-items: center;
        justify-content: center;
    }

    .sidebar .menu {
        padding: 10px;
    }

    .menu-item {
        padding: 12px 15px;
        color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        cursor: pointer;
        border-radius: 5px;
        margin: 4px 0;
        transition: all 0.3s ease;
        position: relative;
        text-decoration: none;
    }

    .menu-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .menu-item i {
        min-width: 30px;
        font-size: 18px;
    }

    .menu-item span {
        margin-left: 10px;
        transition: all 0.3s ease;
        flex-grow: 1;
    }

    .menu-item.active {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-weight: bold;
    }

    .menu-item.active i {
        color: white;
    }

    .has-submenu::after {
        content: '\f107';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-left: 10px;
        transition: transform 0.3s ease;
    }

    .has-submenu.active::after {
        transform: rotate(180deg);
    }

    .sidebar.collapsed .menu-item span,
    .sidebar.collapsed .has-submenu::after {
        display: none;
    }

    .submenu {
        margin-left: 30px;
        display: none;
        transition: all 0.3s ease;
    }

    .submenu.active {
        display: block;
    }
</style>

<div class="mobile-overlay" id="mobileOverlay"></div>
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="images/mkce.png" alt="College Logo">
        <img class='s_logo' src="images/mkce_s.png" alt="College Logo">
    </div>

    <div class="menu">
        <a href="dash.php" class="menu-item">
            <i class="fas fa-home text-primary"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-item has-submenu">
            <i class="fas fa-users text-danger"></i>
            <span>HR Wallet</span>
        </div>
        <div class="submenu">
            <a href="HR_leaveallocation.php" class="menu-item">
                <i class="fas fa-calendar-plus text-success"></i>
                <span>Leave Allocation</span>
            </a>
            <a href="HR_leaveapproval.php" class="menu-item">
                <i class="fas fa-check-circle text-primary"></i>
                <span>Leave Approval</span>
            </a>

            <a href="HR_atten.php" class="menu-item">
            <i class="fas fa-calendar-alt text-info"></i></i>
                <span>Attendance</span>
            </a>
            <a href="HR_report.php" class="menu-item">
                <i class="fas fa-file-alt text-danger"></i>
                <span>Report</span>
            </a>

            <a href="HR_holiday_allocation.php" class="menu-item">
                <i class="fas fa-calendar-alt text-warning"></i>
                <span>Holidays</span>
            </a>
            <a href="HR_others.php" class="menu-item">
            <i class="fas fa-cogs text-info"></i>
                <span>Others</span>
            </a>
        </div>
    </div>
</div>