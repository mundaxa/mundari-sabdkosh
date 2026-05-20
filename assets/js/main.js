/* ============================================================
   MUNDARI SABDKOSH — Main JavaScript
   ============================================================ */

const App = {
    init() {
        this.initSidebar();
        this.initDropdowns();
        this.initModals();
        this.initMaps();
        this.initLazyLoading();
        this.initTooltips();
        this.initMobileOverlay();
        this.initWordOfDayAudio();
    },

    /* ---- Sidebar ---- */
    initSidebar() {
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

        const hamburger = document.querySelector('.hamburger-menu');
        const overlay = document.querySelector('.mobile-overlay');
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

        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.getAttribute('href') && window.location.pathname.endsWith(item.getAttribute('href'))) {
                item.classList.add('active');
            }
        });
    },

    /* ---- Dropdowns ---- */
    initDropdowns() {
        document.addEventListener('click', (e) => {
            const dropdown = e.target.closest('.dropdown');
            document.querySelectorAll('.dropdown.active').forEach(d => {
                if (d !== dropdown) d.classList.remove('active');
            });
            if (dropdown) {
                dropdown.classList.toggle('active');
            }
        });
    },

    /* ---- Modals ---- */
    initModals() {
        document.querySelectorAll('[data-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.querySelector(btn.dataset.modal);
                if (target) target.classList.add('active');
            });
        });
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) overlay.classList.remove('active');
            });
            const closeBtn = overlay.querySelector('.modal-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => overlay.classList.remove('active'));
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
            }
        });
    },

    /* ---- Interactive Maps ---- */
    initMaps() {
        const mapContainers = document.querySelectorAll('.interactive-map');
        mapContainers.forEach(container => {
            const canvas = document.createElement('canvas');
            canvas.width = container.offsetWidth;
            canvas.height = container.offsetHeight;
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.className = 'map-container';
            container.innerHTML = '';
            container.appendChild(canvas);

            const ctx = canvas.getContext('2d');
            const regions = container.dataset.regions
                ? JSON.parse(container.dataset.regions)
                : this.getDefaultRegions();

            this.drawMap(ctx, canvas.width, canvas.height, regions, container);

            canvas.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                this.checkRegionHover(ctx, canvas.width, canvas.height, regions, x, y, container);
            });
        });
    },

    getDefaultRegions() {
        return [
            { name: 'Jharkhand', x: 0.55, y: 0.3, r: 40, color: '#4f7eff', label: 'Jharkhand' },
            { name: 'Odisha', x: 0.5, y: 0.55, r: 35, color: '#7c3aed', label: 'Odisha' },
            { name: 'West Bengal', x: 0.7, y: 0.25, r: 30, color: '#06b6d4', label: 'West Bengal' },
            { name: 'Assam', x: 0.8, y: 0.1, r: 25, color: '#22c55e', label: 'Assam' },
            { name: 'Bihar', x: 0.5, y: 0.15, r: 25, color: '#f59e0b', label: 'Bihar' },
            { name: 'Chhattisgarh', x: 0.35, y: 0.45, r: 30, color: '#ef4444', label: 'Chhattisgarh' }
        ];
    },

    drawMap(ctx, w, h, regions, container) {
        const isDark = document.documentElement.classList.contains('dark');
        ctx.clearRect(0, 0, w, h);

        const gradient = ctx.createRadialGradient(w/2, h/2, 0, w/2, h/2, w/2);
        gradient.addColorStop(0, isDark ? 'rgba(79,126,255,0.05)' : 'rgba(79,126,255,0.03)');
        gradient.addColorStop(1, 'transparent');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, w, h);

        regions.forEach(region => {
            const x = region.x * w;
            const y = region.y * h;
            const r = region.r;

            ctx.beginPath();
            ctx.arc(x, y, r, 0, Math.PI * 2);
            ctx.fillStyle = region.color + '20';
            ctx.fill();
            ctx.strokeStyle = region.color + '40';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            ctx.font = '11px Inter, sans-serif';
            ctx.fillStyle = isDark ? 'rgba(255,255,255,0.6)' : 'rgba(0,0,0,0.6)';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(region.label, x, y);

            ctx.beginPath();
            ctx.arc(x, y, 3, 0, Math.PI * 2);
            ctx.fillStyle = region.color;
            ctx.fill();
        });

        ctx.font = '13px Inter, sans-serif';
        ctx.fillStyle = isDark ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.3)';
        ctx.textAlign = 'center';
        ctx.fillText('Tribal Regions of Eastern India', w/2, h - 20);

        container._regions = regions;
    },

    checkRegionHover(ctx, w, h, regions, mx, my, container) {
        const hovered = regions.find(region => {
            const x = region.x * w;
            const y = region.y * h;
            return Math.sqrt((mx - x) ** 2 + (my - y) ** 2) < region.r;
        });

        if (hovered && container._hovered !== hovered.name) {
            container._hovered = hovered.name;
            container.style.cursor = 'pointer';
            this.drawMap(ctx, w, h, regions, container);

            const x = hovered.x * w;
            const y = hovered.y * h;

            ctx.beginPath();
            ctx.arc(x, y, hovered.r, 0, Math.PI * 2);
            ctx.fillStyle = hovered.color + '30';
            ctx.fill();
            ctx.strokeStyle = hovered.color;
            ctx.lineWidth = 2;
            ctx.stroke();

            ctx.font = '11px Inter, sans-serif';
            ctx.fillStyle = document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.8)' : 'rgba(0,0,0,0.8)';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(hovered.label, x, y);
        } else if (!hovered) {
            container._hovered = null;
            container.style.cursor = 'default';
        }
    },

    /* ---- Lazy Loading ---- */
    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        observer.unobserve(img);
                    }
                });
            }, { rootMargin: '200px' });

            document.querySelectorAll('img[data-src]').forEach(img => observer.observe(img));
        }
    },

    /* ---- Tooltips ---- */
    initTooltips() {
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            let tooltip = null;
            el.addEventListener('mouseenter', () => {
                if (window.innerWidth <= 768) return;
                tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.textContent = el.dataset.tooltip;
                document.body.appendChild(tooltip);

                const rect = el.getBoundingClientRect();
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 8) + 'px';
                tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
            });
            el.addEventListener('mouseleave', () => {
                if (tooltip) { tooltip.remove(); tooltip = null; }
            });
        });
    },

    /* ---- Mobile Overlay ---- */
    initMobileOverlay() {
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.querySelector('.mobile-overlay');
                if (sidebar) sidebar.classList.remove('mobile-open');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    },

    /* ---- Word of Day Audio ---- */
    initWordOfDayAudio() {
        document.querySelectorAll('.wod-audio-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const audioSrc = this.dataset.audio;
                if (!audioSrc) return;
                const audio = new Audio(audioSrc);
                audio.play().catch(() => {});
                this.classList.add('playing');
                audio.addEventListener('ended', () => {
                    this.classList.remove('playing');
                });
            });
        });
    }
};

document.addEventListener('DOMContentLoaded', () => App.init());
