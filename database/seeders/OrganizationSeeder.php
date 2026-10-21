<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if ($organization) {
            $organization->update([
                'name_en' => 'King Motors',
                'name_ar' => 'كينج موتورز',
            ]);

            return;
        }

        Organization::query()->create([
            'name_en' => 'King Motors',
            'name_ar' => 'كينج موتورز',
        ]);
    }
}
