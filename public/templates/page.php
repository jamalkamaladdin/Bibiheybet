<?php
/**
 * Bibiheybet.com - Statik Səhifə Template
 * 
 * FAZA 10: Həzrət haqqında, Məscid haqqında, Dua və ziyarətnamə.
 * Tam dizayn: hero, kontent, əlaqəli linklər.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// Statik səhifə data (hər dil üçün)
$staticPages = [
    'about-hazrat' => [
        'title' => [
            'az' => 'Həzrət haqqında',
            'en' => 'About the Holy Lady',
            'ru' => 'О Её Светлости',
            'ar' => 'نبذة عن السيدة حكيمة',
            'fa' => 'درباره حضرت حکیمه',
        ],
        'description' => [
            'az' => 'Hz. Həkimə Xanım (s) - İmam Musa Kazımın (ə.s.) qızı və İmam Əli Rzanın (ə.s.) bacısı haqqında ətraflı məlumat.',
            'en' => 'Detailed information about Hz. Hakimah Khatun (s) - daughter of Imam Musa al-Kazim (a.s.) and sister of Imam Ali al-Ridha (a.s.).',
            'ru' => 'Подробная информация о Хз. Хакиме Хатун (с) — дочери Имама Мусы аль-Казима (а.с.) и сестре Имама Али ар-Ризы (а.с.).',
            'ar' => 'معلومات مفصلة عن السيدة حكيمة خاتون (ع) - ابنة الإمام موسى الكاظم (ع) وأخت الإمام علي الرضا (ع).',
            'fa' => 'اطلاعات تفصیلی درباره حضرت حکیمه خاتون (س) - دختر امام موسی کاظم (ع) و خواهر امام علی رضا (ع).',
        ],
        'icon' => 'star',
        'content' => [
            'az' => '<p>Həkimə xanım, 9-cu əsrdə yaşamış, İmam Musa Kazımın (ə.s.) qızı və İmam Əli Rzanın (ə.s.) bacısıdır. O, Əhli-Beytin (ə.s.) mübarək nəslindən olan və İslam tarixində mühüm yer tutan şəxsiyyətlərdən biridir.</p>

<h2>Həyatı və nəsli</h2>
<p>Həkimə xanım (s), yeddinci İmam Musa ibn Cəfər əl-Kazımın (ə.s.) qızı kimi dünyaya gəlmişdir. Atası İmam Kazım (ə.s.) Əhli-Beyt İmamlarının yeddincisi, anası isə nəcib bir xanım olmuşdur. Qardaşı səkkizinci İmam Əli ibn Musa ər-Rza (ə.s.) ilə birlikdə böyümüş və İslami təhsil almışdır.</p>

<h2>Bakıya hicrəti</h2>
<p>Abbasi xəlifələrinin Əhli-Beyt övladlarına qarşı apardığı amansız təqib və təzyiqlər nəticəsində, Həkimə xanım (s) digər Əhli-Beyt üzvləri kimi müxtəlif ölkələrə sığınmaq məcburiyyətində qalmışdır. O, İranın şimalından keçərək Azərbaycana, Bakı şəhərinə gəlmiş və burada məskunlaşmışdır.</p>

<p>Bakıda yerli əhali tərəfindən böyük hörmət və ehtiramla qarşılanan Həkimə xanım (s), "Heybət xanımın bibisi" adı ilə tanınmışdır. Məhz bu addan "Bibiheybət" sözü yaranmışdır.</p>

<h2>Vəfatı və türbəsi</h2>
<p>Həkimə xanım (s) Bakıda vəfat etmiş və bu müqəddəs yerdə dəfn olunmuşdur. Zamanla onun məzarı üzərində kiçik bir ziyarətgah tikilmiş, sonralar isə bu ziyarətgah böyük bir məscid-kompleksinə çevrilmişdir.</p>

<p>Əsrlər boyu müsəlmanlar bu müqəddəs məkanı ziyarət etmiş, burada dua və ibadət etmişlər. Həkimə xanımın (s) məzarı bu gün də minlərlə insanın ziyarət etdiyi mübarək bir məkandır.</p>',

            'en' => '<p>Lady Hakima was a 9th-century figure, the daughter of Imam Musa al-Kazim (a.s.) and the sister of Imam Ali al-Ridha (a.s.). She is one of the distinguished personalities from the blessed lineage of Ahlul-Bayt (a.s.) who holds a significant place in Islamic history.</p>

<h2>Life and lineage</h2>
<p>Lady Hakima (s) was born as the daughter of the seventh Imam, Musa ibn Ja\'far al-Kazim (a.s.). Her father was the seventh of the Ahlul-Bayt Imams, and her mother was a noble lady. She grew up alongside her brother, the eighth Imam Ali ibn Musa al-Ridha (a.s.), and received an Islamic education.</p>

<h2>Migration to Baku</h2>
<p>Due to the relentless persecution and pressure carried out by the Abbasid caliphs against the descendants of Ahlul-Bayt, Lady Hakima (s), like other members of Ahlul-Bayt, was forced to seek refuge in various countries. She traveled through northern Iran to Azerbaijan, arriving in the city of Baku, where she settled.</p>

<p>In Baku, Lady Hakima (s) was received with great respect and reverence by the local population and became known as "the aunt of Heybat Khanum." It is from this name that the word "Bibiheybat" originated.</p>

<h2>Passing and mausoleum</h2>
<p>Lady Hakima (s) passed away in Baku and was buried at this sacred site. Over time, a small shrine was built over her grave, which later developed into a large mosque complex.</p>

<p>Throughout the centuries, Muslims have visited this holy place, offering prayers and worship. The tomb of Lady Hakima (s) remains a blessed place visited by thousands of people to this day.</p>',

            'ru' => '<p>Хакима ханым — религиозная деятельница IX века, дочь Имама Мусы аль-Казима (а.с.) и сестра Имама Али ар-Ризы (а.с.). Она является одной из выдающихся личностей благословенного рода Ахль аль-Бейт (а.с.), занимающих значительное место в истории Ислама.</p>

<h2>Жизнь и происхождение</h2>
<p>Хакима ханым (с) родилась как дочь седьмого Имама Мусы ибн Джафара аль-Казима (а.с.). Её отец был седьмым из Имамов Ахль аль-Бейт, а мать — знатной женщиной. Она выросла вместе со своим братом, восьмым Имамом Али ибн Мусой ар-Ризой (а.с.), и получила исламское образование.</p>

<h2>Переселение в Баку</h2>
<p>Вследствие неустанных преследований и давления, оказываемого аббасидскими халифами на потомков Ахль аль-Бейт, Хакима ханым (с), как и другие члены Ахль аль-Бейт, была вынуждена искать убежище в различных странах. Она прошла через Северный Иран в Азербайджан, прибыв в город Баку, где и обосновалась.</p>

<p>В Баку Хакима ханым (с) была встречена местным населением с большим уважением и почтением и стала известна как «тётя Хейбат ханым». Именно от этого имени произошло слово «Бибиэйбат».</p>

<h2>Кончина и мавзолей</h2>
<p>Хакима ханым (с) скончалась в Баку и была похоронена на этом священном месте. Со временем над её могилой была воздвигнута небольшая святыня, которая впоследствии превратилась в большой мечетный комплекс.</p>

<p>На протяжении веков мусульмане посещали это святое место, совершая молитвы и поклонение. Гробница Хакимы ханым (с) по сей день остаётся благословенным местом, которое посещают тысячи людей.</p>',
        ],
    ],
    'about-mosque' => [
        'title' => [
            'az' => 'Məscid haqqında',
            'en' => 'About the Mosque',
            'ru' => 'О Мечети',
            'ar' => 'عن المسجد',
            'fa' => 'درباره مسجد',
        ],
        'description' => [
            'az' => 'Bibiheybət Məscidinin tarixi, memarlığı və yenidən qurulması haqqında.',
            'en' => 'About the history, architecture and reconstruction of Bibiheybat Mosque.',
            'ru' => 'Об истории, архитектуре и восстановлении мечети Бибиэйбат.',
            'ar' => 'عن تاريخ وعمارة وإعادة بناء مسجد بيبي حيبات.',
            'fa' => 'درباره تاریخ، معماری و بازسازی مسجد بی‌بی حیبات.',
        ],
        'icon' => 'mosque',
        'content' => [
            'az' => '<p>Bibiheybət məscidi — Bakının ən qədim və ən müqəddəs dini abidələrindən biridir. XIII əsrdə tikilmiş bu əzəmətli ibadətgah, zəngin tarixi ilə Azərbaycanın mənəvi irsinin ayrılmaz hissəsidir.</p>

<h2>Tarixi</h2>
<p>Bibiheybət məscidi XIII əsrdə Şirvanşah II Fərruxzad ibn Axsitan tərəfindən inşa etdirilib. 1281-1282-ci illərdə tikilən məscidin divarındakı tarixi kitabələrdə onun memarının Mahmud ibn Sədi olduğu qeyd edilib.</p>

<p>Əsrlər boyu məscid müsəlmanların ən mühüm ziyarətgahlarından biri olmuş, burada ibadət edən insanların sayı durmadan artmışdır.</p>

<h2>Dağıdılması (1934)</h2>
<p>1934-cü ilin sentyabr ayında kommunist rejimi tərəfindən ziyarətgah dağıdılaraq yerlə-yeksan edilib. Bu, Sovet hakimiyyətinin din əleyhinə kampaniyasının bir hissəsi idi. Amma qadağalara baxmayaraq, inanclı insanlar yenə bu müqəddəs yerləri unutmayıblar.</p>

<h2>Yenidən qurulması (1997-1998)</h2>
<p>1997-ci il iyulun 23-də Həzrəti Peyğəmbərin (s) mövludu günü məscidin təməli qoyulur. Ulu öndər Heydər Əliyevin rəhbərliyi altında yenidən inşa işləri başlanmışdır. Tikinti işləri bir ilə başa çatır və məscid möminlərin ixtiyarına verilir.</p>

<p>Yenidən qurulan məscid, orijinal memarlıq üslubuna sadiq qalaraq, müasir tikinti texnologiyaları ilə inşa edilmişdir. Bu gün Bibiheybət Məscidi Azərbaycanın ən gözəl dini abidələrindən biri hesab olunur.</p>

<h2>Memarlığı</h2>
<p>Məscidin memarlığı Şirvanşahlar dövrü Azərbaycan memarlığının ən gözəl nümunələrindən biridir. Günbəzlər, minarələr və nəfis ornamentlər kompleksin əzəmətini artırır. Daxili hissədə isə gözəl xətlə yazılmış Quran ayələri və İslami ornamentlər diqqəti cəlb edir.</p>',

            'en' => '<p>Bibi-Heybat Mosque is one of the oldest and most sacred religious monuments of Baku. Built in the 13th century, this majestic place of worship, with its rich history, is an integral part of Azerbaijan\'s spiritual heritage.</p>

<h2>History</h2>
<p>Bibi-Heybat Mosque was built in the 13th century by Shirvanshah II Farrukhzad ibn Akhsitan. Historical inscriptions on the mosque\'s walls, constructed between 1281 and 1282, state that its architect was Mahmud ibn Sadi.</p>

<p>Throughout the centuries, the mosque has been one of the most important pilgrimage sites for Muslims, with an ever-growing number of worshippers.</p>

<h2>Destruction (1934)</h2>
<p>In September 1934, the Communist regime destroyed the shrine, reducing it to ruins. This was part of the Soviet anti-religious campaign. Despite these prohibitions, devout people never forgot this sacred place.</p>

<h2>Reconstruction (1997-1998)</h2>
<p>On July 23, 1997, coinciding with the Prophet\'s (s) birthday, the foundation of the mosque was laid. Reconstruction work began under the leadership of the great leader Heydar Aliyev. Construction was completed within a year, and the mosque was returned to the worshippers.</p>

<p>The reconstructed mosque was built using modern construction technologies while remaining faithful to the original architectural style. Today, Bibi-Heybat Mosque is considered one of the most beautiful religious monuments in Azerbaijan.</p>

<h2>Architecture</h2>
<p>The mosque\'s architecture is one of the finest examples of Shirvanshah-era Azerbaijani architecture. Domes, minarets, and exquisite ornaments enhance the grandeur of the complex. Inside, beautifully calligraphed Quranic verses and Islamic ornaments capture attention.</p>',

            'ru' => '<p>Мечеть Биби-Хейбат — одна из старейших и наиболее священных религиозных памятников Баку. Построенное в XIII веке, это величественное место поклонения с его богатой историей является неотъемлемой частью духовного наследия Азербайджана.</p>

<h2>История</h2>
<p>Мечеть Биби-Хейбат была построена в XIII веке Ширваншахом II Фаррухзадом ибн Ахситаном. Исторические надписи на стенах мечети, возведённой в 1281–1282 годах, указывают, что архитектором был Махмуд ибн Сади.</p>

<p>На протяжении веков мечеть была одним из важнейших мест паломничества мусульман, с постоянно растущим числом верующих.</p>

<h2>Разрушение (1934)</h2>
<p>В сентябре 1934 года коммунистический режим разрушил святыню, полностью её уничтожив. Это было частью советской антирелигиозной кампании. Но несмотря на запреты, верующие никогда не забывали это святое место.</p>

<h2>Восстановление (1997-1998)</h2>
<p>23 июля 1997 года, в день рождения Пророка (с), был заложен фундамент новой мечети. Работы по восстановлению начались под руководством великого лидера Гейдара Алиева. Строительство завершилось в течение года, и мечеть вновь стала доступна для прихожан.</p>

<p>Восстановленная мечеть была построена с использованием современных строительных технологий, оставаясь верной оригинальному архитектурному стилю. Сегодня мечеть Биби-Хейбат считается одним из красивейших религиозных памятников Азербайджана.</p>

<h2>Архитектура</h2>
<p>Архитектура мечети является одним из лучших образцов азербайджанской архитектуры эпохи Ширваншахов. Купола, минареты и изящные орнаменты усиливают величие комплекса. Внутри привлекают внимание красиво каллиграфированные аяты Корана и исламские орнаменты.</p>',
        ],
    ],
    'prayers' => [
        'title' => [
            'az' => 'Dua və ziyarətnamə',
            'en' => 'Prayers and Ziyarat Texts',
            'ru' => 'Молитвы и зиярат',
            'ar' => 'الأدعية والزيارات',
            'fa' => 'دعاها و زیارت‌نامه‌ها',
        ],
        'description' => [
            'az' => 'Bibiheybət ziyarətgahında oxunan dua və ziyarətnamə mətnləri.',
            'en' => 'Prayer and pilgrimage texts recited at Bibiheybat Shrine.',
            'ru' => 'Тексты молитв и зияратов, читаемых в святыне Бибиэйбат.',
            'ar' => 'نصوص الأدعية والزيارات التي تُقرأ في مزار بيبي حيبات.',
            'fa' => 'متون دعاها و زیارت‌نامه‌هایی که در زیارتگاه بی‌بی حیبات خوانده می‌شود.',
        ],
        'icon' => 'prayer',
        'content' => [
            'az' => '<h2>Ziyarətnamə</h2>
<p>Bu ziyarətnamə, Hz. Həkimə Xanımın (s) müqəddəs türbəsini ziyarət edərkən oxunur:</p>

<div class="bb-arabic-text bb-prayer-text">
<p>اَلسَّلامُ عَلَيْکِ يا بِنْتَ مُوسَى بْنِ جَعْفَرٍ</p>
<p>اَلسَّلامُ عَلَيْکِ يا اُخْتَ عَلِيِّ بْنِ مُوسَى الرِّضا</p>
<p>اَلسَّلامُ عَلَيْکِ يا حَكيمَةَ خاتون</p>
<p>اَلسَّلامُ عَلَيْکِ وَ رَحْمَةُ اللهِ وَ بَرَكاتُهُ</p>
</div>

<p class="bb-prayer-transliteration"><em>Əssəlamu əleyki ya bintə Musa ibni Cəfər.<br>
Əssəlamu əleyki ya uxtə Əliyyi ibni Musar-Rza.<br>
Əssəlamu əleyki ya Həkimətu Xatun.<br>
Əssəlamu əleyki və rəhmətullahi və bərəkatuh.</em></p>

<h3>Tərcümə</h3>
<p>Salam olsun sənə, ey Musa ibn Cəfərin qızı!<br>
Salam olsun sənə, ey Əli ibn Musa Rzanın bacısı!<br>
Salam olsun sənə, ey Həkimə Xatun!<br>
Salam olsun sənə, Allahın rəhməti və bərəkəti olsun!</p>

<h2>Ziyarət qaydaları</h2>
<p>Ziyarətgaha daxil olarkən aşağıdakı qaydalara riayət etmək tövsiyə olunur:</p>
<ul>
<li>Dəstəmaz almaq</li>
<li>Təmiz və münasib geyim geymək</li>
<li>Sakitliyi qorumaq</li>
<li>İki rəkət ziyarət namazı qılmaq</li>
<li>Dua və ziyarətnamə oxumaq</li>
</ul>',

            'en' => '<h2>Ziyarat text</h2>
<p>This ziyarat text is recited when visiting the holy mausoleum of Hz. Hakimah Khatun (s):</p>

<div class="bb-arabic-text bb-prayer-text">
<p>اَلسَّلامُ عَلَيْکِ يا بِنْتَ مُوسَى بْنِ جَعْفَرٍ</p>
<p>اَلسَّلامُ عَلَيْکِ يا اُخْتَ عَلِيِّ بْنِ مُوسَى الرِّضا</p>
<p>اَلسَّلامُ عَلَيْکِ يا حَكيمَةَ خاتون</p>
<p>اَلسَّلامُ عَلَيْکِ وَ رَحْمَةُ اللهِ وَ بَرَكاتُهُ</p>
</div>

<p class="bb-prayer-transliteration"><em>Assalamu alayki ya binta Musa ibni Ja\'far.<br>
Assalamu alayki ya ukhta Aliyyi ibni Musa ar-Ridha.<br>
Assalamu alayki ya Hakimatu Khatun.<br>
Assalamu alayki wa rahmatullahi wa barakatuh.</em></p>

<h3>Translation</h3>
<p>Peace be upon you, O daughter of Musa ibn Ja\'far!<br>
Peace be upon you, O sister of Ali ibn Musa al-Ridha!<br>
Peace be upon you, O Hakimah Khatun!<br>
Peace be upon you, and may the mercy and blessings of God be upon you!</p>

<h2>Visiting guidelines</h2>
<p>When entering the shrine, it is recommended to observe the following guidelines:</p>
<ul>
<li>Perform ablution (wudu)</li>
<li>Wear clean and appropriate clothing</li>
<li>Maintain silence</li>
<li>Perform two units of visitation prayer</li>
<li>Recite supplications and ziyarat text</li>
</ul>',

            'ru' => '<h2>Текст зиярата</h2>
<p>Этот текст зиярата читается при посещении священного мавзолея Хз. Хакимы Хатун (с):</p>

<div class="bb-arabic-text bb-prayer-text">
<p>اَلسَّلامُ عَلَيْکِ يا بِنْتَ مُوسَى بْنِ جَعْفَرٍ</p>
<p>اَلسَّلامُ عَلَيْکِ يا اُخْتَ عَلِيِّ بْنِ مُوسَى الرِّضا</p>
<p>اَلسَّلامُ عَلَيْکِ يا حَكيمَةَ خاتون</p>
<p>اَلسَّلامُ عَلَيْکِ وَ رَحْمَةُ اللهِ وَ بَرَكاتُهُ</p>
</div>

<p class="bb-prayer-transliteration"><em>Ассаламу алейки я бинта Муса ибни Джафар.<br>
Ассаламу алейки я ухта Алийи ибни Муса ар-Рида.<br>
Ассаламу алейки я Хакимату Хатун.<br>
Ассаламу алейки ва рахматуллахи ва баракатух.</em></p>

<h3>Перевод</h3>
<p>Мир тебе, о дочь Мусы ибн Джафара!<br>
Мир тебе, о сестра Али ибн Мусы ар-Ризы!<br>
Мир тебе, о Хакима Хатун!<br>
Мир тебе, милость Аллаха и Его благословения!</p>

<h2>Правила посещения</h2>
<p>При входе в святыню рекомендуется соблюдать следующие правила:</p>
<ul>
<li>Совершить омовение (вуду)</li>
<li>Быть в чистой и подобающей одежде</li>
<li>Сохранять тишину</li>
<li>Совершить два раката молитвы посещения</li>
<li>Прочитать мольбы и текст зиярата</li>
</ul>',
        ],
    ],
];

$pageInfo = $staticPages[$pageSlug] ?? null;

if (!$pageInfo) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

// DB-dən override-ları yüklə (fallback: hardcoded dəyərlər)
$dbContents = bb_load_page_contents($db, $pageSlug, [
    'title'       => $pageInfo['title'] ?? [],
    'description' => $pageInfo['description'] ?? [],
    'content'     => $pageInfo['content'] ?? [],
]);

$title = $dbContents['title'][$lang] ?? $dbContents['title']['az'] ?? ($pageInfo['title'][$lang] ?? $pageInfo['title']['az']);
$description = $dbContents['description'][$lang] ?? $dbContents['description']['az'] ?? ($pageInfo['description'][$lang] ?? $pageInfo['description']['az']);
$content = $dbContents['content'][$lang] ?? $dbContents['content']['az'] ?? ($pageInfo['content'][$lang] ?? $pageInfo['content']['az'] ?? '');

$seoData = [
    'title'            => $title,
    'meta_description' => $description,
    'lang'             => $lang,
    'og_type'          => 'website',
    'schema_type'      => 'WebPage',
    'canonical_url'    => bb_lang_url(bb_get_route($pageSlug, $lang) . '/', $lang),
    'alternate_urls'   => array_combine(
        bb_all_langs(),
        array_map(fn($l) => bb_lang_url(bb_get_route($pageSlug, $l) . '/', $l), bb_all_langs())
    ),
];

// Əlaqəli linklər (digər statik səhifələr)
$relatedLinks = [];
foreach ($staticPages as $slug => $info) {
    if ($slug !== $pageSlug) {
        $relatedLinks[] = [
            'title' => $info['title'][$lang] ?? $info['title']['az'],
            'url'   => bb_lang_url(bb_get_route($slug, $lang) . '/', $lang),
        ];
    }
}

$_strings = [
    'related' => [
        'az' => 'Digər bölmələr',
        'en' => 'Other sections',
        'ru' => 'Другие разделы',
        'ar' => 'أقسام أخرى',
        'fa' => 'بخش‌های دیگر',
    ],
];

$t = function (string $key) use ($_strings, $lang): string {
    return $_strings[$key][$lang] ?? $_strings[$key]['az'] ?? '';
};

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-static bb-page-' . $pageSlug,
    'extra_css'  => ['/public/assets/css/pages.css'],
]);
?>

    <div class="bb-container">
        <?= bb_render_breadcrumbs([
            ['label' => ['az'=>'Ana səhifə','en'=>'Home','ru'=>'Главная','ar'=>'الرئيسية','fa'=>'خانه'][$lang] ?? 'Ana səhifə', 'url' => bb_lang_url('', $lang)],
            ['label' => $title],
        ]) ?>
    </div>

    <!-- Statik səhifə hero -->
    <section class="bb-static-hero" data-animate>
        <div class="bb-container bb-text-center">
            <div class="bb-static-ornament" aria-hidden="true">
                <img src="/public/assets/img/naxis.png" alt="">
            </div>
            <h1 class="bb-static-title"><?= bb_sanitize($title) ?></h1>
            <div class="bb-separator bb-separator-center"></div>
            <p class="bb-static-description"><?= bb_sanitize($description) ?></p>
        </div>
    </section>

    <!-- Kontent -->
    <section class="bb-static-content-section">
        <div class="bb-container-narrow">
            <div class="bb-static-body bb-article-content">
                <?= $content ?>
            </div>
        </div>
    </section>

    <!-- Əlaqəli linklər -->
    <?php if (!empty($relatedLinks)): ?>
    <section class="bb-static-related" data-animate>
        <div class="bb-container">
            <h2 class="bb-static-related-title bb-text-center"><?= bb_sanitize($t('related')) ?></h2>
            <div class="bb-separator bb-separator-center"></div>
            <div class="bb-static-related-grid">
                <?php foreach ($relatedLinks as $link): ?>
                <a href="<?= bb_sanitize($link['url']) ?>" class="bb-static-related-card">
                    <span class="bb-static-related-card-text"><?= bb_sanitize($link['title']) ?></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php
bb_frontend_footer(['extra_js' => ['/public/assets/js/home.js']]);
