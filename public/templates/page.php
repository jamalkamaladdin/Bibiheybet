<?php
/**
 * Bibiheybet.com - Statik Səhifə Template
 * 
 * Həzrət haqqında, Məscid haqqında, Dua və ziyarətnamə.
 * FAZA 10-da tam dizaynla tamamlanacaq.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// Statik səhifə data (hər dil üçün)
$staticPages = [
    'about-hazrat' => [
        'title' => [
            'az' => 'Həzrət haqqında',
            'en' => 'About Hazrat',
            'ru' => 'О Хазрат',
        ],
        'description' => [
            'az' => 'Hz. Həkimə Xanım (s) haqqında ətraflı məlumat.',
            'en' => 'Detailed information about Hz. Hakimah Khatun (s).',
            'ru' => 'Подробная информация о Хз. Хакиме Хатун (с).',
        ],
    ],
    'about-mosque' => [
        'title' => [
            'az' => 'Məscid haqqında',
            'en' => 'About Mosque',
            'ru' => 'О Мечети',
        ],
        'description' => [
            'az' => 'Bibiheybət Məscidinin tarixi və memarlığı.',
            'en' => 'History and architecture of Bibiheybat Mosque.',
            'ru' => 'История и архитектура мечети Бибиэйбат.',
        ],
    ],
    'prayers' => [
        'title' => [
            'az' => 'Dua və ziyarətnamə',
            'en' => 'Prayers',
            'ru' => 'Молитвы',
        ],
        'description' => [
            'az' => 'Ziyarətnamə və dua mətnləri.',
            'en' => 'Pilgrimage prayers and texts.',
            'ru' => 'Молитвы и тексты паломничества.',
        ],
    ],
];

$pageInfo = $staticPages[$pageSlug] ?? null;

if (!$pageInfo) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

$title = $pageInfo['title'][$lang] ?? $pageInfo['title']['az'];
$description = $pageInfo['description'][$lang] ?? $pageInfo['description']['az'];

$seoData = [
    'title'            => $title,
    'meta_description' => $description,
    'lang'             => $lang,
    'og_type'          => 'website',
    'schema_type'      => 'WebPage',
    'canonical_url'    => bb_lang_url(bb_get_route($pageSlug, $lang) . '/', $lang),
    'alternate_urls'   => array_combine(
        bb_all_langs(),
        array_map(fn($l) => bb_lang_url(bb_get_route($pageSlug, $l) . '/', $l), bb_all_langs())
    ),
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-static bb-page-' . $pageSlug,
]);
?>

    <section class="bb-section">
        <div class="bb-container-narrow">
            <h1><?= bb_sanitize($title) ?></h1>
            <div class="bb-separator"></div>
            <p class="bb-text-muted"><?= bb_sanitize($description) ?></p>
            <!-- FAZA 10: Statik kontent -->
        </div>
    </section>

<?php
bb_frontend_footer();
