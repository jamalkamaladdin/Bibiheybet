<?php
/**
 * Bibiheybet.com - Tək Ziyarətgah
 * 
 * FAZA 9-da tam funksionallıqla tamamlanacaq.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// Ziyarətgahı DB-dən çək
$pilgrimage = null;
$slugField = 'slug_' . $lang;
$stmt = $db->prepare("SELECT * FROM pilgrimages WHERE {$slugField} = :slug AND status = 'published' LIMIT 1");
$stmt->execute([':slug' => $pageSlug]);
$pilgrimage = $stmt->fetch();

// Slug tapılmadısa AZ fallback
if (!$pilgrimage && $lang !== 'az') {
    $stmt = $db->prepare("SELECT * FROM pilgrimages WHERE slug_az = :slug AND status = 'published' LIMIT 1");
    $stmt->execute([':slug' => $pageSlug]);
    $pilgrimage = $stmt->fetch();
}

if (!$pilgrimage) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

// Alternate URL-lər
$GLOBALS['bb_page_context']['alternate_urls'] = bb_get_alternate_urls('pilgrimage', [
    'az' => $pilgrimage['slug_az'] ?? '',
    'en' => $pilgrimage['slug_en'] ?? '',
    'ru' => $pilgrimage['slug_ru'] ?? '',
]);

$seoData = bb_prepare_seo_data($pilgrimage, 'pilgrimage', $lang);
$seoData['canonical_url'] = bb_lang_url(
    bb_get_route('pilgrimage', $lang) . '/' . bb_get_field($pilgrimage, 'slug', $lang),
    $lang
);
$seoData['alternate_urls'] = $GLOBALS['bb_page_context']['alternate_urls'];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-pilgrimage-single',
]);
?>

    <section class="bb-section">
        <div class="bb-container">
            <h1><?= bb_sanitize(bb_get_field($pilgrimage, 'name', $lang)) ?></h1>
            <div class="bb-separator"></div>
            <!-- FAZA 9: Featured image, kontent, qalereya, paylaşma -->
            <div class="bb-pilgrimage-content">
                <?= bb_get_field($pilgrimage, 'content', $lang) ?>
            </div>
        </div>
    </section>

<?php
bb_frontend_footer();
