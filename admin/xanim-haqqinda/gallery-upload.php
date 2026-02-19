<?php
/**
 * Bibiheybet.com - Xanım haqqında Qalereya Şəkil Yükləmə (AJAX)
 */

require_once __DIR__ . '/../includes/layout.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Yalnız POST metod qəbul olunur.']);
    exit;
}

if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'CSRF token etibarsızdır.']);
    exit;
}

$xanimId = (int)($_POST['pilgrimage_id'] ?? 0);
if ($xanimId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID yanlışdır.']);
    exit;
}

$db = bb_get_db();

$stmt = $db->prepare("SELECT id FROM xanim_haqqinda WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $xanimId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Məqalə tapılmadı.']);
    exit;
}

if (empty($_FILES['image']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Şəkil seçilməyib.']);
    exit;
}

$result = bb_upload_image($_FILES['image'], 'xanim-haqqinda');
if (!$result['success']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $result['error']]);
    exit;
}

$sStmt = $db->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM xanim_gallery WHERE xanim_id = :xid");
$sStmt->execute([':xid' => $xanimId]);
$nextSort = (int)$sStmt->fetchColumn();

$stmt = $db->prepare("
    INSERT INTO xanim_gallery (xanim_id, image_path, sort_order)
    VALUES (:xid, :path, :sort)
");
$stmt->execute([
    ':xid'  => $xanimId,
    ':path' => $result['filepath'],
    ':sort' => $nextSort,
]);

$newId = (int)$db->lastInsertId();

$mStmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
$mStmt->execute([':fn' => $result['filename'], ':fp' => $result['filepath'], ':ft' => $result['filetype'], ':fs' => $result['filesize']]);

$newToken = bb_generate_csrf_token();

echo json_encode([
    'success'    => true,
    'id'         => $newId,
    'image_path' => $result['filepath'],
    'sort_order' => $nextSort,
    'csrf_token' => $newToken,
]);
