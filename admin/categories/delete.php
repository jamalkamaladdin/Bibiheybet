<?php
/**
 * Bibiheybet.com - Kateqoriya Silmə
 * 
 * POST + CSRF qoruması ilə kateqoriyanı silir.
 * Əlaqəli məqalələrin category_id-si NULL olur (FK ON DELETE SET NULL).
 */

require_once __DIR__ . '/../includes/layout.php';

// Yalnız POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    bb_redirect('/admin/categories/');
}

if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
    bb_flash('error', 'CSRF token etibarsızdır.');
    bb_redirect('/admin/categories/');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    bb_flash('error', 'Kateqoriya tapılmadı.');
    bb_redirect('/admin/categories/');
}

$db = bb_get_db();

// Mövcudluğu yoxla
$stmt = $db->prepare("SELECT name_az FROM categories WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$category = $stmt->fetch();

if (!$category) {
    bb_flash('error', 'Kateqoriya tapılmadı.');
    bb_redirect('/admin/categories/');
}

// Sil (FK ON DELETE SET NULL — məqalələrin category_id-si avtomatik NULL olur)
$stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
$stmt->execute([':id' => $id]);

bb_flash('success', '«' . $category['name_az'] . '» kateqoriyası silindi.');
bb_redirect('/admin/categories/');
