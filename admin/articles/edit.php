<?php
/**
 * Bibiheybet.com - Məqalə Redaktə
 * 
 * Mövcud məqaləni 3 dil tabı ilə redaktə edir.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();

// Məqaləni yüklə
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    bb_flash('error', 'Məqalə tapılmadı.');
    bb_redirect('/admin/articles/');
}

$stmt = $db->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    bb_flash('error', 'Məqalə tapılmadı.');
    bb_redirect('/admin/articles/');
}

$errors = [];
$categories = $db->query("SELECT id, name_az FROM categories ORDER BY sort_order ASC, name_az ASC")->fetchAll();

/** Form dəyərləri - mövcud məqalədən doldurulur */
$old = [
    'title_az' => $article['title_az'], 'title_en' => $article['title_en'] ?? '',
    'title_ru' => $article['title_ru'] ?? '', 'title_ar' => $article['title_ar'] ?? '',
    'title_fa' => $article['title_fa'] ?? '',
    'slug_az' => $article['slug_az'], 'slug_en' => $article['slug_en'] ?? '',
    'slug_ru' => $article['slug_ru'] ?? '', 'slug_ar' => $article['slug_ar'] ?? '',
    'slug_fa' => $article['slug_fa'] ?? '',
    'content_az' => $article['content_az'], 'content_en' => $article['content_en'] ?? '',
    'content_ru' => $article['content_ru'] ?? '', 'content_ar' => $article['content_ar'] ?? '',
    'content_fa' => $article['content_fa'] ?? '',
    'excerpt_az' => $article['excerpt_az'] ?? '', 'excerpt_en' => $article['excerpt_en'] ?? '',
    'excerpt_ru' => $article['excerpt_ru'] ?? '', 'excerpt_ar' => $article['excerpt_ar'] ?? '',
    'excerpt_fa' => $article['excerpt_fa'] ?? '',
    'meta_title_az' => $article['meta_title_az'] ?? '', 'meta_title_en' => $article['meta_title_en'] ?? '',
    'meta_title_ru' => $article['meta_title_ru'] ?? '', 'meta_title_ar' => $article['meta_title_ar'] ?? '',
    'meta_title_fa' => $article['meta_title_fa'] ?? '',
    'meta_desc_az' => $article['meta_desc_az'] ?? '', 'meta_desc_en' => $article['meta_desc_en'] ?? '',
    'meta_desc_ru' => $article['meta_desc_ru'] ?? '', 'meta_desc_ar' => $article['meta_desc_ar'] ?? '',
    'meta_desc_fa' => $article['meta_desc_fa'] ?? '',
    'category_id' => $article['category_id'],
    'status' => $article['status'],
    'published_at' => $article['published_at'] ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : '',
];

