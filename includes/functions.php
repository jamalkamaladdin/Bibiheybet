<?php
/**
 * Bibiheybet.com - Ümumi Helper Funksiyalar
 * 
 * Slug yaratma, tarix formatı, şəkil yükləmə, XSS qoruması,
 * flash mesajlar və digər ümumi utility funksiyalar.
 */

/**
 * Azərbaycan və Kiril simvollarını transliterasiya xəritəsi.
 */
function bb_get_transliteration_map(): array
{
    return [
        // Azərbaycan
        'ə' => 'e', 'Ə' => 'E',
        'ı' => 'i', 'I' => 'I',
        'ö' => 'o', 'Ö' => 'O',
        'ü' => 'u', 'Ü' => 'U',
        'ş' => 's', 'Ş' => 'S',
        'ç' => 'c', 'Ç' => 'C',
        'ğ' => 'g', 'Ğ' => 'G',
        // Kiril (Rus)
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
    ];
}

/**
 * Mətndən URL-friendly slug yaradır.
 * 
 * Azərbaycan simvolları (ə, ı, ö, ü, ş, ç, ğ) və Kiril dəstəyi var.
 * 
 * @param string $text Mənbə mətn
 * @param string $lang Dil kodu (az, en, ru)
 * @return string Slug (kiçik hərfli, tire ilə ayrılmış)
 */
function bb_generate_slug(string $text, string $lang = 'az'): string
{
    // Transliterasiya
    $map = bb_get_transliteration_map();
    $text = strtr($text, $map);

    // Kiçik hərflərə çevir
    $text = mb_strtolower($text, 'UTF-8');

    // Yalnız hərflər, rəqəmlər və tireler saxla
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

    // Boşluqları və ardıcıl tireləri tək tireyə çevir
    $text = preg_replace('/[\s-]+/', '-', $text);

    // Baş və sondakı tireləri sil
    $text = trim($text, '-');

    return $text;
}

/**
 * Tarixi dilə uyğun formata çevirir.
 * 
 * @param string $datetime MySQL datetime string
 * @param string $lang Dil kodu
 * @return string Formatlanmış tarix
 */
function bb_format_date(string $datetime, string $lang = 'az'): string
{
    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return $datetime;
    }

    $day = date('j', $timestamp);
    $month = (int)date('n', $timestamp);
    $year = date('Y', $timestamp);

    $months = [
        'az' => [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'İyun', 7 => 'İyul', 8 => 'Avqust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ],
        'en' => [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ],
        'ru' => [
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря',
        ],
        'ar' => [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ],
        'fa' => [
            1 => 'ژانویه', 2 => 'فوریه', 3 => 'مارس', 4 => 'آوریل',
            5 => 'مه', 6 => 'ژوئن', 7 => 'ژوئیه', 8 => 'اوت',
            9 => 'سپتامبر', 10 => 'اکتبر', 11 => 'نوامبر', 12 => 'دسامبر',
        ],
    ];

    // Dil yoxla, default az
    if (!isset($months[$lang])) {
        $lang = 'az';
    }

    $monthName = $months[$lang][$month];

    switch ($lang) {
        case 'en':
            return "{$monthName} {$day}, {$year}";
        case 'ru':
            return "{$day} {$monthName} {$year}";
        case 'az':
        default:
            return "{$day} {$monthName} {$year}";
    }
}

/**
 * Mətni müəyyən uzunluqda kəsir.
 * HTML tag-ları əvvəlcə təmizlənir.
 * 
 * @param string $text Mənbə mətn
 * @param int $length Maksimum uzunluq (default: 160)
 * @param string $suffix Sonluq (default: '...')
 * @return string Kəsilmiş mətn
 */
