<?php
/**
 * Bibiheybet.com - Məqalə Siyahısı (Arxiv)
 * 
 * FAZA 8-də tam funksionallıqla tamamlanacaq.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$pageTitle = [
    'az' => 'Məqalələr',
    'en' => 'Articles',
    'ru' => 'Статьи',
];

$seoData = [
    'title'          => $pageTitle[$lang] ?? $pageTitle['az'],
    'lang'           => $lang,
    'og_type'        => 'website',
    'schema_type'    => 'WebPage',
    'canonical_url'  => bb_lang_url(bb_get_route('articles', $lang) . '/', $lang),
    'alternate_urls' => bb_get_alternate_urls('articles', []),
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-articles',
]);
?>

    <section class="bb-section">
        <div class="bb-container">
            <h1><?= bb_sanitize($pageTitle[$lang] ?? $pageTitle['az']) ?></h1>
            <div class="bb-separator"></div>
            <!-- FAZA 8: Kateqoriya filtri, kart grid, pagination -->
        </div>
    </section>

<?php
bb_frontend_footer();
