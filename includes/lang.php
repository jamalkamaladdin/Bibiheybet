<?php
/**
 * Bibiheybet.com - Dil İdarəetmə Sistemi
 * 
 * Dil detection, switch, fallback məntiqi.
 * URL prefix əsaslı çoxdilli routing.
 */

/**
 * URL route xəritəsi - hər dil üçün slug prefikslər.
 */
$bb_route_map = [
    'az' => [
        'articles'     => 'meqaleler',
        'article'      => 'meqale',
        'pilgrimages'  => 'ziyaretgahlar',
        'pilgrimage'   => 'ziyaretgah',
        'about-hazrat' => 'hezret-haqqinda',
        'about-mosque' => 'mescid-haqqinda',
        'prayers'      => 'dua-ve-ziyaretname',
    ],
    'en' => [
        'articles'     => 'articles',
        'article'      => 'article',
        'pilgrimages'  => 'pilgrimages',
        'pilgrimage'   => 'pilgrimage',
        'about-hazrat' => 'about-hazrat',
        'about-mosque' => 'about-mosque',
        'prayers'      => 'prayers',
    ],
    'ru' => [
        'articles'     => 'stati',
        'article'      => 'statya',
        'pilgrimages'  => 'svyatyni',
        'pilgrimage'   => 'svyatynya',
        'about-hazrat' => 'o-hazrat',
        'about-mosque' => 'o-mecheti',
        'prayers'      => 'molitvy',
    ],
];

/**
 * Dil adları (öz dillərində).
 */
$bb_lang_names = [
    'az' => 'AZ',
    'en' => 'EN',
    'ru' => 'RU',
];

/**
 * Dil-locale xəritəsi (SEO / OG üçün).
 */
$bb_lang_locales = [
    'az' => 'az_AZ',
    'en' => 'en_US',
    'ru' => 'ru_RU',
];

/**
 * Cari dili müəyyən edir.
 * Prioritet: URL prefix > Cookie > Session > Default (az).
 * 
 * @return string Dil kodu (az, en, ru)
 */
function bb_detect_lang(): string
{
    if (!defined('DEFAULT_LANG')) {
        require_once __DIR__ . '/../config.php';
    }

    $availableLangs = AVAILABLE_LANGS;
    $defaultLang = DEFAULT_LANG;

    // 1. URL prefix-dən (GET parametr olaraq gəlir .htaccess-dən)
    if (isset($_GET['lang']) && in_array($_GET['lang'], $availableLangs)) {
        $lang = $_GET['lang'];
        bb_set_lang($lang);
        return $lang;
    }

    // 2. Cookie-dən
    if (isset($_COOKIE['bb_lang']) && in_array($_COOKIE['bb_lang'], $availableLangs)) {
        return $_COOKIE['bb_lang'];
    }

    // 3. Session-dan
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['bb_lang']) && in_array($_SESSION['bb_lang'], $availableLangs)) {
        return $_SESSION['bb_lang'];
    }

    return $defaultLang;
}

/**
 * Dili dəyişdirir (cookie + session).
 * 
 * @param string $lang Dil kodu (az, en, ru)
 */
function bb_set_lang(string $lang): void
{
    if (!in_array($lang, AVAILABLE_LANGS)) {
        $lang = DEFAULT_LANG;
    }

    // Cookie: 1 il müddət
    setcookie('bb_lang', $lang, [
        'expires'  => time() + (365 * 24 * 60 * 60),
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly'  => false, // JS-dən oxuna bilsin (dil switch üçün)
        'samesite' => 'Lax',
    ]);

    // Session
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['bb_lang'] = $lang;
    }
}

/**
 * Cari dili qaytarır.
 * 
 * @return string Dil kodu
 */
function bb_get_lang(): string
{
    static $currentLang = null;

    if ($currentLang === null) {
        $currentLang = bb_detect_lang();
    }

    return $currentLang;
}

/**
 * Mövcud dilləri qaytarır.
 * 
 * @return array
 */
function bb_get_available_langs(): array
{
    return AVAILABLE_LANGS;
}

/**
 * Dilə uyğun URL yaradır.
 * 
 * AZ dili default olduğu üçün prefix almır.
 * EN və RU dillərində /en/ və /ru/ prefix əlavə olunur.
 * 
 * @param string $path URL yolu (məsələn: 'meqaleler', 'meqale/slug-adi')
 * @param string|null $lang Hədəf dil (null = cari dil)
 * @return string Tam URL yolu
 */
