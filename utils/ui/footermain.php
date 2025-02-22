<!-- utils/ui/footer.php -->

        <!-- Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
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
    <!-- SheetJS Library for Excel Import -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>   
  

</body>
</html>
