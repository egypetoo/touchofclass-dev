<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Support\Facades\Cache;

/**
 * Updates Touch of Class service titles and bilingual page bodies only.
 * Does not touch projects, menus, products, settings, or other site data.
 */
class ServiceContentSeeder extends DemoContentSeeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if ($organization === null) {
            $organization = Organization::query()->create([
                'name_en' => 'Touch of Class',
                'name_ar' => 'Touch of Class',
            ]);
        }

        $services = $this->syncServices($organization);
        Cache::forget('services');
        Cache::flush();

        $this->command?->info('Updated '.count($services).' Touch of Class services (content only).');
    }
}
