<?php

use App\Enums\ProductStatusEnum;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Admin;
use App\Models\Product;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Filament::setCurrentPanel('admin');

    $this->admin = Admin::factory()->create();
    $this->admin->assignRole(Role::findOrCreate('super_admin', 'admin'));
});

it('lists existing products in the admin panel', function () {
    Product::query()->create([
        'name' => ['en' => 'Steel Lunch Box', 'ar' => 'علبة غداء ستانلس'],
        'code' => 'AT-100',
        'price' => 150,
    ]);

    $this->actingAs($this->admin, 'admin')
        ->get(ProductResource::getUrl('index'))
        ->assertOk()
        ->assertSee('AT-100');
});

it('creates a product with catalog sizes and colors', function () {
    $this->actingAs($this->admin, 'admin');

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'name.ar' => 'برطمان زجاج',
            'name.en' => 'Glass Jar',
            'code' => 'AT-200',
            'price' => 75,
            'quantity' => 10,
            'status' => ProductStatusEnum::NEW->value,
            'is_active' => true,
            'catalog_sizes' => ['500 ml', '1 L'],
            'catalog_colors' => [
                ['hex' => '#ff0000', 'label' => 'Red'],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $product = Product::query()->where('code', 'AT-200')->sole();

    expect($product->getTranslations('name'))->toBe(['ar' => 'برطمان زجاج', 'en' => 'Glass Jar'])
        ->and($product->status)->toBe(ProductStatusEnum::NEW->value)
        ->and($product->catalog_sizes)->toBe(['500 ml', '1 L'])
        ->and(array_values($product->catalog_colors))->toBe([['hex' => '#ff0000', 'label' => 'Red']]);
});

it('rejects a duplicate product code', function () {
    Product::query()->create([
        'name' => ['en' => 'Existing', 'ar' => 'موجود'],
        'code' => 'AT-300',
    ]);

    $this->actingAs($this->admin, 'admin');

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'name.ar' => 'آخر',
            'code' => 'AT-300',
        ])
        ->call('create')
        ->assertHasFormErrors(['code' => 'unique']);
});

it('forbids admins without product permissions from listing products', function () {
    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(ProductResource::getUrl('index'))
        ->assertForbidden();
});
