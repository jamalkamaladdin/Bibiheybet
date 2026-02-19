<?php
/**
 * Bibiheybet.com - Ana Səhifə
 * 
 * Hero header + bölmələr: Həzrət, Məscid, Ziyarətgahlar, Məqalələr.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// SEO data
$homeTitle = [
    'az' => 'Hz. Həkimə Xanımın (s) Ziyarətgahı',
    'en' => 'Shrine of Hz. Hakimah Khatun (s)',
    'ru' => 'Святыня Хз. Хакимы Хатун (с)',
    'ar' => 'المقام المقدس للسيدة حكيمة خاتون (ع)',
    'fa' => 'زیارتگاه حضرت حکیمه خاتون (س)',
];

$homeDesc = [
    'az' => 'Bibiheybət Ziyarətgahı - Hz. Həkimə Xanımın (s) müqəddəs məzarının rəsmi veb saytı.',
    'en' => 'Bibiheybat Shrine - Official website of the holy shrine of Hz. Hakimah Khatun (s).',
    'ru' => 'Святыня Бибиэйбат - Официальный сайт священной гробницы Хз. Хакимы Хатун (с).',
    'ar' => 'مزار بيبي حيبات - الموقع الرسمي للمقام المقدس للسيدة حكيمة خاتون (ع).',
    'fa' => 'زیارتگاه بی‌بی حیبات - وب‌سایت رسمی زیارتگاه مقدس حضرت حکیمه خاتون (س).',
];

$homeAlternateUrls = [];
foreach (bb_all_langs() as $altLang) {
    $homeAlternateUrls[$altLang] = bb_lang_url('', $altLang);
}

$seoData = [
    'title'           => $homeTitle[$lang] ?? $homeTitle['az'],
    'meta_description' => $homeDesc[$lang] ?? $homeDesc['az'],
    'lang'            => $lang,
    'og_type'         => 'website',
    'schema_type'     => 'WebPage',
    'canonical_url'   => bb_lang_url('', $lang),
    'alternate_urls'  => $homeAlternateUrls,
];

// DB sorğuları
$latestArticles = $db->query(
    "SELECT * FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 3"
)->fetchAll();

$pilgrimagesList = $db->query(
    "SELECT * FROM pilgrimages WHERE status = 'published' ORDER BY sort_order ASC LIMIT 4"
)->fetchAll();

$xanimList = $db->query(
    "SELECT * FROM xanim_haqqinda WHERE status = 'published' ORDER BY sort_order ASC LIMIT 2"
)->fetchAll();

/** Quran bölməsi üçün etiketlər */
$quranLabels = [
    'az' => [
        'title'     => 'Qurani-Kərim dinlə',
        'subtitle'  => 'Tanınmış qarilər tərəfindən 114 surənin tilavəti',
        'reciter'   => 'Qari seçin',
        'surah'     => 'Surə seçin',
        'verses'    => 'ayə',
        'meccan'    => 'Məkkə',
        'medinan'   => 'Mədinə',
    ],
    'en' => [
        'title'     => 'Listen to the Holy Quran',
        'subtitle'  => 'Recitation of 114 surahs by renowned reciters',
        'reciter'   => 'Select reciter',
        'surah'     => 'Select surah',
        'verses'    => 'verses',
        'meccan'    => 'Meccan',
        'medinan'   => 'Medinan',
    ],
    'ru' => [
        'title'     => 'Слушать Священный Коран',
        'subtitle'  => 'Чтение 114 сур известными чтецами',
        'reciter'   => 'Выберите чтеца',
        'surah'     => 'Выберите суру',
        'verses'    => 'аятов',
        'meccan'    => 'Мекканская',
        'medinan'   => 'Мединская',
    ],
    'ar' => [
        'title'     => 'استمع إلى القرآن الكريم',
        'subtitle'  => 'تلاوة ١١٤ سورة بأصوات أشهر القراء',
        'reciter'   => 'اختر القارئ',
        'surah'     => 'اختر السورة',
        'verses'    => 'آية',
        'meccan'    => 'مكية',
        'medinan'   => 'مدنية',
    ],
    'fa' => [
        'title'     => 'گوش دادن به قرآن کریم',
        'subtitle'  => 'تلاوت ۱۱۴ سوره توسط قاریان مشهور',
        'reciter'   => 'قاری را انتخاب کنید',
        'surah'     => 'سوره را انتخاب کنید',
        'verses'    => 'آیه',
        'meccan'    => 'مکی',
        'medinan'   => 'مدنی',
    ],
];
$ql = $quranLabels[$lang] ?? $quranLabels['az'];

