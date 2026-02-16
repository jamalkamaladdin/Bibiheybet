<?php
/**
 * Bibiheybet.com - Axtarış Nəticələri
 * 
 * Məqalələr və Ziyarətgahlar üzrə axtarış.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$pageTitle = [
    'az' => 'Axtarış',
    'en' => 'Search',
    'ru' => 'Поиск',
    'ar' => 'بحث',
    'fa' => 'جستجو',
];

$_strings = [
    'placeholder' => [
        'az' => 'Məqalə və ya ziyarətgah axtar...',
        'en' => 'Search articles or shrines...',
        'ru' => 'Искать статьи или святыни...',
        'ar' => 'ابحث عن مقالات أو مزارات...',
        'fa' => 'جستجوی مقالات یا زیارتگاه‌ها...',
    ],
    'results_for' => [
        'az' => 'nəticə tapıldı',
        'en' => 'results found',
        'ru' => 'результатов найдено',
        'ar' => 'نتائج',
        'fa' => 'نتیجه یافت شد',
    ],
    'no_results' => [
        'az' => 'Heç bir nəticə tapılmadı.',
        'en' => 'No results found.',
        'ru' => 'Ничего не найдено.',
        'ar' => 'لم يتم العثور على نتائج.',
        'fa' => 'نتیجه‌ای یافت نشد.',
    ],
    'no_query' => [
        'az' => 'Axtarış sorğusu daxil edin.',
        'en' => 'Enter a search query.',
        'ru' => 'Введите поисковый запрос.',
        'ar' => 'أدخل استعلام بحث.',
        'fa' => 'عبارت جستجو را وارد کنید.',
    ],
    'articles_label' => [
        'az' => 'Məqalə',
        'en' => 'Article',
        'ru' => 'Статья',
        'ar' => 'مقالة',
        'fa' => 'مقاله',
    ],
    'pilgrimages_label' => [
        'az' => 'Ziyarətgah',
        'en' => 'Shrine',
        'ru' => 'Святыня',
        'ar' => 'مزار',
        'fa' => 'زیارتگاه',
    ],
];

$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

$query = trim($_GET['q'] ?? '');
$results = [];
$totalResults = 0;

if (!empty($query) && mb_strlen($query) >= 2) {
    $searchTerm = '%' . $query . '%';

    // Məqalələrdə axtar
    $articleStmt = $db->prepare(
        "SELECT *, 'article' AS result_type FROM articles
         WHERE status = 'published'
         AND (title_{$lang} LIKE :q1 OR excerpt_{$lang} LIKE :q2 OR content_{$lang} LIKE :q3)
         ORDER BY published_at DESC
         LIMIT 20"
    );
    $articleStmt->execute([':q1' => $searchTerm, ':q2' => $searchTerm, ':q3' => $searchTerm]);
    $articleResults = $articleStmt->fetchAll();

    // Ziyarətgahlarda axtar
    $pilgrimageStmt = $db->prepare(
        "SELECT *, 'pilgrimage' AS result_type FROM pilgrimages
         WHERE status = 'published'
         AND (name_{$lang} LIKE :q1 OR content_{$lang} LIKE :q2)
         ORDER BY sort_order ASC
         LIMIT 20"
    );
    $pilgrimageStmt->execute([':q1' => $searchTerm, ':q2' => $searchTerm]);
    $pilgrimageResults = $pilgrimageStmt->fetchAll();

    $results = array_merge($pilgrimageResults, $articleResults);
    $totalResults = count($results);
}

$seoData = [
    'title'     => ($pageTitle[$lang] ?? $pageTitle['az']) . (!empty($query) ? ': ' . $query : ''),
    'lang'      => $lang,
    'og_type'   => 'website',
    'canonical_url' => bb_lang_url(bb_get_route('search', $lang) . '/', $lang),
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-search',
    'extra_css'  => ['/public/assets/css/search.css'],
]);
?>

    <div class="bb-container">
        <?= bb_render_breadcrumbs([
            ['label' => ['az'=>'Ana səhifə','en'=>'Home','ru'=>'Главная','ar'=>'الرئيسية','fa'=>'خانه'][$lang] ?? 'Ana səhifə', 'url' => bb_lang_url('', $lang)],
            ['label' => $pageTitle[$lang] ?? $pageTitle['az']],
        ]) ?>
    </div>

    <section class="bb-search-section">
        <div class="bb-container">
            <h1 class="bb-search-title"><?= bb_sanitize($pageTitle[$lang] ?? $pageTitle['az']) ?></h1>
            <div class="bb-separator bb-separator-center"></div>

            <!-- Axtarış formu -->
            <form action="<?= bb_lang_url(bb_get_route('search', $lang) . '/', $lang) ?>" method="get" class="bb-search-form">
                <div class="bb-search-input-wrap">
                    <svg class="bb-search-input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="q" class="bb-search-input" value="<?= bb_sanitize($query) ?>" placeholder="<?= bb_sanitize($t('placeholder')) ?>" autofocus autocomplete="off">
                </div>
            </form>

            <?php if (!empty($query)): ?>
                <?php if ($totalResults > 0): ?>
                <p class="bb-search-count"><?= $totalResults ?> <?= bb_sanitize($t('results_for')) ?></p>

                <div class="bb-search-results">
                    <?php foreach ($results as $item): ?>
                        <?php
                            $isArticle = ($item['result_type'] === 'article');

                            if ($isArticle) {
                                $itemTitle = bb_get_field($item, 'title', $lang);
                                $itemSlug  = bb_get_field($item, 'slug', $lang);
                                $itemImage = bb_get_featured_image($item, $lang);
                                $itemUrl   = bb_lang_url(bb_get_route('article', $lang) . '/' . $itemSlug, $lang);
                                $itemExcerpt = bb_get_field($item, 'excerpt', $lang);
                                if (empty($itemExcerpt)) {
                                    $itemExcerpt = bb_truncate(bb_get_field($item, 'content', $lang) ?? '', 160);
                                }
                                $typeLabel = $t('articles_label');
                            } else {
                                $itemTitle = bb_get_field($item, 'name', $lang);
                                $itemSlug  = bb_get_field($item, 'slug', $lang);
                                $itemImage = bb_get_featured_image($item, $lang);
                                $itemUrl   = bb_lang_url(bb_get_route('pilgrimage', $lang) . '/' . $itemSlug, $lang);
                                $itemExcerpt = bb_truncate(bb_get_field($item, 'content', $lang) ?? '', 160);
                                $typeLabel = $t('pilgrimages_label');
                            }
                        ?>
                    <a href="<?= bb_sanitize($itemUrl) ?>" class="bb-search-card">
                        <div class="bb-search-card-img">
                            <?php if ($itemImage): ?>
                            <img src="/<?= bb_sanitize($itemImage) ?>" alt="<?= bb_sanitize($itemTitle) ?>" loading="lazy">
                            <?php else: ?>
                            <div class="bb-search-card-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        <div class="bb-search-card-body">
                            <span class="bb-search-card-type"><?= bb_sanitize($typeLabel) ?></span>
                            <h2 class="bb-search-card-title"><?= bb_sanitize($itemTitle) ?></h2>
                            <?php if ($itemExcerpt): ?>
                            <p class="bb-search-card-excerpt"><?= bb_sanitize($itemExcerpt) ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php else: ?>
                <div class="bb-search-empty">
                    <p><?= bb_sanitize($t('no_results')) ?></p>
                </div>
                <?php endif; ?>
            <?php elseif (isset($_GET['q'])): ?>
                <div class="bb-search-empty">
                    <p><?= bb_sanitize($t('no_query')) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php
bb_frontend_footer();
