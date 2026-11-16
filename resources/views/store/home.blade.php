@extends('layouts.store')
@section('title', 'Caméras Dahua et équipements de sécurité à Conakry | Vigilus Guinée')
@section('meta_description', 'Caméras, enregistreurs et contrôle d’accès : trouvez vos équipements Dahua et demandez votre devis auprès de Vigilus Guinée à Conakry.')
@section('content')
<section class="storefront-hero container-wide">
    <div class="storefront-hero-copy">
        <p class="section-eyebrow">Vigilus Guinée · Sécurité électronique</p>
        <h1>Protégez ce qui<br><span>compte pour vous.</span></h1>
        <p class="storefront-hero-description">Des équipements Dahua pour vos espaces.<br>Une équipe à Conakry pour vous conseiller.</p>
        <div class="storefront-hero-actions">
            <a href="{{ route('catalog') }}" class="btn-red">Explorer le catalogue <x-store-icon name="arrow-right" :size="18"/></a>
            <a href="{{ route('quote.create') }}" class="hero-advice-link">Parlons de votre projet</a>
        </div>
    </div>
    <figure class="storefront-hero-media">
        <div class="storefront-hero-visual"><img src="{{ config('site.hero_illustration_url') }}" alt="Illustration des équipements de sécurité électronique" width="640" height="560" fetchpriority="high"></div>
        <figcaption class="hero-dahua-statement"><img class="dahua-brand-logo" src="{{ asset(config('group.dahua_logo')) }}" alt="Dahua Technology" width="120" height="38"><p>{{ config('group.dahua_statement') }}</p></figcaption>
    </figure>
</section>
<section class="container-wide home-assurances" aria-label="Notre approche">
    <span><x-store-icon name="file" :size="19"/> Références Dahua</span>
    <span><x-store-icon name="package" :size="19"/> À l’unité ou en quantité</span>
    <span><x-store-icon name="shield" :size="19"/> Conseil personnalisé</span>
</section>
<section class="container-wide storefront-section">
    <div class="section-topline">
        <div><p class="section-eyebrow">Le catalogue</p><h2 class="section-heading">À chaque besoin, son équipement.</h2></div>
        <a class="section-link" href="{{ route('catalog') }}">Tout le catalogue <x-store-icon name="arrow-right" :size="17"/></a>
    </div>
    @if($categories->isNotEmpty())
        @php
            $homeCategories = $categories->whereIn('slug', ['cameras-ip', 'enregistreurs-nvr', 'controle-dacces', 'alarmes']);
            $homeCategories = $homeCategories->concat($categories->whereNotIn('id', $homeCategories->pluck('id')))->take(4);
        @endphp
        <div class="home-category-grid">
            @foreach($homeCategories as $category)
                @php $illustration = app(\App\Support\CategoryVisuals::class)->for($category); @endphp
                <a href="{{ route('category.show', $category) }}" class="category-image-card group">
                    <div class="category-image-wrap"><img src="{{ $illustration['url'] }}" width="540" height="400" loading="lazy" alt="Illustration : {{ $illustration['label'] }}" class="category-image-card-img"></div>
                    <span class="category-image-card-foot"><strong>{{ $category->name }}</strong><span class="category-image-card-arrow"><x-store-icon name="arrow-right" :size="18"/></span></span>
                </a>
            @endforeach
        </div>
    @else
        <div class="storefront-empty"><h3>Votre projet commence ici.</h3><p>Indiquez-nous les équipements recherchés pour recevoir un devis.</p><a href="{{ route('quote.create') }}" class="btn-red">Demander un devis</a></div>
    @endif
</section>
@if($featured->isNotEmpty())
<section class="home-selection storefront-section">
    <div class="container-wide">
        <div class="section-topline">
            <div><p class="section-eyebrow">Notre sélection</p><h2 class="section-heading">À découvrir.</h2></div>
            <a class="section-link" href="{{ route('catalog') }}">Tous les équipements <x-store-icon name="arrow-right" :size="17"/></a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($featured->take(8) as $product)
                @include('store.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="container-wide home-project storefront-section">
    <div><p class="section-eyebrow">À vos côtés</p><h2 class="section-heading">Un projet de sécurité ?<br>Faisons le point ensemble.</h2><p>Du choix des équipements au devis, notre équipe vous accompagne.</p><a href="{{ route('quote.create') }}" class="btn-red">Demander un devis <x-store-icon name="arrow-right" :size="18"/></a></div>
    <ol class="home-project-steps">
        <li><span>01</span><div><h3>Choisissez vos équipements</h3><p>Parcourez le catalogue et les fiches techniques.</p></div></li>
        <li><span>02</span><div><h3>Partagez votre besoin</h3><p>Une référence, une quantité ou simplement votre projet.</p></div></li>
        <li><span>03</span><div><h3>Recevez votre proposition</h3><p>Prix, disponibilité et livraison confirmés avec vous.</p></div></li>
    </ol>
</section>
@include('store.partials.clients', ['compact' => true])
@include('store.partials.training')
@include('store.partials.group-network')
@include('store.partials.certifications-summary')
@endsection
