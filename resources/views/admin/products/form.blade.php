@extends('layouts.admin')
@section('title', $product->exists ? 'Modifier un produit' : 'Créer un produit')
@section('content')
<div class="mb-8 flex flex-wrap items-center gap-4">
    <a href="{{ route('admin.products.index') }}" class="font-semibold text-sm text-quiet hover:text-rouge">← Retour aux produits</a>
    <h1 class="text-3xl font-extrabold">{{ $product->exists ? 'Modifier le produit' : 'Nouveau produit' }}</h1>
</div>
<div class="mb-7 rounded-xl border border-slate-200 bg-white p-5">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <strong class="text-ink">État de publication</strong>

        </div>
        <span class="text-sm font-bold {{ $product->is_active ? 'text-emerald-700' : 'text-rouge' }}">
            {{ $product->is_active ? 'Publiée' : 'Brouillon non visible' }}
        </span>
    </div>

    @if($product->exists)
        @if(!empty($publicationIssues))
            <div class="mt-4 border-t border-slate-200 pt-4">
                <strong class="text-sm text-rouge">À compléter avant publication — {{ count($publicationIssues) }} point(s)</strong>
                <ul class="mt-3 grid md:grid-cols-2 gap-x-6 gap-y-2 text-sm text-quiet list-disc pl-5">
                    @foreach($publicationIssues as $issue)<li>{{ $issue }}</li>@endforeach
                </ul>
            </div>
        @else
            <p class="text-sm text-emerald-700 font-bold mt-4">Fiche prête à publier.</p>
        @endif
    @endif
