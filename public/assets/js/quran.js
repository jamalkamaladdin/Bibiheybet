/**
 * Bibiheybet.com - Quran Dinlə
 *
 * quran.com API v4 ilə surə tilavətləri.
 * Qari seçimi, surə grid, audio player.
 */

(function () {
    'use strict';

    var API_BASE = 'https://api.quran.com/api/v4';
    var LABELS = window.BB_QURAN_LABELS || {};
    var LANG = window.BB_QURAN_LANG || 'az';

    var STORAGE_KEY = 'bbQuranState';

    var surahs = [];
    var reciters = [];
    var currentReciterId = 7;
    var currentSurahId = null;
    var audio = null;
    var isLoading = false;

    var langToApi = { az: 'en', en: 'en', ru: 'en', ar: 'ar', fa: 'en' };
    var apiLang = langToApi[LANG] || 'en';

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
        els.grid = document.getElementById('bbSurahGrid');
        els.loading = document.getElementById('bbQuranLoading');
        els.empty = document.getElementById('bbQuranEmpty');
        els.reciterSelect = document.getElementById('bbReciterSelect');
        els.searchInput = document.getElementById('bbSurahSearch');
        els.player = document.getElementById('bbQuranPlayer');
        els.playerSurah = document.getElementById('bbQuranPlayerSurah');
        els.playerReciter = document.getElementById('bbQuranPlayerReciter');
        els.playBtn = document.getElementById('bbQuranPlayBtn');
        els.prevBtn = document.getElementById('bbQuranPrev');
        els.nextBtn = document.getElementById('bbQuranNext');
        els.progress = document.getElementById('bbQuranProgress');
        els.progressBar = document.getElementById('bbQuranProgressBar');
        els.currentTime = document.getElementById('bbQuranCurrentTime');
        els.duration = document.getElementById('bbQuranDuration');

        if (!els.grid) return;

        audio = new Audio();
        audio.preload = 'auto';

        loadReciters();
        loadSurahs();
        bindEvents();
        restoreState();
    }

    function restoreState() {
        try {
            var data = sessionStorage.getItem(STORAGE_KEY);
            if (data) {
                var state = JSON.parse(data);
                if (state.reciterId) {
                    currentReciterId = state.reciterId;
                }
            }
        } catch (e) {}
    }

    function saveState() {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                reciterId: currentReciterId,
                surahId: currentSurahId
            }));
        } catch (e) {}
    }

    function bindEvents() {
        els.reciterSelect.addEventListener('change', function () {
            var val = parseInt(this.value, 10);
            if (val) {
                currentReciterId = val;
                saveState();
                if (currentSurahId) {
                    playSurah(currentSurahId);
                }
            }
        });

        els.searchInput.addEventListener('input', filterSurahs);

        els.playBtn.addEventListener('click', function () {
            if (!audio.src) return;
            if (audio.paused) {
                audio.play().catch(function () {});
            } else {
                audio.pause();
            }
        });

        els.prevBtn.addEventListener('click', function () {
            if (currentSurahId && currentSurahId > 1) {
                playSurah(currentSurahId - 1);
            }
        });

        els.nextBtn.addEventListener('click', function () {
            if (currentSurahId && currentSurahId < 114) {
                playSurah(currentSurahId + 1);
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
            els.player.classList.add('bb-qp-playing');
            els.player.classList.remove('bb-qp-loading');
        });

        audio.addEventListener('pause', function () {
            els.player.classList.remove('bb-qp-playing');
        });

        audio.addEventListener('waiting', function () {
            els.player.classList.add('bb-qp-loading');
        });

        audio.addEventListener('canplay', function () {
            els.player.classList.remove('bb-qp-loading');
        });

        audio.addEventListener('ended', function () {
            els.player.classList.remove('bb-qp-playing');
            if (currentSurahId && currentSurahId < 114) {
                playSurah(currentSurahId + 1);
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
        select.innerHTML = '<option value="">' + escapeHtml(LABELS.select_reciter || 'Select reciter') + '</option>';

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
        els.loading.style.display = '';
        els.empty.style.display = 'none';

        fetch(API_BASE + '/chapters?' + new URLSearchParams({ language: apiLang }))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                surahs = data.chapters || [];
                els.loading.style.display = 'none';
                renderSurahs(surahs);
            })
            .catch(function () {
                els.loading.innerHTML = '<span style="color:var(--bb-color-muted)">' + escapeHtml(LABELS.error || 'Error') + '</span>';
            });
    }

    function renderSurahs(list) {
        var html = '';

        list.forEach(function (s) {
            var localName = getLocalName(s.id);
            var displayName = localName || s.name_simple;
            var revType = s.revelation_place === 'makkah'
                ? (LABELS.meccan || 'Meccan')
                : (LABELS.medinan || 'Medinan');
            var meta = revType + ' · ' + s.verses_count + ' ' + (LABELS.verses || 'ayə');
            var isActive = s.id === currentSurahId;

            html += '<div class="bb-surah-card' + (isActive ? ' bb-surah-active' : '') + '" data-surah="' + s.id + '">';
            html += '<div class="bb-surah-num">';
            html += '<span class="bb-surah-num-text">' + s.id + '</span>';
            html += '<svg class="bb-surah-num-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
            html += '</div>';
            html += '<div class="bb-surah-info">';
            html += '<span class="bb-surah-name">' + escapeHtml(displayName) + '</span>';
            html += '<span class="bb-surah-meta">' + escapeHtml(meta) + '</span>';
            html += '</div>';
            html += '<span class="bb-surah-arabic">' + escapeHtml(s.name_arabic) + '</span>';
            html += '</div>';
        });

        if (!html) {
            els.grid.innerHTML = '';
            els.empty.style.display = '';
            return;
        }

        els.empty.style.display = 'none';
        els.grid.innerHTML = html;

        els.grid.querySelectorAll('.bb-surah-card').forEach(function (card) {
            card.addEventListener('click', function () {
                var id = parseInt(this.dataset.surah, 10);
                playSurah(id);
            });
        });
    }

    function filterSurahs() {
        var query = els.searchInput.value.trim().toLowerCase();

        if (!query) {
            renderSurahs(surahs);
            return;
        }

        var filtered = surahs.filter(function (s) {
            var localName = (getLocalName(s.id) || '').toLowerCase();
            return s.name_simple.toLowerCase().indexOf(query) !== -1
                || s.name_arabic.indexOf(query) !== -1
                || localName.indexOf(query) !== -1
                || String(s.id) === query;
        });

        renderSurahs(filtered);
    }

    function playSurah(surahId) {
        currentSurahId = surahId;
        saveState();

        var surah = surahs.find(function (s) { return s.id === surahId; });
        if (!surah) return;

        var localName = getLocalName(surahId);
        var displayName = localName || surah.name_simple;

        var reciter = reciters.find(function (r) { return r.id === currentReciterId; });
        var reciterName = reciter ? reciter.reciter_name : '';

        els.playerSurah.textContent = surah.name_arabic + ' — ' + displayName;
        els.playerReciter.textContent = reciterName;
        els.player.style.display = '';
        els.player.classList.add('bb-qp-loading');
        els.player.classList.remove('bb-qp-playing');
        els.progressBar.style.width = '0%';
        els.currentTime.textContent = '0:00';
        els.duration.textContent = '0:00';

        highlightSurah(surahId);

        fetch(API_BASE + '/chapter_recitations/' + currentReciterId + '/' + surahId)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                var audioUrl = data.audio_file && data.audio_file.audio_url;
                if (!audioUrl) {
                    els.player.classList.remove('bb-qp-loading');
                    return;
                }

                if (!audioUrl.startsWith('http')) {
                    audioUrl = 'https://audio.qurancdn.com/' + audioUrl;
                }

                audio.src = audioUrl;
                audio.load();
                audio.play().catch(function () {
                    els.player.classList.remove('bb-qp-loading');
                });
            })
            .catch(function () {
                els.player.classList.remove('bb-qp-loading');
            });
    }

    function highlightSurah(activeId) {
        els.grid.querySelectorAll('.bb-surah-card').forEach(function (card) {
            var id = parseInt(card.dataset.surah, 10);
            card.classList.toggle('bb-surah-active', id === activeId);

            var icon = card.querySelector('.bb-surah-num-icon');
            if (id === activeId && icon) {
                icon.innerHTML = '<rect x="6" y="4" width="2.5" height="16" rx="1" fill="currentColor"><animate attributeName="height" values="16;8;16" dur="0.8s" repeatCount="indefinite"/></rect>'
                    + '<rect x="10.75" y="4" width="2.5" height="16" rx="1" fill="currentColor"><animate attributeName="height" values="8;16;8" dur="0.8s" repeatCount="indefinite"/></rect>'
                    + '<rect x="15.5" y="4" width="2.5" height="16" rx="1" fill="currentColor"><animate attributeName="height" values="12;6;12" dur="0.8s" repeatCount="indefinite"/></rect>';
            } else if (icon) {
                icon.innerHTML = '<path d="M8 5v14l11-7z"/>';
            }
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

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

})();
