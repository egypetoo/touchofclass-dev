<?php

use App\Filament\Pages\GenerateUtmLinks;
use App\Models\Admin;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    $this->admin->assignRole(Role::findOrCreate('super_admin', 'admin'));
});

it('switches the admin panel to english and back to arabic', function () {
    $this->actingAs($this->admin, 'admin')
        ->from(GenerateUtmLinks::getUrl())
        ->get(route('admin.locale', 'en'))
        ->assertRedirect(GenerateUtmLinks::getUrl())
        ->assertSessionHas('admin_locale', 'en');

    $this->get(GenerateUtmLinks::getUrl())
        ->assertOk()
        ->assertSee('lang="en"', false)
        ->assertSee('dir="ltr"', false)
        ->assertSee('العربية', false);

    $this->get(route('admin.locale', 'ar'));

    $this->get(GenerateUtmLinks::getUrl())
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertSee('English', false);
});

it('rejects unsupported admin locales', function () {
    $this->actingAs($this->admin, 'admin')
        ->get('/admin/locale/fr')
        ->assertNotFound();
});
