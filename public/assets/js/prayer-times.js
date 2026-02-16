/**
 * Bibiheybet.com - Namaz Vaxtları
 * 
 * PrayTimes kitabxanası ilə Cəfəri məzhəbinə uyğun hesablama.
 * Azərbaycanın bütün şəhər və rayonları üçün.
 * Bu gün + aylıq cədvəl.
 */
(function () {
    'use strict';

    var LANG = document.documentElement.lang || 'az';
    var TZ = 'Asia/Baku';

    /* =============================================
       Ay adları (çoxdilli)
       ============================================= */
    var MONTH_NAMES = {
        az: ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'İyun', 'İyul', 'Avqust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'],
        en: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        ru: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
    };

    var months = MONTH_NAMES[LANG] || MONTH_NAMES.az;

    /* =============================================
       Azərbaycan şəhər və rayonları — [enlik, uzunluq]
       ============================================= */
    var REGIONS = {
        /* ---- Şəhərlər ---- */
        baki:        { az: 'Bakı',        en: 'Baku',        ru: 'Баку',        c: [40.4093, 49.8671] },
        gence:       { az: 'Gəncə',       en: 'Ganja',       ru: 'Гянджа',      c: [40.6828, 46.3606] },
        sumqayit:    { az: 'Sumqayıt',     en: 'Sumgait',     ru: 'Сумгаит',     c: [40.5855, 49.6317] },
        mingecevir:  { az: 'Mingəçevir',   en: 'Mingachevir',  ru: 'Мингечевир',  c: [40.7703, 47.0596] },
        lenkeran:    { az: 'Lənkəran',     en: 'Lankaran',    ru: 'Ленкоран',    c: [38.7536, 48.8511] },
        sirvan:      { az: 'Şirvan',       en: 'Shirvan',     ru: 'Ширван',      c: [39.9264, 48.9206] },
        naxcivan:    { az: 'Naxçıvan',     en: 'Nakhchivan',  ru: 'Нахичевань',  c: [39.2089, 45.4122] },
        seki:        { az: 'Şəki',         en: 'Sheki',       ru: 'Шеки',        c: [41.1975, 47.1706] },
        yevlax:      { az: 'Yevlax',       en: 'Yevlakh',     ru: 'Евлах',       c: [40.6197, 47.1500] },
        xankendi:    { az: 'Xankəndi',     en: 'Khankendi',   ru: 'Ханкенди',    c: [39.8153, 46.7519] },

        /* ---- Rayonlar (əlifba sırası) ---- */
        absheron:    { az: 'Abşeron',      en: 'Absheron',    ru: 'Абшерон',     c: [40.4200, 50.0000] },
        agcabedi:    { az: 'Ağcabədi',     en: 'Aghjabadi',   ru: 'Агджабеди',   c: [40.0500, 47.4600] },
        agdam:       { az: 'Ağdam',        en: 'Aghdam',      ru: 'Агдам',       c: [39.9900, 46.9300] },
        agdas:       { az: 'Ağdaş',        en: 'Agdash',      ru: 'Агдаш',       c: [40.6494, 47.4700] },
        agstafa:     { az: 'Ağstafa',      en: 'Agstafa',     ru: 'Агстафа',     c: [41.1194, 45.4539] },
        agsu:        { az: 'Ağsu',         en: 'Agsu',        ru: 'Агсу',        c: [40.5700, 48.3900] },
        astara:      { az: 'Astara',       en: 'Astara',      ru: 'Астара',      c: [38.4561, 48.8728] },
        babek:       { az: 'Babək',        en: 'Babek',       ru: 'Бабек',       c: [39.1506, 45.4483] },
        balaken:     { az: 'Balakən',      en: 'Balakan',     ru: 'Балакен',     c: [41.7258, 46.4042] },
        berde:       { az: 'Bərdə',        en: 'Barda',       ru: 'Барда',       c: [40.3800, 47.1300] },
        beylaqan:    { az: 'Beyləqan',     en: 'Beylagan',    ru: 'Бейлаган',    c: [39.7700, 47.6200] },
        bilasuvar:   { az: 'Biləsuvar',    en: 'Bilasuvar',   ru: 'Билясувар',   c: [39.4600, 48.5400] },
        cebrayil:    { az: 'Cəbrayıl',     en: 'Jabrayil',    ru: 'Джебраил',    c: [39.3986, 47.0283] },
        celilabad:   { az: 'Cəlilabad',    en: 'Jalilabad',   ru: 'Джалилабад',  c: [39.2050, 48.5100] },
        culfa:       { az: 'Culfa',        en: 'Julfa',       ru: 'Джульфа',     c: [38.9606, 45.6297] },
        dashkesen:   { az: 'Daşkəsən',     en: 'Dashkasan',   ru: 'Дашкесан',    c: [40.5200, 46.0800] },
        fuzuli:      { az: 'Füzuli',       en: 'Fuzuli',      ru: 'Физули',      c: [39.6000, 47.1400] },
        gedebey:     { az: 'Gədəbəy',      en: 'Gadabay',     ru: 'Гедабей',     c: [40.5700, 45.8200] },
        goranboy:    { az: 'Goranboy',     en: 'Goranboy',    ru: 'Горанбой',    c: [40.6100, 46.7900] },
        goycay:      { az: 'Göyçay',       en: 'Goychay',     ru: 'Гёйчай',     c: [40.6533, 47.7406] },
        goygol:      { az: 'Göygöl',       en: 'Goygol',      ru: 'Гёйгёль',    c: [40.5836, 46.3164] },
        haciqabul:   { az: 'Hacıqabul',    en: 'Hajigabul',   ru: 'Гаджигабул',  c: [40.0400, 48.9400] },
        imisli:      { az: 'İmişli',       en: 'Imishli',     ru: 'Имишли',      c: [39.8700, 48.0600] },
        ismayilli:   { az: 'İsmayıllı',    en: 'Ismayilli',   ru: 'Исмаиллы',    c: [40.7867, 48.1500] },
        kelbecer:    { az: 'Kəlbəcər',     en: 'Kalbajar',    ru: 'Кельбаджар',  c: [40.1025, 46.0364] },
        kengerli:    { az: 'Kəngərli',     en: 'Kangarli',    ru: 'Кангерли',    c: [39.3856, 45.1642] },
        kurdamir:    { az: 'Kürdəmir',     en: 'Kurdamir',    ru: 'Кюрдамир',    c: [40.3400, 48.1600] },
        lacin:       { az: 'Laçın',        en: 'Lachin',      ru: 'Лачин',       c: [39.6383, 46.5461] },
        lerik:       { az: 'Lerik',        en: 'Lerik',       ru: 'Лерик',       c: [38.7736, 48.4147] },
        masalli:     { az: 'Masallı',      en: 'Masalli',     ru: 'Масаллы',     c: [39.0342, 48.6589] },
        neftcala:    { az: 'Neftçala',     en: 'Neftchala',   ru: 'Нефтчала',    c: [39.3700, 49.2500] },
        oguz:        { az: 'Oğuz',         en: 'Oguz',        ru: 'Огуз',        c: [41.0725, 47.4650] },
        ordubad:     { az: 'Ordubad',      en: 'Ordubad',     ru: 'Ордубад',     c: [38.9050, 46.0233] },
        qax:         { az: 'Qax',          en: 'Gakh',        ru: 'Гах',         c: [41.4200, 46.9200] },
        qazax:       { az: 'Qazax',        en: 'Gazakh',      ru: 'Газах',       c: [41.0969, 45.3656] },
        qebele:      { az: 'Qəbələ',       en: 'Gabala',      ru: 'Габала',      c: [40.9814, 47.8458] },
        qobustan:    { az: 'Qobustan',     en: 'Gobustan',    ru: 'Гобустан',    c: [40.5300, 48.9300] },
        quba:        { az: 'Quba',         en: 'Guba',        ru: 'Губа',        c: [41.3611, 48.5133] },
        qubadli:     { az: 'Qubadlı',      en: 'Gubadli',     ru: 'Губадлы',     c: [39.3400, 46.5800] },
        qusar:       { az: 'Qusar',        en: 'Gusar',       ru: 'Гусар',       c: [41.4275, 48.4303] },
        saatli:      { az: 'Saatlı',       en: 'Saatli',      ru: 'Саатлы',      c: [39.9300, 48.3700] },
        sabirabad:   { az: 'Sabirabad',    en: 'Sabirabad',   ru: 'Сабирабад',   c: [39.9900, 48.4800] },
        sadarak:     { az: 'Sədərək',      en: 'Sadarak',     ru: 'Садарак',     c: [39.7100, 44.8800] },
        salyan:      { az: 'Salyan',       en: 'Salyan',      ru: 'Сальян',      c: [39.5950, 49.0061] },
        samaxhi:     { az: 'Şamaxı',       en: 'Shamakhi',    ru: 'Шамахы',      c: [40.6319, 48.6367] },
        samux:       { az: 'Samux',        en: 'Samukh',      ru: 'Самух',       c: [40.7653, 46.4039] },
        shabran:     { az: 'Şabran',       en: 'Shabran',     ru: 'Шабран',      c: [41.2156, 48.8544] },
        shahbuz:     { az: 'Şahbuz',       en: 'Shahbuz',     ru: 'Шахбуз',      c: [39.4053, 45.5731] },
        serur:       { az: 'Şərur',        en: 'Sharur',      ru: 'Шарур',       c: [39.5536, 44.9839] },
        siyezen:     { az: 'Siyəzən',      en: 'Siyazan',     ru: 'Сиязань',     c: [41.0786, 49.1117] },
        susa:        { az: 'Şuşa',         en: 'Shusha',      ru: 'Шуша',        c: [39.7578, 46.7461] },
        terter:      { az: 'Tərtər',       en: 'Tartar',      ru: 'Тертер',      c: [40.3400, 46.9300] },
        tovuz:       { az: 'Tovuz',        en: 'Tovuz',       ru: 'Товуз',       c: [40.9925, 45.6286] },
        ucar:        { az: 'Ucar',         en: 'Ujar',        ru: 'Уджар',       c: [40.5100, 47.6500] },
        xachmaz:     { az: 'Xaçmaz',       en: 'Khachmaz',    ru: 'Хачмаз',      c: [41.4592, 48.8022] },
        xizi:        { az: 'Xızı',         en: 'Khizi',       ru: 'Хызы',        c: [40.9100, 49.0700] },
        xocali:      { az: 'Xocalı',       en: 'Khojaly',     ru: 'Ходжалы',     c: [39.9136, 46.7928] },
        xocavend:    { az: 'Xocavənd',     en: 'Khojavend',   ru: 'Ходжавенд',   c: [39.7894, 47.1108] },
        yardimli:    { az: 'Yardımlı',     en: 'Yardimli',    ru: 'Ярдымлы',     c: [38.9064, 48.2397] },
        zagatala:    { az: 'Zaqatala',      en: 'Zagatala',    ru: 'Закатала',    c: [41.6308, 46.6444] },
        zengilan:    { az: 'Zəngilan',     en: 'Zangilan',    ru: 'Зангилан',    c: [39.0853, 46.6528] },
        zerdab:      { az: 'Zərdab',       en: 'Zardab',      ru: 'Зардоб',      c: [40.2178, 47.7136] }
    };

    /* =============================================
       DOM elementləri
       ============================================= */
    var regionSelect   = document.getElementById('bbRegionSelect');
    var prayerGrid     = document.getElementById('bbPrayerGrid');
    var dateEl         = document.getElementById('bbPrayerDate');
    var methodEl       = document.getElementById('bbPrayerMethod');
    var todaySection   = document.getElementById('bbTodaySection');
    var monthlySection = document.getElementById('bbMonthlySection');
    var monthLabel     = document.getElementById('bbMonthLabel');
    var monthPrev      = document.getElementById('bbMonthPrev');
    var monthNext      = document.getElementById('bbMonthNext');
    var monthlyBody    = document.getElementById('bbMonthlyBody');

    if (!regionSelect) return;

    /* =============================================
       Vəziyyət
       ============================================= */
    var currentRegion = null;
    var todayDate     = new Date();
    var currentYear   = todayDate.getFullYear();
    var currentMonth  = todayDate.getMonth();

    /* =============================================
       Dropdown-u doldur (ada görə sıralı)
       ============================================= */
    function populateRegions() {
        var entries = [];
        for (var key in REGIONS) {
            if (REGIONS.hasOwnProperty(key)) {
                entries.push({ key: key, name: REGIONS[key][LANG] || REGIONS[key].az });
            }
        }
        entries.sort(function (a, b) {
            return a.name.localeCompare(b.name, LANG);
        });

        for (var i = 0; i < entries.length; i++) {
            var opt = document.createElement('option');
            opt.value = entries[i].key;
            opt.textContent = entries[i].name;
            regionSelect.appendChild(opt);
        }

        var saved = getCookie('bb_region');
        var defaultRegion = (saved && REGIONS[saved]) ? saved : 'baki';
        regionSelect.value = defaultRegion;
        onRegionChange(defaultRegion);
    }

    /* =============================================
       Rayon dəyişdikdə
       ============================================= */
    function onRegionChange(regionKey) {
        var region = REGIONS[regionKey];
        if (!region) {
            todaySection.style.display = 'none';
            monthlySection.style.display = 'none';
            methodEl.style.display = 'none';
            return;
        }

        currentRegion = regionKey;

        todaySection.style.display = '';
        monthlySection.style.display = '';
        methodEl.style.display = '';

        calculateToday(region);

        try {
            calculateMonthly(region);
        } catch (e) {
            if (typeof console !== 'undefined') console.error('Monthly calculation error:', e);
        }

        setCookie('bb_region', regionKey, 365);
    }

    /* =============================================
       Bu günün namaz vaxtlarını hesabla
       ============================================= */
    function calculateToday(region) {
        var pt = new PrayTime('Jafari');
        var times = pt.location(region.c).timezone(TZ).getTimes();

        document.getElementById('bbTimeFajr').textContent     = times.fajr;
        document.getElementById('bbTimeSunrise').textContent   = times.sunrise;
        document.getElementById('bbTimeDhuhr').textContent     = times.dhuhr;
        document.getElementById('bbTimeAsr').textContent       = times.asr;
        document.getElementById('bbTimeSunset').textContent    = times.sunset;
        document.getElementById('bbTimeMaghrib').textContent   = times.maghrib;
        document.getElementById('bbTimeIsha').textContent      = times.isha;
        document.getElementById('bbTimeMidnight').textContent  = times.midnight;

        highlightCurrent(times);

        var locale = LANG === 'az' ? 'az-AZ' : LANG === 'ru' ? 'ru-RU' : 'en-US';
        dateEl.textContent = new Date().toLocaleDateString(locale, {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    /* =============================================
       Aylıq cədvəli hesabla və render et
       ============================================= */
    function calculateMonthly(region) {
        monthLabel.textContent = months[currentMonth] + ' ' + currentYear;

        var daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        var pt = new PrayTime('Jafari');
        pt.location(region.c).timezone(TZ);

        var isCurrentMonth = (todayDate.getFullYear() === currentYear && todayDate.getMonth() === currentMonth);
        var todayDay = todayDate.getDate();

        var html = '';
        for (var d = 1; d <= daysInMonth; d++) {
            var date = new Date(currentYear, currentMonth, d);
            var times = pt.getTimes(date);
            var isToday = isCurrentMonth && d === todayDay;

            html += '<tr' + (isToday ? ' class="bb-pt-row-today"' : '') + '>' +
                '<td class="bb-pt-cell-day">' + d + '</td>' +
                '<td>' + times.fajr + '</td>' +
                '<td>' + times.sunrise + '</td>' +
                '<td>' + times.dhuhr + '</td>' +
                '<td>' + times.asr + '</td>' +
                '<td>' + times.sunset + '</td>' +
                '<td>' + times.maghrib + '</td>' +
                '<td>' + times.isha + '</td>' +
                '<td>' + times.midnight + '</td>' +
                '</tr>';
        }

        monthlyBody.innerHTML = html;

        if (isCurrentMonth) {
            var todayRow = monthlyBody.querySelector('.bb-pt-row-today');
            if (todayRow) {
                setTimeout(function () {
                    todayRow.scrollIntoView({ block: 'center', behavior: 'smooth' });
                }, 100);
            }
        }
    }

    /* =============================================
       Hazırda hansı namaz vaxtıdırsa, onu vurğula
       ============================================= */
    function highlightCurrent(times) {
        var now = new Date();
        var nowMin = now.getHours() * 60 + now.getMinutes();

        var order = ['fajr', 'sunrise', 'dhuhr', 'asr', 'sunset', 'maghrib', 'isha'];
        var mins = {};
        for (var i = 0; i < order.length; i++) {
            var parts = (times[order[i]] || '').split(':');
            mins[order[i]] = parts.length === 2 ? parseInt(parts[0]) * 60 + parseInt(parts[1]) : -1;
        }

        var active = '';
        for (var j = order.length - 1; j >= 0; j--) {
            if (mins[order[j]] >= 0 && nowMin >= mins[order[j]]) {
                active = order[j];
                break;
            }
        }

        var cards = prayerGrid.querySelectorAll('.bb-pt-card');
        for (var k = 0; k < cards.length; k++) {
            cards[k].classList.remove('bb-pt-card-active');
            if (cards[k].getAttribute('data-prayer') === active) {
                cards[k].classList.add('bb-pt-card-active');
            }
        }
    }

    /* =============================================
       Ay naviqasiyası
       ============================================= */
    function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        } else if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }

        if (currentRegion && REGIONS[currentRegion]) {
            calculateMonthly(REGIONS[currentRegion]);
        }
    }

    /* =============================================
       Cookie helpers
       ============================================= */
    function setCookie(name, value, days) {
        var d = new Date();
        d.setTime(d.getTime() + days * 86400000);
        document.cookie = name + '=' + encodeURIComponent(value) +
            ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^|;)\\s*' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[2]) : '';
    }

    /* =============================================
       Event listener + init
       ============================================= */
    regionSelect.addEventListener('change', function () {
        onRegionChange(this.value);
    });

    monthPrev.addEventListener('click', function () {
        changeMonth(-1);
    });

    monthNext.addEventListener('click', function () {
        changeMonth(1);
    });

    populateRegions();
})();
