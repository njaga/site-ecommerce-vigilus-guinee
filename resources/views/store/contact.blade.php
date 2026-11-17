@extends('layouts.store')
@section('title', 'Contact | Vigilus Guinée — Conakry')
@section('meta_description', 'Contactez Vigilus Guinée à Conakry pour vos équipements Dahua, un devis ou une formation professionnelle.')
@section('canonical', route('contact'))
@section('content')
@php
    $channels = app(\App\Support\ContactChannels::class);
    $whatsappUrl = $channels->whatsappUrl();
    $mapEmbedUrl = $channels->mapEmbedUrl();
@endphp
<section class="contact-hero container-wide">
    <p class="section-eyebrow">Contact · Vigilus Guinée</p>
    <h1>Parlons de votre projet.</h1>
    <p>Un équipement, un conseil ou une formation : notre équipe vous accompagne dans vos projets de sécurité électronique.</p>
</section>
<section class="container-wide contact-layout" aria-label="Coordonnées et localisation">
    <div class="contact-information">
        <p class="section-eyebrow">À votre écoute</p>
        <h2>Votre interlocuteur à Conakry.</h2>
        @include('store.partials.contact-details')
        @if($whatsappUrl)
            <a class="whatsapp-action" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Écrire sur WhatsApp <x-store-icon name="arrow-right" :size="17"/><span class="sr-only"> (nouvel onglet)</span></a>
        @else
            <p class="contact-pending">WhatsApp · +224 — numéro complet à venir</p>
        @endif
    </div>
    <div class="contact-map-card">
        @if($mapEmbedUrl)
            <iframe class="contact-map" title="Localisation : {{ $channels->mapQuery() }}" src="{{ $mapEmbedUrl }}" width="800" height="430" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
        @else
            <div class="contact-map-placeholder">
                <span class="contact-location-icon" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                <strong>{{ $channels->mapQuery() }}</strong>
                <span>Retrouvez notre ville sur Google Maps.</span>
                <a class="btn-light" href="{{ $channels->mapUrl() }}" target="_blank" rel="noopener noreferrer">Ouvrir la carte <x-store-icon name="arrow-right" :size="17"/><span class="sr-only"> (nouvel onglet)</span></a>
            </div>
        @endif
        <div class="contact-map-caption"><div><strong>Vigilus Guinée</strong><p>{{ config('site.address') ?: 'Localisation indicative à Conakry. Adresse précise à venir.' }}</p></div><a href="{{ $channels->mapUrl() }}" target="_blank" rel="noopener noreferrer" aria-label="Voir la localisation sur Google Maps (nouvel onglet)"><x-store-icon name="arrow-right" :size="20"/></a></div>
    </div>
</section>
<section class="container-wide storefront-section" aria-labelledby="contact-needs">
    <div class="section-topline"><h2 id="contact-needs" class="section-heading">Comment pouvons-nous vous aider ?</h2></div>
    <div class="contact-needs">
        <a class="contact-need" href="{{ route('quote.create') }}"><x-store-icon name="file" :size="27"/><h3>Équiper votre projet</h3><p>Décrivez votre besoin et recevez une proposition adaptée.</p><span class="section-link">Demander un devis <x-store-icon name="arrow-right" :size="17"/></span></a>
        <a class="contact-need" href="{{ route('training.index') }}#demande"><x-store-icon name="shield" :size="27"/><h3>Former vos équipes</h3><p>Préparez une session pour vos installateurs ou votre entreprise.</p><span class="section-link">Parler de votre formation <x-store-icon name="arrow-right" :size="17"/></span></a>
    </div>
</section>
@endsection
