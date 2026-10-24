<?php

namespace Database\Seeders;

use App\Enums\MenuRouteNameEnum;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'key' => 'home',
                'title' => ['en' => 'Home', 'ar' => 'الرئيسية'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::HOME,
                'order' => 1,
            ],
            [
                'key' => 'about',
                'title' => ['en' => 'About Us', 'ar' => 'من نحن'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::ABOUT_US,
                'order' => 2,
            ],
            [
                'key' => 'services',
                'title' => ['en' => 'Services', 'ar' => 'الخدمات'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::SERVICES,
                'order' => 3,
            ],
            [
                'key' => 'projects',
                'title' => ['en' => 'Projects', 'ar' => 'المشاريع'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::PROJECTS,
                'order' => 4,
            ],
            [
                'key' => 'residential',
                'title' => ['en' => 'Residential', 'ar' => 'سكني'],
                'parent_key' => 'projects',
                'route_name' => MenuRouteNameEnum::PROJECTS,
                'order' => 1,
            ],
            [
                'key' => 'commercial',
                'title' => ['en' => 'Commercial', 'ar' => 'تجاري'],
                'parent_key' => 'projects',
                'route_name' => MenuRouteNameEnum::PROJECTS,
                'order' => 2,
            ],
            [
                'key' => 'renovation',
                'title' => ['en' => 'Renovation', 'ar' => 'تجديد'],
                'parent_key' => 'projects',
                'route_name' => MenuRouteNameEnum::PROJECTS,
                'order' => 3,
            ],
            [
                'key' => 'architecture',
                'title' => ['en' => 'Architecture', 'ar' => 'معماري'],
                'parent_key' => 'projects',
                'route_name' => MenuRouteNameEnum::PROJECTS,
                'order' => 4,
            ],
            [
                'key' => 'news',
                'title' => ['en' => 'News', 'ar' => 'أخبار'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::NEWS,
                'order' => 5,
            ],
            [
                'key' => 'contact',
                'title' => ['en' => 'Contact Us', 'ar' => 'تواصل معنا'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::CONTACT_US,
                'order' => 6,
            ],
        ];

        $ids = [];

        foreach ($items as $item) {
            $parentId = $item['parent_key'] !== null
                ? ($ids[$item['parent_key']] ?? null)
                : null;

            $menu = Menu::query()
                ->where('title->en', $item['title']['en'])
                ->where('parent_id', $parentId)
                ->first() ?? new Menu;

            $menu->fill([
                'title' => $item['title'],
                'parent_id' => $parentId,
                'route_name' => $item['route_name']->value,
                'order' => $item['order'],
                'is_active' => true,
                'is_navbar' => true,
                'is_footer' => in_array($item['key'], ['about', 'news', 'contact'], true),
            ]);
            $menu->save();

            $ids[$item['key']] = $menu->id;
        }

        Menu::query()
            ->whereNotIn('id', array_values($ids))
            ->update(['is_active' => false]);
    }
}
