<?php
/**
 * Bibiheybet.com - Məqalə Siyahısı
 * 
 * Cədvəl: başlıq, kateqoriya, status, tarix, əməliyyatlar.
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
    $where = 'WHERE a.status = :status';
    $params[':status'] = $statusFilter;
}

// Ümumi say
$countSql = "SELECT COUNT(*) FROM articles a {$where}";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalCount = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalCount / $perPage));

// Məqalələri yüklə
$sql = "
    SELECT a.id, a.title_az, a.status, a.published_at, a.updated_at,
           c.name_az AS category_name
    FROM articles a
    LEFT JOIN categories c ON c.id = a.category_id
    {$where}
    ORDER BY a.updated_at DESC
    LIMIT {$perPage} OFFSET {$offset}
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

bb_admin_header('Məqalələr');
?>

<div class="bb-page-header">
    <h2>Məqalələr (<?= $totalCount ?>)</h2>
    <a href="/admin/articles/create.php" class="bb-btn bb-btn-primary bb-btn-sm">+ Yeni məqalə</a>
</div>

<!-- Status filtr -->
<div class="bb-filter-bar">
    <a href="/admin/articles/" class="bb-filter-link<?= $statusFilter === '' ? ' active' : '' ?>">Hamısı</a>
    <a href="/admin/articles/?status=published" class="bb-filter-link<?= $statusFilter === 'published' ? ' active' : '' ?>">Nəşr olunmuş</a>
    <a href="/admin/articles/?status=draft" class="bb-filter-link<?= $statusFilter === 'draft' ? ' active' : '' ?>">Qaralama</a>
</div>

<?php if (empty($articles)): ?>
    <p class="bb-empty-text">Heç bir məqalə tapılmadı.</p>
<?php else: ?>
    <div class="bb-table-wrapper">
        <table class="bb-admin-table">
            <thead>
                <tr>
                    <th>Başlıq</th>
                    <th>Kateqoriya</th>
                    <th>Status</th>
                    <th>Tarix</th>
                    <th>Əməliyyatlar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td>
                            <a href="/admin/articles/edit.php?id=<?= (int)$article['id'] ?>">
                                <?= bb_sanitize($article['title_az']) ?>
                            </a>
                        </td>
                        <td class="bb-text-muted"><?= bb_sanitize($article['category_name'] ?? '—') ?></td>
                        <td>
                            <span class="bb-badge bb-badge-<?= $article['status'] === 'published' ? 'success' : 'warning' ?>">
                                <?= $article['status'] === 'published' ? 'Nəşr' : 'Qaralama' ?>
                            </span>
                        </td>
                        <td class="bb-text-muted"><?= bb_format_date($article['updated_at']) ?></td>
                        <td class="bb-actions">
                            <a href="/admin/articles/edit.php?id=<?= (int)$article['id'] ?>" class="bb-btn bb-btn-outline bb-btn-sm">Redaktə</a>
                            <button type="button" class="bb-btn bb-btn-danger bb-btn-sm"
                                onclick="bbConfirm('Məqaləni sil', '«<?= bb_sanitize($article['title_az']) ?>» silinəcək. Əmin misiniz?', function(){ document.getElementById('delete-form-<?= (int)$article['id'] ?>').submit(); })">
                                Sil
                            </button>
                            <form id="delete-form-<?= (int)$article['id'] ?>" action="/admin/articles/delete.php" method="POST" style="display:none">
                                <input type="hidden" name="id" value="<?= (int)$article['id'] ?>">
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
                    <a href="/admin/articles/?page=<?= $i ?><?= $qs ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
bb_admin_footer();
