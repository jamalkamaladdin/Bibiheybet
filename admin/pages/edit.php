<?php
/**
 * Bibiheybet.com - Səhifə Məzmunu Redaktə
 * 
 * Seçilmiş səhifənin bütün bölmələrini bütün dillər üçün redaktə edir.
 */

require_once __DIR__ . '/../includes/layout.php';

$db = bb_get_db();

// page_contents cədvəli mövcuddurmu yoxla, yoxdursa yarat
try {
    $db->query("SELECT 1 FROM page_contents LIMIT 1");
} catch (PDOException $e) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS `page_contents` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `page_key` VARCHAR(100) NOT NULL,
            `section_key` VARCHAR(100) NOT NULL,
            `content_az` LONGTEXT NULL,
            `content_en` LONGTEXT NULL,
            `content_ru` LONGTEXT NULL,
            `content_ar` LONGTEXT NULL,
            `content_fa` LONGTEXT NULL,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_page_section` (`page_key`, `section_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

// Səhifə konfiqurasiyası
$pages = [
    'home' => [
        'label' => 'Ana Səhifə',
        'sections' => [
            'hero_subtitle'    => ['label' => 'Hero alt yazı', 'type' => 'text'],
            'hazrat_text'      => ['label' => 'Həzrət haqqında mətn', 'type' => 'textarea'],
            'mosque_text_1'    => ['label' => 'Məscid haqqında mətn (1-ci hissə)', 'type' => 'textarea'],
            'mosque_strong'    => ['label' => 'Məscid haqqında güclü mətn', 'type' => 'text'],
            'mosque_text_2'    => ['label' => 'Məscid haqqında mətn (2-ci hissə)', 'type' => 'textarea'],
            'pilgrimages_title' => ['label' => 'Ziyarətgahlar bölmə başlığı', 'type' => 'text'],
            'articles_title'   => ['label' => 'Məqalələr bölmə başlığı', 'type' => 'text'],
            'read_more'        => ['label' => '"Daha ətraflı" düyməsi', 'type' => 'text'],
            'show_more'        => ['label' => '"Daha çox" düyməsi', 'type' => 'text'],
        ],
    ],
    'about-hazrat' => [
        'label' => 'Həkimə xanım haqqında',
        'sections' => [
            'title'       => ['label' => 'Səhifə başlığı', 'type' => 'text'],
            'description' => ['label' => 'Səhifə təsviri', 'type' => 'textarea'],
            'content'     => ['label' => 'Əsas məzmun', 'type' => 'editor'],
        ],
    ],
    'about-mosque' => [
        'label' => 'Məscid haqqında',
        'sections' => [
            'title'       => ['label' => 'Səhifə başlığı', 'type' => 'text'],
            'description' => ['label' => 'Səhifə təsviri', 'type' => 'textarea'],
            'content'     => ['label' => 'Əsas məzmun', 'type' => 'editor'],
        ],
    ],
    'prayers' => [
        'label' => 'Dua və ziyarətnamə',
        'sections' => [
            'title'       => ['label' => 'Səhifə başlığı', 'type' => 'text'],
            'description' => ['label' => 'Səhifə təsviri', 'type' => 'textarea'],
            'content'     => ['label' => 'Əsas məzmun', 'type' => 'editor'],
        ],
    ],
];

$pageKey = $_GET['page'] ?? '';
if (!isset($pages[$pageKey])) {
    bb_flash('error', 'Belə səhifə tapılmadı.');
    bb_redirect('/admin/pages/');
}

$pageConfig = $pages[$pageKey];
$langs = ['az', 'en', 'ru', 'ar', 'fa'];

// POST: Yadda saxla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!bb_verify_csrf($_POST['csrf_token'] ?? '')) {
        bb_flash('error', 'CSRF doğrulaması uğursuz oldu. Yenidən cəhd edin.');
        bb_redirect('/admin/pages/edit.php?page=' . urlencode($pageKey));
    }

    foreach ($pageConfig['sections'] as $sectionKey => $sectionInfo) {
        $values = [':page_key' => $pageKey, ':section_key' => $sectionKey];
        $columns = [];
        $placeholders = [];
        $updateParts = [];
        foreach ($langs as $l) {
            $col = "content_{$l}";
            $param = ":content_{$l}";
            $values[$param] = $_POST["{$sectionKey}_{$l}"] ?? '';
            $columns[] = $col;
            $placeholders[] = $param;
            $updateParts[] = "`{$col}` = VALUES(`{$col}`)";
        }

        $sql = "INSERT INTO page_contents (page_key, section_key, " . implode(', ', $columns) . ")
                VALUES (:page_key, :section_key, " . implode(', ', $placeholders) . ")
                ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts);

        $stmt = $db->prepare($sql);
        $stmt->execute($values);
    }

    bb_flash('success', 'Səhifə məzmunu uğurla yeniləndi!');
    bb_redirect('/admin/pages/edit.php?page=' . urlencode($pageKey));
}

