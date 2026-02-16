<?php
/**
 * Bibiheybet.com - Kateqoriya Redaktə
 * 
 * Mövcud kateqoriyanı 3 dil tabı ilə redaktə edir.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();

// Kateqoriyanı yüklə
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    bb_flash('error', 'Kateqoriya tapılmadı.');
    bb_redirect('/admin/categories/');
}

$stmt = $db->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$category = $stmt->fetch();

if (!$category) {
    bb_flash('error', 'Kateqoriya tapılmadı.');
    bb_redirect('/admin/categories/');
}

$errors = [];
$old = [
    'name_az'    => $category['name_az'],
    'name_en'    => $category['name_en'] ?? '',
    'name_ru'    => $category['name_ru'] ?? '',
    'name_ar'    => $category['name_ar'] ?? '',
    'name_fa'    => $category['name_fa'] ?? '',
    'slug'       => $category['slug'],
    'sort_order' => (int)$category['sort_order'],
];

// === POST handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token etibarsızdır.';
    } else {
        $old['name_az']    = trim($_POST['name_az'] ?? '');
        $old['name_en']    = trim($_POST['name_en'] ?? '');
        $old['name_ru']    = trim($_POST['name_ru'] ?? '');
        $old['name_ar']    = trim($_POST['name_ar'] ?? '');
        $old['name_fa']    = trim($_POST['name_fa'] ?? '');
        $old['slug']       = trim($_POST['slug'] ?? '');
        $old['sort_order'] = (int)($_POST['sort_order'] ?? 0);

        if ($old['name_az'] === '') {
            $errors[] = 'Azərbaycan adı məcburidir.';
        }

        if ($old['slug'] === '') {
            $old['slug'] = bb_generate_slug($old['name_az']);
        } else {
            $old['slug'] = bb_generate_slug($old['slug']);
        }

        if ($old['slug'] === '') {
            $errors[] = 'Slug yaradıla bilmədi.';
        }

        // Slug unikallığı (öz ID-si xaric)
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE slug = :slug AND id != :id");
            $stmt->execute([':slug' => $old['slug'], ':id' => $id]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errors[] = 'Bu slug artıq mövcuddur.';
            }
        }

        if (empty($errors)) {
            $stmt = $db->prepare("
                UPDATE categories
                SET slug = :slug, name_az = :name_az, name_en = :name_en,
                    name_ru = :name_ru, name_ar = :name_ar, name_fa = :name_fa,
                    sort_order = :sort_order
                WHERE id = :id
            ");
            $stmt->execute([
                ':slug'       => $old['slug'],
                ':name_az'    => $old['name_az'],
                ':name_en'    => $old['name_en'] ?: null,
                ':name_ru'    => $old['name_ru'] ?: null,
                ':name_ar'    => $old['name_ar'] ?: null,
                ':name_fa'    => $old['name_fa'] ?: null,
                ':sort_order' => $old['sort_order'],
                ':id'         => $id,
            ]);

            bb_flash('success', 'Kateqoriya uğurla yeniləndi.');
            bb_redirect('/admin/categories/');
        }
    }
}

bb_admin_header('Kateqoriya Redaktə');
?>

<div class="bb-page-header">
    <h2>Kateqoriya Redaktə: <?= bb_sanitize($category['name_az']) ?></h2>
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

    <div class="bb-form-row">
        <div class="bb-form-group">
            <label for="slug">Slug</label>
            <div class="bb-slug-group">
                <input type="text" id="slug" name="slug" value="<?= bb_sanitize($old['slug']) ?>">
            </div>
        </div>
        <div class="bb-form-group">
            <label for="sort_order">Sıralama</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= (int)$old['sort_order'] ?>" min="0">
        </div>
    </div>

    <div class="bb-form-actions">
        <button type="submit" class="bb-btn bb-btn-primary">Yenilə</button>
        <a href="/admin/categories/" class="bb-btn bb-btn-outline">Ləğv et</a>
    </div>
</form>


<?php
bb_admin_footer();
