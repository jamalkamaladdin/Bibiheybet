<?php
/**
 * Bibiheybet.com - Tək Ziyarətgah
 * 
 * FAZA 9: Featured image, kontent, qalereya lightbox, paylaşma düymələri.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// Ziyarətgahı DB-dən çək
$pilgrimage = null;
$slugField = 'slug_' . $lang;
$stmt = $db->prepare("SELECT * FROM pilgrimages WHERE {$slugField} = :slug AND status = 'published' LIMIT 1");
$stmt->execute([':slug' => $pageSlug]);
$pilgrimage = $stmt->fetch();

// Slug tapılmadısa AZ fallback
if (!$pilgrimage && $lang !== 'az') {
    $stmt = $db->prepare("SELECT * FROM pilgrimages WHERE slug_az = :slug AND status = 'published' LIMIT 1");
    $stmt->execute([':slug' => $pageSlug]);
    $pilgrimage = $stmt->fetch();
}

if (!$pilgrimage) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

// Qalereya şəkillərini çək
$galleryStmt = $db->prepare(
    "SELECT * FROM pilgrimage_gallery WHERE pilgrimage_id = :pid ORDER BY sort_order ASC"
);
$galleryStmt->execute([':pid' => $pilgrimage['id']]);
$gallery = $galleryStmt->fetchAll();

// Alternate URL-lər
$GLOBALS['bb_page_context']['alternate_urls'] = bb_get_alternate_urls('pilgrimage', [
    'az' => $pilgrimage['slug_az'] ?? '',
    'en' => $pilgrimage['slug_en'] ?? '',
    'ru' => $pilgrimage['slug_ru'] ?? '',
]);

$seoData = bb_prepare_seo_data($pilgrimage, 'pilgrimage', $lang);
$seoData['canonical_url'] = bb_lang_url(
    bb_get_route('pilgrimage', $lang) . '/' . bb_get_field($pilgrimage, 'slug', $lang),
    $lang
);
$seoData['alternate_urls'] = $GLOBALS['bb_page_context']['alternate_urls'];

$pName    = bb_get_field($pilgrimage, 'name', $lang);
$pContent = bb_get_field($pilgrimage, 'content', $lang);
$pImage   = bb_get_featured_image($pilgrimage, $lang);

// Strings
$_strings = [
    'gallery_title' => [
        'az' => 'Qalereya',
        'en' => 'Gallery',
        'ru' => 'Галерея',
        'ar' => 'معرض الصور',
        'fa' => 'گالری',
    ],
    'share' => [
        'az' => 'Paylaş',
        'en' => 'Share',
        'ru' => 'Поделиться',
        'ar' => 'مشاركة',
        'fa' => 'اشتراک‌گذاری',
    ],
    'back' => [
        'az' => 'Bütün ziyarətgahlar',
        'en' => 'All pilgrimages',
        'ru' => 'Все святыни',
        'ar' => 'جميع المزارات',
        'fa' => 'همه زیارتگاه‌ها',
    ],
    'prev' => [
        'az' => 'Əvvəlki',
        'en' => 'Previous',
        'ru' => 'Предыдущее',
        'ar' => 'السابق',
        'fa' => 'قبلی',
    ],
    'next' => [
        'az' => 'Sonrakı',
        'en' => 'Next',
        'ru' => 'Следующее',
        'ar' => 'التالي',
        'fa' => 'بعدی',
    ],
    'close' => [
        'az' => 'Bağla',
        'en' => 'Close',
        'ru' => 'Закрыть',
        'ar' => 'إغلاق',
        'fa' => 'بستن',
    ],
];

$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

$shareUrl = $seoData['canonical_url'];
$shareTitle = urlencode($pName ?? '');

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-pilgrimage-single',
    'extra_css'  => ['/public/assets/css/pilgrimages.css'],
]);
?>

    <!-- Geri düyməsi -->
    <section class="bb-ps-back-section">
        <div class="bb-container">
            <a href="<?= bb_sanitize(bb_lang_url(bb_get_route('pilgrimages', $lang) . '/', $lang)) ?>"
               class="bb-ps-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
                <?= bb_sanitize($t('back')) ?>
            </a>
        </div>
    </section>

    <!-- Əsas kontent -->
    <section class="bb-ps-content-section">
        <div class="bb-container-narrow">
            <!-- Başlıq -->
            <h1 class="bb-ps-title"><?= bb_sanitize($pName) ?></h1>
            <div class="bb-separator"></div>

            <!-- Featured image -->
            <?php if ($pImage): ?>
            <div class="bb-ps-featured">
                <img src="/<?= bb_sanitize($pImage) ?>"
                     alt="<?= bb_sanitize($pName) ?>"
                     class="bb-ps-featured-img">
            </div>
            <?php endif; ?>

            <!-- Kontent -->
            <div class="bb-ps-body bb-article-content">
                <?= $pContent ?>
            </div>

            <!-- Paylaşma düymələri -->
            <div class="bb-ps-share">
                <span class="bb-ps-share-label"><?= bb_sanitize($t('share')) ?>:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>"
                   target="_blank" rel="noopener" class="bb-ps-share-btn bb-ps-share-fb"
                   aria-label="Facebook">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl) ?>&text=<?= $shareTitle ?>"
                   target="_blank" rel="noopener" class="bb-ps-share-btn bb-ps-share-tw"
                   aria-label="Twitter">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= urlencode($shareUrl) ?>"
                   target="_blank" rel="noopener" class="bb-ps-share-btn bb-ps-share-wa"
                   aria-label="WhatsApp">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                <a href="https://t.me/share/url?url=<?= urlencode($shareUrl) ?>&text=<?= $shareTitle ?>"
                   target="_blank" rel="noopener" class="bb-ps-share-btn bb-ps-share-tg"
                   aria-label="Telegram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Qalereya -->
    <?php if (!empty($gallery)): ?>
    <section class="bb-ps-gallery-section" data-animate>
        <div class="bb-container">
            <h2 class="bb-ps-gallery-title bb-text-center"><?= bb_sanitize($t('gallery_title')) ?></h2>
            <div class="bb-separator bb-separator-center"></div>

            <div class="bb-ps-gallery-grid">
                <?php foreach ($gallery as $i => $img): ?>
                    <?php
                        $caption = bb_get_field($img, 'caption', $lang) ?? '';
                    ?>
                <button type="button"
                        class="bb-ps-gallery-item"
                        data-lightbox-index="<?= $i ?>"
                        data-lightbox-src="/<?= bb_sanitize($img['image_path']) ?>"
                        data-lightbox-caption="<?= bb_sanitize($caption) ?>"
                        aria-label="<?= bb_sanitize($caption ?: ($pName . ' - ' . ($i + 1))) ?>">
                    <img src="/<?= bb_sanitize($img['image_path']) ?>"
                         alt="<?= bb_sanitize($caption ?: $pName) ?>"
                         loading="lazy">
                    <div class="bb-ps-gallery-overlay">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14zM12 10h-2v2H9v-2H7V9h2V7h1v2h2v1z"/></svg>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox modal -->
    <div class="bb-lightbox" id="bbLightbox" aria-hidden="true">
        <div class="bb-lightbox-overlay"></div>
        <div class="bb-lightbox-content">
            <button type="button" class="bb-lightbox-close" id="bbLightboxClose"
                    aria-label="<?= bb_sanitize($t('close')) ?>">&times;</button>
            <button type="button" class="bb-lightbox-nav bb-lightbox-prev" id="bbLightboxPrev"
                    aria-label="<?= bb_sanitize($t('prev')) ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </button>
            <button type="button" class="bb-lightbox-nav bb-lightbox-next" id="bbLightboxNext"
                    aria-label="<?= bb_sanitize($t('next')) ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </button>
            <img class="bb-lightbox-img" id="bbLightboxImg" src="" alt="">
            <div class="bb-lightbox-caption" id="bbLightboxCaption"></div>
            <div class="bb-lightbox-counter" id="bbLightboxCounter"></div>
        </div>
    </div>
    <?php endif; ?>

<?php
bb_frontend_footer(['extra_js' => ['/public/assets/js/pilgrimages.js', '/public/assets/js/home.js']]);
