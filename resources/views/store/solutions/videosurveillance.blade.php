@extends('layouts.store')
@section('title', 'Équipements de vidéosurveillance Dahua à Conakry | Vigilus Guinée')
@section('meta_description', 'Choisissez vos équipements de vidéosurveillance à Conakry : caméras Dahua, NVR et accessoires selon vos besoins. Références vérifiées, conseils produit et devis.')
@section('canonical', route('solution.videosurveillance'))
@push('jsonld')
@php
    $breadcrumbs = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Équipements de vidéosurveillance à Conakry', 'item' => route('solution.videosurveillance')],
        ],
    ];
@endphp
<script type="application/ld+json">@json($breadcrumbs)</script>
@endpush
@section('content')
<section class="bg-cloud border-b border-slate-200">
    <div class="container-wide py-16 lg:py-24 grid lg:grid-cols-[1.3fr_.7fr] gap-10 items-center">
        <div>
            <nav aria-label="Fil d'Ariane" class="text-xs text-quiet mb-7"><a href="{{ route('home') }}">Accueil</a> / Équipements de vidéosurveillance</nav>
            <p class="kicker">Conakry · Distribution d’équipements</p>
            <h1 class="font-extrabold tracking-tight text-4xl lg:text-5xl leading-tight mt-4">Équipements de vidéosurveillance à Conakry : <span class="text-rouge">choisir la bonne référence.</span></h1>
            <p class="text-lg text-quiet leading-relaxed mt-6">Caméras Dahua, enregistreurs NVR et accessoires : une sélection efficace commence par les contraintes de votre site, les fonctionnalités réellement utiles et la compatibilité entre équipements.</p>
            <div class="flex gap-3 flex-wrap mt-8"><a class="btn-red" href="{{ route('quote.create') }}">Demander un devis</a><a class="btn-light" href="{{ route('catalog') }}">Voir les références</a></div>
        </div>
        <aside class="bg-white border border-slate-200 rounded-2xl p-8">
            <p class="kicker">Avant d'acheter</p><h2 class="text-xl font-extrabold mt-3">Trois critères essentiels</h2>
            <ul class="mt-5 space-y-4 text-sm text-quiet">
                <li><strong class="text-ink block">Les zones à surveiller</strong>Accès, circulation, stockage ou espaces sensibles</li>
                <li><strong class="text-ink block">L'environnement technique</strong>Éclairage, réseau, alimentation et emplacement</li>
                <li><strong class="text-ink block">La compatibilité</strong>Caméras, enregistreurs, capacité et accessoires</li>
            </ul>
        </aside>
    </div>
</section>
<div class="container-wide py-16">
    <p class="kicker">Bien choisir son matériel</p>
    <h2 class="text-3xl font-extrabold mt-2">Les composants d’un système de vidéosurveillance</h2>
    <div class="grid md:grid-cols-3 gap-5 mt-8">
        <article class="border border-slate-200 p-7 rounded-xl">
            <h3 class="text-lg font-extrabold">01 · Caméras de surveillance</h3>
            <p class="text-sm text-quiet mt-4 leading-relaxed">Choisissez une référence IP ou HDCVI selon l'infrastructure existante, le champ à couvrir et les informations que vous souhaitez visualiser.</p>
            <a class="text-sm font-bold text-rouge inline-block mt-5" href="{{ route('catalog') }}">Explorer les caméras →</a>
        </article>
        <article class="border border-slate-200 p-7 rounded-xl">
            <h3 class="text-lg font-extrabold">02 · Enregistreurs et stockage</h3>
            <p class="text-sm text-quiet mt-4 leading-relaxed">Le choix de l'enregistreur dépend notamment du nombre de canaux, de la résolution et de la durée de conservation recherchée.</p>
            <a class="text-sm font-bold text-rouge inline-block mt-5" href="{{ route('catalog') }}">Découvrir les NVR et XVR →</a>
        </article>
        <article class="border border-slate-200 p-7 rounded-xl">
            <h3 class="text-lg font-extrabold">03 · Réseau et accessoires</h3>
            <p class="text-sm text-quiet mt-4 leading-relaxed">Switches PoE, câbles, supports, stockage et alimentation doivent être compatibles avec les modèles retenus.</p>
            <a class="text-sm font-bold text-rouge inline-block mt-5" href="{{ route('quote.create') }}">Préciser les références →</a>
        </article>
    </div>
    <section class="bg-ink text-white rounded-2xl p-8 sm:p-12 mt-14 grid lg:grid-cols-[1fr_auto] gap-6 items-center">
        <div><h2 class="text-2xl font-extrabold">Un besoin d’équipements pour votre site ?</h2>
            <p class="text-slate-300 mt-3 text-sm max-w-2xl leading-relaxed">Transmettez les modèles recherchés, quantités, contraintes techniques et lieu de livraison. L'équipe pourra qualifier votre demande et confirmer la disponibilité et les conditions commerciales.</p></div>
        <a href="{{ route('quote.create') }}" class="btn-red">Demander un devis →</a>
    </section>
    <p class="text-xs text-quiet mt-6">Cette page présente une offre d'équipements. Elle ne constitue pas une promesse de prestations d’installation ni de délais de livraison. Aucun paiement en ligne n'est proposé.</p>
</div>
@endsection
