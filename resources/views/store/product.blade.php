@extends('layouts.store')
@section('title', $product->seo_title ?: $product->name.' — '.$product->sku.' | Vigilus Guinée')
@section('meta_description', $product->seo_description ?: ($product->short_description ?: 'Référence '.$product->sku.' : caractéristiques, prix confirmé et demande de devis chez Vigilus Guinée.'))
@section('canonical', route('product.show', $product->slug))
@push('jsonld')
    @php
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'sku' => $product->sku,
            'brand' => ['@type' => 'Brand', 'name' => $product->brand],
            'description' => $product->short_description ?: $product->name,
            'url' => route('product.show', $product->slug),
        ];
        if ($product->images->isNotEmpty()) {
            $data['image'] = $product->images->map(fn ($image) => asset('storage/'.$image->path))->all();
        }
        if (! \App\Support\CatalogPreview::enabled() && $product->price_gnf !== null && ! $product->price_on_request) {
            $data['offers'] = [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => 'GNF',
                'price' => $product->price_gnf,
                'availability' => $product->stock_qty > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ];
        }
    @endphp
    <script type="application/ld+json">@json($data)</script>
    @php
        $breadcrumb = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Boutique', 'item' => route('catalog')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category->name, 'item' => route('category.show', $product->category)],
                ['@type' => 'ListItem', 'position' => 4, 'name' => $product->name, 'item' => route('product.show', $product->slug)],
            ],
        ];
    @endphp
    <script type="application/ld+json">@json($breadcrumb)</script>
