<?php
/**
 * Bibiheybet.com - Namaz Vaxtları Səhifəsi
 * 
 * PrayTimes kitabxanası ilə Cəfəri firqəsinə uyğun namaz vaxtları.
 * Azərbaycanın bütün rayonları üçün — bu günün vaxtları + aylıq cədvəl.
 */

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

// Çoxdilli başlıq və təsvir
$titles = [
    'az' => 'Namaz vaxtları',
    'en' => 'Prayer Times',
    'ru' => 'Время намаза',
];

$descriptions = [
    'az' => 'Cəfəri məzhəbinə uyğun Azərbaycanın bütün şəhər və rayonları üçün namaz vaxtları.',
    'en' => 'Prayer times for all cities and regions of Azerbaijan according to the Jafari school.',
    'ru' => 'Время намаза для всех городов и районов Азербайджана по джафаритскому мазхабу.',
];

$title = $titles[$lang] ?? $titles['az'];
$description = $descriptions[$lang] ?? $descriptions['az'];

// SEO data
$seoData = [
    'title'            => $title . ' — ' . SITE_NAME,
    'meta_description' => $description,
    'lang'             => $lang,
    'og_type'          => 'website',
    'schema_type'      => 'WebPage',
    'canonical_url'    => bb_lang_url(bb_get_route('prayer-times', $lang) . '/', $lang),
    'alternate_urls'   => array_combine(
        bb_all_langs(),
        array_map(fn($l) => bb_lang_url(bb_get_route('prayer-times', $l) . '/', $l), bb_all_langs())
    ),
];

bb_frontend_header([
    'seo_data'   => $seoData,
    'body_class' => 'bb-page-static bb-page-prayer-times',
    'extra_css'  => ['/public/assets/css/prayer-times.css'],
]);

