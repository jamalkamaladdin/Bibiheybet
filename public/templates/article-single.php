<?php
/**
 * Bibiheybet.com - Tək Məqalə
 * 
 * FAZA 8-də tam funksionallıqla tamamlanacaq.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// Məqaləni DB-dən çək (FAZA 8-də tamamlanacaq)
$article = null;
$slugField = 'slug_' . $lang;
$stmt = $db->prepare("SELECT * FROM articles WHERE {$slugField} = :slug AND status = 'published' LIMIT 1");
$stmt->execute([':slug' => $pageSlug]);
$article = $stmt->fetch();

// Slug tapılmadısa AZ fallback
if (!$article && $lang !== 'az') {
    $stmt = $db->prepare("SELECT * FROM articles WHERE slug_az = :slug AND status = 'published' LIMIT 1");
    $stmt->execute([':slug' => $pageSlug]);
    $article = $stmt->fetch();
}

if (!$article) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

// Alternate URL-lər (dil switch üçün)
$GLOBALS['bb_page_context']['alternate_urls'] = bb_get_alternate_urls('article', [
    'az' => $article['slug_az'] ?? '',
    'en' => $article['slug_en'] ?? '',
    'ru' => $article['slug_ru'] ?? '',
    'ar' => $article['slug_ar'] ?? '',
    'fa' => $article['slug_fa'] ?? '',
]);

$seoData = bb_prepare_seo_data($article, 'article', $lang);
$seoData['canonical_url'] = bb_lang_url(
    bb_get_route('article', $lang) . '/' . bb_get_field($article, 'slug', $lang),
    $lang
);
$seoData['alternate_urls'] = $GLOBALS['bb_page_context']['alternate_urls'];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-article-single',
]);
?>

    <div class="bb-container">
        <?= bb_render_breadcrumbs([
            ['label' => ['az'=>'Ana səhifə','en'=>'Home','ru'=>'Главная','ar'=>'الرئيسية','fa'=>'خانه'][$lang] ?? 'Ana səhifə', 'url' => bb_lang_url('', $lang)],
            ['label' => ['az'=>'Məqalələr','en'=>'Articles','ru'=>'Статьи','ar'=>'المقالات','fa'=>'مقالات'][$lang] ?? 'Məqalələr', 'url' => bb_lang_url(bb_get_route('articles', $lang) . '/', $lang)],
            ['label' => bb_get_field($article, 'title', $lang)],
        ]) ?>
    </div>

    <section class="bb-section">
        <div class="bb-container-narrow">
            <h1><?= bb_sanitize(bb_get_field($article, 'title', $lang)) ?></h1>
            <div class="bb-separator"></div>
            <p class="bb-text-muted"><?= bb_format_date($article['published_at'] ?? $article['created_at'], $lang) ?></p>
            <!-- FAZA 8: Featured image, kontent, paylaşma, əlaqəli məqalələr -->
            <div class="bb-article-content">
                <?= bb_get_field($article, 'content', $lang) ?>
            </div>
        </div>
    </section>

<?php
bb_frontend_footer();
