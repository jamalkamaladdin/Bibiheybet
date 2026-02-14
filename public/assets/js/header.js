/**
 * Bibiheybet.com - Header JS
 * 
 * Hamburger menyu, sticky header, dil switch.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initHamburgerMenu();
        initStickyHeader();
        initLangSwitch();
    });

    /** Hamburger menyu açma/bağlama */
    function initHamburgerMenu() {
        var hamburger = document.getElementById('bbHamburger');
        var menu = document.getElementById('bbMobileMenu');
        var overlay = document.getElementById('bbMobileOverlay');
        var closeBtn = document.getElementById('bbMobileMenuClose');

        if (!hamburger || !menu) return;

        function toggleMenu(open) {
            var isOpen = typeof open === 'boolean' ? open : !menu.classList.contains('bb-open');

            menu.classList.toggle('bb-open', isOpen);
            if (overlay) overlay.classList.toggle('bb-open', isOpen);
            hamburger.classList.toggle('bb-active', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        hamburger.addEventListener('click', function () { toggleMenu(); });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () { toggleMenu(false); });
        }

        if (overlay) {
            overlay.addEventListener('click', function () { toggleMenu(false); });
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

    /** Dil switch cookie sync */
    function initLangSwitch() {
        var langItems = document.querySelectorAll('.bb-lang-item:not(.bb-lang-active)');

        langItems.forEach(function (item) {
            item.addEventListener('click', function () {
                var href = this.getAttribute('href');
                if (href) {
                    var langMatch = href.match(/\/(en|ru)\//);
                    var newLang = langMatch ? langMatch[1] : 'az';
                    if (window.bbApp && window.bbApp.setCookie) {
                        window.bbApp.setCookie('bb_lang', newLang, 365);
                    }
                }
            });
        });
    }
})();
