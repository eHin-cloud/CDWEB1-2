import './bootstrap';
import './quan-ly-nha-tro';
import './rentry';

const THEME_STORAGE_KEY = 'smartroom_theme';
const DUO_STORAGE_KEY = 'smartroom_accent_duo';

function applyThemeMode(mode) {
    const isLight = mode === 'light';
    document.documentElement.classList.toggle('theme-light', isLight);
    document.body?.classList.toggle('theme-light', isLight);

    // Remove legacy theme classes
    document.documentElement.classList.remove('theme-fire', 'theme-ice', 'theme-mint', 'theme-violet');
    document.body?.classList.remove('theme-fire', 'theme-ice', 'theme-mint', 'theme-violet');

    document.querySelectorAll('[data-theme-icon], #theme-toggle-icon').forEach((icon) => {
        if (!icon.classList.contains('theme-switch-icon')) {
            icon.classList.toggle('fa-sun', isLight);
            icon.classList.toggle('fa-moon', !isLight);
        }
    });

    document.querySelectorAll('[data-theme-label]').forEach((label) => {
        label.textContent = isLight ? 'Sáng' : 'Tối';
    });

    document.querySelectorAll('[data-theme-switch]').forEach((button) => {
        button.classList.toggle('is-light', isLight);
        button.setAttribute('aria-pressed', isLight ? 'true' : 'false');
        button.setAttribute('title', isLight ? 'Chế độ Sáng ☀️ (Click đổi sang Tối 🌙)' : 'Chế độ Tối 🌙 (Click đổi sang Sáng ☀️)');
    });
}

function applyDuoTheme(duoKey) {
    const validDuo = ['violet-mint', 'ice-fire', 'pink-cyan', 'amber-blue'];
    const activeDuo = validDuo.includes(duoKey) ? duoKey : 'violet-mint';
    document.documentElement.setAttribute('data-duo', activeDuo);
    document.body?.setAttribute('data-duo', activeDuo);

    // Update active dot in accent dropdown
    const dot = document.getElementById('current-accent-dot');
    if (dot) {
        dot.style.background = 'var(--duo-gradient)';
    }

    // Update active state in double-color buttons
    document.querySelectorAll('.duo-color-btn').forEach(btn => {
        const btnDuo = btn.getAttribute('data-duo');
        btn.classList.toggle('ring-2', btnDuo === activeDuo);
        btn.classList.toggle('ring-white/60', btnDuo === activeDuo);
        btn.classList.toggle('border-white/40', btnDuo === activeDuo);
    });
}

function setDuoTheme(duoKey) {
    localStorage.setItem(DUO_STORAGE_KEY, duoKey);
    applyDuoTheme(duoKey);
    const dropdown = document.getElementById('admin-accent-dropdown');
    if (dropdown) dropdown.classList.add('hidden');
}

function toggleAccentDropdown(event) {
    if (event) event.stopPropagation();
    const dropdown = document.getElementById('admin-accent-dropdown');
    if (dropdown) dropdown.classList.toggle('hidden');
}

window.applyThemeMode = applyThemeMode;
window.applyDuoTheme = applyDuoTheme;
window.setDuoTheme = setDuoTheme;
window.toggleAccentDropdown = toggleAccentDropdown;

window.toggleThemeMode = function toggleThemeMode() {
    const isCurrentlyLight = document.body?.classList.contains('theme-light') || document.documentElement.classList.contains('theme-light');
    const nextMode = isCurrentlyLight ? 'dark' : 'light';
    localStorage.setItem(THEME_STORAGE_KEY, nextMode);
    localStorage.setItem('renty_theme_mode', nextMode);

    // Add flipping animation class to body
    document.body?.classList.remove('theme-flipping');
    if (document.body) {
        void document.body.offsetWidth;
        document.body.classList.add('theme-flipping');
    }

    // Add animating class to switches
    document.querySelectorAll('[data-theme-switch]').forEach((button) => {
        button.classList.remove('is-animating');
        void button.offsetWidth;
        button.classList.add('is-animating');
    });

    applyThemeMode(nextMode);
};

document.addEventListener('click', (e) => {
    const container = document.getElementById('admin-accent-picker-container');
    const dropdown = document.getElementById('admin-accent-dropdown');
    if (container && dropdown && !container.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    let saved = localStorage.getItem(THEME_STORAGE_KEY) || localStorage.getItem('renty_theme_mode') || 'dark';
    if (saved === 'fire' || saved === 'ice') saved = 'dark';
    applyThemeMode(saved);

    const savedDuo = localStorage.getItem(DUO_STORAGE_KEY) || 'violet-mint';
    applyDuoTheme(savedDuo);
});
