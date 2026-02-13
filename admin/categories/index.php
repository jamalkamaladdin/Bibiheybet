<?php
/**
 * Bibiheybet.com - Kateqoriya Siyahısı
 * 
 * Bütün kateqoriyaları cədvəl şəklində göstərir.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();

/** Kateqoriyalar + hər kateqoriyaya aid məqalə sayı */
$categories = $db->query("
    SELECT c.*, COUNT(a.id) AS article_count
    FROM categories c
    LEFT JOIN articles a ON a.category_id = c.id
    GROUP BY c.id
    ORDER BY c.sort_order ASC, c.name_az ASC
")->fetchAll();

bb_admin_header('Kateqoriyalar');
?>

<div class="bb-page-header">
    <h2>Kateqoriyalar (<?= count($categories) ?>)</h2>
    <a href="/admin/categories/create.php" class="bb-btn bb-btn-primary bb-btn-sm">+ Yeni kateqoriya</a>
</div>

<?php if (empty($categories)): ?>
    <p class="bb-empty-text">Hələ heç bir kateqoriya əlavə edilməyib.</p>
<?php else: ?>
    <div class="bb-table-wrapper">
        <table class="bb-admin-table">
            <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Ad (AZ)</th>
                    <th>Ad (EN)</th>
                    <th>Ad (RU)</th>
                    <th>Slug</th>
                    <th>Məqalə</th>
                    <th>Əməliyyatlar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="bb-text-muted"><?= (int)$cat['sort_order'] ?></td>
                        <td><strong><?= bb_sanitize($cat['name_az']) ?></strong></td>
                        <td class="bb-text-muted"><?= bb_sanitize($cat['name_en'] ?? '—') ?></td>
                        <td class="bb-text-muted"><?= bb_sanitize($cat['name_ru'] ?? '—') ?></td>
                        <td class="bb-text-muted"><code><?= bb_sanitize($cat['slug']) ?></code></td>
                        <td><?= (int)$cat['article_count'] ?></td>
                        <td class="bb-actions">
                            <a href="/admin/categories/edit.php?id=<?= (int)$cat['id'] ?>" class="bb-btn bb-btn-outline bb-btn-sm">Redaktə</a>
                            <button type="button" class="bb-btn bb-btn-danger bb-btn-sm"
                                onclick="bbConfirm('Kateqoriyanı sil', '«<?= bb_sanitize($cat['name_az']) ?>» kateqoriyası silinəcək. Əmin misiniz?', function(){ document.getElementById('delete-form-<?= (int)$cat['id'] ?>').submit(); })">
                                Sil
                            </button>
                            <form id="delete-form-<?= (int)$cat['id'] ?>" action="/admin/categories/delete.php" method="POST" style="display:none">
                                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                <?= bb_generate_csrf() ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
bb_admin_footer();
