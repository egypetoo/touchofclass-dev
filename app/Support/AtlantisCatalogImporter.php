<?php

namespace App\Support;

use App\Enums\ProductStatusEnum;
use App\Models\Category;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class AtlantisCatalogImporter
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'jfif', 'JPG', 'JPEG', 'PNG', 'WEBP', 'GIF', 'JFIF'];

    /**
     * Known Arabic root/sub folder names → English labels for bilingual content.
     *
     * @var array<string, string>
     */
    private const NAME_MAP = [
        'أدوات الصاله' => 'Hall Supplies',
        'أدوات الصالة' => 'Hall Supplies',
        'أدوات المائده' => 'Tableware',
        'أدوات المائدة' => 'Tableware',
        'أدوات المطبخ' => 'Kitchen Tools',
        'المعدات' => 'Equipment',
        'بار' => 'Bar',
        'ميلامين' => 'Melamine',
        'صالة' => 'Hall',
        'زجاج' => 'Glass',
        'مائدة' => 'Dinnerware',
        'مائده' => 'Dinnerware',
        'مطبخ' => 'Kitchen',
        'ملامين' => 'Melamine Line',
        'اطباق ابيض' => 'White Plates',
        'اطباق حجري' => 'Stone Plates',
        'بولات ابيض' => 'White Bowls',
        'بولات حجري' => 'Stone Bowls',
        'سرافيس حجري' => 'Stone Serveware',
        'صوصات' => 'Sauces',
    ];

    /**
     * Misspellings in the client's image file names → corrected Arabic product wording.
     *
     * @var array<string, string>
     */
    private const PRODUCT_NAME_CORRECTIONS = [
        'استلس' => 'ستانلس',
        'استانلس' => 'ستانلس',
        'أستانلس' => 'ستانلس',
        'إستانلس' => 'ستانلس',
    ];

    /**
     * Arabic product words with a proper English term (used instead of transliteration).
     *
     * @var array<string, string>
     */
    private const PRODUCT_NAME_ENGLISH_WORDS = [
        'ستانلس' => 'Stainless',
    ];

    /**
     * @return array{categories: int, subcategories: int, products: int}
     */
    public function import(Organization $organization, string $rootPath, bool $replaceExisting = true): array
    {
        $rootPath = rtrim($rootPath, DIRECTORY_SEPARATOR);

        if (! is_dir($rootPath)) {
            throw new \InvalidArgumentException("Catalog path not found: {$rootPath}");
        }

        if ($replaceExisting) {
            $this->purgeCatalog($organization);
        }

        $stats = ['categories' => 0, 'subcategories' => 0, 'products' => 0];
        $order = 0;

        foreach ($this->directories($rootPath) as $categoryDir) {
            $order++;
            $categoryNameAr = $categoryDir->getFilename();
            $category = $this->syncCategory(
                $organization,
                $categoryNameAr,
                null,
                $order,
                true
            );
            $stats['categories']++;

            $leafDirs = $this->leafImageDirectories($categoryDir->getPathname());

            if ($leafDirs === []) {
                continue;
            }

            $subOrder = 0;
            foreach ($leafDirs as $leafDir) {
                $subOrder++;
                $subNameAr = basename($leafDir);
                $subcategory = $this->syncCategory(
                    $organization,
                    $subNameAr,
                    $category,
                    $subOrder,
                    false
                );
                $stats['subcategories']++;

                $productOrder = 0;
                foreach ($this->imageFiles($leafDir) as $imageFile) {
                    $productOrder++;
                    $this->syncProduct(
                        $subcategory,
                        $imageFile,
                        $productOrder
                    );
                    $stats['products']++;
                }
            }
        }

        return $stats;
    }

    private function purgeCatalog(Organization $organization): void
    {
        Product::query()->withTrashed()->chunkById(100, function ($products): void {
            foreach ($products as $product) {
                if (method_exists($product, 'clearMediaCollection')) {
                    $product->clearMediaCollection('products');
                }
                $product->forceDelete();
            }
        });

        Category::query()
            ->where('organization_id', $organization->id)
            ->whereNotNull('parent_id')
            ->orderByDesc('id')
            ->each(function (Category $category): void {
                if (method_exists($category, 'clearMediaCollection')) {
                    $category->clearMediaCollection('categories');
                }
                $category->delete();
            });

        Category::query()
            ->where('organization_id', $organization->id)
            ->whereNull('parent_id')
            ->orderByDesc('id')
            ->each(function (Category $category): void {
                if (method_exists($category, 'clearMediaCollection')) {
                    $category->clearMediaCollection('categories');
                }
                $category->delete();
            });
    }

    /**
     * @return list<\SplFileInfo>
     */
    private function directories(string $path): array
    {
        $items = [];

        foreach (File::directories($path) as $dir) {
            $items[] = new \SplFileInfo($dir);
        }

        usort($items, fn (\SplFileInfo $a, \SplFileInfo $b): int => strcmp($a->getFilename(), $b->getFilename()));

        return $items;
    }

    /**
     * Leaf folders that contain product images (skips intermediate folders like ميلامين/ملامين).
     *
     * @return list<string>
     */
    private function leafImageDirectories(string $path): array
    {
        $leaves = [];

        $this->collectLeafImageDirectories($path, $leaves);

        return $leaves;
    }

    /**
     * @param  list<string>  $leaves
     */
    private function collectLeafImageDirectories(string $path, array &$leaves): void
    {
        $childDirs = File::directories($path);
        $images = $this->imageFiles($path);

        if ($images !== [] && $childDirs === []) {
            $leaves[] = $path;

            return;
        }

        if ($images !== [] && $childDirs !== []) {
            $leaves[] = $path;
        }

        foreach ($childDirs as $child) {
            $this->collectLeafImageDirectories($child, $leaves);
        }
    }

    /**
     * @return list<string>
     */
    private function imageFiles(string $path): array
    {
        $files = [];

        foreach (File::files($path) as $file) {
            $ext = $file->getExtension();
            if (in_array($ext, self::IMAGE_EXTENSIONS, true)) {
                $files[] = $file->getPathname();
            }
        }

        natcasesort($files);

        return array_values($files);
    }

    private function syncCategory(
        Organization $organization,
        string $nameAr,
        ?Category $parent,
        int $order,
        bool $isRoot
    ): Category {
        $nameEn = $this->englishName($nameAr);
        $slug = $this->stableSlug($nameEn, $nameAr, $parent?->id);

        $category = Category::query()
            ->where('organization_id', $organization->id)
            ->where(function ($query) use ($slug) {
                $query->where('slug->en', $slug)
                    ->orWhere('slug->ar', $slug);
            })
            ->first() ?? new Category(['organization_id' => $organization->id]);

        $short = [
            'en' => $nameEn,
            'ar' => $nameAr,
        ];

        $category->fill([
            'organization_id' => $organization->id,
            'parent_id' => $parent?->id,
            'name' => ['en' => $nameEn, 'ar' => $nameAr],
            'short_text' => $short,
            'text' => [
                'en' => '<p>'.$nameEn.'</p>',
                'ar' => '<p>'.$nameAr.'</p>',
            ],
            'meta_title' => ['en' => $nameEn, 'ar' => $nameAr],
            'meta_desc' => $short,
            'slug' => ['en' => $slug, 'ar' => $slug],
            'order' => $order,
            'is_active' => true,
            'show_in_navbar' => $isRoot,
            'show_in_footer' => $isRoot,
            'is_main_section' => $isRoot,
        ]);
        $category->save();

        return $category;
    }

    private function syncProduct(Category $subcategory, string $imagePath, int $order): Product
    {
        $baseName = pathinfo($imagePath, PATHINFO_FILENAME);
        $nameAr = $this->cleanProductName($baseName);
        $nameEn = ArabicTransliterator::containsArabic($nameAr)
            ? ArabicTransliterator::toLatin(strtr($nameAr, self::PRODUCT_NAME_ENGLISH_WORDS))
            : $nameAr;
        $code = 'AT-'.substr(sha1($imagePath), 0, 10);
        $slug = $this->stableSlug($nameEn, $nameAr, $subcategory->id).'-'.substr($code, -6);

        $product = Product::query()->where('code', $code)->first() ?? new Product;

        $product->fill([
            'category_id' => $subcategory->id,
            'name' => ['en' => $nameEn, 'ar' => $nameAr],
            'short_description' => [
                'en' => $subcategory->getTranslation('name', 'en'),
                'ar' => $subcategory->getTranslation('name', 'ar'),
            ],
            'description' => [
                'en' => '<p>'.$nameEn.'</p>',
                'ar' => '<p>'.$nameAr.'</p>',
            ],
            'price' => null,
            'code' => $code,
            'quantity' => 0,
            'is_active' => true,
            'status' => ProductStatusEnum::FEATURED->value,
            'sort_order' => $order,
            'meta_title' => ['en' => $nameEn, 'ar' => $nameAr],
            'meta_desc' => [
                'en' => $nameEn,
                'ar' => $nameAr,
            ],
            'slug' => ['en' => $slug, 'ar' => $slug],
            'catalog_sizes' => null,
            'catalog_colors' => null,
        ]);
        $product->save();

        $this->attachLocalMedia($product, 'products', $imagePath);

        return $product;
    }

    private function attachLocalMedia(HasMedia $model, string $collection, string $absolutePath): void
    {
        if (! is_file($absolutePath)) {
            return;
        }

        $fingerprint = sha1_file($absolutePath) ?: $absolutePath;
        $existing = $model->getFirstMedia($collection);

        if ($existing && $existing->getCustomProperty('source_fingerprint') === $fingerprint) {
            return;
        }

        $model->clearMediaCollection($collection);
        $model->addMedia($absolutePath)
            ->preservingOriginal()
            ->withCustomProperties(['source_fingerprint' => $fingerprint])
            ->toMediaCollection($collection);
    }

    private function englishName(string $arabicOrLabel): string
    {
        return self::NAME_MAP[$arabicOrLabel] ?? $arabicOrLabel;
    }

    private function cleanProductName(string $filename): string
    {
        $name = preg_replace('/^_+/u', '', $filename) ?? $filename;
        $name = preg_replace('/\s+/u', ' ', $name) ?? $name;
        $name = strtr($name, self::PRODUCT_NAME_CORRECTIONS);

        return trim($name) !== '' ? trim($name) : 'Product';
    }

    private function stableSlug(string $nameEn, string $nameAr, ?int $scopeId): string
    {
        $base = Str::slug($nameEn);
        if ($base === '') {
            $base = 'item-'.substr(sha1($nameAr), 0, 8);
        }

        if ($scopeId) {
            $base .= '-'.$scopeId;
        }

        return Str::limit($base, 80, '');
    }
}