// Çoxdilli etiketlər
$labels = [
    'az' => [
        'select_region'    => 'Şəhər və ya rayon seçin',
        'fajr'             => 'Sübh',
        'sunrise'          => 'Günəş',
        'dhuhr'            => 'Zöhr',
        'asr'              => 'Əsr',
        'sunset'           => 'Qürub',
        'maghrib'          => 'Məğrib',
        'isha'             => 'İşa',
        'midnight'         => 'Gecə yarısı',
        'method_note'      => 'Hesablama metodu: Cəfəri (Leva Tədqiqat İnstitutu, Qum)',
        'today'            => 'Bu gün',
        'monthly_schedule' => 'Aylıq cədvəl',
        'day'              => 'Gün',
    ],
    'en' => [
        'select_region'    => 'Select city or region',
        'fajr'             => 'Fajr',
        'sunrise'          => 'Sunrise',
        'dhuhr'            => 'Dhuhr',
        'asr'              => 'Asr',
        'sunset'           => 'Sunset',
        'maghrib'          => 'Maghrib',
        'isha'             => 'Isha',
        'midnight'         => 'Midnight',
        'method_note'      => 'Calculation method: Jafari (Leva Research Institute, Qom)',
        'today'            => 'Today',
        'monthly_schedule' => 'Monthly Schedule',
        'day'              => 'Day',
    ],
    'ru' => [
        'select_region'    => 'Выберите город или район',
        'fajr'             => 'Фаджр',
        'sunrise'          => 'Восход',
        'dhuhr'            => 'Зухр',
        'asr'              => 'Аср',
        'sunset'           => 'Закат',
        'maghrib'          => 'Магриб',
        'isha'             => 'Иша',
        'midnight'         => 'Полночь',
        'method_note'      => 'Метод расчёта: Джафари (Институт Лева, Кум)',
        'today'            => 'Сегодня',
        'monthly_schedule' => 'Расписание на месяц',
        'day'              => 'День',
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

    <section class="bb-section bb-pt-section">
        <div class="bb-container">

            <!-- Başlıq -->
            <div class="bb-pt-header">
                <h1><?= bb_sanitize($title) ?></h1>
                <div class="bb-separator"></div>
                <p class="bb-text-muted"><?= bb_sanitize($description) ?></p>
            </div>

            <!-- Rayon seçimi + tarix -->
            <div class="bb-pt-controls">
                <div class="bb-pt-select-wrap">
                    <select id="bbRegionSelect" class="bb-pt-select" aria-label="<?= bb_sanitize($l['select_region']) ?>">
                        <option value=""><?= bb_sanitize($l['select_region']) ?></option>
                    </select>
                </div>
                <div class="bb-pt-date" id="bbPrayerDate"></div>
            </div>

            <!-- ============================
                 Bu günün namaz vaxtları
                 ============================ -->
            <div id="bbTodaySection" style="display:none;">
                <h2 class="bb-pt-subtitle">
                    <span class="bb-pt-subtitle-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    </span>
                    <?= bb_sanitize($l['today']) ?>
                </h2>

                <div class="bb-pt-grid" id="bbPrayerGrid">
                    <div class="bb-pt-card" data-prayer="fajr">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['fajr']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeFajr">--:--</div>
                    </div>
                    <div class="bb-pt-card" data-prayer="sunrise">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['sunrise']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeSunrise">--:--</div>
                    </div>
                    <div class="bb-pt-card" data-prayer="dhuhr">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['dhuhr']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeDhuhr">--:--</div>
                    </div>
                    <div class="bb-pt-card" data-prayer="asr">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['asr']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeAsr">--:--</div>
                    </div>
                    <div class="bb-pt-card" data-prayer="sunset">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['sunset']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeSunset">--:--</div>
                    </div>
                    <div class="bb-pt-card" data-prayer="maghrib">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['maghrib']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeMaghrib">--:--</div>
                    </div>
                    <div class="bb-pt-card" data-prayer="isha">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['isha']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeIsha">--:--</div>
                    </div>
                    <div class="bb-pt-card bb-pt-card-midnight" data-prayer="midnight">
                        <div class="bb-pt-card-label"><?= bb_sanitize($l['midnight']) ?></div>
                        <div class="bb-pt-card-time" id="bbTimeMidnight">--:--</div>
                    </div>
                </div>
            </div>

            <!-- ============================
                 Aylıq namaz vaxtları cədvəli
                 ============================ -->
            <div id="bbMonthlySection" style="display:none;">
                <h2 class="bb-pt-subtitle">
                    <span class="bb-pt-subtitle-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <?= bb_sanitize($l['monthly_schedule']) ?>
                </h2>

                <div class="bb-pt-month-nav">
                    <button class="bb-pt-month-btn" id="bbMonthPrev" aria-label="Previous month">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <span class="bb-pt-month-label" id="bbMonthLabel"></span>
                    <button class="bb-pt-month-btn" id="bbMonthNext" aria-label="Next month">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

                <div class="bb-pt-table-wrap">
                    <table class="bb-pt-table" id="bbMonthlyTable">
                        <thead>
                            <tr>
                                <th class="bb-pt-th-day"><?= bb_sanitize($l['day']) ?></th>
                                <th><?= bb_sanitize($l['fajr']) ?></th>
                                <th><?= bb_sanitize($l['sunrise']) ?></th>
                                <th><?= bb_sanitize($l['dhuhr']) ?></th>
                                <th><?= bb_sanitize($l['asr']) ?></th>
                                <th><?= bb_sanitize($l['sunset']) ?></th>
                                <th><?= bb_sanitize($l['maghrib']) ?></th>
                                <th><?= bb_sanitize($l['isha']) ?></th>
                                <th><?= bb_sanitize($l['midnight']) ?></th>
                            </tr>
                        </thead>
                        <tbody id="bbMonthlyBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Metod qeydi -->
            <p class="bb-pt-method" id="bbPrayerMethod" style="display:none;">
                <?= bb_sanitize($l['method_note']) ?>
            </p>
        </div>
    </section>

<?php
bb_frontend_footer([
    'extra_js' => [
        'https://cdn.jsdelivr.net/npm/praytime/praytime.js',
        '/public/assets/js/prayer-times.js',
    ],
]);
