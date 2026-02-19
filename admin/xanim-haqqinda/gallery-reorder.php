<?php
/**
 * Bibiheybet.com - Xanım haqqında Qalereya Sıralama (AJAX)
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

$orderJson = $_POST['order'] ?? '';
$order = json_decode($orderJson, true);

if (!is_array($order) || empty($order)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Sıralama məlumatı yanlışdır.']);
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

$updateStmt = $db->prepare("UPDATE xanim_gallery SET sort_order = :sort WHERE id = :id AND xanim_id = :xid");

foreach ($order as $index => $imageId) {
    $updateStmt->execute([
        ':sort' => $index,
        ':id'   => (int)$imageId,
        ':xid'  => $xanimId,
    ]);
}

$newToken = bb_generate_csrf_token();

echo json_encode([
    'success'    => true,
    'csrf_token' => $newToken,
]);
