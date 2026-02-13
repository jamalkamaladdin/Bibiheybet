<?php
/**
 * Bibiheybet.com - Məqalə Silmə
 * 
 * POST + CSRF qoruması ilə məqaləni və əlaqəli şəkilləri silir.
 */

require_once __DIR__ . '/../includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    bb_redirect('/admin/articles/');
}

if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
    bb_flash('error', 'CSRF token etibarsızdır.');
    bb_redirect('/admin/articles/');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    bb_flash('error', 'Məqalə tapılmadı.');
    bb_redirect('/admin/articles/');
}

$db = bb_get_db();

$stmt = $db->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    bb_flash('error', 'Məqalə tapılmadı.');
    bb_redirect('/admin/articles/');
}

// Şəkilləri diskdən sil
$imageFields = [
    'featured_image', 'featured_image_en', 'featured_image_ru',
    'og_image_az', 'og_image_en', 'og_image_ru',
];
foreach ($imageFields as $field) {
    if (!empty($article[$field])) {
        bb_delete_image($article[$field]);
    }
}

// DB-dən sil
$stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);

bb_flash('success', '«' . $article['title_az'] . '» məqaləsi silindi.');
bb_redirect('/admin/articles/');
