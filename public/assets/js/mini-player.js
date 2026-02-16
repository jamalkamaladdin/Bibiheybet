/**
 * Bibiheybet.com - Audio Mini Player
 * 
 * sessionStorage ilə səhifələr arası state saxlayır.
 * Ana səhifədə autoplay, digər səhifələrdə davam edir.
 * Pauza verilibsə istifadəçi özü başlatmalıdır.
 */

(function () {
    'use strict';

    var STORAGE_KEY = 'bbPlayerState';
    var player, audio, playBtn, progressWrap, progressBar, volumeSlider, volBtn;
    var isReady = false;
    var isHome = false;
    var hasUserPaused = false;

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

        isHome = player.dataset.isHome === '1';

        audio = new Audio(src);
        audio.preload = 'auto';
        audio.loop = true;

        isReady = true;

        bindEvents();
        restoreState();
    }

    function getState() {
        try {
            var data = sessionStorage.getItem(STORAGE_KEY);
            if (data) return JSON.parse(data);
        } catch (e) {}
        return null;
    }

    function saveState() {
        if (!audio || !isReady) return;
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                currentTime: audio.currentTime || 0,
                volume: audio.volume,
                muted: audio.muted,
                paused: hasUserPaused,
                wasPlaying: !audio.paused,
                initialized: true
            }));
        } catch (e) {}
    }

    function restoreState() {
        var state = getState();

        if (state && state.initialized) {
            audio.volume = typeof state.volume === 'number' ? state.volume : 0.08;
            audio.muted = !!state.muted;
            volumeSlider.value = audio.muted ? 0 : Math.round(audio.volume * 100);
            updateMuteState();

            if (state.currentTime > 0) {
                audio.addEventListener('loadedmetadata', function onMeta() {
                    audio.removeEventListener('loadedmetadata', onMeta);
                    if (state.currentTime < audio.duration) {
                        audio.currentTime = state.currentTime;
                    }
                    afterRestore(state);
                });
                audio.addEventListener('canplay', function onCanPlay() {
                    audio.removeEventListener('canplay', onCanPlay);
                    if (state.currentTime < audio.duration) {
                        audio.currentTime = state.currentTime;
                    }
                    afterRestore(state);
                });
                audio.load();
            } else {
                afterRestore(state);
            }
        } else {
            audio.volume = 0.08;
            volumeSlider.value = 8;
            if (isHome) {
                tryAutoplay();
            }
        }
    }

    function afterRestore(state) {
        hasUserPaused = !!state.paused;

        if (state.paused) {
            return;
        }

        if (state.wasPlaying) {
            audio.play().catch(function () {
                document.addEventListener('click', resumeOnInteraction, { once: true });
                document.addEventListener('touchstart', resumeOnInteraction, { once: true });
            });
        } else if (isHome) {
            tryAutoplay();
        }
    }

    function bindEvents() {
        playBtn.addEventListener('click', togglePlay);
        progressWrap.addEventListener('click', seek);

        volumeSlider.addEventListener('input', function () {
            var val = parseInt(this.value, 10) / 100;
            audio.volume = val;
            audio.muted = val === 0;
            updateMuteState();
            saveState();
        });

        volBtn.addEventListener('click', function () {
            var volWrap = volBtn.closest('.bb-player-volume-wrap');
            var isMobile = window.innerWidth <= 480;

            if (isMobile) {
                volWrap.classList.toggle('bb-vol-open');
            }

            audio.muted = !audio.muted;
            if (audio.muted) {
                volumeSlider.value = 0;
            } else {
                volumeSlider.value = Math.round(audio.volume * 100);
            }
            updateMuteState();
            saveState();
        });

        audio.addEventListener('timeupdate', updateProgress);
        audio.addEventListener('play', function () {
            player.classList.add('bb-playing');
            saveState();
        });
        audio.addEventListener('pause', function () {
            player.classList.remove('bb-playing');
            saveState();
        });

        window.addEventListener('beforeunload', saveState);

        setInterval(saveState, 3000);
    }

    function tryAutoplay() {
        var playPromise = audio.play();

        if (playPromise !== undefined) {
            playPromise.catch(function () {
                document.addEventListener('click', resumeOnInteraction, { once: true });
                document.addEventListener('touchstart', resumeOnInteraction, { once: true });
            });
        }
    }

    function resumeOnInteraction() {
        if (audio && audio.paused && !hasUserPaused) {
            audio.play().catch(function () {});
        }
    }

    function togglePlay() {
        if (!isReady) return;
        if (audio.paused) {
            hasUserPaused = false;
            audio.play().catch(function () {});
        } else {
            hasUserPaused = true;
            audio.pause();
        }
        saveState();
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
        saveState();
    }

    function updateMuteState() {
        player.classList.toggle('bb-muted', audio.muted || audio.volume === 0);
    }

    window.bbPlayer = {
        play: function () { if (audio) { hasUserPaused = false; audio.play().catch(function () {}); } },
        pause: function () { if (audio) { hasUserPaused = true; audio.pause(); } },
        toggle: function () { if (audio) togglePlay(); },
    };
})();
