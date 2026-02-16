<?php
/**
 * Bibiheybet.com - Ziyarətgah Redaktə
 * 
 * Mövcud ziyarətgahı 3 dil tabı ilə redaktə edir + qalereya idarəsi.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();

// Ziyarətgahı yüklə
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    bb_flash('error', 'Ziyarətgah tapılmadı.');
    bb_redirect('/admin/pilgrimages/');
}

$stmt = $db->prepare("SELECT * FROM pilgrimages WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$pilgrimage = $stmt->fetch();

if (!$pilgrimage) {
    bb_flash('error', 'Ziyarətgah tapılmadı.');
    bb_redirect('/admin/pilgrimages/');
}

// Qalereya şəkillərini yüklə
$gStmt = $db->prepare("SELECT * FROM pilgrimage_gallery WHERE pilgrimage_id = :pid ORDER BY sort_order ASC, id ASC");
$gStmt->execute([':pid' => $id]);
$galleryImages = $gStmt->fetchAll();

$errors = [];

/** Form dəyərləri - mövcud ziyarətgahdan doldurulur */
$old = [
    'name_az' => $pilgrimage['name_az'], 'name_en' => $pilgrimage['name_en'] ?? '',
    'name_ru' => $pilgrimage['name_ru'] ?? '', 'name_ar' => $pilgrimage['name_ar'] ?? '',
    'name_fa' => $pilgrimage['name_fa'] ?? '',
    'slug_az' => $pilgrimage['slug_az'], 'slug_en' => $pilgrimage['slug_en'] ?? '',
    'slug_ru' => $pilgrimage['slug_ru'] ?? '', 'slug_ar' => $pilgrimage['slug_ar'] ?? '',
    'slug_fa' => $pilgrimage['slug_fa'] ?? '',
    'content_az' => $pilgrimage['content_az'], 'content_en' => $pilgrimage['content_en'] ?? '',
    'content_ru' => $pilgrimage['content_ru'] ?? '', 'content_ar' => $pilgrimage['content_ar'] ?? '',
    'content_fa' => $pilgrimage['content_fa'] ?? '',
    'meta_title_az' => $pilgrimage['meta_title_az'] ?? '', 'meta_title_en' => $pilgrimage['meta_title_en'] ?? '',
    'meta_title_ru' => $pilgrimage['meta_title_ru'] ?? '', 'meta_title_ar' => $pilgrimage['meta_title_ar'] ?? '',
    'meta_title_fa' => $pilgrimage['meta_title_fa'] ?? '',
    'meta_desc_az' => $pilgrimage['meta_desc_az'] ?? '', 'meta_desc_en' => $pilgrimage['meta_desc_en'] ?? '',
    'meta_desc_ru' => $pilgrimage['meta_desc_ru'] ?? '', 'meta_desc_ar' => $pilgrimage['meta_desc_ar'] ?? '',
    'meta_desc_fa' => $pilgrimage['meta_desc_fa'] ?? '',
    'sort_order' => (int)$pilgrimage['sort_order'],
    'status' => $pilgrimage['status'],
];

