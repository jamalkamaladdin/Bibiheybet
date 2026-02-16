/**
 * Bibiheybet.com - Ana Səhifə Quran Player
 *
 * quran.com API v4 ilə kompakt surə player.
 * Autoplay yoxdur — istifadəçi özü başladır.
 */

(function () {
    'use strict';

    var API_BASE = 'https://api.quran.com/api/v4';
    var LABELS = window.BB_HQ_LABELS || {};
    var LANG = window.BB_HQ_LANG || 'az';

    var langToApi = { az: 'en', en: 'en', ru: 'en', ar: 'ar', fa: 'en' };
    var apiLang = langToApi[LANG] || 'en';

    var surahs = [];
    var reciters = [];
    var currentReciterId = 7;
    var currentSurahId = 1;
    var audio = null;

    var surahNamesLocal = {
        az: {
            1: 'Fatihə', 2: 'Bəqərə', 3: 'Ali-İmran', 4: 'Nisa', 5: 'Maidə',
            6: 'Ənam', 7: 'Əraf', 8: 'Ənfal', 9: 'Tövbə', 10: 'Yunus',
            11: 'Hud', 12: 'Yusuf', 13: 'Rəd', 14: 'İbrahim', 15: 'Hicr',
            16: 'Nəhl', 17: 'İsra', 18: 'Kəhf', 19: 'Məryəm', 20: 'Taha',
            21: 'Ənbiya', 22: 'Həcc', 23: 'Muminun', 24: 'Nur', 25: 'Furqan',
            26: 'Şüəra', 27: 'Nəml', 28: 'Qəsəs', 29: 'Ənkəbut', 30: 'Rum',
            31: 'Loqman', 32: 'Səcdə', 33: 'Əhzab', 34: 'Səba', 35: 'Fatir',
            36: 'Yasin', 37: 'Saffat', 38: 'Sad', 39: 'Zümər', 40: 'Ğafir',
            41: 'Fussilət', 42: 'Şura', 43: 'Zuxruf', 44: 'Duxan', 45: 'Casiyə',
            46: 'Əhqaf', 47: 'Muhəmməd', 48: 'Fəth', 49: 'Hucurat', 50: 'Qaf',
            51: 'Zariyat', 52: 'Tur', 53: 'Nəcm', 54: 'Qəmər', 55: 'Rəhman',
            56: 'Vaqiə', 57: 'Hədid', 58: 'Mucadilə', 59: 'Həşr', 60: 'Mumtəhinə',
            61: 'Saff', 62: 'Cumə', 63: 'Munafiqun', 64: 'Təğabun', 65: 'Talaq',
            66: 'Təhrim', 67: 'Mulk', 68: 'Qələm', 69: 'Haqqə', 70: 'Məaric',
            71: 'Nuh', 72: 'Cinn', 73: 'Muzzəmmil', 74: 'Muddəssir', 75: 'Qiyamə',
            76: 'İnsan', 77: 'Mursəlat', 78: 'Nəbə', 79: 'Naziat', 80: 'Əbəsə',
            81: 'Təkvir', 82: 'İnfitar', 83: 'Mutəffifin', 84: 'İnşiqaq', 85: 'Buruc',
            86: 'Tariq', 87: 'Əla', 88: 'Ğaşiyə', 89: 'Fəcr', 90: 'Bələd',
            91: 'Şəms', 92: 'Leyl', 93: 'Duha', 94: 'İnşirah', 95: 'Tin',
            96: 'Ələq', 97: 'Qədr', 98: 'Bəyyinə', 99: 'Zəlzələ', 100: 'Adiyat',
            101: 'Qariə', 102: 'Təkasur', 103: 'Əsr', 104: 'Huməzə', 105: 'Fil',
            106: 'Qureyş', 107: 'Maun', 108: 'Kövsər', 109: 'Kafirun', 110: 'Nəsr',
            111: 'Məsəd', 112: 'İxlas', 113: 'Fələq', 114: 'Nas'
        },
        ru: {
            1: 'Аль-Фатиха', 2: 'Аль-Бакара', 3: 'Аль-Имран', 4: 'Ан-Ниса', 5: 'Аль-Маида',
            36: 'Ясин', 55: 'Ар-Рахман', 67: 'Аль-Мульк', 112: 'Аль-Ихлас', 113: 'Аль-Фаляк', 114: 'Ан-Нас'
        }
    };

    var els = {};

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        els.player = document.getElementById('bbHomeQuranPlayer');
        els.display = document.getElementById('bbHQDisplay');
        els.arabic = document.getElementById('bbHQArabic');
        els.name = document.getElementById('bbHQName');
        els.playBtn = document.getElementById('bbHQPlayBtn');
        els.prevBtn = document.getElementById('bbHQPrev');
        els.nextBtn = document.getElementById('bbHQNext');
        els.progress = document.getElementById('bbHQProgress');
        els.progressBar = document.getElementById('bbHQProgressBar');
        els.currentTime = document.getElementById('bbHQCurrentTime');
        els.duration = document.getElementById('bbHQDuration');
        els.surahSelect = document.getElementById('bbHQSurahSelect');
        els.reciterSelect = document.getElementById('bbHQReciterSelect');

        if (!els.player) return;

        audio = new Audio();
        audio.preload = 'none';

        loadReciters();
        loadSurahs();
        bindEvents();
    }

    function bindEvents() {
        els.playBtn.addEventListener('click', function () {
            if (!currentSurahId) return;

            if (!audio.src || audio.src === window.location.href) {
                playSurah(currentSurahId);
                return;
            }

            if (audio.paused) {
                audio.play().catch(function () {});
            } else {
                audio.pause();
            }
        });

        els.prevBtn.addEventListener('click', function () {
            if (currentSurahId > 1) {
                playSurah(currentSurahId - 1);
                updateSurahSelect(currentSurahId);
            }
        });

        els.nextBtn.addEventListener('click', function () {
            if (currentSurahId < 114) {
                playSurah(currentSurahId + 1);
                updateSurahSelect(currentSurahId);
            }
        });

        els.surahSelect.addEventListener('change', function () {
            var val = parseInt(this.value, 10);
            if (val) {
                playSurah(val);
            }
        });

        els.reciterSelect.addEventListener('change', function () {
            var val = parseInt(this.value, 10);
            if (val) {
                currentReciterId = val;
                if (audio.src && audio.src !== window.location.href) {
                    playSurah(currentSurahId);
                }
            }
        });

        els.progress.addEventListener('click', function (e) {
            if (!audio.duration) return;
            var rect = els.progress.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var pct = x / rect.width;
            audio.currentTime = pct * audio.duration;
        });

        audio.addEventListener('timeupdate', function () {
            if (!audio.duration) return;
            var pct = (audio.currentTime / audio.duration) * 100;
            els.progressBar.style.width = pct + '%';
            els.currentTime.textContent = formatTime(audio.currentTime);
        });

        audio.addEventListener('loadedmetadata', function () {
            els.duration.textContent = formatTime(audio.duration);
        });

        audio.addEventListener('play', function () {
            els.player.classList.add('bb-hq-playing');
            els.player.classList.remove('bb-hq-loading');
        });

        audio.addEventListener('pause', function () {
            els.player.classList.remove('bb-hq-playing');
        });

        audio.addEventListener('waiting', function () {
            els.player.classList.add('bb-hq-loading');
        });

        audio.addEventListener('canplay', function () {
            els.player.classList.remove('bb-hq-loading');
        });

        audio.addEventListener('ended', function () {
            els.player.classList.remove('bb-hq-playing');
            if (currentSurahId < 114) {
                playSurah(currentSurahId + 1);
                updateSurahSelect(currentSurahId);
            }
        });
    }

    function loadReciters() {
        fetch(API_BASE + '/resources/recitations?' + new URLSearchParams({ language: apiLang }))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                reciters = data.recitations || [];
                renderReciters();
            })
            .catch(function () {});
    }

    function renderReciters() {
        var select = els.reciterSelect;
        select.innerHTML = '';

        reciters.forEach(function (r) {
            var opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = r.reciter_name + (r.style ? ' (' + r.style + ')' : '');
            if (r.id === currentReciterId) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });
    }

    function loadSurahs() {
        fetch(API_BASE + '/chapters?' + new URLSearchParams({ language: apiLang }))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                surahs = data.chapters || [];
                renderSurahSelect();
                updateDisplay(1);
            })
            .catch(function () {});
    }

    function renderSurahSelect() {
        var select = els.surahSelect;
        select.innerHTML = '';

        surahs.forEach(function (s) {
            var localName = getLocalName(s.id);
            var displayName = localName || s.name_simple;
            var opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.id + '. ' + displayName + '  (' + s.name_arabic + ')';
            if (s.id === currentSurahId) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });
    }

    function updateSurahSelect(surahId) {
        els.surahSelect.value = surahId;
    }

    function updateDisplay(surahId) {
        var surah = surahs.find(function (s) { return s.id === surahId; });
        if (!surah) return;

        var localName = getLocalName(surahId);
        var displayName = localName || surah.name_simple;
        var revType = surah.revelation_place === 'makkah'
            ? (LABELS.meccan || 'Məkkə')
            : (LABELS.medinan || 'Mədinə');

        els.arabic.textContent = surah.name_arabic;
        els.name.textContent = displayName + ' · ' + surah.verses_count + ' ' + (LABELS.verses || 'ayə') + ' · ' + revType;
    }

    function playSurah(surahId) {
        currentSurahId = surahId;
        updateDisplay(surahId);
        updateSurahSelect(surahId);

        els.player.classList.add('bb-hq-loading');
        els.player.classList.remove('bb-hq-playing');
        els.progressBar.style.width = '0%';
        els.currentTime.textContent = '0:00';
        els.duration.textContent = '0:00';

        fetch(API_BASE + '/chapter_recitations/' + currentReciterId + '/' + surahId)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                var audioUrl = data.audio_file && data.audio_file.audio_url;
                if (!audioUrl) {
                    els.player.classList.remove('bb-hq-loading');
                    return;
                }

                if (!audioUrl.startsWith('http')) {
                    audioUrl = 'https://audio.qurancdn.com/' + audioUrl;
                }

                audio.src = audioUrl;
                audio.load();
                audio.play().catch(function () {
                    els.player.classList.remove('bb-hq-loading');
                });
            })
            .catch(function () {
                els.player.classList.remove('bb-hq-loading');
            });
    }

    function getLocalName(surahId) {
        if (surahNamesLocal[LANG] && surahNamesLocal[LANG][surahId]) {
            return surahNamesLocal[LANG][surahId];
        }
        return null;
    }

    function formatTime(seconds) {
        if (!seconds || isNaN(seconds)) return '0:00';
        var m = Math.floor(seconds / 60);
        var s = Math.floor(seconds % 60);
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

})();
