# New Dashboard

Laravel 13 + Filament 5 dashboard and multilingual public website for a medical/clinic-style project.

The project currently supports:

- Public multilingual website in Arabic and English
- Filament admin panel at `admin`
- Services, blogs, gallery, branches, partners, products, and projects
- SEO management, sitemap generation, redirects, PWA settings, and performance metrics
- Contact, newsletter, feedback, and B2B inquiry flows

## Stack

- PHP `8.3+`
- Laravel `13`
- Filament `5`
- Filament Shield
- Spatie Media Library
- Spatie Translatable
- Spatie Sitemap
- Laravel Localization
- PhpSpreadsheet

## Main Areas

### Public website

Key public routes live in `routes/web.php`.

- `/` home page
- `/about-us`
- `/services`
- `/services/{service}`
- `/blogs`
- `/blogs/{blog}`
- `/gallery`
- `/branches`
- `/partners`
- `/products`
- `/products/{product}`
- `/projects`
- `/projects/{project}`
- `/contact-us`
- `/feedback`
- `/b2b`

All public pages are localized using `mcamara/laravel-localization`, so routes are rendered under `/ar/...` and `/en/...`.

### Admin panel

Filament admin is mounted at:

- `/admin`

The panel is configured in `app/Providers/Filament/AdminPanelProvider.php`.

Access control is managed with Filament Shield roles and permissions.

### API

Selected read/API endpoints live in `routes/api.php`.

Examples:

- `/api/home`
- `/api/layout`
- `/api/products`
- `/api/categories`
- `/api/blogs`
- `/api/contact-us`

### Authentication and profile

Standard auth routes are loaded from `routes/auth.php`.

Profile routes:

- `/profile`

Current profile scope is account editing only:

- update profile data
- update password
- delete account

## Important Features

### 1. Services

- Filament resource for managing services
- Public listing and details pages
- Related videos and branch map embeds on details page

Main files:

- `app/Http/Controllers/Site/ServiceController.php`
- `app/Filament/Resources/Services/ServiceResource.php`
- `resources/views/site/services/`

### 2. Blogs

- Filament blog management
- Localized slug-based public blog pages
- SEO metadata per blog

Main files:

- `app/Http/Controllers/Site/BlogController.php`
- `app/Filament/Resources/Blogs/BlogResource.php`
- `resources/views/site/blogs/`

### 3. Products

Products are now used as a catalog/content feature, not a full storefront checkout flow.

- Products listing page
- Product details page
- Product SEO
- Product categories

Main files:

- `app/Http/Controllers/Site/ProductController.php`
- `app/Models/Product.php`
- `resources/views/site/products/`

### 4. Projects

Projects were added to mirror the old Dr Fouad behavior:

- Projects listing page
- Project details page
- Cover image + gallery images
- Optional home-page display

Main files:

- `app/Http/Controllers/Site/ProjectController.php`
- `app/Models/Project.php`
- `app/Filament/Resources/Projects/ProjectResource.php`
- `resources/views/site/projects/`

### 5. SEO

SEO is generated through:

- page-level SEO settings
- entity-level metadata for blogs, services, products, projects, and categories
- canonical and alternate locale links
- sitemap generation

Main files:

- `app/Services/Seo/SeoService.php`
- `app/View/Composers/SeoComposer.php`
- `resources/views/components/seo/head.blade.php`
- `app/Services/Seo/BuildSitemapService.php`
- `app/Filament/Pages/ManageSeoAssistant.php`

### 6. Redirects

Custom redirect management exists for 301, 302, and 410 cases.

Main files:

- `app/Models/Redirect.php`
- `app/Services/RedirectService.php`
- `app/Http/Middleware/RedirectsMiddleware.php`
- `app/Filament/Resources/Redirects/RedirectResource.php`

### 7. PWA

The project includes:

- `manifest.json`
- service worker registration
- admin-managed PWA settings

Main files:

- `app/Models/PwaSetting.php`
- `app/Http/Controllers/ManifestController.php`
- `app/Filament/Pages/ManagePwaSettings.php`
- `public/sw.js`

### 8. Performance metrics

Web vitals are collected from the frontend and posted to:

- `POST /api/performance-metrics`

Main files:

- `app/Http/Controllers/PerformanceMetricController.php`
- `app/Models/PerformanceMetric.php`
- `app/Filament/Pages/ManagePerformance.php`

### 9. Contact and notifications

