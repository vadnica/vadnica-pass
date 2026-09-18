document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle
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
            const currentTheme = document.documentElement.getAttribute('data-theme');
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
