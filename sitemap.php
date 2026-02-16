<?php
/**
 * Bibiheybet.com - Dinamik Sitemap.xml Generator
 * 
 * FAZA 10: Bütün publik səhifələr, məqalələr və ziyarətgahlar üçün sitemap.
 * Hər dil üçün ayrı URL-lər hreflang ilə.
 * 
 * Giriş: /sitemap.xml (.htaccess-dən yönləndirilir)
 */

require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/db.php';
require_once INCLUDES_PATH . '/lang.php';
require_once INCLUDES_PATH . '/functions.php';

header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=3600');

$db = bb_get_db();
$siteUrl = rtrim(SITE_URL, '/');
$langs = bb_all_langs();
$defaultLang = DEFAULT_LANG;
$now = date('c');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

<?php
/**
 * URL entry yaradır (hreflang dəstəyi ilə).
 */
function sitemap_entry(string $siteUrl, array $langUrls, string $priority = '0.5', string $changefreq = 'weekly', ?string $lastmod = null): string
{
    $xml = '';
    foreach ($langUrls as $lang => $path) {
        $url = $siteUrl . '/' . ltrim($path, '/');
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url, ENT_XML1) . "</loc>\n";
        if ($lastmod) {
            $xml .= "    <lastmod>" . htmlspecialchars($lastmod, ENT_XML1) . "</lastmod>\n";
        }
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";

        foreach ($langUrls as $altLang => $altPath) {
            $altUrl = $siteUrl . '/' . ltrim($altPath, '/');
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($altLang, ENT_XML1) . '" href="' . htmlspecialchars($altUrl, ENT_XML1) . '"/>' . "\n";
        }

        $xml .= "  </url>\n";
    }
    return $xml;
}

// Ana səhifə
$homeUrls = [];
foreach ($langs as $l) {
    $homeUrls[$l] = ($l === $defaultLang) ? '' : $l . '/';
}
echo sitemap_entry($siteUrl, $homeUrls, '1.0', 'daily', $now);

// Statik səhifələr
$staticRoutes = ['about-hazrat', 'about-mosque', 'prayers', 'prayer-times'];
foreach ($staticRoutes as $route) {
    $routeUrls = [];
    foreach ($langs as $l) {
        $slug = bb_get_route($route, $l);
        $routeUrls[$l] = ($l === $defaultLang) ? $slug . '/' : $l . '/' . $slug . '/';
    }
    echo sitemap_entry($siteUrl, $routeUrls, '0.7', 'monthly');
}

// Siyahı səhifələri
$listRoutes = ['articles', 'pilgrimages'];
foreach ($listRoutes as $route) {
    $routeUrls = [];
    foreach ($langs as $l) {
        $slug = bb_get_route($route, $l);
        $routeUrls[$l] = ($l === $defaultLang) ? $slug . '/' : $l . '/' . $slug . '/';
    }
    echo sitemap_entry($siteUrl, $routeUrls, '0.8', 'daily');
}

// Məqalələr
$articles = $db->query(
    "SELECT slug_az, slug_en, slug_ru, updated_at FROM articles WHERE status = 'published' ORDER BY published_at DESC"
)->fetchAll();

foreach ($articles as $a) {
    $articleUrls = [];
    foreach ($langs as $l) {
        $routeSlug = bb_get_route('article', $l);
        $articleSlug = $a['slug_' . $l] ?? $a['slug_az'];
        if (!empty($articleSlug)) {
            $articleUrls[$l] = ($l === $defaultLang)
                ? $routeSlug . '/' . $articleSlug
                : $l . '/' . $routeSlug . '/' . $articleSlug;
        }
    }
    $lastmod = !empty($a['updated_at']) ? date('c', strtotime($a['updated_at'])) : null;
    echo sitemap_entry($siteUrl, $articleUrls, '0.6', 'weekly', $lastmod);
}

// Ziyarətgahlar
$pilgrimages = $db->query(
    "SELECT slug_az, slug_en, slug_ru, updated_at FROM pilgrimages WHERE status = 'published' ORDER BY sort_order ASC"
)->fetchAll();

foreach ($pilgrimages as $p) {
    $pilgrimUrls = [];
    foreach ($langs as $l) {
        $routeSlug = bb_get_route('pilgrimage', $l);
        $pilgrimSlug = $p['slug_' . $l] ?? $p['slug_az'];
        if (!empty($pilgrimSlug)) {
            $pilgrimUrls[$l] = ($l === $defaultLang)
                ? $routeSlug . '/' . $pilgrimSlug
                : $l . '/' . $routeSlug . '/' . $pilgrimSlug;
        }
    }
    $lastmod = !empty($p['updated_at']) ? date('c', strtotime($p['updated_at'])) : null;
    echo sitemap_entry($siteUrl, $pilgrimUrls, '0.7', 'monthly', $lastmod);
}
?>
</urlset>
