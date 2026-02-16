<?php
/**
 * Bibiheybet.com - Yeni Kateqoriya Yarat
 * 
 * 3 dil tab (AZ/EN/RU), slug auto-generate, sort order.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();
$errors = [];
$old = [
    'name_az' => '', 'name_en' => '', 'name_ru' => '', 'name_ar' => '', 'name_fa' => '',
    'slug' => '', 'sort_order' => 0,
];

// === POST handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token etibarsızdır. Yenidən cəhd edin.';
    } else {
        $old['name_az']    = trim($_POST['name_az'] ?? '');
        $old['name_en']    = trim($_POST['name_en'] ?? '');
        $old['name_ru']    = trim($_POST['name_ru'] ?? '');
        $old['name_ar']    = trim($_POST['name_ar'] ?? '');
        $old['name_fa']    = trim($_POST['name_fa'] ?? '');
        $old['slug']       = trim($_POST['slug'] ?? '');
        $old['sort_order'] = (int)($_POST['sort_order'] ?? 0);

        // Validasiya
        if ($old['name_az'] === '') {
            $errors[] = 'Azərbaycan adı məcburidir.';
        }

        // Slug generate / yoxla
        if ($old['slug'] === '') {
            $old['slug'] = bb_generate_slug($old['name_az']);
        } else {
            $old['slug'] = bb_generate_slug($old['slug']);
        }

        if ($old['slug'] === '') {
            $errors[] = 'Slug yaradıla bilmədi. Ad sahəsini yoxlayın.';
        }

        // Slug unikallığı
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE slug = :slug");
            $stmt->execute([':slug' => $old['slug']]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errors[] = 'Bu slug artıq mövcuddur. Başqa slug seçin.';
            }
        }

        // DB insert
        if (empty($errors)) {
            $stmt = $db->prepare("
                INSERT INTO categories (slug, name_az, name_en, name_ru, name_ar, name_fa, sort_order)
                VALUES (:slug, :name_az, :name_en, :name_ru, :name_ar, :name_fa, :sort_order)
            ");
            $stmt->execute([
                ':slug'       => $old['slug'],
                ':name_az'    => $old['name_az'],
                ':name_en'    => $old['name_en'] ?: null,
                ':name_ru'    => $old['name_ru'] ?: null,
                ':name_ar'    => $old['name_ar'] ?: null,
                ':name_fa'    => $old['name_fa'] ?: null,
                ':sort_order' => $old['sort_order'],
            ]);

            bb_flash('success', 'Kateqoriya uğurla yaradıldı.');
            bb_redirect('/admin/categories/');
        }
    }
}

bb_admin_header('Yeni Kateqoriya');
?>

<div class="bb-page-header">
    <h2>Yeni Kateqoriya</h2>
    <a href="/admin/categories/" class="bb-btn bb-btn-outline bb-btn-sm">&larr; Geri</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="bb-alert bb-alert-error">
        <span class="bb-alert-message"><?= bb_sanitize(implode('<br>', $errors)) ?></span>
        <button type="button" class="bb-alert-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
<?php endif; ?>

<form method="POST" class="bb-card">
    <?= bb_generate_csrf() ?>

    <!-- Dil tabları -->
    <div class="bb-tabs">
        <button type="button" class="bb-tab active" data-tab="az">Azərbaycan</button>
        <button type="button" class="bb-tab" data-tab="en">English</button>
        <button type="button" class="bb-tab" data-tab="ru">Русский</button>
        <button type="button" class="bb-tab" data-tab="ar">العربية</button>
        <button type="button" class="bb-tab" data-tab="fa">فارسی</button>
    </div>

    <div class="bb-tab-content active" data-tab-content="az">
        <div class="bb-form-group">
            <label for="name_az">Ad (AZ) <span class="bb-required">*</span></label>
            <input type="text" id="name_az" name="name_az" value="<?= bb_sanitize($old['name_az']) ?>" required>
        </div>
    </div>

    <div class="bb-tab-content" data-tab-content="en">
        <div class="bb-form-group">
            <label for="name_en">Ad (EN)</label>
            <input type="text" id="name_en" name="name_en" value="<?= bb_sanitize($old['name_en']) ?>">
        </div>
    </div>

    <div class="bb-tab-content" data-tab-content="ru">
        <div class="bb-form-group">
            <label for="name_ru">Ad (RU)</label>
            <input type="text" id="name_ru" name="name_ru" value="<?= bb_sanitize($old['name_ru']) ?>">
        </div>
    </div>

    <div class="bb-tab-content" data-tab-content="ar">
        <div class="bb-form-group">
            <label for="name_ar">Ad (AR)</label>
            <input type="text" id="name_ar" name="name_ar" value="<?= bb_sanitize($old['name_ar']) ?>" dir="rtl">
        </div>
    </div>

    <div class="bb-tab-content" data-tab-content="fa">
        <div class="bb-form-group">
            <label for="name_fa">Ad (FA)</label>
            <input type="text" id="name_fa" name="name_fa" value="<?= bb_sanitize($old['name_fa']) ?>" dir="rtl">
        </div>
    </div>

    <!-- Ümumi sahələr -->
    <div class="bb-form-row">
        <div class="bb-form-group">
            <label for="slug">Slug</label>
            <div class="bb-slug-group">
                <input type="text" id="slug" name="slug" value="<?= bb_sanitize($old['slug']) ?>" placeholder="Avtomatik yaradılacaq">
            </div>
            <small class="bb-form-hint">Boş buraxsanız, AZ adından avtomatik yaradılacaq.</small>
        </div>
        <div class="bb-form-group">
            <label for="sort_order">Sıralama</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= (int)$old['sort_order'] ?>" min="0">
        </div>
    </div>

    <div class="bb-form-actions">
        <button type="submit" class="bb-btn bb-btn-primary">Yadda saxla</button>
        <a href="/admin/categories/" class="bb-btn bb-btn-outline">Ləğv et</a>
    </div>
</form>


<?php
bb_admin_footer();
