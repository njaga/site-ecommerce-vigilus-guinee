@extends('layouts.store')
@section('title', 'Caméras Dahua et équipements de sécurité à Conakry | Vigilus Guinée')
@section('meta_description', 'Découvrez les références Dahua proposées par Vigilus Guinée : caméras, enregistreurs, contrôle d’accès et équipements de sécurité électronique. Demandez un devis à Conakry.')
@section('content')
<section class="home-hero">
    <div class="container-wide home-hero-inner">
        <div class="home-hero-copy">
            <p class="section-eyebrow">Vigilus Guinée · Distribution professionnelle</p>
            <h1 class="home-hero-title mt-7">La bonne technologie pour <em>protéger l’essentiel.</em></h1>
            <p class="text-base sm:text-lg leading-relaxed text-quiet max-w-xl mt-7">Caméras de surveillance, enregistreurs, contrôle d’accès et accessoires Dahua. Explorez les références et préparez votre demande d’équipement avec notre équipe à Conakry.</p>
            <div class="flex flex-wrap gap-3 mt-9">
                <a class="btn-red" href="{{ route('catalog') }}">Explorer le catalogue <x-store-icon name="arrow-right" :size="18"/></a>
                <a class="btn-light" href="{{ route('quote.create') }}">Demander un devis</a>
            </div>
            <p class="mt-8 text-xs sm:text-sm text-quiet">Pour les particuliers, entreprises, installateurs et revendeurs.</p>
        </div>
        <div class="home-hero-media">
            @php $heroImage = $featured->first()?->images->first(); @endphp
            @if($heroImage)
                <img src="{{ asset('storage/'.$heroImage->path) }}"
                     alt="{{ $heroImage->alt ?: 'Référence de sécurité électronique au catalogue' }}"
                     width="840" height="700" fetchpriority="high">
            @else
                <img src="{{ config('site.hero_illustration_url') }}"
                     alt="Illustration du domaine de la vidéosurveillance" width="840" height="700" fetchpriority="high">
            @endif
            <div class="home-hero-media-label">
                <div><small class="block mb-2">Équipements &amp; technologies</small>
                    <strong class="block">Vidéosurveillance<br>et sécurité électronique</strong></div>
                <span class="text-xs font-bold border border-white/50 px-3 py-2">Conakry · GN</span>
            </div>
        </div>
    </div>
</section>
<section class="home-proof" aria-label="Notre approche">
    <div class="container-wide home-proof-inner">
        <div class="home-proof-item">
            <x-store-icon name="file" :size="25"/>
            <div><strong>Références identifiables</strong><span>Caractéristiques techniques et modèles fabricant</span></div>
        </div>
        <div class="home-proof-item">
            <x-store-icon name="package" :size="25"/>
            <div><strong>Achats adaptés à vos besoins</strong><span>À l’unité ou en quantité, selon disponibilité</span></div>
        </div>
        <div class="home-proof-item">
            <x-store-icon name="shield" :size="25"/>
            <div><strong>Accompagnement commercial</strong><span>Devis, validation et suivi de votre demande</span></div>
        </div>
    </div>
</section>
<section class="container-wide py-16 lg:py-22">
    <div class="flex flex-wrap items-end justify-between gap-5 mb-9">
        <div>
            <p class="section-eyebrow">01 / Catalogue</p>
            <h2 class="section-heading mt-4">Trouvez votre famille d’équipements.</h2>
            <p class="text-quiet mt-3 max-w-2xl">Accédez directement aux références par usage et par catégorie.</p>
        </div>
        <a class="section-link" href="{{ route('catalog') }}">Toutes les références <x-store-icon name="arrow-right" :size="17"/></a>
    </div>
    @if($categories->isNotEmpty())
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($categories as $category)
                <a class="category-tile group" href="{{ route('category.show', $category) }}">
                    <span class="tile-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad((string) $categories->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="tile-bottom"><strong>{{ $category->name }}</strong><x-store-icon name="arrow-right" :size="20" class="shrink-0"/></span>
                </a>
            @endforeach
        </div>
    @else
        <div class="border border-slate-200 bg-cloud p-10 flex flex-wrap gap-5 items-center justify-between">
            <div><h3 class="text-xl font-extrabold">Le catalogue est en préparation.</h3>
                <p class="text-sm text-quiet mt-2">Nos références seront visibles après validation technique et commerciale.</p></div>
            <a href="{{ route('quote.create') }}" class="btn-light">Transmettre mes besoins</a>
        </div>
    @endif