// Saytdakı default məzmunlar (DB-də boş olanda görünən)
$siteDefaults = [
    'home' => [
        'hero_subtitle' => [
            'az' => 'Fatimeyi-Suğra, Həkimə xanımın müqəddəs ziyarətgahı',
            'en' => 'The holy shrine of Lady Fatima Sugra, Hakima Khanym',
            'ru' => 'Святая обитель госпожи Фатимы ас-Сугры (Хакимы ханум)',
            'ar' => "المقام المقدس للسيدة فاطمة الصغرى\nحكيمة خاتون عليها السلام",
            'fa' => 'زیارتگاه مقدس حضرت فاطمه صغری حکیمه خاتون (سلام‌الله‌علیها)',
        ],
        'hazrat_text' => [
            'az' => 'Həkimə xanım, 9-cu əsrdə yaşamış, İmam Musa Kazımın (a.s) qızı və İmam Əli Rzanın (a.s) bacısıdır. O, xəlifələrin təqibindən qaçaraq Bakıya sığınmış və burada "Heybət xanımın bibisi" adı ilə tanınmışdır. Vəfatından sonra, məzarı üzərində inşa edilən məscid və türbə, zamanla Bibi Heybət adı ilə məşhurlaşmışdır.',
            'en' => 'Lady Hakima was a 9th-century figure, the daughter of Imam Musa al-Kazim (a.s.) and the sister of Imam Ali al-Ridha (a.s.). Fleeing persecution by the caliphs, she sought refuge in Baku, where she became known as "the aunt of Heybat Khanum." After her passing, a mosque and mausoleum were constructed over her grave, which in time became renowned as Bibi-Heybat.',
            'ru' => 'Хакима ханым — религиозная деятельница IX века, дочь Имама Мусы аль-Казима (а.с.) и сестра Имама Али ар-Ризы (а.с.). Спасаясь от преследований халифов, она нашла убежище в Баку, где была известна как «тётя Хейбат ханым». После её кончины над её могилой были возведены мечеть и мавзолей, которые со временем стали известны под названием Биби-Хейбат.',
        ],
        'mosque_text_1' => [
            'az' => 'Bibiheybət məscidi XIII əsrdə Şirvanşah II Fərruxzad ibn Axsitan tərəfindən inşa etdirilib. 1281-1282-ci illərdə tikilən məscidin divarındakı tarixi kitabələrdə onun memarının Mahmud ibn Sədi olduğu qeyd edilib.',
            'en' => 'Bibi-Heybat Mosque was built in the 13th century by Shirvanshah II Farrukhzad ibn Akhsitan. Historical inscriptions on the mosque\'s walls, constructed between 1281 and 1282, state that its architect was Mahmud ibn Sadi.',
            'ru' => 'Мечеть Биби-Хейбат была построена в XIII веке Ширваншахом II Фаррухзадом ибн Ахситаном. Исторические надписи на стенах мечети, возведённой в 1281–1282 годах, указывают, что архитектором был Махмуд ибн Сади.',
        ],
        'mosque_strong' => [
            'az' => 'Nə inam öldü, nə iman',
            'en' => 'Neither faith died, nor belief',
            'ru' => 'Ни вера не умерла, ни убеждение',
        ],
        'mosque_text_2' => [
            'az' => '1934-cü ilin sentyabr ayında kommunist rejimi tərəfindən ziyarətgah dağıdılaraq yerlə-yeksan edilib. Amma qadağalara baxmayaraq, inanclı insanlar yenə bu yerləri unutmayıblar. 1997-ci il iyulun 23-də Həzrəti Peyğəmbərin mövludu günü məscidin təməli qoyulur. Tikinti işləri bir ilə başa çatır və məscid möminlərin ixtiyarına verilir.',
            'en' => 'In September 1934, the Communist regime destroyed the shrine, reducing it to ruins. Despite these prohibitions, devout people never forgot this sacred place. On July 23, 1997, coinciding with the Prophet\'s birthday, the foundation of the mosque was laid. Construction was completed within a year, and the mosque was returned to the worshippers.',
            'ru' => 'В сентябре 1934 года коммунистический режим разрушил святыню, полностью её уничтожив. Но несмотря на запреты, верующие никогда не забывали это святое место. 23 июля 1997 года, в день рождения Пророка, был заложен фундамент новой мечети. Строительство завершилось в течение года, и мечеть вновь стала доступна для прихожан.',
        ],
        'read_more' => [
            'az' => 'Daha ətraflı', 'en' => 'Read more', 'ru' => 'Подробнее',
            'ar' => 'اقرأ المزيد', 'fa' => 'بیشتر بخوانید',
        ],
        'show_more' => [
            'az' => 'Daha çox', 'en' => 'Show more', 'ru' => 'Показать ещё',
            'ar' => 'عرض المزيد', 'fa' => 'نمایش بیشتر',
        ],
        'pilgrimages_title' => [
            'az' => 'Ziyarətgahlar', 'en' => 'Pilgrimages', 'ru' => 'Святыни',
            'ar' => 'المقامات والمزارات', 'fa' => 'زیارتگاه‌ها',
        ],
        'articles_title' => [
            'az' => 'Məqalələr', 'en' => 'Articles', 'ru' => 'Статьи',
            'ar' => 'المقالات', 'fa' => 'مقالات',
        ],
    ],
    'about-hazrat' => [
        'title' => [
            'az' => 'Həzrət haqqında', 'en' => 'About the Holy Lady', 'ru' => 'О Её Светлости',
            'ar' => 'نبذة عن السيدة حكيمة', 'fa' => 'درباره حضرت حکیمه',
        ],
        'description' => [
            'az' => 'Hz. Həkimə Xanım (s) - İmam Musa Kazımın (ə.s.) qızı və İmam Əli Rzanın (ə.s.) bacısı haqqında ətraflı məlumat.',
            'en' => 'Detailed information about Hz. Hakimah Khatun (s) - daughter of Imam Musa al-Kazim (a.s.) and sister of Imam Ali al-Ridha (a.s.).',
            'ru' => 'Подробная информация о Хз. Хакиме Хатун (с) — дочери Имама Мусы аль-Казима (а.с.) и сестре Имама Али ар-Ризы (а.с.).',
            'ar' => 'معلومات مفصلة عن السيدة حكيمة خاتون (ع) - ابنة الإمام موسى الكاظم (ع) وأخت الإمام علي الرضا (ع).',
            'fa' => 'اطلاعات تفصیلی درباره حضرت حکیمه خاتون (س) - دختر امام موسی کاظم (ع) و خواهر امام علی رضا (ع).',
        ],
        'content' => [
            'az' => 'Həkimə xanım, 9-cu əsrdə yaşamış, İmam Musa Kazımın (ə.s.) qızı və İmam Əli Rzanın (ə.s.) bacısıdır...',
            'en' => 'Lady Hakima was a 9th-century figure, the daughter of Imam Musa al-Kazim (a.s.) and the sister of Imam Ali al-Ridha (a.s.)...',
            'ru' => 'Хакима ханым — религиозная деятельница IX века, дочь Имама Мусы аль-Казима (а.с.) и сестра Имама Али ар-Ризы (а.с.)...',
        ],
    ],
    'about-mosque' => [
        'title' => [
            'az' => 'Məscid haqqında', 'en' => 'About the Mosque', 'ru' => 'О Мечети',
            'ar' => 'عن المسجد', 'fa' => 'درباره مسجد',
        ],
        'description' => [
            'az' => 'Bibiheybət Məscidinin tarixi, memarlığı və yenidən qurulması haqqında.',
            'en' => 'About the history, architecture and reconstruction of Bibiheybat Mosque.',
            'ru' => 'Об истории, архитектуре и восстановлении мечети Бибиэйбат.',
            'ar' => 'عن تاريخ وعمارة وإعادة بناء مسجد بيبي حيبات.',
            'fa' => 'درباره تاریخ، معماری و بازسازی مسجد بی‌بی حیبات.',
        ],
        'content' => [
            'az' => 'Bibiheybət məscidi — Bakının ən qədim və ən müqəddəs dini abidələrindən biridir...',
            'en' => 'Bibi-Heybat Mosque is one of the oldest and most sacred religious monuments of Baku...',
            'ru' => 'Мечеть Биби-Хейбат — одна из старейших и наиболее священных религиозных памятников Баку...',
        ],
    ],
    'prayers' => [
        'title' => [
            'az' => 'Dua və ziyarətnamə', 'en' => 'Prayers and Ziyarat Texts', 'ru' => 'Молитвы и зиярат',
            'ar' => 'الأدعية والزيارات', 'fa' => 'دعاها و زیارت‌نامه‌ها',
        ],
        'description' => [
            'az' => 'Bibiheybət ziyarətgahında oxunan dua və ziyarətnamə mətnləri.',
            'en' => 'Prayer and pilgrimage texts recited at Bibiheybat Shrine.',
            'ru' => 'Тексты молитв и зияратов, читаемых в святыне Бибиэйбат.',
            'ar' => 'نصوص الأدعية والزيارات التي تُقرأ في مزار بيبي حيبات.',
            'fa' => 'متون دعاها و زیارت‌نامه‌هایی که در زیارتگاه بی‌بی حیبات خوانده می‌شود.',
        ],
        'content' => [],
    ],
];

