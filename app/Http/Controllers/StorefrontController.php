<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController
{
    public function home(): View
    {
        return view('store.home', [
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'featured' => Product::published()->with(['category', 'images'])
                ->where('is_featured', true)->latest()->limit(8)->get(),
        ]);
    }

    public function catalog(Request $request, ?Category $category = null): View
    {
        abort_if($category && ! $category->is_active, 404);

        $query = Product::published()->with(['category', 'images']);
        if ($category) {
            $query->where('category_id', $category->id);
        } elseif ($request->filled('categorie')) {
            $slug = substr((string) $request->query('categorie'), 0, 100);
            $selected = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
            $query->where('category_id', $selected->id);
        }

        $search = trim(substr((string) $request->query('q', ''), 0, 100));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('short_description', 'like', '%'.$search.'%');
            });
        }

        if ($request->boolean('en_stock')) {
            $query->where('stock_qty', '>', 0);
        }

        switch ($request->query('tri')) {
            case 'prix-croissant':
                $query->orderByRaw('price_gnf IS NULL')->orderBy('price_gnf');
                break;
            case 'prix-decroissant':
                $query->orderByRaw('price_gnf IS NULL')->orderByDesc('price_gnf');
                break;
            case 'nom':
                $query->orderBy('name');
                break;
            default:
                $query->latest();
        }

        return view('store.catalog', [
            'category' => $category,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'products' => $query->paginate(12)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function product(string $slug): View
    {
        $product = Product::published()->with(['category', 'images'])->where('slug', $slug)->firstOrFail();

        return view('store.product', [
            'product' => $product,
            'related' => Product::published()->with('images')
                ->where('category_id', $product->category_id)->whereKeyNot($product->id)
                ->limit(4)->get(),
        ]);
    }
}
