<?php
/**
 * Bibiheybet.com - Ana Səhifə
 * 
 * Hero header ilə ana səhifə. FAZA 7-də tam bölmələr əlavə olunacaq.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// SEO data
$homeTitle = [
    'az' => 'Hz. Həkimə Xanımın (s) Ziyarətgahı',
    'en' => 'Shrine of Hz. Hakimah Khatun (s)',
    'ru' => 'Святыня Хз. Хакимы Хатун (с)',
];

$homeDesc = [
    'az' => 'Bibiheybət Ziyarətgahı - Hz. Həkimə Xanımın (s) müqəddəs məzarının rəsmi veb saytı.',
    'en' => 'Bibiheybat Shrine - Official website of the holy shrine of Hz. Hakimah Khatun (s).',
    'ru' => 'Святыня Бибиэйбат - Официальный сайт священной гробницы Хз. Хакимы Хатун (с).',
];

$seoData = [
    'title'           => $homeTitle[$lang] ?? $homeTitle['az'],
    'meta_description' => $homeDesc[$lang] ?? $homeDesc['az'],
    'lang'            => $lang,
    'og_type'         => 'website',
    'schema_type'     => 'WebPage',
    'canonical_url'   => bb_lang_url('', $lang),
    'alternate_urls'  => [
        'az' => bb_lang_url('', 'az'),
        'en' => bb_lang_url('', 'en'),
        'ru' => bb_lang_url('', 'ru'),
    ],
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-home',
    'is_home'    => true,
]);
?>

    <!-- Placeholder bölmələr (FAZA 7-də dolacaq) -->
    <section class="bb-section bb-text-center">
        <div class="bb-container">
            <div class="bb-separator" style="margin: 1rem auto;"></div>
            <p class="bb-text-muted"><?= bb_sanitize($homeDesc[$lang] ?? $homeDesc['az']) ?></p>
        </div>
    </section>

<?php
bb_frontend_footer();
