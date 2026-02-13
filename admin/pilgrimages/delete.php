<?php
/**
 * Bibiheybet.com - Ziyarətgah Silmə
 * 
 * POST + CSRF qoruması ilə ziyarətgahı, əlaqəli şəkilləri və qalereyanı silir.
 */

require_once __DIR__ . '/../includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    bb_redirect('/admin/pilgrimages/');
}

if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
    bb_flash('error', 'CSRF token etibarsızdır.');
    bb_redirect('/admin/pilgrimages/');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    bb_flash('error', 'Ziyarətgah tapılmadı.');
    bb_redirect('/admin/pilgrimages/');
}

$db = bb_get_db();

$stmt = $db->prepare("SELECT * FROM pilgrimages WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$pilgrimage = $stmt->fetch();

if (!$pilgrimage) {
    bb_flash('error', 'Ziyarətgah tapılmadı.');
    bb_redirect('/admin/pilgrimages/');
}

// Featured şəkilləri diskdən sil
$imageFields = [
    'featured_image', 'featured_image_en', 'featured_image_ru',
    'og_image_az', 'og_image_en', 'og_image_ru',
];
foreach ($imageFields as $field) {
    if (!empty($pilgrimage[$field])) {
        bb_delete_image($pilgrimage[$field]);
    }
}

// Qalereya şəkillərini diskdən sil
$gStmt = $db->prepare("SELECT image_path FROM pilgrimage_gallery WHERE pilgrimage_id = :pid");
$gStmt->execute([':pid' => $id]);
$galleryImages = $gStmt->fetchAll();
foreach ($galleryImages as $gi) {
    if (!empty($gi['image_path'])) {
        bb_delete_image($gi['image_path']);
    }
}

// DB-dən sil (qalereya CASCADE ilə silinir)
$stmt = $db->prepare("DELETE FROM pilgrimages WHERE id = :id");
$stmt->execute([':id' => $id]);

bb_flash('success', '«' . $pilgrimage['name_az'] . '» ziyarətgahı silindi.');
bb_redirect('/admin/pilgrimages/');
