<?php
/**
 * Bibiheybet.com - 404 Xəta Səhifəsi
 * 
 * Tapılmayan URL-lər üçün estetik xəta səhifəsi.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$errorTitle = [
    'az' => 'Səhifə tapılmadı',
    'en' => 'Page not found',
    'ru' => 'Страница не найдена',
];

$errorMessage = [
    'az' => 'Axtardığınız səhifə mövcud deyil və ya köçürülmüşdür.',
    'en' => 'The page you are looking for does not exist or has been moved.',
    'ru' => 'Страница, которую вы ищете, не существует или была перемещена.',
];

$homeLabel = [
    'az' => 'Ana səhifəyə qayıt',
    'en' => 'Back to homepage',
    'ru' => 'Вернуться на главную',
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
]);
?>

    <section class="bb-section bb-404-section">
        <div class="bb-container bb-text-center">
            <div class="bb-404-code">404</div>
            <h1><?= bb_sanitize($errorTitle[$lang] ?? $errorTitle['az']) ?></h1>
            <div class="bb-separator" style="margin: 1rem auto;"></div>
            <p class="bb-text-muted"><?= bb_sanitize($errorMessage[$lang] ?? $errorMessage['az']) ?></p>
            <a href="<?= bb_lang_url('', $lang) ?>" class="bb-btn bb-btn-primary" style="margin-top: 2rem;">
                <?= bb_sanitize($homeLabel[$lang] ?? $homeLabel['az']) ?>
            </a>
        </div>
    </section>

<?php
bb_frontend_footer();
