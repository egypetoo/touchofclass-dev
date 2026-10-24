<?php

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Models\Blog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Organization;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Section;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Team;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\OrganizationSeeder;

it('seeds Touch of Class demo content from the design', function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(MenuSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $organization = Organization::query()->first();

    expect($organization)->not->toBeNull()
        ->and($organization->name_en)->toBe('Touch of Class')
        ->and($organization->name_ar)->toBe('Touch of Class')
        ->and(Menu::query()->where('title->en', 'Residential')->where('is_active', true)->exists())->toBeTrue();

    $site = SiteSetting::query()->where('organization_id', $organization->id)->first();

    expect($site)->not->toBeNull()
        ->and($site->site_name_en)->toBe('Touch of Class')
        ->and($site->support_email)->toBe('info@touchofclass.com')
        ->and($site->fax())->toBeNull()
        ->and($site->ctaPhones())->toHaveCount(1)
        ->and($site->default_locale)->toBe('ar')
        ->and($site->coming_soon_enabled)->toBeFalse();

    expect(Category::query()->where('organization_id', $organization->id)->whereNull('parent_id')->where('is_active', true)->count())
        ->toBe(4)
        ->and(Category::query()->where('organization_id', $organization->id)->where('is_active', true)->count())
        ->toBe(4);

    $villa = Product::query()->where('code', 'TOC-RES-01')->first();

    expect(Product::query()->where('is_active', true)->count())->toBe(19)
        ->and($villa->category->getTranslation('slug', 'en'))->toBe('residential')
        ->and($villa->getTranslation('name', 'ar'))->toBe('فيلا الشيخ زايد')
        ->and($villa->getMedia('products'))->toHaveCount(1)
        ->and($villa->catalog_files ?? [])->toBe([])
        ->and(Product::query()->where('code', 'KM-P-SP')->where('is_active', true)->exists())->toBeFalse();

    $interior = Service::query()->where('slug->en', 'interior-design')->first();

    expect(Service::query()->where('organization_id', $organization->id)->roots()->where('is_active', true)->count())->toBe(5)
        ->and($interior->getTranslation('title', 'ar'))->toBe('التصميم الداخلي');

    expect(Blog::query()->where('is_active', true)->count())->toBe(4)
        ->and(Blog::query()->where('slug->en', 'design-style-guide')->exists())->toBeTrue();

    expect(ProjectCategory::query()->where('is_active', true)->count())->toBe(4)
        ->and(Project::query()->where('is_active', true)->where('show_in_home', true)->count())->toBe(10)
        ->and(Project::query()->where('slug->en', 'mivida')->first()?->category?->getTranslation('slug', 'en'))->toBe('residential')
        ->and(Project::query()->where('slug->en', 'mivida')->first()?->getMedia('gallery')->count())->toBeGreaterThan(0)
        ->and(Project::query()->where('slug->en', 'restaurant')->first()?->category?->getTranslation('slug', 'en'))->toBe('commercial')
        ->and(Partner::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(10);

    expect(Branch::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(1)
        ->and(Branch::query()->where('is_active', true)->first()->email)->toBe('info@touchofclass.com')
        ->and(Branch::query()->where('is_active', true)->first()->phone_1)->toBe('1112302002');

    $homeSectionCount = fn (SectionTypeEnum $type): int => Section::query()
        ->where('organization_id', $organization->id)
        ->where('page', PageEnum::HOME->value)
        ->where('type', $type->value)
        ->where('is_active', true)
        ->count();

    expect($homeSectionCount(SectionTypeEnum::HERO))->toBe(3)
        ->and($homeSectionCount(SectionTypeEnum::STATISTICS))->toBe(4);

    $this->seed(DemoContentSeeder::class);

    expect(Category::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(4)
        ->and(Product::query()->where('is_active', true)->count())->toBe(19)
        ->and(Service::query()->where('is_active', true)->count())->toBe(5)
        ->and(Blog::query()->where('is_active', true)->count())->toBe(4)
        ->and(Partner::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(10)
        ->and(Branch::query()->where('organization_id', $organization->id)->where('is_active', true)->count())->toBe(1)
        ->and(Team::query()->where('is_active', true)->count())->toBe(2)
        ->and(Organization::query()->count())->toBe(1);
});
