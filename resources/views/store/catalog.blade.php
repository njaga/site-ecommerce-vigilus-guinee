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
<section class="bg-cloud border-b border-slate-200">
    <div class="container-wide py-10 lg:py-14">
        <nav class="text-xs text-quiet flex items-center gap-2 mb-6" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}" class="hover:text-rouge">Accueil</a>
            <x-store-icon name="chevron-right" :size="13"/>
            @if($category)<a href="{{ route('catalog') }}" class="hover:text-rouge">Catalogue</a>
                <x-store-icon name="chevron-right" :size="13"/><span>{{ $category->name }}</span>
            @else<span>Catalogue Dahua</span>@endif
        </nav>
        <p class="section-eyebrow">Catalogue professionnel</p>
        <div class="flex flex-wrap justify-between items-end gap-5 mt-4">
            <div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">{{ $category ? $category->name : 'Équipements de sécurité électronique' }}</h1>
                <p class="text-quiet max-w-3xl mt-4 leading-relaxed">{{ $category?->description ?: 'Explorez les caméras Dahua, enregistreurs, équipements de contrôle d’accès et accessoires. Recherchez une référence ou précisez votre besoin pour obtenir un devis.' }}</p>
            </div>
            <a href="{{ route('quote.create') }}" class="btn-light text-sm">Besoin d’une référence ? <x-store-icon name="arrow-right" :size="17"/></a>
        </div>
    </div>
</section>
<div class="container-wide mt-8 lg:mt-12 grid lg:grid-cols-[248px_minmax(0,1fr)] gap-6 lg:gap-9">
    <aside class="lg:sticky lg:top-5 self-start">
        <details class="catalog-filters" id="catalog-filters" @if(request()->hasAny(['q','tri','en_stock'])) open @endif>
            <summary class="catalog-filters-summary lg:hidden">
                <span class="flex items-center gap-2"><x-store-icon name="filter" :size="18"/> Affiner la sélection</span>
                <x-store-icon name="chevron-down" :size="19"/>
            </summary>
            <form class="catalog-filters-body" action="{{ $category ? route('category.show', $category) : route('catalog') }}" method="GET">
                <div class="hidden lg:block text-sm font-extrabold pt-1">Affiner la sélection</div>
                <div><label class="input-label" for="product-query">Référence ou désignation</label>
                    <input id="product-query" name="q" type="search" class="field text-sm" value="{{ $search }}" placeholder="Caméra, NVR, modèle…"></div>
                <div><label class="input-label" for="sort">Trier les résultats</label>
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
        <nav class="mt-7 hidden lg:block border-t border-slate-200 pt-6" aria-label="Familles d'équipements">
            <h2 class="text-xs uppercase tracking-widest font-extrabold text-quiet mb-4">Catégories</h2>
            <a href="{{ route('catalog') }}" class="flex items-center justify-between py-2.5 border-b border-slate-100 text-sm {{ ! $category ? 'font-extrabold text-rouge' : 'hover:text-rouge' }}">
                Tout le catalogue <x-store-icon name="chevron-right" :size="14"/></a>
            @foreach($categories as $item)
                <a href="{{ route('category.show', $item) }}" class="flex items-center justify-between py-2.5 border-b border-slate-100 text-sm gap-3 {{ $category?->id === $item->id ? 'text-rouge font-extrabold' : 'hover:text-rouge' }}">
                    {{ $item->name }}<x-store-icon name="chevron-right" :size="14" class="shrink-0"/>
                </a>
            @endforeach
        </nav>
    </aside>
    <div class="min-w-0">
        <div class="flex flex-wrap justify-between items-center gap-3 pb-5 border-b border-slate-200 mb-5">
            <p class="text-sm text-quiet"><strong class="text-ink text-base">{{ $products->total() }}</strong> référence(s) trouvée(s)</p>
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
            <div class="mt-10">{{ $products->links() }}</div>
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
<div class="container-wide pt-12 lg:pt-16">
    <div class="bg-[#12394f] text-white p-7 sm:p-9 flex flex-wrap gap-5 items-center justify-between">
        <div><h2 class="font-extrabold text-xl">Vous avez une liste de références à fournir ?</h2>
            <p class="text-sm text-slate-300 mt-2">Transmettez les modèles, quantités et la zone de livraison souhaitée pour préparer un devis.</p></div>
        <a href="{{ route('quote.create') }}" class="btn-red text-sm">Demander un devis <x-store-icon name="arrow-right" :size="18"/></a>
    </div>
</div>
@endsection
