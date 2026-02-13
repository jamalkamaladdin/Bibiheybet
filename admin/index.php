<?php
/**
 * Bibiheybet.com - Admin Dashboard
 * 
 * Ümumi statistikalar və son fəaliyyət.
 */

require_once __DIR__ . '/includes/layout.php';

$db = bb_get_db();

// === Statistikalar ===

/** Məqalə sayları (ümumi, published, draft) */
$articleStats = $db->query("
    SELECT 
        COUNT(*) as total,
        SUM(status = 'published') as published,
        SUM(status = 'draft') as draft
    FROM articles
")->fetch();

/** Kateqoriya sayı */
$categoryCount = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();

/** Ziyarətgah sayları */
$pilgrimageStats = $db->query("
    SELECT 
        COUNT(*) as total,
        SUM(status = 'published') as published,
        SUM(status = 'draft') as draft
    FROM pilgrimages
")->fetch();

/** Media fayl sayı */
$mediaCount = (int)$db->query("SELECT COUNT(*) FROM media")->fetchColumn();

// === Son fəaliyyət ===

/** Son 5 məqalə */
$recentArticles = $db->query("
    SELECT id, title_az, status, updated_at
    FROM articles
    ORDER BY updated_at DESC
    LIMIT 5
")->fetchAll();

/** Son 5 ziyarətgah */
$recentPilgrimages = $db->query("
    SELECT id, name_az, status, updated_at
    FROM pilgrimages
    ORDER BY updated_at DESC
    LIMIT 5
")->fetchAll();

// === Render ===
bb_admin_header('Dashboard');
?>

<div class="bb-stats-grid">
    <div class="bb-stat-card">
        <div class="bb-stat-icon">&#x1F4DD;</div>
        <div class="bb-stat-info">
            <span class="bb-stat-number"><?= (int)$articleStats['total'] ?></span>
            <span class="bb-stat-label">Məqalə</span>
        </div>
        <div class="bb-stat-detail">
            <span class="bb-stat-published"><?= (int)$articleStats['published'] ?> nəşr</span>
            <span class="bb-stat-draft"><?= (int)$articleStats['draft'] ?> qaralama</span>
        </div>
    </div>

    <div class="bb-stat-card">
        <div class="bb-stat-icon">&#x1F4C1;</div>
        <div class="bb-stat-info">
            <span class="bb-stat-number"><?= $categoryCount ?></span>
            <span class="bb-stat-label">Kateqoriya</span>
        </div>
        <div class="bb-stat-detail">
            <span>&nbsp;</span>
        </div>
    </div>

    <div class="bb-stat-card">
        <div class="bb-stat-icon">&#x1F54C;</div>
        <div class="bb-stat-info">
            <span class="bb-stat-number"><?= (int)$pilgrimageStats['total'] ?></span>
            <span class="bb-stat-label">Ziyarətgah</span>
        </div>
        <div class="bb-stat-detail">
            <span class="bb-stat-published"><?= (int)$pilgrimageStats['published'] ?> nəşr</span>
            <span class="bb-stat-draft"><?= (int)$pilgrimageStats['draft'] ?> qaralama</span>
        </div>
    </div>

    <div class="bb-stat-card">
        <div class="bb-stat-icon">&#x1F4F7;</div>
        <div class="bb-stat-info">
            <span class="bb-stat-number"><?= $mediaCount ?></span>
            <span class="bb-stat-label">Media</span>
        </div>
        <div class="bb-stat-detail">
            <span>&nbsp;</span>
        </div>
    </div>
</div>

<div class="bb-dashboard-grid">
    <!-- Son məqalələr -->
    <div class="bb-dashboard-panel">
        <h2 class="bb-panel-title">Son Məqalələr</h2>
        <?php if (empty($recentArticles)): ?>
            <p class="bb-empty-text">Hələ heç bir məqalə əlavə edilməyib.</p>
        <?php else: ?>
            <table class="bb-admin-table">
                <thead>
                    <tr>
                        <th>Başlıq</th>
                        <th>Status</th>
                        <th>Tarix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentArticles as $article): ?>
                        <tr>
                            <td>
                                <a href="/admin/articles/edit.php?id=<?= (int)$article['id'] ?>">
                                    <?= bb_sanitize($article['title_az']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="bb-badge bb-badge-<?= $article['status'] === 'published' ? 'success' : 'warning' ?>">
                                    <?= $article['status'] === 'published' ? 'Nəşr' : 'Qaralama' ?>
                                </span>
                            </td>
                            <td class="bb-text-muted"><?= bb_format_date($article['updated_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Son ziyarətgahlar -->
    <div class="bb-dashboard-panel">
        <h2 class="bb-panel-title">Son Ziyarətgahlar</h2>
        <?php if (empty($recentPilgrimages)): ?>
            <p class="bb-empty-text">Hələ heç bir ziyarətgah əlavə edilməyib.</p>
        <?php else: ?>
            <table class="bb-admin-table">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Status</th>
                        <th>Tarix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPilgrimages as $pilgrimage): ?>
                        <tr>
                            <td>
                                <a href="/admin/pilgrimages/edit.php?id=<?= (int)$pilgrimage['id'] ?>">
                                    <?= bb_sanitize($pilgrimage['name_az']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="bb-badge bb-badge-<?= $pilgrimage['status'] === 'published' ? 'success' : 'warning' ?>">
                                    <?= $pilgrimage['status'] === 'published' ? 'Nəşr' : 'Qaralama' ?>
                                </span>
                            </td>
                            <td class="bb-text-muted"><?= bb_format_date($pilgrimage['updated_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
bb_admin_footer();
