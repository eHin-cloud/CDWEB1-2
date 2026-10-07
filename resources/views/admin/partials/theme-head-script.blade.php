<script>
    (function() {
        try {
            var initialSavedTheme = localStorage.getItem('smartroom_theme') || localStorage.getItem('renty_theme_mode') || 'dark';
            var isLight = (initialSavedTheme === 'light');
            document.documentElement.classList.remove('theme-fire', 'theme-ice');
            document.documentElement.classList.toggle('theme-light', isLight);
            
            var savedDuo = localStorage.getItem('smartroom_accent_duo') || 'violet-mint';
            document.documentElement.setAttribute('data-duo', savedDuo);
        } catch (e) {}
    })();
</script>

