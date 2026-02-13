# Bibiheybet.com - Layihə Sənədləşdirməsi

> Hz. Həkimə Xanımın (s) Ziyarətgahının rəsmi veb saytı.
> Bu sənəd bütün agentlər və developerlar üçün istinad qaynağıdır.

---

## 0. Deployment Prosesi & Agent Qaydaları (HƏR FAZA ÜÇÜN VACİB!)

### Deployment İş Axını

```
[Developer/Agent] → git push → [GitHub: master] → Cloudways Git Pull → [Server: public_html/]
```

1. Bütün kod dəyişiklikləri lokal `C:\Users\Jamal Ali\Bibiheybet\` qovluğunda edilir.
2. Dəyişikliklər **`master`** branch-ına commit və push edilir.
3. Cloudways panelindən **Git → Pull** düyməsi ilə dəyişikliklər serverə çəkilir.
4. Serverdə fayllar **`public_html/`** qovluğuna yerləşdirilir.

### Git Repository

| Parametr | Dəyər |
|----------|-------|
| **Remote URL** | `git@github.com:jamalkamaladdin/Bibiheybet.git` |
| **Branch** | `master` |
| **Deployment Path** | `public_html/` |

### Server Məlumatları

| Parametr | Dəyər |
|----------|-------|
| **Hosting** | Cloudways (DigitalOcean) |
| **Public IP** | `104.248.41.44` |
| **SSH Username** | `heybat` |
| **App URL** | `phpstack-1454817-6208068.cloudways.com` |

> **Qeyd:** SSH parolu və digər həssas məlumatlar `.env` faylındadır.

### Credential-lar (.env)

Bütün credential-lar layihənin kökündəki **`.env`** faylında saxlanılır:

- **Database:** DB adı, istifadəçi, parol, host, port
- **Redis:** prefix, istifadəçi, parol, host, port
- **Server/SSH:** IP, istifadəçi, parol

> ⚠️ `.env` faylı **mütləq** `.gitignore`-dadır. GitHub-a push edilməməlidir!

### Hər Agent Üçün Qaydalar

#### Başlamazdan Əvvəl:
- [ ] Bu `README.md` faylını **tam** oxu — layihənin strukturunu, texniki stacki və fazaları anla.
- [ ] `.env` faylını oxu — credential-ları bil.
- [ ] Mövcud kodu nəzərdən keçir — əvvəlki fazalarda nə edilib, anla.
- [ ] Aşağıdakı "İnkişaf Fazaları" bölməsinə (Bölmə 18) bax — hansı fazalar tamamlanıb, hansı gözləyir.

#### İnkişaf Zamanı:
- **Heç vaxt credential-ları (parol, API key, vs.) kodun içinə yazma.** `config.php` və ya `.env` faylından oxu.
- **`master` branch-ında işlə.** Başqa branch açmağa ehtiyac yoxdur (xüsusi göstəriş olmadıqca).
- **Mövcud kodu pozmadan işlə.** Əvvəlki fazaların funksionallığını qoru.
- **Təmiz və oxunaqlı kod yaz.** Şərhlər (comments) əlavə et.
- **İnkişaf Qaydaları** bölməsinə (Bölmə 16) riayət et — prefix-lər, təhlükəsizlik, responsive qaydalar.

#### Bitirdikdən Sonra:
- [ ] Bütün dəyişiklikləri commit et (mənalı commit mesajı ilə).
- [ ] Bu README-nin "İnkişaf Fazaları" bölməsində (Bölmə 18) öz fazanı ✅ ilə işarələ.
- [ ] Əmin ol ki `.env` və `config.php` faylları `.gitignore`-dadır və push olunmayıb.
- [ ] `git push origin master` et.

#### Kod Yazma Qaydaları (AI Agentlər üçün MÜTLƏQDİR!):

**Əsas prinsip:** Minimum kod, maksimum nəticə. Hər sətir məqsədli olmalıdır.

**Output Azaltma:**
- Uzun izahatlar yazma. Kodu commit et, qısa commit mesajı yaz, bitdi.
- Hər addımı təkrar-təkrar izah etmə. Nə edəcəyini bir cümlə ilə de, et, nəticəni bildir.
- Bütün faylın məzmununu output-a yazma. Yalnız dəyişən hissəni göstər.

**Kod Stili:**
- **DRY:** Təkrarlanan kodu funksiyaya/komponentə çıxar.
- **Bir fayl = bir məsuliyyət.** Fayllar **300 sətirdən** çox olmamalıdır. Keçərsə, məntiqi hissələrə böl. İstisna: HTML template-lər və CSS faylları daha uzun ola bilər.
- **Mənalı adlar:** `$x` yox, `$totalPrice`. `func1()` yox, `bb_get_articles()`.
- **CSS-də inline style istifadə etmə.** Ayrı CSS fayllarında yaz.
- **JS-i HTML-dən ayır.** Ayrı `.js` fayllarında yaz.
- **PHP-də HTML ilə məntiqi qarışdırma.** Template və logic ayrı olsun.

**Fayl Yaratma:**
- Lazımsız fayl yaratma. Mövcud faylı redaktə etmək həmişə üstündür.
- Hər yeni fayl üçün konkret məqsəd olmalıdır — "ehtiyat üçün" fayl yaratma.
- Boş və ya şablon fayllar yaratma.

**Şərhlər (Comments):**
- Hər funksiyanın üstündə **bir sətirlik** şərh (nə edir).
- Aşkar kodu şərh etmə (`$i++; // i-ni artır` — YAZMA!).
- Mürəkkəb məntiq varsa, **niyə** belə edildiyini yaz, **nə** etdiyini yox.

**Nümunə — PİS vs YAXŞI:**

```php
// PİS: uzun, credential hardcoded, lazımsız şərhlər
function getUsers() {
    $host = "localhost";
    $user = "root";
    $pass = "12345";
    $conn = new mysqli($host, $user, $pass, "mydb");
    $result = $conn->query("SELECT * FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

// YAXŞI: qısa, təmiz, PDO istifadə edir
/** Bütün istifadəçiləri qaytarır */
function bb_get_users(PDO $db): array {
    return $db->query("SELECT * FROM users")->fetchAll();
}
```

---

## 1. Texniki Stack

| Texnologiya | Seçim |
|-------------|-------|
| **Backend** | Düz PHP (framework yoxdur) |
| **Database** | MySQL |
| **Frontend** | HTML, CSS, JavaScript (vanilla) |
| **Rich Text Editor** | TinyMCE 7 (open source, MIT lisenziya) |
| **Dil sistemi** | Manual (AZ, EN, RU) - tab əsaslı admin interfeys |
| **Hosting** | Cloudways (Git deployment) |
| **Deployment** | GitHub repo --> Cloudways Git Pull |