// === POST handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token etibarsızdır.';
    } else {
        foreach ($old as $key => $val) {
            if ($key === 'sort_order') {
                $old[$key] = (int)($_POST[$key] ?? 0);
            } elseif ($key === 'status') {
                $old[$key] = ($_POST[$key] ?? 'draft') === 'published' ? 'published' : 'draft';
            } elseif (str_starts_with($key, 'content_')) {
                $old[$key] = $_POST[$key] ?? '';
            } else {
                $old[$key] = trim($_POST[$key] ?? '');
            }
        }

        if ($old['name_az'] === '') {
            $errors[] = 'Azərbaycan adı məcburidir.';
        }
        if (trim(strip_tags($old['content_az'])) === '') {
            $errors[] = 'Azərbaycan məzmunu məcburidir.';
        }

        $old['slug_az'] = $old['slug_az'] ?: bb_generate_slug($old['name_az']);
        if ($old['name_en'] && !$old['slug_en']) $old['slug_en'] = bb_generate_slug($old['name_en'], 'en');
        if ($old['name_ru'] && !$old['slug_ru']) $old['slug_ru'] = bb_generate_slug($old['name_ru'], 'ru');
        if ($old['name_ar'] && !$old['slug_ar']) $old['slug_ar'] = bb_generate_slug($old['name_ar'], 'ar');
        if ($old['name_fa'] && !$old['slug_fa']) $old['slug_fa'] = bb_generate_slug($old['name_fa'], 'fa');

        // Slug unikallığı (öz ID xaric)
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM pilgrimages WHERE slug_az = :s AND id != :id");
            $stmt->execute([':s' => $old['slug_az'], ':id' => $id]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errors[] = 'AZ slug artıq mövcuddur.';
            }
        }

        // Şəkil yükləmə (mövcud şəkil qorunur, yenisi yüklənsə köhnəsi silinir)
        $imageFields = ['featured_image', 'featured_image_en', 'featured_image_ru', 'featured_image_ar', 'featured_image_fa'];
        $imagePaths = [];
        foreach ($imageFields as $field) {
            $imagePaths[$field] = $pilgrimage[$field]; // default: mövcud
            if (!empty($_FILES[$field]['tmp_name'])) {
                $result = bb_upload_image($_FILES[$field], 'pilgrimages');
                if ($result['success']) {
                    if ($pilgrimage[$field]) bb_delete_image($pilgrimage[$field]);
                    $imagePaths[$field] = $result['filepath'];
                    $mStmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
                    $mStmt->execute([':fn' => $result['filename'], ':fp' => $result['filepath'], ':ft' => $result['filetype'], ':fs' => $result['filesize']]);
                } else {
                    $errors[] = $field . ': ' . $result['error'];
                }
            }
        }

        $ogFields = ['og_image_az', 'og_image_en', 'og_image_ru', 'og_image_ar', 'og_image_fa'];
        $ogPaths = [];
        foreach ($ogFields as $field) {
            $ogPaths[$field] = $pilgrimage[$field]; // default: mövcud
            if (!empty($_FILES[$field]['tmp_name'])) {
                $result = bb_upload_image($_FILES[$field], 'pilgrimages');
                if ($result['success']) {
                    if ($pilgrimage[$field]) bb_delete_image($pilgrimage[$field]);
                    $ogPaths[$field] = $result['filepath'];
                    $mStmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
                    $mStmt->execute([':fn' => $result['filename'], ':fp' => $result['filepath'], ':ft' => $result['filetype'], ':fs' => $result['filesize']]);
                } else {
                    $errors[] = $field . ': ' . $result['error'];
                }
            }
        }

        if (empty($errors)) {
            $stmt = $db->prepare("
                UPDATE pilgrimages SET
                    slug_az = :slug_az, slug_en = :slug_en, slug_ru = :slug_ru, slug_ar = :slug_ar, slug_fa = :slug_fa,
                    name_az = :name_az, name_en = :name_en, name_ru = :name_ru, name_ar = :name_ar, name_fa = :name_fa,
                    content_az = :content_az, content_en = :content_en, content_ru = :content_ru, content_ar = :content_ar, content_fa = :content_fa,
                    featured_image = :featured_image, featured_image_en = :featured_image_en, featured_image_ru = :featured_image_ru, featured_image_ar = :featured_image_ar, featured_image_fa = :featured_image_fa,
                    meta_title_az = :meta_title_az, meta_title_en = :meta_title_en, meta_title_ru = :meta_title_ru, meta_title_ar = :meta_title_ar, meta_title_fa = :meta_title_fa,
                    meta_desc_az = :meta_desc_az, meta_desc_en = :meta_desc_en, meta_desc_ru = :meta_desc_ru, meta_desc_ar = :meta_desc_ar, meta_desc_fa = :meta_desc_fa,
                    og_image_az = :og_image_az, og_image_en = :og_image_en, og_image_ru = :og_image_ru, og_image_ar = :og_image_ar, og_image_fa = :og_image_fa,
                    sort_order = :sort_order, status = :status
                WHERE id = :id
            ");
            $stmt->execute([
                ':slug_az'          => $old['slug_az'],
                ':slug_en'          => $old['slug_en'] ?: null,
                ':slug_ru'          => $old['slug_ru'] ?: null,
                ':slug_ar'          => $old['slug_ar'] ?: null,
                ':slug_fa'          => $old['slug_fa'] ?: null,
                ':name_az'          => $old['name_az'],
                ':name_en'          => $old['name_en'] ?: null,
                ':name_ru'          => $old['name_ru'] ?: null,
                ':name_ar'          => $old['name_ar'] ?: null,
                ':name_fa'          => $old['name_fa'] ?: null,
                ':content_az'       => $old['content_az'],
                ':content_en'       => $old['content_en'] ?: null,
                ':content_ru'       => $old['content_ru'] ?: null,
                ':content_ar'       => $old['content_ar'] ?: null,
                ':content_fa'       => $old['content_fa'] ?: null,
                ':featured_image'   => $imagePaths['featured_image'],
                ':featured_image_en'=> $imagePaths['featured_image_en'],
                ':featured_image_ru'=> $imagePaths['featured_image_ru'],
                ':featured_image_ar'=> $imagePaths['featured_image_ar'],
                ':featured_image_fa'=> $imagePaths['featured_image_fa'],
                ':meta_title_az'    => $old['meta_title_az'] ?: null,
                ':meta_title_en'    => $old['meta_title_en'] ?: null,
                ':meta_title_ru'    => $old['meta_title_ru'] ?: null,
                ':meta_title_ar'    => $old['meta_title_ar'] ?: null,
                ':meta_title_fa'    => $old['meta_title_fa'] ?: null,
                ':meta_desc_az'     => $old['meta_desc_az'] ?: null,
                ':meta_desc_en'     => $old['meta_desc_en'] ?: null,
                ':meta_desc_ru'     => $old['meta_desc_ru'] ?: null,
                ':meta_desc_ar'     => $old['meta_desc_ar'] ?: null,
                ':meta_desc_fa'     => $old['meta_desc_fa'] ?: null,
                ':og_image_az'      => $ogPaths['og_image_az'],
                ':og_image_en'      => $ogPaths['og_image_en'],
                ':og_image_ru'      => $ogPaths['og_image_ru'],
                ':og_image_ar'      => $ogPaths['og_image_ar'],
                ':og_image_fa'      => $ogPaths['og_image_fa'],
                ':sort_order'       => $old['sort_order'],
                ':status'           => $old['status'],
                ':id'               => $id,
            ]);

            bb_flash('success', 'Ziyarətgah uğurla yeniləndi.');
            bb_redirect('/admin/pilgrimages/edit.php?id=' . $id);
        }
    }
}

bb_admin_header('Ziyarətgah Redaktə', [
    'extra_js' => [
        '/admin/assets/tinymce/tinymce.min.js',
        '/admin/assets/js/editor.js',
        '/admin/assets/js/media-upload.js',
        '/admin/assets/js/gallery.js',
    ],
]);

require __DIR__ . '/form.php';

bb_admin_footer();
