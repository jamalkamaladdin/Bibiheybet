/**
 * Bibiheybet.com - Şəkil Yükləmə (Drag & Drop, Preview)
 * 
 * Hər .bb-image-upload elementi üçün:
 *  - Drag & drop dəstəyi
 *  - Seçilmiş şəklin ön izləməsi (preview)
 *  - Mövcud şəklin göstərilməsi
 *  - Client-side tip/ölçü yoxlanışı
 */

/** Bütün şəkil yükləmə sahələrini inisializasiya edir */
function bbInitImageUploads() {
    var wrappers = document.querySelectorAll('.bb-image-upload');
    wrappers.forEach(function (wrapper) {
        bbInitSingleUpload(wrapper);
    });
}

/** Tək yükləmə sahəsi üçün event handler-lar */
function bbInitSingleUpload(wrapper) {
    var fileInput = wrapper.querySelector('.bb-file-input');
    if (!fileInput) return;

    // Drag & drop zona yaratma
    var dropZone = wrapper.querySelector('.bb-drop-zone');
    if (!dropZone) {
        dropZone = document.createElement('div');
        dropZone.className = 'bb-drop-zone';
        dropZone.innerHTML = '<span class="bb-drop-text">Şəkli bura sürükləyin və ya seçin</span>';
        wrapper.insertBefore(dropZone, fileInput);
    }

    // File input-u drop zone ilə bağla
    dropZone.addEventListener('click', function () {
        fileInput.click();
    });

    // File seçildi
    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            handleFile(wrapper, this.files[0], fileInput);
        }
    });

    // Drag & drop hadisələri
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('bb-drop-active');
    });

    dropZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('bb-drop-active');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('bb-drop-active');

        var files = e.dataTransfer.files;
        if (files && files[0]) {
            // File input-a da yaz (form submit üçün)
            var dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput.files = dt.files;
            handleFile(wrapper, files[0], fileInput);
        }
    });
}

/** Seçilmiş faylı yoxlayır və preview göstərir */
function handleFile(wrapper, file, fileInput) {
    // Tip yoxla
    var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (allowedTypes.indexOf(file.type) === -1) {
        alert('İcazə verilməyən fayl tipi. Yalnız JPG, PNG, GIF, WEBP.');
        fileInput.value = '';
        return;
    }

    // Ölçü yoxla (10MB)
    var maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('Fayl ölçüsü 10MB-dan böyükdür.');
        fileInput.value = '';
        return;
    }

    // Preview göstər
    showPreview(wrapper, file);
}

/** Şəklin ön izləməsini göstərir */
function showPreview(wrapper, file) {
    // Mövcud preview-u sil
    var existing = wrapper.querySelector('.bb-image-preview');
    if (existing) existing.remove();

    var preview = document.createElement('div');
    preview.className = 'bb-image-preview';

    var img = document.createElement('img');
    img.alt = 'Preview';

    var removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'bb-preview-remove';
    removeBtn.innerHTML = '&times;';
    removeBtn.title = 'Şəkli sil';

    removeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        preview.remove();
        var input = wrapper.querySelector('.bb-file-input');
        if (input) input.value = '';
    });

    var reader = new FileReader();
    reader.onload = function (e) {
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);

    preview.appendChild(img);
    preview.appendChild(removeBtn);
    wrapper.appendChild(preview);
}
