<?php
/**
 * Bibiheybet.com - Xanım haqqında Siyahısı
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
    $where = 'WHERE x.status = :status';
    $params[':status'] = $statusFilter;
}

// Ümumi say
$countSql = "SELECT COUNT(*) FROM xanim_haqqinda x {$where}";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalCount = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalCount / $perPage));

// Qeydləri yüklə
$sql = "
    SELECT x.id, x.name_az, x.status, x.sort_order, x.updated_at,
           (SELECT COUNT(*) FROM xanim_gallery g WHERE g.xanim_id = x.id) AS gallery_count
    FROM xanim_haqqinda x
    {$where}
    ORDER BY x.sort_order ASC, x.updated_at DESC
    LIMIT {$perPage} OFFSET {$offset}
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

bb_admin_header('Xanım haqqında');
?>

<div class="bb-page-header">
    <h2>Xanım haqqında (<?= $totalCount ?>)</h2>
    <a href="/admin/xanim-haqqinda/create.php" class="bb-btn bb-btn-primary bb-btn-sm">+ Yeni məqalə</a>
</div>

<!-- Status filtr -->
<div class="bb-filter-bar">
    <a href="/admin/xanim-haqqinda/" class="bb-filter-link<?= $statusFilter === '' ? ' active' : '' ?>">Hamısı</a>
    <a href="/admin/xanim-haqqinda/?status=published" class="bb-filter-link<?= $statusFilter === 'published' ? ' active' : '' ?>">Nəşr olunmuş</a>
    <a href="/admin/xanim-haqqinda/?status=draft" class="bb-filter-link<?= $statusFilter === 'draft' ? ' active' : '' ?>">Qaralama</a>
</div>

<?php if (empty($items)): ?>
    <p class="bb-empty-text">Heç bir məqalə tapılmadı.</p>
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
                <?php foreach ($items as $x): ?>
                    <tr>
                        <td>
                            <a href="/admin/xanim-haqqinda/edit.php?id=<?= (int)$x['id'] ?>">
                                <?= bb_sanitize($x['name_az']) ?>
                            </a>
                        </td>
                        <td class="bb-text-muted"><?= (int)$x['gallery_count'] ?> şəkil</td>
                        <td class="bb-text-muted"><?= (int)$x['sort_order'] ?></td>
                        <td>
                            <span class="bb-badge bb-badge-<?= $x['status'] === 'published' ? 'success' : 'warning' ?>">
                                <?= $x['status'] === 'published' ? 'Nəşr' : 'Qaralama' ?>
                            </span>
                        </td>
                        <td class="bb-text-muted"><?= bb_format_date($x['updated_at']) ?></td>
                        <td class="bb-actions">
                            <a href="/admin/xanim-haqqinda/edit.php?id=<?= (int)$x['id'] ?>" class="bb-btn bb-btn-outline bb-btn-sm">Redaktə</a>
                            <button type="button" class="bb-btn bb-btn-danger bb-btn-sm"
                                onclick="bbConfirm('Məqaləni sil', '«<?= bb_sanitize($x['name_az']) ?>» silinəcək. Əmin misiniz?', function(){ document.getElementById('delete-form-<?= (int)$x['id'] ?>').submit(); })">
                                Sil
                            </button>
                            <form id="delete-form-<?= (int)$x['id'] ?>" action="/admin/xanim-haqqinda/delete.php" method="POST" style="display:none">
                                <input type="hidden" name="id" value="<?= (int)$x['id'] ?>">
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
                    <a href="/admin/xanim-haqqinda/?page=<?= $i ?><?= $qs ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
bb_admin_footer();