// Mövcud datanı yüklə (DB dəyərlər + default fallback)
$existing = [];
$rows = $db->prepare("SELECT * FROM page_contents WHERE page_key = :page_key");
$rows->execute([':page_key' => $pageKey]);
foreach ($rows->fetchAll() as $row) {
    $existing[$row['section_key']] = $row;
}

// Default dəyərlərlə birləşdir - DB-də olmayan sahələri default ilə doldur
$defaults = $siteDefaults[$pageKey] ?? [];
foreach ($pageConfig['sections'] as $sectionKey => $sectionInfo) {
    foreach ($langs as $l) {
        $col = "content_{$l}";
        if (empty($existing[$sectionKey][$col]) && !empty($defaults[$sectionKey][$l])) {
            $existing[$sectionKey][$col] = $defaults[$sectionKey][$l];
        }
    }
}

bb_admin_header($pageConfig['label'] . ' - Redaktə', [
    'extra_js' => ['/admin/assets/tinymce/tinymce.min.js', '/admin/assets/js/editor.js'],
]);
?>

<div class="bb-page-header">
    <h2><?= bb_sanitize($pageConfig['label']) ?></h2>
    <a href="/admin/pages/" class="bb-btn bb-btn-outline bb-btn-sm">&larr; Geri</a>
