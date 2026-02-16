<?php
/**
 * Bibiheybet.com - Yeni Ziyarətgah Yarat
 * 
 * 3 dil tab (AZ/EN/RU), TinyMCE, featured image, SEO panel.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();
$errors = [];

/** Form default dəyərləri */
$old = [
    'name_az' => '', 'name_en' => '', 'name_ru' => '', 'name_ar' => '', 'name_fa' => '',
    'slug_az' => '', 'slug_en' => '', 'slug_ru' => '', 'slug_ar' => '', 'slug_fa' => '',
    'content_az' => '', 'content_en' => '', 'content_ru' => '', 'content_ar' => '', 'content_fa' => '',
    'meta_title_az' => '', 'meta_title_en' => '', 'meta_title_ru' => '', 'meta_title_ar' => '', 'meta_title_fa' => '',
    'meta_desc_az' => '', 'meta_desc_en' => '', 'meta_desc_ru' => '', 'meta_desc_ar' => '', 'meta_desc_fa' => '',
    'sort_order' => 0, 'status' => 'draft',
];

// === POST handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token etibarsızdır.';
    } else {
        // Sahələri yığ
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

        // Validasiya
        if ($old['name_az'] === '') {
            $errors[] = 'Azərbaycan adı məcburidir.';
        }
        if (trim(strip_tags($old['content_az'])) === '') {
            $errors[] = 'Azərbaycan məzmunu məcburidir.';
        }

        // Slug generate
        $old['slug_az'] = $old['slug_az'] ?: bb_generate_slug($old['name_az']);
        if ($old['name_en'] && !$old['slug_en']) $old['slug_en'] = bb_generate_slug($old['name_en'], 'en');
        if ($old['name_ru'] && !$old['slug_ru']) $old['slug_ru'] = bb_generate_slug($old['name_ru'], 'ru');
        if ($old['name_ar'] && !$old['slug_ar']) $old['slug_ar'] = bb_generate_slug($old['name_ar'], 'ar');
        if ($old['name_fa'] && !$old['slug_fa']) $old['slug_fa'] = bb_generate_slug($old['name_fa'], 'fa');

        // Slug unikallığı
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM pilgrimages WHERE slug_az = :s");
            $stmt->execute([':s' => $old['slug_az']]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errors[] = 'AZ slug artıq mövcuddur.';
            }
        }

        // Şəkil yükləmə
        $imagePaths = ['featured_image' => null, 'featured_image_en' => null, 'featured_image_ru' => null, 'featured_image_ar' => null, 'featured_image_fa' => null];
        $ogPaths = ['og_image_az' => null, 'og_image_en' => null, 'og_image_ru' => null, 'og_image_ar' => null, 'og_image_fa' => null];

        foreach ($imagePaths as $field => &$path) {
            if (!empty($_FILES[$field]['tmp_name'])) {
                $result = bb_upload_image($_FILES[$field], 'pilgrimages');
                if ($result['success']) {
                    $path = $result['filepath'];
                    $mStmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
                    $mStmt->execute([':fn' => $result['filename'], ':fp' => $result['filepath'], ':ft' => $result['filetype'], ':fs' => $result['filesize']]);
                } else {
                    $errors[] = $field . ': ' . $result['error'];
                }
            }
        }
        unset($path);

        foreach ($ogPaths as $field => &$path) {
            if (!empty($_FILES[$field]['tmp_name'])) {
                $result = bb_upload_image($_FILES[$field], 'pilgrimages');
                if ($result['success']) {
                    $path = $result['filepath'];
                    $mStmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
                    $mStmt->execute([':fn' => $result['filename'], ':fp' => $result['filepath'], ':ft' => $result['filetype'], ':fs' => $result['filesize']]);
                } else {
                    $errors[] = $field . ': ' . $result['error'];
                }
            }
        }
        unset($path);

        // DB insert
        if (empty($errors)) {
            $stmt = $db->prepare("
                INSERT INTO pilgrimages (
                    slug_az, slug_en, slug_ru, slug_ar, slug_fa,
                    name_az, name_en, name_ru, name_ar, name_fa,
                    content_az, content_en, content_ru, content_ar, content_fa,
                    featured_image, featured_image_en, featured_image_ru, featured_image_ar, featured_image_fa,
                    meta_title_az, meta_title_en, meta_title_ru, meta_title_ar, meta_title_fa,
                    meta_desc_az, meta_desc_en, meta_desc_ru, meta_desc_ar, meta_desc_fa,
                    og_image_az, og_image_en, og_image_ru, og_image_ar, og_image_fa,
                    sort_order, status
                ) VALUES (
                    :slug_az, :slug_en, :slug_ru, :slug_ar, :slug_fa,
                    :name_az, :name_en, :name_ru, :name_ar, :name_fa,
                    :content_az, :content_en, :content_ru, :content_ar, :content_fa,
                    :featured_image, :featured_image_en, :featured_image_ru, :featured_image_ar, :featured_image_fa,
                    :meta_title_az, :meta_title_en, :meta_title_ru, :meta_title_ar, :meta_title_fa,
                    :meta_desc_az, :meta_desc_en, :meta_desc_ru, :meta_desc_ar, :meta_desc_fa,
                    :og_image_az, :og_image_en, :og_image_ru, :og_image_ar, :og_image_fa,
                    :sort_order, :status
                )
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
            ]);

            $newId = $db->lastInsertId();
            bb_flash('success', 'Ziyarətgah uğurla yaradıldı.');
            bb_redirect('/admin/pilgrimages/edit.php?id=' . $newId);
        }
    }
}

bb_admin_header('Yeni Ziyarətgah', [
    'extra_js' => [
        '/admin/assets/tinymce/tinymce.min.js',
        '/admin/assets/js/editor.js',
        '/admin/assets/js/media-upload.js',
    ],
]);

require __DIR__ . '/form.php';

bb_admin_footer();
