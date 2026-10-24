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
use App\Models\ProjectCategory;
use App\Models\Redirect;
use App\Models\Section;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SiteSocialLink;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if ($organization) {
            $organization->update([
                'name_en' => 'Touch of Class',
                'name_ar' => 'Touch of Class',
            ]);
        } else {
            $organization = Organization::query()->create([
                'name_en' => 'Touch of Class',
                'name_ar' => 'Touch of Class',
            ]);
        }

        $this->call(MenuSeeder::class);
        $this->seedSiteSettings($organization);
        $this->seedMailSettings();
        $categories = $this->syncCategories($organization);
        $this->syncProducts($categories);
        $this->syncProductCatalogs();
        $this->syncProductSpecifications();
        $services = $this->syncServices($organization);
        $this->syncBlogs($organization);
        $this->retireOldDesignContent($organization);
        $this->syncHomeProjects();
        $this->syncHomePartners($organization);
        $this->syncSections($organization);
        $this->syncBranches($organization);
        $this->syncFounders();
        $this->seedFaqs();
        $this->seedRedirects();
        $this->seedInbox($organization, $services);
        $this->ensurePublicStorageLink();
        Cache::flush();

        $this->command?->info('Touch of Class demo content refreshed.');
    }

    protected function ensurePublicStorageLink(): void
    {
        $link = public_path('storage');

        if (is_link($link) || file_exists($link)) {
            return;
        }

        Artisan::call('storage:link');
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
            'site_name_en' => 'Touch of Class',
            'site_name_ar' => 'Touch of Class',
            'default_locale' => 'ar',
            'cta_phone' => [
                ['code' => '20', 'number' => '1112302002'],
            ],
            'cta_whatsapp' => [
                ['code' => '20', 'number' => '1112302002'],
            ],
            'cta_label_en' => 'Contact Us',
            'cta_label_ar' => 'تواصل معنا',
            'support_email' => 'info@touchofclass.com',
            'notification_emails' => 'info@touchofclass.com',
            'footer_copyright_en' => '© '.date('Y').' Touch of Class. All rights reserved.',
            'footer_copyright_ar' => '© '.date('Y').' Touch of Class. جميع الحقوق محفوظة.',
            'footer_body_en' => 'An interior and architecture studio. We design spaces and manage their delivery from the idea to handover.',
            'footer_body_ar' => 'شركة تصميم داخلي ومعماري. نصمم المساحات وندير تنفيذها من الفكرة حتى التسليم.',
            'extra' => $extra,
            'coming_soon_enabled' => false,
            'coming_soon_title_en' => 'Coming Soon',
            'coming_soon_title_ar' => 'قريباً',
            'coming_soon_message_en' => 'We are preparing the Touch of Class website.',
            'coming_soon_message_ar' => 'نجهّز موقع Touch of Class.',
        ]);

        $links = [
            [SocialLinkPlatform::Instagram, 'https://www.instagram.com/touchofclass_interiors/', 1],
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
            ['from_address' => 'info@touchofclass.com'],
            [
                'is_active' => false,
                'mailer' => 'log',
                'host' => null,
                'port' => 587,
                'username' => null,
                'password' => null,
                'encryption' => 'tls',
                'from_name' => 'Touch of Class',
            ]
        );
    }

    /**
     * @return array<string, Category>
     */
    protected function syncCategories(Organization $organization): array
    {
        $tree = [
            'residential' => [
                'parent' => null,
                'name' => ['en' => 'Residential', 'ar' => 'سكني'],
                'short_text' => [
                    'en' => 'Villas and apartments designed for daily comfort and clear details.',
                    'ar' => 'فلل وشقق مصممة براحة وتفاصيل يومية واضحة.',
                ],
                'image' => 'studio/zayed-villa.jpg',
            ],
            'commercial' => [
                'parent' => null,
                'name' => ['en' => 'Commercial', 'ar' => 'تجاري'],
                'short_text' => [
                    'en' => 'Offices and workspaces that reflect the identity of the place.',
                    'ar' => 'مكاتب ومساحات عمل تعكس هوية المكان.',
                ],
                'image' => 'studio/offices.jpg',
            ],
            'renovation' => [
                'parent' => null,
                'name' => ['en' => 'Renovation', 'ar' => 'تجديد'],
                'short_text' => [
                    'en' => 'A renovation that keeps the spirit of the place and updates how it is used.',
                    'ar' => 'تجديد يحافظ على روح المكان ويحدّث استخدامه.',
                ],
                'image' => 'studio/renovation.jpg',
            ],
            'architecture' => [
                'parent' => null,
                'name' => ['en' => 'Architecture', 'ar' => 'معماري'],
                'short_text' => [
                    'en' => 'Architecture and planning that connect the building to its site.',
                    'ar' => 'تصميم معماري وتخطيط يربط المبنى بموقعه.',
                ],
                'image' => 'studio/architecture.jpg',
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
            ['sheikh-zayed-villa', 'TOC-RES-01', 'residential', 'Sheikh Zayed Villa', 'فيلا الشيخ زايد', 'A villa designed with daily comfort and clear details.', 'فيلا مصممة براحة وتفاصيل يومية واضحة.', 'studio/zayed-villa.jpg'],
            ['contemporary-loft', 'TOC-RES-02', 'residential', 'Contemporary Loft', 'لوفت معاصر', 'A contemporary loft with a calm, lived-in finish.', 'لوفت معاصر بتشطيب هادئ ومريح.', 'studio/the-loft.jpg'],
            ['penthouse', 'TOC-RES-03', 'residential', 'Penthouse', 'بنتهاوس', 'A penthouse planned around light, views and quiet rooms.', 'بنتهاوس مُخطط حول الإضاءة والإطلالة والغرف الهادئة.', 'studio/penthouse.jpg'],
            ['capital-office', 'TOC-RES-04', 'residential', 'Capital Office', 'مكتب العاصمة', 'A residential workspace with a clear daily layout.', 'مساحة عمل سكنية بتوزيع يومي واضح.', 'studio/offices.jpg'],
            ['east-residence', 'TOC-RES-05', 'residential', 'East Residence', 'إقامة شرقية', 'An eastern residence with warm materials and private corners.', 'إقامة شرقية بخامات دافئة وزوايا خاصة.', 'studio/east-residence.jpg'],
            ['lake-house', 'TOC-RES-06', 'residential', 'Lake House', 'بيت البحيرة', 'A lake house shaped around rest and open living.', 'بيت بحيرة مُصمم للراحة والمعيشة المفتوحة.', 'studio/lake-house.jpg'],
            ['home-renovation-project', 'TOC-COM-01', 'commercial', 'Home Renovation', 'تجديد منزل', 'A commercial-facing renovation that updates how the space is used.', 'تجديد يحدّث استخدام المساحة ويحافظ على طابعها.', 'studio/renovation.jpg'],
            ['dallas-villa', 'TOC-COM-02', 'commercial', 'Dallas Villa', 'فيلا دالاس', 'A villa interior with a composed, formal character.', 'تصميم فيلا بطابع هادئ ورسمي.', 'studio/dallas.jpg'],
            ['residential-towers', 'TOC-COM-03', 'commercial', 'Residential Towers', 'أبراج سكنية', 'Shared residential towers with a clear public identity.', 'أبراج سكنية بهوية واضحة للمساحات المشتركة.', 'studio/towers.jpg'],
            ['quiet-elegance', 'TOC-COM-04', 'commercial', 'Quiet Elegance', 'أناقة هادئة', 'A quiet commercial interior built on restrained materials.', 'مساحة تجارية هادئة بخامات موزونة.', 'studio/elegance.jpg'],
            ['city-life', 'TOC-COM-05', 'commercial', 'City Life', 'حياة المدينة', 'An urban interior that reflects the rhythm of the city.', 'تصميم حضري يعكس إيقاع المدينة.', 'studio/urban.jpg'],
            ['residential-suite', 'TOC-REN-01', 'renovation', 'Residential Suite', 'جناح سكني', 'A renovated suite that keeps the character of the building.', 'جناح سكني مُجدد يحافظ على طابع المبنى.', 'studio/brooklyn.jpg'],
            ['contemporary-office', 'TOC-REN-02', 'renovation', 'Contemporary Office', 'مكتب معاصر', 'A renovated office with a contemporary plan.', 'مكتب مُجدد بمخطط معاصر.', 'studio/architecture.jpg'],
            ['custom-details', 'TOC-REN-03', 'renovation', 'Custom Details', 'تفاصيل مخصصة', 'A renovation led by custom details and finishes.', 'تجديد قائم على تفاصيل وتشطيبات مخصصة.', 'studio/prestigious.jpg'],
            ['brooklyn-suite', 'TOC-REN-04', 'renovation', 'Brooklyn Suite', 'جناح بروكلين', 'A suite renovation with a calm, layered interior.', 'تجديد جناح بطبقات هادئة ومدروسة.', 'studio/brooklyn.jpg'],
            ['full-fit-out', 'TOC-ARC-01', 'architecture', 'Full Fit-Out', 'تشطيب متكامل', 'A complete fit-out from the plan to the finished space.', 'تشطيب متكامل من المخطط حتى المساحة الجاهزة.', 'studio/photo-1.jpg'],
            ['interior-design-project', 'TOC-ARC-02', 'architecture', 'Interior Design', 'تصميم داخلي', 'An interior planned with the architecture of the building.', 'تصميم داخلي مرتبط بعمارة المبنى.', 'studio/zayed-villa.jpg'],
            ['architectural-planning', 'TOC-ARC-03', 'architecture', 'Architectural Planning', 'تخطيط معماري', 'Planning that connects the building to its site.', 'تخطيط معماري يربط المبنى بموقعه.', 'studio/the-loft.jpg'],
            ['execution-management', 'TOC-ARC-04', 'architecture', 'Execution Management', 'إدارة تنفيذ', 'Project delivery managed from drawing to handover.', 'إدارة تنفيذ من الرسمة حتى التسليم.', 'studio/penthouse.jpg'],
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
            ['interior-design', 'Interior Design', 'التصميم الداخلي', 'Elegant, practical spaces that balance beauty and comfort, designed around how you live.', 'مساحات أنيقة وعملية بتوازن بين الجمال والراحة، ومصممة على أسلوب حياتك.', 'studio/service-interior.jpg'],
            ['architectural-design', 'Architectural Design', 'التصميم المعماري', 'Architectural solutions that combine creativity, function and sustainability, tailored to the site.', 'حلول معمارية تجمع الإبداع والوظيفة والاستدامة، ومفصّلة على رؤيتك وطبيعة الموقع.', 'studio/service-architecture.jpg'],
            ['home-renovation', 'Home Renovation', 'تجديد المنازل', 'We turn existing spaces into contemporary places while respecting the character of the building.', 'نحوّل المساحات القائمة لبيئات حديثة، مع احترام طابع المبنى وإعادة الحياة لكل تفصيلة.', 'studio/service-renovation.jpg'],
            ['project-management', 'Project Management', 'إدارة المشاريع', 'Full project management from start to finish, focused on quality, time and cost.', 'إدارة كاملة للمشروع من البداية للنهاية، بتركيز على الجودة والوقت والتكلفة.', 'studio/service-management.jpg'],
            ['master-planning', 'Master Planning', 'التخطيط العام', 'Planning for larger projects that considers the site and long-term use before the detail of each building.', 'تخطيط للمشروعات الأكبر يراعي الموقع والاستخدام طويل المدى، قبل الدخول في تفاصيل كل مبنى.', 'studio/service-masterplan.jpg'],
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
            ['design-style-guide', 'A guide to choosing the right design style for your home', 'دليل اختيار أسلوب التصميم المناسب لبيتك', 'Lighting, materials and the layout of rooms: the decisions that matter before design starts.', 'إضاءة وخامات وتوزيع فراغات: أهم القرارات قبل أن تبدأ التصميم.', 'studio/photo-1.jpg', '2025-09-15'],
            ['interior-design-types', 'Interior design styles and where to use them', 'أنواع التصميم الداخلي وأين تستخدم', 'From classic to contemporary: how the styles differ and when each one suits the space.', 'من الكلاسيكي إلى الحديث: الفرق بين الأساليب ومتى يناسب كل منها المساحة.', 'studio/photo-2.jpg', '2025-08-10'],
            ['warmer-lighting', 'Tips for warmer, more comfortable lighting', 'نصائح لإضاءة أدفأ وأكثر راحة', 'How to place light so a space feels wider and calmer without extra cost.', 'كيفية توزيع الإضاءة لتبدو المساحة أوسع وأهدأ من دون تكلفة إضافية.', 'studio/photo-3.jpg', '2025-08-28'],
            ['open-office-planning', 'Planning open offices', 'تخطيط المكاتب المفتوحة', 'How to divide a workplace between focus and meetings without crowding the room.', 'كيفية تقسيم مساحة العمل بين التركيز والاجتماعات من دون ازدحام المكان.', 'studio/photo-4.jpg', '2025-09-02'],
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

    }

    protected function syncHomeProjects(): void
    {
        $categories = [
            'residential' => ['Residential', 'سكني', 'fa-solid fa-house', 'Villas and apartments designed for daily comfort and clear details.', 'فلل وشقق مصممة براحة وتفاصيل يومية واضحة.'],
            'commercial' => ['Commercial', 'تجاري', 'fa-solid fa-store', 'Offices and workspaces that reflect the identity of the place.', 'مكاتب ومساحات عمل تعكس هوية المكان.'],
            'renovation' => ['Renovation', 'تجديد', 'fa-solid fa-grip-lines-vertical', 'A renovation that keeps the spirit of the place and updates how it is used.', 'تجديد يحافظ على روح المكان ويحدّث استخدامه.'],
            'architecture' => ['Architecture', 'معماري', 'fa-solid fa-plug-circle-bolt', 'Architecture and planning that connect the building to its site.', 'تصميم معماري وتخطيط يربط المبنى بموقعه.'],
        ];

        $categoryModels = [];
        $order = 1;

        foreach ($categories as $slug => [$nameEn, $nameAr, $icon, $shortEn, $shortAr]) {
            $category = ProjectCategory::query()->where('slug->en', $slug)->first() ?? new ProjectCategory;
            $category->fill([
                'name' => ['en' => $nameEn, 'ar' => $nameAr],
                'short_text' => ['en' => $shortEn, 'ar' => $shortAr],
                'icon' => $icon,
                'order' => $order,
                'is_active' => true,
            ]);
            $category->save();
            $this->lockSlug($category, $slug);
            $categoryModels[$slug] = $category;
            $order++;
        }

        $projects = [
            [
                'slug' => 'mivida',
                'category' => 'residential',
                'title' => ['en' => 'Mivida', 'ar' => 'ميفيدا'],
                'location' => ['en' => 'Mivida', 'ar' => 'ميفيدا'],
                'short' => [
                    'en' => 'A sophisticated approach to interior design shapes every corner of the Mivida project with elegance and warmth.',
                    'ar' => 'تصميم داخلي متقن يرسم كل ركن في مشروع ميفيدا بأناقة ودفء.',
                ],
                'text' => [
                    'en' => '<p>A sophisticated approach to interior design shapes every corner of the Mivida project with elegance and warmth. The design celebrates a balance between aesthetics and practicality, offering a space that feels luxurious yet deeply personal.</p><p>The space embraces different shades of grey to create a calm, contemporary atmosphere. Clean lines and balanced textures highlight the beauty of simplicity.</p>',
                    'ar' => '<p>تصميم داخلي متقن يرسم كل ركن في مشروع ميفيدا بأناقة ودفء. التصميم يوازن بين الجمال والعملية، ويقدم مساحة فخمة وقريبة من ساكنها.</p><p>المساحة تعتمد درجات الرمادي لجو هادئ ومعاصر. الخطوط النظيفة والخامات المتوازنة تبرز جمال البساطة.</p>',
                ],
            ],
            [
                'slug' => 'hayaty-residence',
                'category' => 'residential',
                'title' => ['en' => 'Hayaty Residence', 'ar' => 'حياة ريزيدنس'],
                'location' => ['en' => 'Hayaty Residence', 'ar' => 'حياة ريزيدنس'],
                'short' => [
                    'en' => 'This residence is a testament to livable elegance, where every design decision reflects a careful blend of style and functionality.',
                    'ar' => 'السكن ده مثال على أناقة تعيشها كل يوم، وكل قرار تصميمي فيه مزيج مدروس من الأسلوب والوظيفة.',
                ],
                'text' => [
                    'en' => '<p>This residence is a testament to livable elegance, where every design decision reflects a careful blend of style and functionality.</p><p>By prioritizing comfort and aesthetics, the interiors create an inviting atmosphere that elevates everyday living to an art form.</p>',
                    'ar' => '<p>السكن ده مثال على أناقة تعيشها كل يوم، وكل قرار تصميمي فيه مزيج مدروس من الأسلوب والوظيفة.</p><p>الاهتمام بالراحة والجمال يخلق جو يدعو للسكن ويرفع تفاصيل الحياة اليومية.</p>',
                ],
            ],
            [
                'slug' => 'lotus-roof',
                'category' => 'residential',
                'title' => ['en' => 'Lotus Roof', 'ar' => 'لوتس روف'],
                'location' => ['en' => 'Lotus Roof', 'ar' => 'لوتس روف'],
                'short' => ['en' => 'Lotus Roof project.', 'ar' => 'مشروع لوتس روف.'],
                'text' => ['en' => '<p>Lotus Roof project.</p>', 'ar' => '<p>مشروع لوتس روف.</p>'],
            ],
            [
                'slug' => 'tala-compound',
                'category' => 'residential',
                'title' => ['en' => 'Tala Compound', 'ar' => 'كمبوند تالا'],
                'location' => ['en' => 'Tala Compound', 'ar' => 'كمبوند تالا'],
                'short' => [
                    'en' => 'Elegance in its purest form, where a serene palette of nude tones meets refined simplicity.',
                    'ar' => 'أناقة في أنقى صورة، بدرجات هادية وبساطة مدروسة.',
                ],
                'text' => [
                    'en' => '<p>Elegance in its purest form, where a serene palette of nude tones meets refined simplicity. Every detail is thoughtfully curated, proving that true luxury lies in subtlety and sophistication.</p>',
                    'ar' => '<p>أناقة في أنقى صورة، بدرجات هادية وبساطة مدروسة. كل تفصيلة مختارة بعناية، والفخامة هنا في الهدوء والرقي.</p>',
                ],
            ],
            [
                'slug' => 'mountain-view',
                'category' => 'residential',
                'title' => ['en' => 'Mountain View', 'ar' => 'ماونتن فيو'],
                'location' => ['en' => 'Mountain View', 'ar' => 'ماونتن فيو'],
                'short' => ['en' => 'Warmth meets serenity.', 'ar' => 'دفء يلتقي بالهدوء.'],
                'text' => ['en' => '<p>Warmth meets serenity.</p>', 'ar' => '<p>دفء يلتقي بالهدوء.</p>'],
            ],
            [
                'slug' => 'sodic',
                'category' => 'residential',
                'title' => ['en' => 'Sodic', 'ar' => 'سوديك'],
                'location' => ['en' => 'Sodic', 'ar' => 'سوديك'],
                'short' => ['en' => 'Sodic project.', 'ar' => 'مشروع سوديك.'],
                'text' => ['en' => '<p>Sodic project.</p>', 'ar' => '<p>مشروع سوديك.</p>'],
            ],
            [
                'slug' => 'ashgar-october',
                'category' => 'residential',
                'title' => ['en' => 'Ashgar Compound', 'ar' => 'كمبوند أشجار'],
                'location' => ['en' => 'October', 'ar' => 'أكتوبر'],
                'short' => ['en' => 'Ashgar Compound, October.', 'ar' => 'كمبوند أشجار، أكتوبر.'],
                'text' => ['en' => '<p>Ashgar Compound, October.</p>', 'ar' => '<p>كمبوند أشجار، أكتوبر.</p>'],
            ],
            [
                'slug' => 'mountain-view-sahel',
                'category' => 'residential',
                'title' => ['en' => 'Mountain View Sahel', 'ar' => 'ماونتن فيو الساحل'],
                'location' => ['en' => 'Sahel', 'ar' => 'الساحل'],
                'short' => ['en' => 'Mountain View, Sahel.', 'ar' => 'ماونتن فيو، الساحل.'],
                'text' => ['en' => '<p>Mountain View, Sahel.</p>', 'ar' => '<p>ماونتن فيو، الساحل.</p>'],
            ],
            [
                'slug' => 'qatar-villas',
                'category' => 'residential',
                'title' => ['en' => 'Qatar Villas', 'ar' => 'فلل قطر'],
                'location' => ['en' => 'Qatar', 'ar' => 'قطر'],
                'short' => [
                    'en' => 'Timeless luxury across multiple projects in Qatar.',
                    'ar' => 'فخامة خالدة في مشاريع متعددة في قطر.',
                ],
                'text' => [
                    'en' => '<p>Timeless luxury across multiple projects in Qatar.</p>',
                    'ar' => '<p>فخامة خالدة في مشاريع متعددة في قطر.</p>',
                ],
            ],
            [
                'slug' => 'restaurant',
                'category' => 'commercial',
                'title' => ['en' => 'Restaurant', 'ar' => 'مطعم'],
                'location' => ['en' => 'Restaurant', 'ar' => 'مطعم'],
                'short' => ['en' => 'Commercial project / Restaurant.', 'ar' => 'مشروع تجاري / مطعم.'],
                'text' => ['en' => '<p>Commercial project / Restaurant.</p>', 'ar' => '<p>مشروع تجاري / مطعم.</p>'],
            ],
        ];

        $keptIds = [];

        foreach ($projects as $index => $item) {
            $project = Project::query()->where('slug->en', $item['slug'])->first() ?? new Project;
            $project->fill([
                'project_category_id' => $categoryModels[$item['category']]->id,
                'title' => $item['title'],
                'location' => $item['location'],
                'short_text' => $item['short'],
                'text' => $item['text'],
                'is_active' => true,
                'show_in_home' => true,
                'order' => $index + 1,
            ]);
            $project->save();
            $this->lockSlug($project, $item['slug']);

            $images = $this->portfolioImages($item['slug']);
            if ($images !== []) {
                $this->attachDesignMedia($project, 'cover', $images[0]);
                $this->attachDesignGallery($project, 'gallery', array_slice($images, 1));
            }

            $keptIds[] = $project->id;
        }

        Project::query()->whereNotIn('id', $keptIds)->update(['is_active' => false, 'show_in_home' => false]);
        ProjectCategory::query()->whereNotIn('id', collect($categoryModels)->pluck('id'))->update(['is_active' => false]);
    }

    protected function syncHomePartners(Organization $organization): void
    {
        $names = ['Interior', 'Architecture', 'Planning', 'Renovation', 'Fit-Out', 'Styling', 'Turnkey', 'Management', 'Lighting', 'Furniture'];

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
                'sub_title' => ['en' => 'Touch of Class', 'ar' => 'Touch of Class'],
                'title' => ['en' => "Interior design\nwith a touch of luxury", 'ar' => "تصميم داخلي\nبلمسة فخامة"],
                'text' => ['en' => 'An interior studio that designs residential and commercial spaces where beauty and function meet, from the idea to delivery.', 'ar' => 'شركة تصميم داخلي تصمم مساحات سكنية وتجارية تجمع بين الجمال والوظيفة، من الفكرة حتى التنفيذ.'],
                'button_text' => ['en' => 'View our work', 'ar' => 'اطّلع على أعمالنا'],
                'button_link_url' => '/projects',
                'image' => 'studio/hero-1.jpg',
            ]],
            [PageEnum::HOME, SectionTypeEnum::HERO, 2, [
                'sub_title' => ['en' => 'Touch of Class', 'ar' => 'Touch of Class'],
                'title' => ['en' => "A renovation that gives a place\nits life and its details back", 'ar' => "تجديد يعيد للمكان\nحياته وتفاصيله"],
                'text' => ['en' => 'We turn existing spaces into contemporary places that keep the character of the building and add comfort and elegance in every detail.', 'ar' => 'نحوّل المساحات القائمة لمكان معاصر يحافظ على طابع المبنى ويضيف راحة وأناقة في كل تفصيلة.'],
                'button_text' => ['en' => 'Renovation services', 'ar' => 'خدمات التجديد'],
                'button_link_url' => '/projects',
                'image' => 'studio/hero-2.jpg',
            ]],
            [PageEnum::HOME, SectionTypeEnum::HERO, 3, [
                'sub_title' => ['en' => 'Touch of Class', 'ar' => 'Touch of Class'],
                'title' => ['en' => "From the plan to handover\nfull project management", 'ar' => "من المخطط حتى التسليم\nإدارة مشروع كاملة"],
                'text' => ['en' => 'We follow design, construction, schedule and cost until the project is finished at the required quality and on time.', 'ar' => 'نتابع التصميم والتنفيذ والجدول الزمني والتكلفة، حتى يُنجز المشروع بالجودة المطلوبة وفي موعده.'],
                'button_text' => ['en' => 'Explore our services', 'ar' => 'تعرّف على خدماتنا'],
                'button_link_url' => '/services',
                'image' => 'studio/hero-3.jpg',
            ]],
            [PageEnum::HOME, SectionTypeEnum::ABOUTUS, 1, [
                'sub_title' => ['en' => 'About the company', 'ar' => 'عن الشركة'],
                'title' => ['en' => "Passion for design,\nprecision in execution", 'ar' => "شغف بالتصميم،\nدقة في التنفيذ"],
                'text' => ['en' => 'At Touch of Class, interior design and architecture, we create spaces that feel effortless yet refined. We listen, we interpret, and we bring to life designs that echo our client\'s personal style, with comfort and function at every turn.', 'ar' => 'في Touch of Class للتصميم الداخلي والعمارة، بنصمم مساحات تبان بسيطة وراقية. بنسمع، وبنترجم، وبنحوّل التصميم لمساحة تعكس أسلوب العميل، براحة ووظيفة في كل تفصيلة.'],
                'button_text' => ['en' => 'Learn more about us', 'ar' => 'اعرف المزيد عنا'],
                'button_link_url' => '/about-us',
                'image' => 'portfolio/about/passion.jpg',
            ]],
            [PageEnum::ABOUT, SectionTypeEnum::ABOUT_BREADCRUMB, 1, [
                'title' => ['en' => 'About us', 'ar' => 'من نحن'],
            ]],
            [PageEnum::ABOUT, SectionTypeEnum::ABOUTUS, 1, [
                'sub_title' => ['en' => 'About the company', 'ar' => 'عن الشركة'],
                'title' => ['en' => "Passion for design,\nprecision in execution", 'ar' => "شغف بالتصميم،\nدقة في التنفيذ"],
                'text' => ['en' => '<p>At Touch of Class, interior design and architecture, we\'re dedicated to creating spaces that feel effortless yet refined. Our approach is simple: we listen, we interpret, and we bring to life designs that echo our client\'s personal style while ensuring comfort and function at every turn.</p><p>Our team brings a diverse set of talents and an eye for detail to every project. We believe in creating spaces that are more than just beautiful. They are spaces that make you feel at ease. Led by our founders, Eng. Sarah Samir and Eng. Mohamed Khallaf, we blend experience with a deep love for design to craft homes and interiors that are as inviting as they are elegant.</p>', 'ar' => '<p>في Touch of Class للتصميم الداخلي والعمارة، بنصمّم مساحات تبان بسيطة وراقية. طريقتنا واضحة: بنسمع، وبنترجم، وبنحوّل التصميم لمساحة تعكس أسلوب العميل، مع راحة ووظيفة في كل تفصيلة.</p><p>الفريق يجمع خبرات مختلفة وعين على التفاصيل. المساحة عندنا مش بس شكل حلو، دي مساحة تحس فيها بالراحة. بقيادة المؤسسين، المهندسة سارة سمير والمهندس محمد خلاف، بنمزج الخبرة بحب التصميم عشان البيوت والتصميمات الداخلية تبقى دافية وأنيقة.</p>'],
                'image' => 'portfolio/about/passion.jpg',
            ]],
            [PageEnum::ABOUT, SectionTypeEnum::VISION, 1, [
                'sub_title' => ['en' => 'Vision', 'ar' => 'الرؤية'],
                'title' => ['en' => "Trusted\npartner", 'ar' => "شريك\nموثوق"],
                'text' => ['en' => '<p>To be the trusted partner by crafting distinctive interiors that reflect each client\'s personality and lifestyle — combining creative design, practical function, and timeless appeal.</p>', 'ar' => '<p>نبقى الشريك الموثوق في تصميم داخلي مميز يعكس شخصية كل عميل وأسلوب حياته، ويجمع بين التصميم الإبداعي والوظيفة العملية والشكل اللي يعيش.</p>'],
                'image' => 'portfolio/qatar-villas/01.jpg',
            ]],
            [PageEnum::ABOUT, SectionTypeEnum::MISSION, 1, [
                'sub_title' => ['en' => 'Mission', 'ar' => 'الرسالة'],
                'title' => ['en' => "Welcoming\nspaces", 'ar' => "مساحات\nترحب بيك"],
                'text' => ['en' => '<p>Our mission is to provide personalized interior design solutions that blend timeless elegance with each client\'s unique taste, ensuring every space feels both welcoming and uniquely theirs.</p>', 'ar' => '<p>رسالتنا نقدم حلول تصميم داخلي خاصة بكل عميل، تمزج الأناقة الخالدة بذوقه، عشان كل مساحة تبقى مرحّبة وبتخصّه هو.</p>'],
                'image' => 'portfolio/hayaty-residence/01.jpg',
            ]],
            [PageEnum::HOME, SectionTypeEnum::SERVICES, 1, [
                'sub_title' => ['en' => 'Our services', 'ar' => 'خدماتنا'],
                'title' => ['en' => 'Our', 'ar' => 'خدماتنا'],
                'second_title' => ['en' => 'main services', 'ar' => 'الرئيسية'],
                'text' => ['en' => 'Five services covering interior design, architecture, planning, renovation and project management.', 'ar' => 'خمس خدمات متكاملة تغطي التصميم الداخلي، المعماري، التخطيط، التجديد، وإدارة المشروع.'],
                'button_text' => ['en' => 'View all services', 'ar' => 'عرض جميع الخدمات'],
                'button_link_url' => '/services',
            ]],
            [PageEnum::HOME, SectionTypeEnum::FEATURED_PRODUCTS, 1, [
                'sub_title' => ['en' => 'Our work', 'ar' => 'أعمالنا'],
                'title' => ['en' => "Selected\nprojects", 'ar' => "مشاريع\nمختارة"],
                'text' => ['en' => 'Residential and commercial spaces we designed with care, from villas and offices to a full renovation.', 'ar' => 'مجموعة من المساحات السكنية والتجارية التي صممناها بعناية، من الفلل والمكاتب حتى التجديد الكامل.'],
                'button_text' => ['en' => 'View all projects', 'ar' => 'عرض كل المشاريع'],
                'button_link_url' => '/projects',
                'image' => 'studio/urban.jpg',
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS, 1, [
                'sub_title' => ['en' => 'Start a project', 'ar' => 'ابدأ مشروعك'],
                'title' => ['en' => "Book\na consultation\nfor your space", 'ar' => "احجز\nاستشارة\nلمساحتك"],
                'text' => ['en' => 'Tell us about the space and we will help you shape the design, from the first idea to delivery.', 'ar' => 'احكيلنا عن المساحة، ونساعدك تحدد التصميم المناسب من الفكرة حتى التنفيذ.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 1, [
                'title' => ['en' => 'Understand the space', 'ar' => 'فهم المساحة'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 2, [
                'title' => ['en' => 'Draw the design', 'ar' => 'رسم التصميم'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 3, [
                'title' => ['en' => 'Choose the materials', 'ar' => 'اختيار الخامات'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::PROCESS_STEP, 4, [
                'title' => ['en' => 'Follow the build', 'ar' => 'متابعة التنفيذ'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::CLIENTS, 1, [
                'sub_title' => ['en' => 'Success partners', 'ar' => 'شركاء النجاح'],
                'title' => ['en' => "Integrated services\nunder one roof", 'ar' => "خدمات\nمتكاملة\nتحت سقف واحد"],
            ]],
            [PageEnum::HOME, SectionTypeEnum::WHY_CHOOSE_US, 1, [
                'sub_title' => ['en' => 'Values', 'ar' => 'قيمنا'],
                'title' => ['en' => "What guides\nevery project", 'ar' => "اللي بيوجّه\nكل مشروع"],
                'text' => ['en' => 'We listen, we interpret, and we bring to life designs that echo our client\'s personal style while ensuring comfort and function at every turn.', 'ar' => 'بنسمع، وبنترجم، وبننفذ تصميم يعكس أسلوب العميل، مع راحة ووظيفة في كل تفصيلة.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 1, [
                'title' => ['en' => 'Elegance & Quality', 'ar' => 'الأناقة والجودة'],
                'text' => ['en' => 'We prioritize timeless elegance and high-quality craftsmanship in every detail.', 'ar' => 'نضع الأناقة الخالدة والحرفية العالية في كل تفصيلة.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 2, [
                'title' => ['en' => 'Personalization', 'ar' => 'التخصيص'],
                'text' => ['en' => 'We believe each space should reflect the individuality of its owner, so we customize designs to tell each client\'s unique story.', 'ar' => 'كل مساحة لازم تعكس شخصية صاحبها، عشان كده بنخصص التصميم ليحكي قصته.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 3, [
                'title' => ['en' => 'Innovation & Creativity', 'ar' => 'الابتكار والإبداع'],
                'text' => ['en' => 'We embrace innovative ideas and creative solutions to bring fresh perspectives to every project.', 'ar' => 'نتبنى الأفكار الجديدة والحلول المبتكرة عشان نضيف منظور جديد لكل مشروع.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BENEFITS, 4, [
                'title' => ['en' => 'Integrity & Trust', 'ar' => 'النزاهة والثقة'],
                'text' => ['en' => 'We are committed to honesty and transparency, building lasting relationships based on trust.', 'ar' => 'ملتزمون بالصدق والشفافية، وبنبني علاقات تدوم على الثقة.'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 1, [
                'title' => ['en' => '15+', 'ar' => '15+'],
                'text' => ['en' => 'Years in the market', 'ar' => 'سنة خبرة في السوق'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 2, [
                'title' => ['en' => '2500+', 'ar' => '2500+'],
                'text' => ['en' => 'Clients who trust us', 'ar' => 'عميل يثق بنا'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 3, [
                'title' => ['en' => '600+', 'ar' => '600+'],
                'text' => ['en' => 'Projects delivered', 'ar' => 'مشروع تم تنفيذه'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::STATISTICS, 4, [
                'title' => ['en' => '1200+', 'ar' => '1200+'],
                'text' => ['en' => 'Completed projects', 'ar' => 'مشروع منجز'],
            ]],
            [PageEnum::HOME, SectionTypeEnum::BLOGS, 1, [
                'sub_title' => ['en' => 'Our articles', 'ar' => 'مقالاتنا'],
                'title' => ['en' => "Latest\narticles", 'ar' => "أحدث\nالمقالات"],
                'text' => ['en' => 'Ideas and advice on interior design, renovation, materials and lighting.', 'ar' => 'أفكار ونصائح في التصميم الداخلي والتجديد واختيار الخامات والإضاءة.'],
                'button_text' => ['en' => 'View all articles', 'ar' => 'عرض جميع المقالات'],
                'button_link_url' => '/news',
            ]],
            [PageEnum::HOME, SectionTypeEnum::CONTACT_US_CONTENT, 1, [
                'sub_title' => ['en' => 'Call us', 'ar' => 'اتصل بنا'],
                'title' => ['en' => "Contact\nus", 'ar' => "تواصل\nمعـنا"],
                'text' => ['en' => 'Our team is ready to help you start the right project and offer a solution that fits it.', 'ar' => 'فريقنا جاهز لمساعدتك في ابدأ مشروعك الأنسب وتقديم الحلول المناسبة لمتطلبات مشروعك.'],
                'image' => 'studio/contact-side.jpg',
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::CONTACT_US_CONTENT, 1, [
                'title' => ['en' => "Let's talk about your project", 'ar' => 'لنتحدث عن مشروعك'],
                'text' => ['en' => 'Please fill in the details and we will contact you as soon as we can.', 'ar' => 'يرجى تعبئة البيانات، وسنتواصل معك في أقرب وقت.'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::BENEFITS, 1, [
                'title' => ['en' => 'Saturday to Thursday', 'ar' => 'السبت – الخميس'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::BENEFITS, 2, [
                'title' => ['en' => '9:00 AM – 6:00 PM', 'ar' => '9:00 ص – 6:00 م'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::BENEFITS, 3, [
                'title' => ['en' => 'Friday off', 'ar' => 'الجمعة إجازة'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::CONTENT, 1, [
                'sub_title' => ['en' => 'Contact channels', 'ar' => 'طرق التواصل'],
                'title' => ['en' => 'Choose what suits you', 'ar' => 'اختر الأنسب لك'],
                'text' => ['en' => 'We are available on every channel. Choose the one that suits you and we will reply as soon as we can.', 'ar' => 'نحن متاحون عبر جميع القنوات. اختر الوسيلة المناسبة لك، وسنرد عليك في أسرع وقت.'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::FEATURES, 1, [
                'title' => ['en' => 'WhatsApp', 'ar' => 'واتساب'],
                'text' => ['en' => 'The fastest way to reply and send photos of the space or the plan.', 'ar' => 'الأسرع للرد وإرسال صور المساحة أو المخطط.'],
                'button_text' => ['en' => 'Start a chat', 'ar' => 'ابدأ المحادثة'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::FEATURES, 2, [
                'title' => ['en' => 'Direct call', 'ar' => 'اتصال مباشر'],
                'text' => ['en' => 'Speak with a sales consultant if your request is urgent.', 'ar' => 'تواصل مع مستشار المبيعات إذا كان طلبك عاجلاً.'],
                'button_text' => ['en' => 'Call now', 'ar' => 'اتصل الآن'],
            ]],
            [PageEnum::CONTACT, SectionTypeEnum::FEATURES, 3, [
                'title' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
                'text' => ['en' => 'For official quotations and tenders.', 'ar' => 'لعروض الأسعار الرسمية والمناقصات.'],
                'button_text' => ['en' => 'Send an email', 'ar' => 'أرسل بريداً'],
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
                'name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
                'address' => ['en' => 'Cairo, Arab Republic of Egypt', 'ar' => 'القاهرة، جمهورية مصر العربية'],
                'phone_1_country_code' => '20',
                'phone_1' => '1112302002',
                'phone_2_country_code' => null,
                'phone_2' => null,
                'email' => 'info@touchofclass.com',
                'working_hours' => ['en' => 'Saturday to Thursday, 9:00 AM – 6:00 PM', 'ar' => 'من السبت إلى الخميس، 9:00 صباحًا – 6:00 مساءً'],
                'map_url' => 'https://www.google.com/maps?q=Cairo,Egypt',
                'map_embed' => 'https://www.google.com/maps?q=Cairo,Egypt&z=13&output=embed',
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
                'question' => ['en' => 'How do we start a project?', 'ar' => 'إزاي نبدأ المشروع؟'],
                'answer' => ['en' => 'Tell us about the space and how you use it. We then prepare the layout, materials and a clear schedule.', 'ar' => 'احكيلنا عن المساحة وطريقة استخدامك لها، وبعدها نجهز التوزيع والخامات والجدول.'],
            ],
            [
                'question' => ['en' => 'Do you renovate existing homes?', 'ar' => 'بتعملوا تجديد للمنازل القائمة؟'],
                'answer' => ['en' => 'Yes. We renovate existing spaces while keeping the character of the building.', 'ar' => 'أيوة، بنجدد المساحات القائمة مع الحفاظ على طابع المبنى.'],
            ],
            [
                'question' => ['en' => 'What are your working hours?', 'ar' => 'مواعيد العمل إيه؟'],
                'answer' => ['en' => 'Saturday to Thursday, 9:00 AM to 6:00 PM. Friday is off.', 'ar' => 'من السبت إلى الخميس، من 9:00 صباحًا حتى 6:00 مساءً. الجمعة إجازة.'],
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
                'project_type' => 'interior',
                'message' => 'محتاج استشارة لتصميم شقة سكنية.',
                'is_read' => false,
            ]
        );

        NewsletterSubscriber::query()
            ->where('email', 'guest@minapack.example')
            ->update(['email' => 'guest@touchofclass.example']);

        NewsletterSubscriber::query()
            ->where('email', 'guest@kingmotors.example')
            ->update(['email' => 'guest@touchofclass.example']);

        NewsletterSubscriber::query()->firstOrCreate(
            [
                'organization_id' => $organization->id,
                'email' => 'guest@touchofclass.example',
            ]
        );
    }

    protected function syncFounders(): void
    {
        $founders = [
            [1, 'portfolio/team/portrait-1.jpg', 'Eng. Sarah Samir', 'م. سارة سمير', 'Founder', 'مؤسسة'],
            [2, 'portfolio/team/portrait-2.jpg', 'Eng. Mohamed Khallaf', 'م. محمد خلاف', 'Founder', 'مؤسس'],
        ];

        $keptIds = [];

        foreach ($founders as [$order, $image, $nameEn, $nameAr, $titleEn, $titleAr]) {
            $member = Team::query()->updateOrCreate(
                ['order' => $order],
                [
                    'name' => ['en' => $nameEn, 'ar' => $nameAr],
                    'title' => ['en' => $titleEn, 'ar' => $titleAr],
                    'is_active' => true,
                ]
            );
            $this->attachDesignMedia($member, 'teams', $image);
            $keptIds[] = $member->id;
        }

        Team::query()->whereNotIn('id', $keptIds)->update(['is_active' => false]);
    }

    /**
     * @return list<string>
     */
    protected function portfolioImages(string $slug): array
    {
        $directory = public_path('assets/images/portfolio/'.$slug);

        if (! is_dir($directory)) {
            return [];
        }

        return collect(File::files($directory))
            ->sortBy(fn (SplFileInfo $file): string => $file->getFilename())
            ->map(fn (SplFileInfo $file): string => 'portfolio/'.$slug.'/'.$file->getFilename())
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $relativePaths
     */
    protected function attachDesignGallery(object $model, string $collection, array $relativePaths): void
    {
        if (! method_exists($model, 'clearMediaCollection') || ! method_exists($model, 'addMedia')) {
            return;
        }

        $model->clearMediaCollection($collection);

        foreach ($relativePaths as $relativePath) {
            $absolutePath = public_path('assets/images/'.$relativePath);

            if (! is_file($absolutePath)) {
                continue;
            }

            $model->addMedia($absolutePath)
                ->preservingOriginal()
                ->toMediaCollection($collection);
        }
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
        $assignments = [];

        if ($assignments === [] || ! is_dir(resource_path('design/data'))) {
            return;
        }

        $this->publishCatalogDirectory();

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