</section>
@if($featured->isNotEmpty())
<section class="bg-cloud py-16 lg:py-20">
    <div class="container-wide">
        <div class="flex flex-wrap items-end justify-between gap-5 mb-9">
            <div><p class="section-eyebrow">02 / Sélection</p>
                <h2 class="section-heading mt-4">Références à découvrir.</h2>
                <p class="text-quiet mt-3">Une sélection de produits actuellement publiés dans notre catalogue.</p>
            </div>
            <a class="section-link" href="{{ route('catalog') }}">Voir le catalogue <x-store-icon name="arrow-right" :size="17"/></a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($featured as $product)
                @include('store.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="container-wide py-16 lg:py-24">
    <div class="grid lg:grid-cols-[.9fr_1.1fr] border border-slate-200">
        <div class="bg-[#12394f] text-white p-8 sm:p-12 lg:p-16">
            <p class="text-xs font-bold tracking-[.17em] uppercase text-cyan-200">Professionnels &amp; projets multi-sites</p>
            <h2 class="text-3xl lg:text-4xl font-extrabold mt-6 leading-tight">Vos références. Vos quantités. <span class="text-cyan-200">Une réponse précise.</span></h2>
            <p class="text-slate-300 leading-relaxed mt-5 max-w-md">Équipez un point de vente, une entreprise ou plusieurs bâtiments : communiquez vos références, vos contraintes et vos quantités pour obtenir une proposition commerciale adaptée.</p>
            <a class="btn-red mt-8" href="{{ route('quote.create') }}">Soumettre mon besoin <x-store-icon name="arrow-right" :size="18"/></a>
        </div>
        <div class="bg-white p-8 sm:p-12 lg:p-16">
            <p class="section-eyebrow">Comment ça marche</p>
            <h3 class="text-2xl font-extrabold mt-4 mb-8">Du besoin à la confirmation.</h3>
            <div class="store-step"><span class="store-step-number">01</span><div>
                <h4 class="text-base font-extrabold">Explorez les équipements</h4>
                <p class="text-sm text-quiet mt-2 leading-relaxed">Consultez les fiches et sélectionnez les références qui correspondent à votre besoin.</p>
            </div></div>
            <div class="store-step"><span class="store-step-number">02</span><div>
                <h4 class="text-base font-extrabold">Envoyez votre demande</h4>
                <p class="text-sm text-quiet mt-2 leading-relaxed">Transmettez votre panier ou demandez un devis en précisant les modèles et les quantités.</p>
            </div></div>
            <div class="store-step"><span class="store-step-number">03</span><div>
                <h4 class="text-base font-extrabold">Validez avec l’équipe</h4>
                <p class="text-sm text-quiet mt-2 leading-relaxed">Stock, livraison, montant final et règlement sont confirmés commercialement, hors du site.</p>
            </div></div>
        </div>
    </div>
</section>
<section class="container-wide pb-8">
    <div class="bg-cloud border border-slate-200 px-7 py-8 flex flex-wrap gap-6 items-center justify-between">
        <div><p class="font-extrabold text-lg">Vous hésitez entre plusieurs équipements ?</p>
            <p class="text-quiet text-sm mt-2">Notre guide vous aide à identifier les caractéristiques à comparer avant de demander un devis.</p></div>
        <a href="{{ route('solution.videosurveillance') }}" class="section-link">Guide vidéosurveillance <x-store-icon name="arrow-right" :size="18"/></a>
    </div>
</section>
@endsection
