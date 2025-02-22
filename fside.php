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
        background-image: url('image/pattern_h.png');
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


    /* Gradient Colors */
    .icon-basic {
        background: linear-gradient(45deg, #4facfe, #00f2fe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .icon-academic {
        background: linear-gradient(45deg, rgb(66, 245, 221), #00d948);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .icon-feedback {
        background: #E91E63;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .icon-password {
        background: #607D8B;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }
    .icon-rhwallet {
        background: linear-gradient(45deg, #FF9800, #F44336);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }
    .icon-mywallet {
        background: linear-gradient(45deg,rgb(203, 5, 243),rgb(134, 2, 101));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }
   
    .icon-mentor {
    background: linear-gradient(45deg, #7b1fa2,rgb(101, 9, 141));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
}

.icon-research {
    background: linear-gradient(45deg, #FF9800, #F44336);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }
    
    .icon-hall {
    background: linear-gradient(45deg, #FF9800, #F44336);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>

<div class="mobile-overlay" id="mobileOverlay"></div>
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="image/mkce.png" alt="College Logo">
        <img class='s_logo' src="image/mkce_s.png" alt="College Logo">
    </div>

    <div class="menu">
        <a href="main.php" class="menu-item">
            <i class="fas fa-home text-primary"></i>
            <span>Dashboard</span>
        </a>

        <a href="profile.php" class="menu-item">
        <i class="fas fa-user text-warning"></i>
            <span>Profile</span>
        </a>

        <div class="menu-item has-submenu">
        <i class="fas fa-book text-success"></i>
            <span>Service Book</span>
        </div>
        <div class="submenu">
            <a href="basic.php" class="menu-item">
            <i class="fas fa-id-card icon-basic"></i> 
            <span>Basic Details</span>
            </a>
            <a href="academic.php" class="menu-item">
            <i class="fas fa-graduation-cap icon-academic"></i>
            <span>Academic Details</span>
            </a>
            <a href="fresearch.php" class="menu-item">                 
                <i class="fas fa-flask   icon-research"></i>                 
                <span>Research</span>             
            </a>

        </div>
        
            <a href="wallet.php" class="menu-item">
                <i class="fas fa-wallet icon-mywallet"></i>
                <span>My Wallet</span>
            </a>
            <div class="menu-item has-submenu">
        <i class="fas fa-book text-success"></i>
            <span>Academics</span>
        </div>
        <div class="submenu">
            <a href="advisorDashboard.php" class="menu-item">
            <i class="fas fa-id-card icon-basic"></i> 
            <span>Advisor Academics</span>
            </a>
            <a href="facultyDashboard.php" class="menu-item">
            <i class="fas fa-graduation-cap icon-academic"></i>
            <span>Course Faculty</span>
            </a>
        </div>

        <a href="fview.php" class="menu-item">
            <i class="fas fa-user-graduate text-primary"></i>
            <span>Student</span>
        </a>
        <a href="hall.php" class="menu-item">
        <i class="fas fa-building icon-hall"></i>  
            <span>Hall Booking</span>
        </a>
        <?php
        if ($s == 1141014) {
        ?>
        <a href="cms_eo.php" class="menu-item">
            <i class="fas fa-exclamation-triangle icon-feedback"></i>
            <span>Grievances</span>
        </a>
        <?php
        }
        ?>
        <?php
        if ($s == 1112001) {

        ?>
        <a href="cms_manager.php" class="menu-item">
            <i class="fas fa-exclamation-triangle icon-feedback"></i>
            <span>Grievances</span>
        </a>
        <a href="fhall.php" class="menu-item">
            <i class="fas fa-exclamation-triangle icon-feedback"></i>
            <span>Hall Approve</span>
        </a>
        <?php
        }
        ?>
        <?php

        $infraquery = "SELECT * FROM faculty WHERE role='infra' AND id='$s'";
        $infraqueryrun = mysqli_query($db, $infraquery);
        ?>
        <?php
        if (mysqli_num_rows($infraqueryrun) > 0) {

        ?>
        <a href="cms_infra.php" class="menu-item">
            <i class="fas fa-exclamation-triangle icon-feedback"></i>
            <span>Grievances</span>
        </a>
        <?php
        }
        ?>

        <?php
        if ($s != 1141014 && $s != 1112001 && mysqli_num_rows($infraqueryrun) == 0) {
        ?>
        <a href="cms_faculty.php" class="menu-item">
            <i class="fas fa-exclamation-triangle icon-feedback"></i>
            <span>Grievances</span>
        </a>
        <?php
        }
        ?>

         <a href="pwd.php" class="menu-item">
            <i class="fas fa-key icon-password"></i>
            <span>Change Password</span>
        </a>
    </div>
</div>
