<?php
/**
 * Bibiheybet.com - Namaz Vaxtları Səhifəsi
 * 
 * PrayTimes kitabxanası ilə Cəfəri firqəsinə uyğun namaz vaxtları.
 * Azərbaycanın bütün rayonları üçün.
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

// Çoxdilli etiketlər (JS-ə ötürülür)
$labels = [
    'az' => [
        'select_region' => 'Şəhər və ya rayon seçin',
        'fajr'          => 'Sübh',
        'sunrise'       => 'Günəş',
        'dhuhr'         => 'Zöhr',
        'asr'           => 'Əsr',
        'sunset'        => 'Qürub',
        'maghrib'       => 'Məğrib',
        'isha'          => 'İşa',
        'midnight'      => 'Gecə yarısı',
        'method_note'   => 'Hesablama metodu: Cəfəri (Leva Tədqiqat İnstitutu, Qum)',
    ],
    'en' => [
        'select_region' => 'Select city or region',
        'fajr'          => 'Fajr',
        'sunrise'       => 'Sunrise',
        'dhuhr'         => 'Dhuhr',
        'asr'           => 'Asr',
        'sunset'        => 'Sunset',
        'maghrib'       => 'Maghrib',
        'isha'          => 'Isha',
        'midnight'      => 'Midnight',
        'method_note'   => 'Calculation method: Jafari (Leva Research Institute, Qom)',
    ],
    'ru' => [
        'select_region' => 'Выберите город или район',
        'fajr'          => 'Фаджр',
        'sunrise'       => 'Восход',
        'dhuhr'         => 'Зухр',
        'asr'           => 'Аср',
        'sunset'        => 'Закат',
        'maghrib'       => 'Магриб',
        'isha'          => 'Иша',
        'midnight'      => 'Полночь',
        'method_note'   => 'Метод расчёта: Джафари (Институт Лева, Кум)',
    ],
];

$l = $labels[$lang] ?? $labels['az'];
?>

    <section class="bb-section bb-pt-section">
        <div class="bb-container-narrow">
            <h1><?= bb_sanitize($title) ?></h1>
            <div class="bb-separator"></div>
            <p class="bb-text-muted"><?= bb_sanitize($description) ?></p>

            <!-- Rayon seçimi -->
            <div class="bb-pt-controls">
                <div class="bb-pt-select-wrap">
                    <select id="bbRegionSelect" class="bb-pt-select" aria-label="<?= bb_sanitize($l['select_region']) ?>">
                        <option value=""><?= bb_sanitize($l['select_region']) ?></option>
                    </select>
                </div>
                <div class="bb-pt-date" id="bbPrayerDate"></div>
            </div>

            <!-- Namaz vaxtları grid -->
            <div class="bb-pt-grid" id="bbPrayerGrid" style="display:none;">
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

            <!-- Method qeydi -->
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
