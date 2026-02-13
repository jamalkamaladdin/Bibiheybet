/**
 * Bibiheybet.com - Ümumi Frontend JS
 * 
 * Dil switch, hamburger menyu, utility funksiyalar.
 */

(function () {
    'use strict';

    // ============================================
    // DOM Hazır
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        initHamburgerMenu();
        initLangSwitch();
        initStickyHeader();
        initLazyLoad();
    });

    // ============================================
    // Hamburger Menyu
    // ============================================
    function initHamburgerMenu() {
        var hamburger = document.getElementById('bbHamburger');
        var menu = document.getElementById('bbMobileMenu');
        var overlay = document.getElementById('bbMobileOverlay');
        var closeBtn = document.getElementById('bbMobileMenuClose');

        if (!hamburger || !menu) return;

        /** Menyu açma/bağlama */
        function toggleMenu(open) {
            var isOpen = typeof open === 'boolean' ? open : !menu.classList.contains('bb-open');

            menu.classList.toggle('bb-open', isOpen);
            overlay.classList.toggle('bb-open', isOpen);
            hamburger.classList.toggle('bb-active', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

            // Scroll lock
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        hamburger.addEventListener('click', function () {
            toggleMenu();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                toggleMenu(false);
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                toggleMenu(false);
            });
        }

        // ESC ilə bağla
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && menu.classList.contains('bb-open')) {
                toggleMenu(false);
            }
        });
    }

    // ============================================
    // Dil Switch
    // ============================================
    function initLangSwitch() {
        var langItems = document.querySelectorAll('.bb-lang-item:not(.bb-lang-active)');

        langItems.forEach(function (item) {
            item.addEventListener('click', function (e) {
                // Cookie-yə yeni dili yaz (server-side bb_set_lang ilə paralel)
                var href = this.getAttribute('href');
                if (href) {
                    var langMatch = href.match(/\/(en|ru)\//);
                    var newLang = langMatch ? langMatch[1] : 'az';
                    bbSetCookie('bb_lang', newLang, 365);
                }
                // Default link davranışı davam etsin (redirect)
            });
        });
    }

    // ============================================
    // Sticky Header
    // ============================================
    function initStickyHeader() {
        var header = document.getElementById('bbHeader');
        if (!header) return;

        var lastScroll = 0;
        var scrollThreshold = 100;

        window.addEventListener('scroll', function () {
            var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScroll > scrollThreshold) {
                header.classList.add('bb-header-sticky');
            } else {
                header.classList.remove('bb-header-sticky');
            }

            lastScroll = currentScroll;
        }, { passive: true });
    }

    // ============================================
    // Lazy Loading (native + fallback)
    // ============================================
    function initLazyLoad() {
        var lazyImages = document.querySelectorAll('.bb-lazy');
        if (!lazyImages.length) return;

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                        img.classList.add('bb-loaded');
                        observer.unobserve(img);
                    }
                });
            }, { rootMargin: '100px' });

            lazyImages.forEach(function (img) {
                observer.observe(img);
            });
        } else {
            // Fallback: hamısını yüklə
            lazyImages.forEach(function (img) {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                img.classList.add('bb-loaded');
            });
        }
    }

    // ============================================
    // Utility Funksiyalar
    // ============================================

    /** Cookie təyin et */
    function bbSetCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/; SameSite=Lax';
    }

    /** Cookie oxu */
    function bbGetCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length);
            }
        }
        return null;
    }

    // Qlobal scope-a export (digər JS fayllar istifadə etsin)
    window.bbApp = {
        setCookie: bbSetCookie,
        getCookie: bbGetCookie,
    };

})();