- Contact us messages
- Newsletter subscriptions
- B2B inquiries
- Mail settings page (SMTP + notification recipients + send test email)
- Notification emails from site settings

Main files:

- `app/Http/Controllers/Site/ContactUsController.php`
- `app/Http/Controllers/Site/NewsletterSubscriberController.php`
- `app/Http/Controllers/Site/B2BController.php`
- `app/Services/Mail/FormNotificationService.php`
- `app/Filament/Pages/ManageMailSettings.php`
- `app/Filament/Pages/ManageSiteContactSettings.php`

### 10. `#call#` shortcode inside content

Content fields can render in-content CTA buttons by placing:

```text
#call#
```

This inserts phone and WhatsApp buttons using site settings.

Currently supported in:

- services content
- blog content
- project content
- section content
- category content
- product description/tabs

Main files:

- `app/Models/Concerns/ReplacesCallShortcodeInContent.php`
- `resources/views/components/site/content-call-to-actions.blade.php`

## Project Structure

High-level folders:

```text
app/
  Filament/
  Http/
  Models/
  Providers/
  Services/
  Support/
  Traits/

database/
  migrations/
  seeders/

resources/
  views/
  lang/

routes/
  web.php
  api.php
  auth.php
```

Useful view locations:

- `resources/views/layouts/site.blade.php`
- `resources/views/partials/site/`
- `resources/views/site/home/`
- `resources/views/site/services/`
- `resources/views/site/blogs/`
- `resources/views/site/products/`
- `resources/views/site/projects/`

## Installation

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Configure:

- database connection
- mail settings
- app URL
- locale settings
- Claude/AI SEO keys if used

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Seed admin + demo data

```bash
php artisan db:seed
```

This seeds:

- Organization (`Dr Ahmed Elwan`)
- Admins (`developer@example.com` / `super_admin@domain.com`, password: `password`)
- Menus, SEO assistant, mail/site settings
- Generic bilingual demo content: services, categories, products, projects, blogs, sections, branches, team, partners, FAQs, gallery, inbox samples, redirects

Demo content is skipped automatically if services already exist for the organization.

### 5. Build frontend assets

```bash
npm run build
```

For local development:

```bash
npm run dev
```

### 6. Start the app

```bash
php artisan serve
```

Or use Laragon / your local web server.

## Useful Commands

```bash
php artisan migrate
php artisan optimize:clear
php artisan route:list
php artisan shield:generate --all --option=permissions --panel=admin
php artisan db:seed
php artisan test
composer run dev
composer run setup
```

## النشر على السيرفر (Server deployment)

على جهازك: ارفع التعديلات الأول.

```bash
git add .
git commit -m "your message"
git push origin master
```

على السيرفر، من فولدر المشروع:

```bash
git pull origin master
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
```

ملاحظات:

- أول مرة بس: `php artisan storage:link` عشان الصور تظهر.
- متشغلش `php artisan optimize` ولا `php artisan route:cache`. باكدج اللغات (mcamara) بيبوّظ الـ routes لما تتعمل لها كاش.
- مش محتاج `npm run build`. ملفات الموقع موجودة جاهزة في `public/assets`.
- أي تصحيح للبيانات (زي "أطلانتس" و "ستانلس") بيبقى migration، فبيتطبّق لوحده مع `migrate --force`.

## استيراد المنتجات من كتالوج العميل (Product catalog import)

المنتجات (حوالي 433 منتج) مش جاية من seeder. بتتعمل بالأمر `atlantis:import-catalog` من فولدر الكتالوج بتاع العميل.

شكل الفولدر:

```text
Atlantis Trade/
├── <القسم الرئيسي>/
│   └── <القسم الفرعي>/
│       └── <اسم المنتج>.jpg
```

- كل فولدر في المستوى الأول بيبقى قسم رئيسي.
- كل فولدر جواه بيبقى قسم فرعي.
- كل صورة بتبقى منتج، واسم المنتج بالعربي هو اسم الصورة.
- الاسم الإنجليزي بيتعمل تلقائي.
- الأخطاء الإملائية المعروفة بتتصحح لوحدها، زي "استلس" و "استانلس" اللي بيبقوا "ستانلس".

> **تحذير:** الأمر بيمسح كل الأقسام والمنتجات الموجودة الأول وبعدين يستورد من جديد. اعمل backup للداتابيز قبل ما تشغله على السيرفر.

### 1. اضغط فولدر الكتالوج على جهازك بـ 7-Zip

من PowerShell (الملف هيطلع حوالي 90 ميجا):

