<?php

use App\Models\Organization;
use App\Models\SiteSetting;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->seed(OrganizationSeeder::class);

    $organization = Organization::query()->first();

    SiteSetting::query()->updateOrCreate(
        ['organization_id' => $organization->id],
        [
            'site_name_en' => 'United Sport',
            'site_name_ar' => 'يونايتد سبورت',
            'default_locale' => 'ar',
            'coming_soon_enabled' => false,
        ]
    );

    Cache::flush();
    config([
        'app.locale' => 'ar',
        'laravellocalization.useAcceptLanguageHeader' => false,
        'laravellocalization.hideDefaultLocaleInURL' => false,
    ]);
});

it('keeps the arabic locale prefix in urls', function () {
    expect(config('laravellocalization.hideDefaultLocaleInURL'))->toBeFalse()
        ->and(config('laravellocalization.useAcceptLanguageHeader'))->toBeFalse();
});

it('enables apache text compression directives for html responses', function () {
    $htaccess = file_get_contents(public_path('.htaccess'));

    expect($htaccess)->toContain('mod_deflate.c')
        ->and($htaccess)->toContain('DEFLATE text/html');
});
