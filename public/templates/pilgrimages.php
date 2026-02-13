<?php
/**
 * Bibiheybet.com - Ziyarətgah Siyahısı
 * 
 * FAZA 9-da tam funksionallıqla tamamlanacaq.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$pageTitle = [
    'az' => 'Ziyarətgahlar',
    'en' => 'Pilgrimages',
    'ru' => 'Святыни',
];

$seoData = [
    'title'          => $pageTitle[$lang] ?? $pageTitle['az'],
    'lang'           => $lang,
    'og_type'        => 'website',
    'schema_type'    => 'WebPage',
    'canonical_url'  => bb_lang_url(bb_get_route('pilgrimages', $lang) . '/', $lang),
    'alternate_urls' => bb_get_alternate_urls('pilgrimages', []),
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-pilgrimages',
]);
?>

    <section class="bb-section">
        <div class="bb-container">
            <h1><?= bb_sanitize($pageTitle[$lang] ?? $pageTitle['az']) ?></h1>
            <div class="bb-separator"></div>
            <!-- FAZA 9: Ziyarətgah kartları -->
        </div>
    </section>

<?php
bb_frontend_footer();
