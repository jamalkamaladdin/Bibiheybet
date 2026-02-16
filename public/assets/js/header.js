/**
 * Bibiheybet.com - Header JS
 * 
 * Hamburger menyu (fullscreen), sticky header, dil dropdown.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initHamburgerMenu();
        initStickyHeader();
        initLangDropdown();
        initMoreDropdown();
    });

    /** Hamburger menyu açma/bağlama (fullscreen) */
    function initHamburgerMenu() {
        var hamburger = document.getElementById('bbHamburger');
        var menu = document.getElementById('bbMobileMenu');
        var closeBtn = document.getElementById('bbMobileMenuClose');

        if (!hamburger || !menu) return;

        function toggleMenu(open) {
            var isOpen = typeof open === 'boolean' ? open : !menu.classList.contains('bb-open');

            menu.classList.toggle('bb-open', isOpen);
            hamburger.classList.toggle('bb-active', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        hamburger.addEventListener('click', function () { toggleMenu(); });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () { toggleMenu(false); });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && menu.classList.contains('bb-open')) {
                toggleMenu(false);
            }
        });
    }

    /** Sticky header (yalnız kompakt header üçün) */
    function initStickyHeader() {
        var header = document.getElementById('bbHeader');
        if (!header || header.dataset.headerType === 'hero') return;

        var scrollThreshold = 80;

        window.addEventListener('scroll', function () {
            var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScroll > scrollThreshold) {
                header.classList.add('bb-header-sticky');
            } else {
                header.classList.remove('bb-header-sticky');
            }
        }, { passive: true });
    }

    /** Dil dropdown */
    function initLangDropdown() {
        var dropdown = document.querySelector('.bb-lang-dropdown');
        var toggle = document.getElementById('bbLangToggle');

        if (!dropdown || !toggle) return;

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = dropdown.classList.contains('bb-open');
            dropdown.classList.toggle('bb-open', !isOpen);
            toggle.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        });

        // Xaricə click ilə bağla
        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('bb-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Escape ilə bağla
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && dropdown.classList.contains('bb-open')) {
                dropdown.classList.remove('bb-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Dil seçimi cookie sync
        var langItems = dropdown.querySelectorAll('.bb-lang-dropdown-item');
        langItems.forEach(function (item) {
            item.addEventListener('click', function () {
                var href = this.getAttribute('href');
                if (href) {
                    var langMatch = href.match(/\/(en|ru|ar|fa)\//);
                    var newLang = langMatch ? langMatch[1] : 'az';
                    if (window.bbApp && window.bbApp.setCookie) {
                        window.bbApp.setCookie('bb_lang', newLang, 365);
                    }
                }
            });
        });
    }

    /** Əlavə menyu dropdown */
    function initMoreDropdown() {
        var dropdown = document.querySelector('.bb-more-dropdown');
        var toggle = document.querySelector('.bb-more-toggle');

        if (!dropdown || !toggle) return;

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = dropdown.classList.contains('bb-open');
            dropdown.classList.toggle('bb-open', !isOpen);
            toggle.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('bb-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && dropdown.classList.contains('bb-open')) {
                dropdown.classList.remove('bb-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
})();