</div>
<form id="product-main-form" method="POST" enctype="multipart/form-data"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
      class="grid lg:grid-cols-[minmax(0,1fr)_340px] gap-6 items-start">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div class="space-y-6">
        <section class="rounded-xl bg-white border border-slate-200 p-6 grid sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <h2 class="text-xl font-extrabold mt-1">Présentation</h2>
            </div>
            <div class="sm:col-span-2">
                <label class="input-label" for="name">Nom du produit *</label>
                <input class="field" id="name" required name="name" maxlength="255" value="{{ old('name', $product->name) }}" placeholder="Ex. Caméra réseau Dahua 4 MP, optique fixe">
            </div>
            <div>
                <label class="input-label" for="sku">Référence fabricant (SKU) *</label>
                <input class="field" id="sku" required name="sku" maxlength="100" value="{{ old('sku', $product->sku) }}">
                @if($product->exists)<p class="text-xs text-quiet mt-2">L’adresse de la fiche reste inchangée.</p>@endif
            </div>
            <div>
                <label class="input-label" for="brand">Marque *</label>
                <input class="field" id="brand" required name="brand" maxlength="80" value="{{ old('brand', $product->brand) }}">
            </div>
            <div class="sm:col-span-2">
                <label class="input-label" for="category_id">Catégorie *</label>
                <select class="field" id="category_id" name="category_id" required>
                    <option value="">Sélectionner une catégorie</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->name }}{{ $category->is_active ? '' : ' (masquée)' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="input-label" for="short_description">Description courte (30 caractères minimum)</label>
                <textarea class="field" name="short_description" id="short_description" maxlength="500" rows="3"
                          placeholder="Présentez l’équipement et son usage en quelques mots.">{{ old('short_description', $product->short_description) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="input-label" for="description">Description détaillée (100 caractères minimum)</label>
                <textarea class="field" name="description" id="description" rows="9" maxlength="20000"
                          placeholder="Fonctions, usages et conseils d’installation…">{{ old('description', $product->description) }}</textarea>

            </div>
            <div class="sm:col-span-2">
                <label class="input-label" for="specifications_text">Caractéristiques techniques (2 minimum)</label>
                <textarea class="field" name="specifications_text" id="specifications_text" rows="7"
                          placeholder="Résolution : 4 MP&#10;Alimentation : PoE&#10;Indice de protection : IP67">{{ old('specifications_text', collect($product->specifications ?? [])->map(fn($v,$k) => $k.': '.$v)->implode("\n")) }}</textarea>
                <p class="text-xs text-quiet mt-2">Une caractéristique par ligne, au format « Propriété : Valeur ».</p>
            </div>
            <div class="sm:col-span-2">
                <label class="input-label" for="datasheet_url">Fiche constructeur officielle (URL)</label>
                <input class="field" id="datasheet_url" type="url" name="datasheet_url" maxlength="255" value="{{ old('datasheet_url', $product->datasheet_url) }}">
            </div>
        </section>
        <section class="rounded-xl bg-white border border-slate-200 p-6 space-y-5">
            <div><h2 class="text-xl font-extrabold mt-1">Référencement</h2></div>
            <div><label class="input-label" for="seo_title">Titre SEO (20 à 75 caractères)</label>
                <input class="field" id="seo_title" name="seo_title" maxlength="75" value="{{ old('seo_title', $product->seo_title) }}" placeholder="Caméra IP Dahua [référence] à Conakry | Vigilus"></div>
            <div><label class="input-label" for="seo_description">Méta-description (70 à 170 caractères)</label>
                <textarea class="field" id="seo_description" name="seo_description" rows="3" maxlength="170" placeholder="Décrire précisément ce produit et sa disponibilité en Guinée.">{{ old('seo_description', $product->seo_description) }}</textarea></div>

        </section>
    </div>
    <div class="space-y-6">
        <section class="rounded-xl bg-white border border-slate-200 p-6 space-y-5">
            <div><h2 class="text-xl font-extrabold mt-1">Prix et disponibilité</h2></div>
            <div>
                <label class="input-label" for="price_gnf">Prix de vente (GNF)</label>
                <input class="field" name="price_gnf" id="price_gnf" type="number" min="0" step="1" value="{{ old('price_gnf', $product->price_gnf) }}" placeholder="Ex. 1250000">
                <p class="text-xs text-quiet mt-2">Renseignez le prix public validé par l’équipe commerciale.</p>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="price_on_request" value="0">
                <input type="checkbox" name="price_on_request" value="1" @checked(old('price_on_request', $product->price_on_request))>
                Prix sur devis uniquement (pas de montant public)
            </label>
            <div><label class="input-label" for="stock_qty">Stock disponible *</label>
                <input class="field" name="stock_qty" id="stock_qty" type="number" min="0" step="1" required value="{{ old('stock_qty', $product->stock_qty) }}">
                <p class="text-xs text-quiet mt-2">Renseignez la quantité réellement disponible à la vente.</p></div>
        </section>
        <section class="rounded-xl bg-white border border-slate-200 p-6 space-y-4">
            <div><h2 class="text-xl font-extrabold mt-1">Photos</h2></div>
            <div><label class="input-label" for="image">Ajouter une photo (JPG / PNG / WebP ; 4 Mo max)</label>
                <input class="field" type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></div>
            <div><label class="input-label" for="image_alt">Description alternative de la nouvelle photo</label>
                <input class="field" name="image_alt" id="image_alt" maxlength="180" value="{{ old('image_alt') }}" placeholder="Caméra Dahua [référence], vue de face"></div>
            <p class="text-xs text-quiet">Ajoutez une photo autorisée correspondant à cette référence.</p>
        </section>
        <section class="rounded-xl bg-white border border-slate-200 p-6 space-y-4">
            <div><h2 class="text-xl font-extrabold mt-1">Publication</h2></div>
            <label class="flex gap-2 items-start text-sm"><input type="hidden" name="technical_verified" value="0">
                <input type="checkbox" name="technical_verified" value="1" class="mt-1" @checked(old('technical_verified', (bool)$product->technical_verified_at))>
                <span>Référence, caractéristiques et description FR vérifiées.</span></label>
            <label class="flex gap-2 items-start text-sm"><input type="hidden" name="commercial_verified" value="0">
                <input type="checkbox" name="commercial_verified" value="1" class="mt-1" @checked(old('commercial_verified', (bool)$product->commercial_verified_at))>
                <span>Prix public ou sur devis, disponibilité et conditions commerciales validés.</span></label>
            <label class="flex gap-2 items-start text-sm"><input type="hidden" name="media_rights_verified" value="0">
                <input type="checkbox" name="media_rights_verified" value="1" class="mt-1" @checked(old('media_rights_verified', (bool)$product->media_rights_verified_at))>
                <span>Droits et fidélité des photos et contenus confirmés.</span></label>
            <div class="border-t border-slate-200 pt-4 space-y-3">
                <label class="flex gap-2 items-center text-sm font-semibold"><input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Publier dans la boutique</label>
                <label class="flex gap-2 items-center text-sm"><input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Mettre en avant sur l'accueil</label>
            </div>
            <p class="text-xs text-quiet">Sans coche « Publier », la fiche reste un brouillon, même avec des validations renseignées.</p>
        </section>
        <button class="btn-red w-full" type="submit">{{ $product->exists ? 'Enregistrer la fiche' : 'Créer la fiche' }}</button>
    </div>
</form>
@if($product->exists && $product->images->isNotEmpty())
    <section class="mt-9 bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-xl font-extrabold">Photothèque de cette référence</h2>
        <p class="text-sm text-quiet mt-2">Définissez une description alternative et l'ordre d'apparition (valeur la plus basse en premier). La dernière image d'un produit publié ne peut pas être supprimée.</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">
            @foreach($product->images as $photo)
            <div class="border border-slate-200 rounded-lg p-3">
                <img src="{{ asset('storage/'.$photo->path) }}" alt="{{ $photo->alt }}" class="w-full aspect-[4/3] object-contain bg-cloud rounded-md">
                <form method="POST" action="{{ route('admin.products.images.update', [$product, $photo]) }}" class="mt-3 space-y-3">
                    @csrf @method('PATCH')
                    <div><label class="input-label" for="alt-{{ $photo->id }}">Texte alternatif</label>
                        <input class="field" id="alt-{{ $photo->id }}" name="alt" maxlength="180" required value="{{ $photo->alt }}"></div>
                    <div><label class="input-label" for="sort-{{ $photo->id }}">Ordre d'affichage</label>
                        <input class="field" id="sort-{{ $photo->id }}" name="sort_order" type="number" min="0" required value="{{ $photo->sort_order }}"></div>
                    <button type="submit" class="btn-light w-full">Mettre à jour</button>
                </form>
                <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $photo]) }}" class="mt-3">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-2 text-sm font-semibold text-rouge hover:underline">Supprimer cette image</button>
                </form>
            </div>
            @endforeach
        </div>
    </section>
@endif
@endsection
