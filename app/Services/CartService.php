<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    public function quantities(): array
    {
        $cart = session()->get('cart', []);
        if (! is_array($cart)) {
            return [];
        }

        return collect($cart)->filter(fn ($qty, $id) => ctype_digit((string) $id) && is_numeric($qty) && (int) $qty > 0)
            ->map(fn ($qty) => min((int) $qty, 100))->all();
    }

    public function lines(): Collection
    {
        $cart = $this->quantities();
        if (! $cart) {
            return collect();
        }

        $products = Product::published()->with(['images', 'category'])->whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)->map(function ($quantity, $id) use ($products) {
            $product = $products->get((int) $id);
            $valid = $product !== null
                && $product->can_order
                && $quantity > 0
                && $quantity <= 100
                && $product->stock_qty >= $quantity;

            return [
                'id' => (int) $id,
                'product' => $product,
                'quantity' => $quantity,
                'valid' => $valid,
                'subtotal' => $valid ? $product->price_gnf * $quantity : null,
            ];
        })->values();
    }

    public function subtotal(): int
    {
        return (int) $this->lines()->filter(fn ($line) => $line['valid'])->sum('subtotal');
    }
}
