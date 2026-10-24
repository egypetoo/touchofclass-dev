<?php

namespace Database\Seeders;

use App\Models\SeoAssistant;
use Illuminate\Database\Seeder;

class SeoAssistantSeeder extends Seeder
{
    public function run(): void
    {
        SeoAssistant::query()->updateOrCreate(
            ['id' => 1],
            [
                'home_meta_title_en' => 'Touch of Class - Interior Design',
                'home_meta_desc_en' => 'Touch of Class — an interior and architecture studio for villas, apartments, offices and renovation projects.',
                'home_meta_title_ar' => 'Touch of Class - تصميم داخلي',
                'home_meta_desc_ar' => 'Touch of Class - شركة تصميم داخلي ومعماري للفلل والشقق والمكاتب ومشروعات التجديد.',

                'about_meta_title_en' => 'About Us | Touch of Class',
                'about_meta_desc_en' => 'Learn about Touch of Class, an interior and architecture studio for apartments, villas, offices and renovation projects.',
                'about_meta_title_ar' => 'من نحن | Touch of Class',
                'about_meta_desc_ar' => 'تعرف على Touch of Class، شركة تصميم داخلي ومعماري للشقق والفلل والمكاتب ومشروعات التجديد.',

                'contact_meta_title_en' => 'Contact Us | Touch of Class',
                'contact_meta_desc_en' => 'Contact Touch of Class to book an interior design consultation or start renovating your space.',
                'contact_meta_title_ar' => 'تواصل معنا | Touch of Class',
                'contact_meta_desc_ar' => 'تواصل مع Touch of Class لحجز استشارة تصميم داخلي أو البدء في تجديد مساحتك.',

                'blog_meta_title_en' => 'News & Articles | Touch of Class',
                'blog_meta_desc_en' => 'News and articles from Touch of Class on interior design, renovation and lighting.',
                'blog_meta_title_ar' => 'أخبار ومقالات | Touch of Class',
                'blog_meta_desc_ar' => 'أخبار ومقالات Touch of Class عن التصميم الداخلي والتجديد والإضاءة.',

                'service_meta_title_en' => 'Our Services | Touch of Class',
                'service_meta_desc_en' => 'Touch of Class services: interior design, architectural design, master planning, home renovation and project management.',
                'service_meta_title_ar' => 'خدماتنا | Touch of Class',
                'service_meta_desc_ar' => 'خدمات Touch of Class: تصميم داخلي، تصميم معماري، تخطيط عام، تجديد منازل، وإدارة مشاريع.',

                'products_meta_title_en' => 'Our Work | Touch of Class',
                'products_meta_desc_en' => 'Browse Touch of Class spaces: villas, apartments, offices and renovations.',
                'products_meta_title_ar' => 'أعمالنا | Touch of Class',
                'products_meta_desc_ar' => 'تصفح أعمال Touch of Class: فلل وشقق ومكاتب وتجديدات.',

                'projects_meta_title_en' => 'Our Projects | Touch of Class',
                'projects_meta_desc_en' => 'Touch of Class projects: villas, apartments, offices and renovations with interior and architectural design.',
                'projects_meta_title_ar' => 'مشاريعنا | Touch of Class',
                'projects_meta_desc_ar' => 'مشاريع Touch of Class: فلل، شقق، مكاتب، وتجديدات بتصميم داخلي ومعماري.',

                'gallery_meta_title_en' => 'Gallery | Touch of Class',
                'gallery_meta_desc_en' => 'A look at interior and architectural spaces by Touch of Class.',
                'gallery_meta_title_ar' => 'المعرض | Touch of Class',
                'gallery_meta_desc_ar' => 'نظرة على المساحات الداخلية والمعمارية من Touch of Class.',

                'branches_meta_title_en' => 'Our Location | Touch of Class',
                'branches_meta_desc_en' => 'Touch of Class in Cairo, Egypt — address, map and working hours.',
                'branches_meta_title_ar' => 'موقعنا | Touch of Class',
                'branches_meta_desc_ar' => 'Touch of Class في القاهرة، جمهورية مصر العربية — العنوان والخريطة ومواعيد العمل.',
            ]
        );
    }
}
