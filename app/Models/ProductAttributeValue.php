<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ProductAttributeValue extends Model
{
    use HasTranslations;

    protected $fillable = [
        'product_attribute_id',
        'value',
        'meta',
        'sort_order',
        'is_active',
    ];

    public $timestamps = false;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public array $translatable = [
        'value',
        'meta',
    ];

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
