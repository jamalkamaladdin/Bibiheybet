<?php
/**
 * Bibiheybet.com - 404 Xəta Səhifəsi
 * 
 * FAZA 10: Estetik 404 səhifəsi - ornament, animasiya, faydalı linklər.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$errorTitle = [
    'az' => 'Səhifə tapılmadı',
    'en' => 'Page not found',
    'ru' => 'Страница не найдена',
    'ar' => 'الصفحة غير موجودة',
    'fa' => 'صفحه یافت نشد',
];

$errorMessage = [
    'az' => 'Axtardığınız səhifə mövcud deyil və ya köçürülmüşdür.',
    'en' => 'The page you are looking for does not exist or has been moved.',
    'ru' => 'Страница, которую вы ищете, не существует или была перемещена.',
    'ar' => 'الصفحة التي تبحث عنها غير موجودة أو تم نقلها.',
    'fa' => 'صفحه‌ای که به دنبال آن هستید وجود ندارد یا منتقل شده است.',
];

$homeLabel = [
    'az' => 'Ana səhifəyə qayıt',
    'en' => 'Back to homepage',
    'ru' => 'Вернуться на главную',
    'ar' => 'العودة إلى الصفحة الرئيسية',
    'fa' => 'بازگشت به صفحه اصلی',
];

$_strings = [
    'useful_links' => [
        'az' => 'Faydalı linklər',
        'en' => 'Useful links',
        'ru' => 'Полезные ссылки',
        'ar' => 'روابط مفيدة',
        'fa' => 'لینک‌های مفید',
    ],
];

$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

$usefulLinks = [
    ['label' => $pageTitle ?? ($homeLabel[$lang] ?? $homeLabel['az']), 'url' => bb_lang_url('', $lang)],
    ['label' => bb_get_menu($lang)[1]['label'] ?? '', 'url' => bb_lang_url(bb_get_route('about-hazrat', $lang) . '/', $lang)],
    ['label' => bb_get_menu($lang)[4]['label'] ?? '', 'url' => bb_lang_url(bb_get_route('pilgrimages', $lang) . '/', $lang)],
    ['label' => bb_get_menu($lang)[5]['label'] ?? '', 'url' => bb_lang_url(bb_get_route('articles', $lang) . '/', $lang)],
];

$seoData = [
    'title'       => '404 - ' . ($errorTitle[$lang] ?? $errorTitle['az']),
    'lang'        => $lang,
    'og_type'     => 'website',
    'schema_type' => 'WebPage',
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-404',
    'extra_css'  => ['/public/assets/css/pages.css'],
]);
?>

    <section class="bb-404-section">
        <div class="bb-container bb-text-center">
            <!-- Ornament -->
            <div class="bb-404-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>

            <!-- 404 kodu -->
            <div class="bb-404-code">404</div>

            <!-- Başlıq -->
            <h1 class="bb-404-title"><?= bb_sanitize($errorTitle[$lang] ?? $errorTitle['az']) ?></h1>
            <div class="bb-separator bb-separator-center"></div>

            <!-- Mesaj -->
            <p class="bb-404-message"><?= bb_sanitize($errorMessage[$lang] ?? $errorMessage['az']) ?></p>

            <!-- Ana səhifə düyməsi -->
            <a href="<?= bb_lang_url('', $lang) ?>" class="bb-btn bb-btn-gold bb-404-home-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <?= bb_sanitize($homeLabel[$lang] ?? $homeLabel['az']) ?>
            </a>

            <!-- Faydalı linklər -->
            <div class="bb-404-links">
                <p class="bb-404-links-title"><?= bb_sanitize($t('useful_links')) ?></p>
                <div class="bb-404-links-grid">
                    <?php foreach ($usefulLinks as $link): ?>
                        <?php if (!empty($link['label'])): ?>
                        <a href="<?= bb_sanitize($link['url']) ?>" class="bb-404-link-item">
                            <?= bb_sanitize($link['label']) ?>
                        </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

<?php
bb_frontend_footer();
