/**
 * Bibiheybet.com - TinyMCE Editor İnisializasiya
 * 
 * Bütün .bb-tinymce-editor textarea-ları TinyMCE-yə çevirir.
 * Şəkil yükləmə /admin/upload.php endpoint-inə gedir.
 */

/** Bütün TinyMCE editorları inisializasiya edir */
function bbInitEditors() {
    var textareas = document.querySelectorAll('.bb-tinymce-editor');
    if (!textareas.length || typeof tinymce === 'undefined') return;

    textareas.forEach(function (el) {
        bbInitEditor('#' + el.id);
    });
}

/** Tək textarea üçün TinyMCE inisializasiya */
function bbInitEditor(selector) {
    tinymce.init({
        selector: selector,
        base_url: '/admin/assets/tinymce',
        license_key: 'gpl',

        /* Plugin-lər */
        plugins: 'image link lists table code fullscreen media directionality',

        /* Toolbar */
        toolbar: 'bold italic underline | blocks | bullist numlist | link image media | blockquote | ltr rtl | code fullscreen',

        /* Ümumi ayarlar */
        menubar: false,
        statusbar: true,
        resize: true,
        height: 400,
        promotion: false,
        branding: false,

        /* Şəkil yükləmə */
        images_upload_url: '/admin/upload.php',
        images_upload_credentials: true,
        automatic_uploads: true,

        /* Şəkil yükləmə handler */
        images_upload_handler: function (blobInfo) {
            return new Promise(function (resolve, reject) {
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/admin/upload.php');
                xhr.withCredentials = true;

                xhr.onload = function () {
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('Yükləmə xətası: ' + xhr.status);
                        return;
                    }

                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.location) {
                            resolve(json.location);
                        } else {
                            reject(json.error || 'Naməlum xəta');
                        }
                    } catch (e) {
                        reject('Cavab oxuna bilmədi.');
                    }
                };

                xhr.onerror = function () {
                    reject('Şəbəkə xətası.');
                };

                xhr.send(formData);
            });
        },

        /* Content stili */
        content_style: 'body { font-family: "Segoe UI", system-ui, sans-serif; font-size: 15px; line-height: 1.7; color: #1e293b; }',

        /* Link ayarları */
        link_default_target: '_blank',
        link_assume_external_targets: true,

        /* Blok formatları */
        block_formats: 'Abzas=p; Başlıq 2=h2; Başlıq 3=h3; Başlıq 4=h4; Sitat=blockquote',

        /* RTL dəstəyi */
        directionality: 'ltr',

        /* Form submit zamanı content-i textarea-ya yaz */
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
}
