<?php
/**
 * Bibiheybet.com - Məqalə Siyahısı (Arxiv)
 * 
 * Kateqoriya filtri, kart grid, pagination.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$pageTitle = [
    'az' => 'Məqalələr',
    'en' => 'Articles',
    'ru' => 'Статьи',
    'ar' => 'المقالات',
    'fa' => 'مقالات',
];

$pageDesc = [
    'az' => 'Bibiheybət ziyarətgahı haqqında məqalələr və yazılar.',
    'en' => 'Articles and writings about Bibiheybat Shrine.',
    'ru' => 'Статьи и записи о святыне Бибиэйбат.',
    'ar' => 'مقالات وكتابات عن مزار بيبي حيبات.',
    'fa' => 'مقالات و نوشته‌ها درباره زیارتگاه بی‌بی حیبات.',
];

$seoData = [
    'title'          => $pageTitle[$lang] ?? $pageTitle['az'],
    'meta_description' => $pageDesc[$lang] ?? $pageDesc['az'],
    'lang'           => $lang,
    'og_type'        => 'website',
    'schema_type'    => 'WebPage',
    'canonical_url'  => bb_lang_url(bb_get_route('articles', $lang) . '/', $lang),
    'alternate_urls' => bb_get_alternate_urls('articles', []),
];

// Kateqoriyaları çək
$categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name_az ASC")->fetchAll();

// Aktiv kateqoriya filtri
$activeCategorySlug = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$activeCategoryId = null;
if ($activeCategorySlug) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $activeCategorySlug) {
            $activeCategoryId = (int)$cat['id'];
            break;
        }
    }
}

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Məqalələri çək
$whereClause = "WHERE status = 'published'";
$params = [];
if ($activeCategoryId) {
    $whereClause .= " AND category_id = :cat_id";
    $params[':cat_id'] = $activeCategoryId;
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM articles {$whereClause}");
$countStmt->execute($params);
$totalArticles = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalArticles / $perPage));

$articlesStmt = $db->prepare(
    "SELECT * FROM articles {$whereClause} ORDER BY published_at DESC, created_at DESC LIMIT {$perPage} OFFSET {$offset}"
);
$articlesStmt->execute($params);
$articles = $articlesStmt->fetchAll();

$_strings = [
    'subtitle' => [
        'az' => 'Bibiheybət ziyarətgahı haqqında məqalələr və yazılar',
        'en' => 'Articles and writings about Bibiheybat Shrine',
        'ru' => 'Статьи и записи о святыне Бибиэйбат',
        'ar' => 'مقالات وكتابات عن مزار بيبي حيبات',
        'fa' => 'مقالات و نوشته‌ها درباره زیارتگاه بی‌بی حیبات',
    ],
    'all_categories' => [
        'az' => 'Hamısı',
        'en' => 'All',
        'ru' => 'Все',
        'ar' => 'الكل',
        'fa' => 'همه',
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
    'body_class' => 'bb-page-articles',
    'extra_css'  => ['/public/assets/css/articles.css'],
]);
?>

    <!-- Məqalə siyahısı hero -->
    <section class="bb-articles-hero" data-animate>
        <div class="bb-container bb-text-center">
            <div class="bb-articles-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>
            <h1 class="bb-articles-title"><?= bb_sanitize($pageTitle[$lang] ?? $pageTitle['az']) ?></h1>
            <div class="bb-separator bb-separator-center"></div>
            <p class="bb-articles-subtitle"><?= bb_sanitize($t('subtitle')) ?></p>
        </div>
    </section>

    <?php if (!empty($categories)): ?>
    <section class="bb-articles-filter">
        <div class="bb-container bb-text-center">
            <div class="bb-articles-categories">
                <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('articles', $lang) . '/', $lang)) ?>"
                   class="bb-articles-cat-btn<?= empty($activeCategorySlug) ? ' active' : '' ?>">
                    <?= bb_sanitize($t('all_categories')) ?>
                </a>
                <?php foreach ($categories as $cat): ?>
                    <?php $catName = bb_get_field($cat, 'name', $lang); ?>
                    <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('articles', $lang) . '/', $lang)) ?>?cat=<?= bb_sanitize($cat['slug']) ?>"
                       class="bb-articles-cat-btn<?= $activeCategorySlug === $cat['slug'] ? ' active' : '' ?>">
                        <?= bb_sanitize($catName) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($articles)): ?>
    <section class="bb-articles-grid-section" data-animate>
        <div class="bb-container">
            <div class="bb-articles-grid">
                <?php foreach ($articles as $a): ?>
                    <?php
                        $aTitle = bb_get_field($a, 'title', $lang);
                        $aSlug  = bb_get_field($a, 'slug', $lang);
                        $aImage = bb_get_featured_image($a, $lang);
                        $aUrl   = bb_lang_url(bb_get_route('article', $lang) . '/' . $aSlug, $lang);
                        $aDate  = bb_format_date($a['published_at'] ?? $a['created_at'], $lang);
                        $aExcerpt = bb_get_field($a, 'excerpt', $lang);
                        if (empty($aExcerpt)) {
                            $aExcerpt = bb_truncate(bb_get_field($a, 'content', $lang) ?? '', 140);
                        }
                    ?>
                <a href="<?= bb_sanitize($aUrl) ?>" class="bb-articles-card">
                    <div class="bb-articles-card-img">
                        <?php if ($aImage): ?>
                        <img src="/<?= bb_sanitize($aImage) ?>"
                             alt="<?= bb_sanitize($aTitle) ?>"
                             loading="lazy">
                        <?php else: ?>
                        <div class="bb-articles-card-placeholder"></div>
                        <?php endif; ?>
                    </div>
                    <div class="bb-articles-card-body">
                        <span class="bb-articles-card-date"><?= bb_sanitize($aDate) ?></span>
                        <h2 class="bb-articles-card-title"><?= bb_sanitize($aTitle) ?></h2>
                        <?php if ($aExcerpt): ?>
                        <p class="bb-articles-card-excerpt"><?= bb_sanitize($aExcerpt) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav class="bb-articles-pagination" aria-label="Səhifələmə">
                <?php
                    $baseUrl = bb_lang_url(bb_get_route('articles', $lang) . '/', $lang);
                    $queryExtra = $activeCategorySlug ? '&cat=' . urlencode($activeCategorySlug) : '';
                ?>
                <?php if ($page > 1): ?>
                <a href="<?= bb_sanitize($baseUrl) ?>?page=<?= $page - 1 ?><?= $queryExtra ?>" class="bb-articles-page-btn">&laquo;</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                    <span class="bb-articles-page-btn active"><?= $i ?></span>
                    <?php else: ?>
                    <a href="<?= bb_sanitize($baseUrl) ?>?page=<?= $i ?><?= $queryExtra ?>" class="bb-articles-page-btn"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="<?= bb_sanitize($baseUrl) ?>?page=<?= $page + 1 ?><?= $queryExtra ?>" class="bb-articles-page-btn">&raquo;</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
        </div>
    </section>
    <?php else: ?>
    <section class="bb-articles-empty">
        <div class="bb-container bb-text-center">
            <p class="bb-text-muted"><?= bb_sanitize($t('empty')) ?></p>
        </div>
    </section>
    <?php endif; ?>

<?php
bb_frontend_footer(['extra_js' => ['/public/assets/js/home.js']]);
