<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestionController
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
        ]);
        $term = trim((string) ($data['q'] ?? ''));

        if (mb_strlen($term) < 2) {
            return $this->respond($term, [], []);
        }

        // The published scope expands to drafts only in explicitly gated
        // localhost preview. Confidential import staging rows are never queried.
        $products = Product::published()
            ->with(['category', 'images'])
            ->where(function ($query) use ($term): void {
                $query->where('name', 'like', '%'.$term.'%')
                    ->orWhere('sku', 'like', '%'.$term.'%')
                    ->orWhere('short_description', 'like', '%'.$term.'%');
            })
            ->orderByRaw('CASE WHEN sku = ? THEN 0 ELSE 1 END', [$term])
            ->orderBy('name')
            ->limit(6)
            ->get()
            ->map(fn (Product $product): array => [
                'name' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category?->name,
                'url' => route('product.show', $product->slug),
                'image' => $product->images->first()
                    ? asset('storage/'.$product->images->first()->path)
                    : null,
                'price_gnf' => $product->display_price_gnf,
                'price_on_request' => $product->display_price_gnf === null,
            ])->all();

        $categories = Category::storefront()
            ->where('name', 'like', '%'.$term.'%')
            ->orderBy('sort_order')
            ->limit(3)
            ->get()
            ->map(fn (Category $category): array => [
                'name' => $category->name,
                'url' => route('category.show', $category),
            ])->all();

        return $this->respond($term, $products, $categories);
    }

    private function respond(string $term, array $products, array $categories): JsonResponse
    {
        return response()->json([
            'query' => $term,
            'products' => $products,
            'categories' => $categories,
        ])->header('Cache-Control', 'no-store, private')
          ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
