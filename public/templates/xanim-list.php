<?php
/**
 * Bibiheybet.com - Xanım haqqında Siyahısı
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$pageTitle = [
    'az' => 'Həkimə xanım haqqında',
    'en' => 'About Lady Hakima',
    'ru' => 'О Хакиме ханым',
    'ar' => 'عن السيدة حكيمة',
    'fa' => 'درباره حکیمه خانم',
];

$pageDesc = [
    'az' => 'Hz. Həkimə Xanım haqqında məqalələr və materiallar.',
    'en' => 'Articles and materials about Hz. Hakima Khatun.',
    'ru' => 'Статьи и материалы о Хз. Хакиме Хатун.',
    'ar' => 'مقالات ومواد حول السيدة حكيمة خاتون.',
    'fa' => 'مقالات و مطالب درباره حضرت حکیمه خاتون.',
];

$seoData = [
    'title'            => $pageTitle[$lang] ?? $pageTitle['az'],
    'meta_description' => $pageDesc[$lang] ?? $pageDesc['az'],
    'lang'             => $lang,
    'og_type'          => 'website',
    'schema_type'      => 'WebPage',
    'canonical_url'    => bb_lang_url(bb_get_route('about-hazrat', $lang) . '/', $lang),
    'alternate_urls'   => bb_get_alternate_urls('about-hazrat', []),
];

$items = $db->query(
    "SELECT * FROM xanim_haqqinda WHERE status = 'published' ORDER BY sort_order ASC, created_at DESC"
)->fetchAll();

$_strings = [
    'subtitle' => [
        'az' => 'Hz. Həkimə Xanım haqqında məqalələr',
        'en' => 'Articles about Hz. Hakima Khatun',
        'ru' => 'Статьи о Хз. Хакиме Хатун',
        'ar' => 'مقالات حول السيدة حكيمة خاتون',
        'fa' => 'مقالات درباره حضرت حکیمه خاتون',
    ],
    'empty' => [
        'az' => 'Hələlik məqalə əlavə edilməyib.',
        'en' => 'No articles have been added yet.',
        'ru' => 'Статьи пока не добавлены.',
        'ar' => 'لم تُضف أي مقالات بعد.',
        'fa' => 'هنوز مقاله‌ای اضافه نشده است.',
    ],
];

$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-xanim-list',
    'extra_css'  => ['/public/assets/css/pilgrimages.css', '/public/assets/css/home.css'],
]);
?>

    <div class="bb-container">
        <?= bb_render_breadcrumbs([
            ['label' => ['az'=>'Ana səhifə','en'=>'Home','ru'=>'Главная','ar'=>'الرئيسية','fa'=>'خانه'][$lang] ?? 'Ana səhifə', 'url' => bb_lang_url('', $lang)],
            ['label' => $pageTitle[$lang] ?? $pageTitle['az']],
        ]) ?>
    </div>

    <section class="bb-pilgrimages-hero" data-animate>
        <div class="bb-container bb-text-center">
            <div class="bb-pilgrimages-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>
            <h1 class="bb-pilgrimages-title"><?= bb_sanitize($pageTitle[$lang] ?? $pageTitle['az']) ?></h1>
            <div class="bb-separator bb-separator-center"></div>
            <p class="bb-pilgrimages-subtitle"><?= bb_sanitize($t('subtitle')) ?></p>
        </div>
    </section>

    <?php if (!empty($items)): ?>
    <section class="bb-pilgrimages-grid-section" data-animate>
        <div class="bb-container bb-text-center">
            <div class="bb-home-pilgrimages-grid">
                <?php foreach ($items as $x): ?>
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
        </div>
    </section>
    <?php else: ?>
    <section class="bb-pilgrimages-empty">
        <div class="bb-container bb-text-center">
            <p class="bb-text-muted"><?= bb_sanitize($t('empty')) ?></p>
        </div>
    </section>
    <?php endif; ?>

<?php
bb_frontend_footer(['extra_js' => ['/public/assets/js/home.js']]);