---

## 2. Database Məlumatları

| Parametr | Dəyər |
|----------|-------|
| **DB Name** | `npttyezhqc` |
| **Username** | `npttyezhqc` |
| **Password** | `K5g6c9UttX` |

> **Qeyd:** Produksiyada bu məlumatlar `config.php` faylında saxlanılır. `config.php` faylı `.gitignore`-a əlavə olunacaq, repoda isə `config.example.php` nümunəsi olacaq.

---

## 3. Admin Panel

### Giriş Məlumatları

| Parametr | Dəyər |
|----------|-------|
| **Login** | `ekosafari` |
| **Parol** | `ParolYanlisdirSifreDogrudur` |

> Parol database-də `password_hash()` ilə hash-lənmiş şəkildə saxlanılacaq.

### Admin Panel Bölmələri

| # | Bölmə | Təsvir |
|---|-------|--------|
| 1 | **Dashboard** | Ümumi statistikalar (məqalə sayı, ziyarətgah sayı) |
| 2 | **Məqalələr** | Yaratma, redaktə, silmə (3 dildə) |
| 3 | **Kateqoriyalar** | Məqalə kateqoriyaları (3 dildə) |
| 4 | **Ziyarətgahlar** | Yaratma, redaktə, silmə (3 dildə) + qalereya |
| 5 | **Media** | Yüklənmiş faylların idarəsi |

---

## 4. Çoxdilli Sistem (AZ / EN / RU)

### Prinsip
- Admin paneldə hər kontent üçün **3 tab** olacaq: Azərbaycan, English, Русский
- Hər tab-da ayrıca başlıq, mətn, SEO məlumatları daxil edilir
- **Məcburi deyil** - əgər hansısa dildə material yoxdursa, post yenə də paylaşıla bilər
- Frontend-də dil seçimi olacaq (URL prefix: `/az/`, `/en/`, `/ru/` - default: az)
- Əgər seçilmiş dildə kontent yoxdursa, AZ dilindəki kontent göstərilir (fallback)

### URL Strukturu

```
bibiheybet.com/                      --> AZ ana səhifə (default)
bibiheybet.com/en/                   --> EN ana səhifə
bibiheybet.com/ru/                   --> RU ana səhifə

bibiheybet.com/meqaleler/            --> AZ məqalə siyahısı
bibiheybet.com/en/articles/          --> EN məqalə siyahısı
bibiheybet.com/ru/stati/             --> RU məqalə siyahısı

bibiheybet.com/meqale/slug-adi       --> AZ tək məqalə
bibiheybet.com/en/article/slug-name  --> EN tək məqalə
bibiheybet.com/ru/statya/slug-name   --> RU tək məqalə

bibiheybet.com/ziyaretgahlar/        --> AZ ziyarətgah siyahısı
bibiheybet.com/en/pilgrimages/       --> EN
bibiheybet.com/ru/svyatyni/          --> RU

bibiheybet.com/ziyaretgah/slug-adi   --> AZ tək ziyarətgah
```

---

## 5. Database Sxemi

### `admins` - Admin istifadəçilər

| Sütun | Tip | Açıqlama |
|-------|-----|----------|
| id | INT AUTO_INCREMENT PK | |
| username | VARCHAR(50) UNIQUE | Login adı |
| password_hash | VARCHAR(255) | `password_hash()` ilə |
| created_at | DATETIME | |

### `categories` - Kateqoriyalar

| Sütun | Tip | Açıqlama |
|-------|-----|----------|
| id | INT AUTO_INCREMENT PK | |
| slug | VARCHAR(255) UNIQUE | URL-friendly ad |
| name_az | VARCHAR(255) | Azərbaycan adı |
| name_en | VARCHAR(255) NULL | İngilis adı |
| name_ru | VARCHAR(255) NULL | Rus adı |
| sort_order | INT DEFAULT 0 | Sıralama |
| created_at | DATETIME | |
| updated_at | DATETIME | |

### `articles` - Məqalələr

| Sütun | Tip | Açıqlama |
|-------|-----|----------|
| id | INT AUTO_INCREMENT PK | |
| category_id | INT FK → categories.id | Kateqoriya |
| slug_az | VARCHAR(255) | AZ URL slug |
| slug_en | VARCHAR(255) NULL | EN URL slug |
| slug_ru | VARCHAR(255) NULL | RU URL slug |
| title_az | VARCHAR(500) | AZ başlıq |
| title_en | VARCHAR(500) NULL | EN başlıq |
| title_ru | VARCHAR(500) NULL | RU başlıq |
| content_az | LONGTEXT | AZ məzmun (HTML) |
| content_en | LONGTEXT NULL | EN məzmun |
| content_ru | LONGTEXT NULL | RU məzmun |
| excerpt_az | TEXT NULL | AZ qısa mətn |
| excerpt_en | TEXT NULL | EN qısa mətn |
| excerpt_ru | TEXT NULL | RU qısa mətn |
| featured_image | VARCHAR(500) | Əsas foto (bütün dillər üçün default) |
| featured_image_en | VARCHAR(500) NULL | EN üçün fərqli foto |
| featured_image_ru | VARCHAR(500) NULL | RU üçün fərqli foto |
| meta_title_az | VARCHAR(255) NULL | SEO title AZ |
| meta_title_en | VARCHAR(255) NULL | SEO title EN |
| meta_title_ru | VARCHAR(255) NULL | SEO title RU |
| meta_desc_az | TEXT NULL | SEO description AZ |
| meta_desc_en | TEXT NULL | SEO description EN |
| meta_desc_ru | TEXT NULL | SEO description RU |
| og_image_az | VARCHAR(500) NULL | OG image AZ (paylaşım üçün) |
| og_image_en | VARCHAR(500) NULL | OG image EN |
| og_image_ru | VARCHAR(500) NULL | OG image RU |
| status | ENUM('draft','published') DEFAULT 'draft' | |
| published_at | DATETIME NULL | Nəşr tarixi |
| created_at | DATETIME | |
| updated_at | DATETIME | |

### `pilgrimages` - Ziyarətgahlar

