<?php
/**
 * Bibiheybet.com - Frontend Header
 * 
 * İki rejim: Hero header (ana səhifə) və kompakt header (digər səhifələr).
 * Hər template bb_frontend_header() çağırır.
 */

/**
 * Frontend səhifənin HTML başlanğıcını render edir.
 * 
 * @param array $options
 *   - seo_data (array)   bb_render_meta() üçün SEO data
 *   - body_class (string) Body-yə əlavə CSS class
 *   - extra_css (array)   Əlavə CSS faylları
 *   - is_home (bool)      Ana səhifə rejimi (hero header)
 */
function bb_frontend_header(array $options = []): void
{
    global $lang;

    $isHome = !empty($options['is_home']);

    // RTL dillər
    $rtlLangs = ['ar', 'fa'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';

    // SEO meta taglar
    $seoHtml = '';
    $defaultAlternateUrls = [];
    foreach (bb_all_langs() as $altLang) {
        $defaultAlternateUrls[$altLang] = bb_lang_url('', $altLang);
    }

    if (!empty($options['seo_data'])) {
        $seoHtml = bb_render_meta($options['seo_data']);
    } else {
        $seoHtml = bb_render_meta([
            'title'     => SITE_NAME,
            'lang'      => $lang,
            'og_type'   => 'website',
            'canonical_url' => bb_lang_url('', $lang),
            'alternate_urls' => $defaultAlternateUrls,
        ]);
    }

    $bodyClass = 'bb-page';
    if (!empty($options['body_class'])) {
        $bodyClass .= ' ' . $options['body_class'];
    }

    $menuItems = bb_get_menu($lang);
    $currentRoute = trim($_GET['route'] ?? '', '/');
?>
<!DOCTYPE html>
<html lang="<?= bb_sanitize($lang) ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $seoHtml ?>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&family=Amiri:ital,wght@0,400;0,700;1,400&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <?php $cssVer = '?v=' . time(); ?>
    <link rel="stylesheet" href="/public/assets/css/global.css<?= $cssVer ?>">
    <link rel="stylesheet" href="/public/assets/css/header.css<?= $cssVer ?>">
    <link rel="stylesheet" href="/public/assets/css/footer.css<?= $cssVer ?>">
    <link rel="stylesheet" href="/public/assets/css/mini-player.css<?= $cssVer ?>">
    <?php if (!empty($options['extra_css'])): ?>
        <?php foreach ((array)$options['extra_css'] as $css): ?>
    <link rel="stylesheet" href="<?= bb_sanitize($css) ?><?= $cssVer ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= bb_sanitize($bodyClass) ?>">

<?php if ($isHome): ?>
    <?php bb_render_hero_header($menuItems, $currentRoute, $lang, $options['hero_subtitle'] ?? ''); ?>
<?php else: ?>
    <?php bb_render_compact_header($menuItems, $currentRoute, $lang); ?>
<?php endif; ?>

    <!-- Hamburger menyu paneli (fullscreen) -->
    <aside class="bb-mobile-menu" id="bbMobileMenu" aria-hidden="true">
        <div class="bb-mobile-menu-inner">
            <button type="button" class="bb-mobile-menu-close" id="bbMobileMenuClose" aria-label="Menyunu bağla">&times;</button>
            <nav class="bb-mobile-nav" aria-label="Əsas naviqasiya">
                <ul class="bb-mobile-menu-list">
                    <?php foreach ($menuItems as $item): ?>
                        <?php
                            $menuUrl = empty($item['route'])
                                ? bb_lang_url('', $lang)
                                : bb_lang_url(bb_get_route($item['route'], $lang) . '/', $lang);
                            $isActive = bb_is_menu_active($currentRoute, $item['route'], $lang);
                        ?>
                    <li class="bb-mobile-menu-item<?= $isActive ? ' active' : '' ?>">
                        <a href="<?= bb_sanitize($menuUrl) ?>"><?= bb_sanitize($item['label']) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div class="bb-mobile-lang">
                <?php foreach (bb_all_langs() as $switchLang): ?>
                    <?php if ($switchLang === $lang): ?>
                <span class="bb-mobile-lang-item bb-mobile-lang-active"><?= strtoupper($switchLang) ?></span>
                    <?php else: ?>
                <a href="<?= bb_sanitize(bb_lang_switch_url($switchLang)) ?>" class="bb-mobile-lang-item"><?= strtoupper($switchLang) ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="bb-mobile-social">
                <a href="https://www.instagram.com/bibiheybetziyaretgahi/" target="_blank" rel="noopener" aria-label="Instagram">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="https://www.facebook.com/bibiheybetmecidi/" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Əsas kontent -->
    <main class="bb-main" id="bbMain">
<?php
}

/** Ana səhifə hero header */
function bb_render_hero_header(array $menuItems, string $currentRoute, string $lang, string $heroSubtitleOverride = ''): void
{
    $heroSubtitles = [
        'az' => 'Fatimeyi-Suğra, Həkimə xanımın müqəddəs ziyarətgahı',
        'en' => 'The holy shrine of Lady Fatima Sugra, Hakima Khanym',
        'ru' => 'Святая обитель госпожи Фатимы ас-Сугры (Хакимы ханум)',
        'ar' => "المقام المقدس للسيدة فاطمة الصغرى\nحكيمة خاتون عليها السلام",
        'fa' => 'زیارتگاه مقدس حضرت فاطمه صغری حکیمه خاتون (سلام‌الله‌علیها)',
    ];
    $heroSubtitle = !empty($heroSubtitleOverride) ? $heroSubtitleOverride : ($heroSubtitles[$lang] ?? $heroSubtitles['az']);
?>
    <header class="bb-hero-header" id="bbHeader" data-header-type="hero">
        <!-- Fon naxışları -->
        <div class="bb-hero-bg">
            <div class="bb-hero-pattern bb-hero-pattern-1">
                <img src="/public/assets/img/naxis.png" alt="" aria-hidden="true">
            </div>
            <div class="bb-hero-pattern bb-hero-pattern-2">
                <img src="/public/assets/img/naxis2.png" alt="" aria-hidden="true">
            </div>
        </div>

        <div class="bb-hero-content">
            <!-- Top bar: logo (mobil) + nav + lang + hamburger -->
            <div class="bb-hero-topbar">
                <a href="<?= bb_lang_url('', $lang) ?>" class="bb-hero-mobile-logo" aria-label="<?= bb_sanitize(SITE_NAME) ?>">
                    <img src="/public/assets/img/logo.png" alt="<?= bb_sanitize(SITE_NAME) ?>">
                </a>

                <nav class="bb-hero-nav" aria-label="Əsas naviqasiya">
                    <ul class="bb-hero-menu">
                        <?php foreach ($menuItems as $item): ?>
                            <?php
                                $menuUrl = empty($item['route'])
                                    ? bb_lang_url('', $lang)
                                    : bb_lang_url(bb_get_route($item['route'], $lang) . '/', $lang);
                                $isActive = bb_is_menu_active($currentRoute, $item['route'], $lang);
                            ?>
                        <li class="bb-hero-menu-item<?= $isActive ? ' active' : '' ?>">
                            <a href="<?= bb_sanitize($menuUrl) ?>"><?= bb_sanitize($item['label']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="bb-lang-dropdown" id="bbLangSwitch">
                        <button type="button" class="bb-lang-toggle" id="bbLangToggle" aria-expanded="false">
                            <?= strtoupper($lang) ?>
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="currentColor"><path d="M1 1l4 4 4-4"/></svg>
                        </button>
                        <ul class="bb-lang-dropdown-list" id="bbLangList">
                            <?php foreach (bb_all_langs() as $switchLang): ?>
                                <?php if ($switchLang === $lang): ?>
                            <li><span class="bb-lang-dropdown-item bb-lang-active"><?= strtoupper($switchLang) ?></span></li>
                                <?php else: ?>
                            <li><a href="<?= bb_sanitize(bb_lang_switch_url($switchLang)) ?>" class="bb-lang-dropdown-item"><?= strtoupper($switchLang) ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </nav>

                <button type="button" class="bb-hamburger" id="bbHamburger" aria-label="Menyunu aç" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <!-- Logo (desktop) -->
            <a href="<?= bb_lang_url('', $lang) ?>" class="bb-hero-logo" aria-label="<?= bb_sanitize(SITE_NAME) ?>">
                <img src="/public/assets/img/logo.png" alt="<?= bb_sanitize(SITE_NAME) ?>">
            </a>

            <!-- Məscid illustrasiyası + subtitle -->
            <div class="bb-hero-mosque">
                <p class="bb-hero-subtitle"><?= nl2br(bb_sanitize($heroSubtitle)) ?></p>
                <img src="/public/assets/img/bibiheybet-ziyaretgah.png?v=2" alt="Bibiheybət Məscidi">
            </div>
        </div>
    </header>
<?php
}

/** Digər səhifələr üçün kompakt header */
function bb_render_compact_header(array $menuItems, string $currentRoute, string $lang): void
{
?>
    <header class="bb-header" id="bbHeader" data-header-type="compact">
        <div class="bb-header-inner bb-container">
            <!-- Logo -->
            <a href="<?= bb_lang_url('', $lang) ?>" class="bb-header-logo" aria-label="<?= bb_sanitize(SITE_NAME) ?>">
                <img src="/public/assets/img/logo.png" alt="<?= bb_sanitize(SITE_NAME) ?>">
            </a>

            <!-- Desktop nav -->
            <nav class="bb-header-nav" aria-label="Əsas naviqasiya">
                <ul class="bb-header-menu">
                    <?php foreach ($menuItems as $item): ?>
                        <?php
                            $menuUrl = empty($item['route'])
                                ? bb_lang_url('', $lang)
                                : bb_lang_url(bb_get_route($item['route'], $lang) . '/', $lang);
                            $isActive = bb_is_menu_active($currentRoute, $item['route'], $lang);
                        ?>
                    <li class="bb-header-menu-item<?= $isActive ? ' active' : '' ?>">
                        <a href="<?= bb_sanitize($menuUrl) ?>"><?= bb_sanitize($item['label']) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="bb-lang-dropdown" id="bbLangSwitch">
                    <button type="button" class="bb-lang-toggle" id="bbLangToggle" aria-expanded="false">
                        <?= strtoupper($lang) ?>
                        <svg width="10" height="6" viewBox="0 0 10 6" fill="currentColor"><path d="M1 1l4 4 4-4"/></svg>
                    </button>
                    <ul class="bb-lang-dropdown-list" id="bbLangList">
                        <?php foreach (bb_all_langs() as $switchLang): ?>
                            <?php if ($switchLang === $lang): ?>
                        <li><span class="bb-lang-dropdown-item bb-lang-active"><?= strtoupper($switchLang) ?></span></li>
                            <?php else: ?>
                        <li><a href="<?= bb_sanitize(bb_lang_switch_url($switchLang)) ?>" class="bb-lang-dropdown-item"><?= strtoupper($switchLang) ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>

            <!-- Sağ tərəf: hamburger -->
            <div class="bb-header-actions">
                <button type="button" class="bb-hamburger" id="bbHamburger" aria-label="Menyunu aç" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>
<?php
}
