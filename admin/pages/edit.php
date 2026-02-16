<?php
/**
 * Bibiheybet.com - Səhifə Məzmunu Redaktə
 * 
 * Seçilmiş səhifənin bütün bölmələrini bütün dillər üçün redaktə edir.
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

// Səhifə konfiqurasiyası
$pages = [
    'home' => [
        'label' => 'Ana Səhifə',
        'sections' => [
            'hero_subtitle'    => ['label' => 'Hero alt yazı', 'type' => 'text'],
            'hazrat_text'      => ['label' => 'Həzrət haqqında mətn', 'type' => 'textarea'],
            'mosque_text_1'    => ['label' => 'Məscid haqqında mətn (1-ci hissə)', 'type' => 'textarea'],
            'mosque_strong'    => ['label' => 'Məscid haqqında güclü mətn', 'type' => 'text'],
            'mosque_text_2'    => ['label' => 'Məscid haqqında mətn (2-ci hissə)', 'type' => 'textarea'],
            'pilgrimages_title' => ['label' => 'Ziyarətgahlar bölmə başlığı', 'type' => 'text'],
            'articles_title'   => ['label' => 'Məqalələr bölmə başlığı', 'type' => 'text'],
            'read_more'        => ['label' => '"Daha ətraflı" düyməsi', 'type' => 'text'],
            'show_more'        => ['label' => '"Daha çox" düyməsi', 'type' => 'text'],
        ],
    ],
    'about-hazrat' => [
        'label' => 'Həkimə xanım haqqında',
        'sections' => [
            'title'       => ['label' => 'Səhifə başlığı', 'type' => 'text'],
            'description' => ['label' => 'Səhifə təsviri', 'type' => 'textarea'],
            'content'     => ['label' => 'Əsas məzmun', 'type' => 'editor'],
        ],
    ],
    'about-mosque' => [
        'label' => 'Məscid haqqında',
        'sections' => [
            'title'       => ['label' => 'Səhifə başlığı', 'type' => 'text'],
            'description' => ['label' => 'Səhifə təsviri', 'type' => 'textarea'],
            'content'     => ['label' => 'Əsas məzmun', 'type' => 'editor'],
        ],
    ],
    'prayers' => [
        'label' => 'Dua və ziyarətnamə',
        'sections' => [
            'title'       => ['label' => 'Səhifə başlığı', 'type' => 'text'],
            'description' => ['label' => 'Səhifə təsviri', 'type' => 'textarea'],
            'content'     => ['label' => 'Əsas məzmun', 'type' => 'editor'],
        ],
    ],
];

$pageKey = $_GET['page'] ?? '';
if (!isset($pages[$pageKey])) {
    bb_flash('error', 'Belə səhifə tapılmadı.');
    bb_redirect('/admin/pages/');
}

$pageConfig = $pages[$pageKey];
$langs = ['az', 'en', 'ru', 'ar', 'fa'];

// POST: Yadda saxla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    bb_verify_csrf();

    foreach ($pageConfig['sections'] as $sectionKey => $sectionInfo) {
        $values = [':page_key' => $pageKey, ':section_key' => $sectionKey];
        $setClauses = [];
        foreach ($langs as $l) {
            $fieldName = "content_{$l}";
            $paramName = ":content_{$l}";
            $values[$paramName] = $_POST["{$sectionKey}_{$l}"] ?? '';
            $setClauses[] = "`{$fieldName}` = {$paramName}";
        }

        $setStr = implode(', ', $setClauses);
        $updateStr = implode(', ', array_map(fn($c) => str_replace('`', '', $c), $setClauses));

        $sql = "INSERT INTO page_contents (page_key, section_key, " . implode(', ', array_map(fn($l) => "content_{$l}", $langs)) . ")
                VALUES (:page_key, :section_key, " . implode(', ', array_map(fn($l) => ":content_{$l}", $langs)) . ")
                ON DUPLICATE KEY UPDATE {$updateStr}";

        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }

    bb_flash('success', 'Səhifə məzmunu uğurla yeniləndi!');
    bb_redirect('/admin/pages/edit.php?page=' . urlencode($pageKey));
}

// Mövcud datanı yüklə
$existing = [];
$rows = $db->prepare("SELECT * FROM page_contents WHERE page_key = :page_key");
$rows->execute([':page_key' => $pageKey]);
foreach ($rows->fetchAll() as $row) {
    $existing[$row['section_key']] = $row;
}

bb_admin_header($pageConfig['label'] . ' - Redaktə', [
    'extra_js' => ['/admin/assets/js/tinymce/tinymce.min.js'],
]);
?>

<div class="bb-page-header">
    <h2><?= bb_sanitize($pageConfig['label']) ?></h2>
    <a href="/admin/pages/" class="bb-btn bb-btn-outline bb-btn-sm">&larr; Geri</a>
</div>

<form method="POST" class="bb-page-edit-form">
    <?= bb_generate_csrf() ?>

    <?php foreach ($pageConfig['sections'] as $sectionKey => $sectionInfo): ?>
    <div class="bb-card bb-section-card">
        <h3 class="bb-card-title"><?= bb_sanitize($sectionInfo['label']) ?></h3>

        <div class="bb-tabs">
            <?php foreach ($langs as $i => $l): ?>
            <button type="button" class="bb-tab<?= $i === 0 ? ' active' : '' ?>" data-tab="<?= $sectionKey ?>_<?= $l ?>"><?= strtoupper($l) ?></button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($langs as $i => $l): ?>
            <?php
                $fieldId = "{$sectionKey}_{$l}";
                $fieldValue = $existing[$sectionKey]["content_{$l}"] ?? '';
            ?>
        <div class="bb-tab-content<?= $i === 0 ? ' active' : '' ?>" data-tab-content="<?= $fieldId ?>">
            <?php if ($sectionInfo['type'] === 'text'): ?>
                <div class="bb-form-group">
                    <input type="text" id="<?= $fieldId ?>" name="<?= $fieldId ?>"
                        value="<?= bb_sanitize($fieldValue) ?>"
                        placeholder="<?= strtoupper($l) ?> dilində daxil edin...">
                </div>
            <?php elseif ($sectionInfo['type'] === 'textarea'): ?>
                <div class="bb-form-group">
                    <textarea id="<?= $fieldId ?>" name="<?= $fieldId ?>"
                        rows="4" placeholder="<?= strtoupper($l) ?> dilində daxil edin..."><?= bb_sanitize($fieldValue) ?></textarea>
                </div>
            <?php elseif ($sectionInfo['type'] === 'editor'): ?>
                <div class="bb-form-group">
                    <textarea id="<?= $fieldId ?>" name="<?= $fieldId ?>"
                        class="bb-tinymce-editor" rows="12"><?= bb_sanitize($fieldValue) ?></textarea>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <div class="bb-form-actions bb-form-actions-sticky">
        <button type="submit" class="bb-btn bb-btn-primary bb-btn-lg">Yadda saxla</button>
    </div>
</form>

<style>
.bb-section-card {
    margin-bottom: 1.5rem;
}

.bb-section-card .bb-card-title {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.bb-form-actions-sticky {
    position: sticky;
    bottom: 0;
    background: var(--admin-content-bg, #f9fafb);
    padding: 1rem 0;
    margin-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    text-align: right;
    z-index: 10;
}

.bb-btn-lg {
    padding: 0.75rem 2.5rem;
    font-size: 1rem;
}
</style>


<?php
bb_admin_footer();