function bb_lang_url(string $path = '', ?string $lang = null): string
{
    if ($lang === null) {
        $lang = bb_get_lang();
    }

    // Əvvəlindəki / sil
    $path = ltrim($path, '/');

    if (!defined('SITE_URL')) {
        require_once __DIR__ . '/../config.php';
    }

    if ($lang === 'az' || $lang === DEFAULT_LANG) {
        return SITE_URL . '/' . $path;
    }

    return SITE_URL . '/' . $lang . '/' . $path;
}

/**
 * Route adını dilə uyğun URL segment-inə çevirir.
 * 
 * Misal: bb_get_route('articles', 'az') => 'meqaleler'
 *        bb_get_route('articles', 'en') => 'articles'
 *        bb_get_route('articles', 'ru') => 'stati'
 * 
 * @param string $routeName Route adı (articles, article, pilgrimages, pilgrimage)
 * @param string|null $lang Dil kodu (null = cari dil)
 * @return string URL segment
 */
function bb_get_route(string $routeName, ?string $lang = null): string
{
    global $bb_route_map;

    if ($lang === null) {
        $lang = bb_get_lang();
    }

    return $bb_route_map[$lang][$routeName] ?? $routeName;
}

/**
 * URL segment-indən route adını tapır (əks çevrilmə).
 * 
 * Misal: bb_resolve_route('meqaleler') => 'articles'
 *        bb_resolve_route('stati')     => 'articles'
 * 
 * @param string $segment URL segment
 * @param string|null $lang Dil kodu (null = cari dil)
 * @return string|null Route adı və ya null
 */
function bb_resolve_route(string $segment, ?string $lang = null): ?string
{
    global $bb_route_map;

    if ($lang === null) {
        $lang = bb_get_lang();
    }

    if (isset($bb_route_map[$lang])) {
        $flipped = array_flip($bb_route_map[$lang]);
        if (isset($flipped[$segment])) {
            return $flipped[$segment];
        }
    }

    return null;
}

/**
 * DB row-dan dilə uyğun sahəni oxuyur.
 * Boşdursa AZ (default) fallback istifadə edir.
 * 
 * Misal: bb_get_field($article, 'title', 'en')
 *   -> title_en boşdursa title_az qaytarır
 * 
 * @param array $row Database row (assoc array)
 * @param string $field Sahə adı prefiksi (title, content, name, slug, və s.)
 * @param string|null $lang Dil kodu (null = cari dil)
 * @return string|null Sahə dəyəri
 */
function bb_get_field(array $row, string $field, ?string $lang = null): ?string
{
    if ($lang === null) {
        $lang = bb_get_lang();
    }

    $fieldName = $field . '_' . $lang;

    // İstənilən dildə dəyər varsa, onu qaytar
    if (!empty($row[$fieldName])) {
        return $row[$fieldName];
    }

    // Fallback: default dil (az)
    $defaultField = $field . '_' . DEFAULT_LANG;
    if (isset($row[$defaultField])) {
        return $row[$defaultField];
    }

    // Suffix-siz sahə adı yoxla (məsələn 'featured_image')
    if (isset($row[$field])) {
        return $row[$field];
    }

    return null;
}

/**
 * Featured image-i dilə uyğun qaytarır.
 * Dil-spesifik foto yoxdursa, əsas (default) foto qaytarılır.
 * 
 * @param array $row Database row
 * @param string|null $lang Dil kodu (null = cari dil)
 * @return string|null Şəkil yolu
 */
function bb_get_featured_image(array $row, ?string $lang = null): ?string
{
    if ($lang === null) {
        $lang = bb_get_lang();
    }

    // Dil-spesifik foto yoxla
    if ($lang !== 'az') {
        $langField = 'featured_image_' . $lang;
        if (!empty($row[$langField])) {
            return $row[$langField];
        }
    }

    // Default foto
    return $row['featured_image'] ?? null;
}

/**
 * Bütün dillər üçün alternativ URL-ləri yaradır (hreflang üçün).
 * 
 * @param string $routeName Route adı (article, pilgrimage, və s.)
 * @param array $slugs Slug-lar massivi ['az' => '...', 'en' => '...', 'ru' => '...']
 * @return array Dil -> URL massivi
 */
