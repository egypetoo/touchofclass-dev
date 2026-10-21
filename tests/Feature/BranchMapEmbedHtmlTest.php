<?php

use App\Models\Branch;
use App\Models\Organization;
use Database\Seeders\OrganizationSeeder;

it('wraps a bare map embed url in an iframe', function () {
    $this->seed(OrganizationSeeder::class);

    $branch = Branch::query()->create([
        'organization_id' => Organization::query()->value('id'),
        'name' => ['en' => 'Cairo Branch', 'ar' => 'فرع القاهرة'],
        'address' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
        'map_embed' => 'https://maps.google.com/maps?q=Cairo&output=embed',
        'is_active' => true,
        'order' => 1,
    ]);

    $html = $branch->mapEmbedHtml('branch-card-map-iframe');

    expect($html)
        ->toContain('<iframe')
        ->toContain('class="branch-card-map-iframe"')
        ->toContain('src="https://maps.google.com/maps?q=Cairo&amp;output=embed"')
        ->not->toStartWith('https://');
});

it('keeps a full iframe snippet when map_embed already contains iframe markup', function () {
    $this->seed(OrganizationSeeder::class);

    $branch = Branch::query()->create([
        'organization_id' => Organization::query()->value('id'),
        'name' => ['en' => 'Tanta Branch', 'ar' => 'فرع طنطا'],
        'map_embed' => '<iframe src="https://maps.google.com/maps?q=Tanta&output=embed" title="Tanta"></iframe>',
        'is_active' => true,
        'order' => 2,
    ]);

    expect($branch->mapEmbedHtml())
        ->toContain('<iframe')
        ->toContain('src="https://maps.google.com/maps?q=Tanta&output=embed"');
});
