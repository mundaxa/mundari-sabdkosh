/* ============================================================
   MUNDARI SABDKOSH — Static Components
   Injects header, sidebar, footer into static HTML pages
   ============================================================ */

const Components = {
    async init() {
        await DB.init();
        this.injectNavbar();
        this.injectSidebar();
        this.injectFooter();
        this.initEventListeners();
    },

    getTheme() {
        return localStorage.getItem('mundari-theme') ||
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    },

    injectNavbar() {
        const user = DB.currentUser;
        const notifCount = 0;
        const theme = this.getTheme();
        document.documentElement.className = theme;

        document.querySelectorAll('.navbar-placeholder').forEach(el => {
            el.outerHTML = `
            <nav class="top-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
                    ${el.dataset.back ? `<a href="${el.dataset.back}" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Back</a>` : ''}
                </div>
                <div class="navbar-search">
                    <input type="text" placeholder="Search words, articles, culture..." id="global-search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="navbar-right">
                    <button class="nav-icon-btn voice-search-btn" title="Voice Search"><i class="fas fa-microphone"></i></button>
                    <button class="nav-icon-btn" title="Notifications" id="notif-btn"><i class="fas fa-bell"></i>${notifCount > 0 ? `<span class="badge">${notifCount}</span>` : ''}</button>
                    <button class="nav-icon-btn" title="Messages"><i class="fas fa-envelope"></i></button>
                    <button class="theme-switch" title="Toggle Theme"><i class="fas ${theme === 'dark' ? 'fa-moon' : 'fa-sun'}"></i></button>
                    ${user ? `
                    <div class="dropdown" id="user-dropdown">
                        <div class="user-profile" onclick="document.getElementById('user-dropdown').classList.toggle('active')">
                            <img src="${DB.avatar(user)}" alt="" class="user-avatar">
                            <span class="user-name">${user.full_name || user.username}</span>
                            <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="profile.html" class="dropdown-item"><i class="fas fa-user di-icon"></i> Profile</a>
                            <a href="bookmarks.html" class="dropdown-item"><i class="fas fa-bookmark di-icon"></i> Bookmarks</a>
                            ${DB.isAdmin() ? `<a href="admin/index.html" class="dropdown-item"><i class="fas fa-shield-alt di-icon"></i> Admin</a>` : ''}
                            <div class="dropdown-divider"></div>
                            <a href="#" onclick="DB.logout();location.reload()" class="dropdown-item"><i class="fas fa-sign-out-alt di-icon"></i> Logout</a>
                        </div>
                    </div>` : `
                    <a href="login.html" class="btn btn-primary btn-sm">Sign In</a>
                    <a href="register.html" class="btn btn-ghost btn-sm">Register</a>`}
                </div>
            </nav>`;
        });
    },

    injectSidebar() {
        document.querySelectorAll('.sidebar-placeholder').forEach(el => {
            const current = window.location.pathname.split('/').pop();
            el.outerHTML = `
            <aside class="sidebar">
                <div class="sidebar-logo">
                    <img src="assets/images/birsa-munda.jpg" alt="Birsa Munda" style="width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                    <div class="logo-text">
                        Mundari Sabdkosh
                        <span class="logo-sub">Tribal Dictionary & Knowledge System</span>
                    </div>
                </div>
                <nav class="sidebar-nav">
                    ${this.renderNavSection('Main', [
                        {label:'Home', icon:'fa-home', link:'index.html', active: current === 'index.html' || current === ''}
                    ])}
                    ${this.renderNavSection('Knowledge', [
                        {label:'Dictionary', icon:'fa-book', link:'dictionary.html', badge:'New'},
                        {label:'Encyclopedia', icon:'fa-globe', link:'encyclopedia.html'},
                        {label:'Culture & Heritage', icon:'fa-dharmachakra', link:'culture.html'},
                        {label:'Archive', icon:'fa-archive', link:'archive.html'}
                    ])}
                    ${this.renderNavSection('Research', [
                        {label:'Research Portal', icon:'fa-flask', link:'research.html'},
                        {label:'Learning Center', icon:'fa-graduation-cap', link:'learning.html'},
                        {label:'Media Library', icon:'fa-photo-video', link:'media.html'},
                        {label:'Maps & Regions', icon:'fa-map-marked-alt', link:'maps.html'},
                        {label:'AI Tools', icon:'fa-robot', link:'ai-tools.html', badge:'AI'}
                    ])}
                    ${this.renderNavSection('Community', [
                        {label:'Community', icon:'fa-users', link:'community.html'},
                        {label:'Discussions', icon:'fa-comments', link:'discussions.html'},
                        {label:'Contributors', icon:'fa-trophy', link:'contributors.html'},
                        {label:'Events & Calendar', icon:'fa-calendar', link:'events.html'}
                    ])}
                    ${this.renderNavSection('Tools', [
                        {label:'Transliteration', icon:'fa-exchange-alt', link:'transliteration.html'},
                        {label:'OCR Scanner', icon:'fa-scanner', link:'ocr.html'},
                        {label:'Developer Portal', icon:'fa-code', link:'developer.html'},
                        {label:'API Docs', icon:'fa-plug', link:'api-docs.html'}
                    ])}
                    ${this.renderNavSection('System', [
                        {label:'Bookmarks', icon:'fa-bookmark', link:'bookmarks.html'},
                        {label:'Reports', icon:'fa-chart-bar', link:'reports.html'},
                        ...(DB.isAdmin() ? [{label:'Admin Panel', icon:'fa-shield-alt', link:'admin/index.html'}] : []),
                        {label:'Settings', icon:'fa-cog', link:'settings.html'}
                    ])}
                </nav>
                <div class="sidebar-footer">
                    <a href="contribute.html" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">
                        <i class="fas fa-plus"></i> <span class="nav-label">Contribute</span>
                    </a>
                </div>
            </aside>`;
        });
    },

    renderNavSection(title, items) {
        return `
        <div class="nav-section">
            <div class="nav-section-title">${title}</div>
            ${items.filter(Boolean).map(item => `
            <a href="${item.link}" class="nav-item ${item.active ? 'active' : ''}" data-tooltip="${item.label}">
                <i class="fas ${item.icon} nav-icon"></i>
                <span class="nav-label">${item.label}</span>
                ${item.badge ? `<span class="nav-badge">${item.badge}</span>` : ''}
            </a>`).join('')}
        </div>`;
    },

    injectFooter() {
        document.querySelectorAll('.footer-placeholder').forEach(el => {
            el.outerHTML = `
            <footer class="content-footer">
                <span>&copy; ${new Date().getFullYear()} Mundari Sabdkosh. All rights reserved.</span>
                <span>Preserving Mundari Language & Culture</span>
            </footer>`;
        });
    },

    initEventListeners() {
        setTimeout(() => {
            const toggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const main = document.querySelector('.main-content');
            const navbar = document.querySelector('.top-navbar');
            if (toggle && sidebar) {
                toggle.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                    if (main) main.classList.toggle('expanded');
                    if (navbar) navbar.classList.toggle('expanded');
                });
            }

            const overlay = document.querySelector('.mobile-overlay');
            const hamburger = document.querySelector('.hamburger-menu');
            if (hamburger && sidebar && overlay) {
                hamburger.addEventListener('click', () => {
                    sidebar.classList.toggle('mobile-open');
                    overlay.classList.toggle('active');
                    document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
                });
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            document.querySelectorAll('.theme-switch').forEach(btn => {
                btn.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.contains('dark');
                    document.documentElement.className = isDark ? 'light' : 'dark';
                    localStorage.setItem('mundari-theme', isDark ? 'light' : 'dark');
                    const icon = btn.querySelector('i');
                    if (icon) icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
                });
            });

            const searchInput = document.getElementById('global-search-input');
            if (searchInput) {
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        const q = searchInput.value.trim();
                        if (q) window.location.href = `dictionary.html?q=${encodeURIComponent(q)}`;
                    }
                });
            }

            window.addEventListener('resize', () => {
                if (window.innerWidth > 768 && sidebar) {
                    sidebar.classList.remove('mobile-open');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }, 100);
    }
};

document.addEventListener('DOMContentLoaded', () => Components.init());