/** Çoxdilli statik mətnlər (default-lar, DB-dən override oluna bilər) */
$_defaults = [
    'hero_subtitle' => [
        'az' => 'Fatimeyi-Suğra, Həkimə xanımın müqəddəs ziyarətgahı',
        'en' => 'The holy shrine of Lady Fatima Sugra, Hakima Khanym',
        'ru' => 'Святая обитель госпожи Фатимы ас-Сугры (Хакимы ханум)',
        'ar' => "المقام المقدس للسيدة فاطمة الصغرى\nحكيمة خاتون عليها السلام",
        'fa' => 'زیارتگاه مقدس حضرت فاطمه صغری حکیمه خاتون (سلام‌الله‌علیها)',
    ],
    'hazrat_text' => [
        'az' => 'Həkimə xanım, 9-cu əsrdə yaşamış, İmam Musa Kazımın (a.s) qızı və İmam Əli Rzanın (a.s) bacısıdır. O, xəlifələrin təqibindən qaçaraq Bakıya sığınmış və burada "Heybət xanımın bibisi" adı ilə tanınmışdır. Vəfatından sonra, məzarı üzərində inşa edilən məscid və türbə, zamanla Bibi Heybət adı ilə məşhurlaşmışdır.',
        'en' => 'Lady Hakima was a 9th-century figure, the daughter of Imam Musa al-Kazim (a.s.) and the sister of Imam Ali al-Ridha (a.s.). Fleeing persecution by the caliphs, she sought refuge in Baku, where she became known as "the aunt of Heybat Khanum." After her passing, a mosque and mausoleum were constructed over her grave, which in time became renowned as Bibi-Heybat.',
        'ru' => 'Хакима ханым — религиозная деятельница IX века, дочь Имама Мусы аль-Казима (а.с.) и сестра Имама Али ар-Ризы (а.с.). Спасаясь от преследований халифов, она нашла убежище в Баку, где была известна как «тётя Хейбат ханым». После её кончины над её могилой были возведены мечеть и мавзолей, которые со временем стали известны под названием Биби-Хейбат.',
    ],
    'mosque_text_1' => [
        'az' => 'Bibiheybət məscidi XIII əsrdə Şirvanşah II Fərruxzad ibn Axsitan tərəfindən inşa etdirilib. 1281-1282-ci illərdə tikilən məscidin divarındakı tarixi kitabələrdə onun memarının Mahmud ibn Sədi olduğu qeyd edilib.',
        'en' => 'Bibi-Heybat Mosque was built in the 13th century by Shirvanshah II Farrukhzad ibn Akhsitan. Historical inscriptions on the mosque\'s walls, constructed between 1281 and 1282, state that its architect was Mahmud ibn Sadi.',
        'ru' => 'Мечеть Биби-Хейбат была построена в XIII веке Ширваншахом II Фаррухзадом ибн Ахситаном. Исторические надписи на стенах мечети, возведённой в 1281–1282 годах, указывают, что архитектором был Махмуд ибн Сади.',
    ],
    'mosque_strong' => [
        'az' => 'Nə inam öldü, nə iman',
        'en' => 'Neither faith died, nor belief',
        'ru' => 'Ни вера не умерла, ни убеждение',
    ],
    'mosque_text_2' => [
        'az' => '1934-cü ilin sentyabr ayında kommunist rejimi tərəfindən ziyarətgah dağıdılaraq yerlə-yeksan edilib. Amma qadağalara baxmayaraq, inanclı insanlar yenə bu yerləri unutmayıblar. 1997-ci il iyulun 23-də Həzrəti Peyğəmbərin mövludu günü məscidin təməli qoyulur. Tikinti işləri bir ilə başa çatır və məscid möminlərin ixtiyarına verilir.',
        'en' => 'In September 1934, the Communist regime destroyed the shrine, reducing it to ruins. Despite these prohibitions, devout people never forgot this sacred place. On July 23, 1997, coinciding with the Prophet\'s birthday, the foundation of the mosque was laid. Construction was completed within a year, and the mosque was returned to the worshippers.',
        'ru' => 'В сентябре 1934 года коммунистический режим разрушил святыню, полностью её уничтожив. Но несмотря на запреты, верующие никогда не забывали это святое место. 23 июля 1997 года, в день рождения Пророка, был заложен фундамент новой мечети. Строительство завершилось в течение года, и мечеть вновь стала доступна для прихожан.',
    ],
    'read_more' => [
        'az' => 'Daha ətraflı',
        'en' => 'Read more',
        'ru' => 'Подробнее',
        'ar' => 'اقرأ المزيد',
        'fa' => 'بیشتر بخوانید',
    ],
    'show_more' => [
        'az' => 'Daha çox',
        'en' => 'Show more',
        'ru' => 'Показать ещё',
        'ar' => 'عرض المزيد',
        'fa' => 'نمایش بیشتر',
    ],
    'pilgrimages_title' => [
        'az' => 'Ziyarətgahlar',
        'en' => 'Pilgrimages',
        'ru' => 'Святыни',
        'ar' => 'المقامات والمزارات',
        'fa' => 'زیارتگاه‌ها',
    ],
    'articles_title' => [
        'az' => 'Məqalələr',
        'en' => 'Articles',
        'ru' => 'Статьи',
        'ar' => 'المقالات',
        'fa' => 'مقالات',
    ],
    'xanim_title' => [
        'az' => 'Həkimə xanım haqqında',
        'en' => 'About Lady Hakima',
        'ru' => 'О Хакиме ханым',
        'ar' => 'عن السيدة حكيمة',
        'fa' => 'درباره حکیمه خانم',
    ],
];

