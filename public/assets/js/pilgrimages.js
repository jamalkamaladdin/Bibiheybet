/**
 * Bibiheybet.com - Ziyarətgah Qalereya Lightbox
 * 
 * Gallery grid-dəki şəkillərə klik edəndə lightbox açılır.
 * Prev/Next naviqasiya, keyboard dəstəyi, swipe (touch).
 */
(function () {
    'use strict';

    var lightbox = document.getElementById('bbLightbox');
    if (!lightbox) return;

    var img        = document.getElementById('bbLightboxImg');
    var caption    = document.getElementById('bbLightboxCaption');
    var counter    = document.getElementById('bbLightboxCounter');
    var closeBtn   = document.getElementById('bbLightboxClose');
    var prevBtn    = document.getElementById('bbLightboxPrev');
    var nextBtn    = document.getElementById('bbLightboxNext');
    var overlay    = lightbox.querySelector('.bb-lightbox-overlay');

    var items = document.querySelectorAll('.bb-ps-gallery-item[data-lightbox-src]');
    if (!items.length) return;

    var currentIndex = 0;
    var totalItems   = items.length;
    var touchStartX  = 0;
    var touchEndX    = 0;

    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.classList.add('bb-lightbox-active');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('bb-lightbox-active');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function updateLightbox() {
        var item = items[currentIndex];
        if (!item) return;
        img.src = item.getAttribute('data-lightbox-src');
        img.alt = item.getAttribute('data-lightbox-caption') || '';
        caption.textContent = item.getAttribute('data-lightbox-caption') || '';
        counter.textContent = (currentIndex + 1) + ' / ' + totalItems;

        prevBtn.style.display = totalItems > 1 ? '' : 'none';
        nextBtn.style.display = totalItems > 1 ? '' : 'none';
    }

    function goNext() {
        currentIndex = (currentIndex + 1) % totalItems;
        updateLightbox();
    }

    function goPrev() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        updateLightbox();
    }

    // Event listeners
    items.forEach(function (item) {
        item.addEventListener('click', function () {
            var index = parseInt(this.getAttribute('data-lightbox-index'), 10);
            openLightbox(index);
        });
    });

    closeBtn.addEventListener('click', closeLightbox);
    overlay.addEventListener('click', closeLightbox);
    prevBtn.addEventListener('click', goPrev);
    nextBtn.addEventListener('click', goNext);

    // Keyboard
    document.addEventListener('keydown', function (e) {
        if (!lightbox.classList.contains('bb-lightbox-active')) return;

        switch (e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                goPrev();
                break;
            case 'ArrowRight':
                goNext();
                break;
        }
    });

    // Touch swipe
    lightbox.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    lightbox.addEventListener('touchend', function (e) {
        touchEndX = e.changedTouches[0].screenX;
        var diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                goNext();
            } else {
                goPrev();
            }
        }
    }, { passive: true });

})();
