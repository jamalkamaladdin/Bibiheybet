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

    // Audio faylını tap
    $audioFile = '/public/assets/audio/ziyaretname.mp3';
?>
    </main>

    <!-- Footer -->
    <footer class="bb-footer">
        <div class="bb-footer-inner bb-container">
            <div class="bb-footer-separator"></div>
            <p class="bb-footer-copyright">
                &copy; <?= $year ?> <?= bb_sanitize(SITE_NAME) ?>
            </p>
        </div>
    </footer>

    <!-- Audio Mini Player -->
    <div class="bb-player" id="bbPlayer" data-src="<?= bb_sanitize($audioFile) ?>">
        <div class="bb-player-inner">
            <!-- Play/Pause -->
            <button type="button" class="bb-player-btn bb-player-play" id="bbPlayerPlay" aria-label="Oxut / Dayandır">
                <svg class="bb-player-icon-play" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <svg class="bb-player-icon-pause" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="display:none">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                </svg>
            </button>

            <!-- Track info -->
            <div class="bb-player-info">
                <span class="bb-player-title">Ziyarətnamə</span>
                <div class="bb-player-progress" id="bbPlayerProgress">
                    <div class="bb-player-progress-bar" id="bbPlayerProgressBar"></div>
                </div>
            </div>

            <!-- Volume -->
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
    <script src="/public/assets/js/app.js"></script>
    <script src="/public/assets/js/header.js"></script>
    <script src="/public/assets/js/mini-player.js"></script>
    <?php if (!empty($options['extra_js'])): ?>
        <?php foreach ((array)$options['extra_js'] as $js): ?>
    <script src="<?= bb_sanitize($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
<?php
}