function bb_get_alternate_urls(string $routeName, array $slugs): array
{
    $urls = [];

    foreach (AVAILABLE_LANGS as $lang) {
        $route = bb_get_route($routeName, $lang);
        $slug = $slugs[$lang] ?? $slugs['az'] ?? '';

        if (!empty($slug)) {
            $urls[$lang] = bb_lang_url($route . '/' . $slug, $lang);
        } else {
            $urls[$lang] = bb_lang_url($route . '/', $lang);
        }
    }

    return $urls;
}

// ============================================
// Frontend Menyu Sistemi
// ============================================

/** Sayt menyusu - hər dil üçün label və route adı */
$bb_menu = [
    'az' => [
        ['label' => 'Ana səhifə',         'route' => ''],
        ['label' => 'Həzrət haqqında',     'route' => 'about-hazrat'],
        ['label' => 'Məscid haqqında',     'route' => 'about-mosque'],
        ['label' => 'Dua və ziyarətnamə',  'route' => 'prayers'],
        ['label' => 'Ziyarətgahlar',       'route' => 'pilgrimages'],
        ['label' => 'Məqalələr',           'route' => 'articles'],
    ],
    'en' => [
        ['label' => 'Home',            'route' => ''],
        ['label' => 'About Hazrat',    'route' => 'about-hazrat'],
        ['label' => 'About Mosque',    'route' => 'about-mosque'],
        ['label' => 'Prayers',         'route' => 'prayers'],
        ['label' => 'Pilgrimages',     'route' => 'pilgrimages'],
        ['label' => 'Articles',        'route' => 'articles'],
    ],
    'ru' => [
        ['label' => 'Главная',     'route' => ''],
        ['label' => 'О Хазрат',    'route' => 'about-hazrat'],
        ['label' => 'О Мечети',    'route' => 'about-mosque'],
        ['label' => 'Молитвы',     'route' => 'prayers'],
        ['label' => 'Святыни',     'route' => 'pilgrimages'],
        ['label' => 'Статьи',      'route' => 'articles'],
    ],
];

/** Menyu elementlərini qaytarır */
function bb_get_menu(?string $lang = null): array
{
    global $bb_menu;
    if ($lang === null) {
        $lang = bb_get_lang();
    }
    return $bb_menu[$lang] ?? $bb_menu['az'];
}

/**
 * Dil switch URL yaradır (cari səhifənin hədəf dildəki versiyası).
 * Tək element səhifələri üçün alternate_urls global-dan istifadə edir.
 */
function bb_lang_switch_url(string $targetLang): string
{
    global $bb_page_context;

    $ctx = $bb_page_context ?? [];
    $pageName = $ctx['name'] ?? 'home';
    $routeName = $ctx['route'] ?? null;

    // Əgər template alternate URL-lər təyin edibsə (tək məqalə/ziyarətgah üçün)
    if (!empty($ctx['alternate_urls'][$targetLang])) {
        return $ctx['alternate_urls'][$targetLang];
    }

    // Ana səhifə
    if ($pageName === 'home') {
        return bb_lang_url('', $targetLang);
    }

    // Siyahı səhifələri (articles, pilgrimages)
    if (in_array($routeName, ['articles', 'pilgrimages'])) {
        return bb_lang_url(bb_get_route($routeName, $targetLang) . '/', $targetLang);
    }

    // Statik səhifələr (about-hazrat, about-mosque, prayers)
    if (in_array($routeName, ['about-hazrat', 'about-mosque', 'prayers'])) {
        return bb_lang_url(bb_get_route($routeName, $targetLang) . '/', $targetLang);
    }

    // Default: ana səhifə
    return bb_lang_url('', $targetLang);
}

/** Cari URL-in menyu itemə uyğun olub-olmadığını yoxlayır */
function bb_is_menu_active(string $currentRoute, string $menuRoute, string $lang): bool
{
    // Ana səhifə
    if (empty($menuRoute)) {
        return empty($currentRoute);
    }

    $routeSlug = bb_get_route($menuRoute, $lang);
    $firstSegment = explode('/', trim($currentRoute, '/'))[0] ?? '';

    // Tam uyğunluq və ya alt-route uyğunluğu
    if ($firstSegment === $routeSlug) {
        return true;
    }

    // Tək element səhifələri üçün (article -> articles menyusu aktiv olsun)
    $parentMap = ['article' => 'articles', 'pilgrimage' => 'pilgrimages'];
    foreach ($parentMap as $child => $parent) {
        if ($menuRoute === $parent) {
            $childSlug = bb_get_route($child, $lang);
            if ($firstSegment === $childSlug) {
                return true;
            }
        }
    }

    return false;
}
