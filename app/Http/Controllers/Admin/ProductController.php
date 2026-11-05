<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::with('category')->latest()->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['brand' => 'Dahua', 'stock_qty' => 0]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $upload = $data['image'] ?? null;
        unset($data['image']);

        $data['slug'] = Str::slug($data['name'].'-'.$data['sku']);
        $data['specifications'] = $this->specifications($request->input('specifications_text'));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $product = Product::create($data);
        $this->saveImage($product, $upload);

        return redirect()->route('admin.products.index')->with('success', 'Produit créé.');
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $upload = $data['image'] ?? null;
        unset($data['image']);

        // Ne jamais changer le slug automatiquement après une publication.
        $data['specifications'] = $this->specifications($request->input('specifications_text'));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $product->update($data);
        $this->saveImage($product, $upload);

        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour.');
    }

    public function archive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return back()->with('success', 'Produit masqué de la boutique sans suppression de ses données.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand' => ['required', 'string', 'max:80'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'specifications_text' => ['nullable', 'string', 'max:5000'],
            'price_gnf' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'stock_qty' => ['required', 'integer', 'min:0', 'max:1000000'],
            'datasheet_url' => ['nullable', 'url:http,https', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function specifications(?string $raw): array
    {
        $result = [];
        foreach (preg_split('/\r?\n/', (string) $raw) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode(':', $line, 2));
            if ($key !== '' && $value !== '') {
                $result[mb_substr($key, 0, 100)] = mb_substr($value, 0, 500);
            }
        }
        return $result;
    }

    private function saveImage(Product $product, mixed $image): void
    {
        if ($image) {
            $path = $image->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'alt' => $product->name.' — '.$product->sku,
                'sort_order' => $product->images()->count(),
            ]);
        }
    }
}
