<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait AppliesProductListingFilters
{
    protected function applyProductListingFilters(Builder|Relation $query, array $filters): Builder|Relation
    {
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (($filters['availability'] ?? null) === 'in_stock') {
            $query->where('quantity', '>', 0);
        } elseif (($filters['availability'] ?? null) === 'out_of_stock') {
            $query->where('quantity', '<=', 0);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (! empty($filters['attribute_values'])) {
            $valueIds = array_map('intval', $filters['attribute_values']);

            ProductAttributeValue::query()
                ->whereIn('id', $valueIds)
                ->get()
                ->groupBy('product_attribute_id')
                ->each(function ($values) use ($query) {
                    $ids = $values->pluck('id')->all();

                    $query->whereHas('variants.attributeValues', function ($attributeQuery) use ($ids) {
                        $attributeQuery->whereIn('product_attribute_values.id', $ids);
                    });
                });
        }

        match ($filters['sort'] ?? 'default') {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest('id'),
            default => $query->latest('id'),
        };

        return $query;
    }
}
