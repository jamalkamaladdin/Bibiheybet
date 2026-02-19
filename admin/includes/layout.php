<?php
/**
 * Bibiheybet.com - Admin Panel Layout
 * 
 * Ortaq header (sidebar + topbar) və footer funksiyaları.
 * Hər admin səhifə bu fayli include edib bb_admin_header() / bb_admin_footer() çağırır.
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Session başlat və auth yoxla DƏRHAL — hər hansı DB sorğusundan ƏVVƏL.
// Bu, session_start()-dan əvvəl output olmasının qarşısını alır.
bb_start_session();
bb_require_auth();

/**
 * Admin səhifənin HTML başlanğıcını render edir: <head>, sidebar, topbar.
 * Auth yoxlanışı artıq yuxarıda (include vaxtı) avtomatik baş verir.
 */
function bb_admin_header(string $pageTitle = 'Dashboard', array $options = []): void
{
    global $_bb_admin_extra_js;
    $_bb_admin_extra_js = $options['extra_js'] ?? [];

    $adminUsername = bb_current_admin_username() ?? 'Admin';
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $flashHtml = bb_render_flash();

    // Session lock-u erkən burax ki, paralel sorğular (JS/CSS yükləmələri)
    // session faylını bloklamasın. CSRF token lazım olanda session yenidən açılacaq.
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    // Sidebar menyu itemləri
    $menuItems = bb_admin_menu_items();
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= bb_sanitize($pageTitle) ?> | <?= bb_sanitize(SITE_NAME) ?> Admin</title>
    <link rel="icon" type="image/png" href="/public/assets/img/icon.png">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
    <?php if (!empty($options['extra_css'])): ?>
        <?php foreach ((array)$options['extra_css'] as $css): ?>
            <link rel="stylesheet" href="<?= bb_sanitize($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="bb-admin-body">

    <!-- Sidebar -->
    <aside class="bb-sidebar" id="bbSidebar">
        <div class="bb-sidebar-header">
            <a href="/admin/index.php" class="bb-sidebar-logo">
                <img src="/public/assets/img/logo.png" alt="<?= bb_sanitize(SITE_NAME) ?>">
            </a>
            <button type="button" class="bb-sidebar-close" id="bbSidebarClose" aria-label="Menyunu bağla">&times;</button>
        </div>

        <nav class="bb-sidebar-nav">
            <ul class="bb-sidebar-menu">
                <?php foreach ($menuItems as $item): ?>
                    <?php if ($item['type'] === 'separator'): ?>
                        <li class="bb-sidebar-separator"></li>
                    <?php else:
                        $isActive = bb_admin_is_active($currentUri, $item['url']);
                    ?>
                        <li class="bb-sidebar-item<?= $isActive ? ' active' : '' ?>">
                            <a href="<?= bb_sanitize($item['url']) ?>">
                                <span class="bb-sidebar-icon"><?= $item['icon'] ?></span>
                                <span class="bb-sidebar-label"><?= bb_sanitize($item['label']) ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="bb-sidebar-footer">
            <a href="/admin/logout.php" class="bb-sidebar-logout">
                <span class="bb-sidebar-icon">&#x1F6AA;</span>
                <span class="bb-sidebar-label">Çıxış</span>
            </a>
        </div>
    </aside>

    <!-- Overlay (mobil sidebar üçün) -->
    <div class="bb-sidebar-overlay" id="bbSidebarOverlay"></div>

    <!-- Main Content -->
    <div class="bb-main">
        <!-- Top Bar -->
        <header class="bb-topbar">
            <button type="button" class="bb-topbar-toggle" id="bbSidebarToggle" aria-label="Menyunu aç">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <h1 class="bb-topbar-title"><?= bb_sanitize($pageTitle) ?></h1>
            <div class="bb-topbar-user">
                <span class="bb-topbar-username"><?= bb_sanitize($adminUsername) ?></span>
            </div>
        </header>

        <!-- Content Area -->
        <main class="bb-content">
            <?= $flashHtml ?>
<?php
}

/**
 * Admin səhifənin HTML sonunu render edir: content bağlanır, JS include olunur.
 */
function bb_admin_footer(array $options = []): void
{
    global $_bb_admin_extra_js;
    $extraJs = !empty($options['extra_js']) ? (array)$options['extra_js'] : (array)($_bb_admin_extra_js ?? []);
?>
        </main>
    </div>

    <script src="/admin/assets/js/admin.js"></script>
    <?php foreach ($extraJs as $js): ?>
        <script src="<?= bb_sanitize($js) ?>"></script>
    <?php endforeach; ?>
    <script>
        if (typeof initTabs === 'function') initTabs();
        if (typeof bbInitEditors === 'function') bbInitEditors();
        if (typeof bbInitImageUploads === 'function') bbInitImageUploads();
        if (typeof bbInitGallery === 'function') bbInitGallery();
    </script>
</body>
</html>
<?php
}

/**
 * Sidebar menyu elementlərini qaytarır.
 */
function bb_admin_menu_items(): array
{
    return [
        [
            'type'  => 'link',
            'label' => 'Dashboard',
            'url'   => '/admin/index.php',
            'icon'  => '&#x1F4CA;', // bar chart
        ],
        ['type' => 'separator'],
        [
            'type'  => 'link',
            'label' => 'Məqalələr',
            'url'   => '/admin/articles/',
            'icon'  => '&#x1F4DD;', // memo
        ],
        [
            'type'  => 'link',
            'label' => 'Kateqoriyalar',
            'url'   => '/admin/categories/',
            'icon'  => '&#x1F4C1;', // folder
        ],
        ['type' => 'separator'],
        [
            'type'  => 'link',
            'label' => 'Ziyarətgahlar',
            'url'   => '/admin/pilgrimages/',
            'icon'  => '&#x1F54C;', // mosque
        ],
        [
            'type'  => 'link',
            'label' => 'Xanım haqqında',
            'url'   => '/admin/xanim-haqqinda/',
            'icon'  => '&#x1F9D5;', // woman
        ],
        ['type' => 'separator'],
        [
            'type'  => 'link',
            'label' => 'Səhifə Məzmunları',
            'url'   => '/admin/pages/',
            'icon'  => '&#x1F4C4;', // page
        ],
    ];
}

/**
 * Cari URL-in menyu iteminə uyğun olub-olmadığını yoxlayır.
 */
function bb_admin_is_active(string $currentUri, string $menuUrl): bool
{
    $currentPath = parse_url($currentUri, PHP_URL_PATH) ?? '';

    // Tam uyğunluq
    if ($currentPath === $menuUrl) {
        return true;
    }

    // Qovluq uyğunluğu (məsələn /admin/articles/ altındakı bütün səhifələr)
    if (str_ends_with($menuUrl, '/') && str_starts_with($currentPath, $menuUrl)) {
        return true;
    }

    return false;
}
