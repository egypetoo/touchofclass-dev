<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class SessionCart
{
    private const SESSION_KEY = 'cart.items';

    /**
     * @return array<int, int>
     */
    public function items(): array
    {
        $items = [];

        foreach (session(self::SESSION_KEY, []) as $id => $qty) {
            $items[(int) $id] = (int) $qty;
        }

        return $items;
    }

    public function add(int $productId, int $qty): void
    {
        $items = $this->items();
        $items[$productId] = min(99, ($items[$productId] ?? 0) + $qty);

        session([self::SESSION_KEY => $items]);
    }

    public function update(int $productId, int $qty): void
    {
        $items = $this->items();

        if (! isset($items[$productId])) {
            return;
        }

        $items[$productId] = min(99, max(1, $qty));

        session([self::SESSION_KEY => $items]);
    }

    public function remove(int $productId): void
    {
        $items = $this->items();
        unset($items[$productId]);

        session([self::SESSION_KEY => $items]);
    }

    /**
     * @param  array<int, int>  $items
     */
    public function replace(array $items): void
    {
        session([self::SESSION_KEY => $items]);
    }

    public function count(): int
    {
        return (int) array_sum($this->items());
    }

    /**
     * @return Collection<int, array{product: Product, qty: int}>
     */
    public function lines(): Collection
    {
        $items = $this->items();

        if ($items === []) {
            return collect();
        }

        $products = Product::query()
            ->active()
            ->whereIn('id', array_keys($items))
            ->with('media')
            ->get()
            ->keyBy('id');

        $kept = [];

        foreach ($items as $id => $qty) {
            if ($products->has($id)) {
                $kept[$id] = $qty;
            }
        }

        if ($kept !== $items) {
            session([self::SESSION_KEY => $kept]);
        }

        return collect($kept)->map(fn (int $qty, int $id): array => [
            'product' => $products->get($id),
            'qty' => $qty,
        ])->values();
    }
}
