<?php
/**
 * Bibiheybet.com - Xanım haqqında Məqalə Silmə
 *
 * POST + CSRF qoruması ilə məqaləni, əlaqəli şəkilləri və qalereyanı silir.
 */

require_once __DIR__ . '/../includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    bb_redirect('/admin/xanim-haqqinda/');
}

if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
    bb_flash('error', 'CSRF token etibarsızdır.');
    bb_redirect('/admin/xanim-haqqinda/');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    bb_flash('error', 'Məqalə tapılmadı.');
    bb_redirect('/admin/xanim-haqqinda/');
}

$db = bb_get_db();

$stmt = $db->prepare("SELECT * FROM xanim_haqqinda WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$xanim = $stmt->fetch();

if (!$xanim) {
    bb_flash('error', 'Məqalə tapılmadı.');
    bb_redirect('/admin/xanim-haqqinda/');
}

// Featured şəkilləri diskdən sil
$imageFields = [
    'featured_image', 'featured_image_en', 'featured_image_ru',
    'og_image_az', 'og_image_en', 'og_image_ru',
];
foreach ($imageFields as $field) {
    if (!empty($xanim[$field])) {
        bb_delete_image($xanim[$field]);
    }
}

// Qalereya şəkillərini diskdən sil
$gStmt = $db->prepare("SELECT image_path FROM xanim_gallery WHERE xanim_id = :xid");
$gStmt->execute([':xid' => $id]);
$galleryImages = $gStmt->fetchAll();
foreach ($galleryImages as $gi) {
    if (!empty($gi['image_path'])) {
        bb_delete_image($gi['image_path']);
    }
}

// DB-dən sil (qalereya CASCADE ilə silinir)
$stmt = $db->prepare("DELETE FROM xanim_haqqinda WHERE id = :id");
$stmt->execute([':id' => $id]);

bb_flash('success', '«' . $xanim['name_az'] . '» məqaləsi silindi.');
bb_redirect('/admin/xanim-haqqinda/');