| Sütun | Tip | Açıqlama |
|-------|-----|----------|
| id | INT AUTO_INCREMENT PK | |
| slug_az | VARCHAR(255) | AZ URL slug |
| slug_en | VARCHAR(255) NULL | EN URL slug |
| slug_ru | VARCHAR(255) NULL | RU URL slug |
| name_az | VARCHAR(500) | AZ ad |
| name_en | VARCHAR(500) NULL | EN ad |
| name_ru | VARCHAR(500) NULL | RU ad |
| content_az | LONGTEXT | AZ məzmun (HTML) |
| content_en | LONGTEXT NULL | EN məzmun |
| content_ru | LONGTEXT NULL | RU məzmun |
| featured_image | VARCHAR(500) | Əsas foto |
| featured_image_en | VARCHAR(500) NULL | EN üçün fərqli foto |
| featured_image_ru | VARCHAR(500) NULL | RU üçün fərqli foto |
| meta_title_az | VARCHAR(255) NULL | SEO |
| meta_title_en | VARCHAR(255) NULL | |
| meta_title_ru | VARCHAR(255) NULL | |
| meta_desc_az | TEXT NULL | |
| meta_desc_en | TEXT NULL | |
| meta_desc_ru | TEXT NULL | |
| og_image_az | VARCHAR(500) NULL | |
| og_image_en | VARCHAR(500) NULL | |
| og_image_ru | VARCHAR(500) NULL | |
| sort_order | INT DEFAULT 0 | Sıralama |
| status | ENUM('draft','published') DEFAULT 'draft' | |
| created_at | DATETIME | |
| updated_at | DATETIME | |

### `pilgrimage_gallery` - Ziyarətgah Qalereyası

| Sütun | Tip | Açıqlama |
|-------|-----|----------|
| id | INT AUTO_INCREMENT PK | |
| pilgrimage_id | INT FK → pilgrimages.id | |
| image_path | VARCHAR(500) | Foto yolu |
| caption_az | VARCHAR(500) NULL | AZ alt yazı |
| caption_en | VARCHAR(500) NULL | EN alt yazı |
| caption_ru | VARCHAR(500) NULL | RU alt yazı |
| sort_order | INT DEFAULT 0 | Sıralama |

### `media` - Media Faylları

| Sütun | Tip | Açıqlama |
|-------|-----|----------|
| id | INT AUTO_INCREMENT PK | |
| filename | VARCHAR(255) | Orijinal fayl adı |
| filepath | VARCHAR(500) | Server-dəki yol |
| filetype | VARCHAR(50) | MIME type |
| filesize | INT | Bayt ölçüsü |
| uploaded_at | DATETIME | |

---

## 6. Layihə Fayl Strukturu

```
bibiheybet/
│
├── config.example.php            # Nümunə konfiqurasiya (repoda)
├── config.php                    # Real konfiqurasiya (.gitignore-da)
├── .htaccess                     # URL rewriting
├── .gitignore
├── .env                          # Credential-lar (.gitignore-da, push etmə!)
├── README.md
│
├── database/
│   └── schema.sql                # Tam database sxemi + seed data
│
├── includes/                     # Ortaq PHP faylları
│   ├── db.php                    # PDO database bağlantısı (singleton)
│   ├── auth.php                  # Session & autentifikasiya
│   ├── functions.php             # Ümumi helper funksiyalar
│   ├── lang.php                  # Dil idarəetməsi (detect, switch, fallback)
│   └── seo.php                   # SEO meta tag generator
│
├── uploads/                      # İstifadəçi yükləmələri
│   ├── articles/                 # Məqalə şəkilləri
│   ├── pilgrimages/              # Ziyarətgah şəkilləri
│   └── media/                    # Ümumi media
│
├── admin/                        # ===== ADMİN PANEL =====
│   ├── index.php                 # Dashboard
│   ├── login.php                 # Giriş səhifəsi
│   ├── logout.php                # Çıxış
│   ├── upload.php                # TinyMCE inline şəkil yükləmə endpoint
│   │
│   ├── articles/
│   │   ├── index.php             # Məqalə siyahısı (pagination + filtr)
│   │   ├── create.php            # Yeni məqalə (3 dil tabları)
│   │   ├── edit.php              # Redaktə
│   │   ├── delete.php            # Silmə
│   │   └── form.php              # Ortaq form template (create/edit paylaşır)
│   │
│   ├── categories/
│   │   ├── index.php             # Kateqoriya siyahısı
│   │   ├── create.php            # Yeni kateqoriya (3 dil tabları)
│   │   ├── edit.php              # Redaktə
│   │   └── delete.php            # Silmə
│   │
│   ├── pilgrimages/
│   │   ├── index.php             # Ziyarətgah siyahısı (pagination + filtr)
│   │   ├── create.php            # Yeni ziyarətgah (3 dil tabları)
│   │   ├── edit.php              # Redaktə (+ qalereya idarəsi)
│   │   ├── delete.php            # Silmə (şəkillər + qalereya ilə birlikdə)
│   │   ├── form.php              # Ortaq form template (create/edit paylaşır)
│   │   ├── gallery-upload.php    # Qalereya şəkil yükləmə (AJAX)
│   │   ├── gallery-delete.php    # Qalereya şəkil silmə (AJAX)
│   │   ├── gallery-reorder.php   # Qalereya sıralama (AJAX)
│   │   └── gallery-caption.php   # Qalereya başlıq yeniləmə (AJAX)
│   │
│   └── assets/                   # Admin panel asset-ləri
│       ├── css/
│       │   └── admin.css         # Admin panel stilləri
│       ├── js/
│       │   ├── admin.js          # Ümumi admin JS (tab switch, alerts)
│       │   ├── editor.js         # TinyMCE inisializasiya + image upload handler
│       │   ├── media-upload.js   # Fayl yükləmə (drag & drop, preview)
│       │   └── gallery.js        # Qalereya idarəsi (sıralama, silmə) — FAZA 4
│       ├── tinymce/              # TinyMCE 7 self-hosted (MIT)
│       │   ├── tinymce.min.js
│       │   ├── plugins/
│       │   ├── skins/
│       │   ├── themes/
│       │   ├── models/
│       │   └── icons/
│       └── img/
│           └── admin-logo.png
│
├── public/                       # ===== FRONTEND =====
│   ├── index.php                 # Ana səhifə (router)
│   │
│   ├── templates/
│   │   ├── header.php            # Sayt header
│   │   ├── footer.php            # Sayt footer
│   │   ├── home.php              # Ana səhifə
│   │   ├── articles.php          # Məqalə siyahısı (arxiv)
│   │   ├── article-single.php   # Tək məqalə
│   │   ├── pilgrimages.php       # Ziyarətgah siyahısı
│   │   ├── pilgrimage-single.php # Tək ziyarətgah
│   │   ├── page.php              # Statik səhifələr (Həzrət, Məscid, Dua)
│   │   └── 404.php               # 404 səhifə
│   │
│   └── assets/
│       ├── css/
│       │   ├── global.css        # Reset, CSS variables, @font-face, tipografiya
│       │   ├── header.css
│       │   ├── footer.css
│       │   ├── mini-player.css
│       │   ├── home.css
│       │   ├── articles.css
│       │   ├── article-single.css
│       │   ├── pilgrimages.css
│       │   ├── pilgrimage-single.css
│       │   └── page.css          # Statik səhifələr
│       ├── js/
│       │   ├── app.js            # Ümumi JS, dil switch
│       │   ├── home.js           # Ana səhifə animasiyaları, audio trigger
│       │   ├── header.js         # Hamburger, sticky header
│       │   ├── mini-player.js    # Audio player
│       │   └── gallery.js        # Frontend lightbox qalereya
│       ├── fonts/                # Self-hosted fontlar
│       │   ├── SpaceGrotesk-VariableFont_wght.woff2
│       │   ├── SpaceGrotesk-Light.woff2
│       │   ├── SpaceGrotesk-Regular.woff2
│       │   ├── SpaceGrotesk-Medium.woff2
│       │   ├── SpaceGrotesk-SemiBold.woff2
│       │   ├── SpaceGrotesk-Bold.woff2
│       │   └── OFL.txt           # Font lisenziya
│       ├── img/                  # Statik şəkillər (logo, icon, vector)
│       │   ├── logo.png
│       │   ├── icon.png
│       │   ├── ton1.png
│       │   ├── ton2.png
│       │   ├── vector1.png
│       │   ├── mescid.png
│       │   └── hokume-bibi.png
│       └── audio/
│           └── ziyaretname.mp3
│
└── assets/                       # Dizayn mənbə faylları (deploy olunmur)
    ├── bibi logo son camal.psd
    └── bibi logo vers 5.png
```

