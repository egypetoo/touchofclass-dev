<?php

use App\Filament\Pages\GenerateUtmLinks;
use App\Models\Admin;
use App\Models\Service;
use App\Services\Marketing\UtmAttribution;
use Spatie\Permission\Models\Role;

it('builds a snapchat contact campaign url for ads', function () {
    config(['app.url' => 'https://atlantistrade-dev.test']);

    $url = app(UtmAttribution::class)->buildCampaignUrl(
        destination: 'contact-us',
        source: 'snapchat',
        medium: 'social',
        campaign: 'ramadan',
    );

    expect($url)->toBe('https://atlantistrade-dev.test/contact-us?utm_source=snapchat&utm_medium=social&utm_campaign=ramadan');
});

it('builds a campaign url for a specific service', function () {
    config(['app.url' => 'https://atlantistrade-dev.test']);

    $service = Service::query()->create([
        'title' => ['en' => 'Dental Care', 'ar' => 'عناية الأسنان'],
        'slug' => ['en' => 'dental-care', 'ar' => 'dental-care'],
        'is_active' => true,
        'order' => 1,
    ]);

    $url = app(UtmAttribution::class)->buildCampaignUrl(
        destination: 'service',
        source: 'facebook',
        medium: 'cpc',
        recordId: $service->id,
    );

    expect($url)->toBe('https://atlantistrade-dev.test/services/dental-care?utm_source=facebook&utm_medium=cpc');
});

it('lets an admin open the utm link builder page', function () {
    $admin = Admin::factory()->create();

    $role = Role::findOrCreate('super_admin', 'admin');
    $admin->assignRole($role);

    $this->actingAs($admin, 'admin')
        ->get(GenerateUtmLinks::getUrl())
        ->assertOk()
        ->assertSee(__('admin_trans.utm_link_builder'), false)
        ->assertSee(__('admin_trans.utm_generated_url'), false)
        ->assertSee(__('admin_trans.utm_destination_service_item'), false);
});
