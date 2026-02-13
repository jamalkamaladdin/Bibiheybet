<?php
/**
 * Bibiheybet.com - Frontend Router
 * 
 * URL parsing, dil detection, route resolve, template yükləmə.
 * .htaccess-dən lang və route parametrləri gəlir.
 */

require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/db.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/lang.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/seo.php';

// Session (dil cookie/session üçün)
bb_start_session();

// Dil detect
$lang = bb_get_lang();

// Route parse
$route = trim($_GET['route'] ?? '', '/');
$segments = $route ? explode('/', $route, 2) : [];
$firstSegment = $segments[0] ?? '';
$secondSegment = rtrim($segments[1] ?? '', '/');

// Route resolve
$pageName = '';
$pageSlug = '';
$resolvedRoute = null;

if (empty($firstSegment)) {
    $pageName = 'home';
} else {
    $resolvedRoute = bb_resolve_route($firstSegment, $lang);

    if ($resolvedRoute !== null) {
        switch ($resolvedRoute) {
            case 'articles':
                $pageName = 'articles';
                break;
            case 'article':
                $pageName = 'article-single';
                $pageSlug = $secondSegment;
                if (empty($pageSlug)) {
                    $pageName = '404';
                }
                break;
            case 'pilgrimages':
                $pageName = 'pilgrimages';
                break;
            case 'pilgrimage':
                $pageName = 'pilgrimage-single';
                $pageSlug = $secondSegment;
                if (empty($pageSlug)) {
                    $pageName = '404';
                }
                break;
            case 'about-hazrat':
            case 'about-mosque':
            case 'prayers':
                $pageName = 'page';
                $pageSlug = $resolvedRoute;
                break;
            default:
                $pageName = '404';
                break;
        }
    } else {
        $pageName = '404';
    }
}

// Qlobal page context (dil switch, SEO və s. üçün)
$GLOBALS['bb_page_context'] = [
    'name'           => $pageName,
    'slug'           => $pageSlug,
    'route'          => $resolvedRoute,
    'alternate_urls' => [],
];

// 404 status
if ($pageName === '404') {
    http_response_code(404);
}

// Template faylı
$templateFile = __DIR__ . '/templates/' . $pageName . '.php';

if (!file_exists($templateFile)) {
    http_response_code(404);
    $pageName = '404';
    $GLOBALS['bb_page_context']['name'] = '404';
    $templateFile = __DIR__ . '/templates/404.php';
}

// DB bağlantısı
$db = bb_get_db();

// Template yüklə
require $templateFile;