---

## 7. SEO & Sosial Paylaşım

### Hər səhifə üçün `<head>` bölməsində:

```html
<!-- Əsas SEO -->
<title>{meta_title} | Bibiheybət Ziyarətgahı</title>
<meta name="description" content="{meta_description}">
<link rel="canonical" href="{canonical_url}">

<!-- Dil alternativləri (hreflang) -->
<link rel="alternate" hreflang="az" href="{az_url}">
<link rel="alternate" hreflang="en" href="{en_url}">
<link rel="alternate" hreflang="ru" href="{ru_url}">
<link rel="alternate" hreflang="x-default" href="{az_url}">

<!-- Open Graph (Facebook, LinkedIn, Telegram və s.) -->
<meta property="og:type" content="article">
<meta property="og:title" content="{meta_title}">
<meta property="og:description" content="{meta_description}">
<meta property="og:image" content="{og_image_url}">
<meta property="og:url" content="{canonical_url}">
<meta property="og:locale" content="az_AZ">
<meta property="og:locale:alternate" content="en_US">
<meta property="og:locale:alternate" content="ru_RU">
<meta property="og:site_name" content="Bibiheybət Ziyarətgahı">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{meta_title}">
<meta name="twitter:description" content="{meta_description}">
<meta name="twitter:image" content="{og_image_url}">

<!-- Structured Data (JSON-LD) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",  // və ya "Place" ziyarətgahlar üçün
  "headline": "{title}",
  "image": "{image_url}",
  "datePublished": "{published_date}",
  "author": {
    "@type": "Organization",
    "name": "Bibiheybət Ziyarətgahı"
  }
}
</script>

<!-- Favicon -->
<link rel="icon" type="image/png" href="/public/assets/img/icon.png">
```

### Avtomatik SEO Davranışları
- Əgər `meta_title` boşdursa → `title` istifadə olunur
- Əgər `meta_description` boşdursa → `excerpt` və ya kontentin ilk 160 simvolu
- Əgər `og_image` boşdursa → `featured_image` istifadə olunur
- `slug` avtomatik generate olunur (başlıqdan), amma admin tərəfindən dəyişdirilə bilər
- `sitemap.xml` avtomatik generate olunur (bütün dillər üçün)
- `robots.txt` mövcud olmalıdır

---

## 8. Rəng Paleti

| Ad | HEX | İstifadə sahəsi |
|----|-----|-----------------|
| **Əsas fon** | `#0a0a0a` | Saytın ümumi arxa fonu |
| **Alternativ fon** | `#111111` | Header sticky fon, kart fonları |
| **Bölmə fon** | `#1a1a2e` | Alternativ bölmə fonları |
| **Ton 1 (Tünd göy)** | `#23405e` | Düymələr, aktiv elementlər, separator xətlər |
| **Ton 2 (Krem/bej)** | `#e2ddcc` | Əsas body yazı rəngi, ikonlar |
| **Aksent (Qızılı)** | `#c9a84c` | Dekorativ xətlər, vurğular |
| **Ağ** | `#ffffff` | Başlıqlar, CTA düymə yazıları |
| **Solğun krem** | `#e2ddcc99` | İkinci dərəcəli yazılar |

```css
:root {
  --bb-bg-primary: #0a0a0a;
  --bb-bg-secondary: #111111;
  --bb-bg-section: #1a1a2e;
  --bb-color-ton1: #23405e;
  --bb-color-ton2: #e2ddcc;
  --bb-color-accent: #c9a84c;
  --bb-color-white: #ffffff;
  --bb-color-ton2-muted: #e2ddcc99;
}
```

---

## 9. Tipografiya

| İstifadə | Şrift | Çəki | Mənbə |
|----------|-------|------|-------|
| **Başlıqlar (AZ/EN/RU)** | Space Grotesk | SemiBold (600), Bold (700) | Self-hosted |
| **Body mətn (AZ/EN/RU)** | Space Grotesk | Light (300), Regular (400), Medium (500) | Self-hosted |
| **Ərəb mətnlər** | Amiri | Regular, Bold | Google Fonts (CDN) |

### Space Grotesk - Font Detalları

| Xüsusiyyət | Dəyər |
|-------------|-------|
| **Tipi** | Geometric sans-serif |
| **Variable font** | Bəli (`wght` oxu: 300–700) |
| **Statik çəkilər** | Light (300), Regular (400), Medium (500), SemiBold (600), Bold (700) |
| **Lisenziya** | SIL Open Font License 1.1 (pulsuz kommersial istifadə) |
| **Kiril dəstəyi** | Bəli (Rus dili üçün) |
| **Latın genişləndirilmiş** | Bəli (ə, ı, ö, ü, ş, ç, ğ - Azərbaycan simvolları) |
| **Ərəb dəstəyi** | Xeyr (Amiri istifadə olunacaq) |

