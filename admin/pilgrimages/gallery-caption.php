<?php
/**
 * Bibiheybet.com - Qalereya Başlıq Yeniləmə (AJAX)
 * 
 * POST ilə qalereya şəklinin başlığını yeniləyir.
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

$imageId = (int)($_POST['image_id'] ?? 0);
$lang = $_POST['lang'] ?? '';
$caption = trim($_POST['caption'] ?? '');

if ($imageId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Şəkil ID yanlışdır.']);
    exit;
}

// Yalnız mövcud dillər icazə verilir
$allowedLangs = AVAILABLE_LANGS;
if (!in_array($lang, $allowedLangs)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Yanlış dil kodu.']);
    exit;
}

$db = bb_get_db();

// Şəkil mövcud mu?
$stmt = $db->prepare("SELECT id FROM pilgrimage_gallery WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $imageId]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Şəkil tapılmadı.']);
    exit;
}

// Başlığı yenilə
$column = "caption_{$lang}";
$stmt = $db->prepare("UPDATE pilgrimage_gallery SET {$column} = :caption WHERE id = :id");
$stmt->execute([
    ':caption' => $caption ?: null,
    ':id'      => $imageId,
]);

// Yeni CSRF token yarat
$newToken = bb_generate_csrf_token();

echo json_encode([
    'success'    => true,
    'csrf_token' => $newToken,
]);
