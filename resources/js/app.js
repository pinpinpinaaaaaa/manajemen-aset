import './bootstrap';

window.toggleSidebar = function () {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const menuIcon = document.getElementById('menu-icon');

    if (!sidebar || !menuIcon) return;

    if (window.innerWidth < 1024) {
        // Mobile: fixed overlay mode
        const isOpen = sidebar.classList.contains('mobile-open');
        sidebar.classList.toggle('mobile-open', !isOpen);
        if (overlay) overlay.classList.toggle('active', !isOpen);
        menuIcon.classList.toggle('fa-xmark', !isOpen);
        menuIcon.classList.toggle('fa-bars', isOpen);
    } else {
        // Desktop: collapse/expand dalam flex flow
        sidebar.classList.toggle('collapsed');
        const isCollapsed = sidebar.classList.contains('collapsed');
        menuIcon.classList.toggle('fa-xmark', !isCollapsed);
        menuIcon.classList.toggle('fa-bars', isCollapsed);
    }
};

// Tutup sidebar mobile saat klik overlay
window.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('sidebar-overlay');
    if (!overlay) return;
    overlay.addEventListener('click', () => {
        const sidebar = document.querySelector('.sidebar');
        const menuIcon = document.getElementById('menu-icon');
        sidebar?.classList.remove('mobile-open');
        overlay.classList.remove('active');
        if (menuIcon) {
            menuIcon.classList.remove('fa-xmark');
            menuIcon.classList.add('fa-bars');
        }
    });
});

// ========== SUBMENU TOGGLE ==========

// Untuk Sarana Prasarana
window.openSaranaSubmenu = function (button) {
    const submenu = button.nextElementSibling;
    const icon = button.querySelector('.submenu-icon');

    if (submenu.classList.contains('show')) {
        submenu.classList.remove('show');
        icon.classList.remove('expanded');
    } else {
        submenu.classList.add('show');
        icon.classList.add('expanded');
    }
};

// Untuk Gudang
window.openGudangSubmenu = function (button) {
    const submenu = button.nextElementSibling;
    const icon = button.querySelector('.submenu-icon');

    if (submenu.classList.contains('show')) {
        submenu.classList.remove('show');
        icon.classList.remove('expanded');
    } else {
        submenu.classList.add('show');
        icon.classList.add('expanded');
    }
};

// Untuk submenu Laporan
window.toggleSubmenu = function (name, button) {
    const submenu = document.getElementById(`${name}-submenu`);
    const icon = button.querySelector('.submenu-icon');

    if (!submenu) return;

    if (submenu.classList.contains('show')) {
        submenu.classList.remove('show');
        icon.classList.remove('expanded');
    } else {
        submenu.classList.add('show');
        icon.classList.add('expanded');
    }
};


// Klik di luar sidebar untuk menutup (opsional)
/* document.addEventListener('click', function (e) {
    const sidebar = document.querySelector('.sidebar');
    const menuButton = document.querySelector('.menu-toggle');
    const menuIcon = document.getElementById('menu-icon');

    if (!sidebar || !menuButton) return;

    const clickedOutside = !sidebar.contains(e.target) && !menuButton.contains(e.target);
    if (clickedOutside && !sidebar.classList.contains('collapsed')) {
        sidebar.classList.add('collapsed');
        menuIcon.classList.remove('fa-xmark');
        menuIcon.classList.add('fa-bars');
    }
});
*/

// ========== AUTO OPEN SUBMENU SESUAI HALAMAN ==========
window.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;

    // Sarana Prasarana submenu
    if (
        path.includes('/gedung') ||
        path.includes('/ruangan') ||
        path.includes('/apar') ||
        path.includes('/aset/sarana')
    ) {
        const saranaSubmenu = document.getElementById('ruangan-submenu');
        const saranaButton = saranaSubmenu?.previousElementSibling;
        const saranaIcon = saranaButton?.querySelector('.submenu-icon');

        saranaSubmenu?.classList.add('show');
        saranaIcon?.classList.add('expanded');
    }

    // Gudang submenu
    if (
        path.includes('/gudang') ||
        path.includes('/atk') ||
        path.includes('/rumah-tangga')
    ) {
        const gudangSubmenu = document.getElementById('gudang-submenu');
        const gudangButton = gudangSubmenu?.previousElementSibling;
        const gudangIcon = gudangButton?.querySelector('.submenu-icon');

        gudangSubmenu?.classList.add('show');
        gudangIcon?.classList.add('expanded');
    }

    // Laporan submenu
    if (
        path.includes('/maintenance') ||
        path.includes('/laporan')
    ) {
        const laporanSubmenu = document.getElementById('laporan-submenu');
        const laporanButton = laporanSubmenu?.previousElementSibling;
        const laporanIcon = laporanButton?.querySelector('.submenu-icon');

        laporanSubmenu?.classList.add('show');
        laporanIcon?.classList.add('expanded');
    }
});
 

// ========== FILTER SEARCH ==========
window.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    searchInput.addEventListener('keyup', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#cardsView .room-card, #tableView tbody tr').forEach(el => {
            const name = el.getAttribute('data-name');
            el.style.display = name && name.includes(query) ? '' : 'none';
        });
    });
});

// ========== TOGGLE VIEW ==========
window.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;
    const cardsView = document.getElementById('cardsView');
    const tableView = document.getElementById('tableView');
    const cardsBtn = document.getElementById('cardsViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');

    if (!cardsView || !tableView) return;

    // Default mode berdasarkan halaman
    if (
        path.includes('/ruangan/') || 
        path.includes('/apar') || 
        path.includes('/aset')
    ) {
        // 🔹 Default: TABLE mode
        cardsView.style.display = 'none';
        tableView.style.display = 'block';
        if (tableBtn) tableBtn.classList.add('active');
        if (cardsBtn) cardsBtn.classList.remove('active');
    } else {
        // 🔹 Default: CARDS mode
        cardsView.style.display = 'grid';
        tableView.style.display = 'none';
        if (cardsBtn) cardsBtn.classList.add('active');
        if (tableBtn) tableBtn.classList.remove('active');
    }
});
