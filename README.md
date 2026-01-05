# AI Showcase

AI araçlarıyla (Claude Code, Cursor, Windsurf, v0 vb.) geliştirilen web projelerinin sergilendiği bir platform.

## Özellikler

- **Google OAuth** ile kullanıcı girişi
- **Proje paylaşımı** - Ekran görüntüsü, link ve açıklama ile
- **Kategori sistemi** - Web App, Mobil App, Oyun, E-ticaret vb.
- **AI araç etiketleri** - Projede kullanılan AI araçlarını belirtme
- **Beğeni ve yorum** sistemi
- **Arama ve filtreleme** - Kategori, AI aracı, sıralama
- **Admin paneli** - Proje onaylama, kullanıcı yönetimi

## Teknolojiler

- **Backend:** PHP 8.1+, CodeIgniter 4
- **Frontend:** Tailwind CSS v4
- **Veritabanı:** MySQL
- **Kimlik doğrulama:** Google OAuth 2.0

## Kurulum

### Gereksinimler

- PHP 8.1+
- Composer
- Node.js & npm
- MySQL

### Adımlar

1. **Bağımlılıkları yükle:**
   ```bash
   composer install
   npm install
   ```

2. **Ortam dosyasını yapılandır:**
   ```bash
   cp env .env
   ```

   `.env` dosyasını düzenle:
   ```
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'

   database.default.hostname = localhost
   database.default.database = ai_showcase
   database.default.username = root
   database.default.password = your_password
   database.default.DBDriver = MySQLi

   google.clientId = YOUR_GOOGLE_CLIENT_ID
   google.clientSecret = YOUR_GOOGLE_CLIENT_SECRET
   google.redirectUri = 'http://localhost:8080/auth/callback'

   admin.emails = 'admin@example.com'
   ```

3. **Veritabanını oluştur ve migrate et:**
   ```bash
   # MySQL'de veritabanı oluştur
   mysql -u root -p -e "CREATE DATABASE ai_showcase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

   # Migration'ları çalıştır
   php spark migrate

   # Seed verilerini ekle (kategoriler ve AI araçları)
   php spark db:seed DatabaseSeeder
   ```

4. **CSS'i derle:**
   ```bash
   npm run build
   ```

5. **Sunucuyu başlat:**
   ```bash
   php spark serve
   ```

   Tarayıcıda http://localhost:8080 adresine git.

## Geliştirme

```bash
# CSS değişikliklerini izle
npm run watch

# Testleri çalıştır
composer test
```

## Proje Yapısı

```
app/
├── Controllers/
│   ├── BaseController.php   # Temel controller (auth, admin helpers)
│   ├── Admin.php            # Admin paneli
│   ├── Auth.php             # Google OAuth
│   ├── Projects.php         # Proje CRUD
│   └── Api.php              # AJAX endpoints
├── Models/
│   ├── ProjectModel.php     # Proje sorguları ve filtreleme
│   ├── UserModel.php
│   └── ...
├── Views/
│   ├── layouts/main.php     # Ana layout
│   ├── components/          # Navbar, footer, project_card
│   ├── pages/               # Sayfa şablonları
│   └── admin/               # Admin paneli görünümleri
└── Database/
    ├── Migrations/          # Veritabanı şeması
    └── Seeds/               # Başlangıç verileri
```

## Admin Paneli

Admin kullanıcıları `.env` dosyasındaki `admin.emails` ile belirlenir:

```
admin.emails = 'admin@example.com,digeradmin@example.com'
```

Admin özellikleri:
- Dashboard ile istatistikler
- Bekleyen projeleri onaylama/reddetme
- Kullanıcıları yasaklama/yasak kaldırma

## Lisans

MIT
