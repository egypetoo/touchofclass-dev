<?php

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

it('serves an uploaded logo from the public storage path', function () {
    Storage::fake('public');
    Storage::disk('public')->put('site/logo.png', 'png');

    app()->setLocale('ar');

    $setting = new SiteSetting([
        'logo_ar_path' => 'site/logo.png',
    ]);

    expect($setting->logoUrl())->toBe('/storage/site/logo.png');
});

it('falls back to the design logo when the uploaded file is missing', function () {
    Storage::fake('public');

    app()->setLocale('ar');

    $setting = new SiteSetting([
        'logo_ar_path' => 'site/missing.png',
    ]);

    expect($setting->logoUrl())->toBe(asset('assets/images/logo/logo-transparent.webp'));
});
