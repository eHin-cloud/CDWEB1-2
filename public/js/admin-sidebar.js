(function () {
    const key = 'smartroom.sidebar.collapsed';

    function setCollapsed(collapsed) {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        document.documentElement.classList.toggle('sidebar-collapsed', collapsed);
        try {
            localStorage.setItem(key, collapsed ? '1' : '0');
        } catch (e) {}
    }

    function initSidebarToggle() {
        const toggle = document.getElementById('sidebar-toggle');
        if (!toggle) return;

        try {
            if (localStorage.getItem(key) === '1') {
                setCollapsed(true);
            }
        } catch (e) {}

        if (!toggle.dataset.bound) {
            toggle.dataset.bound = 'true';
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const isCollapsed = document.body.classList.contains('sidebar-collapsed') ||
                                    document.documentElement.classList.contains('sidebar-collapsed');
                setCollapsed(!isCollapsed);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarToggle);
    } else {
        initSidebarToggle();
    }
})();