</div>

<form method="POST" class="bb-page-edit-form">
    <?= bb_generate_csrf() ?>

    <?php foreach ($pageConfig['sections'] as $sectionKey => $sectionInfo): ?>
    <div class="bb-card bb-section-card">
        <h3 class="bb-card-title"><?= bb_sanitize($sectionInfo['label']) ?></h3>

        <div class="bb-tabs">
            <?php foreach ($langs as $i => $l): ?>
            <button type="button" class="bb-tab<?= $i === 0 ? ' active' : '' ?>" data-tab="<?= $sectionKey ?>_<?= $l ?>"><?= strtoupper($l) ?></button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($langs as $i => $l): ?>
            <?php
                $fieldId = "{$sectionKey}_{$l}";
                $fieldValue = $existing[$sectionKey]["content_{$l}"] ?? '';
            ?>
        <div class="bb-tab-content<?= $i === 0 ? ' active' : '' ?>" data-tab-content="<?= $fieldId ?>">
            <?php if ($sectionInfo['type'] === 'text'): ?>
                <div class="bb-form-group">
                    <input type="text" id="<?= $fieldId ?>" name="<?= $fieldId ?>"
                        value="<?= bb_sanitize($fieldValue) ?>"
                        placeholder="<?= strtoupper($l) ?> dilində daxil edin...">
                </div>
            <?php elseif ($sectionInfo['type'] === 'textarea'): ?>
                <div class="bb-form-group">
                    <textarea id="<?= $fieldId ?>" name="<?= $fieldId ?>"
                        rows="4" placeholder="<?= strtoupper($l) ?> dilində daxil edin..."><?= bb_sanitize($fieldValue) ?></textarea>
                </div>
            <?php elseif ($sectionInfo['type'] === 'editor'): ?>
                <div class="bb-form-group">
                    <textarea id="<?= $fieldId ?>" name="<?= $fieldId ?>"
                        class="bb-tinymce-editor" rows="12"><?= bb_sanitize($fieldValue) ?></textarea>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <div class="bb-form-actions bb-form-actions-sticky">
        <button type="submit" class="bb-btn bb-btn-primary bb-btn-lg">Yadda saxla</button>
    </div>
</form>

<style>
.bb-section-card {
    margin-bottom: 1.5rem;
}

.bb-section-card .bb-card-title {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.bb-form-actions-sticky {
    position: sticky;
    bottom: 0;
    background: var(--admin-content-bg, #f9fafb);
    padding: 1rem 0;
    margin-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    text-align: right;
    z-index: 10;
}

.bb-btn-lg {
    padding: 0.75rem 2.5rem;
    font-size: 1rem;
}
</style>


<?php
bb_admin_footer();
