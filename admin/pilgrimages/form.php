<?php
/**
 * Bibiheybet.com - Ziyarətgah Formu (Ortaq Template)
 * 
 * create.php və edit.php tərəfindən istifadə olunur.
 * Dəyişənlər: $old, $errors, $pilgrimage (edit üçün), $galleryImages (edit üçün).
 */

$isEdit = isset($pilgrimage);
$pageLabel = $isEdit ? 'Ziyarətgah Redaktə' : 'Yeni Ziyarətgah';
$submitLabel = $isEdit ? 'Yenilə' : 'Yadda saxla';
?>

<div class="bb-page-header">
    <h2><?= bb_sanitize($pageLabel) ?></h2>
    <a href="/admin/pilgrimages/" class="bb-btn bb-btn-outline bb-btn-sm">&larr; Geri</a>
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

<form method="POST" enctype="multipart/form-data" class="bb-pilgrimage-form">
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
                    <button type="button" class="bb-tab" data-tab="ar">العربية</button>
                    <button type="button" class="bb-tab" data-tab="fa">فارسی</button>
                </div>

                <?php foreach (['az', 'en', 'ru', 'ar', 'fa'] as $i => $lang): ?>
                    <div class="bb-tab-content<?= $i === 0 ? ' active' : '' ?>" data-tab-content="<?= $lang ?>">
                        <div class="bb-form-group">
                            <label for="name_<?= $lang ?>">Ad (<?= strtoupper($lang) ?>)<?= $lang === 'az' ? ' <span class="bb-required">*</span>' : '' ?></label>
                            <input type="text" id="name_<?= $lang ?>" name="name_<?= $lang ?>"
                                value="<?= bb_sanitize($old["name_{$lang}"]) ?>"
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
                                        placeholder="Boş qalsa ad istifadə olunur" maxlength="255">
                                </div>
                                <div class="bb-form-group">
                                    <label for="meta_desc_<?= $lang ?>">Meta Description</label>
                                    <textarea id="meta_desc_<?= $lang ?>" name="meta_desc_<?= $lang ?>"
                                        rows="2" placeholder="Boş qalsa avtomatik yaradılır" maxlength="320"><?= bb_sanitize($old["meta_desc_{$lang}"]) ?></textarea>
                                </div>
                                <div class="bb-form-group">
                                    <label>OG Image (<?= strtoupper($lang) ?>)</label>
                                    <div class="bb-image-upload" data-field="og_image_<?= $lang ?>">
                                        <input type="file" name="og_image_<?= $lang ?>" accept="image/*" class="bb-file-input">
                                        <?php
                                            $ogField = "og_image_{$lang}";
                                            $ogVal = $isEdit ? ($pilgrimage[$ogField] ?? '') : '';
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

            <!-- Qalereya (yalnız edit rejimində) -->
            <?php if ($isEdit): ?>
                <div class="bb-card">
                    <h3 class="bb-card-title">Qalereya</h3>

                    <div class="bb-gallery-upload-zone" id="bbGalleryDropZone">
                        <span class="bb-drop-text">Şəkilləri bura sürükləyin və ya kliklə seçin</span>
                        <input type="file" id="bbGalleryFileInput" accept="image/*" multiple style="display:none">
                    </div>

                    <div class="bb-gallery-grid" id="bbGalleryGrid"
                         data-pilgrimage-id="<?= (int)$pilgrimage['id'] ?>"
                         data-csrf="<?= bb_sanitize(bb_generate_csrf_token()) ?>">
                        <?php foreach ($galleryImages as $gi): ?>
                            <div class="bb-gallery-item" data-id="<?= (int)$gi['id'] ?>" draggable="true">
                                <div class="bb-gallery-img">
                                    <img src="/<?= bb_sanitize($gi['image_path']) ?>" alt="">
                                    <button type="button" class="bb-gallery-remove" data-id="<?= (int)$gi['id'] ?>" title="Sil">&times;</button>
                                    <span class="bb-gallery-drag-handle" title="Sürükləyib sıralayın">&#9776;</span>
                                </div>
                                <div class="bb-gallery-captions">
                                    <input type="text" class="bb-gallery-caption" data-id="<?= (int)$gi['id'] ?>" data-lang="az"
                                        value="<?= bb_sanitize($gi['caption_az'] ?? '') ?>" placeholder="Başlıq (AZ)">
                                    <input type="text" class="bb-gallery-caption" data-id="<?= (int)$gi['id'] ?>" data-lang="en"
                                        value="<?= bb_sanitize($gi['caption_en'] ?? '') ?>" placeholder="Caption (EN)">
                                    <input type="text" class="bb-gallery-caption" data-id="<?= (int)$gi['id'] ?>" data-lang="ru"
                                        value="<?= bb_sanitize($gi['caption_ru'] ?? '') ?>" placeholder="Подпись (RU)">
                                    <input type="text" class="bb-gallery-caption" data-id="<?= (int)$gi['id'] ?>" data-lang="ar"
                                        value="<?= bb_sanitize($gi['caption_ar'] ?? '') ?>" placeholder="التعليق (AR)">
                                    <input type="text" class="bb-gallery-caption" data-id="<?= (int)$gi['id'] ?>" data-lang="fa"
                                        value="<?= bb_sanitize($gi['caption_fa'] ?? '') ?>" placeholder="توضیح (FA)">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (empty($galleryImages)): ?>
                        <p class="bb-empty-text bb-gallery-empty" id="bbGalleryEmpty">Hələ heç bir qalereya şəkli yoxdur.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sağ tərəf: meta + şəkillər -->
        <div class="bb-form-sidebar">
            <!-- Status & Nəşr -->
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
                    <label for="sort_order">Sıralama</label>
                    <input type="number" id="sort_order" name="sort_order"
                        value="<?= (int)($old['sort_order'] ?? 0) ?>" min="0">
                    <span class="bb-form-hint">Kiçik rəqəm = daha əvvəl göstərilir</span>
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
                        <?php $fi = $isEdit ? ($pilgrimage['featured_image'] ?? '') : ''; ?>
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
                        <?php $fien = $isEdit ? ($pilgrimage['featured_image_en'] ?? '') : ''; ?>
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
                        <?php $firu = $isEdit ? ($pilgrimage['featured_image_ru'] ?? '') : ''; ?>
                        <?php if ($firu): ?>
                            <div class="bb-image-preview">
                                <img src="/<?= bb_sanitize($firu) ?>" alt="Featured RU">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bb-form-group">
                    <label>AR üçün fərqli foto</label>
                    <div class="bb-image-upload" data-field="featured_image_ar">
                        <input type="file" name="featured_image_ar" accept="image/*" class="bb-file-input">
                        <?php $fiar = $isEdit ? ($pilgrimage['featured_image_ar'] ?? '') : ''; ?>
                        <?php if ($fiar): ?>
                            <div class="bb-image-preview">
                                <img src="/<?= bb_sanitize($fiar) ?>" alt="Featured AR">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bb-form-group">
                    <label>FA üçün fərqli foto</label>
                    <div class="bb-image-upload" data-field="featured_image_fa">
                        <input type="file" name="featured_image_fa" accept="image/*" class="bb-file-input">
                        <?php $fifa = $isEdit ? ($pilgrimage['featured_image_fa'] ?? '') : ''; ?>
                        <?php if ($fifa): ?>
                            <div class="bb-image-preview">
                                <img src="/<?= bb_sanitize($fifa) ?>" alt="Featured FA">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

