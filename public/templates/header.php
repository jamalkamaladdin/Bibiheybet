<?php
/**
 * Bibiheybet.com - Frontend Header
 * 
 * Sayt header skeleti: HTML head, logo, menyu, dil switch, hamburger.
 * Hər template bu faylı include edib bb_frontend_header() çağırır.
 */

/**
 * Frontend səhifənin HTML başlanğıcını render edir.
 * 
 * @param array $options Seçimlər:
 *   - seo_data (array) bb_render_meta() üçün SEO data
 *   - body_class (string) Body-yə əlavə CSS class
 *   - extra_css (array) Əlavə CSS faylları
 */
function bb_frontend_header(array $options = []): void
{
    global $lang;

    // SEO meta taglar
    $seoHtml = '';
    if (!empty($options['seo_data'])) {
        $seoHtml = bb_render_meta($options['seo_data']);
    } else {
        $seoHtml = bb_render_meta([
            'title'     => SITE_NAME,
            'lang'      => $lang,
            'og_type'   => 'website',
            'canonical_url' => bb_lang_url('', $lang),
            'alternate_urls' => [
                'az' => bb_lang_url('', 'az'),
                'en' => bb_lang_url('', 'en'),
                'ru' => bb_lang_url('', 'ru'),
            ],
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
<html lang="<?= bb_sanitize($lang) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $seoHtml ?>
    <!-- Font preload -->
    <link rel="preload" href="/public/assets/fonts/SpaceGrotesk-VariableFont_wght.woff2" as="font" type="font/woff2" crossorigin>
    <!-- Amiri (ərəb mətnlər üçün) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="/public/assets/css/global.css">
    <?php if (!empty($options['extra_css'])): ?>
        <?php foreach ((array)$options['extra_css'] as $css): ?>
    <link rel="stylesheet" href="<?= bb_sanitize($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= bb_sanitize($bodyClass) ?>">

    <!-- Header -->
    <header class="bb-header" id="bbHeader">
        <div class="bb-header-inner bb-container">
            <!-- Logo -->
            <a href="<?= bb_lang_url('', $lang) ?>" class="bb-header-logo" aria-label="<?= bb_sanitize(SITE_NAME) ?>">
                <img src="/public/assets/img/logo.png" alt="<?= bb_sanitize(SITE_NAME) ?>">
            </a>

            <!-- Header sağ: dil switch + hamburger -->
            <div class="bb-header-actions">
                <!-- Dil seçici -->
                <div class="bb-lang-switch" id="bbLangSwitch">
                    <?php foreach (AVAILABLE_LANGS as $switchLang): ?>
                        <?php if ($switchLang === $lang): ?>
                    <span class="bb-lang-item bb-lang-active"><?= strtoupper($switchLang) ?></span>
                        <?php else: ?>
                    <a href="<?= bb_sanitize(bb_lang_switch_url($switchLang)) ?>" class="bb-lang-item"><?= strtoupper($switchLang) ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Hamburger -->
                <button type="button" class="bb-hamburger" id="bbHamburger" aria-label="Menyunu aç" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Hamburger menyu paneli -->
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
            <!-- Sosial linklər (FAZA 6-da tamamlanacaq) -->
            <div class="bb-mobile-social">
                <a href="https://www.instagram.com/bibiheybetziyaretgahi/" target="_blank" rel="noopener" aria-label="Instagram">Instagram</a>
                <a href="https://www.facebook.com/bibiheybetmecidi/" target="_blank" rel="noopener" aria-label="Facebook">Facebook</a>
            </div>
        </div>
    </aside>
    <div class="bb-mobile-overlay" id="bbMobileOverlay"></div>

    <!-- Əsas kontent -->
    <main class="bb-main" id="bbMain">
<?php
}
