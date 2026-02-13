<?php
/**
 * Bibiheybet.com - Frontend Footer
 * 
 * Sayt footer skeleti: copyright, separator.
 * Hər template bu faylı include edib bb_frontend_footer() çağırır.
 */

/**
 * Frontend səhifənin HTML sonunu render edir.
 * 
 * @param array $options Seçimlər:
 *   - extra_js (array) Əlavə JS faylları
 */
function bb_frontend_footer(array $options = []): void
{
    global $lang;
    $year = date('Y');
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

    <!-- JS -->
    <script src="/public/assets/js/app.js"></script>
    <?php if (!empty($options['extra_js'])): ?>
        <?php foreach ((array)$options['extra_js'] as $js): ?>
    <script src="<?= bb_sanitize($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
<?php
}
