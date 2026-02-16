<?php
/**
 * Müvəqqəti miqrasiya skripti - AR/FA dil dəstəyi
 * İcra edildikdən sonra SİLİN!
 */

require_once __DIR__ . '/config.php';

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
try {
    $db = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('DB qoşulma xətası: ' . $e->getMessage());
}

header('Content-Type: text/plain; charset=utf-8');

$queries = [
    // articles
    "ALTER TABLE `articles`
        ADD COLUMN `slug_ar` VARCHAR(255) NULL AFTER `slug_ru`,
        ADD COLUMN `slug_fa` VARCHAR(255) NULL AFTER `slug_ar`,
        ADD COLUMN `title_ar` VARCHAR(500) NULL AFTER `title_ru`,
        ADD COLUMN `title_fa` VARCHAR(500) NULL AFTER `title_ar`,
        ADD COLUMN `content_ar` LONGTEXT NULL AFTER `content_ru`,
        ADD COLUMN `content_fa` LONGTEXT NULL AFTER `content_ar`,
        ADD COLUMN `excerpt_ar` TEXT NULL AFTER `excerpt_ru`,
        ADD COLUMN `excerpt_fa` TEXT NULL AFTER `excerpt_ar`,
        ADD COLUMN `featured_image_ar` VARCHAR(500) NULL AFTER `featured_image_ru`,
        ADD COLUMN `featured_image_fa` VARCHAR(500) NULL AFTER `featured_image_ar`,
        ADD COLUMN `meta_title_ar` VARCHAR(255) NULL AFTER `meta_title_ru`,
        ADD COLUMN `meta_title_fa` VARCHAR(255) NULL AFTER `meta_title_ar`,
        ADD COLUMN `meta_desc_ar` TEXT NULL AFTER `meta_desc_ru`,
        ADD COLUMN `meta_desc_fa` TEXT NULL AFTER `meta_desc_ar`,
        ADD COLUMN `og_image_ar` VARCHAR(500) NULL AFTER `og_image_ru`,
        ADD COLUMN `og_image_fa` VARCHAR(500) NULL AFTER `og_image_ar`",

    "ALTER TABLE `articles`
        ADD INDEX `idx_articles_slug_ar` (`slug_ar`),
        ADD INDEX `idx_articles_slug_fa` (`slug_fa`)",

    // pilgrimages
    "ALTER TABLE `pilgrimages`
        ADD COLUMN `slug_ar` VARCHAR(255) NULL AFTER `slug_ru`,
        ADD COLUMN `slug_fa` VARCHAR(255) NULL AFTER `slug_ar`,
        ADD COLUMN `name_ar` VARCHAR(500) NULL AFTER `name_ru`,
        ADD COLUMN `name_fa` VARCHAR(500) NULL AFTER `name_ar`,
        ADD COLUMN `content_ar` LONGTEXT NULL AFTER `content_ru`,
        ADD COLUMN `content_fa` LONGTEXT NULL AFTER `content_ar`,
        ADD COLUMN `featured_image_ar` VARCHAR(500) NULL AFTER `featured_image_ru`,
        ADD COLUMN `featured_image_fa` VARCHAR(500) NULL AFTER `featured_image_ar`,
        ADD COLUMN `meta_title_ar` VARCHAR(255) NULL AFTER `meta_title_ru`,
        ADD COLUMN `meta_title_fa` VARCHAR(255) NULL AFTER `meta_title_ar`,
        ADD COLUMN `meta_desc_ar` TEXT NULL AFTER `meta_desc_ru`,
        ADD COLUMN `meta_desc_fa` TEXT NULL AFTER `meta_desc_ar`,
        ADD COLUMN `og_image_ar` VARCHAR(500) NULL AFTER `og_image_ru`,
        ADD COLUMN `og_image_fa` VARCHAR(500) NULL AFTER `og_image_ar`",

    "ALTER TABLE `pilgrimages`
        ADD INDEX `idx_pilgrimages_slug_ar` (`slug_ar`),
        ADD INDEX `idx_pilgrimages_slug_fa` (`slug_fa`)",

    // pilgrimage_gallery
    "ALTER TABLE `pilgrimage_gallery`
        ADD COLUMN `caption_ar` VARCHAR(500) NULL AFTER `caption_ru`,
        ADD COLUMN `caption_fa` VARCHAR(500) NULL AFTER `caption_ar`",

    // categories
    "ALTER TABLE `categories`
        ADD COLUMN `name_ar` VARCHAR(255) NULL AFTER `name_ru`,
        ADD COLUMN `name_fa` VARCHAR(255) NULL AFTER `name_ar`",
];

$success = 0;
$failed = 0;

foreach ($queries as $i => $sql) {
    try {
        $db->exec($sql);
        echo "OK  [$i]: " . substr($sql, 0, 60) . "...\n";
        $success++;
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column name') || str_contains($e->getMessage(), 'Duplicate key name')) {
            echo "SKIP[$i]: Artıq mövcuddur - " . substr($sql, 0, 60) . "...\n";
        } else {
            echo "ERR [$i]: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
}

echo "\n=============================\n";
echo "Uğurlu: $success, Xəta: $failed\n";
echo "Miqrasiya tamamlandı!\n";
echo "\n!!! BU FAYLYI SİLİN: migrate-5langs-run.php !!!\n";