$_strings = bb_load_page_contents($db, 'home', $_defaults);

/** Dil-uyğun mətn qaytarır */
$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-home',
    'is_home'    => true,
    'extra_css'  => ['/public/assets/css/home.css', '/public/assets/css/prayer-times.css', '/public/assets/css/home-quran.css'],
    'hero_subtitle' => $_strings['hero_subtitle'][$lang] ?? $_strings['hero_subtitle']['az'] ?? '',
]);

$ptLabels = [
    'az' => ['fajr'=>'Sübh','sunrise'=>'Günəş','dhuhr'=>'Zöhr','asr'=>'Əsr','sunset'=>'Qürub','maghrib'=>'Məğrib','isha'=>'İşa','midnight'=>'Gecə yarısı'],
    'en' => ['fajr'=>'Fajr','sunrise'=>'Sunrise','dhuhr'=>'Dhuhr','asr'=>'Asr','sunset'=>'Sunset','maghrib'=>'Maghrib','isha'=>'Isha','midnight'=>'Midnight'],
    'ru' => ['fajr'=>'Фаджр','sunrise'=>'Восход','dhuhr'=>'Зухр','asr'=>'Аср','sunset'=>'Закат','maghrib'=>'Магриб','isha'=>'Иша','midnight'=>'Полночь'],
];
$ptL = $ptLabels[$lang] ?? $ptLabels['az'];
$ptUrl = bb_lang_url(bb_get_route('prayer-times', $lang) . '/', $lang);
$ptKeys = ['fajr','sunrise','dhuhr','asr','sunset','maghrib','isha','midnight'];
?>

    <!-- ==========================================
         Namaz vaxtları strip
         ========================================== -->
    <section class="bb-home-prayer-strip" id="bbHomePrayerStrip" data-url="<?= bb_sanitize($ptUrl) ?>">
        <div class="bb-container bb-pt-strip-wrap">
            <button class="bb-pt-strip-arrow bb-pt-strip-arrow-left" id="bbStripLeft" aria-label="Sol">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="bb-pt-strip" id="bbStripScroll">
                <?php foreach ($ptKeys as $key): ?>
                <div class="bb-pt-strip-item" data-prayer="<?= $key ?>">
                    <span class="bb-pt-strip-label"><?= bb_sanitize($ptL[$key]) ?></span>
                    <span class="bb-pt-strip-time" id="bbHomeTime_<?= $key ?>">--:--</span>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="bb-pt-strip-arrow bb-pt-strip-arrow-right" id="bbStripRight" aria-label="Sağ">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </section>

    <!-- ==========================================
         Bölmə: Həzrət haqqında
         ========================================== -->
    <section class="bb-home-hazrat" data-animate>
        <div class="bb-container bb-text-center">
            <div class="bb-home-hazrat-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>
            <?php if (!empty($xanimList)): ?>
            <div class="bb-home-xanim-subsection">
                <h2 class="bb-home-section-title"><?= bb_sanitize($t('xanim_title')) ?></h2>
                <div class="bb-separator bb-separator-center"></div>

                <div class="bb-home-pilgrimages-grid">
                    <?php foreach ($xanimList as $x): ?>
                        <?php
                            $xName  = bb_get_field($x, 'name', $lang);
                            $xSlug  = bb_get_field($x, 'slug', $lang);
                            $xImage = bb_get_featured_image($x, $lang);
                            $xUrl   = bb_lang_url(bb_get_route('xanim', $lang) . '/' . $xSlug, $lang);
                        ?>
                    <a href="<?= bb_sanitize($xUrl) ?>" class="bb-home-pilgrimage-item">
                        <div class="bb-home-pilgrimage-frame">
                            <?php if ($xImage): ?>
                            <img class="bb-home-pilgrimage-photo"
                                 src="/<?= bb_sanitize($xImage) ?>"
                                 alt="<?= bb_sanitize($xName) ?>"
                                 loading="lazy">
                            <?php else: ?>
                            <div class="bb-home-pilgrimage-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        <h3 class="bb-home-pilgrimage-title"><?= bb_sanitize($xName) ?></h3>
                    </a>
                    <?php endforeach; ?>
                </div>

                <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('xanim-haqqinda', $lang) . '/', $lang)) ?>"
                   class="bb-btn bb-btn-gold bb-home-more-btn">
                    <?= bb_sanitize($t('show_more')) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ==========================================
         Bölmə: Məscid haqqında
         ========================================== -->
    <section class="bb-home-mosque" data-animate>
        <div class="bb-home-mosque-naxis" aria-hidden="true">
            <img src="/public/assets/img/naxis.png" alt="">
        </div>
        <div class="bb-home-mosque-bg" aria-hidden="true">
            <img src="/public/assets/img/bibiheybet.png" alt="">
        </div>
        <div class="bb-container">
            <div class="bb-home-mosque-content">
                <p class="bb-home-mosque-text"><?= bb_sanitize($t('mosque_text_1')) ?></p>
                <p class="bb-home-mosque-strong"><?= bb_sanitize($t('mosque_strong')) ?></p>
                <p class="bb-home-mosque-text"><?= bb_sanitize($t('mosque_text_2')) ?></p>
                <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('about-mosque', $lang) . '/', $lang)) ?>"
                   class="bb-btn bb-btn-gold">
                    <?= bb_sanitize($t('read_more')) ?>
                </a>
            </div>
        </div>
    </section>

    <!-- ==========================================
         Bölmə: Quran Dinlə
         ========================================== -->
    <section class="bb-home-quran" data-animate>
        <div class="bb-home-quran-bg" aria-hidden="true">
            <img src="/public/assets/img/bg.png" alt="">
        </div>
        <div class="bb-container bb-text-center">
            <div class="bb-home-quran-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>

            <h2 class="bb-home-section-title"><?= bb_sanitize($ql['title']) ?></h2>
            <div class="bb-separator bb-separator-center"></div>
            <p class="bb-home-quran-subtitle"><?= bb_sanitize($ql['subtitle']) ?></p>

            <!-- Player -->
            <div class="bb-home-qp" id="bbHomeQuranPlayer">
                <!-- Surə adı (ərəbcə) -->
                <div class="bb-home-qp-display" id="bbHQDisplay">
                    <span class="bb-home-qp-arabic" id="bbHQArabic">بِسْمِ ٱللَّهِ ٱلرَّحْمَـٰنِ ٱلرَّحِيمِ</span>
                    <span class="bb-home-qp-name" id="bbHQName"></span>
                </div>

                <!-- Kontrol paneli -->
                <div class="bb-home-qp-controls">
                    <button type="button" class="bb-home-qp-btn bb-home-qp-prev" id="bbHQPrev" aria-label="Previous">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                    </button>
                    <button type="button" class="bb-home-qp-play" id="bbHQPlayBtn" aria-label="Play">
                        <svg class="bb-hq-icon-play" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <svg class="bb-hq-icon-pause" width="28" height="28" viewBox="0 0 24 24" fill="currentColor" style="display:none">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                        </svg>
                        <svg class="bb-hq-icon-loading" width="28" height="28" viewBox="0 0 24 24" fill="none" style="display:none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="31.4 31.4" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" values="0 12 12;360 12 12" dur="1s" repeatCount="indefinite"/>
                            </circle>
                        </svg>
                    </button>
                    <button type="button" class="bb-home-qp-btn bb-home-qp-next" id="bbHQNext" aria-label="Next">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                    </button>
                </div>

                <!-- Progress -->
                <div class="bb-home-qp-time">
                    <span id="bbHQCurrentTime">0:00</span>
                    <div class="bb-home-qp-progress" id="bbHQProgress">
                        <div class="bb-home-qp-progress-bar" id="bbHQProgressBar"></div>
                    </div>
                    <span id="bbHQDuration">0:00</span>
                </div>

                <!-- Seçimlər -->
                <div class="bb-home-qp-selects">
                    <select id="bbHQSurahSelect" class="bb-home-qp-select" aria-label="<?= bb_sanitize($ql['surah']) ?>">
                        <option value=""><?= bb_sanitize($ql['surah']) ?></option>
                    </select>
                    <select id="bbHQReciterSelect" class="bb-home-qp-select" aria-label="<?= bb_sanitize($ql['reciter']) ?>">
                        <option value=""><?= bb_sanitize($ql['reciter']) ?></option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         Bölmə: Ziyarətgahlar
         ========================================== -->
    <?php if (!empty($pilgrimagesList)): ?>
    <section class="bb-home-pilgrimages" data-animate>
        <div class="bb-home-pilgrimages-bg" aria-hidden="true">
            <img src="/public/assets/img/bg.png" alt="">
        </div>
        <div class="bb-container bb-text-center">
            <h2 class="bb-home-section-title"><?= bb_sanitize($t('pilgrimages_title')) ?></h2>
            <div class="bb-separator bb-separator-center"></div>

            <div class="bb-home-pilgrimages-grid">
                <?php foreach ($pilgrimagesList as $p): ?>
                    <?php
                        $pName  = bb_get_field($p, 'name', $lang);
                        $pSlug  = bb_get_field($p, 'slug', $lang);
                        $pImage = bb_get_featured_image($p, $lang);
                        $pUrl   = bb_lang_url(bb_get_route('pilgrimage', $lang) . '/' . $pSlug, $lang);
                    ?>
                <a href="<?= bb_sanitize($pUrl) ?>" class="bb-home-pilgrimage-item">
                    <div class="bb-home-pilgrimage-frame">
                        <?php if ($pImage): ?>
                        <img class="bb-home-pilgrimage-photo"
                             src="/<?= bb_sanitize($pImage) ?>"
                             alt="<?= bb_sanitize($pName) ?>"
                             loading="lazy">
                        <?php else: ?>
                        <div class="bb-home-pilgrimage-placeholder"></div>
                        <?php endif; ?>
                    </div>
                    <h3 class="bb-home-pilgrimage-title"><?= bb_sanitize($pName) ?></h3>
                </a>
                <?php endforeach; ?>
            </div>

            <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('pilgrimages', $lang) . '/', $lang)) ?>"
               class="bb-btn bb-btn-gold bb-home-more-btn">
                <?= bb_sanitize($t('show_more')) ?>
            </a>
        </div>
    </section>
    <?php endif; ?>

    <!-- ==========================================
         Bölmə: Son Məqalələr
         ========================================== -->
    <?php if (!empty($latestArticles)): ?>
    <section class="bb-home-articles" data-animate>
        <div class="bb-container bb-text-center">
            <div class="bb-home-articles-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>

            <h2 class="bb-home-section-title"><?= bb_sanitize($t('articles_title')) ?></h2>
            <div class="bb-separator bb-separator-center"></div>

            <div class="bb-home-articles-grid">
                <?php foreach ($latestArticles as $a): ?>
                    <?php
                        $aTitle = bb_get_field($a, 'title', $lang);
                        $aSlug  = bb_get_field($a, 'slug', $lang);
                        $aImage = bb_get_featured_image($a, $lang);
                        $aUrl   = bb_lang_url(bb_get_route('article', $lang) . '/' . $aSlug, $lang);
                    ?>
                <a href="<?= bb_sanitize($aUrl) ?>" class="bb-home-article-card">
                    <div class="bb-home-article-img">
                        <?php if ($aImage): ?>
                        <img src="/<?= bb_sanitize($aImage) ?>"
                             alt="<?= bb_sanitize($aTitle) ?>"
                             loading="lazy">
                        <?php else: ?>
                        <div class="bb-home-article-placeholder"></div>
                        <?php endif; ?>
                    </div>
                    <h3 class="bb-home-article-title"><?= bb_sanitize($aTitle) ?></h3>
                </a>
                <?php endforeach; ?>
            </div>

            <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('articles', $lang) . '/', $lang)) ?>"
               class="bb-btn bb-btn-gold bb-home-more-btn">
                <?= bb_sanitize($t('show_more')) ?>
            </a>
        </div>
    </section>
    <?php endif; ?>

<?php
?>
    <script>
        window.BB_HQ_LABELS = <?= json_encode($ql, JSON_UNESCAPED_UNICODE) ?>;
        window.BB_HQ_LANG = <?= json_encode($lang) ?>;
    </script>
<?php
bb_frontend_footer([
    'extra_js' => [
        'https://cdn.jsdelivr.net/npm/praytime/src/praytime.js',
        '/public/assets/js/home.js',
        '/public/assets/js/home-quran.js',
    ],
    'is_home' => true,
]);
