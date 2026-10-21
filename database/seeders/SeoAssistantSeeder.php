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
                'home_meta_title_en' => 'Home | King Motors',
                'home_meta_desc_en' => 'King Motors — motors, pumps, pipes and cables for integrated water pumping solutions.',
                'home_meta_title_ar' => 'الرئيسية | كينج موتورز',
                'home_meta_desc_ar' => 'كينج موتورز — مواتير ومضخات ومواسير وكابلات لحلول ضخ المياه المتكاملة.',

                'about_meta_title_en' => 'About Us | King Motors',
                'about_meta_desc_en' => 'Learn about King Motors and our water pumping equipment for agriculture, industry and projects.',
                'about_meta_title_ar' => 'من نحن | كينج موتورز',
                'about_meta_desc_ar' => 'تعرف على كينج موتورز ومعدات ضخ المياه للزراعة والصناعة والمشروعات.',

                'contact_meta_title_en' => 'Contact Us | King Motors',
                'contact_meta_desc_en' => 'Contact King Motors for quotations, pump selection and after-sales support in Egypt.',
                'contact_meta_title_ar' => 'تواصل معنا | كينج موتورز',
                'contact_meta_desc_ar' => 'تواصل مع كينج موتورز لطلب عرض سعر أو اختيار طلمبة أو الدعم الفني.',

                'blog_meta_title_en' => 'News | King Motors',
                'blog_meta_desc_en' => 'Technical articles about pumps, motors and water pumping systems from King Motors.',
                'blog_meta_title_ar' => 'أخبار | كينج موتورز',
                'blog_meta_desc_ar' => 'مقالات تقنية عن الطلمبات والمواتير وأنظمة ضخ المياه من كينج موتورز.',

                'service_meta_title_en' => 'Services | King Motors',
                'service_meta_desc_en' => 'Consulting, spare parts, warranty maintenance, and supply and installation from King Motors.',
                'service_meta_title_ar' => 'خدماتنا | كينج موتورز',
                'service_meta_desc_ar' => 'استشارات وقطع غيار وصيانة بالضمان وتوريد وتركيب من كينج موتورز.',

                'products_meta_title_en' => 'Products | King Motors',
                'products_meta_desc_en' => 'Browse King Motors products: electric motors, pumps, pipes and cables.',
                'products_meta_title_ar' => 'المنتجات | كينج موتورز',
                'products_meta_desc_ar' => 'تصفح منتجات كينج موتورز: المواتير والمضخات والمواسير والكابلات.',

                'projects_meta_title_en' => 'Projects | King Motors',
                'projects_meta_desc_en' => 'Water pumping projects supplied by King Motors.',
                'projects_meta_title_ar' => 'المشروعات | كينج موتورز',
                'projects_meta_desc_ar' => 'مشروعات ضخ المياه التي توردها كينج موتورز.',

                'gallery_meta_title_en' => 'Gallery | King Motors',
                'gallery_meta_desc_en' => 'A look at King Motors equipment and pumping solutions.',
                'gallery_meta_title_ar' => 'المعرض | كينج موتورز',
                'gallery_meta_desc_ar' => 'نظرة على معدات كينج موتورز وحلول الضخ.',

                'branches_meta_title_en' => 'Our Location | King Motors',
                'branches_meta_desc_en' => 'King Motors in 6th of October, Giza — address, map and working hours.',
                'branches_meta_title_ar' => 'موقعنا | كينج موتورز',
                'branches_meta_desc_ar' => 'كينج موتورز في 6 أكتوبر، الجيزة — العنوان والخريطة ومواعيد العمل.',
            ]
        );
    }
}
