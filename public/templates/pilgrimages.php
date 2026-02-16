<?php
/**
 * Bibiheybet.com - Ziyarətgah Siyahısı
 * 
 * FAZA 9: Tam funksional ziyarətgah siyahı səhifəsi.
 * Shape frame-li grid kartlar, pagination.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$pageTitle = [
    'az' => 'Ziyarətgahlar',
    'en' => 'Holy Shrines',
    'ru' => 'Святыни',
    'ar' => 'المقامات والمزارات',
    'fa' => 'زیارتگاه‌ها',
];

$pageDesc = [
    'az' => 'Bibiheybət ziyarətgahı ərazisindəki müqəddəs məzar və türbələr.',
    'en' => 'Sacred tombs and mausoleums within the Bibiheybat shrine complex.',
    'ru' => 'Священные гробницы и мавзолеи на территории святыни Бибиэйбат.',
    'ar' => 'المقابر والأضرحة المقدسة في مجمع مزار بيبي حيبات.',
    'fa' => 'مقابر و آرامگاه‌های مقدس در مجموعه زیارتگاه بی‌بی حیبات.',
];

$seoData = [
    'title'            => $pageTitle[$lang] ?? $pageTitle['az'],
    'meta_description' => $pageDesc[$lang] ?? $pageDesc['az'],
    'lang'             => $lang,
    'og_type'          => 'website',
    'schema_type'      => 'WebPage',
    'canonical_url'    => bb_lang_url(bb_get_route('pilgrimages', $lang) . '/', $lang),
    'alternate_urls'   => bb_get_alternate_urls('pilgrimages', []),
];

// DB: Bütün published ziyarətgahları çək
$pilgrimages = $db->query(
    "SELECT * FROM pilgrimages WHERE status = 'published' ORDER BY sort_order ASC, created_at DESC"
)->fetchAll();

// Strings
$_strings = [
    'subtitle' => [
        'az' => 'Bibiheybət ziyarətgahı ərazisindəki müqəddəs məzar və türbələr',
        'en' => 'Sacred tombs and mausoleums within the Bibiheybat shrine complex',
        'ru' => 'Священные гробницы и мавзолеи на территории святыни Бибиэйбат',
        'ar' => 'المقابر والأضرحة المقدسة في مجمع مزار بيبي حيبات',
        'fa' => 'مقابر و آرامگاه‌های مقدس در مجموعه زیارتگاه بی‌بی حیبات',
    ],
    'empty' => [
        'az' => 'Hələlik ziyarətgah əlavə edilməyib.',
        'en' => 'No pilgrimages have been added yet.',
        'ru' => 'Святыни пока не добавлены.',
        'ar' => 'لم تُضف أي مزارات بعد.',
        'fa' => 'هنوز زیارتگاهی اضافه نشده است.',
    ],
];

$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-pilgrimages',
    'extra_css'  => ['/public/assets/css/pilgrimages.css'],
]);
?>

    <!-- Ziyarətgah siyahısı -->
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

    <?php if (!empty($pilgrimages)): ?>
    <section class="bb-pilgrimages-grid-section" data-animate>
        <div class="bb-container">
            <div class="bb-pilgrimages-grid">
                <?php foreach ($pilgrimages as $p): ?>
                    <?php
                        $pName  = bb_get_field($p, 'name', $lang);
                        $pSlug  = bb_get_field($p, 'slug', $lang);
                        $pImage = bb_get_featured_image($p, $lang);
                        $pUrl   = bb_lang_url(bb_get_route('pilgrimage', $lang) . '/' . $pSlug, $lang);
                        $pExcerpt = bb_truncate(bb_get_field($p, 'content', $lang) ?? '', 120);
                    ?>
                <a href="<?= bb_sanitize($pUrl) ?>" class="bb-pilgrimages-card">
                    <div class="bb-pilgrimages-card-frame">
                        <?php if ($pImage): ?>
                        <img class="bb-pilgrimages-card-photo"
                             src="/<?= bb_sanitize($pImage) ?>"
                             alt="<?= bb_sanitize($pName) ?>"
                             loading="lazy">
                        <?php else: ?>
                        <div class="bb-pilgrimages-card-placeholder"></div>
                        <?php endif; ?>
                        <img class="bb-pilgrimages-card-shape"
                             src="/public/assets/img/shape.png"
                             alt="" aria-hidden="true">
                    </div>
                    <div class="bb-pilgrimages-card-info">
                        <h2 class="bb-pilgrimages-card-title"><?= bb_sanitize($pName) ?></h2>
                        <?php if ($pExcerpt): ?>
                        <p class="bb-pilgrimages-card-excerpt"><?= bb_sanitize($pExcerpt) ?></p>
                        <?php endif; ?>
                    </div>
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
