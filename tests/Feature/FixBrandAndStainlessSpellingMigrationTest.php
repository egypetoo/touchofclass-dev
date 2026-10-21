<?php

use App\Models\Organization;
use App\Models\Product;
use App\Models\Section;
use Database\Seeders\OrganizationSeeder;

function runSpellingFixMigration(): void
{
    $migration = require database_path('migrations/2026_10_04_104015_fix_brand_and_stainless_spelling_in_content.php');
    $migration->up();
}

it('renames the Arabic brand to أطلانتس across content', function () {
    $this->seed(OrganizationSeeder::class);

    $organization = Organization::query()->firstOrFail();
    $organization->forceFill(['name_ar' => 'أتلانتس تريد'])->save();

    $section = Section::query()->create([
        'organization_id' => $organization->id,
        'title' => ['ar' => 'عن أتلانتس تريد', 'en' => 'About Atlantis Trade'],
    ]);

    runSpellingFixMigration();

    expect($organization->fresh()->name_ar)->toBe('أطلانتس تريد')
        ->and($section->fresh()->getTranslations('title'))->toBe([
            'ar' => 'عن أطلانتس تريد',
            'en' => 'About Atlantis Trade',
        ]);
});

it('corrects stainless spellings in product names and slugs', function () {
    $product = Product::query()->create([
        'name' => ['ar' => 'سكرية استانلس', 'en' => 'Skrya Astanls'],
        'description' => ['ar' => '<p>تراي جاك استلس</p>', 'en' => '<p>Tray Jak Astls</p>'],
        'slug' => ['ar' => 'سكرية-استانلس', 'en' => 'skrya-astanls'],
        'code' => 'AT-TEST',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    runSpellingFixMigration();
    runSpellingFixMigration();

    $product->refresh();

    expect($product->getTranslations('name'))->toBe(['ar' => 'سكرية ستانلس', 'en' => 'Skrya Stainless'])
        ->and($product->getTranslations('description'))->toBe(['ar' => '<p>تراي جاك ستانلس</p>', 'en' => '<p>Tray Jak Stainless</p>'])
        ->and($product->getTranslations('slug'))->toBe(['ar' => 'سكرية-ستانلس', 'en' => 'skrya-stainless']);
});