### Self-hosted Font Qurulması

Font faylları `public/assets/fonts/` qovluğunda saxlanılır:

```
public/assets/fonts/
├── SpaceGrotesk-VariableFont_wght.woff2    # Variable font (əsas - müasir brauzerlər)
├── SpaceGrotesk-Light.woff2                # Fallback: Light 300
├── SpaceGrotesk-Regular.woff2              # Fallback: Regular 400
├── SpaceGrotesk-Medium.woff2               # Fallback: Medium 500
├── SpaceGrotesk-SemiBold.woff2             # Fallback: SemiBold 600
├── SpaceGrotesk-Bold.woff2                 # Fallback: Bold 700
└── OFL.txt                                 # Lisenziya faylı
```

> **Mənbə faylları:** `.ttf` faylları `ikonlar/Space_Grotesk/` qovluğunda mövcuddur (variable + static). Deploy zamanı `.woff2` formatına çevrilib `public/assets/fonts/` qovluğuna yerləşdirilməlidir (ən yaxşı kompressiya, bütün müasir brauzerlər dəstəkləyir). Çevirmə üçün: https://cloudconvert.com/ttf-to-woff2

### @font-face CSS

```css
/* Variable font (müasir brauzerlər) */
@font-face {
  font-family: 'Space Grotesk';
  src: url('/public/assets/fonts/SpaceGrotesk-VariableFont_wght.woff2') format('woff2-variations');
  font-weight: 300 700;
  font-style: normal;
  font-display: swap;
}

/* Statik fallback-lar (köhnə brauzerlər üçün) */
@font-face {
  font-family: 'Space Grotesk';
  src: url('/public/assets/fonts/SpaceGrotesk-Regular.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
/* ... digər çəkilər üçün eyni pattern */
```

### İstifadə Qaydaları

```css
:root {
  --bb-font-primary: 'Space Grotesk', sans-serif;
  --bb-font-arabic: 'Amiri', serif;
}

body {
  font-family: var(--bb-font-primary);
  font-weight: 400;
}

h1, h2, h3 {
  font-family: var(--bb-font-primary);
  font-weight: 600; /* SemiBold başlıqlar */
}

.bb-arabic-text {
  font-family: var(--bb-font-arabic);
  direction: rtl;
}
```

- Başlıqlarda `#ffffff` (ağ), body mətnlərdə `#e2ddcc` (krem)
- Ərəb mətnlər RTL (sağdan-sola) göstərilməlidir
- `font-display: swap` - font yüklənənə qədər sistem fontu göstərilir (performans)

---

## 10. Admin Panel - Detallı Tələblər

### 10.1 Giriş Səhifəsi
- Sadə, təmiz login formu (username + password)
- Session əsaslı autentifikasiya
- Yanlış giriş cəhdləri üçün xəta mesajı
- CSRF token qoruması

### 10.2 Məqalələr CRUD
- **Siyahı:** Cədvəl - başlıq, kateqoriya, status (draft/published), tarix, əməliyyatlar
- **Yaratma/Redaktə formu:**
  - 3 dil tabı (AZ / EN / RU) - hər tabda:
    - Başlıq (input)
    - Slug (auto-generate, manual override)
    - Məzmun (TinyMCE rich text editor)
    - Qısa mətn / excerpt (textarea)
    - SEO bölməsi (collapse/expand):
      - Meta title
      - Meta description
      - OG image (ayrıca yüklə və ya featured image istifadə et)
  - Ümumi sahələr (tablardan kənar):
    - Kateqoriya (select dropdown)
    - Status (draft / published)
    - Nəşr tarixi (datetime picker)
    - Featured image yükləmə (1 əsas + opsional dil fərqli)
- **Silmə:** Təsdiq modal ilə

### 10.3 Kateqoriyalar CRUD
- Sadə forma: ad_az, ad_en, ad_ru, slug, sıralama
- Siyahıda: ad, məqalə sayı, əməliyyatlar
- Kateqoriya silinərkən əlaqəli məqalələrin kateqoriyası null olur

### 10.4 Ziyarətgahlar CRUD
- **Yaratma/Redaktə formu:**
  - 3 dil tabı - hər tabda:
    - Ad (input)
    - Slug (auto-generate, manual override)
    - Məzmun (TinyMCE rich text editor)
    - SEO bölməsi
  - Ümumi sahələr:
    - Featured image (1 əsas + opsional dil fərqli)
    - Qalereya (multiple image upload, drag & drop sıralama, silmə)
    - Qalereya caption-ları (3 dildə)
    - Status, sıralama

### 10.5 Rich Text Editor (TinyMCE)
- **Versiya:** TinyMCE 7 (self-hosted, CDN yox)
- **Lisenziya:** MIT (tam pulsuz)
- **Pluginlər:** image, link, lists, table, code, fullscreen, media
- **Toolbar:** bold, italic, underline | headings | bullist, numlist | link, image | blockquote | code, fullscreen
- Editor-dan şəkil yüklənərkən `uploads/` qovluğuna save olunur
- RTL dəstəyi (Ərəb mətnlər üçün direction toggle)

---

## 11. Mövcud Asset Faylları

| Fayl | Təsvir |
|------|--------|
| `logo.png` | Açıq rəngli logo (krem/qızılı) - tünd fon üçün |
| `icon.png` | Tünd yaşıl ikon (tac + lent) - Favicon |
| `ton1.png` | Logo `#23405e` rəngində |
| `ton2.png` | İkon `#e2ddcc` rəngində |
| `vector1.png` | Bibiheybət məscidinin vektor illustrasiyası |
| `mescid.png` | Məscidin xarici görünüşü (gecə) |
| `hokume-bibi.png` | Məqbərənin daxili (zümrüd yaşıl və qızılı) |
| `ziyaretname.mp3` | Ziyarətnamə audio faylı |
| `full.jpg.jpeg` | Tam ölçülü foto |
| `bibi logo vers 5.png` | Logo versiya 5 |
| `bibi logo son camal.psd` | Logo mənbə faylı (Photoshop) |

---

## 12. Sayt Strukturu & Menyu

