<?php

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Models\Blog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Organization;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Project;
use App\Models\Section;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\OrganizationSeeder;

it('seeds King Motors demo content from the design', function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(MenuSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $organization = Organization::query()->first();

    expect($organization)->not->toBeNull()
        ->and($organization->name_en)->toBe('King Motors')
        ->and($organization->name_ar)->toBe('كينج موتورز');

    $site = SiteSetting::query()->where('organization_id', $organization->id)->first();

    expect($site)->not->toBeNull()
        ->and($site->site_name_en)->toBe('King Motors')
        ->and($site->support_email)->toBe('info@kingmotors.com')
        ->and($site->fax())->toBeNull()
        ->and($site->ctaPhones())->toHaveCount(2)
        ->and($site->default_locale)->toBe('ar')
        ->and($site->coming_soon_enabled)->toBeFalse();

    expect(Category::query()->where('organization_id', $organization->id)->whereNull('parent_id')->where('is_active', true)->count())
        ->toBe(5)
        ->and(Category::query()->where('organization_id', $organization->id)->where('is_active', true)->count())
        ->toBe(6);

    $pump = Product::query()->where('code', 'KM-P-SP')->first();
    $panels = Category::query()->where('slug->en', 'electrical-panels')->first();

    expect(Product::query()->where('is_active', true)->count())->toBe(18)
        ->and($pump->category->getTranslation('slug', 'en'))->toBe('deep-well-pumps')
        ->and($pump->getTranslation('name', 'en'))->toBe('SP')
        ->and($pump->getMedia('products'))->toHaveCount(1)
        ->and($pump->catalog_files)->toHaveCount(2)
        ->and($pump->specifications['brand'] ?? null)->toBe('SP')
        ->and(collect($pump->catalog_files)->pluck('file')->contains(fn (string $file): bool => str_starts_with($file, 'Data SP pumps')))->toBeTrue()
        ->and(Product::query()->where('code', 'KM-M-KPS')->first()->catalog_files)->toHaveCount(2)
        ->and(Product::query()->where('code', 'KM-P-TESLA')->first()->catalog_files[0]['name_ar'])->toBe('كتالوج TESLA')
        ->and(Product::query()->where('code', 'KM-INV-DELIXI')->first()->catalog_files[0]['name_en'])->toBe('Delixi inverter panel data')
        ->and(Product::query()->where('code', 'KM-P-IMPT')->where('is_active', true)->exists())->toBeFalse()
        ->and($panels->children()->where('is_active', true)->count())->toBe(1);

    $consulting = Service::query()->where('slug->en', 'technical-consulting')->first();

    expect(Service::query()->where('organization_id', $organization->id)->roots()->where('is_active', true)->count())->toBe(4)
        ->and($consulting->getTranslation('title', 'ar'))->toBe('استشارات');

    expect(Blog::query()->where('is_active', true)->count())->toBe(7)
        ->and(Blog::query()->where('slug->en', 'pump-selection-guide')->exists())->toBeTrue();

    expect(Project::query()->where('is_active', true)->count())->toBe(0)
        ->and(Partner::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(0);

    expect(Branch::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(1)
        ->and(Branch::query()->where('is_active', true)->first()->email)->toBe('info@kingmotors.com');

    $homeSectionCount = fn (SectionTypeEnum $type): int => Section::query()
        ->where('organization_id', $organization->id)
        ->where('page', PageEnum::HOME->value)
        ->where('type', $type->value)
        ->where('is_active', true)
        ->count();

    expect($homeSectionCount(SectionTypeEnum::HERO))->toBe(3)
        ->and($homeSectionCount(SectionTypeEnum::STATISTICS))->toBe(0);

    $this->seed(DemoContentSeeder::class);

    expect(Category::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(6)
        ->and(Product::query()->where('is_active', true)->count())->toBe(18)
        ->and(Service::query()->where('is_active', true)->count())->toBe(4)
        ->and(Blog::query()->where('is_active', true)->count())->toBe(7)
        ->and(Partner::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(0)
        ->and(Branch::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(1)
        ->and(Organization::query()->count())->toBe(1);
});
