/* ============================================================
   MUNDARI SABDKOSH — Animations & UI Effects
   ============================================================ */

const Animations = {
    init() {
        this.initScrollReveal();
        this.initSkeletonLoaders();
        this.initCounterAnimation();
        this.initParticles();
        this.initSmoothNav();
    },

    initScrollReveal() {
        const elements = document.querySelectorAll('[data-reveal]');
        if (elements.length === 0) return;

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '50px' });
            elements.forEach(el => observer.observe(el));
        } else {
            elements.forEach(el => el.classList.add('revealed'));
        }
    },

    initSkeletonLoaders() {
        document.querySelectorAll('[data-skeleton]').forEach(el => {
            const type = el.dataset.skeleton;
            el.innerHTML = this.getSkeletonHTML(type);
        });
    },

    getSkeletonHTML(type) {
        switch (type) {
            case 'card':
                return `
                    <div class="skeleton skeleton-card"></div>
                    <div class="skeleton skeleton-title"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text" style="width:40%"></div>
                `;
            case 'list':
                return `
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text" style="width:40%"></div>
                `;
            case 'profile':
                return `
                    <div style="display:flex;align-items:center;gap:12px">
                        <div class="skeleton skeleton-avatar"></div>
                        <div style="flex:1">
                            <div class="skeleton skeleton-title"></div>
                            <div class="skeleton skeleton-text" style="width:60%"></div>
                        </div>
                    </div>
                `;
            default:
                return `<div class="skeleton skeleton-text"></div>`;
        }
    },

    initCounterAnimation() {
        const counters = document.querySelectorAll('[data-count]');
        if (counters.length === 0) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.count);
                    const duration = parseInt(el.dataset.duration) || 1500;
                    const suffix = el.dataset.suffix || '';
                    this.animateCounter(el, target, duration, suffix);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(el => observer.observe(el));
    },

    animateCounter(el, target, duration, suffix) {
        const start = 0;
        const startTime = performance.now();

        const update = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(start + (target - start) * eased);
            el.textContent = current.toLocaleString() + suffix;
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                el.textContent = target.toLocaleString() + suffix;
            }
        };
        requestAnimationFrame(update);
    },

    initParticles() {
        const canvas = document.getElementById('hero-particles');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let particles = [];
        const count = 60;

        const resize = () => {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        };
        resize();
        window.addEventListener('resize', resize);

        for (let i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                r: Math.random() * 2 + 0.5,
                alpha: Math.random() * 0.5 + 0.1
            });
        }

        const animate = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const isDark = document.documentElement.classList.contains('dark');
            const color = isDark ? '255,255,255' : '79,126,255';

            particles.forEach((p, i) => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${color},${p.alpha})`;
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[j].x - p.x;
                    const dy = particles[j].y - p.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(${color},${0.05 * (1 - dist / 120)})`;
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        };
        animate();
    },

    initSmoothNav() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }
};

document.addEventListener('DOMContentLoaded', () => Animations.init());