| # | Menyu adı (AZ) | EN | RU | Slug (AZ) |
|---|----------------|----|----|-----------|
| 1 | Ana səhifə | Home | Главная | `/` |
| 2 | Həzrət haqqında | About Hazrat | О Хазрат | `/hezret-haqqinda/` |
| 3 | Məscid haqqında | About Mosque | О Мечети | `/mescid-haqqinda/` |
| 4 | Dua və ziyarətnamə | Prayers | Молитвы | `/dua-ve-ziyaretname/` |
| 5 | Ziyarətgahlar | Pilgrimages | Святыни | `/ziyaretgahlar/` |
| 6 | Məqalələr | Articles | Статьи | `/meqaleler/` |

---

## 13. Sosial Media

| Platform | Link |
|----------|------|
| **Instagram** | https://www.instagram.com/bibiheybetziyaretgahi/ |
| **Facebook** | https://www.facebook.com/bibiheybetmecidi/ |

---

## 14. Frontend Səhifə Təsvirləri (Dizayn sonra veriləcək)

### Header
- Desktop: Logo + axtarış + sosial ikonlar + hamburger menyu
- Sticky: scroll-da logo + inline menyu
- Mobil: Logo + hamburger
- Hamburger menyu: sağdan sola açılır

### Footer
- Minimal: copyright mətni, `#23405e` separator xətt

### Ana Səhifə Bölmələri
1. Hero Banner (tam ekran, vector illustration, audio play)
2. Həzrət haqqında (foto + mətn)
3. Məscid haqqında (foto + mətn)
4. Dua və ziyarətnamə keçid
5. Ziyarətgahlar keçid
6. Son məqalələr (3 kart grid)

### Məqalələr Arxiv
- Kateqoriya filtri
- Kart grid (3 sütun desktop, 1 mobil)
- Pagination / Load more

### Tək Məqalə
- Featured image, başlıq, tarix, kateqoriya
- Mətn (max-width 800px, mərkəzləşmiş)
- Paylaşma düymələri
- Əlaqəli məqalələr

### Ziyarətgah Siyahısı
- Kartlar ilə (foto + ad + qısa mətn)

### Tək Ziyarətgah
- Featured image / banner
- Ad, məzmun
- Foto qalereyası (lightbox ilə)

---

## 15. Responsive Tələblər

| Element | Desktop (>1024px) | Tablet (768-1024px) | Mobil (<768px) |
|---------|-------------------|---------------------|----------------|
| Header | Logo + search + social + hamburger | Eyni | Logo + hamburger |
| Sticky Header | Bəli | Bəli | Yoxdur |
| Hamburger | 70% eni | 80% eni | 85-90% eni |
| Hero | 100vh | 80vh | 70vh |
| Bölmə layout | 2 sütun | 2 sütun (dar) | 1 sütun |
| Məqalə grid | 3 sütun | 2 sütun | 1 sütun |

---

## 16. İnkişaf Qaydaları

1. **PHP prefix:** Bütün funksiya adları `bb_` prefiksi ilə başlayır
2. **CSS prefix:** Bütün class-lar `bb-` prefiksi ilə (`bb-header`, `bb-card`)
3. **CSS Variables:** Rənglər həmişə `var(--bb-color-...)` ilə
4. **PDO:** Bütün DB əməliyyatları PDO prepared statements ilə (SQL injection qoruması)
5. **CSRF:** Bütün POST formlarında CSRF token
6. **XSS:** `htmlspecialchars()` ilə output escaping
7. **Upload:** Fayl yükləmə zamanı tip və ölçü yoxlanışı
8. **Mobile-first:** `min-width` media queries
9. **RTL:** Ərəb mətnlər üçün `dir="rtl"`
10. **Accessibility:** ARIA labellar, alt textlər, keyboard navigation

---

## 17. .htaccess (URL Rewriting)

```apache
RewriteEngine On

# Admin panel
RewriteRule ^admin/?$ admin/index.php [L]
RewriteRule ^admin/login/?$ admin/login.php [L]

# Language detection & routing
RewriteRule ^(en|ru)/(.*)$ public/index.php?lang=$1&route=$2 [L,QSA]
RewriteRule ^(?!admin|public|uploads|includes)(.*)$ public/index.php?lang=az&route=$1 [L,QSA]
```

---

## 18. İnkişaf Fazaları (Agent Bölgüsü)

### FAZA 1: Əsas / Foundation ✅
> **Agent 1 - Backend Foundation** — *Tamamlandı: 13 Fevral 2026*

- [x] `config.example.php` + `config.php` - DB və sayt konfiqurasiyası (.env-dən oxuyur)
- [x] `includes/db.php` - PDO bağlantısı (singleton pattern, utf8mb4)
- [x] `includes/auth.php` - Session management, login/logout, CSRF generate/verify
- [x] `includes/functions.php` - Ümumi helper-lər (slug generate AZ/RU transliterasiya, date format, image upload/resize/delete, flash messages)
- [x] `includes/lang.php` - Dil detection, switch, fallback məntiqi, route map (AZ/EN/RU), bb_get_field fallback
- [x] `includes/seo.php` - Meta tag, OG tag, Twitter Card, hreflang, JSON-LD generator (Article/Place/WebPage)
- [x] `database/schema.sql` - 6 cədvəl (admins, categories, articles, pilgrimages, pilgrimage_gallery, media) + `database/seed.php` admin seed
- [x] `.htaccess` - URL rewriting rules (dil prefix, admin, statik fayllar)
- [x] `.gitignore` - config.php, uploads/, *.psd, assets/ + uploads/ .gitkeep faylları

### FAZA 2: Admin Panel ✅
> **Agent 2 - Admin Panel** — *Tamamlandı: 13 Fevral 2026*

- [x] `admin/login.php` - Giriş səhifəsi + autentifikasiya (CSRF qorumalı, flash mesajlar)
- [x] `admin/index.php` - Dashboard (statistikalar: məqalə/kateqoriya/ziyarətgah/media sayları + son fəaliyyət)
- [x] `admin/logout.php` - Session məhv + login-ə redirect
- [x] `admin/includes/layout.php` - Admin layout (sidebar, topbar, əsas content area, `bb_admin_header`/`bb_admin_footer`)
- [x] `admin/assets/css/admin.css` - Admin panel stilləri (təmiz, modern UI, responsive, login, stat kartları, cədvəllər, formlar, tablar, modals, badges, pagination)
- [x] `admin/assets/js/admin.js` - Sidebar toggle, flash auto-dismiss, `bbConfirm()` modal, `initTabs()` tab switch utility
- [x] Middleware: `bb_admin_header()` içindəki `bb_require_auth()` ilə hər admin səhifə auth yoxlayır

