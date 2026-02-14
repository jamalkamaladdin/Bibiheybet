/**
 * Bibiheybet.com - Audio Mini Player
 * 
 * Autoplay (aşağı səslə), play/pause, progress, volume.
 */

(function () {
    'use strict';

    var player, audio, playBtn, progressWrap, progressBar, volumeSlider, volBtn;
    var isReady = false;

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        player       = document.getElementById('bbPlayer');
        playBtn      = document.getElementById('bbPlayerPlay');
        progressWrap = document.getElementById('bbPlayerProgress');
        progressBar  = document.getElementById('bbPlayerProgressBar');
        volumeSlider = document.getElementById('bbPlayerVolume');
        volBtn       = document.getElementById('bbPlayerVolBtn');

        if (!player) return;

        var src = player.dataset.src;
        if (!src) return;

        audio = new Audio(src);
        audio.preload = 'auto';
        audio.loop = true;
        audio.volume = 0.08; // Çox aşağı səs

        isReady = true;

        bindEvents();
        tryAutoplay();
    }

    function bindEvents() {
        // Play/Pause
        playBtn.addEventListener('click', togglePlay);

        // Progress click
        progressWrap.addEventListener('click', seek);

        // Volume slider
        volumeSlider.addEventListener('input', function () {
            var val = parseInt(this.value, 10) / 100;
            audio.volume = val;
            audio.muted = val === 0;
            updateMuteState();
        });

        // Volume button - mute toggle
        volBtn.addEventListener('click', function () {
            audio.muted = !audio.muted;
            if (audio.muted) {
                volumeSlider.value = 0;
            } else {
                volumeSlider.value = Math.round(audio.volume * 100);
            }
            updateMuteState();
        });

        // Audio events
        audio.addEventListener('timeupdate', updateProgress);
        audio.addEventListener('play', function () { player.classList.add('bb-playing'); });
        audio.addEventListener('pause', function () { player.classList.remove('bb-playing'); });
    }

    /** Autoplay cəhdi (brauzerlər bloklaya bilər) */
    function tryAutoplay() {
        var playPromise = audio.play();

        if (playPromise !== undefined) {
            playPromise.catch(function () {
                // Autoplay bloklandı - istifadəçi interaksiyası lazımdır
                // İlk klikdə oynasın
                document.addEventListener('click', resumeOnInteraction, { once: true });
                document.addEventListener('touchstart', resumeOnInteraction, { once: true });
            });
        }
    }

    /** İstifadəçi interaksiyasından sonra oynamağa başla */
    function resumeOnInteraction() {
        if (audio && audio.paused) {
            audio.play().catch(function () {});
        }
    }

    function togglePlay() {
        if (!isReady) return;
        if (audio.paused) {
            audio.play().catch(function () {});
        } else {
            audio.pause();
        }
    }

    function updateProgress() {
        if (!audio.duration) return;
        var pct = (audio.currentTime / audio.duration) * 100;
        progressBar.style.width = pct + '%';
    }

    function seek(e) {
        if (!audio.duration) return;
        var rect = progressWrap.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var pct = x / rect.width;
        audio.currentTime = pct * audio.duration;
    }

    function updateMuteState() {
        player.classList.toggle('bb-muted', audio.muted || audio.volume === 0);
    }

    // Qlobal scope-a export
    window.bbPlayer = {
        play: function () { if (audio) audio.play().catch(function () {}); },
        pause: function () { if (audio) audio.pause(); },
        toggle: function () { if (audio) togglePlay(); },
    };
})();
