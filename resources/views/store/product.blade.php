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
        if ($product->price_gnf !== null) {
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
<div class="container-wide pt-9">
    <nav class="text-xs text-quiet mb-8" aria-label="Fil d'Ariane">
        <a href="{{ route('home') }}" class="hover:text-rouge">Accueil</a> /
        <a href="{{ route('catalog') }}" class="hover:text-rouge">Boutique</a> /
        <a href="{{ route('category.show', $product->category) }}" class="hover:text-rouge">{{ $product->category->name }}</a> /
        <span>{{ $product->sku }}</span>
    </nav>
    <div class="grid lg:grid-cols-[minmax(0,1.06fr)_minmax(0,.94fr)] gap-10 xl:gap-16">
        <div>
            <div class="aspect-square bg-cloud border border-slate-200 rounded-2xl overflow-hidden">
                @if($product->images->first())
                    <img class="product-photo" src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ $product->images->first()->alt ?: $product->name }}" width="760" height="760">
                @else
                    <div class="h-full grid place-items-center text-quiet text-sm text-center px-10">Photo officielle à renseigner</div>
                @endif
            </div>
            @if($product->images->count() > 1)
                <div class="grid grid-cols-5 gap-3 mt-3">
                    @foreach($product->images->slice(1, 5) as $photo)
                        <a href="{{ asset('storage/'.$photo->path) }}" target="_blank" rel="noopener" class="aspect-square bg-cloud rounded-lg border border-slate-200 overflow-hidden">
                            <img src="{{ asset('storage/'.$photo->path) }}" alt="{{ $photo->alt ?: $product->name }}" class="product-photo" loading="lazy">
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <div>
            <p class="kicker">{{ $product->brand }} · {{ $product->category->name }}</p>
            <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">{{ $product->name }}</h1>
            <p class="mt-3 text-sm font-medium text-quiet">Référence constructeur : <span class="font-bold text-ink">{{ $product->sku }}</span></p>
            <p class="text-quiet text-base leading-relaxed mt-6">{{ $product->short_description }}</p>
            <div class="mt-8 border-y border-slate-200 py-7">
                @if($product->price_gnf !== null)
                    <div class="text-4xl font-extrabold">{{ number_format($product->price_gnf, 0, ',', ' ') }} <span class="text-lg">GNF</span></div>
                @else
                    <div class="text-2xl font-extrabold text-rouge">{{ $product->price_on_request ? 'Prix sur devis' : 'Tarif à confirmer' }}</div>
                @endif
                <p class="mt-2 text-sm {{ $product->stock_qty > 0 ? 'text-emerald-700' : 'text-quiet' }}">
                    {{ $product->stock_qty > 0 ? 'En stock selon disponibilité confirmée' : 'Disponibilité à confirmer' }}
                </p>
                <p class="text-xs text-quiet mt-1">Une offre commerciale sera confirmée avant toute transaction.</p>
            </div>
            @if($errors->has('cart'))<div role="alert" class="mt-5 p-3 bg-red-50 text-red-900 rounded-lg">{{ $errors->first('cart') }}</div>@endif
            <div class="mt-7 flex flex-wrap gap-3">
                @if($product->can_order)
                    <form method="POST" action="{{ route('cart.add') }}" class="inline-flex">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn-red" type="submit">Ajouter au panier</button>
                    </form>
                @endif
                <a href="{{ route('quote.create', ['produit' => $product->id]) }}" class="btn-light">Demander un devis</a>
                <a href="{{ route('quote.create', ['produit' => $product->id]) }}" class="btn-light">Demander une installation</a>
            </div>
            <p class="text-sm text-quiet mt-5">Vous êtes installateur ou revendeur ? Précisez les quantités pour recevoir une proposition adaptée.</p>
            <p class="text-xs text-quiet mt-3">La commande est une demande sans paiement en ligne. Notre équipe confirme les conditions commerciales avant tout règlement hors site.</p>
            @if($product->datasheet_url)
                <a href="{{ $product->datasheet_url }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-block text-sm font-bold text-rouge hover:underline">Consulter la fiche technique constructeur ↗</a>
            @endif
        </div>
    </div>
    <div class="grid lg:grid-cols-2 gap-10 mt-16">
        <section><h2 class="text-2xl font-extrabold mb-5">Description</h2>
            <div class="prose-vigilus">{{ $product->description ?: 'La description détaillée de cet équipement sera prochainement disponible.' }}</div>
        </section>
        <section><h2 class="text-2xl font-extrabold mb-5">Caractéristiques techniques</h2>
            @if($product->specifications)
                <dl class="divide-y divide-slate-200 border-y border-slate-200">
                    @foreach($product->specifications as $label => $value)
                        <div class="grid grid-cols-2 gap-3 py-3 text-sm"><dt class="font-semibold text-quiet">{{ $label }}</dt><dd class="text-ink">{{ $value }}</dd></div>
                    @endforeach
                </dl>
            @else
                <p class="text-quiet text-sm">Les caractéristiques seront publiées après vérification des fiches constructeur.</p>
            @endif
        </section>
    </div>
    @if($related->isNotEmpty())
        <section class="mt-20"><div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-extrabold">Dans la même catégorie</h2>
            <a class="text-sm text-rouge font-bold" href="{{ route('category.show', $product->category) }}">Tout voir →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($related as $product) @include('store.partials.product-card', ['product' => $product]) @endforeach
        </div></section>
    @endif
</div>
@endsection
