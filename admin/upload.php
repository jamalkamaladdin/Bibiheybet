<?php
/**
 * Bibiheybet.com - TinyMCE Şəkil Yükləmə Endpoint
 * 
 * TinyMCE editor-dan şəkil yüklənərkən bu endpoint çağırılır.
 * JSON cavab qaytarır: { location: "uploads/media/xxx.jpg" }
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Auth yoxla
if (!bb_is_logged_in()) {
    http_response_code(403);
    echo json_encode(['error' => 'Giriş tələb olunur.']);
    exit;
}

// POST yoxla
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST metodu tələb olunur.']);
    exit;
}

// Fayl yoxla
if (empty($_FILES['file']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Fayl tapılmadı.']);
    exit;
}

$result = bb_upload_image($_FILES['file'], 'media');

if (!$result['success']) {
    http_response_code(400);
    echo json_encode(['error' => $result['error']]);
    exit;
}

// Media cədvəlinə yaz
$db = bb_get_db();
$stmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
$stmt->execute([
    ':fn' => $result['filename'],
    ':fp' => $result['filepath'],
    ':ft' => $result['filetype'],
    ':fs' => $result['filesize'],
]);

// TinyMCE gözləyir: { location: "/uploads/media/xxx.jpg" }
echo json_encode(['location' => '/' . $result['filepath']]);
