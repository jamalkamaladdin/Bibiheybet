<?php
/**
 * Bibiheybet.com - SEO & Meta Tag Generator
 * 
 * Meta tags, Open Graph, Twitter Card, hreflang, JSON-LD structured data.
 * Hər səhifənin <head> bölməsinə əlavə olunur.
 */

require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/functions.php';

/**
 * Tam SEO meta tag-ları render edir.
 * 
 * @param array $data SEO data massivi:
 *   - title (string) Səhifə başlığı
 *   - meta_title (string|null) SEO title (boşdursa title istifadə olunur)
 *   - meta_description (string|null) SEO description (boşdursa excerpt / kontentin ilk 160 simvolu)
 *   - canonical_url (string) Canonical URL
 *   - og_image (string|null) OG image URL (boşdursa featured_image)
 *   - featured_image (string|null) Featured image URL
 *   - og_type (string) OG type (default: 'article')
 *   - published_date (string|null) ISO 8601 tarix
 *   - modified_date (string|null) ISO 8601 tarix
 *   - schema_type (string) JSON-LD type: 'Article', 'Place', 'WebPage' (default: 'WebPage')
 *   - alternate_urls (array) Dil -> URL massivi (hreflang üçün)
 *   - excerpt (string|null) Qısa mətn (meta_description fallback)
 *   - content (string|null) Tam kontent (meta_description son fallback)
 *   - lang (string) Cari dil kodu
 * @return string HTML meta tag-lar
 */
function bb_render_meta(array $data): string
{
    if (!defined('SITE_NAME')) {
        require_once __DIR__ . '/../config.php';
    }

    global $bb_lang_locales;

    $lang = $data['lang'] ?? bb_get_lang();
    $siteName = SITE_NAME;

    // Title
    $title = $data['meta_title'] ?? $data['title'] ?? $siteName;
    $fullTitle = $title . ' | ' . $siteName;

    // Description (fallback zənciri: meta_description -> excerpt -> content-in ilk 160 simvolu)
    $description = $data['meta_description'] ?? null;
    if (empty($description) && !empty($data['excerpt'])) {
        $description = strip_tags($data['excerpt']);
    }
    if (empty($description) && !empty($data['content'])) {
        $description = bb_truncate($data['content'], 160, '');
    }
    if (empty($description)) {
        $description = 'Hz. Həkimə Xanımın (s) Ziyarətgahının rəsmi veb saytı.';
    }
    // 160 simvolla məhdudlaşdır
    $description = mb_substr(strip_tags($description), 0, 160, 'UTF-8');

    // Image (fallback: og_image -> featured_image)
    $image = $data['og_image'] ?? $data['featured_image'] ?? null;
    if ($image && !str_starts_with($image, 'http')) {
        $image = SITE_URL . '/' . ltrim($image, '/');
    }

    // Canonical URL
    $canonicalUrl = $data['canonical_url'] ?? '';

    // OG type
    $ogType = $data['og_type'] ?? 'website';

    // Locale
    $locale = $bb_lang_locales[$lang] ?? 'az_AZ';

    // ---- Meta tag-ları yığ ----
    $html = '';

    // Əsas SEO
    $html .= '<title>' . bb_sanitize($fullTitle) . '</title>' . "\n";
    $html .= '<meta name="description" content="' . bb_sanitize($description) . '">' . "\n";

    if (!empty($canonicalUrl)) {
        $html .= '<link rel="canonical" href="' . bb_sanitize($canonicalUrl) . '">' . "\n";
    }

    // hreflang tag-lar
    if (!empty($data['alternate_urls'])) {
        foreach ($data['alternate_urls'] as $altLang => $altUrl) {
            $html .= '<link rel="alternate" hreflang="' . bb_sanitize($altLang) . '" href="' . bb_sanitize($altUrl) . '">' . "\n";
        }
        // x-default (AZ dilinə yönləndirir)
        if (isset($data['alternate_urls']['az'])) {
            $html .= '<link rel="alternate" hreflang="x-default" href="' . bb_sanitize($data['alternate_urls']['az']) . '">' . "\n";
        }
    }

    // Open Graph
    $html .= '<meta property="og:type" content="' . bb_sanitize($ogType) . '">' . "\n";
    $html .= '<meta property="og:title" content="' . bb_sanitize($title) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . bb_sanitize($description) . '">' . "\n";
    $html .= '<meta property="og:site_name" content="' . bb_sanitize($siteName) . '">' . "\n";
    $html .= '<meta property="og:locale" content="' . bb_sanitize($locale) . '">' . "\n";

    if (!empty($canonicalUrl)) {
        $html .= '<meta property="og:url" content="' . bb_sanitize($canonicalUrl) . '">' . "\n";
    }
    if (!empty($image)) {
        $html .= '<meta property="og:image" content="' . bb_sanitize($image) . '">' . "\n";
    }

    // Alternativ locale-lar
    foreach ($bb_lang_locales as $altLang => $altLocale) {
        if ($altLang !== $lang) {
            $html .= '<meta property="og:locale:alternate" content="' . bb_sanitize($altLocale) . '">' . "\n";
        }
    }

    // Twitter Card
    $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $html .= '<meta name="twitter:title" content="' . bb_sanitize($title) . '">' . "\n";
    $html .= '<meta name="twitter:description" content="' . bb_sanitize($description) . '">' . "\n";
    if (!empty($image)) {
        $html .= '<meta name="twitter:image" content="' . bb_sanitize($image) . '">' . "\n";
    }

    // Favicon
    $html .= '<link rel="icon" type="image/png" href="' . SITE_URL . '/public/assets/img/icon.png">' . "\n";

    // JSON-LD Structured Data
    $html .= bb_render_jsonld($data, $lang);

    return $html;
}

