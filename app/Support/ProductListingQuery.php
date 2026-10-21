<?php

namespace App\Support;

class ProductListingQuery
{
    /** @return array<int> */
    public static function attributeValues(): array
    {
        $values = request('attribute_values')
            ?? data_get(request()->all(), 'filter.attribute_values');

        return array_map('intval', (array) $values);
    }

    /** @return array<string, mixed> */
    public static function paramsExcept(array $except = []): array
    {
        $except = array_merge(['page', 'filter'], $except);

        $params = request()->except($except);

        $attributeValues = self::attributeValues();
        if ($attributeValues !== []) {
            $params['attribute_values'] = $attributeValues;
        }

        return $params;
    }
}
