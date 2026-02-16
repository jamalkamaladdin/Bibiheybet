<?php
/**
 * Bibiheybet.com - Məqalə Formu (Ortaq Template)
 * 
 * create.php və edit.php tərəfindən istifadə olunur.
 * Dəyişənlər: $old, $errors, $categories, $article (edit üçün).
 */

$isEdit = isset($article);
$pageLabel = $isEdit ? 'Məqalə Redaktə' : 'Yeni Məqalə';
$submitLabel = $isEdit ? 'Yenilə' : 'Yadda saxla';
?>

<div class="bb-page-header">
    <h2><?= bb_sanitize($pageLabel) ?></h2>
    <a href="/admin/articles/" class="bb-btn bb-btn-outline bb-btn-sm">&larr; Geri</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="bb-alert bb-alert-error">
        <span class="bb-alert-message">
            <?php foreach ($errors as $e): ?>
                <?= bb_sanitize($e) ?><br>
            <?php endforeach; ?>
        </span>
        <button type="button" class="bb-alert-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="bb-article-form">
    <?= bb_generate_csrf() ?>

    <div class="bb-form-layout">
        <!-- Sol tərəf: əsas content -->
        <div class="bb-form-main">
            <!-- Dil tabları -->
            <div class="bb-card">
                <div class="bb-tabs">
                    <button type="button" class="bb-tab active" data-tab="az">Azərbaycan</button>
                    <button type="button" class="bb-tab" data-tab="en">English</button>
                    <button type="button" class="bb-tab" data-tab="ru">Русский</button>
                </div>

                <?php foreach (['az', 'en', 'ru'] as $i => $lang): ?>
                    <div class="bb-tab-content<?= $i === 0 ? ' active' : '' ?>" data-tab-content="<?= $lang ?>">
                        <div class="bb-form-group">
                            <label for="title_<?= $lang ?>">Başlıq (<?= strtoupper($lang) ?>)<?= $lang === 'az' ? ' <span class="bb-required">*</span>' : '' ?></label>
                            <input type="text" id="title_<?= $lang ?>" name="title_<?= $lang ?>"
                                value="<?= bb_sanitize($old["title_{$lang}"]) ?>"
                                <?= $lang === 'az' ? 'required' : '' ?>
                                data-slug-source="slug_<?= $lang ?>">
                        </div>

                        <div class="bb-form-group">
                            <label for="slug_<?= $lang ?>">Slug (<?= strtoupper($lang) ?>)</label>
                            <div class="bb-slug-group">
                                <input type="text" id="slug_<?= $lang ?>" name="slug_<?= $lang ?>"
                                    value="<?= bb_sanitize($old["slug_{$lang}"]) ?>"
                                    placeholder="Avtomatik yaradılacaq">
                            </div>
                        </div>

                        <div class="bb-form-group">
                            <label for="content_<?= $lang ?>">Məzmun (<?= strtoupper($lang) ?>)<?= $lang === 'az' ? ' <span class="bb-required">*</span>' : '' ?></label>
                            <textarea id="content_<?= $lang ?>" name="content_<?= $lang ?>"
                                class="bb-tinymce-editor" rows="15"><?= bb_sanitize($old["content_{$lang}"]) ?></textarea>
                        </div>

                        <div class="bb-form-group">
                            <label for="excerpt_<?= $lang ?>">Qısa mətn (<?= strtoupper($lang) ?>)</label>
                            <textarea id="excerpt_<?= $lang ?>" name="excerpt_<?= $lang ?>"
                                rows="3" placeholder="Məqalənin qısa xülasəsi..."><?= bb_sanitize($old["excerpt_{$lang}"]) ?></textarea>
                        </div>

                        <!-- SEO Panel -->
                        <div class="bb-seo-panel">
                            <button type="button" class="bb-seo-toggle" onclick="this.parentElement.classList.toggle('open')">
                                SEO Ayarları (<?= strtoupper($lang) ?>) <span class="bb-seo-arrow">&#9660;</span>
                            </button>
                            <div class="bb-seo-body">
                                <div class="bb-form-group">
                                    <label for="meta_title_<?= $lang ?>">Meta Title</label>
                                    <input type="text" id="meta_title_<?= $lang ?>" name="meta_title_<?= $lang ?>"
                                        value="<?= bb_sanitize($old["meta_title_{$lang}"]) ?>"
                                        placeholder="Boş qalsa başlıq istifadə olunur" maxlength="255">
                                </div>
                                <div class="bb-form-group">
                                    <label for="meta_desc_<?= $lang ?>">Meta Description</label>
                                    <textarea id="meta_desc_<?= $lang ?>" name="meta_desc_<?= $lang ?>"
                                        rows="2" placeholder="Boş qalsa excerpt istifadə olunur" maxlength="320"><?= bb_sanitize($old["meta_desc_{$lang}"]) ?></textarea>
                                </div>
                                <div class="bb-form-group">
                                    <label>OG Image (<?= strtoupper($lang) ?>)</label>
                                    <div class="bb-image-upload" data-field="og_image_<?= $lang ?>">
                                        <input type="file" name="og_image_<?= $lang ?>" accept="image/*" class="bb-file-input">
                                        <?php
                                            $ogField = "og_image_{$lang}";
                                            $ogVal = $isEdit ? ($article[$ogField] ?? '') : '';
                                        ?>
                                        <?php if ($ogVal): ?>
                                            <div class="bb-image-preview">
                                                <img src="/<?= bb_sanitize($ogVal) ?>" alt="OG">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sağ tərəf: meta + şəkillər -->
        <div class="bb-form-sidebar">
            <!-- Status & Publish -->
            <div class="bb-card">
                <h3 class="bb-card-title">Nəşr</h3>
                <div class="bb-form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="draft"<?= ($old['status'] ?? 'draft') === 'draft' ? ' selected' : '' ?>>Qaralama</option>
                        <option value="published"<?= ($old['status'] ?? '') === 'published' ? ' selected' : '' ?>>Nəşr olunmuş</option>
                    </select>
                </div>
                <div class="bb-form-group">
                    <label for="published_at">Nəşr tarixi</label>
                    <input type="datetime-local" id="published_at" name="published_at"
                        value="<?= bb_sanitize($old['published_at'] ?? '') ?>">
                </div>
                <div class="bb-form-group">
                    <label for="category_id">Kateqoriya</label>
                    <select id="category_id" name="category_id">
                        <option value="">— Kateqoriya seçin —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"<?= ((int)($old['category_id'] ?? 0)) === (int)$cat['id'] ? ' selected' : '' ?>>
                                <?= bb_sanitize($cat['name_az']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="bb-form-actions">
                    <button type="submit" class="bb-btn bb-btn-primary bb-btn-block"><?= $submitLabel ?></button>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="bb-card">
                <h3 class="bb-card-title">Əsas şəkil</h3>
                <div class="bb-form-group">
                    <div class="bb-image-upload" data-field="featured_image">
                        <input type="file" name="featured_image" accept="image/*" class="bb-file-input">
                        <?php $fi = $isEdit ? ($article['featured_image'] ?? '') : ''; ?>
                        <?php if ($fi): ?>
                            <div class="bb-image-preview">
                                <img src="/<?= bb_sanitize($fi) ?>" alt="Featured">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bb-form-group">
                    <label>EN üçün fərqli foto</label>
                    <div class="bb-image-upload" data-field="featured_image_en">
                        <input type="file" name="featured_image_en" accept="image/*" class="bb-file-input">
                        <?php $fien = $isEdit ? ($article['featured_image_en'] ?? '') : ''; ?>
                        <?php if ($fien): ?>
                            <div class="bb-image-preview">
                                <img src="/<?= bb_sanitize($fien) ?>" alt="Featured EN">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bb-form-group">
                    <label>RU üçün fərqli foto</label>
                    <div class="bb-image-upload" data-field="featured_image_ru">
                        <input type="file" name="featured_image_ru" accept="image/*" class="bb-file-input">
                        <?php $firu = $isEdit ? ($article['featured_image_ru'] ?? '') : ''; ?>
                        <?php if ($firu): ?>
                            <div class="bb-image-preview">
                                <img src="/<?= bb_sanitize($firu) ?>" alt="Featured RU">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    initTabs();
</script>
