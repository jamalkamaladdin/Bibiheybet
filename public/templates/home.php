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
    "SELECT * FROM pilgrimages WHERE status = 'published' ORDER BY sort_order ASC LIMIT 8"
)->fetchAll();

/** Çoxdilli statik mətnlər */
$_strings = [
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
];

/** Dil-uyğun mətn qaytarır */
$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-home',
    'is_home'    => true,
    'extra_css'  => ['/public/assets/css/home.css'],
]);
?>

    <!-- ==========================================
         Bölmə: Həzrət haqqında
         ========================================== -->
    <section class="bb-home-hazrat" data-animate>
        <div class="bb-container bb-text-center">
            <div class="bb-home-hazrat-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>
            <div class="bb-home-hazrat-body">
                <p class="bb-home-hazrat-text"><?= bb_sanitize($t('hazrat_text')) ?></p>
                <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('about-hazrat', $lang) . '/', $lang)) ?>"
                   class="bb-btn bb-btn-gold">
                    <?= bb_sanitize($t('read_more')) ?>
                </a>
            </div>
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
         Bölmə: Ziyarətgahlar
         ========================================== -->
    <?php if (!empty($pilgrimagesList)): ?>
    <section class="bb-home-pilgrimages" data-animate>
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
                        <img class="bb-home-pilgrimage-shape"
                             src="/public/assets/img/shape.png"
                             alt="" aria-hidden="true">
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
                <img src="/public/assets/img/top.webp" alt="">
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
bb_frontend_footer(['extra_js' => ['/public/assets/js/home.js']]);
