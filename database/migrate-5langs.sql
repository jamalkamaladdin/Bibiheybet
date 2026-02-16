-- ============================================
-- Bibiheybet.com - 5 Dil Miqrasiyası
-- ============================================
-- Bu miqrasiya AR (Ərəbcə) və FA (Farsca) dil
-- dəstəyini articles, pilgrimages, pilgrimage_gallery
-- və categories cədvəllərinə əlavə edir.
-- ============================================
-- İstifadə: Bu SQL-i MySQL/MariaDB-də icra edin.
-- ============================================

SET NAMES utf8mb4;

-- ============================================
-- 1. articles - AR/FA sütunlar əlavə et
-- ============================================
ALTER TABLE `articles`
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
    ADD COLUMN `og_image_fa` VARCHAR(500) NULL AFTER `og_image_ar`;

ALTER TABLE `articles`
    ADD INDEX `idx_articles_slug_ar` (`slug_ar`),
    ADD INDEX `idx_articles_slug_fa` (`slug_fa`);

-- ============================================
-- 2. pilgrimages - AR/FA sütunlar əlavə et
-- ============================================
ALTER TABLE `pilgrimages`
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
    ADD COLUMN `og_image_fa` VARCHAR(500) NULL AFTER `og_image_ar`;

ALTER TABLE `pilgrimages`
    ADD INDEX `idx_pilgrimages_slug_ar` (`slug_ar`),
    ADD INDEX `idx_pilgrimages_slug_fa` (`slug_fa`);

-- ============================================
-- 3. pilgrimage_gallery - AR/FA başlıq sütunları
-- ============================================
ALTER TABLE `pilgrimage_gallery`
    ADD COLUMN `caption_ar` VARCHAR(500) NULL AFTER `caption_ru`,
    ADD COLUMN `caption_fa` VARCHAR(500) NULL AFTER `caption_ar`;

-- ============================================
-- 4. categories - AR/FA ad sütunları
-- ============================================
ALTER TABLE `categories`
    ADD COLUMN `name_ar` VARCHAR(255) NULL AFTER `name_ru`,
    ADD COLUMN `name_fa` VARCHAR(255) NULL AFTER `name_ar`;
