<?php
/**
 * Bibiheybet.com - PDO Database Bağlantısı
 * 
 * Singleton pattern ilə PDO instance idarə edir.
 * Bütün DB əməliyyatları bu fayldan keçir.
 */

/**
 * PDO database bağlantısını qaytarır (singleton).
 * 
 * @return PDO
 * @throws PDOException Bağlantı uğursuz olduqda
 */
function bb_get_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        // config.php yüklənib yoxla
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/../config.php';
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
        ];

        try {
            $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                throw $e;
            }
            // Production-da detallı xəta göstərmə
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database bağlantısı uğursuz oldu. Zəhmət olmasa sonra yenidən cəhd edin.');
        }
    }

    return $pdo;
}
