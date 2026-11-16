<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\ServiceContentSeeder;
use Illuminate\Support\Facades\Cache;

it('updates bilingual service content without changing other site records', function () {
    $this->seed(OrganizationSeeder::class);
    $this->seed(DemoContentSeeder::class);

    $organization = Organization::query()->firstOrFail();
    $projectCount = Project::query()->where('is_active', true)->count();
    $siteEmail = SiteSetting::query()->where('organization_id', $organization->id)->value('support_email');

    Service::query()->where('slug->en', 'interior-design')->update([
        'short_text' => ['en' => 'Old short', 'ar' => 'نص قديم'],
        'text' => ['en' => '<p>Old body</p>', 'ar' => '<p>نص قديم</p>'],
    ]);

    Cache::flush();

    $this->seed(ServiceContentSeeder::class);

    $interior = Service::query()->where('slug->en', 'interior-design')->firstOrFail();

    expect(Service::query()->roots()->where('is_active', true)->count())->toBe(6)
        ->and($interior->getTranslation('title', 'en'))->toBe('Interior Design')
        ->and($interior->getTranslation('title', 'ar'))->toBe('التصميم الداخلي')
        ->and($interior->getTranslation('short_text', 'en'))->toContain('Thoughtfully Designed Interiors')
        ->and($interior->getTranslation('text', 'en'))->toContain('Our Interior Design Services')
        ->and($interior->getTranslation('text', 'ar'))->toContain('اطلب استشارة تصميم داخلي')
        ->and(Service::query()->where('slug->en', 'home-styling')->where('is_active', true)->exists())->toBeTrue()
        ->and(Service::query()->where('slug->en', 'architectural-design')->where('is_active', true)->exists())->toBeFalse()
        ->and(Project::query()->where('is_active', true)->count())->toBe($projectCount)
        ->and(SiteSetting::query()->where('organization_id', $organization->id)->value('support_email'))->toBe($siteEmail);
});
