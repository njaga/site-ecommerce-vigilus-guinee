<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductPublicationGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('statut', '');
        $search = trim(mb_substr((string) $request->query('q', ''), 0, 100));

        $query = Product::with(['category'])->latest();

        if ($status === 'draft') {
            $query->where('is_active', false);
        } elseif ($status === 'published') {
            $query->where('is_active', true);
        } elseif ($status === 'missing-price') {
            $query->whereNull('price_gnf')->where('price_on_request', false);
        }

        if ($search !== '') {
            $query->where(fn ($q) => $q->where('sku', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%'));
        }

        return view('admin.products.index', [
            'products' => $query->paginate(25)->withQueryString(),
            'selectedStatus' => $status,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['brand' => 'Dahua', 'stock_qty' => 0]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, ProductPublicationGuard $guard): RedirectResponse
    {
        [$data, $image, $alt] = $this->prepare($request);
        $guard->check($data, null, $image !== null);

        $data['slug'] = Str::slug($data['name'].'-'.$data['sku']);
        if ($data['slug'] === '') {
            throw ValidationException::withMessages(['sku' => 'Référence invalide pour générer une URL.']);
        }
        if (Product::where('slug', $data['slug'])->exists()) {
            throw ValidationException::withMessages(['sku' => 'Une URL produit identique existe déjà.']);
        }

        $product = DB::transaction(function () use ($data, $image, $alt) {
            $product = Product::create($this->attributes($data));
            $this->saveImage($product, $image, $alt);
            return $product;
        });

        return redirect()->route('admin.products.index')->with('success', $product->is_active
            ? 'Produit publié après validation technique et commerciale.'
            : 'Brouillon produit créé.');
    }

    public function edit(Product $product, ProductPublicationGuard $guard): View
    {
        return view('admin.products.form', [
            'product' => $product->load('images'),
            'categories' => Category::orderBy('name')->get(),
            'publicationIssues' => $guard->issuesFor($product),
        ]);
    }

    public function update(Request $request, Product $product, ProductPublicationGuard $guard): RedirectResponse
    {
        [$data, $image, $alt] = $this->prepare($request, $product);
        $guard->check($data, $product, $image !== null);

        DB::transaction(function () use ($product, $data, $image, $alt) {
            $product->update($this->attributes($data, $product));
            $this->saveImage($product, $image, $alt);
        });

        return redirect()->route('admin.products.index')->with('success',
            $product->is_active ? 'Fiche publiée / mise à jour.' : 'Brouillon mis à jour.');
    }

    public function archive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false, 'is_featured' => false]);

        return back()->with('success', 'Produit masqué sans suppression de ses données ni de son URL historique.');
    }

    private function prepare(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand' => ['required', 'string', 'max:80'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'specifications_text' => ['nullable', 'string', 'max:5000'],
            'price_gnf' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'price_on_request' => ['sometimes', 'boolean'],
            'stock_qty' => ['required', 'integer', 'min:0', 'max:1000000'],
            'datasheet_url' => ['nullable', 'url:http,https', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'technical_verified' => ['sometimes', 'boolean'],
            'commercial_verified' => ['sometimes', 'boolean'],
            'media_rights_verified' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:180'],
        ]);

        $image = $data['image'] ?? null;
        $alt = trim((string) ($data['image_alt'] ?? ''));
        unset($data['image'], $data['image_alt']);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $data['is_active'] && $request->boolean('is_featured');
        $data['price_on_request'] = $request->boolean('price_on_request');
        $data['technical_verified'] = $request->boolean('technical_verified');
        $data['commercial_verified'] = $request->boolean('commercial_verified');
        $data['media_rights_verified'] = $request->boolean('media_rights_verified');
        $data['specifications'] = $this->specifications($request->input('specifications_text'));

        return [$data, $image, $alt];
    }

    private function attributes(array $data, ?Product $current = null): array
    {
        $active = $data['is_active'];
        $data['technical_verified_at'] = $data['technical_verified'] ? now() : null;
        $data['commercial_verified_at'] = $data['commercial_verified'] ? now() : null;
        $data['media_rights_verified_at'] = $data['media_rights_verified'] ? now() : null;
        $data['published_at'] = $active ? ($current?->published_at ?? now()) : $current?->published_at;

        unset($data['technical_verified'], $data['commercial_verified'], $data['media_rights_verified']);
        return $data;
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

    private function saveImage(Product $product, mixed $image, string $alt): void
    {
        if (! $image) {
            return;
        }
        $path = $image->store('products', 'public');
        if ($path === false) {
            throw ValidationException::withMessages(['image' => 'Impossible de sauvegarder le visuel.']);
        }

        $product->images()->create([
            'path' => $path,
            'alt' => $alt !== '' ? $alt : $product->name.' — '.$product->sku,
            'sort_order' => ((int) $product->images()->max('sort_order')) + 1,
        ]);
    }
}
