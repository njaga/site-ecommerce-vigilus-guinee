@extends('layouts.store')
@section('title', 'Caméras Dahua et équipements de sécurité à Conakry | Vigilus Guinée')
@section('meta_description', 'Découvrez les références Dahua proposées par Vigilus Guinée : caméras, enregistreurs, contrôle d’accès et équipements de sécurité électronique. Demandez un devis à Conakry.')
@section('content')
<section class="home-hero-modern">
    <div class="container-wide home-hero-modern-grid">
        <div class="home-hero-modern-copy">
            <p class="hero-modern-eyebrow"><span aria-hidden="true" class="hero-modern-eyebrow-dot"></span> VIGILUS GROUPE SA <span class="opacity-60">/</span> GUINÉE</p>
            <h1 class="hero-modern-title mt-7">La bonne technologie pour <span>une sécurité maîtrisée.</span></h1>
            <p class="mt-7 text-base sm:text-lg leading-relaxed max-w-xl text-[#d3e3e9]">Caméras Dahua, enregistreurs, contrôle d'accès et équipements professionnels : sélectionnez les références adaptées à votre projet à Conakry.</p>
            <div class="flex flex-wrap gap-3 mt-9">
                <a href="{{ route('catalog') }}" class="hero-modern-primary">Explorer les équipements <x-store-icon name="arrow-right" :size="18"/></a>
                <a href="{{ route('quote.create') }}" class="hero-modern-secondary">Obtenir un devis</a>
            </div>
            <button type="button" data-open-search class="hero-modern-search mt-10" aria-haspopup="dialog"
                    aria-label="Rechercher des références dans une fenêtre de suggestions">
                <x-store-icon name="search" :size="20"/>
                <span>Rechercher une référence, un modèle…</span>
                <span aria-hidden="true" class="ml-auto text-lg">↗</span>
            </button>
            <div class="hero-modern-tags mt-8"><span>Équipements électroniques</span><span>Demandes professionnelles</span><span>Conakry · Guinée</span></div>
        </div>
        <div class="home-hero-modern-visual">
            @php $heroImage = $featured->first()?->images->first(); @endphp
            @if($heroImage)
                <img class="hero-modern-illustration hero-modern-product"
                     src="{{ asset('storage/'.$heroImage->path) }}"
                     alt="{{ $heroImage->alt ?: 'Produit sélectionné dans le catalogue' }}"
                     width="850" height="850" fetchpriority="high">
            @else
                <img class="hero-modern-illustration" src="{{ config('site.hero_illustration_url') }}"
                     alt="Illustration des équipements de sécurité électronique"
                     width="850" height="850" fetchpriority="high">
            @endif
            <div class="hero-modern-image-vignette" aria-hidden="true"></div>
            <div class="hero-modern-feature">
                <span class="text-[10px] font-extrabold tracking-[.17em] text-[#b7edf2] uppercase">Une offre claire, un suivi identifié</span>
                <strong class="block mt-2 text-lg sm:text-xl leading-snug font-extrabold">Du choix des références à la confirmation commerciale.</strong>
                <span class="block text-xs mt-3 text-[#c3d8e2]">Disponibilités et conditions vérifiées avant tout règlement.</span>
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
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($categories as $category)
                @php $illustration = app(\App\Support\CategoryVisuals::class)->for($category); @endphp
                <a href="{{ route('category.show', $category) }}" class="category-image-card group">
                    <img src="{{ $illustration['url'] }}" width="540" height="480" loading="lazy"
                         alt="Illustration : {{ $illustration['label'] }}" class="category-image-card-img">
                    <span aria-hidden="true" class="category-image-card-overlay"></span>
                    <span class="category-image-card-content">
                        <span class="category-image-card-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad((string) $categories->count(), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="category-image-card-foot"><strong>{{ $category->name }}</strong>
                            <span class="category-image-card-arrow"><x-store-icon name="arrow-right" :size="18"/></span></span>
                    </span>
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
@include('store.partials.why-us')
@include('store.partials.clients')
@include('store.partials.partners')
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
