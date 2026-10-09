(function () {
    const key = 'smartroom.sidebar.collapsed';
    const toggle = document.getElementById('sidebar-toggle');

    function setCollapsed(collapsed) {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        document.documentElement.classList.toggle('sidebar-collapsed', collapsed);
        try {
            localStorage.setItem(key, collapsed ? '1' : '0');
        } catch (e) {}
    }

    try {
        if (localStorage.getItem(key) === '1') {
            setCollapsed(true);
        }
    } catch (e) {}

    if (toggle && !toggle.dataset.boundSidebarJs) {
        toggle.dataset.boundSidebarJs = 'true';
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const isCollapsed = document.body.classList.contains('sidebar-collapsed') ||
                                document.documentElement.classList.contains('sidebar-collapsed');
            setCollapsed(!isCollapsed);
        });
    }
})();
