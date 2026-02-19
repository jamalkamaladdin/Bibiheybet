-- ============================================
-- Bibiheybet.com - Xanım haqqında Migration
-- Mövcud DB-yə yeni cədvəllər əlavə edir.
-- ============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- xanim_haqqinda cədvəli
CREATE TABLE IF NOT EXISTS `xanim_haqqinda` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug_az` VARCHAR(255) NOT NULL,
    `slug_en` VARCHAR(255) NULL,
    `slug_ru` VARCHAR(255) NULL,
    `slug_ar` VARCHAR(255) NULL,
    `slug_fa` VARCHAR(255) NULL,
    `name_az` VARCHAR(500) NOT NULL,
    `name_en` VARCHAR(500) NULL,
    `name_ru` VARCHAR(500) NULL,
    `name_ar` VARCHAR(500) NULL,
    `name_fa` VARCHAR(500) NULL,
    `content_az` LONGTEXT NOT NULL,
    `content_en` LONGTEXT NULL,
    `content_ru` LONGTEXT NULL,
    `content_ar` LONGTEXT NULL,
    `content_fa` LONGTEXT NULL,
    `featured_image` VARCHAR(500) NULL,
    `featured_image_en` VARCHAR(500) NULL,
    `featured_image_ru` VARCHAR(500) NULL,
    `featured_image_ar` VARCHAR(500) NULL,
    `featured_image_fa` VARCHAR(500) NULL,
    `meta_title_az` VARCHAR(255) NULL,
    `meta_title_en` VARCHAR(255) NULL,
    `meta_title_ru` VARCHAR(255) NULL,
    `meta_title_ar` VARCHAR(255) NULL,
    `meta_title_fa` VARCHAR(255) NULL,
    `meta_desc_az` TEXT NULL,
    `meta_desc_en` TEXT NULL,
    `meta_desc_ru` TEXT NULL,
    `meta_desc_ar` TEXT NULL,
    `meta_desc_fa` TEXT NULL,
    `og_image_az` VARCHAR(500) NULL,
    `og_image_en` VARCHAR(500) NULL,
    `og_image_ru` VARCHAR(500) NULL,
    `og_image_ar` VARCHAR(500) NULL,
    `og_image_fa` VARCHAR(500) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_xanim_status` (`status`),
    INDEX `idx_xanim_slug_az` (`slug_az`),
    INDEX `idx_xanim_slug_en` (`slug_en`),
    INDEX `idx_xanim_slug_ru` (`slug_ru`),
    INDEX `idx_xanim_slug_ar` (`slug_ar`),
    INDEX `idx_xanim_slug_fa` (`slug_fa`),
    INDEX `idx_xanim_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- xanim_gallery cədvəli
CREATE TABLE IF NOT EXISTS `xanim_gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `xanim_id` INT NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `caption_az` VARCHAR(500) NULL,
    `caption_en` VARCHAR(500) NULL,
    `caption_ru` VARCHAR(500) NULL,
    `caption_ar` VARCHAR(500) NULL,
    `caption_fa` VARCHAR(500) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    CONSTRAINT `fk_xanim_gallery` FOREIGN KEY (`xanim_id`)
        REFERENCES `xanim_haqqinda`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_xanim_gallery_xanim` (`xanim_id`),
    INDEX `idx_xanim_gallery_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
