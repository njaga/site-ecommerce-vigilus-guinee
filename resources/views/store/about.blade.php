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
<section class="container-wide py-16 lg:py-24">
    <div class="grid lg:grid-cols-[1.05fr_.95fr] bg-[#12394f] text-white overflow-hidden">
        <div class="p-8 sm:p-12 lg:p-16">
            <p class="text-xs font-bold tracking-[.18em] uppercase text-cyan-200">La force d'un réseau</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold leading-tight mt-5">Un Groupe. Des métiers complémentaires. Une expertise locale.</h2>
            <p class="text-slate-300 leading-relaxed mt-6">Présent dans plusieurs pays africains, Vigilus Groupe SA réunit des métiers complémentaires. En Guinée, notre activité se concentre sur la fourniture d'équipements.</p>
            <a class="btn-red mt-8" href="https://groupevigilus.com/notre-reseau/" target="_blank" rel="noopener noreferrer">Découvrir les implantations <x-store-icon name="arrow-right" :size="18"/></a>
        </div>
        <div class="bg-[#1b4a61] p-8 sm:p-12 lg:p-16 flex flex-col justify-center gap-7">
            <div class="border-b border-white/20 pb-5"><span class="text-xs font-bold tracking-wider uppercase text-cyan-200">Implantation</span><p class="text-xl font-extrabold mt-2">Conakry · Guinée</p></div>
            <div class="border-b border-white/20 pb-5"><span class="text-xs font-bold tracking-wider uppercase text-cyan-200">Activité</span><p class="text-xl font-extrabold mt-2">Fourniture &amp; distribution</p></div>
            <div><span class="text-xs font-bold tracking-wider uppercase text-cyan-200">Réseau</span><p class="text-xl font-extrabold mt-2">Vigilus Groupe SA</p></div>
        </div>
    </div>
</section>
@endsection
