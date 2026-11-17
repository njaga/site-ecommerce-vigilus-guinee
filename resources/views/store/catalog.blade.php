@extends('layouts.store')
@section('title', $category?->seo_title ?: (($category ? $category->name : 'Catalogue Dahua').' à Conakry | Vigilus Guinée'))
@section('meta_description', $category?->seo_description ?: 'Explorez les caméras, enregistreurs, kits, contrôle d’accès et accessoires chez Vigilus Guinée. Recherche par modèle, prix vérifiés et devis à Conakry.')
@section('canonical', ($category ? route('category.show', $category) : route('catalog')).(request()->integer('page') > 1 ? '?page='.request()->integer('page') : ''))
@section('robots', (request()->hasAny(['q', 'tri', 'en_stock', 'categorie']) || ($category && $products->total() === 0)) ? 'noindex,follow' : 'index,follow')
@if($category)
@push('jsonld')
    @php
        $breadcrumbs = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Boutique', 'item' => route('catalog')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $category->name, 'item' => route('category.show', $category)],
            ],
        ];
    @endphp
    <script type="application/ld+json">@json($breadcrumbs)</script>
@endpush
@endif
@section('content')
<section class="catalog-heading">
    <div class="container-wide py-10 lg:py-14">
        <nav class="text-xs text-quiet flex items-center gap-2 mb-6" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}" class="hover:text-rouge">Accueil</a>
            <x-store-icon name="chevron-right" :size="13"/>
            @if($category)<a href="{{ route('catalog') }}" class="hover:text-rouge">Catalogue</a>
                <x-store-icon name="chevron-right" :size="13"/><span>{{ $category->name }}</span>
            @else<span>Catalogue Dahua</span>@endif
        </nav>

        <div class="flex flex-wrap justify-between items-end gap-5 mt-4">
            <div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">{{ $category ? $category->name : 'Nos équipements' }}</h1>
                <p class="text-quiet max-w-3xl mt-4 leading-relaxed">{{ $category?->description ?: 'Les références Dahua pour votre projet de sécurité.' }}</p>
            </div>
            <a href="{{ route('quote.create') }}" class="btn-light text-sm">Besoin d’un conseil ? <x-store-icon name="arrow-right" :size="17"/></a>
        </div>
    </div>
</section>
<div class="container-wide mt-8 lg:mt-12 grid lg:grid-cols-[248px_minmax(0,1fr)] gap-6 lg:gap-9">
    <aside class="catalog-sidebar self-start">
        <details class="catalog-filters" id="catalog-filters" @if(request()->hasAny(['q','tri','en_stock'])) open @endif>
            <summary class="catalog-filters-summary lg:hidden">
                <span class="flex items-center gap-2"><x-store-icon name="filter" :size="18"/> Affiner la sélection</span>
                <x-store-icon name="chevron-down" :size="19"/>
            </summary>
            <form class="catalog-filters-body" action="{{ $category ? route('category.show', $category) : route('catalog') }}" method="GET">
                <div class="hidden lg:block text-sm font-extrabold pt-1">Affiner la sélection</div>
                <div><label class="input-label" for="product-query">Rechercher</label>
                    <input id="product-query" name="q" type="search" class="field text-sm" value="{{ $search }}" placeholder="Caméra, NVR, modèle…"></div>
                <div><label class="input-label" for="sort">Trier par</label>
                    <select name="tri" id="sort" class="field text-sm">
                        <option value="">Les plus récents</option>
                        <option value="nom" @selected(request('tri') === 'nom')>Nom A–Z</option>
                        <option value="prix-croissant" @selected(request('tri') === 'prix-croissant')>Prix croissant</option>
                        <option value="prix-decroissant" @selected(request('tri') === 'prix-decroissant')>Prix décroissant</option>
                    </select></div>
                <label class="catalog-filter-label"><input class="accent-rouge" type="checkbox" name="en_stock" value="1" @checked(request('en_stock'))> En stock uniquement</label>
                <button class="btn-red w-full text-sm" type="submit">Afficher les résultats</button>
                @if(request()->hasAny(['q','tri','en_stock']))
                    <a class="text-rouge font-bold text-xs text-center" href="{{ $category ? route('category.show', $category) : route('catalog') }}">Effacer les filtres</a>
                @endif
            </form>
        </details>
        <nav class="catalog-category-links" aria-label="Familles d'équipements">
            <h2 id="catalog-categories-title" class="text-xs uppercase tracking-widest font-extrabold text-quiet mb-4">Catégories</h2>
            <div class="catalog-category-scroll" tabindex="0" role="region" aria-labelledby="catalog-categories-title">
            <a href="{{ route('catalog') }}" @class(['catalog-category-link', 'is-current' => ! $category]) @if(! $category) aria-current="page" @endif>
                <span class="catalog-category-all"><x-store-icon name="package" :size="20"/></span>
                <span>Tout le catalogue</span><x-store-icon name="chevron-right" :size="14"/></a>
            @foreach($categories as $item)
                @php $categoryVisual = app(\App\Support\CategoryVisuals::class)->for($item); @endphp
                <a href="{{ route('category.show', $item) }}" @class(['catalog-category-link', 'is-current' => $category?->id === $item->id]) @if($category?->id === $item->id) aria-current="page" @endif>
                    <img class="catalog-category-thumbnail" src="{{ $categoryVisual['url'] }}" alt="" width="40" height="40" loading="lazy">
                    <span>{{ $item->name }}</span><x-store-icon name="chevron-right" :size="14"/>
                </a>
            @endforeach
            </div>
        </nav>
    </aside>
    <div class="min-w-0">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
            <p class="text-sm text-quiet"><strong class="text-ink text-base">{{ $products->total() }}</strong> {{ $products->total() > 1 ? 'équipements' : 'équipement' }}</p>
            @if($search)
                <p class="text-xs text-quiet">Recherche : <span class="text-ink font-bold">« {{ $search }} »</span></p>
            @endif
        </div>
        @if($products->count())
            <div class="grid grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-5">
                @foreach($products as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>
            {{ $products->onEachSide(1)->links('pagination.vigilus') }}
        @else
            <div class="bg-cloud border border-slate-200 py-16 px-8 text-center">
                <x-store-icon name="search" :size="35" class="text-slate-400 mx-auto"/>
                <h2 class="text-xl font-extrabold mt-5">Aucune référence pour cette sélection</h2>
                <p class="text-sm text-quiet mt-3 max-w-md mx-auto">Essayez une autre recherche ou demandez directement les modèles et quantités souhaités à notre équipe.</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('catalog') }}" class="btn-light text-sm">Tout le catalogue</a>
                    <a href="{{ route('quote.create') }}" class="btn-red text-sm">Faire une demande</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
