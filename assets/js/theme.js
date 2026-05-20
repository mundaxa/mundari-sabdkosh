/* ============================================================
   MUNDARI SABDKOSH — Theme System
   ============================================================ */

const ThemeManager = {
    key: 'mundari-theme',
    mediaQuery: window.matchMedia('(prefers-color-scheme: dark)'),

    init() {
        const saved = this.getSaved();
        if (saved) {
            this.set(saved, false);
        } else {
            this.set(this.mediaQuery.matches ? 'dark' : 'light', false);
        }
        this.mediaQuery.addEventListener('change', (e) => {
            if (!localStorage.getItem(this.key)) {
                this.set(e.matches ? 'dark' : 'light', false);
            }
        });
        document.querySelectorAll('.theme-switch').forEach(btn => {
            btn.addEventListener('click', () => this.toggle());
        });
    },

    getSaved() {
        return localStorage.getItem(this.key);
    },

    getCurrent() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    },

    set(theme, persist = true) {
        document.documentElement.classList.remove('dark', 'light');
        document.documentElement.classList.add(theme);
        if (persist) {
            localStorage.setItem(this.key, theme);
        }
        const icon = document.querySelector('.theme-switch i');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }
        document.documentElement.style.colorScheme = theme;
    },

    toggle() {
        const current = this.getCurrent();
        this.set(current === 'dark' ? 'light' : 'dark');
    }
};

document.addEventListener('DOMContentLoaded', () => ThemeManager.init());