/**
 * JSON-LD structured data render edir.
 * 
 * @param array $data SEO data
 * @param string $lang Dil kodu
 * @return string <script type="application/ld+json">...</script>
 */
function bb_render_jsonld(array $data, string $lang = 'az'): string
{
    $schemaType = $data['schema_type'] ?? 'WebPage';

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type'    => $schemaType,
    ];

    // Title
    if (!empty($data['title'])) {
        if ($schemaType === 'Article') {
            $jsonLd['headline'] = $data['title'];
        } else {
            $jsonLd['name'] = $data['title'];
        }
    }

    // Description
    $description = $data['meta_description'] ?? $data['excerpt'] ?? null;
    if (!empty($description)) {
        $jsonLd['description'] = strip_tags($description);
    }

    // Image
    $image = $data['og_image'] ?? $data['featured_image'] ?? null;
    if ($image) {
        if (!str_starts_with($image, 'http')) {
            $image = SITE_URL . '/' . ltrim($image, '/');
        }
        $jsonLd['image'] = $image;
    }

    // URL
    if (!empty($data['canonical_url'])) {
        $jsonLd['url'] = $data['canonical_url'];
    }

    // Dates (Article üçün)
    if ($schemaType === 'Article') {
        if (!empty($data['published_date'])) {
            $jsonLd['datePublished'] = $data['published_date'];
        }
        if (!empty($data['modified_date'])) {
            $jsonLd['dateModified'] = $data['modified_date'];
        }
        $jsonLd['author'] = [
            '@type' => 'Organization',
            'name'  => SITE_NAME,
        ];
        $jsonLd['publisher'] = [
            '@type' => 'Organization',
            'name'  => SITE_NAME,
        ];
    }

    // Place üçün (ziyarətgahlar)
    if ($schemaType === 'Place') {
        $jsonLd['address'] = [
            '@type'          => 'PostalAddress',
            'addressCountry' => 'AZ',
            'addressLocality' => 'Bakı',
        ];
    }

    // inLanguage
    $langMap = ['az' => 'az', 'en' => 'en', 'ru' => 'ru', 'ar' => 'ar', 'fa' => 'fa'];
    $jsonLd['inLanguage'] = $langMap[$lang] ?? 'az';

    $json = json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
}

/**
 * Sadə SEO data massivi yaradır (helper).
 * Əksər hallarda bu funksiya ilə data hazırlanıb bb_render_meta()-ya ötürülür.
 * 
 * @param array $row DB row (məqalə, ziyarətgah, və s.)
 * @param string $type Kontent tipi ('article', 'pilgrimage', 'page')
 * @param string|null $lang Dil kodu
 * @return array SEO data massivi
 */
function bb_prepare_seo_data(array $row, string $type = 'page', ?string $lang = null): array
{
    if ($lang === null) {
        $lang = bb_get_lang();
    }

    $data = [
        'lang' => $lang,
    ];

    // Title & content
    $data['title'] = bb_get_field($row, 'title', $lang)
                  ?? bb_get_field($row, 'name', $lang)
                  ?? '';

    $data['content'] = bb_get_field($row, 'content', $lang) ?? '';
    $data['excerpt'] = bb_get_field($row, 'excerpt', $lang) ?? '';

    // SEO fields
    $data['meta_title'] = bb_get_field($row, 'meta_title', $lang);
    $data['meta_description'] = bb_get_field($row, 'meta_desc', $lang);

    // Images
    $data['featured_image'] = bb_get_featured_image($row, $lang);
    $ogImageField = 'og_image_' . $lang;
    $data['og_image'] = $row[$ogImageField] ?? $row['og_image_az'] ?? $data['featured_image'];

    // Schema type
    switch ($type) {
        case 'article':
            $data['schema_type'] = 'Article';
            $data['og_type'] = 'article';
            $data['published_date'] = $row['published_at'] ?? $row['created_at'] ?? null;
            $data['modified_date'] = $row['updated_at'] ?? null;
            break;
        case 'pilgrimage':
            $data['schema_type'] = 'Place';
            $data['og_type'] = 'article';
            break;
        default:
            $data['schema_type'] = 'WebPage';
            $data['og_type'] = 'website';
    }

    return $data;
}
