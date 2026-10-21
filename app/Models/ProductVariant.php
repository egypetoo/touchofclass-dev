<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'sku',
        'additional_price',
        'quantity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // relations
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // relation to attribute values
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_values',
            'variant_id',
            'attribute_value_id'
        );
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(ProductDiscount::class, 'product_variant_id');
    }

    public function netUnitPrice(): float
    {
        return $this->product->netUnitPrice($this);
    }

    /**
     * Human-readable label for admin UIs (e.g. "Green, Large" from attribute values).
     */
    public function displayLabel(): string
    {
        $this->loadMissing('attributeValues');

        $parts = $this->attributeValues->map(function (ProductAttributeValue $av) {
            return $av->value;
        })->filter()->values()->all();

        if ($parts !== []) {
            return implode(', ', $parts);
        }

        return (string) $this->sku;
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
