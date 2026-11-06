/**
 * AKC Modern Scroll Reveal & Smooth Scrolling Controller
 * Features:
 * - Full-site smooth scrolling with anchor support
 * - GPU-accelerated IntersectionObserver scroll reveals
 * - Staggered text & card cascades matching the homepage
 * - Dynamic tab & filter support
 */
(function () {
    'use strict';

    // 1. Smooth Scrolling for Internal Anchors
    function initSmoothScroll() {
        document.addEventListener('click', function (e) {
            const anchor = e.target.closest('a[href^="#"]');
            if (!anchor) return;

            const targetId = anchor.getAttribute('href');
            if (!targetId || targetId === '#' || targetId.length <= 1) return;

            try {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    if (window.history && window.history.pushState) {
                        window.history.pushState(null, null, targetId);
                    }
                }
            } catch (err) {
                // Invalid selector fallback
            }
        });
    }

    // 2. Auto-Discovery for Site-Wide Elements (Seamless fallback)
    function autoTagSiteElements() {
        // Auto-tag section headers across pages for staggered text-reveal
        const headers = document.querySelectorAll(
            '.section-header:not(.reveal):not(.no-reveal), ' +
            '.categories-section-header:not(.reveal), ' +
            '.tc-courses-header:not(.reveal), ' +
            '.voices-header:not(.reveal), ' +
            '.who-we-are-content:not(.reveal), ' +
            '.fc-header:not(.reveal)'
        );
        headers.forEach(h => h.classList.add('reveal'));

        // Auto-tag card grids for staggered child reveals
        const grids = document.querySelectorAll(
            '.values-grid:not(.reveal-group), ' +
            '.vm-grid:not(.reveal-group), ' +
            '.team-grid:not(.reveal-group), ' +
            '.pr-products-grid:not(.reveal-group), ' +
            '.pr-tests-grid:not(.reveal-group), ' +
            '.pr-standards-grid:not(.reveal-group), ' +
            '.categories-grid:not(.reveal-group), ' +
            '.curriculum-grid:not(.reveal-group), ' +
            '.facilities-grid:not(.reveal-group), ' +
            '.advantage-section > .container > div[style*="grid"]:not(.reveal-group)'
        );

        grids.forEach(g => {
            g.classList.add('reveal-group');
            Array.from(g.children).forEach((child, index) => {
                if (!child.classList.contains('reveal-child')) {
                    child.classList.add('reveal-child');
                    child.style.transitionDelay = `${(index % 6) * 0.08 + 0.05}s`;
                }
            });
        });

        // Split 2-column feature grids (left slide & right slide)
        const splitGrids = document.querySelectorAll(
            '.trusted-grid:not(.reveal-group), ' +
            '.pr-overview-inner:not(.reveal-group), ' +
            '.advantage-grid:not(.reveal-group)'
        );

        splitGrids.forEach(sg => {
            sg.classList.add('reveal-group');
            const cols = Array.from(sg.children);
            if (cols.length === 2) {
                if (!cols[0].classList.contains('reveal-left') && !cols[0].classList.contains('reveal')) {
                    cols[0].classList.add('reveal-left');
                }
                if (!cols[1].classList.contains('reveal-right') && !cols[1].classList.contains('reveal')) {
                    cols[1].classList.add('reveal-right');
                }
            }
        });
    }

    // 3. Scroll Reveal Engine
    function initScrollReveal() {
        autoTagSiteElements();

        const revealElements = document.querySelectorAll(
            '.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-fade, .reveal-text, .text-reveal, .reveal-group'
        );

        if (revealElements.length === 0) return;

        // Fallback for older browsers
        if (!('IntersectionObserver' in window)) {
            revealElements.forEach(el => el.classList.add('active'));
            return;
        }

        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -40px 0px',
            threshold: 0.1
        };

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        revealElements.forEach(el => {
            const rect = el.getBoundingClientRect();
            // If already in viewport on load, activate immediately to prevent flashing
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                el.classList.add('active');
            } else {
                revealObserver.observe(el);
            }
        });
    }

    // 4. Lifecycle Binding
    initSmoothScroll();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollReveal);
    } else {
        initScrollReveal();
    }

    // Re-check on dynamic tabs/content changes
    document.addEventListener('click', function (e) {
        if (e.target.closest('.tab-btn, .category-tab, .filter-btn')) {
            setTimeout(initScrollReveal, 150);
        }
    });
})();
