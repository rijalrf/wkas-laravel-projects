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
