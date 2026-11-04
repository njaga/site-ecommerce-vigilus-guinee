@extends('layouts.admin')
@section('title', $product->exists ? 'Modifier un produit' : 'Créer un produit')
@section('content')
<div class="flex flex-wrap gap-4 items-center mb-7">
    <a href="{{ route('admin.products.index') }}" class="font-semibold text-sm text-quiet hover:text-rouge">← Produits</a>
    <h1 class="text-3xl font-extrabold">{{ $product->exists ? 'Modifier la référence' : 'Nouvelle référence' }}</h1>
</div>
<form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="grid lg:grid-cols-[1fr_320px] gap-6">
    @csrf @if($product->exists) @method('PUT') @endif
    <div class="bg-white p-6 rounded-xl border border-slate-200 grid sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2"><label for="name" class="input-label">Nom commercial *</label><input id="name" name="name" required class="field" value="{{ old('name', $product->name) }}"></div>
        <div><label for="sku" class="input-label">Référence constructeur (SKU) *</label><input id="sku" name="sku" required class="field" value="{{ old('sku', $product->sku) }}"></div>
        <div><label for="brand" class="input-label">Marque *</label><input id="brand" name="brand" required class="field" value="{{ old('brand', $product->brand) }}"></div>
        <div class="sm:col-span-2"><label for="category_id" class="input-label">Catégorie *</label>
            <select class="field" name="category_id" id="category_id" required>
                <option value="">Sélectionnez</option>
                @foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach
            </select>
        </div>
        <div class="sm:col-span-2"><label for="short_description" class="input-label">Description courte</label><textarea class="field" id="short_description" rows="3" maxlength="500" name="short_description">{{ old('short_description', $product->short_description) }}</textarea></div>
        <div class="sm:col-span-2"><label for="description" class="input-label">Description détaillée (originale)</label><textarea id="description" rows="7" name="description" class="field">{{ old('description', $product->description) }}</textarea></div>
        <div class="sm:col-span-2">
            <label for="specifications_text" class="input-label">Caractéristiques — une ligne « Nom : Valeur »</label>
            <textarea id="specifications_text" name="specifications_text" rows="8" class="field" placeholder="Résolution : 4 MP&#10;Alimentation : PoE&#10;Protection : IP67">{{ old('specifications_text', collect($product->specifications ?? [])->map(fn($v,$k) => $k.': '.$v)->implode("\n")) }}</textarea>
            <p class="text-xs text-quiet mt-2">Reprenez uniquement les données techniques confirmées sur la fiche officielle.</p>
        </div>
        <div class="sm:col-span-2"><label for="datasheet_url" class="input-label">URL de la fiche technique officielle (https)</label><input id="datasheet_url" name="datasheet_url" type="url" class="field" value="{{ old('datasheet_url', $product->datasheet_url) }}"></div>
        <div class="sm:col-span-2"><h2 class="font-bold text-lg mt-2">Référencement</h2></div>
        <div class="sm:col-span-2"><label for="seo_title" class="input-label">Titre SEO</label><input id="seo_title" name="seo_title" class="field" value="{{ old('seo_title', $product->seo_title) }}"></div>
        <div class="sm:col-span-2"><label for="seo_description" class="input-label">Description SEO</label><textarea id="seo_description" name="seo_description" rows="3" class="field" maxlength="320">{{ old('seo_description', $product->seo_description) }}</textarea></div>
    </div>
    <div class="space-y-5">
        <div class="bg-white p-6 rounded-xl border border-slate-200 space-y-5">
            <h2 class="font-extrabold text-lg">Prix & disponibilité</h2>
            <div><label for="price_gnf" class="input-label">Prix public en GNF</label><input class="field" name="price_gnf" id="price_gnf" type="number" min="0" step="1" placeholder="Vide = sur devis" value="{{ old('price_gnf', $product->price_gnf) }}">
                <p class="text-xs text-quiet mt-2">N'affichez pas un montant non validé. Laissez vide si le prix est sur devis.</p>
            </div>
            <div><label for="stock_qty" class="input-label">Quantité en stock *</label><input class="field" name="stock_qty" type="number" min="0" required id="stock_qty" value="{{ old('stock_qty', $product->stock_qty) }}"></div>
            <label class="flex gap-2 text-sm items-center"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Publier dans la boutique</label>
            <label class="flex gap-2 text-sm items-center"><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Mettre en avant sur l'accueil</label>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <h2 class="font-extrabold text-lg mb-4">Visuels officiels</h2>
            <label for="image" class="input-label">Ajouter une image (JPG, PNG ou WebP ; 4 Mo max)</label>
            <input type="file" accept="image/png,image/jpeg,image/webp" name="image" id="image" class="field">
            @if($product->exists && $product->images->count())
                <div class="grid grid-cols-3 gap-2 mt-4">
                    @foreach($product->images as $image)
                    <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt }}" class="aspect-square object-contain border border-slate-100 rounded-lg" loading="lazy">
                    @endforeach
                </div>
            @endif
        </div>
        <button class="btn-red w-full" type="submit">{{ $product->exists ? 'Enregistrer les modifications' : 'Créer le produit' }}</button>
    </div>
</form>
@endsection
