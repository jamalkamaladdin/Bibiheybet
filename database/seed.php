<?php
/**
 * Bibiheybet.com - Database Seed Script
 * 
 * Admin istifadəçi, kateqoriyalar, məqalələr və ziyarətgahları yaradır.
 * İstifadə: php database/seed.php
 * 
 * Qeyd: Bu script-i yalnız bir dəfə çalışdırın.
 * Əvvəlcə database/schema.sql-i MySQL-ə import edin.
 */

// Konfiqurasiyanı yüklə
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

echo "Bibiheybet - Database Seed\n";
echo "==========================\n\n";

try {
    $db = bb_get_db();

    // ============================================
    // 1. Admin istifadəçi
    // ============================================
    $username = 'ekosafari';
    $password = 'ParolYanlisdirSifreDogrudur';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("SELECT id FROM admins WHERE username = :username");
    $stmt->execute([':username' => $username]);

    if ($stmt->fetch()) {
        echo "[!] Admin '{$username}' artiq movcuddur. Kecilir.\n";
    } else {
        $stmt = $db->prepare("INSERT INTO admins (username, password_hash, created_at) VALUES (:username, :password_hash, NOW())");
        $stmt->execute([
            ':username' => $username,
            ':password_hash' => $passwordHash,
        ]);
        echo "[+] Admin '{$username}' ugurla yaradildi.\n";
    }

    // ============================================
    // 2. Kateqoriyalar
    // ============================================
    $categories = [
        [
            'slug'     => 'tarix',
            'name_az'  => 'Tarix',
            'name_en'  => 'History',
            'name_ru'  => 'История',
            'sort_order' => 1,
        ],
        [
            'slug'     => 'din-ve-medeniyyet',
            'name_az'  => 'Din və mədəniyyət',
            'name_en'  => 'Religion & Culture',
            'name_ru'  => 'Религия и культура',
            'sort_order' => 2,
        ],
        [
            'slug'     => 'ziyaret-rehberi',
            'name_az'  => 'Ziyarət rəhbəri',
            'name_en'  => 'Pilgrimage Guide',
            'name_ru'  => 'Путеводитель паломника',
            'sort_order' => 3,
        ],
    ];

    $catStmt = $db->prepare("SELECT id FROM categories WHERE slug = :slug");
    $catInsert = $db->prepare(
        "INSERT INTO categories (slug, name_az, name_en, name_ru, sort_order, created_at) 
         VALUES (:slug, :name_az, :name_en, :name_ru, :sort_order, NOW())"
    );
    $categoryIds = [];

    foreach ($categories as $cat) {
        $catStmt->execute([':slug' => $cat['slug']]);
        if ($catStmt->fetch()) {
            echo "[!] Kateqoriya '{$cat['slug']}' artiq movcuddur. Kecilir.\n";
            $catStmt2 = $db->prepare("SELECT id FROM categories WHERE slug = :slug");
            $catStmt2->execute([':slug' => $cat['slug']]);
            $row = $catStmt2->fetch();
            $categoryIds[$cat['slug']] = $row['id'];
        } else {
            $catInsert->execute([
                ':slug'       => $cat['slug'],
                ':name_az'    => $cat['name_az'],
                ':name_en'    => $cat['name_en'],
                ':name_ru'    => $cat['name_ru'],
                ':sort_order' => $cat['sort_order'],
            ]);
            $categoryIds[$cat['slug']] = $db->lastInsertId();
            echo "[+] Kateqoriya '{$cat['slug']}' yaradildi.\n";
        }
    }

    // ============================================
    // 3. Meqaleler
    // ============================================
    $articles = [
        [
            'category_id'  => $categoryIds['tarix'] ?? null,
            'slug_az'      => 'bibiheybet-ziyaretgahinin-tarixi',
            'slug_en'      => 'history-of-bibiheybat-shrine',
            'slug_ru'      => 'istoriya-svyatyni-bibiheybat',
            'title_az'     => 'Bibiheybət Ziyarətgahının tarixi',
            'title_en'     => 'History of the Bibi-Heybat Shrine',
            'title_ru'     => 'История святыни Биби-Хейбат',
            'content_az'   => '<p>Bibiheybət ziyarətgahı Bakının ən qədim və müqəddəs yerlərindən biridir. XIII əsrdə Şirvanşah II Fərruxzad ibn Axsitan tərəfindən inşa etdirilən bu məscid, əsrlər boyu müsəlmanların ziyarət yeri olmuşdur.</p><p>1281-1282-ci illərdə tikilən məscidin divarındakı tarixi kitabələrdə onun memarının Mahmud ibn Sədi olduğu qeyd edilib. Ziyarətgah 1934-cü ildə sovet rejimi tərəfindən dağıdılmış, lakin 1997-ci ildə yenidən bərpa olunmuşdur.</p><p>Bu gün Bibiheybət ziyarətgahı yalnız Azərbaycanda deyil, bütün İslam dünyasında tanınan müqəddəs bir məkandır.</p>',
            'content_en'   => '<p>The Bibi-Heybat shrine is one of the oldest and most sacred places in Baku. Built in the 13th century by Shirvanshah II Farrukhzad ibn Akhsitan, this mosque has been a place of pilgrimage for Muslims for centuries.</p><p>Historical inscriptions on the walls of the mosque, constructed between 1281 and 1282, indicate that its architect was Mahmud ibn Sadi. The shrine was destroyed by the Soviet regime in 1934, but was restored in 1997.</p><p>Today, the Bibi-Heybat shrine is a sacred place known not only in Azerbaijan but throughout the entire Islamic world.</p>',
            'content_ru'   => '<p>Святыня Биби-Хейбат — одно из старейших и самых священных мест Баку. Построенная в XIII веке Ширваншахом II Фаррухзадом ибн Ахситаном, эта мечеть веками была местом паломничества мусульман.</p><p>Исторические надписи на стенах мечети, возведённой в 1281–1282 годах, указывают, что архитектором был Махмуд ибн Сади. Святыня была разрушена советским режимом в 1934 году, но восстановлена в 1997 году.</p><p>Сегодня святыня Биби-Хейбат — священное место, известное не только в Азербайджане, но и во всём исламском мире.</p>',
            'excerpt_az'   => 'Bibiheybət ziyarətgahı Bakının ən qədim və müqəddəs yerlərindən biridir. XIII əsrdə inşa edilən bu məscid əsrlər boyu müsəlmanların ziyarət yeri olmuşdur.',
            'excerpt_en'   => 'The Bibi-Heybat shrine is one of the oldest and most sacred places in Baku. Built in the 13th century, this mosque has been a pilgrimage site for centuries.',
            'excerpt_ru'   => 'Святыня Биби-Хейбат — одно из старейших и самых священных мест Баку. Построенная в XIII веке, эта мечеть веками была местом паломничества.',
            'featured_image' => 'public/assets/img/Bibi-Heybat-Mosque.jpg',
            'meta_title_az'  => 'Bibiheybət Ziyarətgahının Tarixi | Bibiheybət',
            'meta_title_en'  => 'History of Bibi-Heybat Shrine | Bibiheybat',
            'meta_title_ru'  => 'История святыни Биби-Хейбат | Бибихейбат',
            'meta_desc_az'   => 'Bibiheybət ziyarətgahının XIII əsrdən bu günə qədər tarixi, dağıdılması və bərpası haqqında ətraflı məlumat.',
            'meta_desc_en'   => 'Detailed information about the history of the Bibi-Heybat shrine from the 13th century to the present day.',
            'meta_desc_ru'   => 'Подробная информация об истории святыни Биби-Хейбат с XIII века до наших дней.',
            'status'         => 'published',
            'published_at'   => '2026-02-10 10:00:00',
        ],
        [
            'category_id'  => $categoryIds['din-ve-medeniyyet'] ?? null,
            'slug_az'      => 'hekime-xanim-kimdir',
            'slug_en'      => 'who-is-hakimah-khatun',
            'slug_ru'      => 'kto-takaya-hakima-hatun',
            'title_az'     => 'Həkimə xanım (s) kimdir?',
            'title_en'     => 'Who is Hakimah Khatun (s)?',
            'title_ru'     => 'Кто такая Хакима Хатун (с)?',
            'content_az'   => '<p>Həkimə xanım (s), İmam Musa Kazımın (ə.s) qızı və İmam Əli Rzanın (ə.s) bacısıdır. O, 9-cu əsrdə yaşamış, xəlifələrin təqibindən qaçaraq Bakıya sığınmış görkəmli bir şəxsiyyətdir.</p><p>Bakıda "Heybət xanımın bibisi" adı ilə tanınan Həkimə xanım, ömrünün sonuna qədər burada yaşamış və vəfatından sonra məzarı üzərində türbə inşa edilmişdir. Bu türbə zamanla Bibiheybət adı ilə məşhurlaşmışdır.</p><p>Həkimə xanımın (s) nəsil şəcərəsi Peyğəmbər (s) ailəsinə bağlıdır və o, İslam tarixində mühüm yer tutan müqəddəs şəxsiyyətlərdən biridir.</p>',
            'content_en'   => '<p>Hakimah Khatun (s) was the daughter of Imam Musa al-Kazim (a.s.) and the sister of Imam Ali al-Ridha (a.s.). She was a distinguished figure of the 9th century who fled the persecution of the caliphs and sought refuge in Baku.</p><p>Known in Baku as "the aunt of Heybat Khanum," Hakimah Khatun lived here until the end of her life. After her passing, a mausoleum was built over her grave, which over time became famous under the name Bibi-Heybat.</p><p>Hakimah Khatun\'s lineage traces back to the Prophet\'s (s) family, and she is one of the revered figures who hold an important place in Islamic history.</p>',
            'content_ru'   => '<p>Хакима Хатун (с) — дочь Имама Мусы аль-Казима (а.с.) и сестра Имама Али ар-Ризы (а.с.). Она была выдающейся личностью IX века, бежавшей от преследований халифов и нашедшей убежище в Баку.</p><p>Известная в Баку как «тётя Хейбат ханым», Хакима Хатун прожила здесь до конца своей жизни. После её кончины над её могилой был построен мавзолей, который со временем стал известен под названием Биби-Хейбат.</p><p>Родословная Хакимы Хатун восходит к семье Пророка (с), и она является одной из почитаемых личностей, занимающих важное место в истории ислама.</p>',
            'excerpt_az'   => 'Həkimə xanım (s), İmam Musa Kazımın (ə.s) qızı və İmam Əli Rzanın (ə.s) bacısıdır. Bakıda "Heybət xanımın bibisi" adı ilə tanınıb.',
            'excerpt_en'   => 'Hakimah Khatun (s) was the daughter of Imam Musa al-Kazim (a.s.) and sister of Imam Ali al-Ridha (a.s.), known in Baku as "the aunt of Heybat Khanum."',
            'excerpt_ru'   => 'Хакима Хатун (с) — дочь Имама Мусы аль-Казима (а.с.) и сестра Имама Али ар-Ризы (а.с.), известная в Баку как «тётя Хейбат ханым».',
            'featured_image' => 'public/assets/img/herem-transparan.png',
            'meta_title_az'  => 'Həkimə Xanım (s) Kimdir? | Bibiheybət',
            'meta_title_en'  => 'Who is Hakimah Khatun (s)? | Bibiheybat',
            'meta_title_ru'  => 'Кто такая Хакима Хатун (с)? | Бибихейбат',
            'meta_desc_az'   => 'İmam Musa Kazımın (ə.s) qızı və İmam Əli Rzanın (ə.s) bacısı Hz. Həkimə xanım (s) haqqında ətraflı məlumat.',
            'meta_desc_en'   => 'Detailed information about Hz. Hakimah Khatun (s), daughter of Imam Musa al-Kazim (a.s.) and sister of Imam Ali al-Ridha (a.s.).',
            'meta_desc_ru'   => 'Подробная информация о Хз. Хакиме Хатун (с), дочери Имама Мусы аль-Казима (а.с.) и сестре Имама Али ар-Ризы (а.с.).',
            'status'         => 'published',
            'published_at'   => '2026-02-12 14:00:00',
        ],
        [
            'category_id'  => $categoryIds['ziyaret-rehberi'] ?? null,
            'slug_az'      => 'ziyaret-qaydalari-ve-adabi',
            'slug_en'      => 'pilgrimage-rules-and-etiquette',
            'slug_ru'      => 'pravila-i-etiket-palomnichestva',
            'title_az'     => 'Ziyarət qaydaları və ədəbi',
            'title_en'     => 'Pilgrimage Rules and Etiquette',
            'title_ru'     => 'Правила и этикет паломничества',
            'content_az'   => '<p>Bibiheybət ziyarətgahını ziyarət edərkən müəyyən qaydalara və ədəb-ərkana riayət etmək vacibdir. Bu qaydalar həm ziyarətçilərin, həm də müqəddəs məkanın hörmətini qorumaq məqsədilə təyin edilmişdir.</p><p><strong>Geyim qaydası:</strong> Ziyarətgaha daxil olarkən təmiz və münasib geyim geyinmək lazımdır. Qadınlar üçün hicab (baş örtüyü) tələb olunur.</p><p><strong>Davranış qaydaları:</strong> Ziyarətgah ərazisində sakit davranmaq, ucadan danışmamaq və digər ziyarətçilərə hörmətlə yanaşmaq vacibdir.</p><p><strong>Namaz vaxtları:</strong> Gündəlik beş vaxt namaz ziyarətgahda icra olunur. Ziyarətçilər camaatla namaza qoşula bilərlər.</p>',
            'content_en'   => '<p>When visiting the Bibi-Heybat shrine, it is important to observe certain rules and etiquette. These rules have been established to maintain the respect of both visitors and the sacred place.</p><p><strong>Dress code:</strong> Clean and appropriate clothing is required when entering the shrine. For women, hijab (headscarf) is required.</p><p><strong>Behavioral rules:</strong> It is important to behave quietly within the shrine premises, avoid speaking loudly, and treat other visitors with respect.</p><p><strong>Prayer times:</strong> Five daily prayers are performed at the shrine. Visitors can join the congregational prayers.</p>',
            'content_ru'   => '<p>При посещении святыни Биби-Хейбат важно соблюдать определённые правила и этикет. Эти правила установлены для поддержания уважения как к посетителям, так и к священному месту.</p><p><strong>Дресс-код:</strong> При входе в святыню требуется чистая и подобающая одежда. Для женщин обязателен хиджаб (головной платок).</p><p><strong>Правила поведения:</strong> На территории святыни важно вести себя тихо, не разговаривать громко и относиться к другим посетителям с уважением.</p><p><strong>Время молитв:</strong> В святыне совершаются ежедневные пятикратные молитвы. Посетители могут присоединиться к коллективной молитве.</p>',
            'excerpt_az'   => 'Bibiheybət ziyarətgahını ziyarət edərkən riayət edilməli olan qaydalar, geyim tələbləri və davranış ədəbi haqqında məlumat.',
            'excerpt_en'   => 'Information about the rules, dress requirements, and behavioral etiquette to observe when visiting the Bibi-Heybat shrine.',
            'excerpt_ru'   => 'Информация о правилах, требованиях к одежде и этикете поведения при посещении святыни Биби-Хейбат.',
            'featured_image' => 'public/assets/img/iceri.webp',
            'meta_title_az'  => 'Ziyarət Qaydaları və Ədəbi | Bibiheybət',
            'meta_title_en'  => 'Pilgrimage Rules and Etiquette | Bibiheybat',
            'meta_title_ru'  => 'Правила и этикет паломничества | Бибихейбат',
            'meta_desc_az'   => 'Bibiheybət ziyarətgahını ziyarət edərkən riayət edilməli qaydalar və ədəb-ərkan haqqında tam rəhbər.',
            'meta_desc_en'   => 'A complete guide to the rules and etiquette to observe when visiting the Bibi-Heybat shrine.',
            'meta_desc_ru'   => 'Полное руководство по правилам и этикету при посещении святыни Биби-Хейбат.',
            'status'         => 'published',
            'published_at'   => '2026-02-14 09:00:00',
        ],
    ];

    $artCheckStmt = $db->prepare("SELECT id FROM articles WHERE slug_az = :slug_az");
    $artInsertSql = "INSERT INTO articles (
        category_id, slug_az, slug_en, slug_ru,
        title_az, title_en, title_ru,
        content_az, content_en, content_ru,
        excerpt_az, excerpt_en, excerpt_ru,
        featured_image,
        meta_title_az, meta_title_en, meta_title_ru,
        meta_desc_az, meta_desc_en, meta_desc_ru,
        status, published_at, created_at
    ) VALUES (
        :category_id, :slug_az, :slug_en, :slug_ru,
        :title_az, :title_en, :title_ru,
        :content_az, :content_en, :content_ru,
        :excerpt_az, :excerpt_en, :excerpt_ru,
        :featured_image,
        :meta_title_az, :meta_title_en, :meta_title_ru,
        :meta_desc_az, :meta_desc_en, :meta_desc_ru,
        :status, :published_at, NOW()
    )";
    $artInsert = $db->prepare($artInsertSql);

    foreach ($articles as $art) {
        $artCheckStmt->execute([':slug_az' => $art['slug_az']]);
        if ($artCheckStmt->fetch()) {
            echo "[!] Meqale '{$art['slug_az']}' artiq movcuddur. Kecilir.\n";
            continue;
        }
        $artInsert->execute([
            ':category_id'    => $art['category_id'],
            ':slug_az'        => $art['slug_az'],
            ':slug_en'        => $art['slug_en'],
            ':slug_ru'        => $art['slug_ru'],
            ':title_az'       => $art['title_az'],
            ':title_en'       => $art['title_en'],
            ':title_ru'       => $art['title_ru'],
            ':content_az'     => $art['content_az'],
            ':content_en'     => $art['content_en'],
            ':content_ru'     => $art['content_ru'],
            ':excerpt_az'     => $art['excerpt_az'],
            ':excerpt_en'     => $art['excerpt_en'],
            ':excerpt_ru'     => $art['excerpt_ru'],
            ':featured_image' => $art['featured_image'],
            ':meta_title_az'  => $art['meta_title_az'],
            ':meta_title_en'  => $art['meta_title_en'],
            ':meta_title_ru'  => $art['meta_title_ru'],
            ':meta_desc_az'   => $art['meta_desc_az'],
            ':meta_desc_en'   => $art['meta_desc_en'],
            ':meta_desc_ru'   => $art['meta_desc_ru'],
            ':status'         => $art['status'],
            ':published_at'   => $art['published_at'],
        ]);
        echo "[+] Meqale '{$art['slug_az']}' yaradildi.\n";
    }

    // ============================================
    // 4. Ziyaretgahlar
    // ============================================
    $pilgrimages = [
        [
            'slug_az'    => 'hz-hekime-xanim-turbesi',
            'slug_en'    => 'hz-hakimah-khatun-mausoleum',
            'slug_ru'    => 'mavzoley-hz-hakimy-hatun',
            'name_az'    => 'Hz. Həkimə Xanımın (s) türbəsi',
            'name_en'    => 'Mausoleum of Hz. Hakimah Khatun (s)',
            'name_ru'    => 'Мавзолей Хз. Хакимы Хатун (с)',
            'content_az' => '<p>Ziyarətgahın mərkəzində yerləşən bu türbə, Hz. Həkimə Xanımın (s) müqəddəs məzarı üzərində inşa edilmişdir. XIII əsrdən bəri ziyarət yeri olan türbə, 1934-cü ildə dağıdılmış və 1997-ci ildə yenidən bərpa olunmuşdur.</p>',
            'content_en' => '<p>Located at the center of the shrine complex, this mausoleum was built over the sacred grave of Hz. Hakimah Khatun (s). A pilgrimage site since the 13th century, the mausoleum was destroyed in 1934 and restored in 1997.</p>',
            'content_ru' => '<p>Расположенный в центре комплекса святыни, этот мавзолей был построен над священной могилой Хз. Хакимы Хатун (с). Место паломничества с XIII века, мавзолей был разрушен в 1934 году и восстановлен в 1997 году.</p>',
            'featured_image' => 'public/assets/img/herem-transparan.png',
            'sort_order'     => 1,
            'status'         => 'published',
        ],
        [
            'slug_az'    => 'hz-ukeyma-xanim-mezari',
            'slug_en'    => 'grave-of-hz-ukeyma-khatun',
            'slug_ru'    => 'mogila-hz-ukeymi-hatun',
            'name_az'    => 'Hz. Ükeymə Xanımın (s) məzarı',
            'name_en'    => 'Grave of Hz. Ukeyma Khatun (s)',
            'name_ru'    => 'Могила Хз. Укеймы Хатун (с)',
            'content_az' => '<p>Hz. Ükeymə Xanım (s) da Əhli-Beytdən olan müqəddəs şəxsiyyətlərdən biridir. Onun məzarı Bibiheybət ziyarətgahı kompleksinin ərazisindədir.</p>',
            'content_en' => '<p>Hz. Ukeyma Khatun (s) is also one of the sacred figures from the Ahl al-Bayt. Her grave is located within the Bibi-Heybat shrine complex.</p>',
            'content_ru' => '<p>Хз. Укейма Хатун (с) — также одна из святых личностей из Ахль аль-Бейт. Её могила находится на территории комплекса святыни Биби-Хейбат.</p>',
            'featured_image' => null,
            'sort_order'     => 2,
            'status'         => 'published',
        ],
        [
            'slug_az'    => 'imam-mehdi-otagi',
            'slug_en'    => 'imam-mahdi-room',
            'slug_ru'    => 'komnata-imama-mahdi',
            'name_az'    => 'İmam Mehdi (ə.f) otağı',
            'name_en'    => 'Imam Mahdi (a.f) Room',
            'name_ru'    => 'Комната Имама Махди (а.ф)',
            'content_az' => '<p>İmam Mehdi (ə.f) otağı ziyarətgahın xüsusi bölmələrindən biridir. Bu otaq, müsəlmanların 12-ci İmama olan sevgi və ehtiramlarının ifadəsidir.</p>',
            'content_en' => '<p>The Imam Mahdi (a.f) room is one of the special sections of the shrine. This room is an expression of Muslims\' love and respect for the 12th Imam.</p>',
            'content_ru' => '<p>Комната Имама Махди (а.ф) — один из особых разделов святыни. Эта комната является выражением любви и уважения мусульман к 12-му Имаму.</p>',
            'featured_image' => 'public/assets/img/bibi-heybet-imam-mehdi.jpg',
            'sort_order'     => 3,
            'status'         => 'published',
        ],
        [
            'slug_az'    => 'imam-huseyn-gusesi',
            'slug_en'    => 'imam-hussein-corner',
            'slug_ru'    => 'ugolok-imama-huseyna',
            'name_az'    => 'İmam Hüseyn (ə) guşəsi',
            'name_en'    => 'Imam Hussein (a) Corner',
            'name_ru'    => 'Уголок Имама Хусейна (а)',
            'content_az' => '<p>İmam Hüseyn (ə) guşəsi, Kərbəla şəhidlərinin xatirəsinə həsr olunmuş xüsusi bir bölmədir. Burada ziyarətçilər İmam Hüseynə (ə) ehtiramlarını bildirirlər.</p>',
            'content_en' => '<p>The Imam Hussein (a) Corner is a special section dedicated to the memory of the martyrs of Karbala. Here, visitors pay their respects to Imam Hussein (a).</p>',
            'content_ru' => '<p>Уголок Имама Хусейна (а) — особый раздел, посвящённый памяти мучеников Кербелы. Здесь посетители выражают своё почтение Имаму Хусейну (а).</p>',
            'featured_image' => null,
            'sort_order'     => 4,
            'status'         => 'published',
        ],
        [
            'slug_az'    => 'hz-ebulfez-abbas-gusesi',
            'slug_en'    => 'hz-abulfaz-abbas-corner',
            'slug_ru'    => 'ugolok-hz-abulfaza-abbasa',
            'name_az'    => 'Hz. Əbülfəz Abbas (ə) guşəsi',
            'name_en'    => 'Hz. Abulfaz Abbas (a) Corner',
            'name_ru'    => 'Уголок Хз. Абульфаза Аббаса (а)',
            'content_az' => '<p>Kərbəla qəhrəmanı Hz. Əbülfəz Abbasın (ə) xatirəsinə həsr olunmuş bu guşə, ziyarətçilərin ən çox sevdiyi yerlərdən biridir.</p>',
            'content_en' => '<p>This corner, dedicated to the memory of the Karbala hero Hz. Abulfaz Abbas (a), is one of the most beloved spots for visitors.</p>',
            'content_ru' => '<p>Этот уголок, посвящённый памяти героя Кербелы Хз. Абульфаза Аббаса (а), является одним из самых любимых мест посетителей.</p>',
            'featured_image' => null,
            'sort_order'     => 5,
            'status'         => 'published',
        ],
        [
            'slug_az'    => 'imam-rza-gusesi',
            'slug_en'    => 'imam-reza-corner',
            'slug_ru'    => 'ugolok-imama-rezy',
            'name_az'    => 'İmam Rza (ə) guşəsi',
            'name_en'    => 'Imam Reza (a) Corner',
            'name_ru'    => 'Уголок Имама Резы (а)',
            'content_az' => '<p>Hz. Həkimə Xanımın (s) qardaşı olan İmam Rzanın (ə) xatirəsinə həsr olunmuş xüsusi bir bölmə. Ziyarətçilər burada İmam Rzaya (ə) ehtiramlarını bildirirlər.</p>',
            'content_en' => '<p>A special section dedicated to the memory of Imam Reza (a), the brother of Hz. Hakimah Khatun (s). Visitors here pay their respects to Imam Reza (a).</p>',
            'content_ru' => '<p>Особый раздел, посвящённый памяти Имама Резы (а), брата Хз. Хакимы Хатун (с). Посетители здесь выражают своё почтение Имаму Резе (а).</p>',
            'featured_image' => null,
            'sort_order'     => 6,
            'status'         => 'published',
        ],
        [
            'slug_az'    => 'hz-zeyneb-gusesi',
            'slug_en'    => 'hz-zaynab-corner',
            'slug_ru'    => 'ugolok-hz-zeynab',
            'name_az'    => 'Hz. Zeynəb (s) guşəsi',
            'name_en'    => 'Hz. Zaynab (s) Corner',
            'name_ru'    => 'Уголок Хз. Зейнаб (с)',
            'content_az' => '<p>Hz. Zeynəbin (s) xatirəsinə həsr olunmuş bu guşə, xüsusilə qadın ziyarətçilərin çox müraciət etdiyi müqəddəs bir məkandır.</p>',
            'content_en' => '<p>This corner, dedicated to the memory of Hz. Zaynab (s), is a sacred space particularly frequented by women visitors.</p>',
            'content_ru' => '<p>Этот уголок, посвящённый памяти Хз. Зейнаб (с), является священным пространством, особенно часто посещаемым женщинами.</p>',
            'featured_image' => null,
            'sort_order'     => 7,
            'status'         => 'published',
        ],
        [
            'slug_az'    => 'bibiheybet-mescidi',
            'slug_en'    => 'bibiheybat-mosque',
            'slug_ru'    => 'mechet-bibiheybat',
            'name_az'    => 'Bibiheybət Məscidi',
            'name_en'    => 'Bibi-Heybat Mosque',
            'name_ru'    => 'Мечеть Биби-Хейбат',
            'content_az' => '<p>Bibiheybət Məscidi, XIII əsrdə Şirvanşah II Fərruxzad tərəfindən inşa etdirilmiş tarixi məsciddir. 1934-cü ildə sovet rejimi tərəfindən dağıdılmış və 1997-1998-ci illərdə yenidən bərpa olunmuşdur. Bu gün həm ibadət, həm də ziyarət məqsədilə istifadə olunur.</p>',
            'content_en' => '<p>Bibi-Heybat Mosque is a historic mosque built in the 13th century by Shirvanshah II Farrukhzad. It was destroyed by the Soviet regime in 1934 and rebuilt in 1997-1998. Today it serves both as a place of worship and pilgrimage.</p>',
            'content_ru' => '<p>Мечеть Биби-Хейбат — историческая мечеть, построенная в XIII веке Ширваншахом II Фаррухзадом. Она была разрушена советским режимом в 1934 году и перестроена в 1997–1998 годах. Сегодня она служит как местом богослужения, так и паломничества.</p>',
            'featured_image' => 'public/assets/img/Bibi-Heybat-Mosque.jpg',
            'sort_order'     => 8,
            'status'         => 'published',
        ],
    ];

    $pilgCheckStmt = $db->prepare("SELECT id FROM pilgrimages WHERE slug_az = :slug_az");
    $pilgInsertSql = "INSERT INTO pilgrimages (
        slug_az, slug_en, slug_ru,
        name_az, name_en, name_ru,
        content_az, content_en, content_ru,
        featured_image,
        sort_order, status, created_at
    ) VALUES (
        :slug_az, :slug_en, :slug_ru,
        :name_az, :name_en, :name_ru,
        :content_az, :content_en, :content_ru,
        :featured_image,
        :sort_order, :status, NOW()
    )";
    $pilgInsert = $db->prepare($pilgInsertSql);

    foreach ($pilgrimages as $pilg) {
        $pilgCheckStmt->execute([':slug_az' => $pilg['slug_az']]);
        if ($pilgCheckStmt->fetch()) {
            echo "[!] Ziyaretgah '{$pilg['slug_az']}' artiq movcuddur. Kecilir.\n";
            continue;
        }
        $pilgInsert->execute([
            ':slug_az'        => $pilg['slug_az'],
            ':slug_en'        => $pilg['slug_en'],
            ':slug_ru'        => $pilg['slug_ru'],
            ':name_az'        => $pilg['name_az'],
            ':name_en'        => $pilg['name_en'],
            ':name_ru'        => $pilg['name_ru'],
            ':content_az'     => $pilg['content_az'],
            ':content_en'     => $pilg['content_en'],
            ':content_ru'     => $pilg['content_ru'],
            ':featured_image' => $pilg['featured_image'],
            ':sort_order'     => $pilg['sort_order'],
            ':status'         => $pilg['status'],
        ]);
        echo "[+] Ziyaretgah '{$pilg['slug_az']}' yaradildi.\n";
    }

    echo "\nSeed tamamlandi!\n";

} catch (PDOException $e) {
    echo "[XETA] Database xetasi: " . $e->getMessage() . "\n";
    exit(1);
}
