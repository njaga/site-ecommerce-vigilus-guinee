@extends('layouts.store')
@section('title', 'À propos de Vigilus Guinée | Distribution d’équipements de sécurité à Conakry')
@section('meta_description', 'Découvrez Vigilus Guinée, implantation de Vigilus Groupe SA dédiée à la distribution d’équipements électroniques professionnels à Conakry, son approche et son réseau.')
@section('canonical', route('about'))
@section('content')
<section class="about-hero">
    <div class="container-wide grid lg:grid-cols-[.9fr_1.1fr] min-h-[480px] gap-10 items-center">
        <div class="py-16 lg:py-20">
            <p class="section-eyebrow">À propos · Vigilus Guinée</p>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mt-6 leading-[1.12]">Des équipements adaptés.<span class="block text-rouge">Un partenaire de proximité.</span></h1>
            <p class="text-quiet text-lg leading-relaxed mt-6 max-w-xl">Implantée à Conakry, Vigilus Guinée s'inscrit dans le réseau de Vigilus Groupe SA. Notre activité locale est centrée sur la sélection et la distribution d'équipements professionnels.</p>
            <div class="flex flex-wrap gap-3 mt-8">
                <a href="{{ route('catalog') }}" class="btn-red">Découvrir les équipements <x-store-icon name="arrow-right" :size="18"/></a>
                <a href="{{ route('quote.create') }}" class="btn-light">Échanger avec notre équipe</a>
            </div>
        </div>
        <div class="about-hero-photo">
            <img src="{{ config('site.hero_illustration_url') }}" alt="Illustration d'équipements de sécurité électronique" width="980" height="760" loading="eager">
            <div class="about-hero-caption">Conakry <span>République de Guinée</span></div>
        </div>
    </div>
</section>
<section class="container-wide py-16 lg:py-24">
    <div class="grid lg:grid-cols-[.7fr_1.3fr] gap-8 lg:gap-20">
        <div><p class="section-eyebrow">Notre mission</p><h2 class="section-heading mt-4">Simplifier les achats techniques.</h2></div>
        <div>
            <p class="text-lg text-ink font-semibold leading-relaxed">La bonne référence, au bon usage, avec une proposition claire et un interlocuteur identifié.</p>
            <p class="text-quiet mt-5 leading-relaxed">Nous accompagnons les demandes d'équipements de sécurité électronique : analyse des spécifications, orientation vers les modèles du catalogue, devis, vérification de disponibilité et coordination de livraison après validation commerciale.</p>
        </div>
    </div>
</section>
@include('store.partials.why-us')
@include('store.partials.clients')
@include('store.partials.partners')
@include('store.partials.training')
@include('store.partials.group-network')
@include('store.partials.certifications')
<section id="nos-filiales" class="container-wide storefront-section">
    <div class="section-topline"><div><p class="section-eyebrow">Des métiers complémentaires</p><h2 class="section-heading">Les filiales du Groupe.</h2></div><p class="text-sm text-quiet">Toutes basées au Sénégal.</p></div>
    <div class="subsidiary-grid">
        @foreach(config('group.subsidiaries') as $subsidiary)
            <article class="subsidiary-card">
                <div class="subsidiary-logo-slot">
                    @if($subsidiary['logo'])<img src="{{ $subsidiary['logo'] }}" alt="{{ $subsidiary['name'] }}" width="180" height="80" loading="lazy">
                    @else<span class="subsidiary-name-fallback">{{ $subsidiary['name'] }}</span>@endif
                </div>
                <h3>{{ $subsidiary['name'] }}</h3>@if(isset($subsidiary['detail']))<p>{{ $subsidiary['detail'] }}</p>@endif<span class="subsidiary-country"><img src="{{ asset('images/flags/sn.svg') }}" width="20" height="14" alt=""> Sénégal</span>
                @if(isset($subsidiary['url']))<a class="section-link mt-3" href="{{ $subsidiary['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Visiter le site de {{ $subsidiary['name'] }} (nouvel onglet)">Voir le site <x-store-icon name="arrow-right" :size="15"/></a>@endif
            </article>
        @endforeach
    </div>
</section>
@endsection
