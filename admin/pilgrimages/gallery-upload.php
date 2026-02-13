<?php
/**
 * Bibiheybet.com - Qalereya Şəkil Yükləmə (AJAX)
 * 
 * POST ilə şəkil yükləyir və JSON cavab qaytarır.
 * Hər uğurlu cavabda yeni CSRF token qaytarılır.
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

$pilgrimageId = (int)($_POST['pilgrimage_id'] ?? 0);
if ($pilgrimageId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ziyarətgah ID yanlışdır.']);
    exit;
}

$db = bb_get_db();

// Ziyarətgah mövcud mu?
$stmt = $db->prepare("SELECT id FROM pilgrimages WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $pilgrimageId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Ziyarətgah tapılmadı.']);
    exit;
}

// Şəkil yüklənib?
if (empty($_FILES['image']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Şəkil seçilməyib.']);
    exit;
}

// Şəkli yüklə
$result = bb_upload_image($_FILES['image'], 'pilgrimages');
if (!$result['success']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $result['error']]);
    exit;
}

// Mövcud maksimum sort_order tapılsın
$sStmt = $db->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM pilgrimage_gallery WHERE pilgrimage_id = :pid");
$sStmt->execute([':pid' => $pilgrimageId]);
$nextSort = (int)$sStmt->fetchColumn();

// DB-yə yaz
$stmt = $db->prepare("
    INSERT INTO pilgrimage_gallery (pilgrimage_id, image_path, sort_order)
    VALUES (:pid, :path, :sort)
");
$stmt->execute([
    ':pid'  => $pilgrimageId,
    ':path' => $result['filepath'],
    ':sort' => $nextSort,
]);

$newId = (int)$db->lastInsertId();

// Media cədvəlinə yaz
$mStmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
$mStmt->execute([':fn' => $result['filename'], ':fp' => $result['filepath'], ':ft' => $result['filetype'], ':fs' => $result['filesize']]);

// Yeni CSRF token yarat
$newToken = bb_generate_csrf_token();

echo json_encode([
    'success'    => true,
    'id'         => $newId,
    'image_path' => $result['filepath'],
    'sort_order' => $nextSort,
    'csrf_token' => $newToken,
]);
