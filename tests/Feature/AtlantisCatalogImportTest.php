<?php

use App\Models\Category;
use App\Models\Organization;
use App\Models\Product;
use App\Support\AtlantisCatalogImporter;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Support\Facades\File;

it('purges existing catalog data and imports category subcategory products from a folder', function () {
    $this->seed(OrganizationSeeder::class);

    $organization = Organization::query()->firstOrFail();

    Category::query()->create([
        'organization_id' => $organization->id,
        'name' => ['en' => 'Old Category', 'ar' => 'قديم'],
        'slug' => ['en' => 'old-category', 'ar' => 'old-category'],
        'order' => 1,
        'is_active' => true,
    ]);

    Product::query()->create([
        'name' => ['en' => 'Old Product', 'ar' => 'منتج قديم'],
        'slug' => ['en' => 'old-product', 'ar' => 'old-product'],
        'code' => 'OLD-001',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $root = storage_path('app/testing-catalog-'.uniqid());
    $sub = $root.DIRECTORY_SEPARATOR.'أدوات المائده'.DIRECTORY_SEPARATOR.'زجاج';
    File::ensureDirectoryExists($sub);

    $imagePath = $sub.DIRECTORY_SEPARATOR.'كأس تجريبي.png';
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    File::put($imagePath, $png);

    $stats = app(AtlantisCatalogImporter::class)->import($organization, $root, replaceExisting: true);

    expect($stats['categories'])->toBe(1)
        ->and($stats['subcategories'])->toBe(1)
        ->and($stats['products'])->toBe(1)
        ->and(Category::query()->where('slug->en', 'old-category')->exists())->toBeFalse()
        ->and(Product::query()->where('code', 'OLD-001')->exists())->toBeFalse()
        ->and(Category::query()->whereNull('parent_id')->where('is_active', true)->count())->toBe(1)
        ->and(Category::query()->whereNotNull('parent_id')->where('is_active', true)->count())->toBe(1)
        ->and(Product::query()->where('is_active', true)->count())->toBe(1)
        ->and(Product::query()->where('is_active', true)->first()?->getFirstMedia('products'))->not->toBeNull();

    File::deleteDirectory($root);
});

it('corrects the stainless spelling from catalog file names', function (string $fileName, string $expectedAr, string $expectedEn) {
    $this->seed(OrganizationSeeder::class);

    $organization = Organization::query()->firstOrFail();

    $root = storage_path('app/testing-catalog-'.uniqid());
    $sub = $root.DIRECTORY_SEPARATOR.'أدوات الصالة'.DIRECTORY_SEPARATOR.'صالة';
    File::ensureDirectoryExists($sub);
    File::put(
        $sub.DIRECTORY_SEPARATOR.$fileName,
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
    );

    app(AtlantisCatalogImporter::class)->import($organization, $root, replaceExisting: true);

    $product = Product::query()->firstOrFail();

    expect($product->getTranslation('name', 'ar'))->toBe($expectedAr)
        ->and($product->getTranslation('name', 'en'))->toBe($expectedEn);

    File::deleteDirectory($root);
})->with([
    'استلس' => ['_تراي جاك استلس.png', 'تراي جاك ستانلس', 'Tray Jak Stainless'],
    'استانلس' => ['سكرية استانلس.png', 'سكرية ستانلس', 'Skrya Stainless'],
]);
