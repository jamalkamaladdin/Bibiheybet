/**
 * Bibiheybet.com - Ümumi Frontend JS
 * 
 * Cookie utility, lazy load. Header/menyu kodu header.js-ə köçürülüb.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initLazyLoad();
    });

    /** Lazy loading (IntersectionObserver) */
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
            lazyImages.forEach(function (img) {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                img.classList.add('bb-loaded');
            });
        }
    }

    // ============================================
    // Cookie Utility (qlobal)
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

    // Qlobal scope-a export
    window.bbApp = {
        setCookie: bbSetCookie,
        getCookie: bbGetCookie,
    };

})();