function bb_truncate(string $text, int $length = 160, string $suffix = '...'): string
{
    // HTML tag-ları təmizlə
    $text = strip_tags($text);
    // Ardıcıl boşluqları tək boşluğa çevir
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);

    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }

    // Söz ortasından kəsmə, sonuncu boşluğa qədər kəs
    $truncated = mb_substr($text, 0, $length, 'UTF-8');
    $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');

    if ($lastSpace !== false && $lastSpace > $length * 0.7) {
        $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
    }

    return $truncated . $suffix;
}

/**
 * Şəkil faylı yükləyir.
 * 
 * @param array $file $_FILES array-dən bir element
 * @param string $directory Upload qovluğu adı (articles, pilgrimages, media)
 * @return array ['success' => bool, 'filepath' => string|null, 'filename' => string|null, 'error' => string|null]
 */
function bb_upload_image(array $file, string $directory = 'media'): array
{
    // config.php yüklənib yoxla
    if (!defined('UPLOADS_PATH')) {
        require_once __DIR__ . '/../config.php';
    }

    // Fayl yüklənib yoxla
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'filepath' => null, 'filename' => null, 'error' => 'Fayl yüklənməyib.'];
    }

    // Xəta yoxla
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'filepath' => null, 'filename' => null, 'error' => 'Fayl yükləmə xətası: ' . $file['error']];
    }

    // Ölçü yoxla
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $maxMB = MAX_UPLOAD_SIZE / 1024 / 1024;
        return ['success' => false, 'filepath' => null, 'filename' => null, 'error' => "Fayl ölçüsü {$maxMB}MB-dan böyükdür."];
    }

    // MIME tip yoxla
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'filepath' => null, 'filename' => null, 'error' => 'İcazə verilməyən fayl tipi: ' . $mimeType];
    }

    // Genişlənmə yoxla
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
        return ['success' => false, 'filepath' => null, 'filename' => null, 'error' => 'İcazə verilməyən fayl uzantısı: ' . $extension];
    }

    // Unikal fayl adı yarat
    $uniqueName = uniqid('bb_', true) . '.' . $extension;

    // Hədəf qovluq
    $targetDir = UPLOADS_PATH . '/' . $directory;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetPath = $targetDir . '/' . $uniqueName;

    // Faylı köçür
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'filepath' => null, 'filename' => null, 'error' => 'Fayl köçürülə bilmədi.'];
    }

    // Relative path (DB-yə yazılacaq)
    $relativePath = 'uploads/' . $directory . '/' . $uniqueName;

    return [
        'success'  => true,
        'filepath' => $relativePath,
        'filename' => $file['name'],
        'filetype' => $mimeType,
        'filesize' => $file['size'],
        'error'    => null,
    ];
}

/**
 * Şəkil faylını diskdən silir.
 * 
 * @param string $filepath Relative path (uploads/articles/xxx.jpg)
 * @return bool Silinib-silinmədiyini qaytarır
 */
function bb_delete_image(string $filepath): bool
{
    if (!defined('BASE_PATH')) {
        require_once __DIR__ . '/../config.php';
    }

    $fullPath = BASE_PATH . '/' . $filepath;

    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }

    return false;
}

/**
 * Şəkli resize edir (aspect ratio qorunur).
 * GD kitabxanası tələb olunur.
 * 
 * @param string $source Mənbə fayl yolu (tam yol)
 * @param string $dest Hədəf fayl yolu (tam yol)
 * @param int $maxWidth Maksimum eni
 * @param int $maxHeight Maksimum hündürlüyü
 * @return bool Uğurlu olub-olmadığı
 */
