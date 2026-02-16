<?php
/**
 * Bibiheybet.com - Səhifə Məzmunları İdarəetmə
 * 
 * Redaktə edilə bilən səhifə siyahısı.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();

// page_contents cədvəli mövcuddurmu yoxla, yoxdursa yarat
try {
    $db->query("SELECT 1 FROM page_contents LIMIT 1");
} catch (PDOException $e) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS `page_contents` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `page_key` VARCHAR(100) NOT NULL,
            `section_key` VARCHAR(100) NOT NULL,
            `content_az` LONGTEXT NULL,
            `content_en` LONGTEXT NULL,
            `content_ru` LONGTEXT NULL,
            `content_ar` LONGTEXT NULL,
            `content_fa` LONGTEXT NULL,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_page_section` (`page_key`, `section_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

$pages = [
    'home' => [
        'label' => 'Ana Səhifə',
        'icon'  => '&#x1F3E0;',
        'sections' => [
            'hero_subtitle'    => 'Hero alt yazı',
            'hazrat_text'      => 'Həzrət haqqında mətn',
            'mosque_text_1'    => 'Məscid haqqında mətn (1-ci hissə)',
            'mosque_strong'    => 'Məscid haqqında güclü mətn',
            'mosque_text_2'    => 'Məscid haqqında mətn (2-ci hissə)',
            'pilgrimages_title' => 'Ziyarətgahlar bölmə başlığı',
            'articles_title'   => 'Məqalələr bölmə başlığı',
            'read_more'        => '"Daha ətraflı" düyməsi',
            'show_more'        => '"Daha çox" düyməsi',
        ],
    ],
    'about-hazrat' => [
        'label' => 'Həkimə xanım haqqında',
        'icon'  => '&#x2B50;',
        'sections' => [
            'title'       => 'Səhifə başlığı',
            'description' => 'Səhifə təsviri',
            'content'     => 'Əsas məzmun',
        ],
    ],
    'about-mosque' => [
        'label' => 'Məscid haqqında',
        'icon'  => '&#x1F54C;',
        'sections' => [
            'title'       => 'Səhifə başlığı',
            'description' => 'Səhifə təsviri',
            'content'     => 'Əsas məzmun',
        ],
    ],
    'prayers' => [
        'label' => 'Dua və ziyarətnamə',
        'icon'  => '&#x1F64F;',
        'sections' => [
            'title'       => 'Səhifə başlığı',
            'description' => 'Səhifə təsviri',
            'content'     => 'Əsas məzmun',
        ],
    ],
];

// Hər səhifə üçün son update tarixini çək
$lastUpdates = [];
$rows = $db->query("SELECT page_key, MAX(updated_at) as last_update FROM page_contents GROUP BY page_key")->fetchAll();
foreach ($rows as $r) {
    $lastUpdates[$r['page_key']] = $r['last_update'];
}

bb_admin_header('Səhifə Məzmunları');
?>

<div class="bb-page-header">
    <h2>Səhifə Məzmunları</h2>
    <p class="bb-text-muted">Saytdakı statik səhifələrin mətnlərini bütün dillər üçün redaktə edin.</p>
</div>

<div class="bb-pages-grid">
    <?php foreach ($pages as $pageKey => $pageInfo): ?>
    <a href="/admin/pages/edit.php?page=<?= bb_sanitize($pageKey) ?>" class="bb-page-card">
        <div class="bb-page-card-icon"><?= $pageInfo['icon'] ?></div>
        <div class="bb-page-card-info">
            <h3 class="bb-page-card-title"><?= bb_sanitize($pageInfo['label']) ?></h3>
            <p class="bb-page-card-meta">
                <?= count($pageInfo['sections']) ?> bölmə
                <?php if (!empty($lastUpdates[$pageKey])): ?>
                    &middot; Son: <?= bb_format_date($lastUpdates[$pageKey]) ?>
                <?php endif; ?>
            </p>
        </div>
        <span class="bb-page-card-arrow">&rarr;</span>
    </a>
    <?php endforeach; ?>
</div>

<style>
.bb-pages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.bb-page-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.2rem 1.5rem;
    background: var(--admin-card-bg, #fff);
    border: 1px solid var(--admin-border, #e5e7eb);
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.bb-page-card:hover {
    border-color: #c9a84c;
    box-shadow: 0 2px 8px rgba(201, 168, 76, 0.15);
    transform: translateY(-2px);
}

.bb-page-card-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.bb-page-card-info {
    flex: 1;
}

.bb-page-card-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem;
    color: #1a1a2e;
}

.bb-page-card-meta {
    font-size: 0.8rem;
    color: #888;
    margin: 0;
}

.bb-page-card-arrow {
    font-size: 1.2rem;
    color: #c9a84c;
    flex-shrink: 0;
}
</style>

<?php
bb_admin_footer();
