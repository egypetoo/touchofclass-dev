<?php

namespace Database\Seeders;

use App\Enums\BranchTypeEnum;
use App\Enums\PageEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\SectionTypeEnum;
use App\Enums\SocialLinkPlatform;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\MailSetting;
use App\Models\NewsletterSubscriber;
use App\Models\Organization;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Project;
use App\Models\Redirect;
use App\Models\Section;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SiteSocialLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if ($organization) {
            $organization->update([
                'name_en' => 'King Motors',
                'name_ar' => 'كينج موتورز',
            ]);
        } else {
            $organization = Organization::query()->create([
                'name_en' => 'King Motors',
                'name_ar' => 'كينج موتورز',
            ]);
        }

        $this->seedSiteSettings($organization);
        $this->seedMailSettings();
        $categories = $this->syncCategories($organization);
        $this->syncProducts($categories);
        $this->syncProductCatalogs();
        $this->syncProductSpecifications();
        $services = $this->syncServices($organization);
        $this->syncBlogs($organization);
        $this->retireOldDesignContent($organization);
        $this->syncHomePartners($organization);
        $this->syncSections($organization);
        $this->syncBranches($organization);
        $this->seedFaqs();
        $this->seedRedirects();
        $this->seedInbox($organization, $services);

        $this->command?->info('King Motors demo content refreshed.');
    }

    protected function seedSiteSettings(Organization $organization): void
    {
        $site = SiteSetting::query()->firstOrCreate(
            ['organization_id' => $organization->id],
            []
        );

        $extra = $site->extra ?? [];
        unset($extra['fax']);

        $site->update([
            'site_name_en' => 'King Motors',
            'site_name_ar' => 'كينج موتورز',
            'default_locale' => 'ar',
            'cta_phone' => [
                ['code' => '20', 'number' => '1001234567'],
                ['code' => '20', 'number' => '1007654321'],
            ],
            'cta_whatsapp' => [
                ['code' => '20', 'number' => '1001234567'],
            ],
            'cta_label_en' => 'Contact Us',
            'cta_label_ar' => 'تواصل معنا',
            'support_email' => 'info@kingmotors.com',
            'notification_emails' => 'info@kingmotors.com',
            'footer_copyright_en' => '© '.date('Y').' King Motors. All rights reserved.',
            'footer_copyright_ar' => '© '.date('Y').' King Motors. جميع الحقوق محفوظة.',
            'footer_body_en' => 'Motors, pumps, pipes and cables for integrated water pumping solutions.',
            'footer_body_ar' => 'نوفر مجموعة متكاملة من الطلمبات والمواتير وحلول نقل المياه لمختلف القطاعات الصناعية والزراعية والتجارية.',
            'extra' => $extra,
            'coming_soon_enabled' => false,
            'coming_soon_title_en' => 'Coming Soon',
            'coming_soon_title_ar' => 'قريباً',
            'coming_soon_message_en' => 'We are preparing the King Motors website.',
            'coming_soon_message_ar' => 'نجهّز موقع كينج موتورز.',
        ]);

        $links = [
            [SocialLinkPlatform::Facebook, '#', 1],
            [SocialLinkPlatform::LinkedIn, '#', 2],
            [SocialLinkPlatform::Youtube, '#', 3],
        ];

        $keptPlatforms = [];

        foreach ($links as [$platform, $url, $order]) {
            SiteSocialLink::query()->updateOrCreate(
                [
                    'site_setting_id' => $site->id,
                    'platform' => $platform->value,
                ],
                [
                    'url' => $url,
                    'sort_order' => $order,
                    'is_active' => true,
                ]
            );

            $keptPlatforms[] = $platform->value;
        }

        SiteSocialLink::query()
            ->where('site_setting_id', $site->id)
            ->whereNotIn('platform', $keptPlatforms)
            ->update(['is_active' => false]);
    }

    protected function seedMailSettings(): void
    {
        MailSetting::query()->update(['is_active' => false]);

        MailSetting::query()->updateOrCreate(
            ['from_address' => 'info@kingmotors.com'],
            [
                'is_active' => false,
                'mailer' => 'log',
                'host' => null,
                'port' => 587,
                'username' => null,
                'password' => null,
                'encryption' => 'tls',
                'from_name' => 'King Motors',
            ]
        );
    }

    /**
     * @return array<string, Category>
     */
    protected function syncCategories(Organization $organization): array
    {
        $tree = [
            'deep-well-pumps' => [
                'parent' => null,
                'name' => ['en' => 'Deep Well Pumps', 'ar' => 'طلمبات اعماق'],
                'short_text' => [
                    'en' => 'Deep well pump brands: IMPT, VAN SAN, GOLD, Poldap, SP, TESLA and KPS.',
                    'ar' => 'براندات طلمبات الأعماق: IMPT و VAN SAN و GOLD و Poldap و SP و TESLA و KPS.',
                ],
                'image' => 'products/pump-submersible.webp',
            ],
            'deep-well-motors' => [
                'parent' => null,
                'name' => ['en' => 'Deep Well Motors', 'ar' => 'مواتير اعماق'],
                'short_text' => [
                    'en' => 'Deep well motor brands: SP, POLDAP, KPS, Van San, IPMT and Gold.',
                    'ar' => 'براندات مواتير الأعماق: SP و POLDAP و KPS و Van San و IPMT و Gold.',
                ],
                'image' => 'products/motor-ie3.webp',
            ],
            'deep-well-pipes' => [
                'parent' => null,
                'name' => ['en' => 'Deep Well Pipes', 'ar' => 'مواسير اعماق'],
                'short_text' => [
                    'en' => 'Deep well pipe brands: Bore Power, Pure Life, Ashirvad and Pure Pipe.',
                    'ar' => 'براندات مواسير الأعماق: Bore Power و Pure Life و Ashirvad و Pure Pipe.',
                ],
                'image' => 'products/pipe-hdpe.webp',
            ],
            'submersible-cables' => [
                'parent' => null,
                'name' => ['en' => 'Submersible Cables', 'ar' => 'كابلات بحارى'],
                'short_text' => [
                    'en' => 'Submersible cable brands: Carkit, Axelon and SEVAL.',
                    'ar' => 'براندات الكابلات البحاري: Carkit و Axelon و SEVAL.',
                ],
                'image' => 'products/cable-submersible.webp',
            ],
            'electrical-panels' => [
                'parent' => null,
                'name' => ['en' => 'Electrical Panels', 'ar' => 'لوحات كهربائية'],
                'short_text' => [
                    'en' => 'Delixi inverter panels.',
                    'ar' => 'لوحات انفرتر Delixi.',
                ],
                'image' => 'products/cable-power.webp',
            ],
            'inverter-panels' => [
                'parent' => 'electrical-panels',
                'name' => ['en' => 'Inverter Panels', 'ar' => 'لوحات انفرتر'],
                'short_text' => [
                    'en' => 'Delixi inverter panels.',
                    'ar' => 'لوحات انفرتر Delixi.',
                ],
                'image' => 'products/cable-power.webp',
            ],
        ];

        $categories = [];

        foreach ($tree as $index => $item) {
            $slug = (string) $index;
            $category = $this->findBySlug(Category::class, $slug, $organization->id) ?? new Category([
                'organization_id' => $organization->id,
            ]);
            $isRoot = $item['parent'] === null;

            $category->fill([
                'organization_id' => $organization->id,
                'parent_id' => $isRoot ? null : $categories[$item['parent']]->id,
                'name' => $item['name'],
                'short_text' => $item['short_text'],
                'text' => [
                    'en' => '<p>'.$item['short_text']['en'].'</p>',
                    'ar' => '<p>'.$item['short_text']['ar'].'</p>',
                ],
                'meta_title' => $item['name'],
                'meta_desc' => $item['short_text'],
                'slug' => ['en' => $slug, 'ar' => $slug],
                'order' => count($categories) + 1,
                'is_active' => true,
                'show_in_navbar' => $isRoot,
                'show_in_footer' => $isRoot,
                'is_main_section' => $isRoot,
            ]);
            $category->save();
            $this->lockSlug($category, $slug);
            $this->attachDesignMedia($category, 'categories', $item['image']);
            $categories[$slug] = $category->fresh();
        }

        Category::query()
            ->where('organization_id', $organization->id)
            ->whereNotIn('id', collect($categories)->pluck('id'))
            ->update(['is_active' => false, 'show_in_navbar' => false, 'show_in_footer' => false]);

        return $categories;
    }

    /**
     * @param  array<string, Category>  $categories
     */
    protected function syncProducts(array $categories): void
    {
        $products = [
            ['van-san-deep-well-pump', 'KM-P-VANSAN', 'deep-well-pumps', 'VAN SAN', 'VAN SAN', 'Deep well pump', 'طلمبة اعماق', 'products/brands/km-p-vansan.jpg'],
            ['gold-deep-well-pump', 'KM-P-GOLD', 'deep-well-pumps', 'GOLD', 'GOLD', 'Deep well pump', 'طلمبة اعماق', 'products/brands/km-p-gold.jpg'],
            ['sp-deep-well-pump', 'KM-P-SP', 'deep-well-pumps', 'SP', 'SP', 'Deep well pump', 'طلمبة اعماق', 'products/brands/km-p-sp.jpg'],
            ['tesla-deep-well-pump', 'KM-P-TESLA', 'deep-well-pumps', 'TESLA', 'TESLA', 'Deep well pump', 'طلمبة اعماق', 'products/brands/km-p-tesla.jpg'],
            ['kps-deep-well-pump', 'KM-P-KPS', 'deep-well-pumps', 'KPS', 'KPS', 'Deep well pump', 'طلمبة اعماق', 'products/brands/km-p-kps.jpg'],
            ['sp-deep-well-motor', 'KM-M-SP', 'deep-well-motors', 'SP', 'SP', 'Deep well motor', 'موتور اعماق', 'products/brands/km-m-sp.jpg'],
            ['poldap-deep-well-motor', 'KM-M-POLDAP', 'deep-well-motors', 'POLDAP', 'POLDAP', 'Deep well motor', 'موتور اعماق', 'products/brands/km-m-poldap.jpg'],
            ['kps-deep-well-motor', 'KM-M-KPS', 'deep-well-motors', 'KPS', 'KPS', 'Deep well motor', 'موتور اعماق', 'products/brands/km-m-kps.jpg'],
            ['van-san-deep-well-motor', 'KM-M-VANSAN', 'deep-well-motors', 'Van San', 'Van San', 'Deep well motor', 'موتور اعماق', 'products/brands/km-m-vansan.jpg'],
            ['ipmt-deep-well-motor', 'KM-M-IPMT', 'deep-well-motors', 'IPMT', 'IPMT', 'Deep well motor', 'موتور اعماق', 'products/brands/km-m-ipmt.jpg'],
            ['bore-power-pipe', 'KM-PIPE-BORE', 'deep-well-pipes', 'Bore Power', 'Bore Power', 'Deep well pipe', 'ماسورة اعماق', 'products/brands/km-pipe-bore.jpg'],
            ['pure-life-pipe', 'KM-PIPE-PURELIFE', 'deep-well-pipes', 'Pure Life', 'Pure Life', 'Deep well pipe', 'ماسورة اعماق', 'products/brands/km-pipe-purelife.jpg'],
            ['ashirvad-pipe', 'KM-PIPE-ASHIRVAD', 'deep-well-pipes', 'Ashirvad', 'Ashirvad', 'Deep well pipe', 'ماسورة اعماق', 'products/brands/km-pipe-ashirvad.jpg'],
            ['pure-pipe', 'KM-PIPE-PURE', 'deep-well-pipes', 'Pure Pipe', 'Pure Pipe', 'Deep well pipe', 'ماسورة اعماق', 'products/brands/km-pipe-pure.jpg'],
            ['carkit-cable', 'KM-C-CARKIT', 'submersible-cables', 'Carkit', 'Carkit', 'Submersible cable', 'كابل بحارى', 'products/brands/km-c-carkit.jpg'],
            ['axelon-cable', 'KM-C-AXELON', 'submersible-cables', 'Axelon', 'Axelon', 'Submersible cable', 'كابل بحارى', 'products/brands/km-c-axelon.jpg'],
            ['seval-cable', 'KM-C-SEVAL', 'submersible-cables', 'SEVAL', 'SEVAL', 'Submersible cable', 'كابل بحارى', 'products/brands/km-c-seval.jpg'],
            ['delixi-inverter-panel', 'KM-INV-DELIXI', 'inverter-panels', 'Delixi', 'Delixi', 'Inverter panel', 'لوحة انفرتر', 'products/brands/km-inv-delixi.jpg'],
        ];

        $keptIds = [];

        foreach ($products as $index => [$slug, $code, $category, $nameEn, $nameAr, $shortEn, $shortAr, $image]) {
            $product = Product::query()->where('code', $code)->first() ?? new Product;
            $product->fill([
                'category_id' => $categories[$category]->id,
                'name' => ['en' => $nameEn, 'ar' => $nameAr],
                'short_description' => ['en' => $shortEn, 'ar' => $shortAr],
                'description' => [
                    'en' => '<p>'.e($shortEn).'</p>',
                    'ar' => '<p>'.e($shortAr).'</p>',
                ],
                'price' => 0,
                'code' => $code,
                'quantity' => 50,
                'is_active' => true,
                'status' => ProductStatusEnum::FEATURED->value,
                'sort_order' => $index + 1,
                'meta_title' => ['en' => $nameEn, 'ar' => $nameAr],
                'meta_desc' => ['en' => $shortEn, 'ar' => $shortAr],
                'slug' => ['en' => $slug, 'ar' => $slug],
                'catalog_sizes' => [],
                'catalog_colors' => [],
            ]);
            $product->save();
            $this->lockSlug($product, $slug);
            $product->tags()->sync([]);
            $this->attachDesignMedia($product, 'products', $image);
            $keptIds[] = $product->id;
        }

        Product::query()
            ->whereNotIn('id', $keptIds)
            ->update(['is_active' => false]);
    }

    /**
     * @return list<Service>
     */
    protected function syncServices(Organization $organization): array
    {
        $items = [
            ['technical-consulting', 'Technical Consulting', 'استشارات', 'Technical advice to choose the right pumping equipment.', 'نقدم استشارات فنية لمساعدتك في اختيار الحل والمعدات الأنسب وفقاً لاحتياجات مشروعك.', 'services/service-consulting.webp'],
            ['spare-parts', 'Spare Parts', 'قطع غيار', 'Reliable spare parts for pumping systems.', 'نوفر قطع غيار موثوقة لمختلف أنظمة الضخ والمعدات لضمان الأداء والاستمرارية.', 'services/service-spareparts.webp'],
            ['warranty-maintenance', 'Warranty Maintenance', 'صيانة بالضمان', 'Specialized maintenance and technical support.', 'خدمات صيانة متخصصة ودعم فني موثوق للحفاظ على كفاءة المعدات واستمرارية التشغيل.', 'services/service-maintenance.webp'],
            ['supply-and-installation', 'Supply and Installation', 'توريد وتركيب', 'Supply and installation of pumping systems.', 'نوفر حلول متكاملة لتوريد وتركيب أنظمة الضخ والمعدات، بما يضمن التشغيل بكفاءة واعتمادية عالية.', 'services/service-supply.webp'],
        ];

        $services = [];

        foreach ($items as $index => [$slug, $titleEn, $titleAr, $shortEn, $shortAr, $image]) {
            $service = $this->findBySlug(Service::class, $slug, $organization->id) ?? new Service([
                'organization_id' => $organization->id,
            ]);

            $service->fill([
                'organization_id' => $organization->id,
                'parent_id' => null,
                'title' => ['en' => $titleEn, 'ar' => $titleAr],
                'label' => ['en' => $titleEn, 'ar' => $titleAr],
                'short_text' => ['en' => $shortEn, 'ar' => $shortAr],
                'text' => ['en' => '<p>'.e($shortEn).'</p>', 'ar' => '<p>'.e($shortAr).'</p>'],
                'meta_title' => ['en' => $titleEn, 'ar' => $titleAr],
                'meta_desc' => ['en' => $shortEn, 'ar' => $shortAr],
                'slug' => ['en' => $slug, 'ar' => $slug],
                'is_active' => true,
                'order' => $index + 1,
            ]);
            $service->save();
            $this->lockSlug($service, $slug);
            $this->attachDesignMedia($service, 'services', $image);
            $services[] = $service->fresh();
        }

        Service::query()
            ->where('organization_id', $organization->id)
            ->whereNotIn('id', collect($services)->pluck('id'))
            ->update(['is_active' => false]);

        return $services;
    }

    protected function syncBlogs(Organization $organization): void
    {
        $category = BlogCategory::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'slug->en' => 'articles'],
            [
                'name' => ['en' => 'Articles', 'ar' => 'مقالات'],
                'slug' => ['en' => 'articles', 'ar' => 'articles'],
                'order' => 1,
                'is_active' => true,
            ]
        );
        $this->lockSlug($category, 'articles');

        $posts = [
            ['pump-selection-guide', 'A complete guide to choosing the right pump', 'دليل شامل لاختيار الطلمبة المناسبة لمشروعك', 'The factors that matter when selecting a pump.', 'تعرف على أهم العوامل التي يجب مراعاتها عند اختيار الطلمبة المناسبة.', 'articles/article-featured.webp', '2025-09-15'],
            ['pump-types', 'Main pump types and their uses', 'أهم أنواع الطلمبات واستخداماتها', 'Industrial, agricultural and domestic pump types.', 'استعرض أشهر أنواع الطلمبات الصناعية والزراعية والمنزلية.', 'articles/article-types.webp', '2025-08-10'],
            ['pump-efficiency', 'Tips to improve pump efficiency', 'نصائح لزيادة كفاءة الطلمبات', 'How to keep pumps efficient for longer.', 'تعرف على أهم النصائح للحفاظ على كفاءة الطلمبات.', 'articles/article-efficiency.webp', '2025-08-28'],
            ['industrial-pumping-systems', 'Water pumping systems in industrial plants', 'أنظمة ضخ المياه في المنشآت الصناعية', 'How industrial pumping systems are planned.', 'كيف يتم تخطيط أنظمة الضخ في المنشآت الصناعية.', 'articles/article-systems.webp', '2025-07-20'],
            ['submersible-maintenance-schedule', 'Submersible pump maintenance schedule', 'جدول الصيانة الدورية للطلمبات الغاطسة', 'A practical maintenance schedule for submersible pumps.', 'جدول عملي للصيانة الدورية للطلمبات الغاطسة.', 'articles/article-efficiency.webp', '2025-07-02'],
            ['october-branch', 'King Motors opens its new 6th of October branch', 'كينج موتورز تفتتح فرعها الجديد في 6 أكتوبر', 'The new branch serves Giza and nearby industrial zones.', 'الفرع الجديد يخدم الجيزة والمناطق الصناعية القريبة.', 'articles/article-featured.webp', '2025-06-18'],
            ['ie2-vs-ie3', 'IE2 vs IE3 motors and which saves more', 'الفرق بين مواتير IE2 و IE3 وأيهما أوفر', 'How efficiency classes affect running cost.', 'كيف تؤثر فئة الكفاءة على تكلفة التشغيل.', 'articles/article-types.webp', '2025-06-01'],
        ];

        $keptIds = [$category->id];
        $blogIds = [];

        foreach ($posts as $index => [$slug, $titleEn, $titleAr, $excerptEn, $excerptAr, $image, $date]) {
            $blog = Blog::query()->where('slug->en', $slug)->first() ?? new Blog([
                'organization_id' => $organization->id,
            ]);

            $blog->fill([
                'organization_id' => $organization->id,
                'blog_category_id' => $category->id,
                'title' => ['en' => $titleEn, 'ar' => $titleAr],
                'excerpt' => ['en' => $excerptEn, 'ar' => $excerptAr],
                'content' => ['en' => '<p>'.e($excerptEn).'</p>', 'ar' => '<p>'.e($excerptAr).'</p>'],
                'slug' => ['en' => $slug, 'ar' => $slug],
                'meta_title' => ['en' => $titleEn, 'ar' => $titleAr],
                'meta_desc' => ['en' => $excerptEn, 'ar' => $excerptAr],
                'order' => $index + 1,
                'is_active' => true,
                'published_at' => $date,
            ]);
            $blog->save();
            $this->lockSlug($blog, $slug);
            $this->attachDesignMedia($blog, 'blogs', $image);
            $blogIds[] = $blog->id;
        }

        Blog::query()
            ->where('organization_id', $organization->id)
            ->whereNotIn('id', $blogIds)
            ->update(['is_active' => false]);

        BlogCategory::query()
            ->where('organization_id', $organization->id)
            ->whereNotIn('id', $keptIds)
            ->update(['is_active' => false]);
    }

    protected function retireOldDesignContent(Organization $organization): void
    {
        Partner::query()
            ->where('organization_id', $organization->id)
            ->update(['is_active' => false, 'show_in_home' => false]);

        Project::query()->update(['is_active' => false, 'show_in_home' => false]);
    }

    protected function syncHomePartners(Organization $organization): void
    {
        $names = ['VAN SAN', 'GOLD', 'SP', 'TESLA', 'KPS', 'POLDAP', 'IPMT', 'BORE', 'PURE LIFE', 'ASHIRVAD', 'CARKIT', 'AXELON', 'SEVAL', 'DELIXI'];

        foreach ($names as $index => $name) {
            Partner::query()->updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'order' => $index + 1,
                ],
                [
                    'name' => ['en' => $name, 'ar' => $name],
                    'show_in_home' => true,
                    'is_active' => true,
                ],
            );
        }
    }

    protected function syncSections(Organization $organization): void
    {
        $definitions = [
            [PageEnum::HOME, SectionTypeEnum::HERO, 1, [
                'sub_title' => ['en' => 'King Motors', 'ar' => 'كينج موتورز'],
                'title' => ['en' => "Integrated solutions\nfor water pumping", 'ar' => "حلول متكاملة\nلضخ المياه"],
                'text' => ['en' => 'Supply of submersible motors, pumps, pipes and cables for agricultural, industrial and project water systems.', 'ar' => 'توريد مواتير غاطسة وطلمبات ومواسير وكابلات لحلول ضخ المياه المتكاملة للزراعة والصناعة والمشروعات المختلفة.'],
                'button_text' => ['en' => 'Discover our products', 'ar' => 'اكتشف منتجاتنا'],
                'button_link_url' => '/products',
                'image' => 'hero/hero-1.webp',
            ]],
            [PageEnum::HOME, SectionTypeEnum::HERO, 2, [
                'sub_title' => ['en' => 'King Motors', 'ar' => 'كينج موتورز'],
                'title' => ['en' => "Submersible pumps and motors\nwith high efficiency", 'ar' => "طلمبات ومواتير غاطسة\nبأعلى كفاءة واعتمادية"],
                'text' => ['en' => 'Water pumping technology for wells and large agricultural and industrial projects.', 'ar' => 'أحدث تكنولوجيا ضخ المياه للآبار الارتوازية والمشروعات الزراعية والصناعية الكبرى بمواصفات قياسية عالمية.'],
                'button_text' => ['en' => 'Explore pumps', 'ar' => 'استكشف الطلمبات'],
                'button_link_url' => '/products',
                'image' => 'hero/hero-2.webp',
            ]],
            [PageEnum::HOME, SectionTypeEnum::HERO, 3, [
                'sub_title' => ['en' => 'King Motors', 'ar' => 'كينج موتورز'],
                'title' => ['en' => "Networks and cables\nfor reliable operation", 'ar' => "شبكات وكابلات متطورة\nلقوة تشغيل مستدامة"],
                'text' => ['en' => 'Submersible cables, high-pressure pipes, and protection panels built for continuous flow.', 'ar' => 'كابلات غاطسة، مواسير مقاومة للضغوط العالية، ولوحات تحكم وحماية كهربائية مصممة لضمان استمرارية التدفق.'],
                'button_text' => ['en' => 'Our services', 'ar' => 'خدماتنا وقدراتنا'],
                'button_link_url' => '/services',
                'image' => 'hero/hero-3.webp',
            ]],
            [PageEnum::HOME, SectionTypeEnum::ABOUTUS, 1, [
                'sub_title' => ['en' => 'About the company', 'ar' => 'عن الشركة'],
                'title' => ['en' => "Experience that supports\nevery drop of water", 'ar' => "خبرة تمتد\nلدعم كل قطرة ماء"],
                'text' => ['en' => 'King Motors supplies motors, pumps, pipes and cables for water pumping across agriculture, industry and projects.', 'ar' => 'في كينج موتورز نعمل على توفير مجموعة متكاملة من المواتير والطلمبات والمواسير والكابلات لحلول ضخ المياه للزراعة والصناعة والمشروعات المختلفة، مع الالتزام بأعلى معايير الجودة والكفاءة.'],
                'button_text' => ['en' => 'Learn more about us', 'ar' => 'تعرف أكثر عنا'],
                'button_link_url' => '/about-us',
                'image' => 'about/about.webp',
            ]],
            [PageEnum::HOME, SectionTypeEnum::SERVICES, 1, [
                'sub_title' => ['en' => 'Our services', 'ar' => 'خدماتنا'],
                'title' => ['en' => "Our\nmain services", 'ar' => "خدماتنا\nالرئيسية"],
                'text' => ['en' => 'Specialized support for water pumping systems across different projects.', 'ar' => 'حلول متكاملة ومتخصصة لدعم أنظمة ضخ المياه وتلبية احتياجات مختلف المشروعات بكفاءة واعتمادية.'],
                'button_text' => ['en' => 'View all services', 'ar' => 'عرض جميع الخدمات'],
                'button_link_url' => '/services',
            ]],
            [PageEnum::HOME, SectionTypeEnum::FEATURED_PRODUCTS, 1, [
                'sub_title' => ['en' => 'Product categories', 'ar' => 'فئات المنتجات'],
                'title' => ['en' => "Our\nproduct categories", 'ar' => "فئات\nمنتجاتنا"],
                'text' => ['en' => 'A complete range of pumping systems and industrial equipment.', 'ar' => 'مجموعة متكاملة من منتجات أنظمة الضخ والمعدات الصناعية لتلبية احتياجات مختلف المشروعات بكفاءة وجودة عالية.'],
                'button_text' => ['en' => 'View all products', 'ar' => 'عرض جميع المنتجات'],
                'button_link_url' => '/products',
                'image' => 'products/products-banner.webp',
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS, 1, [
                'sub_title' => ['en' => 'Pump selection', 'ar' => 'اختيار الطلمبة'],
                'title' => ['en' => "Choose\nthe right pump\nfor the duty point", 'ar' => "اختر\nالطلمبة المناسبة\nلنقطة التشغيل"],
                'text' => ['en' => 'Enter the required flow and total head to identify a suitable pump.', 'ar' => 'أدخل معدل التصرف والضغط الكلي المطلوب، لنساعدك في تحديد الطلمبة الأنسب من مجموعة منتجاتنا بسرعة وسهولة.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 1, [
                'title' => ['en' => 'Specialist support', 'ar' => 'دعم فني متخصص'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 2, [
                'title' => ['en' => 'A wide product range', 'ar' => 'مجموعة واسعة من المنتجات'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 3, [
                'title' => ['en' => 'Saves time and effort', 'ar' => 'يوفر الوقت والجهد'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 4, [
                'title' => ['en' => 'Accurate selection', 'ar' => 'دقة في الاختيار'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::CLIENTS, 1, [
                'sub_title' => ['en' => 'Success partners', 'ar' => 'شركاء النجاح'],
                'title' => ['en' => "Brands\nwe trust", 'ar' => "ماركات\nعالمية\nنثق فيها"],
            ]],
            [PageEnum::HOME, SectionTypeEnum::WHY_CHOOSE_US, 1, [
                'sub_title' => ['en' => 'Why us', 'ar' => 'لماذا نحن'],
                'title' => ['en' => "Why choose\nKing Motors?", 'ar' => "ليه تختار\nكينج موتورز؟"],
                'text' => ['en' => 'A reliable partner for motors, pumps, pipes and cables.', 'ar' => 'شريكك الموثوق في توريد المواتير والمضخات والمواسير والكابلات، بخبرة فنية تضمن لك الحل المناسب لكل مشروع وبأفضل قيمة.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 1, [
                'title' => ['en' => 'Guaranteed quality', 'ar' => 'جودة مضمونة'],
                'text' => ['en' => 'Original products from trusted brands with a certified warranty.', 'ar' => 'منتجات أصلية من أقوى الماركات العالمية بضمان معتمد.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 2, [
                'title' => ['en' => 'Technical experience', 'ar' => 'خبرة فنية'],
                'text' => ['en' => 'Engineers help you choose the product that fits the job.', 'ar' => 'فريق مهندسين يساعدك تختار المنتج الأنسب لاحتياجك.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 3, [
                'title' => ['en' => 'Fast supply', 'ar' => 'توريد سريع'],
                'text' => ['en' => 'Ready stock and delivery across the country.', 'ar' => 'مخزون جاهز وتوصيل سريع لكل محافظات الجمهورية.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 4, [
                'title' => ['en' => 'After-sales support', 'ar' => 'دعم ما بعد البيع'],
                'text' => ['en' => 'Maintenance, spare parts and follow-up after installation.', 'ar' => 'صيانة وقطع غيار ومتابعة مستمرة بعد التركيب.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 1, [
                'title' => ['en' => '15+', 'ar' => '15+'],
                'text' => ['en' => 'Years in the market', 'ar' => 'سنة خبرة في السوق'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 2, [
                'title' => ['en' => '2500+', 'ar' => '2500+'],
                'text' => ['en' => 'Clients who trust us', 'ar' => 'عميل يثق فينا'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 3, [
                'title' => ['en' => '600+', 'ar' => '600+'],
                'text' => ['en' => 'Completed projects', 'ar' => 'مشروع تم تنفيذه'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 4, [
                'title' => ['en' => '1200+', 'ar' => '1200+'],
                'text' => ['en' => 'Available products', 'ar' => 'منتج متاح'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BLOGS, 1, [
                'sub_title' => ['en' => 'Our articles', 'ar' => 'مقالاتنا'],
                'title' => ['en' => "Latest\narticles", 'ar' => "أحدث\nالمقالات"],
                'text' => ['en' => 'Technical notes that help you choose the right pump and understand water systems.', 'ar' => 'مقالات ونصائح تقنية تساعدك في اختيار الطلمبة المناسبة وفهم أنظمة ضخ المياه والصناعات المختلفة.'],
                'button_text' => ['en' => 'View all articles', 'ar' => 'عرض جميع المقالات'],
                'button_link_url' => '/news',
            ]],
            [PageEnum::HOME, SectionTypeEnum::CONTACT_US_CONTENT, 1, [
                'sub_title' => ['en' => 'Call us', 'ar' => 'اتصل بنا'],
                'title' => ['en' => "Contact\nus", 'ar' => "تواصل\nمعـنا"],
                'text' => ['en' => 'Our team helps you choose the right pump for the project.', 'ar' => 'فريقنا جاهز لمساعدتك في اختيار الطلمبة الأنسب وتقديم الحلول المناسبة لمتطلبات مشروعك.'],
                'image' => 'contact/contact-pumps.webp',
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::CONTACT_US_CONTENT, 1, [
                'title' => ['en' => 'Contact us', 'ar' => 'تواصل معنا'],
                'text' => ['en' => 'Tell us about the project and we will help you choose the right equipment.', 'ar' => 'احكيلنا عن المشروع وهنساعدك تختار المعدة المناسبة.'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::BENEFITS, 1, [
                'title' => ['en' => 'Reply within two hours', 'ar' => 'رد خلال ساعتين'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::BENEFITS, 2, [
                'title' => ['en' => 'Delivery across the country', 'ar' => 'توصيل لكل المحافظات'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::BENEFITS, 3, [
                'title' => ['en' => '+15 years of experience', 'ar' => '+15 سنة خبرة'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::CONTENT, 1, [
                'sub_title' => ['en' => 'Contact channels', 'ar' => 'طرق التواصل'],
                'title' => ['en' => 'Choose what suits you', 'ar' => 'اختار الأنسب ليك'],
                'text' => ['en' => 'We are available on every channel. Pick the one you prefer and we will reply as soon as we can.', 'ar' => 'إحنا موجودين على كل القنوات، اختار اللي يريحك وهنرد عليك في أسرع وقت.'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::FEATURES, 1, [
                'title' => ['en' => 'WhatsApp', 'ar' => 'واتساب'],
                'text' => ['en' => 'Fastest way to reply and send photos of the pump or the fault.', 'ar' => 'الأسرع للرد وإرسال صور الطلمبة أو العطل.'],
                'button_text' => ['en' => 'Start a chat', 'ar' => 'ابدأ المحادثة'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::FEATURES, 2, [
                'title' => ['en' => 'Direct call', 'ar' => 'اتصال مباشر'],
                'text' => ['en' => 'Call a sales engineer if the request is urgent.', 'ar' => 'كلم مهندس المبيعات لو طلبك مستعجل.'],
                'button_text' => ['en' => 'Call now', 'ar' => 'اتصل الآن'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::FEATURES, 3, [
                'title' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
                'text' => ['en' => 'For official quotations and tenders.', 'ar' => 'لعروض الأسعار الرسمية والمناقصات.'],
                'button_text' => ['en' => 'Send an email', 'ar' => 'ابعت إيميل'],
            ]],
        ];

        $keptIds = [];

        foreach ($definitions as [$page, $type, $order, $data]) {
            $image = $data['image'] ?? null;
            unset($data['image']);

            $section = Section::query()->updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'page' => $page->value,
                    'type' => $type->value,
                    'order' => $order,
                ],
                array_merge([
                    'sub_title' => null,
                    'text' => null,
                    'button_text' => null,
                    'button_link_url' => null,
                    'alt_image' => null,
                ], $data, [
                    'is_active' => true,
                ])
            );

            if (is_string($image)) {
                $this->attachDesignMedia($section, 'sections', $image);
            } else {
                $section->clearMediaCollection('sections');
            }

            $keptIds[] = $section->id;
        }

        Section::query()
            ->where('organization_id', $organization->id)
            ->whereNotIn('id', $keptIds)
            ->update(['is_active' => false]);
    }

    protected function syncBranches(Organization $organization): void
    {
        $branch = Branch::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'type' => BranchTypeEnum::Main,
            ],
            [
                'name' => ['en' => '6th of October', 'ar' => '6 أكتوبر'],
                'address' => ['en' => '10 Industrial Street, 6th of October, Giza, Egypt', 'ar' => '10 شارع الصناعة الصناعية، 6 أكتوبر، الجيزة، مصر'],
                'phone_1_country_code' => '20',
                'phone_1' => '1001234567',
                'phone_2_country_code' => '20',
                'phone_2' => '1007654321',
                'email' => 'info@kingmotors.com',
                'working_hours' => ['en' => 'Sunday to Thursday, 9:00 AM – 5:00 PM', 'ar' => 'من الأحد إلى الخميس، 9:00 صباحاً – 5:00 مساءً'],
                'map_url' => 'https://www.google.com/maps?q=6th+of+October+City,Giza,Egypt',
                'map_embed' => 'https://www.google.com/maps?q=6th+of+October+City,Giza,Egypt&z=13&output=embed',
                'is_active' => true,
                'order' => 1,
            ]
        );

        Branch::query()
            ->where('organization_id', $organization->id)
            ->whereKeyNot($branch->id)
            ->update(['is_active' => false]);
    }

    protected function seedFaqs(): void
    {
        $items = [
            [
                'question' => ['en' => 'How do I choose the right pump?', 'ar' => 'إزاي أختار الطلمبة المناسبة؟'],
                'answer' => ['en' => 'Share the required flow, head and power source and our team will recommend a pump.', 'ar' => 'ابعت التصرف والضغط ومصدر الكهرباء، وفريقنا هيقترح الطلمبة المناسبة.'],
            ],
            [
                'question' => ['en' => 'Do you supply spare parts?', 'ar' => 'بتوفروا قطع غيار؟'],
                'answer' => ['en' => 'Yes. We supply spare parts for pumps, motors and related equipment.', 'ar' => 'أيوة، بنوفر قطع غيار للمضخات والمواتير والمعدات المرتبطة.'],
            ],
            [
                'question' => ['en' => 'What are your working hours?', 'ar' => 'مواعيد العمل إيه؟'],
                'answer' => ['en' => 'Sunday to Thursday, 9:00 AM to 5:00 PM.', 'ar' => 'من الأحد إلى الخميس، من 9:00 صباحاً حتى 5:00 مساءً.'],
            ],
        ];

        foreach ($items as $index => $item) {
            Faq::query()->updateOrCreate(
                ['sort_order' => $index + 1],
                [
                    'question' => $item['question'],
                    'answer' => $item['answer'],
                    'is_active' => true,
                ]
            );
        }

        Faq::query()
            ->where('sort_order', '>', count($items))
            ->update(['is_active' => false]);
    }

    protected function seedRedirects(): void
    {
        Redirect::query()->where('source', '/old-home')->update([
            'is_active' => false,
            'notes' => 'Retired Mina Pack sample redirect',
        ]);
    }

    /**
     * @param  list<Service>  $services
     */
    protected function seedInbox(Organization $organization, array $services): void
    {
        ContactMessage::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'email' => 'client@example.com',
            ],
            [
                'service_id' => $services[0]->id ?? null,
                'name' => 'أحمد محمود',
                'phone' => '+201000000000',
                'project_type' => 'pump',
                'message' => 'محتاج عرض سعر لمضخة غاطسة بتصرف 18 متر مكعب في الساعة.',
                'is_read' => false,
            ]
        );

        NewsletterSubscriber::query()
            ->where('email', 'guest@minapack.example')
            ->update(['email' => 'guest@kingmotors.example']);

        NewsletterSubscriber::query()->firstOrCreate(
            [
                'organization_id' => $organization->id,
                'email' => 'guest@kingmotors.example',
            ]
        );
    }

    protected function lockSlug(Model $model, string $slug): void
    {
        if (! method_exists($model, 'setTranslation')) {
            return;
        }

        $model->setTranslation('slug', 'en', $slug);
        $model->setTranslation('slug', 'ar', $slug);
        $model->saveQuietly();
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function findBySlug(string $modelClass, string $slug, int|string $organizationId): ?Model
    {
        return $modelClass::query()
            ->where('organization_id', $organizationId)
            ->where(function ($query) use ($slug): void {
                $query->where('slug->en', $slug)->orWhere('slug->ar', $slug);
            })
            ->first();
    }

    protected function syncProductCatalogs(): void
    {
        $this->publishCatalogDirectory();

        $assignments = [
            'KM-P-VANSAN' => [['Data Vansan Pumps', 'VAN SAN pumps catalogue', 'كتالوج طلمبات VAN SAN']],
            'KM-P-GOLD' => [['Data Gold Pumps', 'GOLD pumps catalogue', 'كتالوج طلمبات GOLD']],
            'KM-P-SP' => [
                ['Data SP pumps', 'SP pumps data', 'بيانات طلمبات SP'],
                ['SP catalogue', 'SP 4, 5 and 6 inch catalogue', 'كتالوج SP 4 و5 و6 بوصة'],
            ],
            'KM-P-TESLA' => [['TESLA CATALOUGE', 'TESLA catalogue', 'كتالوج TESLA']],
            'KM-P-KPS' => [['Data Kps Pumps', 'KPS pumps catalogue', 'كتالوج طلمبات KPS']],
            'KM-M-SP' => [['SP catalogue', 'SP 4, 5 and 6 inch catalogue', 'كتالوج SP 4 و5 و6 بوصة']],
            'KM-M-POLDAP' => [['Data Poldap Motors', 'Poldap motors catalogue', 'كتالوج مواتير Poldap']],
            'KM-M-KPS' => [
                ['Data 8 inch kps motor', 'KPS 8 inch motor data', 'بيانات موتور KPS 8 بوصة'],
                ['Data motor kps 6', 'KPS 6 inch motor data', 'بيانات موتور KPS 6 بوصة'],
            ],
            'KM-M-VANSAN' => [['Data Vansan Motors', 'Van San motors catalogue', 'كتالوج مواتير Van San']],
            'KM-M-IPMT' => [['Data IPMT Pumps', 'IPMT catalogue', 'كتالوج IPMT']],
            'KM-PIPE-BORE' => [['Data Bore Power', 'Bore Power catalogue', 'كتالوج Bore Power']],
            'KM-PIPE-PURELIFE' => [['Data Pure Life', 'Pure Life catalogue', 'كتالوج Pure Life']],
            'KM-PIPE-ASHIRVAD' => [['Data  ASHIRVAD', 'Ashirvad uPVC catalogue', 'كتالوج Ashirvad uPVC']],
            'KM-PIPE-PURE' => [['Pure Pipe Catalogue', 'Pure Pipe catalogue', 'كتالوج Pure Pipe']],
            'KM-C-CARKIT' => [['Data Carkit', 'Carkit catalogue', 'كتالوج Carkit']],
            'KM-C-AXELON' => [['AXelon catalogue', 'Axelon catalogue', 'كتالوج Axelon']],
            'KM-C-SEVAL' => [['Data Seval', 'SEVAL catalogue', 'كتالوج SEVAL']],
            'KM-INV-DELIXI' => [['DELIXI SPD-C1', 'Delixi inverter panel data', 'بيانات لوحة انفرتر Delixi']],
        ];

        $files = collect(File::files(resource_path('design/data')));

        foreach ($assignments as $code => $documents) {
            $catalogs = [];

            foreach ($documents as [$prefix, $nameEn, $nameAr]) {
                $match = $files->first(fn (SplFileInfo $file): bool => str_starts_with($file->getFilename(), $prefix));

                if (! $match instanceof SplFileInfo) {
                    continue;
                }

                $catalogs[] = [
                    'file' => $match->getFilename(),
                    'name_en' => $nameEn,
                    'name_ar' => $nameAr,
                ];
            }

            $product = Product::query()->where('code', $code)->first();

            if ($product) {
                $product->update([
                    'catalog_files' => $catalogs === [] ? null : $catalogs,
                ]);
            }
        }
    }

    protected function syncProductSpecifications(): void
    {
        foreach (CatalogSpecifications::definitions() as $code => $specifications) {
            Product::query()->where('code', $code)->update([
                'specifications' => $specifications,
            ]);
        }
    }

    protected function publishCatalogDirectory(): void
    {
        $link = public_path('catalogs');
        $target = resource_path('design/data');

        if (is_dir($link) || ! is_dir($target)) {
            return;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            exec('cmd /c mklink /J '.escapeshellarg($link).' '.escapeshellarg($target));

            return;
        }

        symlink($target, $link);
    }

    protected function attachDesignMedia(object $model, string $collection, string $relativePath): void
    {
        if (! method_exists($model, 'clearMediaCollection') || ! method_exists($model, 'addMedia')) {
            return;
        }

        $absolutePath = public_path('assets/images/'.$relativePath);

        if (! is_file($absolutePath)) {
            return;
        }

        $model->clearMediaCollection($collection);
        $model->addMedia($absolutePath)
            ->preservingOriginal()
            ->toMediaCollection($collection);
    }
}