### FAZA 3: Admin - Kateqoriyalar & Məqalələr ✅
> **Agent 3 - Content Management** — *Tamamlandı: 13 Fevral 2026*

- [x] Kateqoriya CRUD (siyahı, yaratma, redaktə, silmə)
- [x] Məqalə CRUD (siyahı, yaratma, redaktə, silmə)
- [x] 3 dil tab interfeysi
- [x] TinyMCE 7 inteqrasiyası (self-hosted, admin/assets/tinymce/)
- [x] Featured image yükləmə (+ dil fərqli foto seçimi)
- [x] SEO sahələri (collapse panel: meta title, meta desc, OG image)
- [x] Slug auto-generation (Azərbaycan simvolları dəstəyi: ə, ı, ö, ü, ş, ç, ğ)
- [x] Status management (draft/published) + nəşr tarixi
- [x] `admin/assets/js/editor.js` — TinyMCE init, image upload handler, RTL dəstəyi
- [x] `admin/assets/js/media-upload.js` — Drag & drop, preview, client-side yoxlanış
- [x] `admin/upload.php` — TinyMCE inline image upload endpoint
- [x] `admin/articles/form.php` — Ortaq form template (create/edit paylaşır)
- [x] Pagination (məqalə siyahısında, səhifə başı 15)
- [x] Status filtri (hamısı / nəşr / qaralama)
- [x] admin.css genişləndi (SEO panel, image upload, form layout, filter bar)

### FAZA 4: Admin - Ziyarətgahlar ✅
> **Agent 4 - Pilgrimage Management** — *Tamamlandı: 13 Fevral 2026*

- [x] Ziyarətgah CRUD (siyahı, yaratma, redaktə, silmə)
- [x] 3 dil tab interfeysi (AZ/EN/RU)
- [x] Featured image + dil fərqli foto
- [x] Qalereya sistemi (multiple upload, drag & drop sıralama, silmə — AJAX)
- [x] Qalereya caption-ları (3 dildə, debounce ilə auto-save)
- [x] `admin/assets/js/gallery.js` — qalereya idarəsi (upload, sıralama, silmə, caption)
- [x] `admin/pilgrimages/form.php` — ortaq form template (create/edit paylaşır)
- [x] AJAX endpoints: `gallery-upload.php`, `gallery-delete.php`, `gallery-reorder.php`, `gallery-caption.php`
- [x] Pagination + status filtri (siyahı səhifəsi)
- [x] SEO sahələri (meta title, meta desc, OG image — hər dil üçün)
- [x] admin.css genişləndi (qalereya grid, drag & drop stilləri, spinner, responsive)

### FAZA 5: Frontend Əsas / Foundation ✅
> **Agent 5 - Frontend Təməl Qurulması** — *Tamamlandı: 13 Fevral 2026*

- [x] Router (`public/index.php`) - URL parsing, dil detection, route resolve, template yükləmə
- [x] `.htaccess` yeniləndi - robots.txt/sitemap.xml/favicon.ico pass-through əlavə edildi
- [x] `public/assets/css/global.css` - CSS reset, CSS custom properties, @font-face (Space Grotesk variable + statik), tipografiya, rəng paleti, layout, kart, düymə, utility class-lar
- [x] `public/assets/fonts/` - Qovluq yaradıldı (.gitkeep), woff2 faylları manual əlavə olunmalıdır
- [x] `public/templates/header.php` - `bb_frontend_header()` funksiyası: HTML head, SEO meta inject, logo, dil switch (AZ|EN|RU), hamburger, mobil menyu paneli, sosial linklər
- [x] `public/templates/footer.php` - `bb_frontend_footer()` funksiyası: separator, copyright, JS include
- [x] `public/assets/js/app.js` - Hamburger menyu (açma/bağlama, ESC, overlay), dil switch (cookie sync), sticky header, lazy load (IntersectionObserver), cookie utility
- [x] `includes/seo.php` inteqrasiyası - hər template `bb_render_meta()` vasitəsilə SEO meta tag-lar alır
- [x] `includes/lang.php` genişləndi - statik səhifə route-ları (about-hazrat, about-mosque, prayers), menyu sistemi (`$bb_menu`, `bb_get_menu()`), dil switch URL (`bb_lang_switch_url()`), menyu aktiv yoxlama (`bb_is_menu_active()`)
- [x] Dil seçici UI elementi (header-də: AZ | EN | RU - aktiv dil span, digərləri link)
- [x] Placeholder template-lər: home.php, articles.php, article-single.php, pilgrimages.php, pilgrimage-single.php, page.php, 404.php
- [x] Tək element template-ləri (article-single, pilgrimage-single) DB-dən data çəkir, SEO + hreflang işləyir
- [x] Statik səhifə template (page.php) - 3 statik səhifə üçün ortaq template, çoxdilli dəstək

### FAZA 6: Frontend - Header, Footer & Naviqasiya
> **Agent 6 - Header/Footer Dizayn & Funksionallıq**
>
> Dizayn istifadəçi tərəfindən veriləcək. Bu faza header, footer, hamburger menyu və audio mini-player-i əhatə edir.

- [ ] `public/assets/css/header.css` - Header stilləri (desktop, tablet, mobil)
- [ ] `public/assets/css/footer.css` - Footer stilləri
- [ ] `public/assets/js/header.js` - Hamburger menyu açma/bağlama, sticky header
- [ ] `public/assets/js/mini-player.js` - Audio mini-player (fixed bottom, play/pause, progress, close)
- [ ] `public/assets/css/mini-player.css` - Mini-player stilləri
- [ ] Desktop header: Logo + axtarış + sosial ikonlar + hamburger menyu
- [ ] Sticky header: scroll-da logo + inline menyu linkləri (slide-down animasiya)
- [ ] Mobil header: Logo + hamburger (sticky yoxdur)
- [ ] Hamburger menyu: sağdan sola slide, overlay, menyu itemləri, sosial ikonlar
- [ ] Footer: separator xətt + copyright
- [ ] Responsive davranışlar (desktop/tablet/mobil ölçüləri)

### FAZA 7: Frontend - Ana Səhifə
> **Agent 7 - Homepage**
>
> Ana səhifənin bütün bölmələri. Dizayn istifadəçi tərəfindən veriləcək.

