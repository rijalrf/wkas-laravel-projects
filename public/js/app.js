document.addEventListener('DOMContentLoaded', () => {
    // Dropdown toggle logic for three-dot menus
    document.addEventListener('click', (e) => {
        const dropdownToggle = e.target.closest('.action-dropdown-btn');
        if (dropdownToggle) {
            e.stopPropagation();
            const dropdown = dropdownToggle.nextElementSibling;
            // Close all other dropdowns
            document.querySelectorAll('.action-dropdown-menu').forEach(menu => {
                if (menu !== dropdown) menu.classList.remove('show');
            });
            dropdown.classList.toggle('show');
        } else {
            // Clicked outside dropdown
            document.querySelectorAll('.action-dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    // Modal Close Logic
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.closest('.modal-close') || e.target.closest('.btn-close-modal')) {
                modal.classList.remove('show');
            }
        });
    });

    // Theme Toggle Logic
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const isDark = document.body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeToggleIcon(isDark);
        });
    }

    // Initialize Theme on Load
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
        updateThemeToggleIcon(true);
    } else {
        document.body.classList.remove('dark-theme');
        updateThemeToggleIcon(false);
    }

    function updateThemeToggleIcon(isDark) {
        const sunIcon = document.getElementById('theme-toggle-sun');
        const moonIcon = document.getElementById('theme-toggle-moon');
        if (sunIcon && moonIcon) {
            if (isDark) {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            } else {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            }
        }
    }
});

// Helper functions for modals
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}
