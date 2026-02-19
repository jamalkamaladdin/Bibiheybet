/**
 * Bibiheybet.com - Qalereya İdarəetmə (FAZA 4)
 * 
 * Ziyarətgah redaktə səhifəsində qalereya funksionallığı:
 *  - Şəkil yükləmə (drag & drop + klik)
 *  - Şəkil silmə (AJAX)
 *  - Drag & drop sıralama
 *  - Başlıq (caption) redaktə (AZ/EN/RU)
 *  - Hər AJAX cavabında CSRF token yenilənir
 */

var bbGallery = {
    grid: null,
    dropZone: null,
    fileInput: null,
    csrfToken: '',
    pilgrimageId: 0,
    dragSrcEl: null,

    /**
     * Qalereyanı inisializasiya edir
     */
    init: function () {
        this.grid = document.getElementById('bbGalleryGrid');
        this.dropZone = document.getElementById('bbGalleryDropZone');
        this.fileInput = document.getElementById('bbGalleryFileInput');

        if (!this.grid || !this.dropZone || !this.fileInput) return;

        this.pilgrimageId = parseInt(this.grid.dataset.pilgrimageId) || 0;
        this.csrfToken = this.grid.dataset.csrf || '';
        this.galleryBase = this.grid.dataset.galleryBase || '/admin/pilgrimages/';

        if (!this.pilgrimageId) return;

        this.bindUploadEvents();
        this.bindDeleteEvents();
        this.bindDragEvents();
        this.bindCaptionEvents();
    },

    /* ============================================
       YÜKLƏMƏ
       ============================================ */
    bindUploadEvents: function () {
        var self = this;

        // Drop zone klik
        this.dropZone.addEventListener('click', function () {
            self.fileInput.click();
        });

        // File seçildi
        this.fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                self.uploadFiles(this.files);
                this.value = '';
            }
        });

        // Drag & drop zona
        this.dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('bb-drop-active');
        });

        this.dropZone.addEventListener('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('bb-drop-active');
        });

        this.dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('bb-drop-active');

            var files = e.dataTransfer.files;
            if (files && files.length > 0) {
                self.uploadFiles(files);
            }
        });
    },

    /**
     * Çoxlu fayl yükləyir (ardıcıl)
     */
    uploadFiles: function (files) {
        var self = this;
        var queue = Array.from(files);

        function processNext() {
            if (queue.length === 0) return;
            var file = queue.shift();

            // Tip yoxla
            var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (allowedTypes.indexOf(file.type) === -1) {
                alert('İcazə verilməyən fayl tipi: ' + file.name + '. Yalnız JPG, PNG, GIF, WEBP.');
                processNext();
                return;
            }

            // Ölçü yoxla (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('Fayl ölçüsü 10MB-dan böyükdür: ' + file.name);
                processNext();
                return;
            }

            self.uploadSingleFile(file, function () {
                processNext();
            });
        }

        processNext();
    },

    /**
     * Tək fayl yükləyir
     */
    uploadSingleFile: function (file, callback) {
        var self = this;

        var formData = new FormData();
        formData.append('image', file);
        formData.append('pilgrimage_id', this.pilgrimageId);
        formData.append('csrf_token', this.csrfToken);

        // Yükləmə göstəricisi
        var placeholder = self.createPlaceholder();

        var xhr = new XMLHttpRequest();
        xhr.open('POST', self.galleryBase + 'gallery-upload.php', true);

        xhr.onload = function () {
            placeholder.remove();

            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        // CSRF token yenilə
                        self.csrfToken = data.csrf_token;
                        self.grid.dataset.csrf = data.csrf_token;

                        // Yeni şəkli grid-ə əlavə et
                        self.addImageToGrid(data);
                        self.hideEmptyMessage();
                    } else {
                        alert('Xəta: ' + (data.error || 'Naməlum xəta'));
                    }
                } catch (e) {
                    alert('Server cavabı xətalıdır.');
                }
            } else {
                try {
                    var errData = JSON.parse(xhr.responseText);
                    alert('Xəta: ' + (errData.error || 'Server xətası'));
                } catch (e) {
                    alert('Server xətası: ' + xhr.status);
                }
            }

            if (typeof callback === 'function') callback();
        };

        xhr.onerror = function () {
            placeholder.remove();
            alert('Şəbəkə xətası.');
            if (typeof callback === 'function') callback();
        };

        xhr.send(formData);
    },

    /**
     * Yükləmə göstəricisi yaradır
     */
    createPlaceholder: function () {
        var el = document.createElement('div');
        el.className = 'bb-gallery-item bb-gallery-loading';
        el.innerHTML = '<div class="bb-gallery-img"><div class="bb-gallery-spinner"></div></div>';
        this.grid.appendChild(el);
        return el;
    },

    /**
     * Yeni şəkli grid-ə əlavə edir
     */
    addImageToGrid: function (data) {
        var self = this;
        var item = document.createElement('div');
        item.className = 'bb-gallery-item';
        item.setAttribute('data-id', data.id);
        item.setAttribute('draggable', 'true');

        item.innerHTML =
            '<div class="bb-gallery-img">' +
                '<img src="/' + this.escapeHtml(data.image_path) + '" alt="">' +
                '<button type="button" class="bb-gallery-remove" data-id="' + data.id + '" title="Sil">&times;</button>' +
                '<span class="bb-gallery-drag-handle" title="Sürükləyib sıralayın">&#9776;</span>' +
            '</div>' +
            '<div class="bb-gallery-captions">' +
                '<input type="text" class="bb-gallery-caption" data-id="' + data.id + '" data-lang="az" value="" placeholder="Başlıq (AZ)">' +
                '<input type="text" class="bb-gallery-caption" data-id="' + data.id + '" data-lang="en" value="" placeholder="Caption (EN)">' +
                '<input type="text" class="bb-gallery-caption" data-id="' + data.id + '" data-lang="ru" value="" placeholder="Подпись (RU)">' +
            '</div>';

        this.grid.appendChild(item);

        // Yeni element üçün event-lar bağla
        var removeBtn = item.querySelector('.bb-gallery-remove');
        removeBtn.addEventListener('click', function () {
            self.deleteImage(parseInt(this.dataset.id), item);
        });

        this.bindItemDrag(item);
        this.bindItemCaptions(item);
    },

    /* ============================================
       SİLMƏ
       ============================================ */
    bindDeleteEvents: function () {
        var self = this;
        var buttons = this.grid.querySelectorAll('.bb-gallery-remove');

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var imageId = parseInt(this.dataset.id);
                var item = this.closest('.bb-gallery-item');
                self.deleteImage(imageId, item);
            });
        });
    },

    /**
     * Şəkli silir (AJAX)
     */
    deleteImage: function (imageId, itemEl) {
        var self = this;

        if (!confirm('Bu şəkli silmək istədiyinizə əminsiniz?')) return;

        var formData = new FormData();
        formData.append('image_id', imageId);
        formData.append('csrf_token', this.csrfToken);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.galleryBase + 'gallery-delete.php', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        self.csrfToken = data.csrf_token;
                        self.grid.dataset.csrf = data.csrf_token;

                        // Fade out animasiya
                        itemEl.classList.add('bb-gallery-fade-out');
                        itemEl.addEventListener('animationend', function () {
                            itemEl.remove();
                            self.checkEmpty();
                        });
                    } else {
                        alert('Xəta: ' + (data.error || 'Naməlum xəta'));
                    }
                } catch (e) {
                    alert('Server cavabı xətalıdır.');
                }
            } else {
                alert('Server xətası: ' + xhr.status);
            }
        };

        xhr.onerror = function () {
            alert('Şəbəkə xətası.');
        };

        xhr.send(formData);
    },

    /* ============================================
       DRAG & DROP SIRALAMA
       ============================================ */
    bindDragEvents: function () {
        var items = this.grid.querySelectorAll('.bb-gallery-item');
        var self = this;

        items.forEach(function (item) {
            self.bindItemDrag(item);
        });
    },

    bindItemDrag: function (item) {
        var self = this;

        item.addEventListener('dragstart', function (e) {
            self.dragSrcEl = this;
            this.classList.add('bb-gallery-dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.id);
        });

        item.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('bb-gallery-drag-over');
        });

        item.addEventListener('dragleave', function () {
            this.classList.remove('bb-gallery-drag-over');
        });

        item.addEventListener('dragend', function () {
            this.classList.remove('bb-gallery-dragging');
            // Bütün drag-over class-larını təmizlə
            var allItems = self.grid.querySelectorAll('.bb-gallery-item');
            allItems.forEach(function (i) {
                i.classList.remove('bb-gallery-drag-over');
            });
        });

        item.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('bb-gallery-drag-over');

            if (self.dragSrcEl !== this) {
                // Element sıralamasını dəyişdir
                var allItems = Array.from(self.grid.querySelectorAll('.bb-gallery-item'));
                var fromIndex = allItems.indexOf(self.dragSrcEl);
                var toIndex = allItems.indexOf(this);

                if (fromIndex < toIndex) {
                    self.grid.insertBefore(self.dragSrcEl, this.nextSibling);
                } else {
                    self.grid.insertBefore(self.dragSrcEl, this);
                }

                // Sıralamanı server-ə göndər
                self.saveOrder();
            }
        });
    },

    /**
     * Sıralamanı server-ə göndərir
     */
    saveOrder: function () {
        var self = this;
        var items = this.grid.querySelectorAll('.bb-gallery-item');
        var order = [];

        items.forEach(function (item) {
            var id = parseInt(item.dataset.id);
            if (id) order.push(id);
        });

        if (order.length === 0) return;

        var formData = new FormData();
        formData.append('pilgrimage_id', this.pilgrimageId);
        formData.append('order', JSON.stringify(order));
        formData.append('csrf_token', this.csrfToken);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.galleryBase + 'gallery-reorder.php', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        self.csrfToken = data.csrf_token;
                        self.grid.dataset.csrf = data.csrf_token;
                    }
                } catch (e) {
                    // Sıralama xətası - kritik deyil
                }
            }
        };

        xhr.send(formData);
    },

    /* ============================================
       BAŞLIQ (CAPTİON) REDAKTƏ
       ============================================ */
    bindCaptionEvents: function () {
        var items = this.grid.querySelectorAll('.bb-gallery-item');
        var self = this;

        items.forEach(function (item) {
            self.bindItemCaptions(item);
        });
    },

    bindItemCaptions: function (item) {
        var self = this;
        var inputs = item.querySelectorAll('.bb-gallery-caption');

        inputs.forEach(function (input) {
            var debounceTimer = null;

            input.addEventListener('input', function () {
                var inp = this;
                if (debounceTimer) clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    self.saveCaption(
                        parseInt(inp.dataset.id),
                        inp.dataset.lang,
                        inp.value
                    );
                }, 600);
            });
        });
    },

    /**
     * Başlığı server-ə göndərir (debounce ilə)
     */
    saveCaption: function (imageId, lang, caption) {
        var self = this;

        var formData = new FormData();
        formData.append('image_id', imageId);
        formData.append('lang', lang);
        formData.append('caption', caption);
        formData.append('csrf_token', this.csrfToken);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.galleryBase + 'gallery-caption.php', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        self.csrfToken = data.csrf_token;
                        self.grid.dataset.csrf = data.csrf_token;
                    }
                } catch (e) {
                    // Başlıq xətası - kritik deyil
                }
            }
        };

        xhr.send(formData);
    },

    /* ============================================
       UTIL
       ============================================ */
    hideEmptyMessage: function () {
        var empty = document.getElementById('bbGalleryEmpty');
        if (empty) empty.style.display = 'none';
    },

    checkEmpty: function () {
        var items = this.grid.querySelectorAll('.bb-gallery-item');
        var empty = document.getElementById('bbGalleryEmpty');
        if (items.length === 0 && empty) {
            empty.style.display = '';
        }
    },

    escapeHtml: function (str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
};

/** Qalereyanı inisializasiya etmək üçün global funksiya */
function bbInitGallery() {
    bbGallery.init();
}
