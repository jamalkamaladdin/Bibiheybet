-- ============================================
-- Bibiheybet.com - Database Sxemi
-- ============================================
-- Bu SQL faylı bütün cədvəlləri yaradır.
-- Admin seed data üçün database/seed.php istifadə edin.
-- ============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- ============================================
-- 1. admins - Admin istifadəçilər
-- ============================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. categories - Kateqoriyalar
-- ============================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `name_az` VARCHAR(255) NOT NULL,
    `name_en` VARCHAR(255) NULL,
    `name_ru` VARCHAR(255) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. articles - Məqalələr
-- ============================================
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NULL,
    
    -- Slugs
    `slug_az` VARCHAR(255) NOT NULL,
    `slug_en` VARCHAR(255) NULL,
    `slug_ru` VARCHAR(255) NULL,
    
    -- Titles
    `title_az` VARCHAR(500) NOT NULL,
    `title_en` VARCHAR(500) NULL,
    `title_ru` VARCHAR(500) NULL,
    
    -- Content (HTML)
    `content_az` LONGTEXT NOT NULL,
    `content_en` LONGTEXT NULL,
    `content_ru` LONGTEXT NULL,
    
    -- Excerpts
    `excerpt_az` TEXT NULL,
    `excerpt_en` TEXT NULL,
    `excerpt_ru` TEXT NULL,
    
    -- Featured images
    `featured_image` VARCHAR(500) NULL COMMENT 'Əsas foto (bütün dillər üçün default)',
    `featured_image_en` VARCHAR(500) NULL COMMENT 'EN üçün fərqli foto',
    `featured_image_ru` VARCHAR(500) NULL COMMENT 'RU üçün fərqli foto',
    
    -- SEO: Meta titles
    `meta_title_az` VARCHAR(255) NULL,
    `meta_title_en` VARCHAR(255) NULL,
    `meta_title_ru` VARCHAR(255) NULL,
    
    -- SEO: Meta descriptions
    `meta_desc_az` TEXT NULL,
    `meta_desc_en` TEXT NULL,
    `meta_desc_ru` TEXT NULL,
    
    -- SEO: OG images
    `og_image_az` VARCHAR(500) NULL,
    `og_image_en` VARCHAR(500) NULL,
    `og_image_ru` VARCHAR(500) NULL,
    
    -- Status & dates
    `status` ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    `published_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    CONSTRAINT `fk_articles_category` FOREIGN KEY (`category_id`) 
        REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    
    -- Indexes
    INDEX `idx_articles_status` (`status`),
    INDEX `idx_articles_published_at` (`published_at`),
    INDEX `idx_articles_slug_az` (`slug_az`),
    INDEX `idx_articles_slug_en` (`slug_en`),
    INDEX `idx_articles_slug_ru` (`slug_ru`),
    INDEX `idx_articles_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. pilgrimages - Ziyarətgahlar
-- ============================================
CREATE TABLE IF NOT EXISTS `pilgrimages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Slugs
    `slug_az` VARCHAR(255) NOT NULL,
    `slug_en` VARCHAR(255) NULL,
    `slug_ru` VARCHAR(255) NULL,
    
    -- Names
    `name_az` VARCHAR(500) NOT NULL,
    `name_en` VARCHAR(500) NULL,
    `name_ru` VARCHAR(500) NULL,
    
    -- Content (HTML)
    `content_az` LONGTEXT NOT NULL,
    `content_en` LONGTEXT NULL,
    `content_ru` LONGTEXT NULL,
    
    -- Featured images
    `featured_image` VARCHAR(500) NULL COMMENT 'Əsas foto',
    `featured_image_en` VARCHAR(500) NULL,
    `featured_image_ru` VARCHAR(500) NULL,
    
    -- SEO: Meta titles
    `meta_title_az` VARCHAR(255) NULL,
    `meta_title_en` VARCHAR(255) NULL,
    `meta_title_ru` VARCHAR(255) NULL,
    
    -- SEO: Meta descriptions
    `meta_desc_az` TEXT NULL,
    `meta_desc_en` TEXT NULL,
    `meta_desc_ru` TEXT NULL,
    
    -- SEO: OG images
    `og_image_az` VARCHAR(500) NULL,
    `og_image_en` VARCHAR(500) NULL,
    `og_image_ru` VARCHAR(500) NULL,
    
    -- Status & sorting
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX `idx_pilgrimages_status` (`status`),
    INDEX `idx_pilgrimages_slug_az` (`slug_az`),
    INDEX `idx_pilgrimages_slug_en` (`slug_en`),
    INDEX `idx_pilgrimages_slug_ru` (`slug_ru`),
    INDEX `idx_pilgrimages_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. pilgrimage_gallery - Ziyarətgah Qalereyası
-- ============================================
CREATE TABLE IF NOT EXISTS `pilgrimage_gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pilgrimage_id` INT NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `caption_az` VARCHAR(500) NULL,
    `caption_en` VARCHAR(500) NULL,
    `caption_ru` VARCHAR(500) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    
    -- Foreign key
    CONSTRAINT `fk_gallery_pilgrimage` FOREIGN KEY (`pilgrimage_id`) 
        REFERENCES `pilgrimages`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes
    INDEX `idx_gallery_pilgrimage` (`pilgrimage_id`),
    INDEX `idx_gallery_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. media - Media Faylları
-- ============================================
CREATE TABLE IF NOT EXISTS `media` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL COMMENT 'Orijinal fayl adı',
    `filepath` VARCHAR(500) NOT NULL COMMENT 'Server-dəki yol',
    `filetype` VARCHAR(50) NOT NULL COMMENT 'MIME type',
    `filesize` INT NOT NULL COMMENT 'Bayt ölçüsü',
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
