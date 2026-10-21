<?php

namespace App\Models;

use App\Enums\ProductDiscountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ProductDiscount extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'type',
        'value',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductDiscountType::class,
            'value' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function scopeActive(Builder $query, ?Carbon $at = null): Builder
    {
        $at ??= now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $q) use ($at): void {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $at);
            })
            ->where(function (Builder $q) use ($at): void {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $at);
            });
    }

    /**
     * Apply this discount to a unit price (base + variant additional).
     */
    public function applyToUnitPrice(float $unitPrice): float
    {
        return match ($this->type) {
            ProductDiscountType::Percentage => round($unitPrice * (1 - ((float) $this->value / 100)), 2),
            ProductDiscountType::FixedAmount => max(0, round($unitPrice - (float) $this->value, 2)),
        };
    }

    /**
     * Whole number for UI (e.g. badge): stored % or equivalent % off for fixed amount vs. unit price.
     */
    public function displayDiscountPercent(float $unitPrice): float
    {
        return match ($this->type) {
            ProductDiscountType::Percentage => round((float) $this->value),
            ProductDiscountType::FixedAmount => $unitPrice > 0
                ? round(((float) $this->value / $unitPrice) * 100)
                : 0.0,
        };
    }

    public function validateValueForType(): void
    {
        $v = (float) $this->value;
        if ($this->type === ProductDiscountType::Percentage) {
            if ($v < 0 || $v > 100) {
                throw ValidationException::withMessages([
                    'value' => __('admin_trans.discount_invalid_percentage'),
                ]);
            }
        } elseif ($v < 0) {
            throw ValidationException::withMessages([
                'value' => __('admin_trans.discount_invalid_fixed'),
            ]);
        }
    }

    protected static function booted(): void
    {
        static::saving(function (ProductDiscount $discount): void {
            $discount->validateValueForType();
            if ($discount->product_variant_id) {
                $variant = ProductVariant::query()->find($discount->product_variant_id);
                if (! $variant || (int) $variant->product_id !== (int) $discount->product_id) {
                    throw ValidationException::withMessages([
                        'product_variant_id' => __('admin_trans.discount_variant_mismatch'),
                    ]);
                }
            }
        });
    }
}
