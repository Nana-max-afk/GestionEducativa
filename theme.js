(function () {
    const STORAGE_KEY = 'app-theme';
    const root = document.documentElement;
    const body = document.body;

    if (window.__themeScriptInitialized) {
        return;
    }
    window.__themeScriptInitialized = true;

    const getPreferredTheme = function () {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    const getStoredTheme = function () {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            return stored === 'dark' || stored === 'light' ? stored : null;
        } catch (error) {
            return null;
        }
    };

    const updateToggleState = function (theme) {
        const toggles = document.querySelectorAll('.theme-toggle');
        toggles.forEach(function (toggle) {
            const icon = toggle.querySelector('i');
            const label = toggle.querySelector('span');
            if (icon) {
                icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
            if (label) {
                label.textContent = theme === 'dark' ? 'Modo claro' : 'Modo oscuro';
            }
        });
    };

    const applyTheme = function (theme) {
        const nextTheme = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-theme', nextTheme);
        if (body) {
            body.setAttribute('data-theme', nextTheme);
        }
        try {
            localStorage.setItem(STORAGE_KEY, nextTheme);
        } catch (error) {
            // Ignore storage errors in private mode or restricted environments.
        }
        updateToggleState(nextTheme);
    };

    const initializeTheme = function () {
        const currentTheme = root.getAttribute('data-theme') || getStoredTheme() || getPreferredTheme();
        applyTheme(currentTheme);

        document.querySelectorAll('.theme-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(nextTheme);
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeTheme);
    } else {
        initializeTheme();
    }

    window.applyTheme = applyTheme;
})();
