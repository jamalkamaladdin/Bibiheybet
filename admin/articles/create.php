<?php
/**
 * Bibiheybet.com - Yeni Məqalə Yarat
 * 
 * 3 dil tab (AZ/EN/RU), TinyMCE, featured image, SEO panel.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();
$errors = [];

/** Kateqoriyalar (dropdown üçün) */
$categories = $db->query("SELECT id, name_az FROM categories ORDER BY sort_order ASC, name_az ASC")->fetchAll();

/** Form default dəyərləri */
$old = [
    'title_az' => '', 'title_en' => '', 'title_ru' => '',
    'slug_az' => '', 'slug_en' => '', 'slug_ru' => '',
    'content_az' => '', 'content_en' => '', 'content_ru' => '',
    'excerpt_az' => '', 'excerpt_en' => '', 'excerpt_ru' => '',
    'meta_title_az' => '', 'meta_title_en' => '', 'meta_title_ru' => '',
    'meta_desc_az' => '', 'meta_desc_en' => '', 'meta_desc_ru' => '',
    'category_id' => '', 'status' => 'draft', 'published_at' => '',
];

// === POST handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token etibarsızdır.';
    } else {
        // Sahələri yığ
        foreach ($old as $key => $val) {
            if ($key === 'category_id') {
                $old[$key] = (int)($_POST[$key] ?? 0) ?: null;
            } elseif ($key === 'status') {
                $old[$key] = ($_POST[$key] ?? 'draft') === 'published' ? 'published' : 'draft';
            } elseif (str_starts_with($key, 'content_')) {
                // HTML content — sanitize etmə
                $old[$key] = $_POST[$key] ?? '';
            } else {
                $old[$key] = trim($_POST[$key] ?? '');
            }
        }

        // Validasiya
        if ($old['title_az'] === '') {
            $errors[] = 'Azərbaycan başlığı məcburidir.';
        }
        if (trim(strip_tags($old['content_az'])) === '') {
            $errors[] = 'Azərbaycan məzmunu məcburidir.';
        }

        // Slug generate
        $old['slug_az'] = $old['slug_az'] ?: bb_generate_slug($old['title_az']);
        if ($old['title_en'] && !$old['slug_en']) $old['slug_en'] = bb_generate_slug($old['title_en'], 'en');
        if ($old['title_ru'] && !$old['slug_ru']) $old['slug_ru'] = bb_generate_slug($old['title_ru'], 'ru');

        // Slug unikallığı
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM articles WHERE slug_az = :s");
            $stmt->execute([':s' => $old['slug_az']]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errors[] = 'AZ slug artıq mövcuddur.';
            }
        }

        // Şəkil yükləmə
        $imagePaths = ['featured_image' => null, 'featured_image_en' => null, 'featured_image_ru' => null];
        $ogPaths = ['og_image_az' => null, 'og_image_en' => null, 'og_image_ru' => null];

        foreach ($imagePaths as $field => &$path) {
            if (!empty($_FILES[$field]['tmp_name'])) {
                $result = bb_upload_image($_FILES[$field], 'articles');
                if ($result['success']) {
                    $path = $result['filepath'];
                    // Media cədvəlinə yaz
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
                $result = bb_upload_image($_FILES[$field], 'articles');
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

        // Nəşr tarixi
        $publishedAt = null;
        if ($old['status'] === 'published') {
            $publishedAt = $old['published_at'] ?: date('Y-m-d H:i:s');
        }

        // DB insert
        if (empty($errors)) {
            $stmt = $db->prepare("
                INSERT INTO articles (
                    category_id, slug_az, slug_en, slug_ru,
                    title_az, title_en, title_ru,
                    content_az, content_en, content_ru,
                    excerpt_az, excerpt_en, excerpt_ru,
                    featured_image, featured_image_en, featured_image_ru,
                    meta_title_az, meta_title_en, meta_title_ru,
                    meta_desc_az, meta_desc_en, meta_desc_ru,
                    og_image_az, og_image_en, og_image_ru,
                    status, published_at
                ) VALUES (
                    :category_id, :slug_az, :slug_en, :slug_ru,
                    :title_az, :title_en, :title_ru,
                    :content_az, :content_en, :content_ru,
                    :excerpt_az, :excerpt_en, :excerpt_ru,
                    :featured_image, :featured_image_en, :featured_image_ru,
                    :meta_title_az, :meta_title_en, :meta_title_ru,
                    :meta_desc_az, :meta_desc_en, :meta_desc_ru,
                    :og_image_az, :og_image_en, :og_image_ru,
                    :status, :published_at
                )
            ");
            $stmt->execute([
                ':category_id'      => $old['category_id'] ?: null,
                ':slug_az'          => $old['slug_az'],
                ':slug_en'          => $old['slug_en'] ?: null,
                ':slug_ru'          => $old['slug_ru'] ?: null,
                ':title_az'         => $old['title_az'],
                ':title_en'         => $old['title_en'] ?: null,
                ':title_ru'         => $old['title_ru'] ?: null,
                ':content_az'       => $old['content_az'],
                ':content_en'       => $old['content_en'] ?: null,
                ':content_ru'       => $old['content_ru'] ?: null,
                ':excerpt_az'       => $old['excerpt_az'] ?: null,
                ':excerpt_en'       => $old['excerpt_en'] ?: null,
                ':excerpt_ru'       => $old['excerpt_ru'] ?: null,
                ':featured_image'   => $imagePaths['featured_image'],
                ':featured_image_en'=> $imagePaths['featured_image_en'],
                ':featured_image_ru'=> $imagePaths['featured_image_ru'],
                ':meta_title_az'    => $old['meta_title_az'] ?: null,
                ':meta_title_en'    => $old['meta_title_en'] ?: null,
                ':meta_title_ru'    => $old['meta_title_ru'] ?: null,
                ':meta_desc_az'     => $old['meta_desc_az'] ?: null,
                ':meta_desc_en'     => $old['meta_desc_en'] ?: null,
                ':meta_desc_ru'     => $old['meta_desc_ru'] ?: null,
                ':og_image_az'      => $ogPaths['og_image_az'],
                ':og_image_en'      => $ogPaths['og_image_en'],
                ':og_image_ru'      => $ogPaths['og_image_ru'],
                ':status'           => $old['status'],
                ':published_at'     => $publishedAt,
            ]);

            bb_flash('success', 'Məqalə uğurla yaradıldı.');
            bb_redirect('/admin/articles/');
        }
    }
}

bb_admin_header('Yeni Məqalə', [
    'extra_js' => [
        '/admin/assets/tinymce/tinymce.min.js',
        '/admin/assets/js/editor.js',
        '/admin/assets/js/media-upload.js',
    ],
]);

require __DIR__ . '/form.php';

bb_admin_footer();