@endpush
@section('content')
<div class="container-wide pt-7 lg:pt-10">
    <nav class="flex flex-wrap items-center gap-2 text-xs text-quiet mb-9" aria-label="Fil d'Ariane">
        <a class="hover:text-rouge" href="{{ route('home') }}">Accueil</a><x-store-icon name="chevron-right" :size="13"/>
        <a class="hover:text-rouge" href="{{ route('catalog') }}">Catalogue</a><x-store-icon name="chevron-right" :size="13"/>
        <a class="hover:text-rouge" href="{{ route('category.show', $product->category) }}">{{ $product->category->name }}</a>
        <x-store-icon name="chevron-right" :size="13"/><span class="font-semibold text-ink">{{ $product->sku }}</span>
    </nav>
    <div class="grid lg:grid-cols-[minmax(0,1.08fr)_minmax(0,.92fr)] gap-8 lg:gap-12 xl:gap-20 items-start">
        <section data-product-gallery aria-label="Photographies du produit">
            @php $mainImage = $product->images->first(); @endphp
            <div class="gallery-main">
                @if($mainImage)
                    <a id="gallery-large-link" href="{{ asset('storage/'.$mainImage->path) }}" target="_blank" rel="noopener noreferrer"
                       class="block w-full h-full" aria-label="Ouvrir cette photographie en grand">
                        <img id="gallery-main-image" src="{{ asset('storage/'.$mainImage->path) }}"
                             alt="{{ $mainImage->alt ?: $product->name }}" width="780" height="740">
                    </a>
                @else
                    <div class="h-full grid place-items-center text-center p-10">
                        <x-store-icon name="package" :size="48" class="mx-auto text-slate-300"/>
                        <p class="text-sm text-quiet mt-5">Photo non disponible</p>
                    </div>
                @endif
            </div>
            @if($product->images->count() > 1)
                <div class="grid grid-cols-5 sm:grid-cols-6 gap-2.5 mt-3" role="group" aria-label="Choisir une photographie">
                    @foreach($product->images->take(6) as $photo)
                        <button type="button" class="gallery-thumbnail"
                                data-gallery-thumb
                                data-gallery-src="{{ asset('storage/'.$photo->path) }}"
                                data-gallery-alt="{{ $photo->alt ?: $product->name }}"
                                aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                aria-label="Afficher la photographie {{ $loop->iteration }} sur {{ min(6, $product->images->count()) }}">
                            <img src="{{ asset('storage/'.$photo->path) }}"
                                 alt="" width="120" height="120" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                        </button>
                    @endforeach
                </div>
                <p class="sr-only" id="gallery-status" aria-live="polite">Photographie 1 affichée</p>
            @endif
        </section>
        <div class="lg:sticky lg:top-6">
            <p class="section-eyebrow">{{ $product->brand }} / {{ $product->category->name }}</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight tracking-tight mt-5">{{ $product->name }}</h1>
            <p class="text-sm text-quiet mt-3">Référence fabricant : <strong class="text-ink tracking-wide">{{ $product->sku }}</strong></p>
            <p class="text-base text-quiet leading-relaxed mt-6">{{ $product->short_description }}</p>
            <div class="product-purchase mt-8">
                <p class="text-xs text-quiet uppercase tracking-widest font-extrabold mb-3">Conditions commerciales</p>
                @if($product->display_price_gnf !== null)
                    <p class="product-purchase-price">{{ number_format($product->display_price_gnf, 0, ',', ' ') }} <span class="text-base font-bold">GNF</span></p>
                @else
                    <p class="product-purchase-price text-rouge">Prix sur devis</p>
                @endif
                <p class="product-card-availability mt-4 {{ $product->can_order ? 'is-available' : '' }}">
                    {{ $product->can_order ? 'Disponible sur demande' : 'Disponibilité à confirmer avec l’équipe' }}
                </p>
                <p class="text-xs text-quiet leading-relaxed mt-3">Disponibilité et livraison confirmées avec notre équipe.</p>
                @if($errors->has('cart'))<div class="bg-red-50 text-red-900 text-sm p-3 mt-5" role="alert">{{ $errors->first('cart') }}</div>@endif
                @if($product->can_order)
                    <form action="{{ route('cart.add') }}" method="POST" class="mt-7">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <label for="product-quantity" class="input-label">Quantité souhaitée</label>
                        <div class="flex flex-wrap gap-3 items-stretch">
                            <input id="product-quantity" name="quantity" type="number" min="1"
                                   max="{{ \App\Support\CatalogPreview::enabled() ? 100 : min(100, $product->stock_qty) }}" value="1" required inputmode="numeric"
                                   class="field w-24 text-center font-bold">
                            <button class="btn-red flex-1" type="submit"><x-store-icon name="cart" :size="19"/> Ajouter au panier</button>
                        </div>
                    </form>
                @endif
                <a class="{{ $product->can_order ? 'btn-light' : 'btn-red' }} w-full mt-3"
                   href="{{ route('quote.create', ['produit' => $product->id]) }}">
                    Demander un devis <x-store-icon name="arrow-right" :size="18"/>
                </a>
            </div>
            @if($product->datasheet_url)
                <a href="{{ $product->datasheet_url }}" target="_blank" rel="noopener noreferrer"
                   class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-rouge hover:underline">
                    <x-store-icon name="file" :size="19"/> Consulter la fiche technique fabricant <x-store-icon name="arrow-right" :size="15"/>
                </a>
            @endif
        </div>
    </div>
    <section class="grid lg:grid-cols-[1fr_1fr] gap-10 lg:gap-16 mt-16 pt-12 border-t border-slate-200">
        <div>
            <p class="section-eyebrow">Présentation</p>
            <h2 class="text-2xl font-extrabold mt-4 mb-5">Description du produit</h2>
            <div class="prose-vigilus">{{ $product->description ?: 'Contactez notre équipe pour les informations complémentaires.' }}</div>
        </div>
        <div>
            <p class="section-eyebrow">Données techniques</p>
            <h2 class="text-2xl font-extrabold mt-4 mb-5">Caractéristiques</h2>
            @if($product->specifications)
                <dl class="product-specs border-t border-slate-200">
                    @foreach($product->specifications as $label => $value)
                        <div class="grid grid-cols-[minmax(0,.9fr)_minmax(0,1.1fr)] gap-5 border-b border-slate-200 py-4 text-sm">
                            <dt>{{ $label }}</dt><dd class="font-semibold">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            @else
                <p class="text-sm text-quiet">Caractéristiques complémentaires sur demande.</p>
            @endif
        </div>
    </section>
    <section class="mt-16 bg-cloud border border-slate-200 p-7 sm:p-10 flex flex-wrap items-center gap-5 justify-between">
        <div><h2 class="font-extrabold text-xl">Besoin de plusieurs références ou de grandes quantités ?</h2>
            <p class="text-sm text-quiet mt-2">Précisez vos besoins pour obtenir une proposition commerciale adaptée.</p></div>
        <a href="{{ route('quote.create', ['produit' => $product->id]) }}" class="btn-red">Préparer un devis <x-store-icon name="arrow-right" :size="17"/></a>
    </section>
    @if($related->isNotEmpty())
        <section class="mt-20">
            <div class="flex flex-wrap items-end justify-between gap-5 mb-7">
                <div><p class="section-eyebrow">À découvrir aussi</p><h2 class="section-heading mt-4">Dans la même catégorie</h2></div>
                <a class="section-link" href="{{ route('category.show', $product->category) }}">Toutes les références <x-store-icon name="arrow-right" :size="17"/></a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
                @foreach($related as $suggestion)
                    @include('store.partials.product-card', ['product' => $suggestion])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
