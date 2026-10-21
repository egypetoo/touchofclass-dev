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
                'key' => 'products',
                'title' => ['en' => 'Products', 'ar' => 'المنتجات'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::PRODUCTS,
                'order' => 4,
            ],
            [
                'key' => 'pump-selector',
                'title' => ['en' => 'Pump Selector', 'ar' => 'اختيار الطلمبة'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::PUMP_SELECTOR,
                'order' => 5,
            ],
            [
                'key' => 'news',
                'title' => ['en' => 'News', 'ar' => 'أخبار'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::NEWS,
                'order' => 6,
            ],
            [
                'key' => 'contact',
                'title' => ['en' => 'Contact Us', 'ar' => 'تواصل معنا'],
                'parent_key' => null,
                'route_name' => MenuRouteNameEnum::CONTACT_US,
                'order' => 7,
            ],
        ];

        $ids = [];

        foreach ($items as $item) {
            $parentId = $item['parent_key'] !== null
                ? ($ids[$item['parent_key']] ?? null)
                : null;

            $menu = Menu::query()->updateOrCreate(
                [
                    'route_name' => $item['route_name']->value,
                    'parent_id' => $parentId,
                ],
                [
                    'title' => $item['title'],
                    'order' => $item['order'],
                    'is_active' => true,
                    'is_navbar' => true,
                    'is_footer' => in_array($item['key'], ['about', 'news', 'contact'], true),
                ],
            );

            $ids[$item['key']] = $menu->id;
        }

        Menu::query()
            ->whereNotIn('id', array_values($ids))
            ->update(['is_active' => false]);
    }
}
