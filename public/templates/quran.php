<?php
/**
 * Bibiheybet.com - Quran Dinlə Səhifəsi
 * 
 * quran.com API v4 ilə 114 surənin audio tilavəti.
 * Qari seçimi, surə siyahısı, audio player.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

$titles = [
    'az' => 'Quran dinlə',
    'en' => 'Listen to Quran',
    'ru' => 'Слушать Коран',
    'ar' => 'استماع القرآن',
    'fa' => 'گوش دادن به قرآن',
];

$descriptions = [
    'az' => 'Qurani-Kərimi tanınmış qarilər tərəfindən dinləyin. 114 surənin tam audio tilavəti.',
    'en' => 'Listen to the Holy Quran recited by renowned reciters. Full audio recitation of all 114 surahs.',
    'ru' => 'Слушайте Священный Коран в исполнении известных чтецов. Полная аудио-рецитация всех 114 сур.',
    'ar' => 'استمع إلى القرآن الكريم بأصوات أشهر القراء. تلاوة صوتية كاملة لجميع السور الـ ١١٤.',
    'fa' => 'قرآن کریم را با صدای قاریان مشهور بشنوید. تلاوت کامل صوتی تمام ۱۱۴ سوره.',
];

$title = $titles[$lang] ?? $titles['az'];
$description = $descriptions[$lang] ?? $descriptions['az'];

$seoData = [
    'title'            => $title . ' — ' . SITE_NAME,
    'meta_description' => $description,
    'lang'             => $lang,
    'og_type'          => 'website',
    'schema_type'      => 'WebPage',
    'canonical_url'    => bb_lang_url(bb_get_route('quran-listen', $lang) . '/', $lang),
    'alternate_urls'   => array_combine(
        bb_all_langs(),
        array_map(fn($l) => bb_lang_url(bb_get_route('quran-listen', $l) . '/', $l), bb_all_langs())
    ),
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-static bb-page-quran',
    'extra_css'  => ['/public/assets/css/quran.css'],
]);

$labels = [
    'az' => [
        'select_reciter'  => 'Qari seçin',
        'search'          => 'Surə axtar...',
        'verses'          => 'ayə',
        'meccan'          => 'Məkkə',
        'medinan'         => 'Mədinə',
        'loading'         => 'Yüklənir...',
        'play'            => 'Oxut',
        'pause'           => 'Dayandır',
        'now_playing'     => 'İndi oxunur',
        'no_results'      => 'Nəticə tapılmadı',
        'error'           => 'Xəta baş verdi. Zəhmət olmasa yenidən cəhd edin.',
        'all_surahs'      => 'Bütün surələr',
    ],
    'en' => [
        'select_reciter'  => 'Select reciter',
        'search'          => 'Search surah...',
        'verses'          => 'verses',
        'meccan'          => 'Meccan',
        'medinan'         => 'Medinan',
        'loading'         => 'Loading...',
        'play'            => 'Play',
        'pause'           => 'Pause',
        'now_playing'     => 'Now playing',
        'no_results'      => 'No results found',
        'error'           => 'An error occurred. Please try again.',
        'all_surahs'      => 'All surahs',
    ],
    'ru' => [
        'select_reciter'  => 'Выберите чтеца',
        'search'          => 'Поиск суры...',
        'verses'          => 'аятов',
        'meccan'          => 'Мекканская',
        'medinan'         => 'Мединская',
        'loading'         => 'Загрузка...',
        'play'            => 'Воспроизвести',
        'pause'           => 'Пауза',
        'now_playing'     => 'Сейчас играет',
        'no_results'      => 'Ничего не найдено',
        'error'           => 'Произошла ошибка. Попробуйте ещё раз.',
        'all_surahs'      => 'Все суры',
    ],
    'ar' => [
        'select_reciter'  => 'اختر القارئ',
        'search'          => 'ابحث عن سورة...',
        'verses'          => 'آية',
        'meccan'          => 'مكية',
        'medinan'         => 'مدنية',
        'loading'         => 'جاري التحميل...',
        'play'            => 'تشغيل',
        'pause'           => 'إيقاف',
        'now_playing'     => 'يتم الآن',
        'no_results'      => 'لم يتم العثور على نتائج',
        'error'           => 'حدث خطأ. يرجى المحاولة مرة أخرى.',
        'all_surahs'      => 'جميع السور',
    ],
    'fa' => [
        'select_reciter'  => 'قاری را انتخاب کنید',
        'search'          => 'جستجوی سوره...',
        'verses'          => 'آیه',
        'meccan'          => 'مکی',
        'medinan'         => 'مدنی',
        'loading'         => 'در حال بارگذاری...',
        'play'            => 'پخش',
        'pause'           => 'توقف',
        'now_playing'     => 'در حال پخش',
        'no_results'      => 'نتیجه‌ای یافت نشد',
        'error'           => 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
        'all_surahs'      => 'همه سوره‌ها',
    ],
];

$l = $labels[$lang] ?? $labels['az'];
?>

    <div class="bb-container">
        <?= bb_render_breadcrumbs([
            ['label' => ['az'=>'Ana səhifə','en'=>'Home','ru'=>'Главная','ar'=>'الرئيسية','fa'=>'خانه'][$lang] ?? 'Ana səhifə', 'url' => bb_lang_url('', $lang)],
            ['label' => $title],
        ]) ?>
    </div>

    <section class="bb-section bb-quran-section">
        <div class="bb-container">

            <!-- Başlıq -->
            <div class="bb-quran-header">
                <h1><?= bb_sanitize($title) ?></h1>
                <div class="bb-separator"></div>
                <p class="bb-text-muted"><?= bb_sanitize($description) ?></p>
            </div>

            <!-- Audio Player (surə seçildikcə görünəcək) -->
            <div class="bb-quran-player" id="bbQuranPlayer" style="display:none;">
                <div class="bb-quran-player-inner">
                    <div class="bb-quran-player-info">
                        <span class="bb-quran-player-surah" id="bbQuranPlayerSurah"></span>
                        <span class="bb-quran-player-reciter" id="bbQuranPlayerReciter"></span>
                    </div>
                    <div class="bb-quran-player-controls">
                        <button type="button" class="bb-quran-ctrl-btn" id="bbQuranPrev" aria-label="Previous">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                        </button>
                        <button type="button" class="bb-quran-play-btn" id="bbQuranPlayBtn" aria-label="<?= bb_sanitize($l['play']) ?>">
                            <svg class="bb-quran-icon-play" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <svg class="bb-quran-icon-pause" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="display:none">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                            <svg class="bb-quran-icon-loading" width="22" height="22" viewBox="0 0 24 24" fill="none" style="display:none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="31.4 31.4" stroke-linecap="round">
                                    <animateTransform attributeName="transform" type="rotate" values="0 12 12;360 12 12" dur="1s" repeatCount="indefinite"/>
                                </circle>
                            </svg>
                        </button>
                        <button type="button" class="bb-quran-ctrl-btn" id="bbQuranNext" aria-label="Next">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                        </button>
                    </div>
                    <div class="bb-quran-player-time">
                        <span id="bbQuranCurrentTime">0:00</span>
                        <div class="bb-quran-progress" id="bbQuranProgress">
                            <div class="bb-quran-progress-bar" id="bbQuranProgressBar"></div>
                        </div>
                        <span id="bbQuranDuration">0:00</span>
                    </div>
                </div>
            </div>

            <!-- Kontrol paneli: qari seçimi + axtar -->
            <div class="bb-quran-controls">
                <div class="bb-quran-select-wrap">
                    <select id="bbReciterSelect" class="bb-quran-select" aria-label="<?= bb_sanitize($l['select_reciter']) ?>">
                        <option value=""><?= bb_sanitize($l['select_reciter']) ?></option>
                    </select>
                </div>
                <div class="bb-quran-search-wrap">
                    <svg class="bb-quran-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="bbSurahSearch" class="bb-quran-search" placeholder="<?= bb_sanitize($l['search']) ?>" autocomplete="off">
                </div>
            </div>

            <!-- Surə siyahısı -->
            <div class="bb-quran-grid" id="bbSurahGrid">
                <div class="bb-quran-loading" id="bbQuranLoading">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="var(--bb-color-accent)" stroke-width="2" stroke-dasharray="31.4 31.4" stroke-linecap="round">
                            <animateTransform attributeName="transform" type="rotate" values="0 12 12;360 12 12" dur="1s" repeatCount="indefinite"/>
                        </circle>
                    </svg>
                    <span><?= bb_sanitize($l['loading']) ?></span>
                </div>
            </div>

            <!-- Boş nəticə -->
            <div class="bb-quran-empty" id="bbQuranEmpty" style="display:none;">
                <p><?= bb_sanitize($l['no_results']) ?></p>
            </div>

        </div>
    </section>

    <script>
        window.BB_QURAN_LABELS = <?= json_encode($l, JSON_UNESCAPED_UNICODE) ?>;
        window.BB_QURAN_LANG = <?= json_encode($lang) ?>;
    </script>

<?php
bb_frontend_footer([
    'extra_js' => [
        '/public/assets/js/quran.js',
    ],
]);
