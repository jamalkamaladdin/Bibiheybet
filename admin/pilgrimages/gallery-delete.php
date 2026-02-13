<?php
/**
 * Bibiheybet.com - Qalereya Şəkil Silmə (AJAX)
 * 
 * POST ilə qalereya şəklini silir və JSON cavab qaytarır.
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
if ($imageId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Şəkil ID yanlışdır.']);
    exit;
}

$db = bb_get_db();

// Şəkli tap
$stmt = $db->prepare("SELECT * FROM pilgrimage_gallery WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $imageId]);
$image = $stmt->fetch();

if (!$image) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Şəkil tapılmadı.']);
    exit;
}

// Diskdən sil
if (!empty($image['image_path'])) {
    bb_delete_image($image['image_path']);
}

// DB-dən sil
$stmt = $db->prepare("DELETE FROM pilgrimage_gallery WHERE id = :id");
$stmt->execute([':id' => $imageId]);

// Yeni CSRF token yarat
$newToken = bb_generate_csrf_token();

echo json_encode([
    'success'    => true,
    'csrf_token' => $newToken,
]);
