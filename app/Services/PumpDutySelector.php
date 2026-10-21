<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class PumpDutySelector
{
    /**
     * @return array{flow: float, head: float}|null
     */
    public function duty(?float $flow, string $flowUnit, ?float $head, string $headUnit): ?array
    {
        if ($flow === null || $head === null) {
            return null;
        }

        return [
            'flow' => round($this->toCubicMetersPerHour($flow, $flowUnit), 2),
            'head' => round($this->toMeters($head, $headUnit), 2),
        ];
    }

    /**
     * Highest catalogue flow in cubic metres per hour, when the product sheet states one.
     */
    public function catalogFlow(Product $product): ?float
    {
        $encoded = $this->specificationText($product->specifications ?? []);

        if ($encoded === '' || ! preg_match_all('/(\d+(?:\.\d+)?)\s*m(?:³|3)\s*\/\s*h/u', $encoded, $matches)) {
            return null;
        }

        return max(array_map(floatval(...), $matches[1]));
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return array{matches: Collection<int, Product>, unrated: Collection<int, Product>}
     */
    public function split(Collection $products, float $flow): array
    {
        $matches = collect();
        $unrated = collect();

        foreach ($products as $product) {
            $catalogFlow = $this->catalogFlow($product);

            if ($catalogFlow === null) {
                $unrated->push($product);

                continue;
            }

            if ($catalogFlow >= $flow) {
                $matches->push($product);
            }
        }

        return [
            'matches' => $matches->values(),
            'unrated' => $unrated->values(),
        ];
    }

    private function specificationText(mixed $value): string
    {
        if (is_string($value) || is_numeric($value)) {
            return $value.' ';
        }

        if (! is_array($value)) {
            return '';
        }

        return implode('', array_map($this->specificationText(...), $value));
    }

    private function toCubicMetersPerHour(float $flow, string $unit): float
    {
        return match ($unit) {
            'ls' => $flow * 3.6,
            'gpm' => $flow * 0.227124707,
            default => $flow,
        };
    }

    private function toMeters(float $head, string $unit): float
    {
        return match ($unit) {
            'bar' => $head * 10.1971621298,
            'ft' => $head * 0.3048,
            default => $head,
        };
    }
}