function bb_resize_image(string $source, string $dest, int $maxWidth, int $maxHeight): bool
{
    if (!file_exists($source)) {
        return false;
    }

    $imageInfo = getimagesize($source);
    if ($imageInfo === false) {
        return false;
    }

    list($origWidth, $origHeight, $type) = $imageInfo;

    // Resize lazım deyilsə
    if ($origWidth <= $maxWidth && $origHeight <= $maxHeight) {
        if ($source !== $dest) {
            return copy($source, $dest);
        }
        return true;
    }

    // Aspect ratio qoruyaraq yeni ölçüləri hesabla
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = (int)round($origWidth * $ratio);
    $newHeight = (int)round($origHeight * $ratio);

    // Mənbə şəkli yüklə
    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $srcImage = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $srcImage = imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $srcImage = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }

    if (!$srcImage) {
        return false;
    }

    // Yeni şəkil yarat
    $destImage = imagecreatetruecolor($newWidth, $newHeight);

    // PNG və WEBP üçün şəffaflığı qoru
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
        imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Resize
    imagecopyresampled(
        $destImage, $srcImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $origWidth, $origHeight
    );

    // Yadda saxla
    $result = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($destImage, $dest, 85);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($destImage, $dest, 8);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($destImage, $dest);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($destImage, $dest, 85);
            break;
    }

    // Yaddaşı boşalt
    imagedestroy($srcImage);
    imagedestroy($destImage);

    return $result;
}

/**
 * Səhifə məzmunlarını DB-dən yükləyir.
 * DB-də tapılmadıqda $defaults fallback kimi istifadə olunur.
 * 
 * @param PDO $db Database bağlantısı
 * @param string $pageKey Səhifə açarı (home, about-hazrat, və s.)
 * @param array $defaults Default dəyərlər [section_key => [lang => value, ...], ...]
 * @return array [section_key => [lang => value, ...], ...]
 */
function bb_load_page_contents(PDO $db, string $pageKey, array $defaults = []): array
{
    $result = $defaults;

    try {
        $stmt = $db->prepare("SELECT * FROM page_contents WHERE page_key = :page_key");
        $stmt->execute([':page_key' => $pageKey]);
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            $sectionKey = $row['section_key'];
            $langs = ['az', 'en', 'ru', 'ar', 'fa'];
            foreach ($langs as $l) {
                $dbValue = $row["content_{$l}"] ?? '';
                if (!empty(trim($dbValue))) {
                    $result[$sectionKey][$l] = $dbValue;
                }
            }
        }
    } catch (PDOException $e) {
        // Cədvəl mövcud deyilsə fallback-ləri istifadə et
    }

    return $result;
}

/**
 * XSS qoruması üçün output escaping.
 * 
 * @param string|null $input Giriş mətni
 * @return string Təhlükəsiz mətn
 */
function bb_sanitize(?string $input): string
{
    if ($input === null) {
        return '';
    }
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * Yönləndirmə (redirect) edir.
 * 
 * @param string $url Hədəf URL
 * @param int $statusCode HTTP status kodu (default: 302)
 */
function bb_redirect(string $url, int $statusCode = 302): void
{
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Flash mesaj yaradır (session-based).
 * Növbəti səhifə yüklənməsində göstərilir və silinir.
 * 
 * @param string $type Mesaj tipi (success, error, warning, info)
 * @param string $message Mesaj mətni
 */
function bb_flash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }

    $_SESSION['flash_messages'][] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Flash mesajları oxuyub silir.
 * 
 * @return array Mesajlar massivi [['type' => '...', 'message' => '...'], ...]
 */
function bb_get_flash(): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);

    return $messages;
}

/**
 * Flash mesajları HTML olaraq render edir.
 * 
 * @return string HTML output
 */
function bb_render_flash(): string
{
    $messages = bb_get_flash();
    if (empty($messages)) {
        return '';
    }

    $html = '';
    foreach ($messages as $msg) {
        $type = bb_sanitize($msg['type']);
        $message = bb_sanitize($msg['message']);
        $html .= '<div class="bb-alert bb-alert-' . $type . '">';
        $html .= '<span class="bb-alert-message">' . $message . '</span>';
        $html .= '<button type="button" class="bb-alert-close" onclick="this.parentElement.remove()">&times;</button>';
        $html .= '</div>';
    }

    return $html;
}
