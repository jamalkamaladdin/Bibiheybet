<?php
/**
 * Bibiheybet.com - Konfiqurasiya Nümunəsi
 * 
 * Bu faylı `config.php` olaraq kopyalayın və real dəyərləri yazın.
 * config.php faylı .gitignore-dadır və repo-ya push olunmur.
 * 
 * Alternativ: .env faylından dəyərləri oxuya bilərsiniz.
 */

// ============================================
// Application
// ============================================
define('APP_ENV', 'production');           // 'development' və ya 'production'
define('APP_DEBUG', false);                // true = xəta detalları göstərilir
define('SITE_URL', 'https://bibiheybet.com');
define('SITE_NAME', 'Bibiheybət Ziyarətgahı');

// ============================================
// Database
// ============================================
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'your_db_name');
define('DB_USERNAME', 'your_db_user');
define('DB_PASSWORD', 'your_db_password');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// Paths
// ============================================
define('BASE_PATH', __DIR__);
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL', SITE_URL . '/uploads');
define('ADMIN_PATH', '/admin');

// ============================================
// Upload Limits
// ============================================
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ============================================
// Language
// ============================================
define('DEFAULT_LANG', 'az');
define('AVAILABLE_LANGS', ['az', 'en', 'ru', 'ar', 'fa']);

// ============================================
// Session & Security
// ============================================
define('SESSION_LIFETIME', 86400);          // 24 saat (saniyə)
define('CSRF_TOKEN_LIFETIME', 86400);       // 24 saat
define('SESSION_NAME', 'bb_session');

// ============================================
// Pagination
// ============================================
define('ARTICLES_PER_PAGE', 9);
define('ADMIN_ITEMS_PER_PAGE', 20);