- [ ] `public/templates/home.php` - Ana səhifə template
- [ ] `public/assets/css/home.css` - Ana səhifə stilləri
- [ ] `public/assets/js/home.js` - Animasiyalar, audio trigger
- [ ] **Bölmə 1: Hero Banner** - tam ekran, vektor təsvir, başlıq, audio play ikonu
- [ ] **Bölmə 2: Həzrət haqqında** - foto + ərəb mətn (RTL) + AZ tərcümə + "Ətraflı oxu" düyməsi
- [ ] **Bölmə 3: Məscid haqqında** - böyük foto + mətn (paralaks opsional)
- [ ] **Bölmə 4: Dua və ziyarətnamə** - dekorativ bölmə, CTA düymə
- [ ] **Bölmə 5: Ziyarətgahlar keçid** - şəkilli banner, CTA düymə
- [ ] **Bölmə 6: Son məqalələr** - 3 kart grid (DB-dən son published məqalələr), "Bütün məqalələr" düyməsi
- [ ] Scroll animasiyaları (bölmələr fade-in, AOS-a bənzər CSS/JS)
- [ ] Audio play --> mini-player trigger

### FAZA 8: Frontend - Məqalə Səhifələri
> **Agent 8 - Articles Frontend**
>
> Məqalə siyahısı (arxiv) və tək məqalə səhifəsi. DB-dən data çəkilir.

- [ ] `public/templates/articles.php` - Məqalə siyahısı (arxiv)
- [ ] `public/assets/css/articles.css` - Arxiv stilləri
- [ ] Kateqoriya filtri düymələri (DB-dən kateqoriyalar, AJAX və ya server-side filtr)
- [ ] Kart grid (3 sütun desktop, 2 tablet, 1 mobil)
- [ ] Hər kart: featured image, başlıq, qısa excerpt, tarix, kateqoriya
- [ ] Kart hover effekti (translateY + kölgə)
- [ ] Pagination və ya "Daha çox yüklə" (AJAX load more)
- [ ] `public/templates/article-single.php` - Tək məqalə
- [ ] `public/assets/css/article-single.css` - Single stilləri
- [ ] Featured image (tam eni banner)
- [ ] Başlıq, tarix, kateqoriya, müəllif
- [ ] Mətn kontenti (max-width 800px, mərkəzləşmiş, oxunaqlı tipografiya)
- [ ] Sosial paylaşma düymələri (Facebook, Twitter, Telegram, WhatsApp, link copy)
- [ ] Əlaqəli məqalələr bölməsi (eyni kateqoriyadan 3 məqalə)
- [ ] SEO: `<head>` meta taglar, OG, Twitter Card, JSON-LD (Article schema)
- [ ] Çoxdilli URL routing (AZ/EN/RU slug-lara görə)

### FAZA 9: Frontend - Ziyarətgah Səhifələri
> **Agent 9 - Pilgrimages Frontend**
>
> Ziyarətgah siyahısı və tək ziyarətgah səhifəsi. DB-dən data + qalereya.

- [ ] `public/templates/pilgrimages.php` - Ziyarətgah siyahısı
- [ ] `public/assets/css/pilgrimages.css` - Siyahı stilləri
- [ ] Kartlar (featured image + ad + qısa mətn)
- [ ] `public/templates/pilgrimage-single.php` - Tək ziyarətgah
- [ ] `public/assets/css/pilgrimage-single.css` - Single stilləri
- [ ] Featured image / hero banner
- [ ] Ad, məzmun (tam genişlikdə, oxunaqlı)
- [ ] Foto qalereyası (grid layout, klikləndikdə lightbox açılır)
- [ ] `public/assets/js/gallery.js` - Lightbox funksionallığı (öncəki/sonrakı, bağla, swipe mobilda)
- [ ] Sosial paylaşma düymələri
- [ ] SEO: meta taglar, OG, JSON-LD (Place schema, `sameAs`, `geo` koordinatlar)
- [ ] Çoxdilli URL routing

### FAZA 10: Frontend - Statik Səhifələr, 404 & SEO Tamamlama
> **Agent 10 - Static Pages & SEO Finalization**
>
> Statik səhifələr, xəta səhifəsi və SEO faylları.

- [ ] `public/templates/page.php` - Ümumi statik səhifə template
- [ ] Həzrət haqqında səhifəsi (kontent admindən və ya hardcoded)
- [ ] Məscid haqqında səhifəsi
- [ ] Dua və ziyarətnamə səhifəsi
- [ ] Hər səhifənin yuxarısında page header/banner (tünd fon + böyük başlıq)
- [ ] Kontent area (max-width 900-1000px, mərkəzləşmiş)
- [ ] `public/templates/404.php` - 404 xəta səhifəsi (estetik dizayn, ana səhifəyə keçid)
- [ ] `public/assets/css/page.css` - Statik səhifə stilləri
- [ ] `sitemap.xml` generator PHP (bütün dillər üçün, bütün məqalə/ziyarətgah URL-ləri)
- [ ] `robots.txt` (admin panel indeksləmədən çıxarılır)
- [ ] Canonical URL-lərin son yoxlanışı
- [ ] hreflang tag-ların bütün səhifələrdə düzgün işləməsi
- [ ] Open Graph preview testi (Facebook debugger, Twitter card validator)
- [ ] Performans: CSS/JS minification, şəkil lazy loading, font preload

---

## 19. Fazaların Asılılıq Xəritəsi

```
FAZA 1 (Foundation)
  ├── FAZA 2 (Admin Panel UI)
  │     ├── FAZA 3 (Kateqoriya & Məqalə CRUD)
  │     └── FAZA 4 (Ziyarətgah CRUD)
  │
  └── FAZA 5 (Frontend Foundation)
        ├── FAZA 6 (Header/Footer) ─── dizayn lazımdır
        ├── FAZA 7 (Ana Səhifə) ────── dizayn lazımdır, FAZA 6-dan sonra
        ├── FAZA 8 (Məqalələr) ─────── FAZA 3-dən sonra (DB data lazım)
        ├── FAZA 9 (Ziyarətgahlar) ─── FAZA 4-dən sonra (DB data lazım)
        └── FAZA 10 (Statik & SEO) ─── hamısından sonra
```

> **Qeyd:** FAZA 1-4 (backend + admin) dizayndan asılı deyil və dərhal başlana bilər.
> FAZA 5 də dizayndan asılı deyil (infrastruktur). FAZA 6-dan etibarən dizayn lazımdır.
> FAZA 8 və 9 admin CRUD-larından sonra edilməlidir (test etmək üçün data lazım).

---

*Bu sənəd layihə boyunca yenilənəcək. Son yenilənmə: 13 Fevral 2026 (FAZA 5 tamamlandı)*
