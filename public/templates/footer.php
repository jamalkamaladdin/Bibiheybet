<?php
/**
 * Bibiheybet.com - Frontend Footer
 * 
 * Footer: copyright + audio mini-player.
 */

/**
 * Frontend səhifənin HTML sonunu render edir.
 * 
 * @param array $options
 *   - extra_js (array) Əlavə JS faylları
 */
function bb_frontend_footer(array $options = []): void
{
    global $lang;
    $year = date('Y');

    $audioFile = '/public/assets/audio/ziyaretname.mp3';

    $rights = [
        'az' => 'Bütün hüquqlar qorunur.',
        'en' => 'All rights reserved.',
        'ru' => 'Все права защищены.',
        'ar' => 'جميع الحقوق محفوظة.',
        'fa' => 'تمامی حقوق محفوظ است.',
    ];
    $rightsText = $rights[$lang] ?? $rights['az'];

    $menuItems = bb_get_menu($lang);
?>
    </main>

    <!-- Footer -->
    <footer class="bb-footer">
        <div class="bb-footer-inner bb-container">
            <!-- Logo -->
            <a href="<?= bb_lang_url('', $lang) ?>" class="bb-footer-logo" aria-label="<?= bb_sanitize(SITE_NAME) ?>">
                <img src="/public/assets/img/logo.png" alt="<?= bb_sanitize(SITE_NAME) ?>">
            </a>

            <!-- Nav (üfüqi) -->
            <nav class="bb-footer-nav" aria-label="Footer naviqasiya">
                <?php foreach ($menuItems as $i => $item): ?>
                    <?php
                        $menuUrl = empty($item['route'])
                            ? bb_lang_url('', $lang)
                            : bb_lang_url(bb_get_route($item['route'], $lang) . '/', $lang);
                    ?><?php if ($i > 0): ?><span class="bb-footer-nav-dot" aria-hidden="true"></span><?php endif; ?><a href="<?= bb_sanitize($menuUrl) ?>"><?= bb_sanitize($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>

            <!-- Sosial -->
            <div class="bb-footer-social">
                <a href="https://www.instagram.com/bibiheybetziyaretgahi/" target="_blank" rel="noopener" aria-label="Instagram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="https://www.facebook.com/bibiheybetmecidi/" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
            </div>

            <!-- Separator + Copyright -->
            <div class="bb-footer-line"></div>
            <p class="bb-footer-copy">&copy; <?= $year ?> <?= bb_sanitize(SITE_NAME) ?>. <?= bb_sanitize($rightsText) ?></p>
        </div>
    </footer>

    <!-- Audio Mini Player -->
    <div class="bb-player" id="bbPlayer" data-src="<?= bb_sanitize($audioFile) ?>" data-is-home="<?= !empty($options['is_home']) ? '1' : '0' ?>">
        <div class="bb-player-inner">
            <button type="button" class="bb-player-btn bb-player-play" id="bbPlayerPlay" aria-label="Oxut / Dayandır">
                <svg class="bb-player-icon-play" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <svg class="bb-player-icon-pause" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="display:none">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                </svg>
            </button>
            <div class="bb-player-info">
                <span class="bb-player-title">Ziyarətnamə</span>
                <div class="bb-player-progress" id="bbPlayerProgress">
                    <div class="bb-player-progress-bar" id="bbPlayerProgressBar"></div>
                </div>
            </div>
            <div class="bb-player-volume-wrap">
                <button type="button" class="bb-player-btn bb-player-vol-btn" id="bbPlayerVolBtn" aria-label="Səs">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                    </svg>
                </button>
                <input type="range" class="bb-player-volume" id="bbPlayerVolume"
                       min="0" max="100" value="8" aria-label="Səs səviyyəsi">
            </div>
        </div>
    </div>

    <!-- JS -->
    <?php $jsVer = '?v=' . time(); ?>
    <script src="/public/assets/js/app.js<?= $jsVer ?>"></script>
    <script src="/public/assets/js/header.js<?= $jsVer ?>"></script>
    <script src="/public/assets/js/mini-player.js<?= $jsVer ?>"></script>
    <?php if (!empty($options['extra_js'])): ?>
        <?php foreach ((array)$options['extra_js'] as $js): ?>
            <?php if (str_starts_with($js, 'http')): ?>
    <script src="<?= bb_sanitize($js) ?>"></script>
            <?php else: ?>
    <script src="<?= bb_sanitize($js) ?><?= $jsVer ?>"></script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
<?php
}