```powershell
& "C:\Program Files\7-Zip\7z.exe" a -tzip -mcu=on atlantis-catalog.zip "D:\Design\atlantisTrade\Atlantis Trade"
```

`-mcu=on` بيحفظ الأسماء العربي UTF-8، فبتفضل سليمة على السيرفر.

> متستخدمش "Send to > Compressed (zipped) folder" بتاع ويندوز، ولا `tar` اللي جاي مع ويندوز. الأول بيبوّظ الأسماء العربي على Linux، والتاني بيقف على الأسماء العربي.

### 2. ارفع الملف على السيرفر

ارفعه برا فولدر `public` بأي طريقة (scp أو File Manager). مثال:

```bash
scp atlantis-catalog.zip user@your-server:~/
```

### 3. فك الضغط على السيرفر

```bash
mkdir -p ~/atlantis-catalog
unzip -q ~/atlantis-catalog.zip -d ~/atlantis-catalog
ls ~/atlantis-catalog/"Atlantis Trade"
```

لازم الأسماء العربي تظهر سليمة في `ls`. لو ظهرت رموز غريبة، متكملش واضغط الفولدر تاني بأمر الخطوة 1.

### 4. اعمل backup للداتابيز

```bash
mysqldump -u DB_USER -p DB_NAME > ~/backup-before-catalog-$(date +%F).sql
```

### 5. شغّل الاستيراد

لازم يكون فيه organization في الداتابيز. لو السيرفر جديد خالص، شغّل `php artisan db:seed` الأول.

```bash
php artisan atlantis:import-catalog "$HOME/atlantis-catalog/Atlantis Trade"
php artisan optimize:clear
```

أو حط المسار في `.env` وشغّل الأمر من غير مسار:

```dotenv
ATLANTIS_CATALOG_PATH="/home/user/atlantis-catalog/Atlantis Trade"
```

```bash
php artisan atlantis:import-catalog
```

خيارات:

- من غير أي خيار: بيمسح الأقسام والمنتجات الموجودة ويستورد كل حاجة من جديد.
- `--keep-existing`: مش بيمسح حاجة. بيحدّث المنتجات الموجودة لأن كل صورة ليها كود ثابت، ويضيف الجديد، ومش بيرفع تاني الصور اللي متغيرتش.

### 6. اتأكد من النتيجة

- الأمر بيطبع جدول بعدد الأقسام والأقسام الفرعية والمنتجات.
- افتح لوحة التحكم ← المنتجات، واتأكد إن العدد حوالي 433.
- بعد ما تتأكد، تقدر تمسح `~/atlantis-catalog.zip`. سيب الفولدر لو هتحتاج تعيد الاستيراد.

## Media and uploads

The project uses Spatie Media Library for images/files.

Make sure storage is linked:

```bash
php artisan storage:link
```

Common media collections include:

- `services`
- `blogs`
- `products`
- `gallery_images`
- `cover`
- `gallery`
- `sections`
- `before`
- `after`

## Localization

Supported languages:

- Arabic `ar`
- English `en`

Slug-based entities use translated slugs and localized URLs.
Language switching on detail pages should route to the correct translated slug.

Key files:

- `app/Traits/HasSlug.php`
- `app/Traits/HasLocalizedUrl.php`
- `app/Support/LocaleUrl.php`

## Roles and permissions

Admin permissions rely on Filament Shield.

If a new Filament resource/page is added and does not appear in admin, regenerate permissions:

```bash
php artisan shield:generate --all --option=permissions --panel=admin
```

Then ensure the admin user has the correct role, such as `super_admin`.

## Known conventions

- Public site views live under `resources/views/site`
- Shared site partials live under `resources/views/partials/site`
- SEO data is injected through a view composer
- Rich text content may include `#call#`
- Section records are used heavily for page headings, hero content, and breadcrumbs
- Null-safe fallbacks are important because some pages depend on optional database content

## Suggested onboarding path

For a new developer, start here:

1. Read `routes/web.php`
2. Review `app/Providers/AppServiceProvider.php`
3. Review `app/View/Composers/SiteLayoutComposer.php`
4. Review `app/View/Composers/SeoComposer.php`
5. Review `app/Services/Seo/SeoService.php`
6. Review one public feature end-to-end, such as services or blogs
7. Review Filament resources under `app/Filament/Resources`

## Notes

- `resources/views/STRUCTURE.md` contains an older generic template structure and may not fully match the current project state.
- This README reflects the current customized application, not the default Laravel starter documentation.
