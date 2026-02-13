<?php
/**
 * Bibiheybet.com - Ziyarətgah Siyahısı
 * 
 * Cədvəl: ad, status, sıralama, tarix, əməliyyatlar.
 * Pagination + status filtri.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();

// === Filtr & Pagination parametrləri ===
$statusFilter = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

// WHERE şərti
$where = '';
$params = [];
if ($statusFilter === 'published' || $statusFilter === 'draft') {
    $where = 'WHERE p.status = :status';
    $params[':status'] = $statusFilter;
}

// Ümumi say
$countSql = "SELECT COUNT(*) FROM pilgrimages p {$where}";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalCount = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalCount / $perPage));

// Ziyarətgahları yüklə
$sql = "
    SELECT p.id, p.name_az, p.status, p.sort_order, p.updated_at,
           (SELECT COUNT(*) FROM pilgrimage_gallery g WHERE g.pilgrimage_id = p.id) AS gallery_count
    FROM pilgrimages p
    {$where}
    ORDER BY p.sort_order ASC, p.updated_at DESC
    LIMIT {$perPage} OFFSET {$offset}
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$pilgrimages = $stmt->fetchAll();

bb_admin_header('Ziyarətgahlar');
?>

<div class="bb-page-header">
    <h2>Ziyarətgahlar (<?= $totalCount ?>)</h2>
    <a href="/admin/pilgrimages/create.php" class="bb-btn bb-btn-primary bb-btn-sm">+ Yeni ziyarətgah</a>
</div>

<!-- Status filtr -->
<div class="bb-filter-bar">
    <a href="/admin/pilgrimages/" class="bb-filter-link<?= $statusFilter === '' ? ' active' : '' ?>">Hamısı</a>
    <a href="/admin/pilgrimages/?status=published" class="bb-filter-link<?= $statusFilter === 'published' ? ' active' : '' ?>">Nəşr olunmuş</a>
    <a href="/admin/pilgrimages/?status=draft" class="bb-filter-link<?= $statusFilter === 'draft' ? ' active' : '' ?>">Qaralama</a>
</div>

<?php if (empty($pilgrimages)): ?>
    <p class="bb-empty-text">Heç bir ziyarətgah tapılmadı.</p>
<?php else: ?>
    <div class="bb-table-wrapper">
        <table class="bb-admin-table">
            <thead>
                <tr>
                    <th>Ad</th>
                    <th>Qalereya</th>
                    <th>Sıra</th>
                    <th>Status</th>
                    <th>Tarix</th>
                    <th>Əməliyyatlar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pilgrimages as $p): ?>
                    <tr>
                        <td>
                            <a href="/admin/pilgrimages/edit.php?id=<?= (int)$p['id'] ?>">
                                <?= bb_sanitize($p['name_az']) ?>
                            </a>
                        </td>
                        <td class="bb-text-muted"><?= (int)$p['gallery_count'] ?> şəkil</td>
                        <td class="bb-text-muted"><?= (int)$p['sort_order'] ?></td>
                        <td>
                            <span class="bb-badge bb-badge-<?= $p['status'] === 'published' ? 'success' : 'warning' ?>">
                                <?= $p['status'] === 'published' ? 'Nəşr' : 'Qaralama' ?>
                            </span>
                        </td>
                        <td class="bb-text-muted"><?= bb_format_date($p['updated_at']) ?></td>
                        <td class="bb-actions">
                            <a href="/admin/pilgrimages/edit.php?id=<?= (int)$p['id'] ?>" class="bb-btn bb-btn-outline bb-btn-sm">Redaktə</a>
                            <button type="button" class="bb-btn bb-btn-danger bb-btn-sm"
                                onclick="bbConfirm('Ziyarətgahı sil', '«<?= bb_sanitize($p['name_az']) ?>» silinəcək. Əmin misiniz?', function(){ document.getElementById('delete-form-<?= (int)$p['id'] ?>').submit(); })">
                                Sil
                            </button>
                            <form id="delete-form-<?= (int)$p['id'] ?>" action="/admin/pilgrimages/delete.php" method="POST" style="display:none">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <?= bb_generate_csrf() ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="bb-pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php
                    $qs = $statusFilter ? "&status={$statusFilter}" : '';
                ?>
                <?php if ($i === $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="/admin/pilgrimages/?page=<?= $i ?><?= $qs ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
bb_admin_footer();
