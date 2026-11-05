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
<div class="container-wide pt-10">
    <nav class="text-xs text-quiet mb-5" aria-label="Fil d'Ariane">
        <a href="{{ route('home') }}" class="hover:text-rouge">Accueil</a> <span aria-hidden="true">/</span>
        <span>{{ $category?->name ?: 'Boutique' }}</span>
    </nav>
    <p class="kicker">Boutique Vigilus Guinée</p>
    <h1 class="text-4xl sm:text-5xl font-extrabold mt-3">{{ $category ? $category->name : 'Équipements de sécurité électronique' }}</h1>
    <p class="text-quiet max-w-2xl mt-4 leading-relaxed">{{ $category?->description ?: 'Trouvez la référence adaptée à votre installation : caméras, enregistreurs, contrôle d’accès, alarmes, réseaux et accessoires.' }}</p>
</div>
@if($category && str_contains(mb_strtolower($category->name), 'caméra'))
    <div class="container-wide mt-6">
        <p class="text-sm text-quiet">Besoin de choisir un modèle ou de faire installer un système ?
            <a href="{{ route('solution.videosurveillance') }}" class="font-bold text-rouge hover:underline">Choisir votre équipement de vidéosurveillance à Conakry →</a>
        </p>
    </div>
@endif
<div class="container-wide grid lg:grid-cols-[240px_minmax(0,1fr)] gap-8 mt-10">
    <aside class="lg:sticky lg:top-28 self-start">
        <form class="border border-slate-200 p-5 rounded-xl bg-cloud space-y-4" action="{{ $category ? route('category.show', $category) : route('catalog') }}" method="GET">
            <h2 class="font-bold">Affiner la recherche</h2>
            <div><label for="product-query" class="input-label">Référence ou produit</label>
                <input id="product-query" class="field" type="search" name="q" value="{{ $search }}" placeholder="Ex. NVR, caméra IP">
            </div>
            <div><label for="sort" class="input-label">Trier par</label>
                <select id="sort" class="field" name="tri">
                    <option value="">Nouveautés</option>
                    <option value="nom" @selected(request('tri') === 'nom')>Nom A–Z</option>
                    <option value="prix-croissant" @selected(request('tri') === 'prix-croissant')>Prix croissant</option>
                    <option value="prix-decroissant" @selected(request('tri') === 'prix-decroissant')>Prix décroissant</option>
                </select>
            </div>
            <label class="flex gap-2 items-center text-sm"><input type="checkbox" name="en_stock" value="1" @checked(request('en_stock'))> En stock uniquement</label>
            <button class="btn-red w-full" type="submit">Appliquer</button>
        </form>
        <nav aria-label="Catégories" class="mt-7">
            <h2 class="font-bold mb-3">Catégories</h2>
            <a href="{{ route('catalog') }}" class="block py-2 text-sm hover:text-rouge {{ ! $category ? 'text-rouge font-bold' : '' }}">Tout le catalogue</a>
            @foreach($categories as $item)
                <a href="{{ route('category.show', $item) }}" class="block py-2 border-t border-slate-100 text-sm hover:text-rouge {{ $category?->id === $item->id ? 'text-rouge font-bold' : '' }}">{{ $item->name }}</a>
            @endforeach
        </nav>
    </aside>
    <div>
        <div class="flex justify-between items-center text-sm text-quiet mb-5">
            <span><strong class="text-ink">{{ $products->total() }}</strong> référence(s)</span>
            @if($search)<span>Recherche : « {{ $search }} »</span>@endif
        </div>
        @if($products->count())
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($products as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        @else
            <div class="border border-slate-200 rounded-xl bg-cloud text-center px-5 py-20">
                <h2 class="text-xl font-extrabold">Pas encore de référence disponible</h2>
                <p class="text-quiet mt-3 max-w-md mx-auto">Notre catalogue est en préparation ou aucun modèle ne correspond aux filtres. Vous pouvez nous transmettre les références recherchées.</p>
                <a href="{{ route('quote.create') }}" class="btn-red mt-6">Demander un produit</a>
            </div>
        @endif
    </div>
</div>
@endsection
