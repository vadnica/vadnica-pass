document.addEventListener('DOMContentLoaded', () => {
    // Mobile Sidebar Toggle (logged-in users)
    const mobileToggle = document.getElementById('mobile-toggle');
    const navbarOverlay = document.getElementById('navbar-overlay');
    const sidebar = document.querySelector('.app-sidebar');

    if (mobileToggle && navbarOverlay && sidebar) {
        const toggleSidebar = function() {
            sidebar.classList.toggle('active');
            navbarOverlay.classList.toggle('active');
        };

        mobileToggle.addEventListener('click', toggleSidebar);
        navbarOverlay.addEventListener('click', toggleSidebar);
    }

    // Auth Dropdown Toggle (guest/logged-out users)
    const authMenuToggle = document.getElementById('auth-menu-toggle');
    const authDropdownMenu = document.getElementById('auth-dropdown-menu');

    if (authMenuToggle && authDropdownMenu) {
        const toggleAuthMenu = function(e) {
            e.stopPropagation();
            const isOpen = authDropdownMenu.classList.toggle('active');
            authMenuToggle.classList.toggle('active', isOpen);
            if (navbarOverlay) {
                navbarOverlay.classList.toggle('active', isOpen);
            }
        };

        const closeAuthMenu = function() {
            authDropdownMenu.classList.remove('active');
            authMenuToggle.classList.remove('active');
            if (navbarOverlay && (!sidebar || !sidebar.classList.contains('active'))) {
                navbarOverlay.classList.remove('active');
            }
        };

        authMenuToggle.addEventListener('click', toggleAuthMenu);

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!authDropdownMenu.contains(e.target) && !authMenuToggle.contains(e.target)) {
                closeAuthMenu();
            }
        });

        // Close dropdown on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAuthMenu();
            }
        });

        // Close dropdown when overlay is clicked
        if (navbarOverlay) {
            navbarOverlay.addEventListener('click', function() {
                closeAuthMenu();
            });
        }

        // Close dropdown when a navigation link inside is clicked
        authDropdownMenu.querySelectorAll('.auth-dropdown-list a').forEach(function(link) {
            link.addEventListener('click', function() {
                closeAuthMenu();
            });
        });
    }

    // Close sidebar when clicking a link (mobile)
    document.querySelectorAll('.sidebar-nav a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024 && sidebar && navbarOverlay) {
                sidebar.classList.remove('active');
                navbarOverlay.classList.remove('active');
            }
        });
    });

    // Theme Toggle
    const themeToggles = [document.getElementById('theme-toggle'), document.getElementById('theme-toggle-mobile')];
    
    themeToggles.forEach(themeToggle => {
        if (themeToggle) {
            const themeIcon = themeToggle.querySelector('.material-icons-outlined');
            
            const updateIcon = (theme) => {
                if (!themeIcon) return;
                themeIcon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
            };

            // Initial icon update
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateIcon(currentTheme);

            themeToggle.addEventListener('click', () => {
                const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                // Update all icons
                themeToggles.forEach(toggle => {
                    if (toggle) {
                        const icon = toggle.querySelector('.material-icons-outlined');
                        if (icon) icon.textContent = newTheme === 'dark' ? 'light_mode' : 'dark_mode';
                    }
                });
            });
        }
    });
});
