(function () {
    const STORAGE_KEY = 'whatsai-theme';

    function getPreferredTheme() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) return stored;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        localStorage.setItem(STORAGE_KEY, theme);
        const btn = document.getElementById('themeToggle');
        if (btn) btn.textContent = theme === 'dark' ? '\u2600\uFE0F' : '\uD83C\uDF19';
    }

    function toggleTheme() {
        const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        if (menu) menu.classList.toggle('open');
    }

    applyTheme(getPreferredTheme());

    document.addEventListener('click', function (e) {
        if (e.target.closest('#themeToggle')) toggleTheme();
        if (e.target.closest('#menuToggle')) toggleMobileMenu();
    });
})();