// === POST handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token etibarsızdır.';
    } else {
        foreach ($old as $key => $val) {
            if ($key === 'category_id') {
                $old[$key] = (int)($_POST[$key] ?? 0) ?: null;
            } elseif ($key === 'status') {
                $old[$key] = ($_POST[$key] ?? 'draft') === 'published' ? 'published' : 'draft';
            } elseif (str_starts_with($key, 'content_')) {
                $old[$key] = $_POST[$key] ?? '';
            } else {
                $old[$key] = trim($_POST[$key] ?? '');
            }
        }

        if ($old['title_az'] === '') {
            $errors[] = 'Azərbaycan başlığı məcburidir.';
        }
        if (trim(strip_tags($old['content_az'])) === '') {
            $errors[] = 'Azərbaycan məzmunu məcburidir.';
        }

        $old['slug_az'] = $old['slug_az'] ?: bb_generate_slug($old['title_az']);
        if ($old['title_en'] && !$old['slug_en']) $old['slug_en'] = bb_generate_slug($old['title_en'], 'en');
        if ($old['title_ru'] && !$old['slug_ru']) $old['slug_ru'] = bb_generate_slug($old['title_ru'], 'ru');
        if ($old['title_ar'] && !$old['slug_ar']) $old['slug_ar'] = bb_generate_slug($old['title_ar'], 'ar');
        if ($old['title_fa'] && !$old['slug_fa']) $old['slug_fa'] = bb_generate_slug($old['title_fa'], 'fa');

        // Slug unikallığı (öz ID xaric)
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM articles WHERE slug_az = :s AND id != :id");
            $stmt->execute([':s' => $old['slug_az'], ':id' => $id]);
            if ((int)$stmt->fetchColumn() > 0) {
                $errors[] = 'AZ slug artıq mövcuddur.';
            }
        }

        // Şəkil yükləmə (mövcud şəkil qorunur, yenisi yüklənsə köhnəsi silinir)
        $imageFields = ['featured_image', 'featured_image_en', 'featured_image_ru', 'featured_image_ar', 'featured_image_fa'];
        $imagePaths = [];
        foreach ($imageFields as $field) {
            $imagePaths[$field] = $article[$field]; // default: mövcud
            if (!empty($_FILES[$field]['tmp_name'])) {
                $result = bb_upload_image($_FILES[$field], 'articles');
                if ($result['success']) {
                    // Köhnə şəkli sil
                    if ($article[$field]) bb_delete_image($article[$field]);
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
            $ogPaths[$field] = $article[$field]; // default: mövcud
            if (!empty($_FILES[$field]['tmp_name'])) {
                $result = bb_upload_image($_FILES[$field], 'articles');
                if ($result['success']) {
                    if ($article[$field]) bb_delete_image($article[$field]);
                    $ogPaths[$field] = $result['filepath'];
                    $mStmt = $db->prepare("INSERT INTO media (filename, filepath, filetype, filesize) VALUES (:fn, :fp, :ft, :fs)");
                    $mStmt->execute([':fn' => $result['filename'], ':fp' => $result['filepath'], ':ft' => $result['filetype'], ':fs' => $result['filesize']]);
                } else {
                    $errors[] = $field . ': ' . $result['error'];
                }
            }
        }

        $publishedAt = null;
        if ($old['status'] === 'published') {
            $publishedAt = $old['published_at'] ? date('Y-m-d H:i:s', strtotime($old['published_at'])) : ($article['published_at'] ?? date('Y-m-d H:i:s'));
        }

        if (empty($errors)) {
            $stmt = $db->prepare("
                UPDATE articles SET
                    category_id = :category_id,
                    slug_az = :slug_az, slug_en = :slug_en, slug_ru = :slug_ru, slug_ar = :slug_ar, slug_fa = :slug_fa,
                    title_az = :title_az, title_en = :title_en, title_ru = :title_ru, title_ar = :title_ar, title_fa = :title_fa,
                    content_az = :content_az, content_en = :content_en, content_ru = :content_ru, content_ar = :content_ar, content_fa = :content_fa,
                    excerpt_az = :excerpt_az, excerpt_en = :excerpt_en, excerpt_ru = :excerpt_ru, excerpt_ar = :excerpt_ar, excerpt_fa = :excerpt_fa,
                    featured_image = :featured_image, featured_image_en = :featured_image_en, featured_image_ru = :featured_image_ru, featured_image_ar = :featured_image_ar, featured_image_fa = :featured_image_fa,
                    meta_title_az = :meta_title_az, meta_title_en = :meta_title_en, meta_title_ru = :meta_title_ru, meta_title_ar = :meta_title_ar, meta_title_fa = :meta_title_fa,
                    meta_desc_az = :meta_desc_az, meta_desc_en = :meta_desc_en, meta_desc_ru = :meta_desc_ru, meta_desc_ar = :meta_desc_ar, meta_desc_fa = :meta_desc_fa,
                    og_image_az = :og_image_az, og_image_en = :og_image_en, og_image_ru = :og_image_ru, og_image_ar = :og_image_ar, og_image_fa = :og_image_fa,
                    status = :status, published_at = :published_at
                WHERE id = :id
            ");
            $stmt->execute([
                ':category_id'      => $old['category_id'] ?: null,
                ':slug_az'          => $old['slug_az'],
                ':slug_en'          => $old['slug_en'] ?: null,
                ':slug_ru'          => $old['slug_ru'] ?: null,
                ':slug_ar'          => $old['slug_ar'] ?: null,
                ':slug_fa'          => $old['slug_fa'] ?: null,
                ':title_az'         => $old['title_az'],
                ':title_en'         => $old['title_en'] ?: null,
                ':title_ru'         => $old['title_ru'] ?: null,
                ':title_ar'         => $old['title_ar'] ?: null,
                ':title_fa'         => $old['title_fa'] ?: null,
                ':content_az'       => $old['content_az'],
                ':content_en'       => $old['content_en'] ?: null,
                ':content_ru'       => $old['content_ru'] ?: null,
                ':content_ar'       => $old['content_ar'] ?: null,
                ':content_fa'       => $old['content_fa'] ?: null,
                ':excerpt_az'       => $old['excerpt_az'] ?: null,
                ':excerpt_en'       => $old['excerpt_en'] ?: null,
                ':excerpt_ru'       => $old['excerpt_ru'] ?: null,
                ':excerpt_ar'       => $old['excerpt_ar'] ?: null,
                ':excerpt_fa'       => $old['excerpt_fa'] ?: null,
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
                ':status'           => $old['status'],
                ':published_at'     => $publishedAt,
                ':id'               => $id,
            ]);

            bb_flash('success', 'Məqalə uğurla yeniləndi.');
            bb_redirect('/admin/articles/');
        }
    }
}

bb_admin_header('Məqalə Redaktə', [
    'extra_js' => [
        '/admin/assets/tinymce/tinymce.min.js',
        '/admin/assets/js/editor.js',
        '/admin/assets/js/media-upload.js',
    ],
]);

require __DIR__ . '/form.php';

bb_admin_footer();
