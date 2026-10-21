<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use App\Models\Concerns\ReplacesCallShortcodeInContent;
use App\Traits\HasLocalizedUrl;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasLocalizedUrl, HasSlug, HasTranslations, InteractsWithMedia, ReplacesCallShortcodeInContent, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'category_id',
        'name',
        'short_description',
        'description',
        'price',
        'code',
        'quantity',
        'is_active',
        'slug',
        'meta_title',
        'meta_desc',
        'status',
        'sort_order',
        'catalog_sizes',
        'catalog_colors',
        'catalog_files',
        'specifications',
    ];

    public array $translatable = [
        'name',
        'short_description',
        'description',
        'slug',
        'meta_title',
        'meta_desc',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'catalog_sizes' => 'array',
            'catalog_colors' => 'array',
            'catalog_files' => 'array',
            'specifications' => 'array',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    protected function localizedUrlPath(): string
    {
        return 'products';
    }

    // Relations
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function styleAnswers(): BelongsToMany
    {
        return $this->belongsToMany(StyleAnswer::class, 'product_style_answer')
            ->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(ProductDiscount::class);
    }

    public function tabs(): HasMany
    {
        return $this->hasMany(ProductTab::class)->orderBy('sort_order')->orderBy('id');
    }

    public static function listingRelations(): array
    {
        return [
            'tags' => fn ($query) => $query->active(),
            'media' => fn ($query) => $query->where('collection_name', 'products')->orderBy('order_column'),
            'category',
            'discounts' => fn ($query) => $query->active(),
            'variants' => fn ($query) => $query->where('is_active', true),
        ];
    }

    public function loadRelations(): void
    {
        $this->load(static::listingRelations());
    }

    public function attributes()
    {
        return ProductAttribute::whereHas('values', function ($query) {
            $query->whereExists(function ($q) {
                $q->from('product_variant_values')
                    ->whereColumn('product_variant_values.attribute_value_id', 'product_attribute_values.id')
                    ->whereIn('product_variant_values.variant_id', function ($sub) {
                        $sub->select('id')
                            ->from('product_variants')
                            ->where('product_id', $this->id);
                    });
            });
        })->with('values')->get();
    }

    // scope to get status products
    #[Scope]
    protected function status(Builder $query, ProductStatusEnum $type): void
    {
        $query->where('status', $type);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function withRelations(Builder $query): void
    {
        $query->with(static::listingRelations());
    }

    public function getHasVariantsAttribute(): bool
    {
        return $this->variants()->active()->exists();
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discounts()->active()->exists();
    }

    public function getPriceAttribute(mixed $value): float
    {
        return round((float) ($value ?? 0));
    }

    /**
     * Gross unit price before discount: product base, plus optional variant surcharge.
     */
    public function grossUnitPrice(?ProductVariant $variant = null): float
    {
        $base = (float) $this->price;
        if ($variant) {
            $base += (float) $variant->additional_price;
        }

        return $base;
    }

    /**
     * When `$variant` is set, variant-specific rules win; else product-wide. Newest id wins if several match.
     */
    public function applicableDiscount(?ProductVariant $variant = null): ?ProductDiscount
    {
        if ($variant) {
            $specific = $this->discounts()
                ->active()
                ->where('product_variant_id', $variant->id)
                ->orderByDesc('id')
                ->first();
            if ($specific) {
                return $specific;
            }
        }

        return $this->discounts()
            ->active()
            ->whereNull('product_variant_id')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Net unit price after {@see applicableDiscount()} (same as gross when no discount applies).
     */
    public function netUnitPrice(?ProductVariant $variant = null): float
    {
        $unit = $this->grossUnitPrice($variant);
        $discount = $this->applicableDiscount($variant);

        if ($discount === null) {
            return $unit;
        }

        return $discount->applyToUnitPrice($unit);
    }

    /**
     * Min/max gross unit price across variants (before discount).
     *
     * @return array{from: float|null, to: float|null}
     */
    public function variantUnitPriceRange(): array
    {
        $prices = $this->variants->map(
            fn (ProductVariant $variant): float => $this->grossUnitPrice($variant)
        );

        return [
            'from' => $prices->min(),
            'to' => $prices->max(),
        ];
    }

    /**
     * Human-readable gross range across variants: one amount if min equals max, otherwise "min-max".
     */
    public function variantUnitPriceRangeLabel(): string
    {
        $range = $this->variantUnitPriceRange();
        $from = $range['from'];
        $to = $range['to'];

        if ($from === null || $to === null) {
            return '';
        }

        if (round((float) $from, 2) === round((float) $to, 2)) {
            return (string) $from;
        }

        return $from.' - '.$to;
    }

    /**
     * Featured / main image: first file in the ordered `images` collection (set order in Filament).
     */
    /**
     * Downloadable catalog PDFs stored in public/catalogs.
     *
     * @return list<array{name: string, url: string, size: string}>
     */
    public function catalogDownloads(): array
    {
        $downloads = [];

        foreach ($this->catalog_files ?? [] as $file) {
            $filename = (string) ($file['file'] ?? '');
            $absolute = public_path('catalogs/'.$filename);

            if ($filename === '' || ! is_file($absolute)) {
                continue;
            }

            $locale = app()->getLocale();
            $name = $file['name_'.$locale] ?? $file['name_ar'] ?? $file['name_en'] ?? $filename;

            $downloads[] = [
                'name' => (string) $name,
                'url' => asset('catalogs/'.rawurlencode($filename)),
                'size' => $this->formatCatalogSize((int) filesize($absolute)),
            ];
        }

        return $downloads;
    }

    public function specText(mixed $value): string
    {
        if (! is_array($value)) {
            return (string) $value;
        }

        $locale = app()->getLocale();

        return (string) ($value[$locale] ?? $value['ar'] ?? $value['en'] ?? '');
    }

    protected function formatCatalogSize(int $bytes): string
    {
        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 1).' MB';
        }

        return number_format(max(1, (int) ceil($bytes / 1024))).' KB';
    }

    public function mainImageMedia(): ?Media
    {
        return $this->getFirstMedia('products');
    }

    /**
     * Public URL for the main image, or empty string when none.
     */
    public function mainImageUrl(): string
    {
        return $this->publicMediaUrl($this->mainImageMedia());
    }

    /**
     * @return list<string>
     */
    public function galleryUrls(): array
    {
        return $this->getMedia('products')
            ->map(fn (Media $media): string => $this->publicMediaUrl($media))
            ->filter()
            ->values()
            ->all();
    }

    public function publicMediaUrl(?Media $media): string
    {
        if (! $media) {
            return '';
        }

        $brandImage = 'assets/images/products/brands/'.$media->file_name;

        if (is_file(public_path($brandImage))) {
            return asset($brandImage);
        }

        return asset('storage/media/'.ltrim(str_replace('\\', '/', $media->getPathRelativeToRoot()), '/'));
    }

    public function getDescriptionAttribute($description)
    {
        return $this->replaceCallShortcode($description);
    }

    /**
     * HTML-ready description for product detail tabs.
     */
    public function getDescriptionHtmlAttribute(): string
    {
        return (string) ($this->description ?? '');
    }

    public function stockCount(?ProductVariant $variant = null): int
    {
        if ($variant) {
            return $variant->quantity;
        }

        return $this->quantity;
    }

    public function getInStockAttribute(): bool
    {
        return $this->stockCount() > 0;
    }

    /**
     * Whether the product can be ordered (any variant stock or base product stock).
     */
    public function hasOrderableStock(): bool
    {
        if ($this->has_variants) {
            return $this->variants->where('is_active', true)->sum('quantity') > 0;
        }

        return $this->stockCount() > 0;
    }

    /**
     * Variant rows for storefront JS (price, discount display, stock, attribute value ids).
     *
     * @return array<int, array<string, mixed>>
     */
    public function variantsClientPayload(): array
    {
        if (! $this->has_variants) {
            return [];
        }

        $this->loadMissing('variants.attributeValues');

        $rows = [];
        foreach ($this->variants as $variant) {
            if (! $variant->is_active) {
                continue;
            }

            $variant->loadMissing('attributeValues');
            $ids = $variant->attributeValues->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            if ($ids === []) {
                continue;
            }

            $gross = $this->grossUnitPrice($variant);
            $net = $this->netUnitPrice($variant);
            $discount = $this->applicableDiscount($variant);
            $hasDiscount = $discount !== null && round((float) $gross, 2) > round((float) $net, 2);

            $rows[] = [
                'id' => (int) $variant->id,
                'attribute_value_ids' => $ids,
                'gross_price' => round((float) $gross, 2),
                'net_price' => round((float) $net, 2),
                'has_discount' => $hasDiscount,
                'discount_percent' => $hasDiscount && $discount !== null
                    ? round((float) $discount->displayDiscountPercent($gross), 2)
                    : 0.0,
                'stock' => (int) $variant->quantity,
            ];
        }

        return $rows;
    }
}
