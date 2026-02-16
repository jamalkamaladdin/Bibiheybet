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

    /** Ana səhifə namaz vaxtları strip */
    function initPrayerStrip() {
        var strip = document.getElementById('bbHomePrayerStrip');
        if (!strip || typeof PrayTime === 'undefined') return;

        try {
            var pt = new PrayTime('Jafari');
            var times = pt.location([40.4093, 49.8671]).timezone('Asia/Baku').getTimes();

            var keys = ['fajr', 'sunrise', 'dhuhr', 'asr', 'sunset', 'maghrib', 'isha', 'midnight'];
            for (var i = 0; i < keys.length; i++) {
                var el = document.getElementById('bbHomeTime_' + keys[i]);
                if (el) el.textContent = times[keys[i]];
            }

            var now = new Date();
            var nowMin = now.getHours() * 60 + now.getMinutes();
            var order = ['fajr', 'sunrise', 'dhuhr', 'asr', 'sunset', 'maghrib', 'isha'];
            var active = '';
            for (var j = order.length - 1; j >= 0; j--) {
                var parts = (times[order[j]] || '').split(':');
                var min = parts.length === 2 ? parseInt(parts[0]) * 60 + parseInt(parts[1]) : -1;
                if (min >= 0 && nowMin >= min) { active = order[j]; break; }
            }

            if (active) {
                var items = strip.querySelectorAll('.bb-pt-strip-item');
                for (var k = 0; k < items.length; k++) {
                    if (items[k].getAttribute('data-prayer') === active) {
                        items[k].classList.add('bb-pt-strip-item-active');
                    }
                }
            }
        } catch (e) {
            if (typeof console !== 'undefined') console.error('Prayer strip error:', e);
        }
    }

    // DOM hazır olduqda başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initScrollAnimations();
            initPrayerStrip();
        });
    } else {
        initScrollAnimations();
        initPrayerStrip();
    }
})();
