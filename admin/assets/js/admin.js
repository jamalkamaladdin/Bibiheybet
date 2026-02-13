/**
 * Bibiheybet.com - Admin Panel JS
 * 
 * Sidebar toggle, flash auto-dismiss, confirm modal,
 * tab switch utility, active sidebar highlight.
 */

document.addEventListener('DOMContentLoaded', function () {
    initSidebar();
    initFlashAutoDismiss();
    initActiveMenu();
});

/* ============================================
   SIDEBAR TOGGLE (mobil)
   ============================================ */
function initSidebar() {
    var toggle = document.getElementById('bbSidebarToggle');
    var sidebar = document.getElementById('bbSidebar');
    var overlay = document.getElementById('bbSidebarOverlay');
    var closeBtn = document.getElementById('bbSidebarClose');

    if (!toggle || !sidebar) return;

    /** Sidebar-ı açır */
    toggle.addEventListener('click', function () {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    /** Overlay klikləndikdə bağlayır */
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    /** X düyməsi ilə bağlayır */
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/* ============================================
   FLASH MESAJ AUTO-DISMISS
   ============================================ */
function initFlashAutoDismiss() {
    var alerts = document.querySelectorAll('.bb-alert');
    if (!alerts.length) return;

    alerts.forEach(function (alert) {
        // 5 saniyə sonra fade-out
        setTimeout(function () {
            alert.classList.add('bb-alert-fade-out');
            // Animasiya bitdikdən sonra DOM-dan sil
            alert.addEventListener('animationend', function () {
                alert.remove();
            });
        }, 5000);
    });
}

/* ============================================
   AKTİV MENYU HİGHLİGHT
   ============================================ */
function initActiveMenu() {
    // Server tərəfdə artıq highlight olunur, amma
    // JS ilə əlavə dəqiqlik təmin edirik
    var currentPath = window.location.pathname;
    var menuLinks = document.querySelectorAll('.bb-sidebar-item a');

    menuLinks.forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href) return;

        // Dəqiq uyğunluq və ya qovluq uyğunluğu
        var isActive = currentPath === href ||
            (href.endsWith('/') && currentPath.startsWith(href));

        if (isActive) {
            link.parentElement.classList.add('active');
        }
    });
}

/* ============================================
   CONFIRM MODAL (FAZA 3-4 üçün hazır utility)
   ============================================ */

/**
 * Təsdiq modal-ı göstərir.
 * 
 * @param {string} title Modal başlığı
 * @param {string} text Modal mətni
 * @param {Function} onConfirm Təsdiq ediləndə çağırılan callback
 */
function bbConfirm(title, text, onConfirm) {
    // Mövcud modal varsa sil
    var existing = document.getElementById('bbConfirmModal');
    if (existing) existing.remove();

    var overlay = document.createElement('div');
    overlay.className = 'bb-modal-overlay active';
    overlay.id = 'bbConfirmModal';

    overlay.innerHTML =
        '<div class="bb-modal">' +
            '<h3 class="bb-modal-title">' + escapeHtml(title) + '</h3>' +
            '<p class="bb-modal-text">' + escapeHtml(text) + '</p>' +
            '<div class="bb-modal-actions">' +
                '<button type="button" class="bb-btn bb-btn-outline" id="bbConfirmCancel">Ləğv et</button>' +
                '<button type="button" class="bb-btn bb-btn-danger" id="bbConfirmOk">Sil</button>' +
            '</div>' +
        '</div>';

    document.body.appendChild(overlay);

    // Ləğv et
    document.getElementById('bbConfirmCancel').addEventListener('click', function () {
        overlay.remove();
    });

    // Overlay kliklə bağla
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) overlay.remove();
    });

    // Təsdiq et
    document.getElementById('bbConfirmOk').addEventListener('click', function () {
        overlay.remove();
        if (typeof onConfirm === 'function') onConfirm();
    });
}

/* ============================================
   TAB SWİTCH UTİLİTY (FAZA 3-4 üçün hazır)
   ============================================ */

/**
 * Tab panelləri arasında keçid edir.
 * 
 * HTML strukturu:
 *   <div class="bb-tabs">
 *     <button class="bb-tab active" data-tab="az">AZ</button>
 *     <button class="bb-tab" data-tab="en">EN</button>
 *     <button class="bb-tab" data-tab="ru">RU</button>
 *   </div>
 *   <div class="bb-tab-content active" data-tab-content="az">...</div>
 *   <div class="bb-tab-content" data-tab-content="en">...</div>
 *   <div class="bb-tab-content" data-tab-content="ru">...</div>
 */
function initTabs(container) {
    var parent = container || document;
    var tabs = parent.querySelectorAll('.bb-tab[data-tab]');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var targetId = this.getAttribute('data-tab');
            var tabContainer = this.closest('.bb-tabs');
            if (!tabContainer) return;

            // Bütün tabları deaktiv et
            tabContainer.querySelectorAll('.bb-tab').forEach(function (t) {
                t.classList.remove('active');
            });

            // Bütün contentləri gizlət
            var wrapper = tabContainer.parentElement;
            wrapper.querySelectorAll('.bb-tab-content').forEach(function (c) {
                c.classList.remove('active');
            });

            // Seçilmişi aktiv et
            this.classList.add('active');
            var content = wrapper.querySelector('[data-tab-content="' + targetId + '"]');
            if (content) content.classList.add('active');
        });
    });
}

/* ============================================
   HELPER: HTML escape
   ============================================ */
function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
