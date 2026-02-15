/**
 * Bibiheybet.com - Ana Səhifə JS
 * 
 * Scroll animasiyaları: bölmələr görünəndə fade-in effekti.
 */
(function () {
    'use strict';

    /** IntersectionObserver ilə scroll animasiyalar */
    function initScrollAnimations() {
        var elements = document.querySelectorAll('[data-animate]');
        if (!elements.length) return;

        // Brauzer dəstəyi yoxla
        if (!('IntersectionObserver' in window)) {
            elements.forEach(function (el) { el.classList.add('bb-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('bb-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    // DOM hazır olduqda başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollAnimations);
    } else {
        initScrollAnimations();
    }
})();
